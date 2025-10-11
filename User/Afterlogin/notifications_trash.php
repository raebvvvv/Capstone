<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
try{
  $user_id = (int)($_SESSION['user_id'] ?? 0);
  $st = $pdo->prepare('SELECT id, title, message, meta, is_read, deleted_at, created_at FROM user_notifications WHERE user_id = ? AND deleted_at IS NOT NULL ORDER BY deleted_at DESC LIMIT 200');
  $st->execute([$user_id]);
  $notes = $st->fetchAll(PDO::FETCH_ASSOC) ?: [];
}catch(Throwable $e){ $notes = []; }
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Notifications Trash</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
<?php include __DIR__ . '/../../partials/navbar_afterlogin_fallback.php'; ?>
<main class="container py-4">
  <h3>Notifications Trash</h3>
  <div class="card"><ul class="list-group list-group-flush">
    <?php if(empty($notes)): ?><li class="list-group-item text-center text-muted py-4">Trash is empty.</li><?php else: foreach($notes as $n):
      $isRead = (int)($n['is_read'] ?? 0);
    ?>
      <li class="list-group-item d-flex justify-content-between align-items-start">
        <div>
          <div class="fw-semibold small mb-1"><?php echo htmlspecialchars($n['title']); ?></div>
          <div class="small text-muted"><?php echo nl2br(htmlspecialchars($n['message'])); ?></div>
          <div class="text-muted small">Deleted at: <?php echo htmlspecialchars($n['deleted_at']); ?></div>
        </div>
        <div class="text-end">
          <button class="btn btn-sm btn-outline-secondary restore-single" data-id="<?php echo (int)$n['id']; ?>">Restore</button>
          <button class="btn btn-sm btn-outline-danger perm-delete-single ms-2" data-id="<?php echo (int)$n['id']; ?>">Delete permanently</button>
        </div>
      </li>
    <?php endforeach; endif; ?></ul></div>
  </main>
  <script>
    (function(){ const base = '<?php echo rtrim(asset_url(''), '/'); ?>';
      document.querySelectorAll('.restore-single').forEach(btn=>{
        btn.addEventListener('click', function(){ const id = this.getAttribute('data-id'); fetch(base + '/admin/restore_notification.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+encodeURIComponent(id) }).then(r=>r.json()).then(d=>{ if(d && d.success) location.reload(); }); });
      });
      document.querySelectorAll('.perm-delete-single').forEach(btn=>{
    btn.addEventListener('click', function(){ const id = this.getAttribute('data-id'); showConfirmModal('Permanently delete? This cannot be undone.', { static: true, keyboard: false, title: 'Confirm Permanent Delete', okText: 'Delete permanently', cancelText: 'Cancel' }).then(yes=>{ if(!yes) return; fetch(base + '/admin/delete_notification.php', { method:'POST', credentials:'same-origin', headers:{'Content-Type':'application/x-www-form-urlencoded'}, body:'id='+encodeURIComponent(id) }).then(r=>r.json()).then(d=>{ if(d && d.success) location.reload(); }); }); });
      });
    })();
  </script>
</body></html>
