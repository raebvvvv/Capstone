<?php
// forgot_password.php
// Page for students and employees to request a password reset
// No design changes, basic form only

require_once 'conn.php'; // adjust path if needed
$emailConfig = require 'email_config.php'; // load config as array
require_once 'PHPMailer/src/PHPMailer.php';
require_once 'PHPMailer/src/SMTP.php';
require_once 'PHPMailer/src/Exception.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $identifier = trim($_POST['identifier']);
    if ($identifier) {
        // Check if user exists (students, employees, admins)
        $stmt = $pdo->prepare("SELECT u.user_id, u.email
            FROM users u
            LEFT JOIN student_profiles sp ON sp.user_id = u.user_id
            LEFT JOIN employee_profiles ep ON ep.user_id = u.user_id
            LEFT JOIN admin_profiles ap ON ap.user_id = u.user_id
            WHERE u.email = ?
               OR sp.student_number = ?
               OR ep.employee_number = ?
               OR ap.admin_number = ?
            LIMIT 1");
        $stmt->execute([$identifier, $identifier, $identifier, $identifier]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            // User found, generate token and store
            $user_id = $row['user_id'];
            $token = bin2hex(random_bytes(32));
            $expiry = date('Y-m-d H:i:s', strtotime('+1 hour'));
            $update = $pdo->prepare("UPDATE users SET password_reset_token = ?, token_expiry = ? WHERE user_id = ?");
            $update->execute([$token, $expiry, $user_id]);
            // Send password reset email
            $mail = new PHPMailer(true);
            try {
                // Server settings from email_config.php
                $mail->isSMTP();
                $mail->Host = $emailConfig['smtp']['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $emailConfig['smtp']['username'];
                $mail->Password = $emailConfig['smtp']['password'];
                $mail->SMTPSecure = $emailConfig['smtp']['encryption'];
                $mail->Port = $emailConfig['smtp']['port'];

                // Recipients
                $mail->setFrom($emailConfig['smtp']['from_email'], $emailConfig['smtp']['from_name']);
                $mail->addAddress($row['email']);

                // Content
                $mail->isHTML(true);
                $mail->Subject = 'Password Reset Request';
                $reset_link = 'https://' . $_SERVER['HTTP_HOST'] . '/Capstone/reset_password.php?token=' . urlencode($token);
                $mail->Body = 'Click <a href="' . $reset_link . '">here</a> to reset your password. This link will expire in 1 hour.';

                $mail->send();
            } catch (Exception $e) {
                // Log error or handle as needed
            }
            $message = 'If your account exists, you will receive an email with reset instructions.';
        } else {
            $message = 'If your account exists, you will receive an email with reset instructions.';
        }
    } else {
        $message = 'Please enter your email, student/employee/admin number.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - PUP e-IPMO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="Photos/pup-logo.png">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body class="login-page-body">
    <nav class="navbar navbar-expand-lg bg-white border-bottom w-100">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="index.php">
                <img src="Photos/pup-logo.png" alt="PUP Logo" width="50" class="me-2">
                <span>PUP e-IPMO</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="User/Beforelogin/about.php">About Us</a></li>
                </ul>
            </div>
        </div>
    </nav>
    <div class="container-fluid d-flex justify-content-center align-items-center login-container">
        <div class="card shadow-sm p-4 login-card-custom">
            <div class="text-center mb-3">
                <img src="Photos/pup-logo.png" alt="PUP Logo" class="login-logo">
            </div>
            <div class="text-center mb-3">
                <span class="fw-normal student-login-text">Forgot Password</span>
            </div>
            <form method="post" action="">
                <div class="mb-3 position-relative">
                    <input type="text" class="form-control rounded-pill ps-4 pe-5" name="identifier" id="identifier" placeholder="Email or Username" required style="border: 2px solid #222;">
                    <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            <path d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/>
                        </svg>
                    </span>
                </div>
                <button type="submit" class="btn w-100 login-btn-custom">Request Reset</button>
            </form>
            <p class="mt-3 text-center text-danger"><?php echo htmlspecialchars($message); ?></p>
        </div>
    </div>
    <?php include __DIR__ . '/partials/standard_footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
</body>
</html>
