<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
header('Content-Type: application/json');

$userId = (int)($_SESSION['user_id'] ?? 0);
$code = trim((string)($_GET['code'] ?? ''));
if ($userId <= 0 || $code === '') { echo json_encode(['success'=>false,'error'=>'Missing params']); exit; }

try {
    // Get the name from user profile tables
    $stmt = $pdo->prepare("
        SELECT 
            CASE 
                WHEN u.role = 'student' THEN sp.first_name
                WHEN u.role = 'employee' THEN ep.first_name
                ELSE 'Unknown'
            END as first_name,
            CASE 
                WHEN u.role = 'student' THEN sp.middle_name
                WHEN u.role = 'employee' THEN ep.middle_name
                ELSE ''
            END as middle_name,
            CASE 
                WHEN u.role = 'student' THEN sp.last_name
                WHEN u.role = 'employee' THEN ep.last_name
                ELSE 'User'
            END as last_name
        FROM submissions s
        LEFT JOIN users u ON u.user_id = s.user_id
        LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
        LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
        WHERE s.submission_code = ? AND s.user_id = ? LIMIT 1");
    $stmt->execute([$code, $userId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    $full = '';
    if ($row) {
        $fn = trim((string)($row['first_name'] ?? ''));
        $mn = trim((string)($row['middle_name'] ?? ''));
        $ln = trim((string)($row['last_name'] ?? ''));
        $full = trim(trim($fn . ' ' . $mn . ' ' . $ln));
    }

    // Fallback to users table if submission has no name
    if ($full === '') {
        $u = $pdo->prepare("SELECT first_name, middle_name, last_name FROM users WHERE user_id = ? LIMIT 1");
        $u->execute([$userId]);
        $ur = $u->fetch(PDO::FETCH_ASSOC) ?: [];
        $fn = trim((string)($ur['first_name'] ?? ''));
        $mn = trim((string)($ur['middle_name'] ?? ''));
        $ln = trim((string)($ur['last_name'] ?? ''));
        $full = trim(trim($fn . ' ' . $mn . ' ' . $ln));
    }

    // Fallback to session as last resort
    if ($full === '') {
        $sf = trim((string)($_SESSION['first_name'] ?? ''));
        $sm = trim((string)($_SESSION['middle_name'] ?? ''));
        $sl = trim((string)($_SESSION['last_name'] ?? ''));
        $full = trim(trim($sf . ' ' . $sm . ' ' . $sl));
    }

    echo json_encode(['success'=>true, 'name'=>$full]);
} catch (Throwable $e) {
    echo json_encode(['success'=>false,'error'=>'Server error']);
}
