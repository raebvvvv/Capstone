<?php
// Debug helper (local only): show session user and unread notifications for current session user.
// Place this file in debug/ and open in the same browser session you're testing.
require_once __DIR__ . '/../config.php';
require_once app_path('conn.php');
header('Content-Type: application/json; charset=utf-8');
if (session_status() !== PHP_SESSION_ACTIVE) session_start();
$out = [
  'ts' => date('c'),
  'session' => [
    'session_id' => session_id(),
    'user_id' => isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : null,
    'user_logged_in' => !empty($_SESSION['user_logged_in']) ? true : false,
    'username' => $_SESSION['username'] ?? ($_SESSION['name'] ?? null),
  ],
  'unread' => null,
  'recent' => [],
  'errors' => []
];
try {
  if (!isset($_SESSION['user_id'])) {
    $out['errors'][] = 'No user_id in session. Are you logged in?';
    echo json_encode($out, JSON_PRETTY_PRINT);
    exit;
  }
  $uid = (int)$_SESSION['user_id'];
  $st = $pdo->prepare('SELECT COUNT(*) AS cnt FROM user_notifications WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL');
  $st->execute([$uid]);
  $out['unread'] = (int)$st->fetchColumn();

  $nstmt = $pdo->prepare('SELECT id, title, message, meta, is_read, created_at FROM user_notifications WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT 10');
  $nstmt->execute([$uid]);
  $rows = $nstmt->fetchAll(PDO::FETCH_ASSOC);
  foreach ($rows as $r) {
    $r['meta'] = $r['meta'] ? json_decode($r['meta'], true) : null;
    $out['recent'][] = $r;
  }
} catch (Throwable $e) {
  $out['errors'][] = 'Exception: ' . $e->getMessage();
}
echo json_encode($out, JSON_PRETTY_PRINT);
