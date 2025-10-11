<?php
require_once __DIR__ . '/../config.php';
require_once __DIR__ . '/../auth_check.php';
header('Content-Type: application/json; charset=utf-8');
try {
    if (!isset($_SESSION['user_id'])) {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'unauthenticated']);
        exit;
    }
    $user_id = (int)$_SESSION['user_id'];
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    if ($id <= 0) { http_response_code(400); echo json_encode(['success'=>false,'message'=>'invalid_id']); exit; }
    $st = $pdo->prepare('UPDATE user_notifications SET deleted_at = NULL WHERE id = ? AND user_id = ?');
    $st->execute([$id, $user_id]);
    echo json_encode(['success'=>true,'restored'=>$st->rowCount()]);
} catch(Throwable $e){ http_response_code(500); echo json_encode(['success'=>false,'message'=>'server_error']); }
