<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$dataFile = __DIR__ . '/tasks.json';
$logFile  = __DIR__ . '/activity.json';
$tgConfig = __DIR__ . '/telegram.json';
$uploadDir = __DIR__ . '/uploads/';

if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

/* ── helpers ── */
function readJSON($file) {
    if (!file_exists($file)) return [];
    return json_decode(file_get_contents($file), true) ?: [];
}
function writeJSON($file, $data) {
    file_put_contents($file, json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}
function nextId($items) {
    $max = 0;
    foreach ($items as $t) { if (($t['id'] ?? 0) > $max) $max = $t['id']; }
    return $max + 1;
}

/* ── activity log ── */
function logActivity($logFile, $action, $details) {
    $log = readJSON($logFile);
    array_unshift($log, [
        'time' => date('c'),
        'action' => $action,
        'details' => $details
    ]);
    // keep last 200 entries
    $log = array_slice($log, 0, 200);
    writeJSON($logFile, $log);
}

/* ── telegram notify ── */
function notifyTelegram($tgConfig, $message) {
    if (!file_exists($tgConfig)) return;
    $cfg = json_decode(file_get_contents($tgConfig), true);
    if (empty($cfg['bot_token']) || empty($cfg['chat_id'])) return;
    $url = "https://api.telegram.org/bot{$cfg['bot_token']}/sendMessage";
    $data = ['chat_id' => $cfg['chat_id'], 'text' => $message, 'parse_mode' => 'HTML'];
    $opts = ['http' => ['method' => 'POST', 'header' => 'Content-Type: application/x-www-form-urlencoded', 'content' => http_build_query($data), 'timeout' => 5]];
    @file_get_contents($url, false, stream_context_create($opts));
}

/* ── routing ── */
$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

// Handle file upload separately (multipart)
if ($method === 'POST' && $action === 'upload') {
    $taskId = $_POST['task_id'] ?? null;
    if (!$taskId || empty($_FILES['file'])) {
        http_response_code(400);
        echo json_encode(['error' => 'Missing task_id or file']);
        exit;
    }
    $file = $_FILES['file'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $allowed = ['jpg','jpeg','png','gif','webp','pdf','doc','docx','xls','xlsx','zip','txt','svg'];
    if (!in_array($ext, $allowed)) {
        http_response_code(400);
        echo json_encode(['error' => 'File type not allowed']);
        exit;
    }
    $fname = time() . '_' . preg_replace('/[^a-zA-Z0-9._-]/', '_', $file['name']);
    if (move_uploaded_file($file['tmp_name'], $uploadDir . $fname)) {
        // add attachment to task
        $tasks = readJSON($dataFile);
        foreach ($tasks as &$t) {
            if ($t['id'] == $taskId) {
                if (!isset($t['attachments'])) $t['attachments'] = [];
                $t['attachments'][] = [
                    'name' => $file['name'],
                    'file' => $fname,
                    'size' => $file['size'],
                    'time' => date('c')
                ];
                break;
            }
        }
        unset($t);
        writeJSON($dataFile, $tasks);
        logActivity($logFile, 'attachment', "Файл \"{$file['name']}\" прикреплён к задаче #$taskId");
        echo json_encode(['ok' => true, 'file' => $fname]);
    } else {
        http_response_code(500);
        echo json_encode(['error' => 'Upload failed']);
    }
    exit;
}

// Delete attachment
if ($method === 'POST' && $action === 'delete_attachment') {
    $input = json_decode(file_get_contents('php://input'), true);
    $taskId = $input['task_id'] ?? null;
    $fname = $input['file'] ?? null;
    if (!$taskId || !$fname) { http_response_code(400); echo json_encode(['error' => 'Missing params']); exit; }
    $tasks = readJSON($dataFile);
    foreach ($tasks as &$t) {
        if ($t['id'] == $taskId) {
            $t['attachments'] = array_values(array_filter($t['attachments'] ?? [], function($a) use ($fname) { return $a['file'] !== $fname; }));
            break;
        }
    }
    unset($t);
    writeJSON($dataFile, $tasks);
    @unlink($uploadDir . $fname);
    echo json_encode(['ok' => true]);
    exit;
}

// Activity log
if ($method === 'GET' && $action === 'activity') {
    echo json_encode(readJSON($logFile), JSON_UNESCAPED_UNICODE);
    exit;
}

// Telegram config
if ($method === 'POST' && $action === 'telegram_config') {
    $input = json_decode(file_get_contents('php://input'), true);
    writeJSON($tgConfig, ['bot_token' => $input['bot_token'] ?? '', 'chat_id' => $input['chat_id'] ?? '']);
    echo json_encode(['ok' => true]);
    exit;
}
if ($method === 'GET' && $action === 'telegram_config') {
    $cfg = readJSON($tgConfig);
    echo json_encode(['configured' => !empty($cfg['bot_token']) && !empty($cfg['chat_id'])]);
    exit;
}

// Test telegram
if ($method === 'POST' && $action === 'telegram_test') {
    notifyTelegram($tgConfig, "✅ Brite Sprint Planner подключён!");
    echo json_encode(['ok' => true]);
    exit;
}

switch ($method) {
    case 'GET':
        echo json_encode(readJSON($dataFile), JSON_UNESCAPED_UNICODE);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) { http_response_code(400); echo json_encode(['error' => 'Invalid JSON']); break; }

        if ($action === 'reorder') {
            $ids = $input['ids'] ?? [];
            $tasks = readJSON($dataFile);
            $indexed = [];
            foreach ($tasks as $t) $indexed[$t['id']] = $t;
            $reordered = [];
            foreach ($ids as $id) { if (isset($indexed[$id])) $reordered[] = $indexed[$id]; }
            foreach ($tasks as $t) { if (!in_array($t['id'], $ids)) $reordered[] = $t; }
            writeJSON($dataFile, $reordered);
            echo json_encode(['ok' => true]);
            break;
        }

        // Add comment
        if ($action === 'comment') {
            $taskId = $input['task_id'] ?? null;
            $text = $input['text'] ?? '';
            $author = $input['author'] ?? 'Unknown';
            if (!$taskId || !$text) { http_response_code(400); echo json_encode(['error' => 'Missing params']); break; }
            $tasks = readJSON($dataFile);
            foreach ($tasks as &$t) {
                if ($t['id'] == $taskId) {
                    if (!isset($t['comments'])) $t['comments'] = [];
                    $t['comments'][] = ['author' => $author, 'text' => $text, 'time' => date('c')];
                    break;
                }
            }
            unset($t);
            writeJSON($dataFile, $tasks);
            logActivity($logFile, 'comment', "$author прокомментировал задачу #$taskId");
            notifyTelegram($tgConfig, "💬 <b>$author</b> прокомментировал задачу #$taskId:\n$text");
            echo json_encode(['ok' => true]);
            break;
        }

        // Delete comment
        if ($action === 'delete_comment') {
            $taskId = $input['task_id'] ?? null;
            $idx = $input['index'] ?? null;
            if ($taskId === null || $idx === null) { http_response_code(400); echo json_encode(['error' => 'Missing params']); break; }
            $tasks = readJSON($dataFile);
            foreach ($tasks as &$t) {
                if ($t['id'] == $taskId) {
                    array_splice($t['comments'], $idx, 1);
                    break;
                }
            }
            unset($t);
            writeJSON($dataFile, $tasks);
            echo json_encode(['ok' => true]);
            break;
        }

        // Create task
        $tasks = readJSON($dataFile);
        $input['id'] = nextId($tasks);
        $defaults = ['done'=>false,'blocker'=>false,'subtasks'=>[],'flag'=>'','deadline'=>'','status'=>'todo','tags'=>[],'comments'=>[],'attachments'=>[],'created'=>date('c')];
        foreach ($defaults as $k => $v) { if (!isset($input[$k])) $input[$k] = $v; }
        $tasks[] = $input;
        writeJSON($dataFile, $tasks);
        logActivity($logFile, 'create', "Создана задача #{$input['id']} \"{$input['title']}\"");
        notifyTelegram($tgConfig, "➕ Новая задача <b>#{$input['id']}</b>: {$input['title']}\nКто: {$input['owner']} | Приоритет: {$input['priority']}");
        echo json_encode($input, JSON_UNESCAPED_UNICODE);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['id'])) { http_response_code(400); echo json_encode(['error' => 'Missing id']); break; }
        $tasks = readJSON($dataFile);
        $found = false;
        $changes = [];
        foreach ($tasks as &$t) {
            if ($t['id'] == $input['id']) {
                foreach ($input as $k => $v) {
                    if ($k !== 'id' && ($t[$k] ?? null) !== $v) {
                        $changes[] = $k;
                    }
                    $t[$k] = $v;
                }
                $found = true;
                $taskTitle = $t['title'];
                break;
            }
        }
        unset($t);
        if (!$found) { http_response_code(404); echo json_encode(['error' => 'Not found']); break; }
        writeJSON($dataFile, $tasks);

        if (!empty($changes)) {
            $changeStr = implode(', ', $changes);
            logActivity($logFile, 'update', "Задача #{$input['id']} \"{$taskTitle}\": обновлено $changeStr");

            // Telegram for important changes
            if (in_array('done', $changes) && ($input['done'] ?? false)) {
                notifyTelegram($tgConfig, "✅ Задача <b>#{$input['id']}</b> выполнена: $taskTitle");
            } elseif (in_array('status', $changes)) {
                $statusLabels = ['todo'=>'К выполнению','progress'=>'В работе','done'=>'Готово'];
                $sl = $statusLabels[$input['status'] ?? 'todo'] ?? $input['status'];
                notifyTelegram($tgConfig, "🔄 Задача <b>#{$input['id']}</b> → $sl: $taskTitle");
            } elseif (in_array('priority', $changes)) {
                notifyTelegram($tgConfig, "⚡ Приоритет задачи <b>#{$input['id']}</b> изменён: $taskTitle");
            }
        }
        echo json_encode(['ok' => true]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? ($_GET['id'] ?? null);
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'Missing id']); break; }
        $tasks = readJSON($dataFile);
        $title = '';
        foreach ($tasks as $t) { if ($t['id'] == $id) { $title = $t['title']; break; } }
        $tasks = array_values(array_filter($tasks, function($t) use ($id) { return $t['id'] != $id; }));
        writeJSON($dataFile, $tasks);
        logActivity($logFile, 'delete', "Удалена задача #$id \"$title\"");
        notifyTelegram($tgConfig, "🗑 Удалена задача <b>#$id</b>: $title");
        echo json_encode(['ok' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
