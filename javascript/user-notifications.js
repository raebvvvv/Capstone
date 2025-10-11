// user-notifications.js
(function(){
  const POLL = 45000; // poll unread count every 45s
  const UNREAD_ENDPOINT = (window.APP_BASE || '') + '/admin/get_unread_count.php';
  const FULL_ENDPOINT = (window.APP_BASE || '') + '/admin/get_user_notifications.php';
  let timer = null;

  function qs(sel, ctx=document){ return ctx.querySelector(sel); }
  function ce(tag, cls){ const e=document.createElement(tag); if(cls) e.className=cls; return e; }

  function start(){
    const bell = qs('#userNotifBell');
    const dropdown = qs('#userNotifDropdown');
    const list = qs('#userNotifList');
    const badge = qs('#userNotifBadge');
    const refresh = qs('#userNotifRefresh');
    if(!bell) return;
    bell.addEventListener('click', ()=>{ 
      const isOpen = dropdown.style.display === 'block';
      dropdown.style.display = isOpen ? 'none' : 'block';
      bell.setAttribute('aria-expanded', (!isOpen).toString());
      if(!isOpen){
        // just load the full list on open; do not auto-mark all as read — user should choose that explicitly
        fetchFullAndRender();
      }
    });
    refresh && refresh.addEventListener('click', (e)=>{ e.preventDefault(); fetchFullAndRender(); });
    const markAllBtn = qs('#userNotifMarkAll');
    if(markAllBtn){
      markAllBtn.addEventListener('click', (e)=>{ e.preventDefault();
        fetch((window.APP_BASE || '') + '/admin/mark_all_notifications_read.php', { method: 'POST', credentials: 'same-origin' })
          .then(r=>r.json()).then(data=>{ if(data && data.success){ const b=qs('#userNotifBadge'); if(b){ b.classList.add('d-none'); b.textContent='0'; } fetchFullAndRender(); } });
      });
    }
    // Clear-read removed per UI simplification; keep Mark all and Refresh only
    document.addEventListener('click', (e)=>{ const wrap = qs('#userNotifWidget'); if(wrap && !wrap.contains(e.target)) { if(dropdown) dropdown.style.display='none'; } });
    // poll only the unread count to keep payload minimal
    fetchUnread();
    timer = setInterval(fetchUnread, POLL);
  }

  // Simplified: use only the numeric inline badge. No fixed fallback and no dot.
  function updateNumericBadge(unread){
    const inlineBadge = qs('#userNotifBadge');
    const bell = qs('#userNotifBell');
    if(typeof unread === 'undefined' || unread === null) unread = 0;
    if(inlineBadge){
      inlineBadge.textContent = String(unread || 0);
      if(unread && unread > 0) inlineBadge.classList.remove('d-none'); else inlineBadge.classList.add('d-none');
    }
    if(bell){ if(unread && unread>0) bell.classList.add('has-unread'); else bell.classList.remove('has-unread'); }
  }

  function fetchUnread(){
    fetch(UNREAD_ENDPOINT, { credentials: 'same-origin' }).then(r=>r.json()).then(data=>{
      if(!data || !data.success) return;
      const badge = qs('#userNotifBadge');
      const dot = qs('#userNotifDot');
      const bell = qs('#userNotifBell');
      if(badge){ badge.textContent = String(data.unread || 0); }
      updateNumericBadge(Number(data.unread || 0));
    }).catch(err=>{ console.error('notif unread fetch err', err); });
  }

  function fetchFullAndRender(){
    fetch(FULL_ENDPOINT, { credentials: 'same-origin' }).then(r=>r.json()).then(data=>{
      if(!data || !data.success) return;
      renderList(data.notifications || []);
      const badge = qs('#userNotifBadge');
      if(badge){ badge.textContent = String(data.unread || 0); }
      updateNumericBadge(Number(data.unread || 0));
    }).catch(err=>{ console.error('notif full fetch err', err); });
  }

  function renderList(items){
    const list = qs('#userNotifList'); if(!list) return; list.innerHTML='';
    if(!items.length){ const li = ce('li','list-group-item text-center text-muted py-2'); li.textContent='No notifications'; list.appendChild(li); return; }
    items.forEach(it=>{
      const li = ce('li','list-group-item py-2 d-flex justify-content-between align-items-start');
      // ensure dropdown rows use same classes as view-all for consistent styling
      li.classList.add('notif-row');
      li.classList.add((it.is_read && Number(it.is_read) === 1) ? 'read' : 'unread');
      li.dataset.id = it.id || '';
      li.style.cursor = 'pointer';
      const left = ce('div','flex-grow-1');
      const t = ce('div','fw-semibold small mb-1'); t.textContent = it.title || 'Notification';
      const m = ce('div','small text-muted mb-1'); m.textContent = it.message || '';
      const time = ce('div','text-muted x-small'); time.style.fontSize='11px'; time.textContent = it.created_at || '';
      left.appendChild(t); left.appendChild(m); left.appendChild(time);

  const actions = ce('div','ms-2 text-end');
  // use a clear 'Read' action and a primary outline so users notice new items
  const markBtn = ce('button','btn btn-sm btn-outline-primary mark-single');
  markBtn.textContent = (it.is_read && Number(it.is_read) === 1) ? 'Read' : 'Mark as read';
  markBtn.setAttribute('data-id', it.id || '');
  markBtn.addEventListener('click', (e)=>{ e.stopPropagation(); e.preventDefault(); markSingle(it.id, li, markBtn); });
  actions.appendChild(markBtn);
  // delete history button (small)
  const delBtn = ce('button','btn btn-sm btn-outline-danger ms-2 delete-single');
  delBtn.textContent = 'Delete';
  delBtn.setAttribute('data-id', it.id || '');
  delBtn.addEventListener('click', (e)=>{ e.stopPropagation(); e.preventDefault(); deleteNotification(it.id, li); });
  actions.appendChild(delBtn);

      // clickable row: if meta.target_url present, open it on click
      li.addEventListener('click', ()=>{
        let target = (it.meta && it.meta.target_url) ? it.meta.target_url : null;
        if(target){ window.location.href = target; return; }
        // otherwise mark as read
        markSingle(it.id, li, markBtn);
      });

      li.appendChild(left); li.appendChild(actions);
      list.appendChild(li);
    });
  }

      function deleteNotification(id, liElement){
        if(!id) return;
        fetch((window.APP_BASE || '') + '/admin/delete_notification.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: 'id='+encodeURIComponent(id) })
          .then(r=>r.json()).then(data=>{
            if(!data || !data.success) return;
            // remove DOM row and update badge/dot
            if(liElement && liElement.parentNode){ liElement.parentNode.removeChild(liElement); }
              const b = qs('#userNotifBadge'); if(b){ b.textContent = String(data.unread || 0); }
                updateNumericBadge(Number(data.unread || 0));
          }).catch(err=>{ console.error('deleteNotification error', err); });
      }

  function markSingle(id, liElement, btn){
    if(!id) return;
    if(btn) btn.disabled = true;
    fetch((window.APP_BASE || '') + '/admin/mark_notification_read.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: 'id='+encodeURIComponent(id) })
      .then(r=>r.json()).then(data=>{
        console.log('markSingle response', data);
          if(data && data.success){
          const b = qs('#userNotifBadge');
          if(b){ b.textContent = String(data.unread || 0); }
          updateNumericBadge(Number(data.unread || 0));
          // visually mark in-place
          if(liElement){
            liElement.classList.remove('unread');
            liElement.classList.add('read');
            liElement.style.opacity = '0.85';
            const localBtn = liElement.querySelector('.mark-single');
            if(localBtn){ localBtn.textContent = 'Read'; localBtn.disabled = true; }
          } else {
            // fallback: re-fetch full list
            fetchFullAndRender();
          }
        }
      }).catch((err)=>{ console.error('markSingle error', err); }).finally(()=>{ if(btn) btn.disabled = false; });
  }

  if(document.readyState==='loading') document.addEventListener('DOMContentLoaded', start); else start();
})();
