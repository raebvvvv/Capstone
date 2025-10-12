<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
require_admin();
header('Content-Type: application/json');

try {
    // Ensure admin_notifications table exists with the extended schema used by re-upload flow
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        submission_id INT NOT NULL,
        submission_code VARCHAR(100) NOT NULL,
        user_id INT NOT NULL,
        doc_type VARCHAR(100) NOT NULL,
        message VARCHAR(255) NOT NULL,
        notification_type VARCHAR(50) DEFAULT 'resubmission',
        occurrence_count INT DEFAULT 1,
        meta TEXT DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_read TINYINT(1) DEFAULT 0,
        INDEX (is_read),
        INDEX (submission_id),
        INDEX (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // If table exists from an older schema, attempt to add the missing columns (best-effort)
    try {
        $colCheck = $pdo->query("SHOW COLUMNS FROM admin_notifications LIKE 'notification_type'");
        if ($colCheck && $colCheck->rowCount() === 0) {
            $pdo->exec("ALTER TABLE admin_notifications ADD COLUMN notification_type VARCHAR(50) DEFAULT 'resubmission'");
        }
    } catch (Throwable $e) { /* ignore */ }
    try {
        $colCheck = $pdo->query("SHOW COLUMNS FROM admin_notifications LIKE 'occurrence_count'");
        if ($colCheck && $colCheck->rowCount() === 0) {
            $pdo->exec("ALTER TABLE admin_notifications ADD COLUMN occurrence_count INT DEFAULT 1");
        }
    } catch (Throwable $e) { /* ignore */ }
    try {
        $colCheck = $pdo->query("SHOW COLUMNS FROM admin_notifications LIKE 'meta'");
        if ($colCheck && $colCheck->rowCount() === 0) {
            $pdo->exec("ALTER TABLE admin_notifications ADD COLUMN meta TEXT DEFAULT NULL");
        }
    } catch (Throwable $e) { /* ignore */ }

    // Pagination inputs
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 20;
    if ($page < 1) { $page = 1; }
    if ($limit < 5) { $limit = 5; }
    if ($limit > 100) { $limit = 100; }
    $offset = ($page - 1) * $limit;

    // Total notifications count
    $totalStmt = $pdo->query('SELECT COUNT(*) FROM admin_notifications');
    $total = (int)$totalStmt->fetchColumn();

    // Include type and occurrence_count to allow clients to render aggregated entries properly
    $stmt = $pdo->prepare('SELECT id, submission_code, doc_type, message, notification_type, occurrence_count, created_at, is_read FROM admin_notifications ORDER BY created_at DESC, id DESC LIMIT :offset, :limit');
    $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
    $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $unreadStmt = $pdo->query('SELECT COUNT(*) FROM admin_notifications WHERE is_read = 0');
    $unread = (int)$unreadStmt->fetchColumn();

    $totalPages = $total > 0 ? (int)ceil($total / $limit) : 1;
    echo json_encode([
        'success'=>true,
        'unread'=>$unread,
        'notifications'=>$rows,
        'pagination'=>[
            'page'=>$page,
            'limit'=>$limit,
            'total'=>$total,
            'totalPages'=>$totalPages,
            'hasPrev'=>$page > 1,
            'hasNext'=>$page < $totalPages
        ]
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Server error','detail'=>$e->getMessage()]);
}
