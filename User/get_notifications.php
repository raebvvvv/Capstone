<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
header('Content-Type: application/json');

// Require login - use existing helper if available
if (function_exists('require_login')) { require_login(); }
session_start();
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($user_id <= 0) {
    http_response_code(401);
    echo json_encode(['success'=>false,'error'=>'Not authenticated']);
    exit;
}

try {
    // pagination
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
    if ($limit <= 0 || $limit > 200) $limit = 20;
    if ($offset < 0) $offset = 0;

    // unread count
    $unreadStmt = $pdo->prepare('SELECT COUNT(*) FROM user_notifications WHERE user_id = ? AND is_read = 0');
    $unreadStmt->execute([$user_id]);
    $unread = (int)$unreadStmt->fetchColumn();

    $stmt = $pdo->prepare('SELECT id, title, message, meta, is_read, created_at FROM user_notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT ? OFFSET ?');
    $stmt->bindValue(1, $user_id, PDO::PARAM_INT);
    $stmt->bindValue(2, $limit, PDO::PARAM_INT);
    $stmt->bindValue(3, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
    foreach ($rows as &$r) {
        $r['meta'] = $r['meta'] ? json_decode($r['meta'], true) : [];
    }

    echo json_encode(['success'=>true,'unread'=>$unread,'notifications'=>$rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Server error']);
}

?>
