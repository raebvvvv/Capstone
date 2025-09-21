<?php
require __DIR__ . '/../../conn.php'; // Use your PDO connection file

$success = '';
$error = '';

// Get code and email from URL
$code  = isset($_GET['code']) ? $_GET['code'] : '';
$email = isset($_GET['email']) ? $_GET['email'] : '';

if (!$code || !$email) {
    $error = "Invalid verification link.";
} else {
    // Find user with matching code and email, and not expired
    $stmt = $pdo->prepare("SELECT user_id, code_expires_at, status FROM users WHERE email = ? AND verification_code = ?");
    $stmt->execute([$email, $code]);
    $user = $stmt->fetch();

    if (!$user) {
        $error = "Invalid or expired verification link.";
    } elseif ($user['status'] === 'active') {
        $success = "Your email is already verified.";
    } elseif (strtotime($user['code_expires_at']) < time()) {
        $error = "Verification link has expired.";
    } else {
        // Mark email as verified
        $stmt = $pdo->prepare("UPDATE users SET status = 'active', email_verified_at = NOW(), verification_code = NULL, code_expires_at = NULL WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        $success = "Your email has been verified! You may now log in.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Email Verification | PUP e-IPMO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
    <main class="flex-grow-1 d-flex justify-content-center align-items-center py-4">
        <div class="card shadow p-4" style="max-width: 500px;">
            <h3 class="mb-4">Email Verification</h3>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php elseif ($success): ?>
                <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>
            <a href="login.php" class="btn btn-primary mt-3">Go to Login</a>
        </div>
    </main>
</body>
</html>