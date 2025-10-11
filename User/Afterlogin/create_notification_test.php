<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
require_once __DIR__ . '/../../includes/notification_helpers.php';

header('Content-Type: application/json');
if (empty($_SESSION['user_id'])) {
    echo json_encode(['success'=>false,'error'=>'not_authenticated']); exit;
}
$user_id = (int)$_SESSION['user_id'];
try {
    $title = 'Test notification from user panel';
    $msg = 'This is a test notification created at ' . date('Y-m-d H:i:s');
    $meta = ['test'=>true];
    // optional submission_id
    if (!empty($_POST['submission_id'])) $meta['submission_id'] = (int)$_POST['submission_id'];
    $sendEmail = !empty($_POST['send_email']) && $_POST['send_email'] === '1';
    $ok = create_site_notification($pdo, $user_id, $title, $msg, $meta);
    if ($sendEmail) {
        // best-effort: send email too
        send_user_email($pdo, $user_id, $title, '<p>' . htmlspecialchars($msg) . '</p>');
    }
    echo json_encode(['success'=>true,'created'=>$ok]);
} catch (Throwable $e) {
    if (function_exists('log_event')) log_event('NOTIF_TEST_FAIL', 'create test notification failed', ['err'=>substr($e->getMessage(),0,200),'user_id'=>$user_id]);
    echo json_encode(['success'=>false,'error'=>'exception']);
}
