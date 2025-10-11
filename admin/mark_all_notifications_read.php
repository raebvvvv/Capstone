<?php
// Marks all unread notifications as read.
// - If the caller is an admin (session is_admin=true), mark admin_notifications (global) as read.
// - Otherwise, mark the current user's user_notifications as read (back-compat for user UI/demo).
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth_check.php';
header('Content-Type: application/json; charset=utf-8');
try {
  // Accept POST or GET (some browsers may preflight) but prefer POST
  if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'unauthenticated']);
    exit;
  }

  $is_admin = !empty($_SESSION['is_admin']);
  $user_id = (int)$_SESSION['user_id'];

  if ($is_admin) {
    // Admins: mark all admin notifications as read (table is global by design)
    $pdo->beginTransaction();
    $pdo->exec('UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0');
    $pdo->commit();
    $unread = (int)($pdo->query('SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0')->fetchColumn());
    echo json_encode(['success' => true, 'unread' => $unread, 'scope' => 'admin']);
    return;
  }

  // Non-admins: fall back to marking the current user's notifications
  $pdo->beginTransaction();
  $st = $pdo->prepare('UPDATE user_notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL');
  $st->execute([$user_id]);
  $pdo->commit();

  // return updated unread count
  $st2 = $pdo->prepare('SELECT COUNT(*) AS cnt FROM user_notifications WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL');
  $st2->execute([$user_id]);
  $row = $st2->fetch(PDO::FETCH_ASSOC);
  $count = $row ? (int)$row['cnt'] : 0;
  echo json_encode(['success' => true, 'unread' => $count, 'scope' => 'user']);
} catch (Throwable $e) {
  if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) { $pdo->rollBack(); }
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'server_error']);
}
