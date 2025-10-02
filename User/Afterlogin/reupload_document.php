<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
header('Content-Type: application/json');

if($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success'=>false,'error'=>'Method not allowed']);
    exit;
}

$user_id = (int)($_SESSION['user_id'] ?? 0);
$submissionCode = trim((string)($_POST['submission_code'] ?? ''));
$docType = trim((string)($_POST['doc_type'] ?? ''));

if($submissionCode === '' || $docType === ''){
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Missing parameters']);
    exit;
}

// Basic allowed doc types whitelist (can be expanded)
$ALLOWED_DOC_TYPES = [
    'journal_publication_format', 'notarized_copyright', 'receipt_payment', 'full_manuscript',
    'notarized_coauthorship', 'approval_sheet', 'record_copyright'
];
if(!in_array($docType, $ALLOWED_DOC_TYPES, true)){
    http_response_code(422);
    echo json_encode(['success'=>false,'error'=>'Invalid document type']);
    exit;
}

try {
    $stmt = $pdo->prepare('SELECT submission_id,status,user_id FROM submissions WHERE submission_code = ? LIMIT 1');
    $stmt->execute([$submissionCode]);
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);
    if(!$sub || (int)$sub['user_id'] !== $user_id){
        http_response_code(403);
        echo json_encode(['success'=>false,'error'=>'Access denied']);
        exit;
    }

    // Allow re-upload while in pending-like states AND when status is approved (admin-approved but flagged for resubmission)
    $allowStates = ['pending','pending_review','under_review','revision_needed','approved'];
    if(!in_array(strtolower($sub['status']), $allowStates, true)){
        http_response_code(409);
        echo json_encode(['success'=>false,'error'=>'Submission not editable in current status']);
        exit;
    }

    if(empty($_FILES['file']) || $_FILES['file']['error'] !== UPLOAD_ERR_OK){
        http_response_code(400);
        echo json_encode(['success'=>false,'error'=>'No file uploaded or upload error']);
        exit;
    }

    $file = $_FILES['file'];
    $maxSize = 5 * 1024 * 1024; // 5MB
    if($file['size'] > $maxSize){
        http_response_code(413);
        echo json_encode(['success'=>false,'error'=>'File too large (max 5MB)']);
        exit;
    }

    // Basic MIME / extension validation
    $allowedExt = ['pdf'];
    $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if(!in_array($ext, $allowedExt, true)){
        http_response_code(422);
        echo json_encode(['success'=>false,'error'=>'Only PDF files allowed']);
        exit;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if($mime !== 'application/pdf'){
        http_response_code(422);
        echo json_encode(['success'=>false,'error'=>'Invalid MIME type']);
        exit;
    }

    // Ensure this document type is requested for resubmission. Accept either pending or approved scope.
    // Prefer approved scope when present (keeps item in Approved while allowing re-upload).
    $metaStmt = $pdo->prepare('SELECT affected_doc_types FROM submission_incomplete_meta WHERE submission_id=? AND scope IN ("pending","approved") ORDER BY FIELD(scope,"approved","pending") LIMIT 1');
    $metaStmt->execute([(int)$sub['submission_id']]);
    $metaRow = $metaStmt->fetch(PDO::FETCH_ASSOC);
    $requested = [];
    if($metaRow && !empty($metaRow['affected_doc_types'])){
        $requested = array_filter(array_map('trim', explode('|', $metaRow['affected_doc_types'])));
    }
    if(!in_array($docType, $requested, true)){
        http_response_code(409);
        echo json_encode(['success'=>false,'error'=>'Document not currently requested for resubmission']);
        exit;
    }

    // Locate existing document row
    $docStmt = $pdo->prepare('SELECT document_id,file_path FROM submission_documents WHERE submission_id=? AND doc_type=? LIMIT 1');
    $docStmt->execute([(int)$sub['submission_id'], $docType]);
    $doc = $docStmt->fetch(PDO::FETCH_ASSOC);
    if(!$doc){
        http_response_code(404);
        echo json_encode(['success'=>false,'error'=>'Document slot not found']);
        exit;
    }

    // Generate new filename (keeping doc_type prefix)
    $newName = $docType . '_' . time() . '_' . bin2hex(random_bytes(4)) . '.pdf';
    // Determine upload directory (relative to project root)
    $uploadDir = __DIR__ . '/../../uploads';
    if(!is_dir($uploadDir)) { @mkdir($uploadDir, 0755, true); }

    $target = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $newName;
    if(!move_uploaded_file($file['tmp_name'], $target)){
        http_response_code(500);
        echo json_encode(['success'=>false,'error'=>'Failed to store file']);
        exit;
    }

    // Optionally remove old file (best-effort)
    if(!empty($doc['file_path'])){
        $oldPath = rtrim($uploadDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR . $doc['file_path'];
        if(is_file($oldPath)) { @unlink($oldPath); }
    }

    $upd = $pdo->prepare('UPDATE submission_documents SET file_path=?, uploaded_at=NOW(), file_size=?, mime_type=?, verified=0, verified_by=NULL, verified_at=NULL WHERE document_id=?');
    $upd->execute([$newName, (int)$file['size'], $mime, (int)$doc['document_id']]);

    // Remove this doc type from pending affected list (submission_incomplete_meta)
    $remaining = [];
    $updatedStatus = null;
    try {
        $metaSel = $pdo->prepare('SELECT affected_doc_types FROM submission_incomplete_meta WHERE submission_id=? LIMIT 1');
        $metaSel->execute([(int)$sub['submission_id']]);
        $meta = $metaSel->fetch(PDO::FETCH_ASSOC);
        if($meta && !empty($meta['affected_doc_types'])){
            $parts = array_filter(array_map('trim', explode('|', $meta['affected_doc_types'])));
            $remaining = array_values(array_filter($parts, function($p) use ($docType){ return $p !== $docType; }));
            $newVal = implode('|', $remaining);
            $updMeta = $pdo->prepare('UPDATE submission_incomplete_meta SET affected_doc_types=? WHERE submission_id=?');
            $updMeta->execute([$newVal, (int)$sub['submission_id']]);
            if(empty($remaining)){
                // Auto-advance status back to pending_review if currently a revision state
                $revStates = ['revision_needed','pending','pending_review','under_review'];
                if(in_array(strtolower($sub['status']), $revStates, true)){
                    $newStatus = 'pending_review';
                    $stUpd = $pdo->prepare('UPDATE submissions SET status=? WHERE submission_id=?');
                    $stUpd->execute([$newStatus, (int)$sub['submission_id']]);
                    $updatedStatus = $newStatus;
                }
            }
        }
    } catch(Throwable $ign){ /* non-fatal */ }

    // Ensure notifications table exists (lightweight check each call; could be optimized)
    $pdo->exec("CREATE TABLE IF NOT EXISTS admin_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        submission_id INT NOT NULL,
        submission_code VARCHAR(100) NOT NULL,
        user_id INT NOT NULL,
        doc_type VARCHAR(100) NOT NULL,
        message VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        is_read TINYINT(1) DEFAULT 0,
        INDEX (is_read),
        INDEX (submission_id),
        INDEX (user_id)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

    // Compose message
    $msg = sprintf('User #%d re-uploaded %s (%s)', $user_id, str_replace('_',' ', $docType), $submissionCode);
    $ins = $pdo->prepare('INSERT INTO admin_notifications (submission_id, submission_code, user_id, doc_type, message) VALUES (?,?,?,?,?)');
    $ins->execute([(int)$sub['submission_id'], $submissionCode, $user_id, $docType, $msg]);

    echo json_encode(['success'=>true,'doc_type'=>$docType,'file_name'=>$newName,'size'=>$file['size'],'notified'=>true,'remaining'=>$remaining,'updated_status'=>$updatedStatus]);
} catch(Throwable $e){
    http_response_code(500);
    echo json_encode(['success'=>false,'error'=>'Server error','detail'=>$e->getMessage()]);
}
