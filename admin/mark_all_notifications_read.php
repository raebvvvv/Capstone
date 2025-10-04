<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
require_admin();
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'error'=>'Method not allowed']);
    exit;
}

try {
    // Optional: if you want to scope to this admin only, add an admin_id column in the table in the future.
    $stmt = $pdo->prepare('UPDATE admin_notifications SET is_read = 1 WHERE is_read = 0');
    $stmt->execute();
    echo json_encode(['success'=>true,'affected'=>$stmt->rowCount()]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Server error']);
}
