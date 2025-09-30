<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

$status = isset($_GET['status']) ? strtolower(trim($_GET['status'])) : 'active';
$allowed_status = ['active','inactive','pending'];
if (!in_array($status, $allowed_status, true)) { $status = 'active'; }
$role = isset($_GET['role']) ? strtolower(trim($_GET['role'])) : 'all';
$allowed_roles = ['all','student','employee','admin'];
if (!in_array($role, $allowed_roles, true)) { $role = 'all'; }
$search = isset($_GET['search']) ? $_GET['search'] : '';
$q = '%' . $search . '%';

$sql = "SELECT user_id, student_number, email, role, status, created_at FROM users WHERE status = :status AND (student_number LIKE :q1 OR email LIKE :q2)";
if ($role !== 'all') { $sql .= " AND role = :role"; }
$sql .= " ORDER BY created_at DESC";
$stmt = $pdo->prepare($sql);
$stmt->bindValue(':status', $status, PDO::PARAM_STR);
$stmt->bindValue(':q1', $q, PDO::PARAM_STR);
$stmt->bindValue(':q2', $q, PDO::PARAM_STR);
if ($role !== 'all') { $stmt->bindValue(':role', $role, PDO::PARAM_STR); }
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$filename = sprintf('users_%s_%s.csv', $status, date('Ymd_His'));
header('Content-Type: text/csv; charset=UTF-8');
header('Content-Disposition: attachment; filename=' . $filename);
// UTF-8 BOM for Excel compatibility
$bom = chr(239) . chr(187) . chr(191);
echo $bom;
$out = fopen('php://output', 'w');
fputcsv($out, ['User ID','Student/Employee ID','Email','Role','Status','Created At']);
foreach ($rows as $r) {
    fputcsv($out, [
        $r['user_id'],
        $r['student_number'],
        $r['email'],
        ucfirst($r['role']),
        ucfirst($r['status']),
        $r['created_at'],
    ]);
}
fclose($out);
exit;
