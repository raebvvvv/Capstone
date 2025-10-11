<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
header('Content-Type: application/json');

// Require a logged-in user
if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Not authenticated']);
    exit;
}

$uid = (int)$_SESSION['user_id'];
try {
    // unread count
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM user_notifications WHERE user_id = ? AND is_read = 0 AND deleted_at IS NULL');
    $stmt->execute([$uid]);
    $unread = (int)$stmt->fetchColumn();

    // recent notifications (limit configurable)
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    if ($limit <= 0 || $limit > 200) $limit = 20;
    $nstmt = $pdo->prepare('SELECT id, title, message, meta, is_read, created_at FROM user_notifications WHERE user_id = ? AND deleted_at IS NULL ORDER BY created_at DESC LIMIT ?');
    $nstmt->bindValue(1, $uid, PDO::PARAM_INT);
    $nstmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
    $nstmt->execute();
    $rows = $nstmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$r) { $r['meta'] = $r['meta'] ? json_decode($r['meta'], true) : null; }

    echo json_encode(['success'=>true,'unread'=>$unread,'notifications'=>$rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Server error']);
}

?>
