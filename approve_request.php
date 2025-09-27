<?php
// Secure JSON endpoint to approve a submission (uses PDO)
require __DIR__ . '/config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
header('Content-Type: application/json');
if (function_exists('verify_csrf_header')) { verify_csrf_header(); }
if (function_exists('require_admin')) { require_admin(); }

function respond_error($msg, array $extra = []) { echo json_encode(array_merge(['success'=>false,'error'=>$msg], $extra)); exit(); }

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) { respond_error('Invalid payload'); }

// request_id may be alphanumeric (e.g., SRID-2025-...), so keep as string
$requestId = trim((string)($input['request_id'] ?? ''));
$comment = trim((string)($input['comment'] ?? ''));
if (strlen($comment) > 1000) { $comment = substr($comment, 0, 1000); }

if ($requestId === '') { respond_error('Invalid request'); }

try {
    // Determine identifier column: numeric -> submission_id, else submission_code
    $idCol = ctype_digit($requestId) ? 'submission_id' : 'submission_code';
    // Step 1: update status + reviewer (no remarks logic) to minimize placeholder complexity
    $sqlStatus = "UPDATE submissions
                  SET status = 'approved',
                      reviewer_id = :rid,
                      reviewed_at = NOW(),
                      status_updated_at = NOW()
                  WHERE $idCol = :id";
    $stmtStatus = $pdo->prepare($sqlStatus);
    if(!$stmtStatus){
        $info = method_exists($pdo,'errorInfo') ? implode(' | ', array_filter($pdo->errorInfo() ?: [])) : 'prepare failed';
        respond_error('Prepare failed', ['phase'=>'prepare-status','detail'=>$info]);
    }
    $okStatus = $stmtStatus->execute([
        ':rid' => (int)($_SESSION['user_id'] ?? 0),
        ':id' => ctype_digit($requestId) ? (int)$requestId : $requestId
    ]);
    if(!$okStatus){
        $errInfo = $stmtStatus->errorInfo();
        $safeMsg = isset($errInfo[2]) ? substr($errInfo[2],0,200) : 'DB execute error';
        respond_error('Execute failed', ['phase'=>'execute-status','detail'=>$safeMsg]);
    }
    $affected = (int)$stmtStatus->rowCount();

    // Step 2: update remarks only if a new comment was provided
    if($comment !== ''){
        $sqlRemarks = "UPDATE submissions SET remarks = :remarks WHERE $idCol = :id";
        $stmtRemarks = $pdo->prepare($sqlRemarks);
        if(!$stmtRemarks){
            $info = method_exists($pdo,'errorInfo') ? implode(' | ', array_filter($pdo->errorInfo() ?: [])) : 'prepare failed';
            respond_error('Prepare failed', ['phase'=>'prepare-remarks','detail'=>$info]);
        }
        $okRemarks = $stmtRemarks->execute([
            ':remarks' => $comment,
            ':id' => ctype_digit($requestId) ? (int)$requestId : $requestId
        ]);
        if(!$okRemarks){
            $errInfo = $stmtRemarks->errorInfo();
            $safeMsg = isset($errInfo[2]) ? substr($errInfo[2],0,200) : 'DB execute error';
            respond_error('Execute failed', ['phase'=>'execute-remarks','detail'=>$safeMsg]);
        }
    }
    if ($affected === 0) {
        // Check if row already in approved state -> treat as idempotent success
        $checkSql = "SELECT status FROM submissions WHERE $idCol = :id LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        $checkStmt->execute([':id' => ctype_digit($requestId) ? (int)$requestId : $requestId]);
        $current = $checkStmt->fetchColumn();
        if ($current && strtolower($current) === 'approved') {
            echo json_encode(['success'=>true,'updated'=>0,'idempotent'=>true,'id_column'=>$idCol]);
            return;
        }
        respond_error('No row affected', ['phase'=>'rowcount','id_column'=>$idCol,'request_id'=>$requestId]);
    }

    if (function_exists('log_event')) { log_event('APPROVE_REQUEST', 'Request approved', ['request_id' => $requestId, 'rows'=>$affected, 'id_column'=>$idCol, 'comment_set'=> ($comment !== '') ]); }
    echo json_encode(['success' => true, 'updated' => $affected, 'id_column'=>$idCol, 'comment_set'=> ($comment !== '')]);
} catch (Throwable $e) {
    $msg = substr($e->getMessage(),0,200);
    if (function_exists('log_event')) { log_event('DB_ERROR', 'Approve exception', ['err' => $msg, 'request_id' => $requestId]); }
    respond_error('Exception thrown', ['phase'=>'exception','detail'=>$msg]);
}
