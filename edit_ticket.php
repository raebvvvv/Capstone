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
// Optional fields (update only if provided)
$email = isset($input['email']) ? trim((string)$input['email']) : null; // maps to webmail
$homeAddress = isset($input['home_address']) ? trim((string)$input['home_address']) : null;
$campus = isset($input['campus']) ? trim((string)$input['campus']) : null;
$college = isset($input['college']) ? trim((string)$input['college']) : null;
$documentTitle = isset($input['document_title']) ? trim((string)$input['document_title']) : null;
$accomplishmentDate = isset($input['accomplishment_date']) ? trim((string)$input['accomplishment_date']) : null; // YYYY-MM-DD

if ($requestId === '') {
    echo json_encode(['success' => false, 'error' => 'Invalid request_id']);
    exit();
}
// Note: email is not stored on submissions; skip email edits here

try {
    // Determine identifier column for submissions table
    $idCol = ctype_digit($requestId) ? 'submission_id' : 'submission_code';
    // Build dynamic SET clause for optional fields
    $sets = [
        'first_name = :first_name',
        'middle_name = :middle_name',
        'last_name = :last_name',
        'student_number = :student_number',
        'program = :program'
    ];
    $params = [];
    if ($email !== null) { $sets[] = 'webmail = :webmail'; $params[':webmail'] = $email; }
    if ($homeAddress !== null) { $sets[] = 'home_address = :home_address'; $params[':home_address'] = $homeAddress; }
    if ($campus !== null) { $sets[] = 'campus = :campus'; $params[':campus'] = $campus; }
    if ($college !== null) { $sets[] = 'college = :college'; $params[':college'] = $college; }
    if ($documentTitle !== null) { $sets[] = 'title = :title'; $params[':title'] = $documentTitle; }
    if ($accomplishmentDate !== null && $accomplishmentDate !== '') {
        // Basic YYYY-MM-DD validation
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $accomplishmentDate)) {
            echo json_encode(['success' => false, 'error' => 'Invalid date format (YYYY-MM-DD)']);
            exit();
        }
        $sets[] = 'date_accomplished = :date_accomplished';
        $params[':date_accomplished'] = $accomplishmentDate;
    }
    $sets[] = 'updated_at = NOW()';
    $sql = 'UPDATE submissions SET ' . implode(', ', $sets) . " WHERE $idCol = :id";
    // Attempt to split full name into first, middle, last
    $firstName = trim($studentName);
    $middleName = '';
    $lastName = '';
    if ($studentName !== '') {
        $parts = preg_split('/\s+/', $studentName, -1, PREG_SPLIT_NO_EMPTY);
        if (count($parts) === 1) {
            $firstName = $parts[0];
            $middleName = '';
            $lastName = '';
        } elseif (count($parts) === 2) {
            $firstName = $parts[0];
            $middleName = '';
            $lastName = $parts[1];
        } else {
            $firstName = array_shift($parts);
            $lastName = array_pop($parts);
            $middleName = trim(implode(' ', $parts));
        }
    }
    $stmt = $pdo->prepare($sql);
    $execParams = array_merge($params, [
        ':first_name' => $firstName,
        ':middle_name' => $middleName,
        ':last_name' => $lastName,
        ':student_number' => $studentId,
        ':program' => $program,
        ':id' => ctype_digit($requestId) ? (int)$requestId : $requestId,
    ]);
    $ok = $stmt->execute($execParams);
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