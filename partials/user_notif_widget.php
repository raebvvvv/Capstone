<?php
// Include this partial in your header to show the user's notification bell.
// Example: include __DIR__ . '/partials/user_notif_widget.php';
?>
<div id="userNotifWidget" class="nav-item dropdown ms-3" style="position:relative;">
  <button class="btn btn-outline-secondary position-relative" id="userNotifBtn" type="button" aria-expanded="false">
    <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M8 16a2 2 0 0 0 1.985-1.75H6.015A2 2 0 0 0 8 16m6-5c-1.098 0-1.918-.5-2.401-1.326-.49-.838-.599-1.87-.599-2.674 0-1.742-.454-2.83-1.276-3.472-.353-.276-.77-.463-1.224-.563V2.5a1.5 1.5 0 0 0-3 0v.465c-.454.1-.87.287-1.224.563-.822.643-1.276 1.73-1.276 3.472 0 .803-.108 1.836-.599 2.674C3.918 10.5 3.098 11 2 11v1h12z"/></svg>
    <span class="badge bg-danger user-notif-badge d-none" style="position:absolute; top:-6px; right:-6px;">0</span>
  </button>
  <div class="card shadow position-absolute" style="width:360px; right:0; display:none; z-index:1050;" id="userNotifDropdown">
    <div class="card-header small fw-semibold py-2">Notifications</div>
    <div class="card-body p-0" style="max-height:320px; overflow-y:auto;">
      <div class="user-notif-list">
        <div class="p-2 text-center text-muted">Loading…</div>
      </div>
    </div>
    <div class="card-footer text-center small py-1"><a href="/Capstone/user/notifications.php">View all</a></div>
  </div>
</div>
<script src="/Capstone/javascript/user-notifications.js"></script>
<script>
  document.addEventListener('click', function(e){
    const btn = document.getElementById('userNotifBtn');
    const drop = document.getElementById('userNotifDropdown');
    if(!btn || !drop) return;
    if(e.target.closest && e.target.closest('#userNotifWidget')){
      drop.style.display = drop.style.display === 'block' ? 'none' : 'block';
    } else {
      if(drop.style.display === 'block') drop.style.display = 'none';
    }
  });
</script>
