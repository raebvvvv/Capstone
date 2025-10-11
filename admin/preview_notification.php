<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('require_admin')) { require_admin(); }

$user_id = (int)($_GET['user_id'] ?? 0);
$title = trim((string)($_POST['title'] ?? ''));
$message = trim((string)($_POST['message'] ?? ''));
$meta = trim((string)($_POST['meta'] ?? ''));
$sent = false; $error = null;
if($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id && $title && $message){
    try{
        require_once __DIR__ . '/../includes/notification_helpers.php';
        $metaArr = $meta ? json_decode($meta, true) : null;
        if(!is_array($metaArr)) $metaArr = null;
        $ok = create_site_notification($pdo, $user_id, $title, $message, $metaArr);
        if($ok) $sent = true;
    } catch(Throwable $e){ $error = $e->getMessage(); }
}
?>
<!doctype html>
<html><head><meta charset="utf-8"><title>Preview Notification</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"></head>
<body class="p-4">
<h3>Admin: Preview / Send Notification</h3>
<form method="post">
  <div class="mb-2"><label class="form-label">User ID</label><input name="user_id" class="form-control" value="<?php echo htmlspecialchars($user_id); ?>" readonly></div>
  <div class="mb-2"><label class="form-label">Title</label><input name="title" class="form-control" value="<?php echo htmlspecialchars($title); ?>"></div>
  <div class="mb-2"><label class="form-label">Message</label><textarea name="message" class="form-control" rows="4"><?php echo htmlspecialchars($message); ?></textarea></div>
  <div class="mb-2"><label class="form-label">Meta (JSON)</label><textarea name="meta" class="form-control" rows="2"><?php echo htmlspecialchars($meta); ?></textarea></div>
  <div class="mb-2"><button type="submit" class="btn btn-primary">Send</button></div>
</form>
<?php if($sent): ?><div class="alert alert-success">Notification created</div><?php endif; ?>
<?php if($error): ?><div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
</body></html>
