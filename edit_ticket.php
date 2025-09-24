<?php
// Update selected fields of a submission (uses PDO)
require __DIR__ . '/config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
header('Content-Type: application/json');
if (function_exists('verify_csrf_header')) { verify_csrf_header(); }
if (function_exists('require_admin')) { require_admin(); }

$input = json_decode(file_get_contents('php://input'), true) ?: [];
$requestId = trim((string)($input['request_id'] ?? ''));
$studentName = trim((string)($input['student_name'] ?? ''));
$studentId = trim((string)($input['student_id'] ?? ''));
$program = trim((string)($input['program'] ?? ''));

if ($requestId === '') {
    echo json_encode(['success' => false, 'error' => 'Invalid request_id']);
    exit();
}
// Note: email is not stored on submissions; skip email edits here

try {
    // Determine identifier column for submissions table
    $idCol = ctype_digit($requestId) ? 'submission_id' : 'submission_code';
    $sql = "UPDATE submissions
            SET first_name = :first_name,
                last_name = :last_name,
                student_number = :student_number,
                program = :program,
                updated_at = NOW()
            WHERE $idCol = :id";
    // Attempt to split full name if provided; fallback to existing values if empty inputs
    $firstName = $studentName;
    $lastName = '';
    if (strpos($studentName, ' ') !== false) {
        $parts = preg_split('/\s+/', $studentName);
        $firstName = array_shift($parts);
        $lastName = implode(' ', $parts);
    }
    $stmt = $pdo->prepare($sql);
    $ok = $stmt->execute([
        ':first_name' => $firstName,
        ':last_name' => $lastName,
        ':student_number' => $studentId,
        ':program' => $program,
        ':id' => ctype_digit($requestId) ? (int)$requestId : $requestId,
    ]);
    if ($ok) {
        if (function_exists('log_event')) { log_event('EDIT_REQUEST', 'Request edited', ['request_id' => $requestId]); }
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'DB error']);
    }
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_ERROR', 'Edit request failed', ['err' => $e->getMessage()]); }
    echo json_encode(['success' => false, 'error' => 'Operation failed']);
}
?>