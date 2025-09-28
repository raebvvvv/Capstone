<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
require_admin();
header('Content-Type: application/json');

try {
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        submission_id INT NOT NULL,
        submission_code VARCHAR(100) NOT NULL,
        user_id INT NOT NULL,
        doc_type VARCHAR(100) NOT NULL,
        message VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_read TINYINT(1) DEFAULT 0,
        INDEX (is_read),
        INDEX (submission_id),
        INDEX (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    $limit = 20;
    $stmt = $pdo->prepare('SELECT id, submission_code, doc_type, message, created_at, is_read FROM admin_notifications ORDER BY id DESC LIMIT ?');
    $stmt->bindValue(1, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $unreadStmt = $pdo->query('SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0');
    $unread = (int)$unreadStmt->fetchColumn();

    echo json_encode(['success'=>true,'unread'=>$unread,'notifications'=>$rows]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Server error','detail'=>$e->getMessage()]);
}
