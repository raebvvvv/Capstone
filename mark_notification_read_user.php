<?php
require __DIR__ . '/config.php';
require app_path('conn.php');
header('Content-Type: application/json');
session_start();
$user_id = isset($_SESSION['user_id']) ? (int)$_SESSION['user_id'] : 0;
if ($user_id <= 0) { http_response_code(401); echo json_encode(['success'=>false,'error'=>'Not authenticated']); exit; }

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) { echo json_encode(['success'=>false,'error'=>'Invalid id']); exit; }

try {
    $stmt = $pdo->prepare('UPDATE user_notifications SET is_read = 1 WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, $user_id]);
    echo json_encode(['success'=>true,'updated'=>$stmt->rowCount()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Server error']);
}

?>
