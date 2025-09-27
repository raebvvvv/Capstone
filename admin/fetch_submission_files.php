<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();

header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }

    // CSRF (accept either JSON header or form token)
    $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (function_exists('csrf_token') && $csrfHeader !== csrf_token()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'error' => 'CSRF token mismatch']);
        exit;
    }

    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);
    if (!is_array($payload)) { $payload = $_POST; }

    $requestId = trim((string)($payload['request_id'] ?? ''));
    if ($requestId === '') {
        echo json_encode(['success' => false, 'error' => 'Missing request_id']);
        exit;
    }

    // Determine identifying column (submission_code or submission_id)
    $isNumeric = ctype_digit($requestId);
    $col = $isNumeric ? 'submission_id' : 'submission_code';

    $stmt = $pdo->prepare("SELECT submission_id, submission_code FROM submissions WHERE $col = ? LIMIT 1");
    $stmt->execute([$isNumeric ? (int)$requestId : $requestId]);
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$sub) {
        echo json_encode(['success' => false, 'error' => 'Submission not found']);
        exit;
    }

    $sid = (int)$sub['submission_id'];
    $filesStmt = $pdo->prepare("SELECT document_id, doc_type, file_path, verified FROM submission_documents WHERE submission_id = ? ORDER BY doc_type ASC");
    $filesStmt->execute([$sid]);
    $files = $filesStmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'submission_id' => $sid,
        'request_id' => $sub['submission_code'],
        'files' => array_map(function($f){
            return [
                'document_id' => (int)$f['document_id'],
                'doc_type' => $f['doc_type'],
                'file_path' => $f['file_path'],
                'verified' => (int)$f['verified']
            ];
        }, $files)
    ]);
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Exception', 'detail' => $e->getMessage()]);
}
