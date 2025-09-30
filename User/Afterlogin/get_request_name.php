<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
header('Content-Type: application/json');

$userId = (int)($_SESSION['user_id'] ?? 0);
$code = trim((string)($_GET['code'] ?? ''));
if ($userId <= 0 || $code === '') { echo json_encode(['success'=>false,'error'=>'Missing params']); exit; }

try {
    // Prefer the name stored in the submission (authoritative for the ticket)
    $stmt = $pdo->prepare("SELECT first_name, middle_name, last_name FROM submissions WHERE submission_code = ? AND user_id = ? LIMIT 1");
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
