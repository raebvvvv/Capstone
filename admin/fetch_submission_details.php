<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();
header('Content-Type: application/json');
// Avoid stale caches for details
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
try {
    $method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
    // Read-only endpoint: allow GET without CSRF; require CSRF only for POST
    if ($method !== 'GET' && $method !== 'POST') {
        http_response_code(405);
        echo json_encode(['success'=>false,'error'=>'Method not allowed']);
        exit;
    }
    if ($method === 'POST') {
        $csrfHeader = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (function_exists('csrf_token') && $csrfHeader !== csrf_token()) {
            http_response_code(403);
            echo json_encode(['success'=>false,'error'=>'CSRF token mismatch']);
            exit;
        }
    }
    $requestId = '';
    if ($method === 'POST') {
        $raw = file_get_contents('php://input');
        $payload = json_decode($raw, true);
        if(!is_array($payload)) { $payload = $_POST; }
        $requestId = trim((string)($payload['request_id'] ?? ''));
    } else { // GET
        $requestId = trim((string)($_GET['request_id'] ?? ''));
    }
    if($requestId==='') { echo json_encode(['success'=>false,'error'=>'Missing request_id']); exit; }
    $isNumeric = ctype_digit($requestId);
    $col = $isNumeric ? 'submission_id' : 'submission_code';
    $stmt = $pdo->prepare("
        SELECT s.*, 
               CASE 
                   WHEN u.role = 'student' THEN sp.student_number
                   WHEN u.role = 'employee' THEN ep.employee_number  
                   ELSE CONCAT('User-', s.user_id)
               END as identifier
        FROM submissions s
        LEFT JOIN users u ON u.user_id = s.user_id
        LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
        LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
        WHERE $col = ? LIMIT 1");
        $stmt = $pdo->prepare("
            SELECT s.*, 
                   CASE 
                       WHEN u.role = 'student' THEN sp.student_number
                       WHEN u.role = 'employee' THEN ep.employee_number  
                       ELSE CONCAT('User-', s.user_id)
                   END as identifier,
                   ep.academic_level AS ep_level, ep.program AS ep_program,
                   sp.academic_level AS sp_level, sp.program AS sp_program,
                   u.role AS user_role
            FROM submissions s
            LEFT JOIN users u ON u.user_id = s.user_id
            LEFT JOIN student_profiles sp ON u.user_id = sp.user_id AND u.role = 'student'
            LEFT JOIN employee_profiles ep ON u.user_id = ep.user_id AND u.role = 'employee'
            WHERE $col = ? LIMIT 1");
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
    // Fetch authors (include middle_name for proper display)
    $authStmt = $pdo->prepare("SELECT author_id, first_name, middle_name, last_name, student_id, mobile, home_address, webmail, is_adviser FROM submission_authors WHERE submission_id = ? ORDER BY is_adviser DESC, first_name ASC");
    $authStmt->execute([$sid]);
    $authorsRaw = $authStmt->fetchAll(PDO::FETCH_ASSOC);
    $additionalAuthors = [];
    foreach($authorsRaw as $a){
        // Build full name: First Middle Last (use middle when available)
        $mid = trim((string)($a['middle_name'] ?? ''));
        $dispName = trim(preg_replace('/\s+/', ' ',
            ($a['first_name'] ?? '') . ' ' . ($mid !== '' ? $mid . ' ' : '') . ($a['last_name'] ?? '')
        ));
        $additionalAuthors[] = [
            'id' => isset($a['author_id']) ? (int)$a['author_id'] : null,
            'name' => $dispName !== '' ? $dispName : trim(($a['first_name'] ?? '').' '.($a['last_name'] ?? '')),
            'studentNumber' => $a['student_id'],
            'email' => $a['webmail'],
            'phone' => $a['mobile'],
            'address' => $a['home_address'],
            'is_adviser' => (int)$a['is_adviser']
        ];
    }
    // Build student's full name with full middle name if present
    $mid = trim((string)($sub['middle_name'] ?? ''));
    $fullName = trim(preg_replace('/\s+/', ' ',
        (string)($sub['first_name'] ?? '') . ' ' . ($mid !== '' ? $mid . ' ' : '') . (string)($sub['last_name'] ?? '')
    ));
    // Determine adviser name:
    // 1) prefer submissions.adviser (string)
    // 2) then submissions.adviser_id via advisers table
    // 3) then first author flagged is_adviser=1
    $adviserName = trim((string)($sub['adviser'] ?? ''));
    if ($adviserName === '') {
        $adviserId = isset($sub['adviser_id']) ? (int)$sub['adviser_id'] : 0;
        if ($adviserId > 0) {
            try {
                $advStmt = $pdo->prepare('SELECT first_name, middle_name, last_name FROM advisers WHERE adviser_id = ? LIMIT 1');
                $advStmt->execute([$adviserId]);
                if ($row = $advStmt->fetch(PDO::FETCH_ASSOC)) {
                    $adviserName = trim(($row['first_name']??'') . ' ' . ($row['middle_name']??'') . ' ' . ($row['last_name']??''));
                }
            } catch (Throwable $e) { /* ignore missing table/column */ }
        }
    }
    if ($adviserName === '') {
        foreach ($authorsRaw as $a) {
            if ((int)($a['is_adviser'] ?? 0) === 1) {
                $adviserName = trim(($a['first_name'] ?? '') . ' ' . ($a['last_name'] ?? ''));
                if ($adviserName !== '') break;
            }
        }
    }

    // Determine Academic Level with robust fallbacks/inference
    $rawLevel = trim((string)($sub['academic_level'] ?? ''));
    $profileLevel = '';
    $userRole = (string)($sub['user_role'] ?? '');
    if ($userRole === 'employee') {
        $profileLevel = trim((string)($sub['ep_level'] ?? ''));
    } elseif ($userRole === 'student') {
        $profileLevel = trim((string)($sub['sp_level'] ?? ''));
    }
    $level = $rawLevel !== '' ? $rawLevel : $profileLevel;
    $program = trim((string)($sub['program'] ?? ''));
    if ($program === '') {
        $program = trim((string)($sub['ep_program'] ?? $sub['sp_program'] ?? ''));
    }
    $lvlLow = strtolower($level);
    if ($level === '' || $lvlLow === 'employee' || $lvlLow === 'n/a' || $lvlLow === 'na' || $lvlLow === '-') {
        $p = strtolower($program);
        if ($p === 'n/a' || $p === 'na' || $p === '' || strpos($p,'not study') !== false) {
            $level = 'Not Studying';
        } elseif (strpos($p,'open') !== false) {
            $level = 'Open University';
        } elseif (strpos($p,'doctor') !== false || strpos($p,'doctoral') !== false || strpos($p,'doctorate') !== false || strpos($p,'phd') !== false || strpos($p,'ph.d') !== false || strpos($p,'master') !== false || strpos($p,'postgrad') !== false) {
            $level = 'Graduate School';
        } else {
            $level = 'Undergraduate';
        }
    }

    $response = [
        'success' => true,
        'submission_id' => $sid,
        'request_id' => $sub['submission_code'],
        'studentName' => $fullName !== '' ? $fullName : trim(($sub['first_name']??'').' '.($sub['last_name']??'')),
        'studentNumber' => $sub['identifier'] ?? '',
        'email' => $sub['webmail'] ?? '',
        'homeAddress' => $sub['home_address'] ?? '',
        'campus' => $sub['campus'] ?? '',
        'department' => $sub['college'] ?? '',
        'college' => $sub['college'] ?? '',
    'program' => $sub['program'] ?? '',
            // Provide both camelCase and snake_case for front-end tolerance
            'academicLevel' => $level,
            'academic_level' => $level,
        'documentTitle' => $sub['title'] ?? '',
    // Adviser fields (string name stored on submission table; fallback to first co-author with is_adviser=1)
    'adviser' => $adviserName,
        'adviser_coauthor' => isset($sub['adviser_coauthor']) ? (int)$sub['adviser_coauthor'] : 0,
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
