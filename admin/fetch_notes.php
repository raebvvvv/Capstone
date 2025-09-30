<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

$submission_id = (int)($_GET['submission_id'] ?? 0);
if ($submission_id <= 0) {
    http_response_code(400);
    header('Content-Type: application/json');
    echo json_encode(['error' => 'Invalid submission_id']);
    exit;
}

try {
    // Ensure notes table exists
    $pdo->exec("CREATE TABLE IF NOT EXISTS submission_notes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        submission_id INT NOT NULL,
        user_id INT NOT NULL,
        note TEXT NOT NULL,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        CONSTRAINT fk_sn_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    // Tracking table: marks when admins viewed notes (for unread count)
    $pdo->exec("CREATE TABLE IF NOT EXISTS submission_notes_admin_views (
        id INT AUTO_INCREMENT PRIMARY KEY,
        submission_id INT NOT NULL,
        admin_id INT NOT NULL,
        last_viewed_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        UNIQUE KEY uniq_view (submission_id, admin_id),
        INDEX idx_submission_admin (submission_id, admin_id),
        CONSTRAINT fk_snav_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE,
        CONSTRAINT fk_snav_admin FOREIGN KEY (admin_id) REFERENCES users(user_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
} catch (Throwable $e) { /* ignore */ }

$stmt = $pdo->prepare('SELECT n.note, n.created_at, u.email FROM submission_notes n LEFT JOIN users u ON u.user_id = n.user_id WHERE n.submission_id = ? ORDER BY n.created_at DESC');
$stmt->execute([$submission_id]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Mark as read for this admin
try {
    $adminId = (int)($_SESSION['user_id'] ?? 0);
    if ($adminId > 0) {
        $ins = $pdo->prepare('INSERT INTO submission_notes_admin_views (submission_id, admin_id, last_viewed_at) VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE last_viewed_at = VALUES(last_viewed_at)');
        $ins->execute([$submission_id, $adminId]);
    }
} catch (Throwable $e) { /* ignore */ }

header('Content-Type: application/json');
echo json_encode(['notes' => $rows]);
