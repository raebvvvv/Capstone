<?php
// Centralized secure bootstrap & logging
require __DIR__ . '/security_bootstrap.php';
secure_bootstrap();
require 'conn.php';

// Create CSRF token if not set
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Initialize brute force tracking structure in session
if (!isset($_SESSION['login_attempts'])) {
    $_SESSION['login_attempts'] = ['count' => 0, 'last_attempt' => 0, 'lock_until' => 0];
}

$lockDurationSeconds = 300; // 5 minutes lock after too many attempts
$maxAttempts = 5; // threshold within time window
$attemptWindowSeconds = 900; // 15 minutes window to evaluate attempts

if (time() < $_SESSION['login_attempts']['lock_until']) {
    $error = 'Too many failed attempts. Please try again later.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($error)) {
    // CSRF validation
    if (!isset($_POST['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
        $error = 'Invalid request.'; // Generic message
    } else {
        $student_number = trim($_POST['student_number'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($student_number === '' || $password === '') {
            $error = 'Invalid credentials.'; // Generic to avoid enumeration
        } else {
            // Reset attempts window if outside time window
            if (time() - $_SESSION['login_attempts']['last_attempt'] > $attemptWindowSeconds) {
                $_SESSION['login_attempts']['count'] = 0;
            }

            $_SESSION['login_attempts']['last_attempt'] = time();

            // Prepare and execute lookup (student_number assumed unique)
            $query = 'SELECT user_id, username, student_number, email, password, is_admin, status FROM users WHERE student_number = ? LIMIT 1';
            if ($stmt = $conn->prepare($query)) {
                $stmt->bind_param('s', $student_number);
                $stmt->execute();
                $result = $stmt->get_result();
                $user = $result->fetch_assoc();
                $stmt->close();
            } else {
                // Fail closed if DB error (avoid detailed leakage)
                $user = null;
            }

            $loginOk = false;
            if ($user) {
                // Verify password (constant-time internally)
                if (password_verify($password, $user['password'])) {
                    // Optionally rehash if algorithm updated
                    if (password_needs_rehash($user['password'], PASSWORD_DEFAULT)) {
                        if ($rehashStmt = $conn->prepare('UPDATE users SET password = ? WHERE user_id = ?')) {
                            $newHash = password_hash($password, PASSWORD_DEFAULT);
                            $rehashStmt->bind_param('si', $newHash, $user['user_id']);
                            $rehashStmt->execute();
                            $rehashStmt->close();
                        }
                    }
                    // Check active status (avoid differentiating message). Assuming status 'active'.
                    if (isset($user['status']) && strtolower($user['status']) !== 'pending') {
                        $loginOk = true;
                    }
                }
            }

            if ($loginOk) {
                // Reset attempts
                $_SESSION['login_attempts'] = ['count' => 0, 'last_attempt' => time(), 'lock_until' => 0];
                session_regenerate_id(true); // Prevent fixation
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['student_number'] = $user['student_number'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = $user['is_admin'];

                // Rotate CSRF token after privilege escalation
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));

                log_event('LOGIN_SUCCESS', 'User logged in', ['user_id' => $user['user_id']]);
                $destination = $user['is_admin'] ? 'admin/admin.php' : 'after-landing.php';
                header('Location: ' . $destination);
                exit();
            } else {
                // Increment attempts on failure
                $_SESSION['login_attempts']['count']++;
                if ($_SESSION['login_attempts']['count'] >= $maxAttempts) {
                    $_SESSION['login_attempts']['lock_until'] = time() + $lockDurationSeconds;
                }
                // Small delay to slow brute force
                usleep(300000); // 0.3s
                log_event('LOGIN_FAIL', 'Failed login attempt', ['student_number' => $student_number]);
                $error = 'Invalid credentials.'; // Generic message
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
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css?v=2">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <div class="container">
        <a href="index.php"><i class="fa fa-home" style="font-size:48px;color:blue;"></i></a>
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php" autocomplete="off" novalidate>
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">
            <div class="mb-3">
                <label for="student_number" class="form-label">Student Number</label>
                <input type="text" class="form-control" id="student_number" name="student_number" required autocomplete="username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required autocomplete="current-password">
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <a href="register.php"><button class="btn btn-secondary mt-2">Register</button></a>
    </div>
 
</body>
</html>