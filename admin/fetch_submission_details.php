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
    // Fetch documents with richer metadata
    $filesStmt = $pdo->prepare("SELECT document_id, doc_type, file_path, file_size, mime_type, verified FROM submission_documents WHERE submission_id = ? ORDER BY doc_type ASC");
    $filesStmt->execute([$sid]);
    $docs = $filesStmt->fetchAll(PDO::FETCH_ASSOC);
    $filesMap = []; // legacy simple map doc_type => absolute URL (kept for backward compatibility)
    $filesDetailed = [];
    foreach($docs as $d){
        $docType = $d['doc_type'];
        $publicUrl = asset_url('uploads/'.$d['file_path']);
        $filesMap[$docType] = $publicUrl;
        // Human readable label (basic normalization; can be extended)
        $label = ucwords(str_replace(['_','-'],' ', $docType));
        $filesDetailed[] = [
            'document_id' => (int)$d['document_id'],
            'type'        => $docType,
            'label'       => $label,
            'url'         => $publicUrl,
            'file_path'   => $d['file_path'],
            'size'        => isset($d['file_size']) ? (int)$d['file_size'] : null,
            'mime_type'   => $d['mime_type'] ?? null,
            'verified'    => isset($d['verified']) ? (int)$d['verified'] : 0
        ];
    }
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
    $fullName = trim(trim(($sub['first_name']??'')) . ' ' . trim(($sub['middle_name']??'')) . ' ' . trim(($sub['last_name']??'')));
    $response = [
        'success' => true,
        'submission_id' => $sid,
        'request_id' => $sub['submission_code'],
        'studentName' => $fullName !== '' ? $fullName : trim(($sub['first_name']??'').' '.($sub['last_name']??'')),
        'studentNumber' => $sub['student_number'] ?? '',
        'email' => $sub['webmail'] ?? '',
        'homeAddress' => $sub['home_address'] ?? '',
        'campus' => $sub['campus'] ?? '',
        'department' => $sub['college'] ?? '',
        'college' => $sub['college'] ?? '',
        'program' => $sub['program'] ?? '',
    'academicLevel' => $sub['academic_level'] ?? '',
        'documentTitle' => $sub['title'] ?? '',
        'accomplishmentDate' => $sub['date_accomplished'] ?? '',
        // Application Type (Work Classification)
        'workClassification' => $sub['work_classification'] ?? '',
        'workClassificationCode' => (function($wc){
            if(!$wc) return '';
            if(preg_match('/\(([^)]+)\)/', (string)$wc, $m)){
                return strtolower(trim($m[1]));
            }
            return '';
        })($sub['work_classification'] ?? ''),
        'files' => $filesMap,            // simple map kept for existing JS code
        'files_list' => $filesDetailed,  // new richer list for enhanced UI
        'additionalAuthors' => $additionalAuthors
    ];
    echo json_encode($response);
} catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Exception','detail'=>$e->getMessage()]);
}
