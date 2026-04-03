<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Brite Sprint Tasks</title>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif; background: #f5f5f3; color: #1a1a1a; padding: 1.5rem 1rem 4rem; }
        .root { max-width: 680px; margin: 0 auto; }

        /* header */
        .header { display: flex; align-items: baseline; gap: 12px; margin-bottom: 0.75rem; }
        .header h2 { font-size: 18px; font-weight: 600; letter-spacing: -0.3px; }
        .progress-label { font-size: 12px; color: #999; }
        .progress-bar { height: 3px; background: #e8e8e6; border-radius: 2px; margin-bottom: 1rem; overflow: hidden; }
        .progress-fill { height: 100%; background: #1D9E75; border-radius: 2px; transition: width 0.5s cubic-bezier(0.4, 0, 0.2, 1); }

        /* toolbar: search + filters */
        .toolbar { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 1rem; align-items: center; }
        .search-box { position: relative; flex: 1; min-width: 140px; max-width: 220px; }
        .search-box input { width: 100%; border: 1px solid #e0e0de; border-radius: 8px; padding: 5px 10px 5px 28px; font-size: 12px; outline: none; font-family: inherit; background: #fff; transition: border-color 0.15s; }
        .search-box input:focus { border-color: #1D9E75; }
        .search-box::before { content: ''; position: absolute; left: 10px; top: 50%; transform: translateY(-50%); width: 12px; height: 12px; background: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23aaa'%3E%3Cpath d='M8 3a5 5 0 104.58 7.17l4.13 4.13a.75.75 0 001.06-1.06l-4.13-4.13A5 5 0 008 3zM4.5 8a3.5 3.5 0 117 0 3.5 3.5 0 01-7 0z'/%3E%3C/svg%3E") no-repeat center; background-size: contain; }
        .search-hint { position: absolute; right: 8px; top: 50%; transform: translateY(-50%); font-size: 10px; color: #ccc; pointer-events: none; }

        .filters { display: flex; flex-wrap: wrap; gap: 6px; align-items: center; }
        .filter-group { display: flex; gap: 3px; }
        .filter-sep { width: 1px; height: 16px; background: #e0e0de; margin: 0 2px; align-self: center; }
        .chip { font-size: 11px; font-weight: 500; padding: 3px 10px; border-radius: 20px; border: 0.5px solid #d0d0ce; background: #fff; color: #777; cursor: pointer; transition: all 0.15s; white-space: nowrap; }
        .chip:hover { border-color: #aaa; color: #333; }
        .chip:active { transform: scale(0.95); }
        .chip.active { border-color: transparent; color: #fff; }
        .chip.active.c-all   { background: #444; }
        .chip.active.c-boris { background: #185FA5; }
        .chip.active.c-marat { background: #3B6D11; }
        .chip.active.c-high  { background: #A32D2D; }
        .chip.active.c-med   { background: #854F0B; }
        .chip.active.c-low   { background: #3B6D11; }
        .chip.active.c-open  { background: #534AB7; }
        .chip.active.c-done  { background: #1D9E75; }

        /* ── inline add ── */
        .inline-add { background: #fff; border: 1.5px solid #e0e0de; border-radius: 10px; padding: 10px 12px; margin-bottom: 8px; display: none; animation: cardIn 0.2s ease both; }
        .inline-add.visible { display: block; }
        .inline-add-title { width: 100%; border: none; outline: none; font-size: 13px; font-weight: 500; font-family: inherit; color: #1a1a1a; padding: 0; }
        .inline-add-title::placeholder { color: #bbb; }
        .inline-add-meta { display: flex; gap: 6px; margin-top: 8px; align-items: center; flex-wrap: wrap; }
        .meta-select { font-size: 11px; padding: 2px 8px; border-radius: 20px; border: 1px solid #e0e0de; background: #fff; color: #666; cursor: pointer; font-family: inherit; outline: none; -webkit-appearance: none; appearance: none; transition: border-color 0.15s; }
        .meta-select:focus { border-color: #1D9E75; }
        .meta-input { font-size: 11px; padding: 2px 8px; border-radius: 20px; border: 1px solid #e0e0de; background: #fff; color: #666; outline: none; font-family: inherit; width: 100px; transition: border-color 0.15s; }
        .meta-input:focus { border-color: #1D9E75; }
        .meta-input.date-input { width: 120px; }
        .inline-add-actions { margin-left: auto; display: flex; gap: 4px; }
        .ia-btn { font-size: 11px; padding: 3px 12px; border-radius: 6px; border: none; cursor: pointer; font-weight: 500; font-family: inherit; transition: background 0.15s; }
        .ia-btn.save { background: #1D9E75; color: #fff; }
        .ia-btn.save:hover { background: #178a64; }
        .ia-btn.cancel { background: #f0f0ee; color: #888; }
        .ia-btn.cancel:hover { background: #e4e4e2; }

        /* add trigger */
        .add-trigger { display: flex; align-items: center; gap: 6px; padding: 7px 12px; color: #bbb; font-size: 12px; cursor: pointer; transition: color 0.15s; border-radius: 8px; margin-bottom: 6px; }
        .add-trigger:hover { color: #1D9E75; background: rgba(29,158,117,0.04); }
        .add-trigger .plus { font-size: 15px; font-weight: 300; }
        .add-trigger .hint { margin-left: auto; font-size: 10px; color: #ddd; }

        /* ── card ── */
        @keyframes cardIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

        .task-card {
            background: #fff; border: 0.5px solid #e0e0de; border-radius: 10px;
            padding: 9px 12px 9px 12px; margin-bottom: 5px;
            transition: border-color 0.2s, box-shadow 0.2s, opacity 0.4s, transform 0.2s;
            animation: cardIn 0.25s cubic-bezier(0.4, 0, 0.2, 1) both;
            overflow: hidden; position: relative;
            border-left: 3px solid transparent;
        }
        .task-card.pri-high { border-left-color: #E05555; }
        .task-card.pri-med  { border-left-color: #E8A830; }
        .task-card.pri-low  { border-left-color: #7BBF4A; }
        .task-card:hover { border-color: #c0c0bc; box-shadow: 0 1px 6px rgba(0,0,0,0.04); }
        .task-card.done { opacity: 0.4; }
        .task-card.done:hover { box-shadow: none; }
        .task-card.dragging { opacity: 0.4; transform: scale(0.98); }
        .task-card.drag-over { border-color: #1D9E75; box-shadow: 0 0 0 2px rgba(29,158,117,0.15); }

        .task-top { display: flex; align-items: flex-start; gap: 8px; }

        .drag-handle { cursor: grab; color: #ddd; font-size: 11px; padding: 2px 1px; flex-shrink: 0; user-select: none; transition: color 0.15s; line-height: 1; }
        .drag-handle:hover { color: #999; }
        .drag-handle:active { cursor: grabbing; }

        @keyframes checkPop { 0% { transform: scale(1); } 40% { transform: scale(1.25); } 100% { transform: scale(1); } }
        .checkbox {
            width: 17px; height: 17px; border-radius: 50%; border: 1.5px solid #ccc;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0; margin-top: 1px; cursor: pointer;
            transition: background 0.2s, border-color 0.2s;
        }
        .checkbox:hover { border-color: #1D9E75; }
        .checkbox.checked { background: #1D9E75; border-color: #1D9E75; animation: checkPop 0.3s ease; }
        .check-icon { display: none; width: 9px; height: 7px; }
        .check-icon path { fill: none; stroke: white; stroke-width: 2.2; stroke-linecap: round; stroke-linejoin: round; }
        .checkbox.checked .check-icon { display: block; }

        .task-body { flex: 1; min-width: 0; }
        .task-title { font-size: 13px; font-weight: 500; color: #1a1a1a; line-height: 1.35; cursor: text; }
        .task-card.done .task-title { text-decoration: line-through; color: #aaa; }
        .task-meta { display: flex; gap: 4px; flex-wrap: wrap; margin-top: 4px; align-items: center; }
        .badge { font-size: 10px; padding: 1px 7px; border-radius: 20px; font-weight: 500; cursor: pointer; transition: opacity 0.15s; }
        .badge:hover { opacity: 0.7; }
        .badge-app      { background: #E6F1FB; color: #185FA5; }
        .badge-pri-high { background: #FCEBEB; color: #A32D2D; }
        .badge-pri-med  { background: #FFF3DC; color: #854F0B; }
        .badge-pri-low  { background: #EAF3DE; color: #3B6D11; }
        .badge-blocker  { background: #FBEAF0; color: #993556; }
        .badge-date { background: #f5f5f3; color: #999; cursor: default; }
        .badge-date.overdue { background: #FCEBEB; color: #A32D2D; }
        .badge-date.soon { background: #FFF3DC; color: #854F0B; }
        .owner-avatar { width: 17px; height: 17px; border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; font-size: 7px; font-weight: 600; flex-shrink: 0; cursor: pointer; transition: opacity 0.15s; }
        .owner-avatar:hover { opacity: 0.7; }
        .avatar-boris { background: #E6F1FB; color: #185FA5; }
        .avatar-marat { background: #EAF3DE; color: #3B6D11; }
        .avatar-other { background: #F1EFE8; color: #5F5E5A; }

        /* mini progress */
        .mini-progress { display: flex; align-items: center; gap: 4px; margin-left: auto; flex-shrink: 0; cursor: pointer; }
        .mini-bar { width: 32px; height: 3px; background: #eee; border-radius: 2px; overflow: hidden; }
        .mini-fill { height: 100%; background: #5DCAA5; border-radius: 2px; transition: width 0.3s ease; }
        .mini-label { font-size: 10px; color: #bbb; }

        .flag { font-size: 11px; color: #aaa; margin-top: 3px; line-height: 1.4; font-style: italic; }

        /* action buttons */
        .task-actions { position: absolute; top: 5px; right: 5px; display: flex; gap: 2px; opacity: 0; transition: opacity 0.15s; }
        .task-card:hover .task-actions { opacity: 1; }
        .act-btn { width: 22px; height: 22px; border-radius: 5px; border: none; background: #f5f5f3; color: #bbb; cursor: pointer; font-size: 12px; display: flex; align-items: center; justify-content: center; transition: all 0.15s; }
        .act-btn:hover { background: #eee; color: #555; }
        .act-btn.danger:hover { background: #FCEBEB; color: #A32D2D; }

        /* subtasks */
        .subtasks-wrap { display: grid; grid-template-rows: 0fr; transition: grid-template-rows 0.25s ease; }
        .subtasks-wrap.open { grid-template-rows: 1fr; }
        .subtasks-inner { overflow: hidden; }
        .subtasks { margin-top: 8px; padding-top: 7px; border-top: 0.5px solid #f0f0ee; }
        .subtask { display: flex; align-items: center; gap: 6px; padding: 2.5px 0; cursor: pointer; transition: opacity 0.15s; }
        .subtask:hover { opacity: 0.7; }
        .subtask-box {
            width: 13px; height: 13px; border: 1px solid #d0d0ce; border-radius: 3px;
            flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            transition: all 0.15s;
        }
        .subtask-box.checked { background: #5DCAA5; border-color: #5DCAA5; }
        .subtask-box.checked::after { content: ''; display: block; width: 5px; height: 3.5px; border-left: 1.5px solid white; border-bottom: 1.5px solid white; transform: rotate(-45deg) translateY(-0.5px); }
        .subtask-label { font-size: 12px; color: #555; flex: 1; }
        .subtask.done .subtask-label { text-decoration: line-through; color: #ccc; }
        .subtask-del { opacity: 0; color: #ddd; cursor: pointer; font-size: 13px; transition: all 0.15s; padding: 0 3px; }
        .subtask:hover .subtask-del { opacity: 1; }
        .subtask-del:hover { color: #A32D2D; }
        .add-subtask-row { display: flex; gap: 4px; margin-top: 5px; }
        .add-subtask-input { flex: 1; border: 1px solid #eee; border-radius: 5px; padding: 4px 8px; font-size: 11px; outline: none; font-family: inherit; }
        .add-subtask-input:focus { border-color: #1D9E75; }
        .add-subtask-btn { background: #1D9E75; color: #fff; border: none; border-radius: 5px; padding: 4px 10px; font-size: 11px; cursor: pointer; font-weight: 500; }
        .add-subtask-btn:hover { background: #178a64; }

        /* done section */
        .done-section { margin-top: 12px; }
        .done-toggle { display: flex; align-items: center; gap: 6px; padding: 6px 0; cursor: pointer; color: #bbb; font-size: 12px; transition: color 0.15s; }
        .done-toggle:hover { color: #888; }
        .done-toggle .arrow { font-size: 10px; transition: transform 0.2s; }
        .done-toggle .arrow.open { transform: rotate(90deg); }

        /* modal (for edit) */
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.3); z-index: 100; display: flex; align-items: center; justify-content: center; animation: fadeIn 0.12s ease; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        .modal { background: #fff; border-radius: 14px; padding: 20px; width: 90%; max-width: 440px; box-shadow: 0 16px 50px rgba(0,0,0,0.15); animation: modalIn 0.18s ease; }
        @keyframes modalIn { from { opacity: 0; transform: translateY(16px) scale(0.97); } to { opacity: 1; transform: translateY(0) scale(1); } }
        .modal h3 { font-size: 15px; font-weight: 500; margin-bottom: 14px; }
        .modal-field { margin-bottom: 10px; }
        .modal-field label { display: block; font-size: 11px; color: #999; margin-bottom: 3px; font-weight: 500; }
        .modal-field input, .modal-field select, .modal-field textarea { width: 100%; border: 1px solid #e0e0de; border-radius: 8px; padding: 7px 10px; font-size: 12px; outline: none; font-family: inherit; transition: border-color 0.15s; }
        .modal-field input:focus, .modal-field select:focus, .modal-field textarea:focus { border-color: #1D9E75; }
        .modal-field textarea { resize: vertical; min-height: 50px; }
        .modal-buttons { display: flex; gap: 6px; justify-content: flex-end; margin-top: 14px; }
        .modal-btn { padding: 7px 16px; border-radius: 7px; border: none; font-size: 12px; font-weight: 500; cursor: pointer; transition: background 0.15s; font-family: inherit; }
        .modal-btn.cancel { background: #f0f0ee; color: #666; }
        .modal-btn.cancel:hover { background: #e4e4e2; }
        .modal-btn.save { background: #1D9E75; color: #fff; }
        .modal-btn.save:hover { background: #178a64; }
        .modal-btn.delete { background: #FCEBEB; color: #A32D2D; }
        .modal-btn.delete:hover { background: #f5d5d5; }

        /* inline edit */
        .inline-edit { border: 1px solid #1D9E75; border-radius: 5px; padding: 3px 7px; font-size: 13px; font-weight: 500; outline: none; width: 100%; font-family: inherit; }

        /* popover for quick-change */
        .popover { position: absolute; background: #fff; border: 1px solid #e0e0de; border-radius: 8px; padding: 4px; box-shadow: 0 4px 16px rgba(0,0,0,0.1); z-index: 50; animation: fadeIn 0.1s ease; }
        .popover-item { display: block; width: 100%; padding: 4px 12px; font-size: 11px; border: none; background: none; cursor: pointer; border-radius: 4px; text-align: left; font-family: inherit; color: #444; transition: background 0.1s; }
        .popover-item:hover { background: #f5f5f3; }
        .popover-item.active { font-weight: 600; color: #1D9E75; }

        /* toast */
        .toast { position: fixed; bottom: 20px; left: 50%; transform: translateX(-50%); background: #333; color: #fff; padding: 8px 18px; border-radius: 8px; font-size: 12px; z-index: 200; animation: toastIn 0.2s ease, toastOut 0.2s ease 1.8s forwards; pointer-events: none; }
        @keyframes toastIn { from { opacity: 0; transform: translateX(-50%) translateY(8px); } to { opacity: 1; transform: translateX(-50%) translateY(0); } }
        @keyframes toastOut { from { opacity: 1; } to { opacity: 0; } }

        #confetti-canvas { position: fixed; top: 0; left: 0; width: 100%; height: 100%; pointer-events: none; z-index: 999; }
        .empty { text-align: center; color: #bbb; font-size: 12px; padding: 2rem 0; }

        /* kbd hint */
        .kbd { display: inline-block; font-size: 9px; padding: 1px 4px; border-radius: 3px; background: #f0f0ee; color: #bbb; font-family: inherit; margin-left: 2px; }
    </style>
</head>
<body>
<canvas id="confetti-canvas"></canvas>
<div class="root">
    <div class="header">
        <h2>Sprint задачи</h2>
        <span class="progress-label" id="prog-label"></span>
    </div>
    <div class="progress-bar"><div class="progress-fill" id="prog-fill" style="width:0%"></div></div>

    <div class="toolbar">
        <div class="search-box">
            <input type="text" id="search" placeholder="Поиск..." />
            <span class="search-hint"><span class="kbd">/</span></span>
        </div>
        <div class="filters">
            <div class="filter-group" id="filter-owner"></div>
            <div class="filter-sep"></div>
            <div class="filter-group" id="filter-priority"></div>
            <div class="filter-sep"></div>
            <div class="filter-group" id="filter-status"></div>
        </div>
    </div>

    <!-- inline add -->
    <div class="inline-add" id="inline-add">
        <input class="inline-add-title" id="ia-title" placeholder="Название задачи..." />
        <div class="inline-add-meta">
            <select class="meta-select" id="ia-pri">
                <option value="high">Срочно</option>
                <option value="med" selected>Важно</option>
                <option value="low">Потом</option>
            </select>
            <select class="meta-select" id="ia-owner">
                <option value="Борис">Борис</option>
                <option value="Марат">Марат</option>
            </select>
            <input class="meta-input" id="ia-app" placeholder="Приложение..." />
            <input class="meta-input date-input" id="ia-deadline" type="date" title="Дедлайн" />
            <div class="inline-add-actions">
                <button class="ia-btn cancel" onclick="closeInlineAdd()">Отмена</button>
                <button class="ia-btn save" onclick="submitInlineAdd()">Добавить</button>
            </div>
        </div>
    </div>

    <div class="add-trigger" id="add-trigger" onclick="openInlineAdd()">
        <span class="plus">+</span> Новая задача <span class="hint"><span class="kbd">N</span></span>
    </div>

    <div id="tasks"></div>
    <div class="done-section" id="done-section"></div>
</div>

<div id="modal-root"></div>
<div id="popover-root"></div>

<script>
const API = 'api.php';
let tasks = [];
let expanded = {};
let showDone = false;
let searchQuery = '';
let activeOwner = 'all', activePriority = 'all', activeStatus = 'all';
let dragSrcId = null;

const priLabel = { high:'Срочно', med:'Важно', low:'Потом' };
const priClass = { high:'badge-pri-high', med:'badge-pri-med', low:'badge-pri-low' };
const priColors = { high:'#E05555', med:'#E8A830', low:'#7BBF4A' };

/* ── API ── */
async function api(method, body, action) {
    const url = action ? `${API}?action=${action}` : API;
    const opts = { method, headers: { 'Content-Type': 'application/json' } };
    if (body) opts.body = JSON.stringify(body);
    const r = await fetch(url, opts);
    return r.json();
}
async function loadTasks() { tasks = await api('GET'); fullRender(); }
async function saveTask(task) { await api('PUT', task); }
async function createTask(task) { const c = await api('POST', task); tasks.push(c); fullRender(); toast('Задача добавлена'); }
async function deleteTask(id) { await api('DELETE', {id}); tasks = tasks.filter(t => t.id !== id); fullRender(); toast('Задача удалена'); }
async function reorderTasks(ids) { await api('POST', {ids}, 'reorder'); }

/* ── confetti ── */
const canvas = document.getElementById('confetti-canvas');
const ctx = canvas.getContext('2d');
let particles = [], animating = false;
function resizeCanvas() { canvas.width = innerWidth; canvas.height = innerHeight; }
resizeCanvas(); addEventListener('resize', resizeCanvas);
function spawnConfetti(x, y) {
    const cols = ['#1D9E75','#5DCAA5','#185FA5','#85B7EB','#EF9F27','#FAC775'];
    for (let k = 0; k < 24; k++) {
        const a = Math.random()*Math.PI*2, s = 2+Math.random()*4;
        particles.push({x,y,vx:Math.cos(a)*s,vy:Math.sin(a)*s-3,size:3+Math.random()*4,color:cols[~~(Math.random()*cols.length)],life:1,decay:0.025+Math.random()*0.02,rot:Math.random()*6.28,rotV:(Math.random()-0.5)*0.2});
    }
    if (!animating) animConfetti();
}
function animConfetti() {
    animating = true; ctx.clearRect(0,0,canvas.width,canvas.height);
    particles = particles.filter(p => p.life > 0);
    particles.forEach(p => {
        p.x+=p.vx; p.y+=p.vy; p.vy+=0.18; p.life-=p.decay; p.rot+=p.rotV;
        ctx.save(); ctx.globalAlpha=Math.max(0,p.life); ctx.translate(p.x,p.y); ctx.rotate(p.rot);
        ctx.fillStyle=p.color; ctx.fillRect(-p.size/2,-p.size/2,p.size,p.size*0.6); ctx.restore();
    });
    if (particles.length > 0) requestAnimationFrame(animConfetti);
    else { animating=false; ctx.clearRect(0,0,canvas.width,canvas.height); }
}

/* ── toast ── */
function toast(msg) { const el=document.createElement('div'); el.className='toast'; el.textContent=msg; document.body.appendChild(el); setTimeout(()=>el.remove(),2200); }

/* ── helpers ── */
function avatarClass(o) { return o==='Борис'?'avatar-boris':o==='Марат'?'avatar-marat':'avatar-other'; }
function findTask(id) { return tasks.find(t => t.id===id); }

function isVisible(t) {
    if (activeOwner!=='all' && t.owner!==activeOwner) return false;
    if (activePriority!=='all' && t.priority!==activePriority) return false;
    if (activeStatus==='open' && t.done) return false;
    if (activeStatus==='done' && !t.done) return false;
    if (searchQuery) {
        const q = searchQuery.toLowerCase();
        if (!(t.title||'').toLowerCase().includes(q) && !(t.app||'').toLowerCase().includes(q) && !(t.flag||'').toLowerCase().includes(q)) return false;
    }
    return true;
}

function deadlineInfo(d) {
    if (!d) return null;
    const now = new Date(); now.setHours(0,0,0,0);
    const dl = new Date(d+'T00:00:00');
    const diff = Math.round((dl-now)/(1000*60*60*24));
    const fmt = dl.toLocaleDateString('ru-RU',{day:'numeric',month:'short'});
    if (diff < 0) return {label:fmt, cls:'overdue'};
    if (diff <= 3) return {label:fmt, cls:'soon'};
    return {label:fmt, cls:''};
}

function updateProgress() {
    const vis = tasks.filter(isVisible);
    const done = vis.filter(t => t.done).length;
    document.getElementById('prog-label').textContent = `${done} / ${vis.length}`;
    const pct = vis.length ? Math.round(done/vis.length*100) : 0;
    document.getElementById('prog-fill').style.width = `${pct}%`;
}

/* ── search ── */
document.getElementById('search').addEventListener('input', e => { searchQuery = e.target.value; fullRender(); });

/* ── filters ── */
function buildFilters() {
    const ownerDef = [{val:'all',label:'Все',c:'c-all'},{val:'Борис',label:'Б',c:'c-boris'},{val:'Марат',label:'М',c:'c-marat'}];
    const priDef = [{val:'all',label:'Все',c:'c-all'},{val:'high',label:'!!!',c:'c-high'},{val:'med',label:'!!',c:'c-med'},{val:'low',label:'!',c:'c-low'}];
    const stDef = [{val:'all',label:'Все',c:'c-all'},{val:'open',label:'Открыто',c:'c-open'},{val:'done',label:'Готово',c:'c-done'}];
    function bg(id,defs,get,set) {
        const el=document.getElementById(id); el.innerHTML='';
        defs.forEach(d => {
            const b=document.createElement('button'); b.className='chip '+d.c+(get()===d.val?' active':'');
            b.textContent=d.label; b.onclick=()=>{set(d.val);buildFilters();fullRender();}; el.appendChild(b);
        });
    }
    bg('filter-owner',ownerDef,()=>activeOwner,v=>activeOwner=v);
    bg('filter-priority',priDef,()=>activePriority,v=>activePriority=v);
    bg('filter-status',stDef,()=>activeStatus,v=>activeStatus=v);
}

/* ── inline add ── */
function openInlineAdd() {
    document.getElementById('inline-add').classList.add('visible');
    document.getElementById('add-trigger').style.display='none';
    setTimeout(()=>document.getElementById('ia-title').focus(),50);
}
function closeInlineAdd() {
    document.getElementById('inline-add').classList.remove('visible');
    document.getElementById('add-trigger').style.display='';
    document.getElementById('ia-title').value='';
    document.getElementById('ia-app').value='';
    document.getElementById('ia-deadline').value='';
}
async function submitInlineAdd() {
    const title = document.getElementById('ia-title').value.trim();
    if (!title) { document.getElementById('ia-title').style.borderBottomColor='#E05555'; return; }
    await createTask({
        title, app: document.getElementById('ia-app').value.trim()||'',
        priority: document.getElementById('ia-pri').value,
        owner: document.getElementById('ia-owner').value,
        deadline: document.getElementById('ia-deadline').value||'',
        flag:'', subtasks:[], done:false, blocker:false
    });
    closeInlineAdd();
}
document.getElementById('ia-title').addEventListener('keydown', e => {
    if (e.key==='Enter') submitInlineAdd();
    if (e.key==='Escape') closeInlineAdd();
});

/* ── toggle ── */
async function toggleDone(id, el) {
    const t=findTask(id); t.done=!t.done;
    if (t.done && el) { const r=el.getBoundingClientRect(); spawnConfetti(r.left+r.width/2,r.top+r.height/2); }
    await saveTask({id:t.id,done:t.done}); fullRender();
}
async function toggleSubtask(tid,si,el) {
    const t=findTask(tid); t.subtasks[si].done=!t.subtasks[si].done;
    if (t.subtasks[si].done && el) { const r=el.getBoundingClientRect(); spawnConfetti(r.left+r.width/2,r.top+r.height/2); }
    await saveTask({id:t.id,subtasks:t.subtasks}); fullRender();
}

/* ── inline edits ── */
function editTitle(id, span) {
    const t=findTask(id), inp=document.createElement('input'); inp.className='inline-edit'; inp.value=t.title;
    span.replaceWith(inp); inp.focus(); inp.select();
    const done=async()=>{ const v=inp.value.trim(); if(v&&v!==t.title){t.title=v; await saveTask({id:t.id,title:v}); toast('Обновлено');} fullRender(); };
    inp.onblur=done; inp.onkeydown=e=>{if(e.key==='Enter')inp.blur();if(e.key==='Escape'){inp.value=t.title;inp.blur();}};
}
function editSubtaskLabel(tid,si,span) {
    const t=findTask(tid), inp=document.createElement('input'); inp.className='inline-edit'; inp.style.fontSize='12px'; inp.value=t.subtasks[si].label;
    span.replaceWith(inp); inp.focus(); inp.select();
    const done=async()=>{ const v=inp.value.trim(); if(v&&v!==t.subtasks[si].label){t.subtasks[si].label=v; await saveTask({id:t.id,subtasks:t.subtasks}); toast('Обновлено');} fullRender(); };
    inp.onblur=done; inp.onkeydown=e=>{if(e.key==='Enter')inp.blur();if(e.key==='Escape'){inp.value=t.subtasks[si].label;inp.blur();}};
}
async function addSubtask(tid,inp) {
    const v=inp.value.trim(); if(!v)return; const t=findTask(tid);
    t.subtasks.push({label:v,done:false}); await saveTask({id:t.id,subtasks:t.subtasks}); inp.value=''; fullRender();
}
async function delSubtask(tid,si) { const t=findTask(tid); t.subtasks.splice(si,1); await saveTask({id:t.id,subtasks:t.subtasks}); fullRender(); }

/* ── popover (quick change priority/owner) ── */
function closePopover() { document.getElementById('popover-root').innerHTML=''; }
document.addEventListener('click', closePopover);

function showPopover(anchor, items, current, onSelect) {
    closePopover();
    const rect = anchor.getBoundingClientRect();
    const root = document.getElementById('popover-root');
    const pop = document.createElement('div');
    pop.className = 'popover';
    pop.style.top = (rect.bottom + 4 + scrollY) + 'px';
    pop.style.left = rect.left + 'px';
    items.forEach(it => {
        const btn = document.createElement('button');
        btn.className = 'popover-item' + (it.val === current ? ' active' : '');
        btn.textContent = it.label;
        btn.onclick = e => { e.stopPropagation(); onSelect(it.val); closePopover(); };
        pop.appendChild(btn);
    });
    pop.onclick = e => e.stopPropagation();
    root.appendChild(pop);
}

async function quickPriority(id, anchorEl) {
    const t = findTask(id);
    showPopover(anchorEl, [{val:'high',label:'Срочно'},{val:'med',label:'Важно'},{val:'low',label:'Потом'}], t.priority, async v => {
        t.priority=v; await saveTask({id:t.id,priority:v}); fullRender(); toast('Приоритет изменён');
    });
}
async function quickOwner(id, anchorEl) {
    const t = findTask(id);
    showPopover(anchorEl, [{val:'Борис',label:'Борис'},{val:'Марат',label:'Марат'}], t.owner, async v => {
        t.owner=v; await saveTask({id:t.id,owner:v}); fullRender(); toast('Ответственный изменён');
    });
}

/* ── modal (edit) ── */
function closeModal() { document.getElementById('modal-root').innerHTML=''; }
function openEditModal(id) {
    const t=findTask(id);
    document.getElementById('modal-root').innerHTML = `
    <div class="modal-overlay" onclick="if(event.target===this)closeModal()">
        <div class="modal">
            <h3>Редактировать</h3>
            <div class="modal-field"><label>Название</label><input id="m-title" value="${(t.title||'').replace(/"/g,'&quot;')}" /></div>
            <div class="modal-field"><label>Приложение</label><input id="m-app" value="${(t.app||'').replace(/"/g,'&quot;')}" /></div>
            <div class="modal-field"><label>Приоритет</label>
                <select id="m-pri"><option value="high" ${t.priority==='high'?'selected':''}>Срочно</option><option value="med" ${t.priority==='med'?'selected':''}>Важно</option><option value="low" ${t.priority==='low'?'selected':''}>Потом</option></select></div>
            <div class="modal-field"><label>Ответственный</label>
                <select id="m-owner"><option value="Борис" ${t.owner==='Борис'?'selected':''}>Борис</option><option value="Марат" ${t.owner==='Марат'?'selected':''}>Марат</option></select></div>
            <div class="modal-field"><label>Дедлайн</label><input id="m-deadline" type="date" value="${t.deadline||''}" /></div>
            <div class="modal-field"><label>Блокер</label>
                <select id="m-blocker"><option value="0" ${!t.blocker?'selected':''}>Нет</option><option value="1" ${t.blocker?'selected':''}>Да</option></select></div>
            <div class="modal-field"><label>Описание</label><textarea id="m-flag">${t.flag||''}</textarea></div>
            <div class="modal-buttons">
                <button class="modal-btn delete" onclick="if(confirm('Удалить?')){deleteTask(${t.id});closeModal()}">Удалить</button>
                <button class="modal-btn cancel" onclick="closeModal()">Отмена</button>
                <button class="modal-btn save" onclick="submitEdit(${t.id})">Сохранить</button>
            </div>
        </div>
    </div>`;
}
async function submitEdit(id) {
    const t=findTask(id);
    t.title=document.getElementById('m-title').value.trim()||t.title;
    t.app=document.getElementById('m-app').value.trim();
    t.priority=document.getElementById('m-pri').value;
    t.owner=document.getElementById('m-owner').value;
    t.deadline=document.getElementById('m-deadline').value||'';
    t.blocker=document.getElementById('m-blocker').value==='1';
    t.flag=document.getElementById('m-flag').value.trim();
    await saveTask(t); closeModal(); fullRender(); toast('Сохранено');
}

/* ── drag & drop ── */
function onDragStart(e,id){dragSrcId=id;e.dataTransfer.effectAllowed='move';e.target.closest('.task-card').classList.add('dragging');}
function onDragEnd(){dragSrcId=null;document.querySelectorAll('.task-card').forEach(c=>c.classList.remove('dragging','drag-over'));}
function onDragOver(e){e.preventDefault();e.target.closest('.task-card')?.classList.add('drag-over');}
function onDragLeave(e){e.target.closest('.task-card')?.classList.remove('drag-over');}
async function onDrop(e,tid){e.preventDefault();if(dragSrcId===null||dragSrcId===tid)return;const si=tasks.findIndex(t=>t.id===dragSrcId),ti=tasks.findIndex(t=>t.id===tid);const[m]=tasks.splice(si,1);tasks.splice(ti,0,m);await reorderTasks(tasks.map(t=>t.id));fullRender();toast('Порядок обновлён');}

/* ── render card ── */
function renderCard(t, container) {
    const subDone=(t.subtasks||[]).filter(s=>s.done).length, subTotal=(t.subtasks||[]).length;
    const dl = deadlineInfo(t.deadline);
    const card=document.createElement('div');
    card.className='task-card pri-'+t.priority+(t.done?' done':'');
    card.draggable=true;
    card.addEventListener('dragstart',e=>onDragStart(e,t.id));
    card.addEventListener('dragend',onDragEnd);
    card.addEventListener('dragover',onDragOver);
    card.addEventListener('dragleave',onDragLeave);
    card.addEventListener('drop',e=>onDrop(e,t.id));

    card.innerHTML = `
        <div class="task-actions">
            <button class="act-btn" title="Редактировать" data-edit="${t.id}">&#9998;</button>
            <button class="act-btn danger" title="Удалить" data-del="${t.id}">&times;</button>
        </div>
        <div class="task-top">
            <div class="drag-handle">&#8942;&#8942;</div>
            <div class="checkbox ${t.done?'checked':''}" data-task="${t.id}">
                <svg class="check-icon" viewBox="0 0 10 7"><path d="M1 3.5L3.5 6L9 1"/></svg>
            </div>
            <div class="task-body">
                <span class="task-title" data-title-id="${t.id}">${t.title}</span>
                <div class="task-meta">
                    <div class="owner-avatar ${avatarClass(t.owner)}" data-qowner="${t.id}">${(t.owner||'?').slice(0,2).toUpperCase()}</div>
                    ${t.app?`<span class="badge badge-app">${t.app}</span>`:''}
                    <span class="badge ${priClass[t.priority]}" data-qpri="${t.id}">${priLabel[t.priority]}</span>
                    ${t.blocker?'<span class="badge badge-blocker">Блокер</span>':''}
                    ${dl?`<span class="badge badge-date ${dl.cls}">${dl.label}</span>`:''}
                </div>
                ${t.flag?`<div class="flag">${t.flag}</div>`:''}
            </div>
            ${subTotal>0?`
            <div class="mini-progress" data-expand="${t.id}">
                <div class="mini-bar"><div class="mini-fill" style="width:${subTotal?Math.round(subDone/subTotal*100):0}%"></div></div>
                <span class="mini-label">${subDone}/${subTotal}</span>
            </div>`:''}
        </div>
        <div class="subtasks-wrap ${expanded[t.id]?'open':''}">
            <div class="subtasks-inner"><div class="subtasks">
                ${(t.subtasks||[]).map((s,j)=>`
                    <div class="subtask ${s.done?'done':''}" data-si="${t.id}" data-sj="${j}">
                        <div class="subtask-box ${s.done?'checked':''}"></div>
                        <span class="subtask-label" data-sl-task="${t.id}" data-sl-idx="${j}">${s.label}</span>
                        <span class="subtask-del" data-sdel-task="${t.id}" data-sdel-idx="${j}">&times;</span>
                    </div>`).join('')}
                <div class="add-subtask-row">
                    <input class="add-subtask-input" placeholder="+ подзадача..." data-addsub="${t.id}" />
                    <button class="add-subtask-btn" data-addsubbtn="${t.id}">+</button>
                </div>
            </div></div>
        </div>`;
    container.appendChild(card);
}

/* ── bind events ── */
function bindEvents(container) {
    container.querySelectorAll('[data-task]').forEach(el => el.onclick = e => { e.stopPropagation(); toggleDone(+el.dataset.task, el); });
    container.querySelectorAll('[data-expand]').forEach(el => el.onclick = e => { e.stopPropagation(); expanded[+el.dataset.expand]=!expanded[+el.dataset.expand]; fullRender(); });
    container.querySelectorAll('[data-si]').forEach(el => el.onclick = e => {
        if(e.target.closest('.subtask-del')||e.target.closest('.subtask-label'))return;
        e.stopPropagation(); toggleSubtask(+el.dataset.si,+el.dataset.sj,el.querySelector('.subtask-box'));
    });
    container.querySelectorAll('[data-title-id]').forEach(el => el.ondblclick = e => { e.stopPropagation(); editTitle(+el.dataset.titleId, el); });
    container.querySelectorAll('[data-sl-task]').forEach(el => el.ondblclick = e => { e.stopPropagation(); editSubtaskLabel(+el.dataset.slTask,+el.dataset.slIdx,el); });
    container.querySelectorAll('[data-sdel-task]').forEach(el => el.onclick = e => { e.stopPropagation(); delSubtask(+el.dataset.sdelTask,+el.dataset.sdelIdx); });
    container.querySelectorAll('[data-edit]').forEach(el => el.onclick = e => { e.stopPropagation(); openEditModal(+el.dataset.edit); });
    container.querySelectorAll('[data-del]').forEach(el => el.onclick = e => { e.stopPropagation(); if(confirm('Удалить?'))deleteTask(+el.dataset.del); });
    container.querySelectorAll('[data-qpri]').forEach(el => el.onclick = e => { e.stopPropagation(); quickPriority(+el.dataset.qpri, el); });
    container.querySelectorAll('[data-qowner]').forEach(el => el.onclick = e => { e.stopPropagation(); quickOwner(+el.dataset.qowner, el); });
    container.querySelectorAll('[data-addsubbtn]').forEach(el => {
        const tid=+el.dataset.addsubbtn, inp=container.querySelector(`[data-addsub="${tid}"]`);
        el.onclick=e=>{e.stopPropagation();addSubtask(tid,inp);};
        inp.onkeydown=e=>{if(e.key==='Enter')addSubtask(tid,inp);};
    });
}

/* ── full render ── */
function fullRender() {
    const openTasks = tasks.filter(t => !t.done && isVisible(t));
    const doneTasks = tasks.filter(t => t.done && isVisible(t));

    const container = document.getElementById('tasks');
    container.innerHTML = '';
    openTasks.forEach(t => renderCard(t, container));
    if (openTasks.length === 0 && doneTasks.length === 0) {
        container.innerHTML = '<div class="empty">Нет задач по выбранным фильтрам</div>';
    }
    bindEvents(container);

    // done section
    const ds = document.getElementById('done-section');
    ds.innerHTML = '';
    if (doneTasks.length > 0) {
        ds.innerHTML = `<div class="done-toggle" id="done-toggle"><span class="arrow ${showDone?'open':''}">&#9654;</span> ${doneTasks.length} выполненных</div>`;
        if (showDone) {
            const wrap = document.createElement('div');
            doneTasks.forEach(t => renderCard(t, wrap));
            ds.appendChild(wrap);
            bindEvents(wrap);
        }
        document.getElementById('done-toggle').onclick = () => { showDone=!showDone; fullRender(); };
    }
    updateProgress();
}

/* ── hotkeys ── */
document.addEventListener('keydown', e => {
    if (e.target.tagName==='INPUT'||e.target.tagName==='TEXTAREA'||e.target.tagName==='SELECT') {
        if (e.key==='Escape') { e.target.blur(); closeInlineAdd(); closeModal(); }
        return;
    }
    if (e.key==='n'||e.key==='N') { e.preventDefault(); openInlineAdd(); }
    if (e.key==='/') { e.preventDefault(); document.getElementById('search').focus(); }
    if (e.key==='Escape') { closeModal(); closePopover(); }
});

/* ── init ── */
buildFilters();
loadTasks();
</script>
</body>
</html>
