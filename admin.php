<?php
// Moved: keep backward compatibility by redirecting to /admin/admin.php
require __DIR__ . '/config.php';
redirect('admin/admin.php');
exit;

$stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE role IN ('student','employee') AND status = ?");
$stmt->execute(['active']);
$total_users = $stmt->fetch()['count'];

