<?php
// Lightweight endpoint: returns JSON with unlock_files for a submission
require __DIR__ . '/config.php';
require app_path('conn.php');
header('Content-Type: application/json; charset=utf-8');

// CORS / same-origin only (basic): allow only GET and same host
if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$code = isset($_GET['code']) ? trim((string)$_GET['code']) : '';
if ($code === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Missing code']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT si.unlock_files FROM submissions s LEFT JOIN submission_incomplete si ON si.submission_code = s.submission_code WHERE s.submission_code = :c LIMIT 1");
    $stmt->execute([':c' => $code]);
    $row = $stmt->fetch();
    if(!$row){
        echo json_encode(['submission_code' => $code, 'unlock_files' => []]);
        exit;
    }
    $filesJson = $row['unlock_files'] ?? '[]';
    $files = [];
    if ($filesJson) {
        $decoded = json_decode($filesJson, true);
        if (is_array($decoded)) { $files = $decoded; }
    }
    echo json_encode(['submission_code' => $code, 'unlock_files' => $files]);
} catch (Throwable $e) {
    if (function_exists('log_event')) { log_event('DB_ERROR', 'unlock info fetch failed', ['err' => $e->getMessage()]); }
    http_response_code(500);
    echo json_encode(['error' => 'Server error']);
}
