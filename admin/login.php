<?php
// Admin-only login (no public links)
require __DIR__ . '/../config.php';
require app_path('conn.php');
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }

// If already logged in as admin, go straight to dashboard
if (!empty($_SESSION['user_logged_in']) && !empty($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1) {
    redirect('admin/admin.php');
}

$error = null;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (function_exists('verify_csrf_post')) { verify_csrf_post(); }

    $identifier = trim($_POST['identifier'] ?? ''); // admin_number only
    $password   = $_POST['password'] ?? '';

    if ($identifier === '' || $password === '') {
        $error = 'Please fill in all fields.';
    } else {
        // Strict: accept ONLY admin_number (no email-based login)
        // Reject if input looks like an email to avoid user confusion or enumeration
        if (strpos($identifier, '@') !== false) {
            $error = 'Invalid credentials.'; // generic
            $user = null;
        } else {
            $sql = "SELECT u.user_id, u.email, u.password, u.role, u.status, ap.admin_number 
                    FROM users u 
                    INNER JOIN admin_profiles ap ON u.user_id = ap.user_id 
                    WHERE ap.admin_number = ? LIMIT 1";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$identifier]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
        }

        if ($user && password_verify($password, $user['password'])) {
            // Only allow admins to log in here
            if (strtolower((string)$user['role']) !== 'admin') {
                // To avoid leaking role info, show generic error
                $error = 'Invalid credentials.';
            } else {
                // Optional: allow disabled admins to be blocked via status if needed
                if (isset($user['status']) && strtolower((string)$user['status']) === 'disabled') {
                    $error = 'This account is disabled.';
                } else {
                    if (function_exists('log_event')) { log_event('AUTH', 'Admin login success', ['user_id' => $user['user_id'], 'identifier' => $identifier]); }
                    session_regenerate_id(true);
                    $_SESSION['user_logged_in'] = true;
                    $_SESSION['user_id'] = $user['user_id'];
                    $_SESSION['email'] = $user['email'] ?? '';
                    $_SESSION['admin_number'] = $user['admin_number'] ?? $identifier;
                    $_SESSION['user_identifier'] = $user['admin_number'] ?? $user['email'];
                    $_SESSION['is_admin'] = 1;
                    $_SESSION['role'] = 'admin';
                    redirect('admin/admin.php');
                }
            }
        } else {
            if (function_exists('log_event')) { log_event('AUTH_FAIL', 'Admin login failed', ['identifier' => $identifier]); }
            $error = 'Invalid credentials.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - PUP e-IPMO</title>
    <meta name="robots" content="noindex, nofollow">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
    <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/login.css'); ?>">
    <style>
        body.login-page-body { background-color: #f8f9fa; }
        .login-card-custom { max-width: 420px; width: 100%; border-radius: 12px; }
        .login-logo { width: 72px; height: 72px; }
        .login-btn-custom { background-color: #800000; color: #fff; }
        .login-btn-custom:hover { background-color: #6b0000; color: #fff; }
    </style>
    <!-- No navbar on purpose to avoid discoverability -->
    <link rel="preload" as="image" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
    <meta http-equiv="X-Content-Type-Options" content="nosniff">
    <meta http-equiv="Referrer-Policy" content="no-referrer">
    <meta http-equiv="Permissions-Policy" content="camera=(), microphone=(), geolocation=()">
    <?php
    // Security headers should be sent via PHP header() BEFORE output, not as meta http-equiv; remove noisy ineffective meta directives.
    // If a global header injector (e.g., security_headers.php) is available, rely on it; else set minimal headers here.
    if (!headers_sent()) {
        header("X-Frame-Options: DENY");
        header("Frame-Options: DENY"); // legacy
        header("Referrer-Policy: no-referrer");
        header("X-Content-Type-Options: nosniff");
        header("Permissions-Policy: camera=(), microphone=(), geolocation=()");
        // Strict CSP: no inline scripts; allow required CDNs for bootstrap + fonts
        $csp = "default-src 'self'; font-src 'self' https://fonts.gstatic.com data:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data:; script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; object-src 'none'; frame-ancestors 'none'; base-uri 'self';";
        header("Content-Security-Policy: $csp");
    }
    ?>
    <?php /* Intentionally no links in app UI to this page */ ?>
    <!-- Honeypot field against bots -->
    <style> .hp-field { position: absolute; left: -9999px; top: -9999px; } </style>
    <?php $token = function_exists('csrf_token') ? csrf_token() : ''; ?>
    <?php // CSRF token will be set via external script to comply with CSP (no inline JS) ?>
    <meta name="csrf-token" content="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
    <noscript><style>.js-only{display:none!important}</style></noscript>
    <meta http-equiv="Cache-Control" content="no-store, no-cache, must-revalidate, max-age=0">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <!--
      Access: direct URL only (e.g., /admin/login.php). No visible links should point here.
    -->
</head>
<body class="login-page-body">
    <div class="container-fluid d-flex justify-content-center align-items-center login-container" style="min-height:100vh;">
        <div class="card shadow-sm p-4 login-card-custom">
            <div class="text-center mb-3">
                <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" class="login-logo">
            </div>
            <div class="text-center mb-3">
                <span class="fw-normal">Administrator Login</span>
            </div>
            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2 mb-3" role="alert"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
            <?php endif; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" autocomplete="off" novalidate>
                <?php if (function_exists('csrf_input')) { csrf_input(); } ?>
                <div class="hp-field">
                    <label>Leave this field empty</label>
                    <input type="text" name="website" value="">
                </div>
                <div class="mb-3 position-relative">
                    <input type="text" class="form-control rounded-pill ps-4 pe-5" id="identifier" name="identifier" placeholder="Admin ID" required style="border: 2px solid #222;">
                    <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor"><path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/><path d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/></svg>
                    </span>
                </div>
                <div class="mb-2 position-relative">
                    <input type="password" class="form-control rounded-pill ps-4 pe-5" id="password" name="password" placeholder="Password" required style="border: 2px solid #222;">
                    <button type="button" class="position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary bg-transparent border-0 toggle-password js-only" data-target="#password" aria-label="Show password" style="text-decoration:none; box-shadow:none;">
                        <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/><path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/></svg>
                        <svg class="icon-eye-off d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true"><path d="M13.359 11.238C15.06 9.87 16 8 16 8s-3-5.5-8-5.5a8.06 8.06 0 0 0-2.79.533l1.262 1.262A6.52 6.52 0 0 1 8 4.5c3.5 0 5.8 3.5 5.8 3.5a13.2 13.2 0 0 1-1.65 1.987l-0.791-.749z"/><path d="M3.35 4.354l.707-.708 9.9 9.9-.707.707-1.68-1.68A7.74 7.74 0 0 1 8 13.5C3 13.5 0 8 0 8a17.28 17.28 0 0 1 3.35-3.646zM1.2 8s2.3 3.5 6.8 3.5c.648 0 1.248-.08 1.8-.224L7.06 8.036A3 3 0 0 1 6.5 8a3 3 0 0 1 2.95-3l-1.55-1.55C7.5 3.32 7.26 3.3 7 3.3 2.5 3.3 1.2 8 1.2 8z"/></svg>
                    </button>
                </div>
                <button type="submit" class="btn w-100 login-btn-custom">Login</button>
            </form>
            <div class="text-center mt-3">
                <a href="<?php echo asset_url('index.php'); ?>" class="small">Back to Home</a>
            </div>
        </div>
    </div>
    <script src="<?php echo asset_url('javascript/login.js'); ?>" defer></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
</body>
<!-- No links to this page exist in the UI. Access is by direct URL only. -->
</html>
