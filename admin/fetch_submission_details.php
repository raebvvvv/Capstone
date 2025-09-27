<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();
header('Content-Type: application/json');
try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success'=>false,'error'=>'Method not allowed']);
        exit;
    }
    $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
    if (function_exists('csrf_token') && $csrfHeader !== csrf_token()) {
        http_response_code(403);
        echo json_encode(['success'=>false,'error'=>'CSRF token mismatch']);
        exit;
    }
    $raw = file_get_contents('php://input');
    $payload = json_decode($raw, true);
    if(!is_array($payload)) { $payload = $_POST; }
    $requestId = trim((string)($payload['request_id'] ?? ''));
    if($requestId==='') { echo json_encode(['success'=>false,'error'=>'Missing request_id']); exit; }
    $isNumeric = ctype_digit($requestId);
    $col = $isNumeric ? 'submission_id' : 'submission_code';
    $stmt = $pdo->prepare("SELECT * FROM submissions WHERE $col = ? LIMIT 1");
    $stmt->execute([$isNumeric ? (int)$requestId : $requestId]);
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$sub) { echo json_encode(['success'=>false,'error'=>'Submission not found']); exit; }
    $sid = (int)$sub['submission_id'];
    // Fetch documents
    $filesStmt = $pdo->prepare("SELECT document_id, doc_type, file_path, file_size, mime_type, verified FROM submission_documents WHERE submission_id = ?");
    $filesStmt->execute([$sid]);
    $docs = $filesStmt->fetchAll(PDO::FETCH_ASSOC);
    $filesMap = [];
    foreach($docs as $d){ $filesMap[$d['doc_type']] = asset_url('uploads/'.$d['file_path']); }
    // Fetch authors
    $authStmt = $pdo->prepare("SELECT first_name, last_name, student_id, mobile, home_address, webmail, is_adviser FROM submission_authors WHERE submission_id = ? ORDER BY is_adviser DESC, first_name ASC");
    $authStmt->execute([$sid]);
    $authorsRaw = $authStmt->fetchAll(PDO::FETCH_ASSOC);
    $additionalAuthors = [];
    foreach($authorsRaw as $a){
        $additionalAuthors[] = [
            'name' => trim($a['first_name'].' '.$a['last_name']),
            'studentNumber' => $a['student_id'],
            'email' => $a['webmail'],
            'phone' => $a['mobile'],
            'address' => $a['home_address'],
            'is_adviser' => (int)$a['is_adviser']
        ];
    }
    $response = [
        'success' => true,
        'submission_id' => $sid,
        'request_id' => $sub['submission_code'],
        'studentName' => trim(($sub['first_name']??'').' '.($sub['last_name']??'')),
        'studentNumber' => $sub['student_number'] ?? '',
        'email' => $sub['webmail'] ?? '',
        'homeAddress' => $sub['home_address'] ?? '',
        'campus' => $sub['campus'] ?? '',
        'department' => $sub['college'] ?? '',
        'college' => $sub['college'] ?? '',
        'program' => $sub['program'] ?? '',
        'documentTitle' => $sub['title'] ?? '',
        'accomplishmentDate' => $sub['date_accomplished'] ?? '',
        'files' => $filesMap,
        'additionalAuthors' => $additionalAuthors
    ];
    echo json_encode($response);
} catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Exception','detail'=>$e->getMessage()]);
}
