<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8"/>
    <meta name="viewport" content="width=device-width,initial-scale=1.0"/>
    <meta name="apple-mobile-web-app-capable" content="yes"/>
    <meta name="apple-mobile-web-app-status-bar-style" content="black-translucent"/>
    <meta name="theme-color" content="#f5f5f3"/>
    <link rel="manifest" href="manifest.json"/>
    <title>Brite Sprint</title>
    <style>
        *,*::before,*::after{box-sizing:border-box;margin:0;padding:0}
        body{font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',sans-serif;background:#f5f5f3;color:#1a1a1a;padding:1rem 1rem 5rem}
        .root{max-width:960px;margin:0 auto}

        /* header */
        .hdr{display:flex;align-items:center;gap:10px;margin-bottom:.6rem;flex-wrap:wrap}
        .hdr h2{font-size:17px;font-weight:600;letter-spacing:-.3px}
        .prog-lbl{font-size:11px;color:#999}
        .prog-bar{height:3px;background:#e8e8e6;border-radius:2px;margin-bottom:.8rem;overflow:hidden}
        .prog-fill{height:100%;background:#1D9E75;border-radius:2px;transition:width .5s ease}
        .view-toggle{margin-left:auto;display:flex;gap:2px}
        .view-btn{font-size:11px;padding:3px 10px;border-radius:6px;border:1px solid #e0e0de;background:#fff;color:#888;cursor:pointer;font-family:inherit;transition:all .15s}
        .view-btn.active{background:#444;color:#fff;border-color:#444}

        /* toolbar */
        .toolbar{display:flex;flex-wrap:wrap;gap:6px;margin-bottom:.8rem;align-items:center}
        .search-box{position:relative;flex:1;min-width:120px;max-width:200px}
        .search-box input{width:100%;border:1px solid #e0e0de;border-radius:7px;padding:4px 8px 4px 26px;font-size:11px;outline:none;font-family:inherit;background:#fff}
        .search-box input:focus{border-color:#1D9E75}
        .search-box::before{content:'';position:absolute;left:8px;top:50%;transform:translateY(-50%);width:11px;height:11px;background:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%23bbb'%3E%3Cpath d='M8 3a5 5 0 104.58 7.17l4.13 4.13a.75.75 0 001.06-1.06l-4.13-4.13A5 5 0 008 3zM4.5 8a3.5 3.5 0 117 0 3.5 3.5 0 01-7 0z'/%3E%3C/svg%3E") no-repeat center;background-size:contain}
        .filters{display:flex;flex-wrap:wrap;gap:4px;align-items:center}
        .fg{display:flex;gap:2px}
        .fsep{width:1px;height:14px;background:#e0e0de;margin:0 2px;align-self:center}
        .chip{font-size:10px;font-weight:500;padding:2px 8px;border-radius:16px;border:.5px solid #d0d0ce;background:#fff;color:#888;cursor:pointer;transition:all .12s;white-space:nowrap}
        .chip:hover{border-color:#aaa;color:#333}
        .chip.active{border-color:transparent;color:#fff}
        .chip.active.c-all{background:#444}.chip.active.c-boris{background:#185FA5}.chip.active.c-marat{background:#3B6D11}
        .chip.active.c-high{background:#A32D2D}.chip.active.c-med{background:#854F0B}.chip.active.c-low{background:#3B6D11}
        .chip.active.c-todo{background:#534AB7}.chip.active.c-progress{background:#E8A830}.chip.active.c-done{background:#1D9E75}

        /* inline add */
        .ia{background:#fff;border:1.5px solid #e0e0de;border-radius:10px;padding:10px 12px;margin-bottom:6px;display:none;animation:ci .2s ease both}
        .ia.visible{display:block}
        .ia-title{width:100%;border:none;outline:none;font-size:13px;font-weight:500;font-family:inherit;color:#1a1a1a;padding:0}
        .ia-title::placeholder{color:#ccc}
        .ia-row{display:flex;gap:5px;margin-top:7px;align-items:center;flex-wrap:wrap}
        .ia-sel{font-size:10px;padding:2px 7px;border-radius:16px;border:1px solid #e0e0de;background:#fff;color:#666;cursor:pointer;font-family:inherit;outline:none;-webkit-appearance:none;appearance:none}
        .ia-inp{font-size:10px;padding:2px 7px;border-radius:16px;border:1px solid #e0e0de;background:#fff;color:#666;outline:none;font-family:inherit;width:90px}
        .ia-inp:focus,.ia-sel:focus{border-color:#1D9E75}
        .ia-acts{margin-left:auto;display:flex;gap:3px}
        .ia-btn{font-size:10px;padding:3px 10px;border-radius:5px;border:none;cursor:pointer;font-weight:500;font-family:inherit}
        .ia-btn.save{background:#1D9E75;color:#fff}.ia-btn.cancel{background:#f0f0ee;color:#888}

        /* tag input in inline add */
        .tag-input-wrap{display:flex;gap:3px;flex-wrap:wrap;align-items:center}
        .tag-mini{font-size:9px;padding:1px 6px;border-radius:10px;font-weight:500;display:inline-flex;align-items:center;gap:2px}
        .tag-mini .tx{cursor:pointer;font-size:8px;opacity:.6}
        .tag-mini .tx:hover{opacity:1}

        .add-trig{display:flex;align-items:center;gap:5px;padding:6px 10px;color:#ccc;font-size:11px;cursor:pointer;border-radius:7px;margin-bottom:5px}
        .add-trig:hover{color:#1D9E75;background:rgba(29,158,117,.04)}
        .add-trig .kbd{font-size:8px;padding:1px 3px;border-radius:3px;background:#f0f0ee;color:#ddd;margin-left:auto}

        /* ── card ── */
        @keyframes ci{from{opacity:0;transform:translateY(6px)}to{opacity:1;transform:translateY(0)}}
        .card{background:#fff;border:.5px solid #e0e0de;border-radius:9px;padding:8px 10px;margin-bottom:4px;position:relative;overflow:hidden;border-left:3px solid transparent;transition:all .2s;animation:ci .2s ease both}
        .card:hover{border-color:#c0c0bc;box-shadow:0 1px 5px rgba(0,0,0,.04)}
        .card.pri-high{border-left-color:#E05555}.card.pri-med{border-left-color:#E8A830}.card.pri-low{border-left-color:#7BBF4A}
        .card.st-done{opacity:.4}
        .card.dragging{opacity:.4;transform:scale(.98)}.card.drag-over{border-color:#1D9E75;box-shadow:0 0 0 2px rgba(29,158,117,.15)}
        .card-top{display:flex;align-items:flex-start;gap:6px}
        .drag-h{cursor:grab;color:#ddd;font-size:10px;padding:1px;flex-shrink:0;user-select:none;line-height:1}.drag-h:hover{color:#999}
        .cb{width:16px;height:16px;border-radius:50%;border:1.5px solid #ccc;display:flex;align-items:center;justify-content:center;flex-shrink:0;cursor:pointer;transition:all .2s}
        .cb:hover{border-color:#1D9E75}
        .cb.checked{background:#1D9E75;border-color:#1D9E75}
        .cb.checked .ci{display:block}
        .ci{display:none;width:8px;height:6px}.ci path{fill:none;stroke:#fff;stroke-width:2.2;stroke-linecap:round;stroke-linejoin:round}
        .card-body{flex:1;min-width:0}
        .card-title{font-size:12px;font-weight:500;color:#1a1a1a;line-height:1.35;cursor:text}
        .card.st-done .card-title{text-decoration:line-through;color:#aaa}
        .card-meta{display:flex;gap:3px;flex-wrap:wrap;margin-top:3px;align-items:center}
        .bdg{font-size:9px;padding:1px 6px;border-radius:16px;font-weight:500;cursor:pointer;transition:opacity .15s}
        .bdg:hover{opacity:.7}
        .bdg-app{background:#E6F1FB;color:#185FA5}
        .bdg-h{background:#FCEBEB;color:#A32D2D}.bdg-m{background:#FFF3DC;color:#854F0B}.bdg-l{background:#EAF3DE;color:#3B6D11}
        .bdg-blk{background:#FBEAF0;color:#993556}
        .bdg-date{background:#f5f5f3;color:#999;cursor:default}.bdg-date.overdue{background:#FCEBEB;color:#A32D2D}.bdg-date.soon{background:#FFF3DC;color:#854F0B}
        .bdg-status{cursor:pointer}
        .bdg-status.s-todo{background:#EEEDF7;color:#534AB7}.bdg-status.s-progress{background:#FFF3DC;color:#854F0B}.bdg-status.s-done{background:#E3F5ED;color:#1D9E75}
        /* tags */
        .tag{font-size:8px;padding:1px 5px;border-radius:10px;font-weight:500}
        .ow{width:15px;height:15px;border-radius:50%;display:inline-flex;align-items:center;justify-content:center;font-size:7px;font-weight:600;cursor:pointer;transition:opacity .15s}
        .ow:hover{opacity:.7}
        .ow-b{background:#E6F1FB;color:#185FA5}.ow-m{background:#EAF3DE;color:#3B6D11}.ow-o{background:#F1EFE8;color:#5F5E5A}
        .flag{font-size:10px;color:#bbb;margin-top:2px;line-height:1.35;font-style:italic}
        /* mini progress */
        .mp{display:flex;align-items:center;gap:3px;margin-left:auto;flex-shrink:0;cursor:pointer}
        .mp-bar{width:28px;height:2.5px;background:#eee;border-radius:2px;overflow:hidden}
        .mp-fill{height:100%;background:#5DCAA5;border-radius:2px;transition:width .3s}
        .mp-lbl{font-size:9px;color:#ccc}
        /* card actions */
        .card-acts{position:absolute;top:4px;right:4px;display:flex;gap:1px;opacity:0;transition:opacity .15s}
        .card:hover .card-acts{opacity:1}
        .abtn{width:20px;height:20px;border-radius:4px;border:none;background:#f5f5f3;color:#ccc;cursor:pointer;font-size:10px;display:flex;align-items:center;justify-content:center;transition:all .12s}
        .abtn:hover{background:#eee;color:#555}
        .abtn.dng:hover{background:#FCEBEB;color:#A32D2D}

        /* subtasks */
        .subs-w{display:grid;grid-template-rows:0fr;transition:grid-template-rows .25s ease}
        .subs-w.open{grid-template-rows:1fr}
        .subs-in{overflow:hidden}
        .subs{margin-top:6px;padding-top:6px;border-top:.5px solid #f0f0ee}
        .sub{display:flex;align-items:center;gap:5px;padding:2px 0;cursor:pointer;transition:opacity .15s}
        .sub:hover{opacity:.7}
        .sub-box{width:12px;height:12px;border:1px solid #d0d0ce;border-radius:3px;flex-shrink:0;display:flex;align-items:center;justify-content:center;transition:all .15s}
        .sub-box.checked{background:#5DCAA5;border-color:#5DCAA5}
        .sub-box.checked::after{content:'';display:block;width:5px;height:3px;border-left:1.5px solid #fff;border-bottom:1.5px solid #fff;transform:rotate(-45deg) translateY(-.5px)}
        .sub-lbl{font-size:11px;color:#555;flex:1}.sub.done .sub-lbl{text-decoration:line-through;color:#ccc}
        .sub-del{opacity:0;color:#ddd;cursor:pointer;font-size:12px;padding:0 2px;transition:all .15s}
        .sub:hover .sub-del{opacity:1}.sub-del:hover{color:#A32D2D}
        .add-sub-r{display:flex;gap:3px;margin-top:4px}
        .add-sub-i{flex:1;border:1px solid #eee;border-radius:5px;padding:3px 7px;font-size:10px;outline:none;font-family:inherit}
        .add-sub-i:focus{border-color:#1D9E75}
        .add-sub-b{background:#1D9E75;color:#fff;border:none;border-radius:5px;padding:3px 8px;font-size:10px;cursor:pointer;font-weight:500}

        /* comments section */
        .comments-section{margin-top:6px;padding-top:6px;border-top:.5px solid #f0f0ee}
        .comment{display:flex;gap:6px;padding:4px 0;align-items:flex-start}
        .comment-avatar{width:18px;height:18px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-size:7px;font-weight:600;flex-shrink:0}
        .comment-body{flex:1;min-width:0}
        .comment-head{display:flex;gap:6px;align-items:baseline}
        .comment-author{font-size:10px;font-weight:600;color:#444}
        .comment-time{font-size:9px;color:#ccc}
        .comment-text{font-size:11px;color:#555;line-height:1.4;margin-top:1px}
        .comment-del{opacity:0;color:#ddd;cursor:pointer;font-size:10px;margin-left:auto;transition:opacity .15s}
        .comment:hover .comment-del{opacity:1}
        .comment-del:hover{color:#A32D2D}
        .add-comment-r{display:flex;gap:3px;margin-top:4px}
        .add-comment-i{flex:1;border:1px solid #eee;border-radius:5px;padding:3px 7px;font-size:10px;outline:none;font-family:inherit}
        .add-comment-i:focus{border-color:#1D9E75}
        .add-comment-sel{font-size:10px;padding:2px 6px;border-radius:5px;border:1px solid #eee;background:#fff;font-family:inherit;outline:none}
        .add-comment-b{background:#1D9E75;color:#fff;border:none;border-radius:5px;padding:3px 8px;font-size:10px;cursor:pointer;font-weight:500}

        /* attachments */
        .att-section{margin-top:4px}
        .att{display:inline-flex;align-items:center;gap:3px;font-size:9px;color:#888;background:#f5f5f3;border-radius:5px;padding:2px 6px;margin:1px;cursor:pointer;transition:background .15s}
        .att:hover{background:#eee}
        .att-del{color:#ddd;font-size:8px;cursor:pointer}.att-del:hover{color:#A32D2D}
        .att-upload{display:inline-flex;align-items:center;gap:2px;font-size:9px;color:#bbb;cursor:pointer;padding:2px 6px;border:1px dashed #ddd;border-radius:5px;transition:all .15s}
        .att-upload:hover{border-color:#1D9E75;color:#1D9E75}
        .att-upload input{display:none}

        /* ── kanban ── */
        .kanban{display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px}
        @media(max-width:600px){.kanban{grid-template-columns:1fr}}
        .kan-col{background:#f0f0ee;border-radius:10px;padding:8px;min-height:200px}
        .kan-hdr{font-size:11px;font-weight:600;color:#888;margin-bottom:6px;display:flex;align-items:center;gap:4px}
        .kan-cnt{font-size:10px;font-weight:400;color:#bbb;background:#e8e8e6;border-radius:10px;padding:0 5px}
        .kan-col .card{margin-bottom:4px}

        /* ── activity log ── */
        .log-section{margin-top:20px;border-top:1px solid #e8e8e6;padding-top:12px}
        .log-hdr{font-size:13px;font-weight:500;color:#888;margin-bottom:8px;cursor:pointer;display:flex;align-items:center;gap:5px}
        .log-hdr .arr{font-size:9px;transition:transform .2s}.log-hdr .arr.open{transform:rotate(90deg)}
        .log-list{max-height:300px;overflow-y:auto}
        .log-item{display:flex;gap:6px;padding:3px 0;font-size:10px;color:#999;align-items:baseline}
        .log-time{color:#ccc;flex-shrink:0;width:60px}
        .log-action{color:#888;font-weight:500;width:60px;flex-shrink:0}
        .log-details{color:#666;flex:1}

        /* done section */
        .done-section{margin-top:10px}
        .done-toggle{display:flex;align-items:center;gap:5px;padding:5px 0;cursor:pointer;color:#ccc;font-size:11px;transition:color .15s}
        .done-toggle:hover{color:#888}
        .done-toggle .arr{font-size:9px;transition:transform .2s}.done-toggle .arr.open{transform:rotate(90deg)}

        /* modal */
        .mo{position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.3);z-index:100;display:flex;align-items:center;justify-content:center;animation:fi .12s ease}
        @keyframes fi{from{opacity:0}to{opacity:1}}
        .mod{background:#fff;border-radius:14px;padding:18px;width:92%;max-width:420px;box-shadow:0 16px 50px rgba(0,0,0,.15);animation:mi .18s ease;max-height:90vh;overflow-y:auto}
        @keyframes mi{from{opacity:0;transform:translateY(14px) scale(.97)}to{opacity:1;transform:translateY(0) scale(1)}}
        .mod h3{font-size:14px;font-weight:500;margin-bottom:12px}
        .mf{margin-bottom:8px}
        .mf label{display:block;font-size:10px;color:#999;margin-bottom:2px;font-weight:500}
        .mf input,.mf select,.mf textarea{width:100%;border:1px solid #e0e0de;border-radius:7px;padding:6px 9px;font-size:11px;outline:none;font-family:inherit}
        .mf input:focus,.mf select:focus,.mf textarea:focus{border-color:#1D9E75}
        .mf textarea{resize:vertical;min-height:40px}
        .mb{display:flex;gap:5px;justify-content:flex-end;margin-top:12px}
        .mbtn{padding:6px 14px;border-radius:6px;border:none;font-size:11px;font-weight:500;cursor:pointer;font-family:inherit}
        .mbtn.cancel{background:#f0f0ee;color:#666}.mbtn.save{background:#1D9E75;color:#fff}.mbtn.del{background:#FCEBEB;color:#A32D2D}

        /* popover */
        .pop{position:absolute;background:#fff;border:1px solid #e0e0de;border-radius:7px;padding:3px;box-shadow:0 4px 14px rgba(0,0,0,.1);z-index:50;animation:fi .1s ease}
        .pop-item{display:block;width:100%;padding:3px 10px;font-size:10px;border:none;background:none;cursor:pointer;border-radius:3px;text-align:left;font-family:inherit;color:#444}
        .pop-item:hover{background:#f5f5f3}
        .pop-item.active{font-weight:600;color:#1D9E75}

        /* tag colors */
        .tc0{background:#E6F1FB;color:#185FA5}.tc1{background:#EAF3DE;color:#3B6D11}.tc2{background:#FFF3DC;color:#854F0B}
        .tc3{background:#FBEAF0;color:#993556}.tc4{background:#F0EDFA;color:#534AB7}.tc5{background:#E8F5F0;color:#1D7A5F}
        .tc6{background:#FDE8E0;color:#B5451B}.tc7{background:#F5F5F3;color:#666}

        /* inline edit */
        .ined{border:1px solid #1D9E75;border-radius:5px;padding:2px 6px;font-size:12px;font-weight:500;outline:none;width:100%;font-family:inherit}

        /* toast */
        .toast{position:fixed;bottom:18px;left:50%;transform:translateX(-50%);background:#333;color:#fff;padding:7px 16px;border-radius:7px;font-size:11px;z-index:200;animation:ti .2s ease,to .2s ease 1.8s forwards;pointer-events:none}
        @keyframes ti{from{opacity:0;transform:translateX(-50%) translateY(6px)}to{opacity:1;transform:translateX(-50%) translateY(0)}}
        @keyframes to{from{opacity:1}to{opacity:0}}

        #confetti{position:fixed;top:0;left:0;width:100%;height:100%;pointer-events:none;z-index:999}
        .empty{text-align:center;color:#ccc;font-size:11px;padding:1.5rem 0}

        /* settings gear */
        .gear{font-size:14px;color:#ddd;cursor:pointer;transition:color .15s}.gear:hover{color:#888}
    </style>
</head>
<body>
<canvas id="confetti"></canvas>
<div class="root">
    <div class="hdr">
        <h2>Sprint</h2>
        <span class="prog-lbl" id="pl"></span>
        <div class="view-toggle">
            <button class="view-btn active" data-view="list" onclick="setView('list')">List</button>
            <button class="view-btn" data-view="kanban" onclick="setView('kanban')">Board</button>
        </div>
        <span class="gear" onclick="openSettings()" title="Настройки Telegram">&#9881;</span>
    </div>
    <div class="prog-bar"><div class="prog-fill" id="pf" style="width:0%"></div></div>

    <div class="toolbar">
        <div class="search-box"><input type="text" id="search" placeholder="Поиск..." /></div>
        <div class="filters">
            <div class="fg" id="fo"></div><div class="fsep"></div>
            <div class="fg" id="fp"></div><div class="fsep"></div>
            <div class="fg" id="fs"></div>
        </div>
    </div>

    <div class="ia" id="ia">
        <input class="ia-title" id="ia-t" placeholder="Название задачи..."/>
        <div class="ia-row">
            <select class="ia-sel" id="ia-p"><option value="high">Срочно</option><option value="med" selected>Важно</option><option value="low">Потом</option></select>
            <select class="ia-sel" id="ia-o"><option value="Борис">Борис</option><option value="Марат">Марат</option></select>
            <input class="ia-inp" id="ia-a" placeholder="Приложение..."/>
            <input class="ia-inp" id="ia-d" type="date" title="Дедлайн"/>
            <input class="ia-inp" id="ia-tag" placeholder="Тег..." style="width:70px"/>
            <div class="ia-acts">
                <button class="ia-btn cancel" onclick="closeIA()">Esc</button>
                <button class="ia-btn save" onclick="submitIA()">Добавить</button>
            </div>
        </div>
    </div>
    <div class="add-trig" id="at" onclick="openIA()"><span>+</span> Новая задача <span class="kbd">N</span></div>

    <div id="main-view"></div>
    <div class="done-section" id="done-sec"></div>
    <div class="log-section" id="log-sec"></div>
</div>
<div id="mr"></div><div id="pr"></div>

<script>
const API='api.php';
let tasks=[],activity=[],expanded={},expandedComments={},showDone=false,showLog=false,searchQ='',
    fOwner='all',fPri='all',fStatus='all',curView='list',dragSrcId=null;
const PL={high:'Срочно',med:'Важно',low:'Потом'},PC={high:'bdg-h',med:'bdg-m',low:'bdg-l'},
      SL={todo:'К выполнению',progress:'В работе',done:'Готово'},SC={todo:'s-todo',progress:'s-progress',done:'s-done'},
      TAG_COLORS=['tc0','tc1','tc2','tc3','tc4','tc5','tc6','tc7'];
function tagColor(t){let h=0;for(let i=0;i<t.length;i++)h=t.charCodeAt(i)+((h<<5)-h);return TAG_COLORS[Math.abs(h)%TAG_COLORS.length]}

/* API */
async function rpc(m,b,a){const u=a?`${API}?action=${a}`:API;const o={method:m,headers:{'Content-Type':'application/json'}};if(b)o.body=JSON.stringify(b);return(await fetch(u,o)).json()}
async function load(){tasks=await rpc('GET');fullRender()}
async function save(t){await rpc('PUT',t)}
async function create(t){const c=await rpc('POST',t);tasks.push(c);fullRender();toast('Создано')}
async function del(id){await rpc('DELETE',{id});tasks=tasks.filter(t=>t.id!==id);fullRender();toast('Удалено')}
async function reorder(ids){await rpc('POST',{ids},'reorder')}
async function addComment(tid,text,author){await rpc('POST',{task_id:tid,text,author},'comment');const t=find(tid);if(!t.comments)t.comments=[];t.comments.push({author,text,time:new Date().toISOString()});fullRender()}
async function delComment(tid,idx){await rpc('POST',{task_id:tid,index:idx},'delete_comment');const t=find(tid);t.comments.splice(idx,1);fullRender()}
async function uploadFile(tid,file){const fd=new FormData();fd.append('task_id',tid);fd.append('file',file);const r=await fetch(`${API}?action=upload`,{method:'POST',body:fd});return r.json()}
async function delAttachment(tid,fname){await rpc('POST',{task_id:tid,file:fname},'delete_attachment');const t=find(tid);t.attachments=(t.attachments||[]).filter(a=>a.file!==fname);fullRender()}
async function loadActivity(){activity=await rpc('GET',null,'activity');renderLog()}

/* confetti */
const cv=document.getElementById('confetti'),cx=cv.getContext('2d');let ps=[],anim=false;
function rCV(){cv.width=innerWidth;cv.height=innerHeight}rCV();addEventListener('resize',rCV);
function boom(x,y){const cs=['#1D9E75','#5DCAA5','#185FA5','#85B7EB','#EF9F27','#FAC775'];for(let k=0;k<20;k++){const a=Math.random()*6.28,s=2+Math.random()*4;ps.push({x,y,vx:Math.cos(a)*s,vy:Math.sin(a)*s-3,sz:3+Math.random()*3,c:cs[~~(Math.random()*cs.length)],l:1,d:.03+Math.random()*.02,r:Math.random()*6.28,rv:(Math.random()-.5)*.2})}if(!anim)aF()}
function aF(){anim=true;cx.clearRect(0,0,cv.width,cv.height);ps=ps.filter(p=>p.l>0);ps.forEach(p=>{p.x+=p.vx;p.y+=p.vy;p.vy+=.18;p.l-=p.d;p.r+=p.rv;cx.save();cx.globalAlpha=Math.max(0,p.l);cx.translate(p.x,p.y);cx.rotate(p.r);cx.fillStyle=p.c;cx.fillRect(-p.sz/2,-p.sz/2,p.sz,p.sz*.6);cx.restore()});if(ps.length>0)requestAnimationFrame(aF);else{anim=false;cx.clearRect(0,0,cv.width,cv.height)}}

function toast(m){const e=document.createElement('div');e.className='toast';e.textContent=m;document.body.appendChild(e);setTimeout(()=>e.remove(),2200)}
function find(id){return tasks.find(t=>t.id===id)}
function owC(o){return o==='Борис'?'ow-b':o==='Марат'?'ow-m':'ow-o'}
function avC(o){return o==='Борис'?'avatar-boris':o==='Марат'?'avatar-marat':'avatar-other'}

function isVis(t){
    if(fOwner!=='all'&&t.owner!==fOwner)return false;
    if(fPri!=='all'&&t.priority!==fPri)return false;
    if(fStatus!=='all'&&(t.status||'todo')!==fStatus)return false;
    if(searchQ){const q=searchQ.toLowerCase();if(!(t.title||'').toLowerCase().includes(q)&&!(t.app||'').toLowerCase().includes(q)&&!(t.flag||'').toLowerCase().includes(q)&&!(t.tags||[]).some(tg=>tg.toLowerCase().includes(q)))return false}
    return true;
}
function dlInfo(d){if(!d)return null;const now=new Date();now.setHours(0,0,0,0);const dl=new Date(d+'T00:00:00'),diff=Math.round((dl-now)/864e5),fmt=dl.toLocaleDateString('ru-RU',{day:'numeric',month:'short'});if(diff<0)return{l:fmt+' !',c:'overdue'};if(diff<=3)return{l:fmt,c:'soon'};return{l:fmt,c:''}}
function updProg(){const vis=tasks.filter(isVis),dn=vis.filter(t=>(t.status||'todo')==='done').length;document.getElementById('pl').textContent=`${dn}/${vis.length}`;document.getElementById('pf').style.width=`${vis.length?Math.round(dn/vis.length*100):0}%`}
function timeAgo(ts){const d=new Date(ts),now=new Date(),m=Math.round((now-d)/60000);if(m<1)return'только что';if(m<60)return m+'м';const h=Math.round(m/60);if(h<24)return h+'ч';const dy=Math.round(h/24);return dy+'д'}

/* search */
document.getElementById('search').addEventListener('input',e=>{searchQ=e.target.value;fullRender()});

/* filters */
function buildF(){
    const od=[{v:'all',l:'Все',c:'c-all'},{v:'Борис',l:'Б',c:'c-boris'},{v:'Марат',l:'М',c:'c-marat'}];
    const pd=[{v:'all',l:'Все',c:'c-all'},{v:'high',l:'!!!',c:'c-high'},{v:'med',l:'!!',c:'c-med'},{v:'low',l:'!',c:'c-low'}];
    const sd=[{v:'all',l:'Все',c:'c-all'},{v:'todo',l:'Todo',c:'c-todo'},{v:'progress',l:'WIP',c:'c-progress'},{v:'done',l:'Done',c:'c-done'}];
    function bg(id,ds,get,set){const el=document.getElementById(id);el.innerHTML='';ds.forEach(d=>{const b=document.createElement('button');b.className='chip '+d.c+(get()===d.v?' active':'');b.textContent=d.l;b.onclick=()=>{set(d.v);buildF();fullRender()};el.appendChild(b)})}
    bg('fo',od,()=>fOwner,v=>fOwner=v);bg('fp',pd,()=>fPri,v=>fPri=v);bg('fs',sd,()=>fStatus,v=>fStatus=v);
}

/* view */
function setView(v){curView=v;document.querySelectorAll('.view-btn').forEach(b=>b.classList.toggle('active',b.dataset.view===v));fullRender()}

/* inline add */
function openIA(){document.getElementById('ia').classList.add('visible');document.getElementById('at').style.display='none';setTimeout(()=>document.getElementById('ia-t').focus(),50)}
function closeIA(){document.getElementById('ia').classList.remove('visible');document.getElementById('at').style.display='';document.getElementById('ia-t').value='';document.getElementById('ia-a').value='';document.getElementById('ia-d').value='';document.getElementById('ia-tag').value=''}
async function submitIA(){
    const title=document.getElementById('ia-t').value.trim();if(!title)return;
    const tags=document.getElementById('ia-tag').value.trim();
    await create({title,app:document.getElementById('ia-a').value.trim()||'',priority:document.getElementById('ia-p').value,owner:document.getElementById('ia-o').value,deadline:document.getElementById('ia-d').value||'',tags:tags?tags.split(',').map(s=>s.trim()).filter(Boolean):[],flag:'',subtasks:[],done:false,blocker:false,status:'todo',comments:[],attachments:[]});
    closeIA();
}
document.getElementById('ia-t').addEventListener('keydown',e=>{if(e.key==='Enter')submitIA();if(e.key==='Escape')closeIA()});

/* toggle */
async function togDone(id,el){const t=find(id);const newSt=(t.status||'todo')==='done'?'todo':'done';t.status=newSt;t.done=newSt==='done';if(t.done&&el){const r=el.getBoundingClientRect();boom(r.left+r.width/2,r.top+r.height/2)}await save({id:t.id,status:t.status,done:t.done});fullRender()}
async function togSub(tid,si,el){const t=find(tid);t.subtasks[si].done=!t.subtasks[si].done;if(t.subtasks[si].done&&el){const r=el.getBoundingClientRect();boom(r.left+r.width/2,r.top+r.height/2)}await save({id:t.id,subtasks:t.subtasks});fullRender()}

/* quick change */
function closePop(){document.getElementById('pr').innerHTML=''}
document.addEventListener('click',closePop);
function showPop(anchor,items,cur,onSel){closePop();const r=anchor.getBoundingClientRect(),root=document.getElementById('pr'),p=document.createElement('div');p.className='pop';p.style.top=(r.bottom+2+scrollY)+'px';p.style.left=r.left+'px';items.forEach(it=>{const b=document.createElement('button');b.className='pop-item'+(it.v===cur?' active':'');b.textContent=it.l;b.onclick=e=>{e.stopPropagation();onSel(it.v);closePop()};p.appendChild(b)});p.onclick=e=>e.stopPropagation();root.appendChild(p)}
async function qPri(id,el){const t=find(id);showPop(el,[{v:'high',l:'Срочно'},{v:'med',l:'Важно'},{v:'low',l:'Потом'}],t.priority,async v=>{t.priority=v;await save({id:t.id,priority:v});fullRender()})}
async function qOwn(id,el){const t=find(id);showPop(el,[{v:'Борис',l:'Борис'},{v:'Марат',l:'Марат'}],t.owner,async v=>{t.owner=v;await save({id:t.id,owner:v});fullRender()})}
async function qSt(id,el){const t=find(id);showPop(el,[{v:'todo',l:'К выполнению'},{v:'progress',l:'В работе'},{v:'done',l:'Готово'}],t.status||'todo',async v=>{t.status=v;t.done=v==='done';await save({id:t.id,status:v,done:t.done});fullRender()})}

/* inline edits */
function edTitle(id,span){const t=find(id),inp=document.createElement('input');inp.className='ined';inp.value=t.title;span.replaceWith(inp);inp.focus();inp.select();const done=async()=>{const v=inp.value.trim();if(v&&v!==t.title){t.title=v;await save({id:t.id,title:v})}fullRender()};inp.onblur=done;inp.onkeydown=e=>{if(e.key==='Enter')inp.blur();if(e.key==='Escape'){inp.value=t.title;inp.blur()}}}
function edSub(tid,si,span){const t=find(tid),inp=document.createElement('input');inp.className='ined';inp.style.fontSize='11px';inp.value=t.subtasks[si].label;span.replaceWith(inp);inp.focus();inp.select();const done=async()=>{const v=inp.value.trim();if(v&&v!==t.subtasks[si].label){t.subtasks[si].label=v;await save({id:t.id,subtasks:t.subtasks})}fullRender()};inp.onblur=done;inp.onkeydown=e=>{if(e.key==='Enter')inp.blur();if(e.key==='Escape'){inp.value=t.subtasks[si].label;inp.blur()}}}
async function addSub(tid,inp){const v=inp.value.trim();if(!v)return;const t=find(tid);t.subtasks.push({label:v,done:false});await save({id:t.id,subtasks:t.subtasks});inp.value='';fullRender()}
async function delSub(tid,si){const t=find(tid);t.subtasks.splice(si,1);await save({id:t.id,subtasks:t.subtasks});fullRender()}

/* tags */
async function addTag(tid){const t=find(tid);const tag=prompt('Тег:');if(!tag)return;if(!t.tags)t.tags=[];t.tags.push(tag.trim());await save({id:t.id,tags:t.tags});fullRender()}
async function delTag(tid,idx){const t=find(tid);t.tags.splice(idx,1);await save({id:t.id,tags:t.tags});fullRender()}

/* modal */
function closeMod(){document.getElementById('mr').innerHTML=''}
function openEdit(id){
    const t=find(id);
    document.getElementById('mr').innerHTML=`<div class="mo" onclick="if(event.target===this)closeMod()"><div class="mod">
        <h3>Редактировать</h3>
        <div class="mf"><label>Название</label><input id="mt" value="${(t.title||'').replace(/"/g,'&quot;')}"/></div>
        <div class="mf"><label>Приложение</label><input id="ma" value="${(t.app||'').replace(/"/g,'&quot;')}"/></div>
        <div class="mf"><label>Приоритет</label><select id="mp"><option value="high" ${t.priority==='high'?'selected':''}>Срочно</option><option value="med" ${t.priority==='med'?'selected':''}>Важно</option><option value="low" ${t.priority==='low'?'selected':''}>Потом</option></select></div>
        <div class="mf"><label>Статус</label><select id="ms"><option value="todo" ${(t.status||'todo')==='todo'?'selected':''}>К выполнению</option><option value="progress" ${t.status==='progress'?'selected':''}>В работе</option><option value="done" ${t.status==='done'?'selected':''}>Готово</option></select></div>
        <div class="mf"><label>Ответственный</label><select id="mo"><option value="Борис" ${t.owner==='Борис'?'selected':''}>Борис</option><option value="Марат" ${t.owner==='Марат'?'selected':''}>Марат</option></select></div>
        <div class="mf"><label>Дедлайн</label><input id="md" type="date" value="${t.deadline||''}"/></div>
        <div class="mf"><label>Теги (через запятую)</label><input id="mtg" value="${(t.tags||[]).join(', ')}"/></div>
        <div class="mf"><label>Блокер</label><select id="mb"><option value="0" ${!t.blocker?'selected':''}>Нет</option><option value="1" ${t.blocker?'selected':''}>Да</option></select></div>
        <div class="mf"><label>Описание</label><textarea id="mf">${t.flag||''}</textarea></div>
        <div class="mb"><button class="mbtn del" onclick="if(confirm('Удалить?')){del(${t.id});closeMod()}">Удалить</button><button class="mbtn cancel" onclick="closeMod()">Отмена</button><button class="mbtn save" onclick="submitEd(${t.id})">Сохранить</button></div>
    </div></div>`;
}
async function submitEd(id){
    const t=find(id);
    t.title=document.getElementById('mt').value.trim()||t.title;t.app=document.getElementById('ma').value.trim();
    t.priority=document.getElementById('mp').value;t.status=document.getElementById('ms').value;t.done=t.status==='done';
    t.owner=document.getElementById('mo').value;t.deadline=document.getElementById('md').value||'';
    t.tags=document.getElementById('mtg').value.split(',').map(s=>s.trim()).filter(Boolean);
    t.blocker=document.getElementById('mb').value==='1';t.flag=document.getElementById('mf').value.trim();
    await save(t);closeMod();fullRender();toast('Сохранено');
}

/* settings (telegram) */
function openSettings(){
    document.getElementById('mr').innerHTML=`<div class="mo" onclick="if(event.target===this)closeMod()"><div class="mod">
        <h3>Telegram уведомления</h3>
        <div class="mf"><label>Bot Token</label><input id="tg-tok" placeholder="123456:ABC-DEF..."/></div>
        <div class="mf"><label>Chat ID</label><input id="tg-cid" placeholder="-100123456789"/></div>
        <div class="mb"><button class="mbtn cancel" onclick="closeMod()">Отмена</button><button class="mbtn save" onclick="saveTG()">Сохранить</button><button class="mbtn save" style="background:#534AB7" onclick="testTG()">Тест</button></div>
    </div></div>`;
}
async function saveTG(){await rpc('POST',{bot_token:document.getElementById('tg-tok').value.trim(),chat_id:document.getElementById('tg-cid').value.trim()},'telegram_config');closeMod();toast('Telegram настроен')}
async function testTG(){await saveTG();await rpc('POST',{},'telegram_test');toast('Тестовое сообщение отправлено')}

/* drag */
function dStart(e,id){dragSrcId=id;e.dataTransfer.effectAllowed='move';e.target.closest('.card').classList.add('dragging')}
function dEnd(){dragSrcId=null;document.querySelectorAll('.card').forEach(c=>c.classList.remove('dragging','drag-over'))}
function dOver(e){e.preventDefault();e.target.closest('.card')?.classList.add('drag-over')}
function dLeave(e){e.target.closest('.card')?.classList.remove('drag-over')}
async function dDrop(e,tid){e.preventDefault();if(dragSrcId===null||dragSrcId===tid)return;const si=tasks.findIndex(t=>t.id===dragSrcId),ti=tasks.findIndex(t=>t.id===tid);const[m]=tasks.splice(si,1);tasks.splice(ti,0,m);await reorder(tasks.map(t=>t.id));fullRender()}

/* kanban drag between columns */
async function kanDrop(e, newStatus) {
    e.preventDefault();
    if (dragSrcId === null) return;
    const t = find(dragSrcId);
    if (t && (t.status||'todo') !== newStatus) {
        t.status = newStatus; t.done = newStatus === 'done';
        await save({id:t.id, status:newStatus, done:t.done});
        fullRender(); toast(SL[newStatus]);
    }
}

/* ── render card ── */
function renderCard(t,container){
    const sd=(t.subtasks||[]).filter(s=>s.done).length,st=(t.subtasks||[]).length;
    const dl=dlInfo(t.deadline),status=t.status||'todo';
    const c=document.createElement('div');
    c.className='card pri-'+t.priority+' st-'+status;
    c.draggable=true;
    c.addEventListener('dragstart',e=>dStart(e,t.id));c.addEventListener('dragend',dEnd);
    c.addEventListener('dragover',dOver);c.addEventListener('dragleave',dLeave);c.addEventListener('drop',e=>dDrop(e,t.id));

    const tagsHtml=(t.tags||[]).map((tg,i)=>`<span class="tag ${tagColor(tg)}" title="Клик чтобы удалить" data-dtag="${t.id}" data-dti="${i}">${tg}</span>`).join('')+`<span class="tag tc7" style="cursor:pointer" data-atag="${t.id}">+</span>`;
    const attsHtml=(t.attachments||[]).map(a=>`<span class="att"><a href="uploads/${a.file}" target="_blank" style="color:inherit;text-decoration:none">${a.name}</a> <span class="att-del" data-adel="${t.id}" data-af="${a.file}">&times;</span></span>`).join('');

    const commentsHtml=(t.comments||[]).map((cm,i)=>`<div class="comment">
        <div class="comment-avatar ${cm.author==='Борис'?'ow-b':'ow-m'}">${(cm.author||'?').slice(0,2).toUpperCase()}</div>
        <div class="comment-body"><div class="comment-head"><span class="comment-author">${cm.author}</span><span class="comment-time">${timeAgo(cm.time)}</span></div><div class="comment-text">${cm.text}</div></div>
        <span class="comment-del" data-cdel="${t.id}" data-ci="${i}">&times;</span>
    </div>`).join('');

    c.innerHTML=`
        <div class="card-acts"><button class="abtn" data-ed="${t.id}">&#9998;</button><button class="abtn dng" data-rm="${t.id}">&times;</button></div>
        <div class="card-top">
            <div class="drag-h">&#8942;&#8942;</div>
            <div class="cb ${status==='done'?'checked':''}" data-ck="${t.id}"><svg class="ci" viewBox="0 0 10 7"><path d="M1 3.5L3.5 6L9 1"/></svg></div>
            <div class="card-body">
                <span class="card-title" data-et="${t.id}">${t.title}</span>
                <div class="card-meta">
                    <div class="ow ${owC(t.owner)}" data-qo="${t.id}">${(t.owner||'?').slice(0,2).toUpperCase()}</div>
                    ${t.app?`<span class="bdg bdg-app">${t.app}</span>`:''}
                    <span class="bdg ${PC[t.priority]}" data-qp="${t.id}">${PL[t.priority]}</span>
                    <span class="bdg bdg-status ${SC[status]}" data-qs="${t.id}">${SL[status]}</span>
                    ${t.blocker?'<span class="bdg bdg-blk">Блокер</span>':''}
                    ${dl?`<span class="bdg bdg-date ${dl.c}">${dl.l}</span>`:''}
                    ${tagsHtml}
                </div>
                ${t.flag?`<div class="flag">${t.flag}</div>`:''}
                ${attsHtml?`<div class="att-section">${attsHtml}<label class="att-upload"><input type="file" data-up="${t.id}"/>+ файл</label></div>`:`<div class="att-section"><label class="att-upload"><input type="file" data-up="${t.id}"/>+ файл</label></div>`}
            </div>
            ${st>0?`<div class="mp" data-ex="${t.id}"><div class="mp-bar"><div class="mp-fill" style="width:${st?Math.round(sd/st*100):0}%"></div></div><span class="mp-lbl">${sd}/${st}</span></div>`:`<div class="mp" data-ex="${t.id}"><span class="mp-lbl" style="font-size:10px;color:#ddd">&#9662;</span></div>`}
        </div>
        <div class="subs-w ${expanded[t.id]?'open':''}"><div class="subs-in"><div class="subs">
            ${(t.subtasks||[]).map((s,j)=>`<div class="sub ${s.done?'done':''}" data-si="${t.id}" data-sj="${j}"><div class="sub-box ${s.done?'checked':''}"></div><span class="sub-lbl" data-sl="${t.id}" data-sli="${j}">${s.label}</span><span class="sub-del" data-sd="${t.id}" data-sdi="${j}">&times;</span></div>`).join('')}
            <div class="add-sub-r"><input class="add-sub-i" placeholder="+ подзадача..." data-asi="${t.id}"/><button class="add-sub-b" data-asb="${t.id}">+</button></div>
        </div>
        ${(t.comments||[]).length>0||true?`<div class="comments-section">
            ${commentsHtml}
            <div class="add-comment-r">
                <select class="add-comment-sel" data-csel="${t.id}"><option value="Борис">Борис</option><option value="Марат">Марат</option></select>
                <input class="add-comment-i" placeholder="Комментарий..." data-cinp="${t.id}"/>
                <button class="add-comment-b" data-cbtn="${t.id}">&#10148;</button>
            </div>
        </div>`:''}
        </div></div>`;
    container.appendChild(c);
}

/* bind */
function bind(ct){
    ct.querySelectorAll('[data-ck]').forEach(el=>el.onclick=e=>{e.stopPropagation();togDone(+el.dataset.ck,el)});
    ct.querySelectorAll('[data-ex]').forEach(el=>el.onclick=e=>{e.stopPropagation();expanded[+el.dataset.ex]=!expanded[+el.dataset.ex];fullRender()});
    ct.querySelectorAll('[data-si]').forEach(el=>el.onclick=e=>{if(e.target.closest('.sub-del')||e.target.closest('.sub-lbl'))return;e.stopPropagation();togSub(+el.dataset.si,+el.dataset.sj,el.querySelector('.sub-box'))});
    ct.querySelectorAll('[data-et]').forEach(el=>el.ondblclick=e=>{e.stopPropagation();edTitle(+el.dataset.et,el)});
    ct.querySelectorAll('[data-sl]').forEach(el=>el.ondblclick=e=>{e.stopPropagation();edSub(+el.dataset.sl,+el.dataset.sli,el)});
    ct.querySelectorAll('[data-sd]').forEach(el=>el.onclick=e=>{e.stopPropagation();delSub(+el.dataset.sd,+el.dataset.sdi)});
    ct.querySelectorAll('[data-ed]').forEach(el=>el.onclick=e=>{e.stopPropagation();openEdit(+el.dataset.ed)});
    ct.querySelectorAll('[data-rm]').forEach(el=>el.onclick=e=>{e.stopPropagation();if(confirm('Удалить?'))del(+el.dataset.rm)});
    ct.querySelectorAll('[data-qp]').forEach(el=>el.onclick=e=>{e.stopPropagation();qPri(+el.dataset.qp,el)});
    ct.querySelectorAll('[data-qo]').forEach(el=>el.onclick=e=>{e.stopPropagation();qOwn(+el.dataset.qo,el)});
    ct.querySelectorAll('[data-qs]').forEach(el=>el.onclick=e=>{e.stopPropagation();qSt(+el.dataset.qs,el)});
    ct.querySelectorAll('[data-atag]').forEach(el=>el.onclick=e=>{e.stopPropagation();addTag(+el.dataset.atag)});
    ct.querySelectorAll('[data-dtag]').forEach(el=>el.onclick=e=>{e.stopPropagation();delTag(+el.dataset.dtag,+el.dataset.dti)});
    ct.querySelectorAll('[data-asb]').forEach(el=>{const tid=+el.dataset.asb,inp=ct.querySelector(`[data-asi="${tid}"]`);el.onclick=e=>{e.stopPropagation();addSub(tid,inp)};inp.onkeydown=e=>{if(e.key==='Enter')addSub(tid,inp)}});
    ct.querySelectorAll('[data-cbtn]').forEach(el=>{const tid=+el.dataset.cbtn,inp=ct.querySelector(`[data-cinp="${tid}"]`),sel=ct.querySelector(`[data-csel="${tid}"]`);el.onclick=e=>{e.stopPropagation();const v=inp.value.trim();if(v){addComment(tid,v,sel.value);inp.value=''}};inp.onkeydown=e=>{if(e.key==='Enter'){const v=inp.value.trim();if(v){addComment(tid,v,sel.value);inp.value=''}}}});
    ct.querySelectorAll('[data-cdel]').forEach(el=>el.onclick=e=>{e.stopPropagation();delComment(+el.dataset.cdel,+el.dataset.ci)});
    ct.querySelectorAll('[data-adel]').forEach(el=>el.onclick=e=>{e.stopPropagation();delAttachment(+el.dataset.adel,el.dataset.af)});
    ct.querySelectorAll('[data-up]').forEach(el=>el.onchange=async e=>{if(el.files[0]){await uploadFile(+el.dataset.up,el.files[0]);const t=find(+el.dataset.up);tasks=await rpc('GET');fullRender();toast('Файл загружен')}});
}

/* full render */
function fullRender(){
    const mv=document.getElementById('main-view');
    if(curView==='kanban'){renderKanban(mv)}
    else{renderList(mv)}
    updProg();loadActivity();
}

function renderList(mv){
    mv.innerHTML='';
    const open=tasks.filter(t=>(t.status||'todo')!=='done'&&isVis(t));
    const done=tasks.filter(t=>(t.status||'todo')==='done'&&isVis(t));
    if(open.length===0&&done.length===0){mv.innerHTML='<div class="empty">Нет задач</div>';return}
    open.forEach(t=>renderCard(t,mv));bind(mv);
    const ds=document.getElementById('done-sec');ds.innerHTML='';
    if(done.length>0){
        ds.innerHTML=`<div class="done-toggle" id="dt"><span class="arr ${showDone?'open':''}">&#9654;</span> ${done.length} выполненных</div>`;
        if(showDone){const w=document.createElement('div');done.forEach(t=>renderCard(t,w));ds.appendChild(w);bind(w)}
        document.getElementById('dt').onclick=()=>{showDone=!showDone;fullRender()};
    }
}

function renderKanban(mv){
    mv.innerHTML='';
    const kanban=document.createElement('div');kanban.className='kanban';
    ['todo','progress','done'].forEach(st=>{
        const col=document.createElement('div');col.className='kan-col';
        col.addEventListener('dragover',e=>e.preventDefault());
        col.addEventListener('drop',e=>kanDrop(e,st));
        const items=tasks.filter(t=>(t.status||'todo')===st&&isVis(t));
        col.innerHTML=`<div class="kan-hdr">${SL[st]} <span class="kan-cnt">${items.length}</span></div>`;
        items.forEach(t=>renderCard(t,col));
        bind(col);kanban.appendChild(col);
    });
    mv.appendChild(kanban);
    document.getElementById('done-sec').innerHTML='';
}

/* activity log */
function renderLog(){
    const sec=document.getElementById('log-sec');
    sec.innerHTML=`<div class="log-hdr" id="lh"><span class="arr ${showLog?'open':''}">&#9654;</span> История</div>`;
    if(showLog&&activity.length>0){
        const list=document.createElement('div');list.className='log-list';
        activity.slice(0,50).forEach(a=>{
            const d=new Date(a.time);
            list.innerHTML+=`<div class="log-item"><span class="log-time">${d.toLocaleDateString('ru-RU',{day:'numeric',month:'short'})} ${d.toLocaleTimeString('ru-RU',{hour:'2-digit',minute:'2-digit'})}</span><span class="log-action">${a.action}</span><span class="log-details">${a.details}</span></div>`;
        });
        sec.appendChild(list);
    }
    document.getElementById('lh').onclick=()=>{showLog=!showLog;renderLog()};
}

/* hotkeys */
document.addEventListener('keydown',e=>{
    if(e.target.tagName==='INPUT'||e.target.tagName==='TEXTAREA'||e.target.tagName==='SELECT'){if(e.key==='Escape'){e.target.blur();closeIA();closeMod()}return}
    if(e.key==='n'||e.key==='N'){e.preventDefault();openIA()}
    if(e.key==='/'){e.preventDefault();document.getElementById('search').focus()}
    if(e.key==='Escape'){closeMod();closePop()}
});

buildF();load();
if('serviceWorker' in navigator)navigator.serviceWorker.register('sw.js');
</script>
</body>
</html>
