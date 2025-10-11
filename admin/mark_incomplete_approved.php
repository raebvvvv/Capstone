<?php
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
require_admin();
header('Content-Type: application/json');

// Fast response helper to flush JSON quickly and continue any follow-ups if needed.
function mia_respond_fast_and_detach(array $payload): void {
    if (!headers_sent()) {
        header('Content-Type: application/json');
    }
    echo json_encode($payload);
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        ignore_user_abort(true);
        if (function_exists('ob_get_length')) {
            $len = ob_get_length();
            if ($len !== false && !headers_sent()) {
                header('Content-Length: ' . $len);
            }
        }
        @ob_end_flush(); @flush();
    }
}

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['success' => false, 'error' => 'Method not allowed']);
        exit;
    }
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
    $remark    = trim((string)($payload['remark'] ?? ''));
    $comment   = trim((string)($payload['comment'] ?? ''));
    $affected  = $payload['affected_files'] ?? [];
    if(!is_array($affected)) { $affected = []; }
    // Support objects or simple strings/ids; extract doc_type if present
    $affectedDocTypes = [];
    foreach($affected as $af){
        if(is_array($af)){
            if(isset($af['doc_type'])) { $affectedDocTypes[] = (string)$af['doc_type']; }
            elseif(isset($af['type'])) { $affectedDocTypes[] = (string)$af['type']; }
        } else {
            $affectedDocTypes[] = (string)$af; // fallback
        }
    }

    if ($requestId === '') {
        echo json_encode(['success' => false, 'error' => 'Missing request_id']);
        exit;
    }

    if (strlen($remark) > 255) { $remark = substr($remark, 0, 255); }
    if (strlen($comment) > 1000) { $comment = substr($comment, 0, 1000); }

    $col = ctype_digit($requestId) ? 'submission_id' : 'submission_code';
    $val = ctype_digit($requestId) ? (int)$requestId : $requestId;
    $stmt = $pdo->prepare("SELECT submission_id, submission_code, status FROM submissions WHERE $col = ? LIMIT 1");
    $stmt->execute([$val]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'Submission not found']);
        exit;
    }

    // Keep status as 'approved' and update remarks only to reflect the flag.
    $storeRemark = $remark;
    if ($storeRemark === '' || strcasecmp($storeRemark, 'Remarks') === 0) {
        $storeRemark = $comment !== '' ? mb_substr($comment, 0, 255) : 'Needs Attention';
    }
    $upd = $pdo->prepare("UPDATE submissions SET remarks = :r, status_updated_at = NOW() WHERE submission_id = :id");
    $upd->execute([
        ':r' => $storeRemark,
        ':id' => (int)$row['submission_id']
    ]);

    // Ensure lean meta table exists & upsert (scope=approved)
    $pdo->exec("CREATE TABLE IF NOT EXISTS submission_incomplete_meta (
        submission_id INT NOT NULL,
        scope ENUM('pending','approved') NOT NULL,
        issue_label VARCHAR(150) DEFAULT NULL,
        admin_comment TEXT NULL,
        affected_doc_types TEXT NULL,
        updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
        PRIMARY KEY (submission_id, scope),
        CONSTRAINT fk_sim_submission FOREIGN KEY (submission_id) REFERENCES submissions(submission_id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
    // Reset verification flags for the selected document types (best-effort)
    if (!empty($affectedDocTypes)) {
        $placeholders = implode(',', array_fill(0, count($affectedDocTypes), '?'));
        $selDocs = $pdo->prepare("SELECT document_id FROM submission_documents WHERE submission_id=? AND doc_type IN ($placeholders)");
        $selDocs->execute(array_merge([ (int)$row['submission_id'] ], array_map('strval', $affectedDocTypes)));
        $docIds = $selDocs->fetchAll(PDO::FETCH_COLUMN, 0);
        if ($docIds) {
            $place2 = implode(',', array_fill(0, count($docIds), '?'));
            $reset = $pdo->prepare("UPDATE submission_documents SET verified=0, verified_by=NULL, verified_at=NULL WHERE document_id IN ($place2)");
            $reset->execute($docIds);
        }
    }

    // Store approved-scope meta so the student side can detect re-upload unlocks while staying Approved
    $issueLabel = ($remark !== '' && strtolower($remark) !== 'remarks') ? $remark : '';
    $affectedList = $affectedDocTypes ? implode('|', array_unique($affectedDocTypes)) : '';
    $metaStmt = $pdo->prepare("INSERT INTO submission_incomplete_meta (submission_id, scope, issue_label, admin_comment, affected_doc_types) VALUES (:sid, 'approved', :issue_label, :admin_comment, :affected)
        ON DUPLICATE KEY UPDATE scope='approved', issue_label=VALUES(issue_label), admin_comment=VALUES(admin_comment), affected_doc_types=VALUES(affected_doc_types)");
    $metaStmt->execute([
        ':sid' => (int)$row['submission_id'],
        ':issue_label' => $issueLabel !== '' ? $issueLabel : null,
        ':admin_comment' => $comment !== '' ? $comment : null,
        ':affected' => $affectedList !== '' ? $affectedList : null,
    ]);

    // Respond immediately for consistent speed
    mia_respond_fast_and_detach([
        'success' => true,
        'request_id' => $row['submission_code'],
        'scope' => 'approved',
        'echo_remark' => $remark,
        'comment_received' => ($comment !== ''),
        'affected_files' => $affectedDocTypes,
        'meta' => [
            'issue_label' => $issueLabel,
            'admin_comment' => $comment,
            'affected_doc_types' => $affectedList
        ]
    ]);
    // Background: notify user about resubmission requirements and alert admins feed
    try {
        require_once __DIR__ . '/../includes/notification_helpers.php';
        notify_documents_need_resubmission($pdo, (int)$row['submission_id'], $affectedDocTypes, $comment);
    } catch (Throwable $e) { /* ignore */ }
    try {
        // lightweight admin_notifications aggregation (best-effort)
        $pdo->exec("CREATE TABLE IF NOT EXISTS admin_notifications (
            id INT AUTO_INCREMENT PRIMARY KEY,
            type VARCHAR(50) NOT NULL,
            message TEXT NOT NULL,
            submission_id INT NULL,
            is_read TINYINT(1) DEFAULT 0,
            occurrence_count INT DEFAULT 1,
            meta JSON NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_type_created (type, created_at DESC)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");
        $anMsg = 'User needs to re-upload (Approved scope) for ' . ($row['submission_code'] ?? ('#'.$row['submission_id']));
        $ins = $pdo->prepare("INSERT INTO admin_notifications (type, message, submission_id, meta) VALUES ('reupload_request', :m, :sid, :meta)");
        $ins->execute([
            ':m' => $anMsg,
            ':sid' => (int)$row['submission_id'],
            ':meta' => json_encode(['scope'=>'approved','affected'=>$affectedDocTypes])
        ]);
    } catch (Throwable $e) { /* ignore admin notif errors */ }
    exit;
} catch (Throwable $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Exception', 'detail' => $e->getMessage()]);
}
