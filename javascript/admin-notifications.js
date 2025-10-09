// admin-notifications.js
(function(){
  const POLL_INTERVAL = 15000; // 15s
  let timer = null;

  function qs(sel, ctx=document){ return ctx.querySelector(sel); }
  function ce(tag, cls){ const el=document.createElement(tag); if(cls) el.className=cls; return el; }

  function ensureUI(){
    let nav = document.querySelector('header nav .navbar-nav');
    if(!nav) return null;
    let existing = qs('#adminNotifWrapper');
    if(existing) return existing;
    const li = ce('li','nav-item position-relative me-3');
    li.id='adminNotifWrapper';
    li.innerHTML = `
      <button type="button" id="notifBell" class="btn btn-outline-secondary position-relative" title="Re-upload Notifications" style="display:inline-flex;align-items:center;gap:4px;">
        <span class="notif-bell-icon" aria-hidden="true" style="display:inline-flex;">
          <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg">
            <path d="M8 16a2 2 0 0 0 1.985-1.75H6.015A2 2 0 0 0 8 16m6-5c-1.098 0-1.918-.5-2.401-1.326-.49-.838-.599-1.87-.599-2.674 0-1.742-.454-2.83-1.276-3.472-.353-.276-.77-.463-1.224-.563V2.5a1.5 1.5 0 0 0-3 0v.465c-.454.1-.87.287-1.224.563-.822.643-1.276 1.73-1.276 3.472 0 .803-.108 1.836-.599 2.674C3.918 10.5 3.098 11 2 11v1h12z"/>
          </svg>
        </span>
        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger d-none" id="notifBadge">0</span>
      </button>
      <div id="notifDropdown" class="card shadow position-absolute end-0 mt-2" style="width:360px; display:none; z-index:1050;">
        <div class="card-header py-2 d-flex justify-content-between align-items-center gap-2">
          <span class="fw-semibold small mb-0">Recent Re-uploads</span>
          <div class="d-flex gap-1">
            <button class="btn btn-sm btn-outline-secondary" id="notifMarkAllBtn" title="Mark all as read">Mark all</button>
            <button class="btn btn-sm btn-outline-secondary" id="notifRefreshBtn" title="Refresh">↻</button>
          </div>
        </div>
        <ul class="list-group list-group-flush small" id="notifList" style="max-height:300px; overflow-y:auto;"></ul>
        <div class="card-footer text-center small py-1"><em>Showing latest 20</em></div>
      </div>`;
    // insert near beginning before profile/logout item
    const profileLi = nav.querySelector('.header-actions');
    nav.insertBefore(li, profileLi || nav.firstChild);
    return li;
  }

  function fetchNotifications(){
    fetch('get_notifications.php')
      .then(r=>r.json())
      .then(data=>{
        if(!data.success) return;
        updateUI(data);
      })
      .catch(()=>{});
  }

  function updateUI(data){
    const wrap = ensureUI();
    if(!wrap) return;
    const badge = qs('#notifBadge', wrap);
    const list = qs('#notifList', wrap);
    badge.textContent = data.unread;
    if(data.unread>0){ badge.classList.remove('d-none'); } else { badge.classList.add('d-none'); }
    list.innerHTML = '';
    if(!data.notifications.length){
      const li = ce('li','list-group-item text-center text-muted py-2');
      li.textContent='No notifications';
      list.appendChild(li);
      return;
    }
    data.notifications.forEach(n=>{
      const li = ce('li','list-group-item d-flex justify-content-between align-items-start gap-2 py-2');
      li.dataset.id = n.id;
      li.innerHTML = `<div class="flex-grow-1">
          <div class="small fw-semibold">${escapeHtml(n.doc_type.replaceAll('_',' '))}</div>
          <div class="small">${escapeHtml(n.message)}</div>
          <div class="text-muted x-small" style="font-size:11px;">${escapeHtml(n.created_at)}</div>
        </div>
        <button class="btn btn-sm ${n.is_read? 'btn-outline-secondary' : 'btn-outline-primary'} mark-read-btn">${n.is_read? 'Read' : 'Mark Read'}</button>`;
      list.appendChild(li);
    });
  }

  function escapeHtml(str){
    return str.replace(/[&<>"]+/g, s=>({ '&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;' }[s]));
  }

  function markRead(id, li){
    const fd = new FormData(); fd.append('id', id);
    fetch('mark_notification_read.php',{method:'POST', body:fd})
      .then(r=>r.json())
      .then(data=>{
        if(!data.success) return;
        li.querySelector('.mark-read-btn').textContent='Read';
        li.querySelector('.mark-read-btn').className='btn btn-sm btn-outline-secondary mark-read-btn';
        // Optimistically decrement badge
        const badge = qs('#notifBadge');
        if(badge && !badge.classList.contains('d-none')){
          let v = parseInt(badge.textContent||'0',10); if(v>0){ v--; badge.textContent=String(v); if(v===0) badge.classList.add('d-none'); }
        }
      });
  }

  function markAllRead(){
    fetch('mark_all_notifications_read.php', { method:'POST' })
      .then(r=>r.json())
      .then(data=>{
        if(!data.success) return;
        const wrap = ensureUI();
        if(!wrap) return;
        const list = qs('#notifList', wrap);
        list.querySelectorAll('.mark-read-btn').forEach(btn=>{
          btn.textContent='Read';
          btn.className='btn btn-sm btn-outline-secondary mark-read-btn';
        });
        const badge = qs('#notifBadge', wrap);
        if(badge){ badge.textContent='0'; badge.classList.add('d-none'); }
      })
      .catch(()=>{});
  }

  function bindListeners(){
    const wrap = ensureUI();
    if(!wrap) return;
    const bell = qs('#notifBell', wrap);
    const dropdown = qs('#notifDropdown', wrap);
  const refresh = qs('#notifRefreshBtn', wrap);
  const markAll = qs('#notifMarkAllBtn', wrap);
    bell.addEventListener('click', ()=>{
      dropdown.style.display = dropdown.style.display==='none' || !dropdown.style.display ? 'block':'none';
      if(dropdown.style.display==='block'){ fetchNotifications(); }
    });
  refresh.addEventListener('click', ()=> fetchNotifications());
  if (markAll) { markAll.addEventListener('click', (e)=>{ e.preventDefault(); markAllRead(); }); }
    document.addEventListener('click', (e)=>{
      if(!wrap.contains(e.target) && dropdown.style.display==='block'){
        dropdown.style.display='none';
      }
    });
    dropdown.addEventListener('click', (e)=>{
      const btn = e.target.closest('.mark-read-btn');
      if(!btn) return;
      const li = btn.closest('li');
      const id = li ? li.dataset.id : null;
      if(id) markRead(id, li);
    });
  }

  function start(){
    ensureUI();
    // Inline SVG used; no external font needed.
    bindListeners();
    fetchNotifications();
    timer = setInterval(fetchNotifications, POLL_INTERVAL);
  }

  if(document.readyState==='loading'){
    document.addEventListener('DOMContentLoaded', start);
  } else {
    start();
  }
})();
