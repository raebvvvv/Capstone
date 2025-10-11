<?php
// Include this partial in your site header where you want the notifications bell to appear.
// Example: include __DIR__ . '/partials/user_notifications.php';
// Render an initial unread count on the server so the badge is visible before JS runs.
$unread_count = 0;
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
if (!empty($_SESSION['user_id'])){
  try{
    // Attempt to use existing PDO connection if available
    if (!isset($pdo)) {
      require_once __DIR__ . '/../config.php';
      require_once app_path('conn.php');
    }
    $st = $pdo->prepare('SELECT COUNT(*) FROM user_notifications WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL');
    $st->execute([ (int) $_SESSION['user_id'] ]);
    $unread_count = (int)$st->fetchColumn();
  } catch(Throwable $e){ $unread_count = 0; }
}
?>
<div id="userNotifWidget" class="d-inline-block position-relative">
  <button id="userNotifBell" class="btn btn-sm btn-outline-secondary position-relative notif-bell-btn <?php echo $unread_count>0 ? 'has-unread' : ''; ?>" aria-label="Notifications" title="Notifications" aria-expanded="false" aria-controls="userNotifDropdown" style="display:inline-flex;align-items:center;justify-content:center;padding:8px;border-radius:8px;">
    <span class="bell-icon" aria-hidden="true">
      <svg width="18" height="18" viewBox="0 0 16 16" fill="currentColor" xmlns="http://www.w3.org/2000/svg"><path d="M8 16a2 2 0 0 0 1.985-1.75H6.015A2 2 0 0 0 8 16m6-5c-1.098 0-1.918-.5-2.401-1.326-.49-.838-.599-1.87-.599-2.674 0-1.742-.454-2.83-1.276-3.472-.353-.276-.77-.463-1.224-.563V2.5a1.5 1.5 0 0 0-3 0v.465c-.454.1-.87.287-1.224.563-.822.643-1.276 1.73-1.276 3.472 0 .803-.108 1.836-.599 2.674C3.918 10.5 3.098 11 2 11v1h12z"/></svg>
    </span>
  <span id="userNotifBadge" class="position-absolute badge-notif <?php echo $unread_count>0 ? '' : 'd-none'; ?>"><?php echo $unread_count>0 ? $unread_count : '0'; ?></span>
  </button>
  <div id="userNotifDropdown" class="card shadow position-absolute end-0 mt-2" style="width:360px; display:none; z-index:1050;" role="region" aria-live="polite">
    <div class="card-header py-2 d-flex justify-content-between align-items-center gap-2">
      <span class="fw-semibold small mb-0">Notifications</span>
      <div class="d-flex gap-2 align-items-center">
        <button class="btn btn-sm btn-link" id="userNotifMarkAll">Mark all as read</button>
        <button class="btn btn-sm btn-link" id="userNotifRefresh">Refresh</button>
      </div>
    </div>
  <ul id="userNotifList" class="list-group list-group-flush small" style="max-height:300px; overflow-y:auto;" role="list"></ul>
    <div class="card-footer text-center small py-1"><a href="<?php echo asset_url('User/Afterlogin/notifications.php'); ?>">View all</a></div>
  </div>
  </div>
  <?php // expose app base for the client so JS doesn't need hard-coded paths ?>
  <script>window.APP_BASE = '<?php echo rtrim(asset_url(''), '/'); ?>';</script>
  <style>
    /* Slightly smaller font inside the dropdown for compactness */
    #userNotifDropdown .list-group-item { font-size: 0.95rem; }
    #userNotifDropdown .fw-semibold { font-size: 0.98rem; }
  /* Ensure unread/read visual styles apply in dropdown */
    #userNotifDropdown .notif-row.unread { background: #fff; }
    #userNotifDropdown .notif-row.read { background: #f8f9fa; opacity: 0.85; }
    #userNotifDropdown .notif-row:hover { background: #f1f3f5; }
  #userNotifDropdown .delete-single { min-width:64px; }
    /* Ensure badge is visible and not clipped in narrow header containers */
    #userNotifWidget { overflow: visible !important; }
    #userNotifWidget #userNotifBell { overflow: visible !important; }

  /* Bell button appearance: small rounded square with centered bell icon */
  .notif-bell-btn { background: #f8f9fa; border-radius:8px; min-width:40px; min-height:36px; padding:6px; }
  .notif-bell-btn .bell-icon svg { display:block; }

  /* Compact circular numeric badge that matches the screenshot */
  /* Slightly smaller, less intrusive badge */
  .badge-notif { position:absolute; top:-4px; right:-4px; z-index:3000; display:inline-flex; align-items:center; justify-content:center; min-width:18px; height:18px; padding:0 5px; font-size:11px; line-height:1; color:#fff; background:#dc3545; border-radius:999px; border:2px solid #fff; box-shadow:0 2px 6px rgba(0,0,0,0.08); }

    /* Fallback pseudo-element removed to keep only the numeric badge indicator */
  </style>
  <script src="<?php echo asset_url('javascript/user-notifications.js'); ?>"></script>
  <script>
    // Quick client-side fallback: if the server rendered unread > 0 but the badge is hidden
    // due to CSS/JS load ordering, un-hide it. Also log a helpful message for debugging.
    (function(){
      try{
        var badge = document.getElementById('userNotifBadge');
        if(badge){
          var v = badge.textContent && badge.textContent.trim() ? parseInt(badge.textContent.trim(),10) : 0;
          if(v && v > 0){
            badge.classList.remove('d-none');
            console.debug('user-notif: server-side unread=', v, 'badge forced visible');
          } else {
            console.debug('user-notif: server-side unread=0');
          }
        } else {
          console.debug('user-notif: badge element not found');
        }
      }catch(e){ console.warn('user-notif fallback error', e); }
    })();
  </script>

  <!-- Opt-in debug: append ?notif_debug=1 to the page URL to run these checks in the browser console -->
  <script>
    (function(){
      try{
        if(!location.search || location.search.indexOf('notif_debug=1') === -1) return;
        console.group('user-notif-debug');
        console.info('Running opt-in notification debug checks...');
        var base = window.APP_BASE || '';
        function jlog(name, obj){ try{ console.log(name, obj); }catch(e){console.log(name);} }

        // 1) fetch unread count endpoint
        fetch((base || '') + '/admin/get_unread_count.php', { credentials: 'same-origin' })
          .then(function(r){ return r.text().then(function(t){ jlog('GET /admin/get_unread_count.php -> status:'+r.status, t); try{ jlog('parsed', JSON.parse(t)); }catch(e){} }); })
          .catch(function(err){ console.error('GET unread error', err); });

        // 2) fetch full notifications endpoint
        fetch((base || '') + '/admin/get_user_notifications.php?limit=10', { credentials: 'same-origin' })
          .then(function(r){ return r.text().then(function(t){ jlog('GET /admin/get_user_notifications.php -> status:'+r.status, t); try{ jlog('parsed', JSON.parse(t)); }catch(e){} }); })
          .catch(function(err){ console.error('GET full-notifs error', err); });

        // 3) hit the local debug helper if present
        fetch((base || '') + '/debug/debug_user_notif.php', { credentials: 'same-origin' })
          .then(function(r){ return r.text().then(function(t){ jlog('GET /debug/debug_user_notif.php -> status:'+r.status, t); try{ jlog('parsed', JSON.parse(t)); }catch(e){} }); })
          .catch(function(err){ console.error('GET debug helper error', err); });

        // 4) inspect DOM presence of the badge
        setTimeout(function(){
          var badge = document.getElementById('userNotifBadge');
          if(!badge){ console.warn('user-notif-debug: badge element (#userNotifBadge) not found in DOM'); }
          else { console.log('user-notif-debug: badge element found, classes:', badge.className, 'text:', badge.textContent.trim()); }
          console.groupEnd();
        }, 700);
      }catch(e){ console.error('user-notif-debug exception', e); }
    })();
  </script>
