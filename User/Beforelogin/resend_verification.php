<?php
require __DIR__ . '/../../config.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../../PHPMailer/vendor/autoload.php';

$success = '';
$error = '';

// Prefill email from querystring if present
$email = '';
$prefill = strtolower(trim($_GET['email'] ?? ''));
if ($prefill && filter_var($prefill, FILTER_VALIDATE_EMAIL)) {
  $email = $prefill;
}
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $email = strtolower(trim($_POST['email'] ?? ''));
    if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Please enter a valid email address.';
    } else {
        // Find pending user by email
        $stmt = $pdo->prepare("SELECT user_id, status FROM users WHERE LOWER(TRIM(email)) = LOWER(TRIM(?)) LIMIT 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        if (!$user) {
            $error = 'No account found for that email.';
        } elseif (strtolower($user['status']) === 'active') {
            $error = 'This email is already verified. You can log in.';
        } else {
            try {
                // Generate a new code and extend expiry by 24 hours
                $verification_code = bin2hex(random_bytes(8));
                $code_expires_at = date('Y-m-d H:i:s', strtotime('+1 day'));
                $stmt = $pdo->prepare("UPDATE users SET verification_code = ?, code_expires_at = ? WHERE user_id = ?");
                $stmt->execute([$verification_code, $code_expires_at, $user['user_id']]);

                // Load SMTP config
                $smtp_config = null;
                $projectRoot = realpath(__DIR__ . '/../../');
                $cfgPath = $projectRoot . DIRECTORY_SEPARATOR . 'email_config.php';
                if ($projectRoot && is_file($cfgPath)) {
                    $email_config = require $cfgPath;
                    if (is_array($email_config) && isset($email_config['smtp'])) {
                        $smtp_config = $email_config['smtp'];
                    }
                }
                if (!$smtp_config) {
                    $get = function ($key, $default = null) {
                        if (class_exists('Environment') && method_exists('Environment', 'get')) {
                            return Environment::get($key, $default);
                        }
                        $val = getenv($key);
                        return ($val !== false && $val !== '') ? $val : $default;
                    };
                    $smtp_config = [
                        'host' => $get('SMTP_HOST', 'smtp.gmail.com'),
                        'port' => (int) $get('SMTP_PORT', '587'),
                        'username' => $get('SMTP_USERNAME'),
                        'password' => $get('SMTP_PASSWORD'),
                        'encryption' => $get('SMTP_ENCRYPTION', 'tls'),
                        'from_email' => $get('SMTP_FROM_EMAIL'),
                        'from_name' => $get('SMTP_FROM_NAME', 'PUP e-IPMO'),
                    ];
                }
                if (empty($smtp_config['host']) || empty($smtp_config['username']) || empty($smtp_config['password']) || empty($smtp_config['from_email'])) {
                    throw new RuntimeException('SMTP configuration missing.');
                }

                // Send email
                $mail = new PHPMailer(true);
                $mail->isSMTP();
                $mail->Host = $smtp_config['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $smtp_config['username'];
                $mail->Password = $smtp_config['password'];
                $mail->SMTPSecure = $smtp_config['encryption'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
                $mail->Port = $smtp_config['port'];

                $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);
                $mail->addAddress($email);
                // Optional: forward replies elsewhere
                // $mail->addReplyTo('ipmo@pup.edu.ph', 'PUP e-IPMO');

                // Build verification URL using configured BASE_URL to respect subfolder paths (e.g., /fix/Capstone)
                $verifyUrl = asset_url('User/Beforelogin/verify.php') . '?code=' . urlencode($verification_code) . '&email=' . urlencode($email);
                $mail->isHTML(true);
                $mail->Subject = 'Verify your email address (resend)';
                $mail->Body = "Hello,<br><br>Here is your verification link:<br><a href='$verifyUrl'>$verifyUrl</a><br><br>This link expires in 24 hours.";

                $mail->send();
                $success = 'A new verification email has been sent. Please check your inbox (or spam folder).';
            } catch (Throwable $e) {
                $error = 'Unable to resend verification email: ' . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Resend Verification | PUP e-IPMO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
  <main class="flex-grow-1 d-flex justify-content-center align-items-center py-4">
    <div class="card shadow p-4" style="max-width: 500px; width: 100%;">
      <h3 class="mb-3">Resend Verification Email</h3>
      <?php if ($error): ?>
        <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
      <?php elseif ($success): ?>
        <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
      <?php endif; ?>
      <form method="POST" class="mt-2">
        <div class="mb-3">
          <label for="email" class="form-label">Email address</label>
          <input type="email" name="email" id="email" class="form-control" value="<?php echo htmlspecialchars($email); ?>" required>
        </div>
        <button class="btn btn-primary" type="submit">Send</button>
        <a class="btn btn-link" href="login.php">Back to Login</a>
      </form>
    </div>
  </main>
</body>
</html>
