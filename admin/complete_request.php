<?php
// Robust completion endpoint (mirrors structured approach of approve_request)
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
header('Content-Type: application/json');
if (function_exists('verify_csrf_header')) { verify_csrf_header(); }
if (function_exists('require_admin')) { require_admin(); }

function complete_respond_error(string $msg, array $extra = [], int $code = 400): void {
    if (!headers_sent()) http_response_code($code);
    echo json_encode(array_merge(['success'=>false,'error'=>$msg], $extra));
    exit();
}

$raw = file_get_contents('php://input');
$input = json_decode($raw, true);
if (!is_array($input)) complete_respond_error('Invalid payload', ['phase'=>'payload']);

$requestId = trim((string)($input['request_id'] ?? ''));
$comment   = trim((string)($input['comment'] ?? ''));
if (strlen($comment) > 1000) { $comment = substr($comment, 0, 1000); }
if ($requestId === '') complete_respond_error('Missing request_id', ['phase'=>'validation']);

try {
    $idCol = ctype_digit($requestId) ? 'submission_id' : 'submission_code';

    // Prepare update
    // Use NULLIF to avoid repeating the same named placeholder twice, which can
    // trigger SQLSTATE[HY093] with native prepared statements.
    // Resolve admin profile_id to set reviewer_id as the completer
    $adminProfileId = null;
    try {
        $uid = (int)($_SESSION['user_id'] ?? 0);
        $anum = trim((string)($_SESSION['admin_number'] ?? ''));
        if ($uid > 0) {
            if ($anum !== '') {
                $p = $pdo->prepare("SELECT profile_id FROM admin_profiles WHERE user_id = ? AND admin_number = ? LIMIT 1");
                if ($p && $p->execute([ $uid, $anum ])) {
                    $tmp = $p->fetchColumn();
                    if ($tmp !== false) { $adminProfileId = (int)$tmp; }
                }
            }
            if ($adminProfileId === null) {
                $p = $pdo->prepare("SELECT profile_id FROM admin_profiles WHERE user_id = ? ORDER BY profile_id DESC LIMIT 1");
                if ($p && $p->execute([ $uid ])) {
                    $tmp = $p->fetchColumn();
                    if ($tmp !== false) { $adminProfileId = (int)$tmp; }
                }
            }
        }
    } catch (Throwable $e) { $adminProfileId = null; }

    if (function_exists('log_event')) { log_event('COMPLETE_RESOLVE_REVIEWER', 'Resolved reviewer for completion (JSON endpoint)', [
        'uid' => (int)($_SESSION['user_id'] ?? 0),
        'admin_number' => (string)($_SESSION['admin_number'] ?? ''),
        'profile_id' => $adminProfileId,
        'request_id' => $requestId,
    ]); }
    $sql = "UPDATE submissions
            SET status='completed',
                remarks = NULLIF(:remark, ''),
                reviewer_id = :rid,
                status_updated_at = NOW()
            WHERE $idCol = :id"; // completed_by & completed_at removed from schema; reviewer_id reflects completer
    $stmt = $pdo->prepare($sql);
    if(!$stmt){
        $info = method_exists($pdo,'errorInfo') ? implode(' | ', array_filter($pdo->errorInfo() ?: [])) : 'prepare failed';
        complete_respond_error('Prepare failed', ['phase'=>'prepare','detail'=>$info]);
    }
    $ok = $stmt->execute([
        ':remark' => $comment,
        ':rid' => $adminProfileId,
        ':id' => ctype_digit($requestId) ? (int)$requestId : $requestId
    ]);
    if(!$ok){
        $errInfo = $stmt->errorInfo();
        $safeMsg = isset($errInfo[2]) ? substr($errInfo[2],0,200) : 'DB execute error';
        complete_respond_error('Execute failed', ['phase'=>'execute','detail'=>$safeMsg]);
    }
    $affected = (int)$stmt->rowCount();

    if ($affected === 0) {
        // Determine current state for idempotency
        $checkSql = "SELECT status, remarks FROM submissions WHERE $idCol = :id LIMIT 1";
        $checkStmt = $pdo->prepare($checkSql);
        if($checkStmt && $checkStmt->execute([':id'=> ctype_digit($requestId) ? (int)$requestId : $requestId])){
            $row = $checkStmt->fetch();
            if($row){
                if(strtolower($row['status']) === 'completed'){
                    echo json_encode(['success'=>true,'updated'=>0,'idempotent'=>true,'id_column'=>$idCol,'phase'=>'idempotent']);
                    return;
                }
                complete_respond_error('No row affected', ['phase'=>'rowcount','current_status'=>$row['status'],'id_column'=>$idCol]);
            } else {
                complete_respond_error('Request not found', ['phase'=>'not-found','id_column'=>$idCol]);
            }
        } else {
            complete_respond_error('Rowcount diagnostic failed', ['phase'=>'diagnostic']);
        }
    }

    if (function_exists('log_event')) { log_event('COMPLETE_REQUEST', 'Request completed', ['request_id'=>$requestId,'rows'=>$affected,'id_column'=>$idCol,'comment_set'=>($comment!=='')]); }

    // Notify applicant about completion
    try {
        // Resolve numeric submission_id if necessary
        $numericId = ctype_digit($requestId) ? (int)$requestId : null;
        if (!$numericId) {
            $tmp = $pdo->prepare('SELECT submission_id FROM submissions WHERE submission_code = ? LIMIT 1');
            $tmp->execute([ $requestId ]);
            $numericId = (int)$tmp->fetchColumn();
        }
        if ($numericId) {
            require_once __DIR__ . '/../includes/notification_helpers.php';
            notify_submission_status_change($pdo, $numericId, 'completed');
        }
    } catch (Throwable $e) { if (function_exists('log_event')) log_event('NOTIF_HOOK_FAIL','complete notify failed', ['err'=>substr($e->getMessage(),0,200)]); }

    echo json_encode(['success'=>true,'updated'=>$affected,'id_column'=>$idCol,'comment_set'=>($comment!=='')]);
} catch(Throwable $e){
    $msg = substr($e->getMessage(),0,200);
    if (function_exists('log_event')) { log_event('DB_ERROR', 'Complete exception', ['err'=>$msg,'request_id'=>$requestId]); }
    complete_respond_error('Exception thrown', ['phase'=>'exception','detail'=>$msg], 500);
}
