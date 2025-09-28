<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
require_admin();
header('Content-Type: application/json');

$id = (int)($_POST['id'] ?? 0);
if(!$id){
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Missing id']);
    exit;
}

try {
    $stmt = $pdo->prepare('UPDATE admin_notifications SET is_read = 1 WHERE id = ?');
    $stmt->execute([$id]);
    echo json_encode(['success'=>true]);
} catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Server error']);
}
