<?php
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }

$dataFile = __DIR__ . '/tasks.json';

function readTasks($file) {
    if (!file_exists($file)) return [];
    $json = file_get_contents($file);
    return json_decode($json, true) ?: [];
}

function writeTasks($file, $tasks) {
    file_put_contents($file, json_encode($tasks, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
}

function nextId($tasks) {
    $max = 0;
    foreach ($tasks as $t) { if ($t['id'] > $max) $max = $t['id']; }
    return $max + 1;
}

$method = $_SERVER['REQUEST_METHOD'];
$action = $_GET['action'] ?? '';

switch ($method) {
    case 'GET':
        echo json_encode(readTasks($dataFile), JSON_UNESCAPED_UNICODE);
        break;

    case 'POST':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input) { http_response_code(400); echo json_encode(['error' => 'Invalid JSON']); break; }

        if ($action === 'reorder') {
            // Reorder tasks by array of IDs
            $ids = $input['ids'] ?? [];
            $tasks = readTasks($dataFile);
            $indexed = [];
            foreach ($tasks as $t) $indexed[$t['id']] = $t;
            $reordered = [];
            foreach ($ids as $id) {
                if (isset($indexed[$id])) $reordered[] = $indexed[$id];
            }
            // append any tasks not in the list
            foreach ($tasks as $t) {
                if (!in_array($t['id'], $ids)) $reordered[] = $t;
            }
            writeTasks($dataFile, $reordered);
            echo json_encode(['ok' => true]);
            break;
        }

        $tasks = readTasks($dataFile);
        $input['id'] = nextId($tasks);
        if (!isset($input['done'])) $input['done'] = false;
        if (!isset($input['blocker'])) $input['blocker'] = false;
        if (!isset($input['subtasks'])) $input['subtasks'] = [];
        if (!isset($input['flag'])) $input['flag'] = '';
        if (!isset($input['deadline'])) $input['deadline'] = '';
        $tasks[] = $input;
        writeTasks($dataFile, $tasks);
        echo json_encode($input, JSON_UNESCAPED_UNICODE);
        break;

    case 'PUT':
        $input = json_decode(file_get_contents('php://input'), true);
        if (!$input || !isset($input['id'])) { http_response_code(400); echo json_encode(['error' => 'Missing id']); break; }
        $tasks = readTasks($dataFile);
        $found = false;
        foreach ($tasks as &$t) {
            if ($t['id'] == $input['id']) {
                foreach ($input as $k => $v) {
                    $t[$k] = $v;
                }
                $found = true;
                break;
            }
        }
        unset($t);
        if (!$found) { http_response_code(404); echo json_encode(['error' => 'Not found']); break; }
        writeTasks($dataFile, $tasks);
        echo json_encode(['ok' => true]);
        break;

    case 'DELETE':
        $input = json_decode(file_get_contents('php://input'), true);
        $id = $input['id'] ?? ($_GET['id'] ?? null);
        if (!$id) { http_response_code(400); echo json_encode(['error' => 'Missing id']); break; }
        $tasks = readTasks($dataFile);
        $tasks = array_values(array_filter($tasks, function($t) use ($id) { return $t['id'] != $id; }));
        writeTasks($dataFile, $tasks);
        echo json_encode(['ok' => true]);
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
