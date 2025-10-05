<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';

header('Content-Type: application/json');

$code = trim((string)($_GET['code'] ?? ''));
$userId = (int)($_SESSION['user_id'] ?? 0);
if ($code === '' || $userId <= 0) { echo json_encode(['success'=>false,'message'=>'Missing params']); exit; }

try {
    // Ensure this submission belongs to the logged-in user
    $stmt = $pdo->prepare("
        SELECT 
            s.submission_id, 
            s.submission_code, 
            s.status,
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
    if (!$row) { echo json_encode(['success'=>false,'message'=>'Not found']); exit; }

    // Build signed token (HMAC over compact payload)
    $payload = [
        'c' => (string)$row['submission_code'],
        'u' => $userId,
        's' => (string)($row['status'] ?? ''),
        'iat'=> time(),
        'exp'=> time() + 24*60*60 // 24h validity
    ];
    $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
    $pB64 = rtrim(strtr(base64_encode($json), '+/', '-_'), '=');
    $sig  = hash_hmac('sha256', $pB64, TICKET_SIGNING_KEY, true);
    $sB64 = rtrim(strtr(base64_encode($sig), '+/', '-_'), '=');
    $token = $pB64 . '.' . $sB64;

    $base = rtrim(BASE_URL, '/');
    // Prefer validate_ticket (minimal for code-only, full details with token)
    $verifyUrl = $base . '/validate_ticket.php?code=' . urlencode($row['submission_code']) . '&t=' . urlencode($token);

    // Robust full name
    $fullName = trim(trim((string)($row['first_name'] ?? '')) . ' ' . trim((string)($row['middle_name'] ?? '')) . ' ' . trim((string)($row['last_name'] ?? '')));
    if ($fullName === '') {
        try {
            $u = $pdo->prepare('SELECT first_name, middle_name, last_name FROM users WHERE user_id = ?');
            $u->execute([$userId]);
            $ur = $u->fetch(PDO::FETCH_ASSOC) ?: [];
            $fullName = trim(trim((string)($ur['first_name'] ?? '')) . ' ' . trim((string)($ur['middle_name'] ?? '')) . ' ' . trim((string)($ur['last_name'] ?? '')));
        } catch (Throwable $e) { /* ignore */ }
    }

    echo json_encode([
        'success'   => true,
        'verifyUrl' => $verifyUrl,
        'name'      => $fullName,
        'status'    => (string)($row['status'] ?? ''),
        'expires_at'=> $payload['exp'],
    ]);
} catch (Throwable $e) {
    echo json_encode(['success'=>false,'message'=>'Server error']);
}
