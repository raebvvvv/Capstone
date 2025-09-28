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
    $remark = trim((string)($payload['remark'] ?? ''));
    $comment = trim((string)($payload['comment'] ?? ''));
    $fileIds = $payload['file_ids'] ?? [];
    if (!is_array($fileIds)) { $fileIds = []; }
    $affectedDocTypes = $payload['affected_doc_types'] ?? [];
    if(!is_array($affectedDocTypes)) { $affectedDocTypes = []; }

    if ($requestId === '') {
        echo json_encode(['success' => false, 'error' => 'Missing request_id']);
        exit;
    }

    if (strlen($remark) > 255) { $remark = substr($remark,0,255); }
    if (strlen($comment) > 1000) { $comment = substr($comment,0,1000); }

    $isNumeric = ctype_digit($requestId);
    $col = $isNumeric ? 'submission_id' : 'submission_code';
    $stmt = $pdo->prepare("SELECT submission_id, status, submission_code FROM submissions WHERE $col = ? LIMIT 1");
    $stmt->execute([$isNumeric ? (int)$requestId : $requestId]);
    $sub = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$sub) {
        echo json_encode(['success' => false, 'error' => 'Submission not found']);
        exit;
    }
    $sid = (int)$sub['submission_id'];

    // IMPORTANT: Create meta table BEFORE starting an explicit transaction.
    // MySQL/MariaDB executes implicit commits around DDL; doing DDL inside a transaction
    // then calling commit() can throw "There is no active transaction" leading to the
    // front-end seeing {success:false,error:"Exception"} even though changes persisted.
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

    // Now start transaction for the mutating DML only
    $pdo->beginTransaction();

    // Prepare internal combined note (not persisted as display remark now)
    $internalNote = '';
    if ($remark !== '' && $comment !== '') {
        $internalNote = $remark . ' - ' . $comment;
    } elseif ($remark !== '') {
        $internalNote = $remark;
    } elseif ($comment !== '') {
        $internalNote = $comment;
    }

    // Display remark for user interface requirement: standardized default pending text
    $displayRemark = 'For Evaluation';

    $upd = $pdo->prepare("UPDATE submissions SET status = 'pending', remarks = ?, status_updated_at = NOW() WHERE submission_id = ?");
    $upd->execute([$displayRemark, $sid]);

    // Flag selected documents for re-upload by resetting verified fields (could add a dedicated flag column later)
    $updatedDocs = 0;
    if ($fileIds) {
        $placeholders = implode(',', array_fill(0, count($fileIds), '?'));
        $check = $pdo->prepare("SELECT document_id FROM submission_documents WHERE submission_id = ? AND document_id IN ($placeholders)");
        $check->execute(array_merge([$sid], array_map('intval',$fileIds)));
        $validIds = $check->fetchAll(PDO::FETCH_COLUMN,0);
        if ($validIds) {
            $place2 = implode(',', array_fill(0, count($validIds), '?'));
            $reset = $pdo->prepare("UPDATE submission_documents SET verified = 0, verified_by = NULL, verified_at = NULL WHERE document_id IN ($place2)");
            $reset->execute($validIds);
            $updatedDocs = $reset->rowCount();
        }
    }

    // Upsert meta (scope = pending)
    $issueLabel = ($remark !== '' && strtolower($remark) !== 'remarks') ? $remark : '';
    $affectedList = $affectedDocTypes ? implode('|', array_unique(array_map('strval',$affectedDocTypes))) : '';
    $metaStmt = $pdo->prepare("INSERT INTO submission_incomplete_meta (submission_id, scope, issue_label, admin_comment, affected_doc_types) VALUES (:sid, 'pending', :issue_label, :admin_comment, :affected)
        ON DUPLICATE KEY UPDATE scope='pending', issue_label=VALUES(issue_label), admin_comment=VALUES(admin_comment), affected_doc_types=VALUES(affected_doc_types)");
    $metaStmt->execute([
        ':sid' => $sid,
        ':issue_label' => $issueLabel !== '' ? $issueLabel : null,
        ':admin_comment' => $comment !== '' ? $comment : null,
        ':affected' => $affectedList !== '' ? $affectedList : null,
    ]);

    if($pdo->inTransaction()) { $pdo->commit(); }

    echo json_encode([
        'success' => true,
        'submission_id' => $sid,
        'request_id' => $sub['submission_code'],
        'updated_docs' => $updatedDocs,
        'remarks' => $displayRemark,
        'note_saved' => ($internalNote !== ''),
        'internal_note' => $internalNote,
        'meta' => [
            'issue_label' => $issueLabel,
            'admin_comment' => $comment,
            'affected_doc_types' => $affectedList
        ]
    ]);
} catch (Throwable $e) {
    if (isset($pdo) && $pdo instanceof PDO && $pdo->inTransaction()) { $pdo->rollBack(); }
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Exception', 'detail' => $e->getMessage()]);
}
