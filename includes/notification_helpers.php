<?php
// Notification helper: creates in-site notifications and sends email to users when submissions change
// Usage: require_once __DIR__ . '/includes/notification_helpers.php' (paths depend on caller location)

require_once __DIR__ . '/../config.php';
require_once app_path('conn.php');

/** Ensure notifications table exists and return void. Safe to call repeatedly. */
function ensure_user_notifications_table(PDO $pdo) {
    $pdo->exec("CREATE TABLE IF NOT EXISTS user_notifications (
        id INT AUTO_INCREMENT PRIMARY KEY,
        user_id INT NOT NULL,
        title VARCHAR(255) NOT NULL,
        message TEXT NOT NULL,
        meta TEXT NULL,
        is_read TINYINT(1) DEFAULT 0,
        deleted_at DATETIME NULL DEFAULT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        INDEX (user_id),
        INDEX (is_read)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");
}

function create_site_notification(PDO $pdo, int $user_id, string $title, string $message, $meta = null) {
    try {
        ensure_user_notifications_table($pdo);
        $metaJson = is_null($meta) ? null : json_encode($meta);
        // Dedupe: if we have a recent notification (within window) for same user + title + submission_id (if provided), update instead of inserting
        $dedupeMinutes = 60; // window to consider duplicates
        $submissionId = null;
        if (is_array($meta) && isset($meta['submission_id'])) { $submissionId = (int)$meta['submission_id']; }
        if ($submissionId) {
            $q = $pdo->prepare('SELECT id, meta, is_read FROM user_notifications WHERE user_id = ? AND title = ? AND created_at >= (NOW() - INTERVAL ? MINUTE) ORDER BY created_at DESC LIMIT 5');
            $q->execute([$user_id, $title, $dedupeMinutes]);
            $found = null;
            while ($r = $q->fetch(PDO::FETCH_ASSOC)) {
                $m = null;
                if (!empty($r['meta'])) {
                    $m = json_decode($r['meta'], true);
                }
                if (is_array($m) && isset($m['submission_id']) && ((int)$m['submission_id'] === $submissionId)) { $found = $r; break; }
            }
            if ($found) {
                // update existing record instead of inserting a new duplicate.
                // Preserve the user's read state: if the existing notification is already read, do not flip it back to unread.
                $existingIsRead = (int)($found['is_read'] ?? 0);
                if ($existingIsRead === 1) {
                    $up = $pdo->prepare('UPDATE user_notifications SET message = ?, meta = ?, created_at = NOW() WHERE id = ?');
                    $up->execute([$message, $metaJson, (int)$found['id']]);
                } else {
                    $up = $pdo->prepare('UPDATE user_notifications SET message = ?, meta = ?, is_read = 0, created_at = NOW() WHERE id = ?');
                    $up->execute([$message, $metaJson, (int)$found['id']]);
                }
                return true;
            }
        }

        $stmt = $pdo->prepare('INSERT INTO user_notifications (user_id, title, message, meta) VALUES (:uid, :t, :m, :meta)');
        $stmt->execute([':uid'=>$user_id, ':t'=>$title, ':m'=>$message, ':meta'=>$metaJson]);
        return true;
    } catch (Throwable $e) {
        if (function_exists('log_event')) log_event('NOTIF_ERROR', 'create_site_notification failed', ['err'=>substr($e->getMessage(),0,200),'user_id'=>$user_id]);
        return false;
    }
}

function send_user_email(PDO $pdo, int $user_id, string $subject, string $body_html, string $body_text = ''): bool {
    try {
        // Resolve email for user
        $st = $pdo->prepare('SELECT email FROM users WHERE user_id = ? LIMIT 1');
        $st->execute([$user_id]);
        $email = $st->fetchColumn();
        if (!$email) return false;

        // Load email config if present. Use include (not require) and validate the returned value
        // so a malformed email_config.php doesn't cause a fatal error and break JSON responses.
        $cfgPath = __DIR__ . '/../email_config.php';
        $emailConfig = null;
        if (is_file($cfgPath)) {
            $maybe = @include $cfgPath; // include to avoid fatal on parse error
            if (is_array($maybe)) {
                $emailConfig = $maybe;
            } else {
                if (function_exists('log_event')) log_event('EMAIL_CONFIG_INVALID', 'email_config.php did not return array', ['path'=>$cfgPath]);
                $emailConfig = null;
            }
        }

        // Prefer PHPMailer if available
        if (file_exists(__DIR__ . '/../PHPMailer/src/PHPMailer.php')) {
            require_once __DIR__ . '/../PHPMailer/src/PHPMailer.php';
            require_once __DIR__ . '/../PHPMailer/src/SMTP.php';
            require_once __DIR__ . '/../PHPMailer/src/Exception.php';
            $class = '\\PHPMailer\\PHPMailer\\PHPMailer';
            $mail = new $class(true);
            try {
                $mail->isSMTP();
                if (is_array($emailConfig) && isset($emailConfig['smtp'])) {
                    $smtp = $emailConfig['smtp'];
                    $mail->Host = $smtp['host'] ?? 'localhost';
                    $mail->SMTPAuth = !empty($smtp['username']);
                    if (!empty($smtp['username'])) $mail->Username = $smtp['username'];
                    if (!empty($smtp['password'])) $mail->Password = $smtp['password'];
                    if (!empty($smtp['encryption'])) {
                        $mail->SMTPSecure = $smtp['encryption'];
                    }
                    if (!empty($smtp['port'])) $mail->Port = (int)$smtp['port'];
                    $from = $smtp['from_email'] ?? ($smtp['username'] ?? 'no-reply@localhost');
                    $fromName = $smtp['from_name'] ?? 'No-reply';
                } else {
                    $mail->Host = 'localhost';
                    $mail->SMTPAuth = false;
                    $from = 'no-reply@localhost';
                    $fromName = 'No-reply';
                }
                $mail->setFrom($from, $fromName);
                $mail->addAddress($email);
                $mail->isHTML(true);
                $mail->Subject = $subject;
                $mail->Body = $body_html;
                if ($body_text) $mail->AltBody = $body_text;
                $mail->send();
                return true;
            } catch (Throwable $e) {
                if (function_exists('log_event')) log_event('NOTIF_EMAIL_FAIL', 'PHPMailer send failed', ['err'=>substr($e->getMessage(),0,200),'user_id'=>$user_id]);
                // fall through to false
            }
        }

        // Fallback to PHP mail() when PHPMailer not present. Keep simple headers.
        $headers = "MIME-Version: 1.0\r\n";
        $headers .= "Content-Type: text/html; charset=UTF-8\r\n";
        $fromHeader = 'no-reply@localhost';
        if (is_array($emailConfig) && isset($emailConfig['smtp']['from_email'])) {
            $fromHeader = $emailConfig['smtp']['from_email'];
        }
        $headers .= 'From: ' . $fromHeader . "\r\n";
        return @mail($email, $subject, $body_html, $headers);
    } catch (Throwable $e) {
        if (function_exists('log_event')) log_event('NOTIF_EMAIL_EXCEPTION', 'send_user_email exception', ['err'=>substr($e->getMessage(),0,200),'user_id'=>$user_id]);
        return false;
    }
}

function notify_submission_status_change(PDO $pdo, int $submission_id, string $new_status, ?string $old_status = null) {
    try {
        $s = $pdo->prepare('SELECT user_id, submission_code FROM submissions WHERE submission_id = ? LIMIT 1');
        $s->execute([$submission_id]);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        $uid = (int)$row['user_id'];
        $code = $row['submission_code'] ?? '';
        $title = 'Application status updated';
        $msg = "Your application " . ($code ?: "#{$submission_id}") . " status changed";
        if ($old_status) $msg .= " from {$old_status} to {$new_status}."; else $msg .= " to {$new_status}.";
        // site notification
        create_site_notification($pdo, $uid, $title, $msg, ['submission_id'=>$submission_id,'submission_code'=>$code,'new_status'=>$new_status,'old_status'=>$old_status]);
        // email
        $subj = "Application {$code} status: {$new_status}";
        $body = "<p>Dear user,</p><p>Your application <strong>" . htmlspecialchars($code) . "</strong> status has been updated to <strong>" . htmlspecialchars($new_status) . "</strong>.</p><p>Regards,<br/>Admissions Team</p>";
        send_user_email($pdo, $uid, $subj, $body);
        return true;
    } catch (Throwable $e) {
        if (function_exists('log_event')) log_event('NOTIF_ERROR', 'notify_submission_status_change failed', ['err'=>substr($e->getMessage(),0,200),'submission_id'=>$submission_id]);
        return false;
    }
}

function notify_documents_need_resubmission(PDO $pdo, int $submission_id, array $doc_types = [], string $comment = '') {
    try {
        $s = $pdo->prepare('SELECT user_id, submission_code FROM submissions WHERE submission_id = ? LIMIT 1');
        $s->execute([$submission_id]);
        $row = $s->fetch(PDO::FETCH_ASSOC);
        if (!$row) return false;
        $uid = (int)$row['user_id'];
        $code = $row['submission_code'] ?? '';
        $title = 'Documents require resubmission';
        $docList = $doc_types ? implode(', ', $doc_types) : 'one or more documents';
        $msg = "Your application " . ($code ?: "#{$submission_id}") . " requires resubmission of: {$docList}.";
        if ($comment) $msg .= ' Note: ' . $comment;
        create_site_notification($pdo, $uid, $title, $msg, ['submission_id'=>$submission_id, 'submission_code'=>$code, 'doc_types'=>$doc_types]);
        $subj = "Action required: Documents need resubmission for {$code}";
        $body = "<p>Dear user,</p><p>Your application <strong>" . htmlspecialchars($code) . "</strong> requires resubmission of the following documents: <strong>" . htmlspecialchars($docList) . "</strong>.</p>";
        if ($comment) $body .= "<p>Comment from admin: " . htmlspecialchars($comment) . "</p>";
        $body .= "<p>Please log in and upload the requested documents.</p><p>Regards,<br/>Admissions Team</p>";
        send_user_email($pdo, $uid, $subj, $body);
        return true;
    } catch (Throwable $e) {
        if (function_exists('log_event')) log_event('NOTIF_ERROR', 'notify_documents_need_resubmission failed', ['err'=>substr($e->getMessage(),0,200),'submission_id'=>$submission_id]);
        return false;
    }
}

?>
