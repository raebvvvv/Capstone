<?php
// Marks all unread notifications for the current user as read.
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

  $user_id = (int)$_SESSION['user_id'];

  $pdo->beginTransaction();
  $st = $pdo->prepare('UPDATE user_notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL');
  $st->execute([$user_id]);
  $pdo->commit();

  // return updated unread count
  $st2 = $pdo->prepare('SELECT COUNT(*) AS cnt FROM user_notifications WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL');
  $st2->execute([$user_id]);
  $row = $st2->fetch(PDO::FETCH_ASSOC);
  $count = $row ? (int)$row['cnt'] : 0;
  echo json_encode(['success' => true, 'unread' => $count]);
} catch (Throwable $e) {
  if ($pdo && $pdo->inTransaction()) { $pdo->rollBack(); }
  http_response_code(500);
  echo json_encode(['success' => false, 'message' => 'server_error']);
}
