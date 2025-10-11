<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
// Note: we do NOT mark all unread as read on page load so users can see which are new and act on them individually.
try{
  $user_id = (int)($_SESSION['user_id'] ?? 0);
  // pagination
  $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
  $perPage = 25;
  $offset = ($page - 1) * $perPage;
  $st = $pdo->prepare('SELECT id, title, message, meta, is_read, created_at FROM user_notifications WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT ? OFFSET ?');
  $st->bindValue(1, $user_id, PDO::PARAM_INT);
  $st->bindValue(2, (int)$perPage, PDO::PARAM_INT);
  $st->bindValue(3, (int)$offset, PDO::PARAM_INT);
  $st->execute();
  $notes = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
  // total count for pagination
  $cntStmt = $pdo->prepare('SELECT COUNT(*) FROM user_notifications WHERE user_id = ? AND deleted_at IS NULL');
  $cntStmt->execute([$user_id]);
  $total = (int)$cntStmt->fetchColumn();
}catch(Throwable $e){ $notes = []; }
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>Notifications | PUP e-IPMO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <?php include __DIR__ . '/../../partials/navbar_afterlogin_fallback.php'; ?>
  <main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h1 class="h4 mb-0">Notifications</h1>
      <div class="d-flex gap-2">
        <button id="pageMarkAll" class="btn btn-sm btn-outline-secondary">Mark all as read</button>
        <button id="pageDeleteSelected" class="btn btn-sm btn-outline-danger" disabled>Delete selected</button>
        <button id="pageRefresh" class="btn btn-sm btn-outline-secondary">Refresh</button>
      </div>
    </div>
    <div class="card shadow-sm">
      <ul class="list-group list-group-flush">
        <?php if(empty($notes)): ?>
          <li class="list-group-item text-center text-muted py-4">You have no notifications.</li>
        <?php else: foreach($notes as $n):
          $meta = $n['meta'] ? json_decode($n['meta'], true) : null;
          $target = $meta['target_url'] ?? null;
          $isRead = (int)($n['is_read'] ?? 0);
        ?>
          <li class="list-group-item d-flex justify-content-between align-items-start notif-row <?php echo $isRead ? 'read' : 'unread'; ?>" data-target="<?php echo htmlspecialchars($target ?? ''); ?>" style="cursor:pointer;">
            <div class="form-check me-2">
              <input class="form-check-input select-notif" type="checkbox" value="" id="sel-<?php echo (int)$n['id']; ?>" data-id="<?php echo (int)$n['id']; ?>">
            </div>
            <div class="d-flex gap-3 align-items-start flex-grow-1 row-click-area">
              <div class="notif-icon mt-1">
                <?php if($isRead): ?>
                  <svg width="20" height="20" viewBox="0 0 16 16" fill="#6c757d" xmlns="http://www.w3.org/2000/svg"><path d="M13.485 1.929a1 1 0 0 1 0 1.414L6.414 10.414a1 1 0 0 1-1.414 0L2.515 7.93a1 1 0 1 1 1.414-1.414L6 8.586l6.071-6.071a1 1 0 0 1 1.414 0z"/></svg>
                <?php else: ?>
                  <svg width="20" height="20" viewBox="0 0 16 16" fill="#0d6efd" xmlns="http://www.w3.org/2000/svg"><path d="M8 1a7 7 0 1 0 0 14A7 7 0 0 0 8 1zM6.5 9.5l5-5L11.5 3l-5 5L6.5 9.5z"/></svg>
                <?php endif; ?>
              </div>
              <div class="flex-grow-1">
                <div class="d-flex w-100 justify-content-between">
                  <h6 class="mb-1 small fw-semibold"><?php echo htmlspecialchars($n['title'] ?: 'Notification'); ?></h6>
                  <small class="text-muted"><?php echo htmlspecialchars((string)$n['created_at']); ?></small>
                </div>
                <p class="mb-0 small text-muted"><?php echo nl2br(htmlspecialchars($n['message'] ?? '')); ?></p>
              </div>
            </div>
            <div class="ms-3 text-end">
              <button class="btn btn-sm btn-outline-primary mark-single" data-id="<?php echo (int)$n['id']; ?>"><?php echo $isRead ? 'Read' : 'Mark as read'; ?></button>
              <button class="btn btn-sm btn-outline-danger delete-single ms-2" data-id="<?php echo (int)$n['id']; ?>">Delete</button>
            </div>
          </li>
        <?php endforeach; endif; ?>
      </ul>
    </div>
    <nav class="mt-2" aria-label="Notifications pagination">
      <?php
        $totalPages = (int)ceil($total / $perPage);
        if($totalPages > 1):
      ?>
      <ul class="pagination pagination-sm">
        <li class="page-item <?php echo $page <= 1 ? 'disabled' : ''; ?>"><a class="page-link" href="?page=<?php echo max(1, $page-1); ?>">Previous</a></li>
        <li class="page-item disabled"><span class="page-link">Page <?php echo $page; ?> of <?php echo $totalPages; ?></span></li>
        <li class="page-item <?php echo $page >= $totalPages ? 'disabled' : ''; ?>"><a class="page-link" href="?page=<?php echo min($totalPages, $page+1); ?>">Next</a></li>
      </ul>
      <?php endif; ?>
    </nav>
  </main>
  <style>
    .notif-row.unread { background: #fff; }
    .notif-row.read { background: #f8f9fa; opacity: 0.85; }
    .notif-row:hover { background: #f1f3f5; }
    .notif-icon { width:28px; }
  </style>
  <script>
    (function(){
      const base = '<?php echo rtrim(asset_url(''), '/'); ?>';
  const deleteSelectedBtn = document.getElementById('pageDeleteSelected');

      document.getElementById('pageMarkAll').addEventListener('click', function(){
        fetch(base + '/admin/mark_all_notifications_read.php', { method:'POST', credentials:'same-origin' }).then(r=>r.json()).then(()=>{ location.reload(); });
      });
      document.getElementById('pageRefresh').addEventListener('click', function(){
        location.reload();
      });

      // checkbox handling
      function updateSelectedCount(){
        const checked = Array.from(document.querySelectorAll('.select-notif')).filter(i=>i.checked).map(i=>i.getAttribute('data-id'));
        deleteSelectedBtn.disabled = checked.length === 0;
        return checked;
      }
      document.querySelectorAll('.select-notif').forEach(cb=> cb.addEventListener('change', updateSelectedCount));

      deleteSelectedBtn.addEventListener('click', function(){
        const ids = updateSelectedCount();
        if(!ids.length) return;
        if(!confirm('Permanently delete selected notifications? This cannot be undone.')) return;
        fetch(base + '/User/Afterlogin/ajax_bulk_delete_notifications.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: 'ids='+encodeURIComponent(JSON.stringify(ids)) })
          .then(r=>r.json()).then(data=>{ if(data && data.success) location.reload(); else alert('Failed to delete selected'); });
      });

      // Trash/restore UI removed - page now only supports mark-all, multi-delete, and refresh
      // wire per-item mark buttons
      document.querySelectorAll('.mark-single').forEach(btn => {
        btn.addEventListener('click', function(e){
          e.stopPropagation();
          const id = this.getAttribute('data-id');
          const btnEl = this;
          const row = btnEl.closest('.notif-row');
          btnEl.disabled = true;
            fetch(base + '/admin/mark_notification_read.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: 'id='+encodeURIComponent(id) })
            .then(r=>r.json()).then((data)=>{
              console.log('mark_notification_read response:', data);
              if(data && data.success){
                if(row){ row.classList.remove('unread'); row.classList.add('read'); row.style.opacity='0.85'; }
                btnEl.textContent = 'Marked';
                // update header badge if present
                const hb = document.querySelector('#userNotifBadge'); if(hb){ hb.textContent = String(data.unread || 0); if(data.unread && data.unread>0) hb.classList.remove('d-none'); else hb.classList.add('d-none'); }
              } else {
                console.warn('mark failed', data);
                btnEl.disabled = false;
              }
            }).catch((err)=>{ console.error('mark request error', err); btnEl.disabled = false; });
        });
        });

      // delete buttons
      document.querySelectorAll('.delete-single').forEach(btn=>{
        btn.addEventListener('click', function(e){
          e.stopPropagation();
          if(!confirm('Delete this notification?')) return;
          const id = this.getAttribute('data-id');
          const btnEl = this;
          const row = btnEl.closest('.notif-row');
          btnEl.disabled = true;
            fetch(base + '/admin/delete_notification.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: 'id='+encodeURIComponent(id) })
            .then(r=>r.json()).then((data)=>{
              if(data && data.success){ if(row) row.remove(); const hb = document.querySelector('#userNotifBadge'); if(hb){ hb.textContent = String(data.unread || 0); if(data.unread && data.unread>0) hb.classList.remove('d-none'); else hb.classList.add('d-none'); } }
              else btnEl.disabled = false;
            }).catch((err)=>{ console.error('delete error', err); btnEl.disabled = false; });
        });
      });

        // wire row click handlers (navigate if target exists, otherwise mark as read)
        document.querySelectorAll('.notif-row').forEach(row => {
      row.addEventListener('click', function(e){
        // ignore clicks coming from action buttons or checkboxes
        if(e.target.closest('.mark-single')) return;
        if(e.target.closest('.delete-single')) return;
        if(e.target.closest('.select-notif') || e.target.classList.contains('form-check-input')) return;
            const target = row.getAttribute('data-target');
            if(target){ window.location.href = target; return; }
            // no target: mark as read via AJAX
            const mid = row.querySelector('.mark-single') ? row.querySelector('.mark-single').getAttribute('data-id') : null;
            if(!mid) return;
            const btnEl = row.querySelector('.mark-single');
            if(btnEl) btnEl.disabled = true;
            fetch(base + '/admin/mark_notification_read.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body: 'id='+encodeURIComponent(mid) })
              .then(r=>r.json()).then((data)=>{
                if(data && data.success){ row.classList.remove('unread'); row.classList.add('read'); row.style.opacity='0.85'; if(btnEl){ btnEl.textContent='Read'; btnEl.disabled = true; }
                  const hb = document.querySelector('#userNotifBadge'); if(hb){ hb.textContent = String(data.unread || 0); if(data.unread && data.unread>0) hb.classList.remove('d-none'); else hb.classList.add('d-none'); }
                } else { if(btnEl) btnEl.disabled = false; }
              }).catch((err)=>{ console.error('row mark error', err); if(btnEl) btnEl.disabled = false; });
          });
        });
    })();
  </script>
</body>
</html>
