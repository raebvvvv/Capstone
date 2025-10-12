<?php
// reset_password.php
// Form for users to enter a new password after clicking the reset link
// Bootstrap config and DB
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/conn.php';

$message = '';
$show_form = false;
$current_token = '';

if (isset($_GET['token'])) {
    $token = $_GET['token'];
    $current_token = $token;
    // Check if token exists and is valid
    $stmt = $pdo->prepare("SELECT user_id, token_expiry FROM users WHERE password_reset_token = ? LIMIT 1");
    $stmt->execute([$token]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        if (strtotime($row['token_expiry']) > time()) {
            $show_form = true;
            $user_id = $row['user_id'];
        } else {
            $message = 'This password reset link has expired.';
        }
    } else {
        $message = 'Invalid password reset link.';
    }
} else {
    $message = 'No password reset token provided.';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['new_password'], $_POST['confirm_password'], $_POST['token'])) {
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $post_token = $_POST['token'];

    // Server-side validation: match and policy
    $errors = [];
    if ($new_password !== $confirm_password) {
        $errors[] = 'Passwords do not match.';
    }
    if (strlen($new_password) < 12) {
        $errors[] = 'Password must be at least 12 characters.';
    }
    if (!preg_match('/[A-Z]/', $new_password)) {
        $errors[] = 'Password must contain at least one uppercase letter.';
    }
    if (!preg_match('/[a-z]/', $new_password)) {
        $errors[] = 'Password must contain at least one lowercase letter.';
    }
    if (!preg_match('/[0-9]/', $new_password)) {
        $errors[] = 'Password must contain at least one number.';
    }
    if (!preg_match('/[^a-zA-Z0-9]/', $new_password)) {
        $errors[] = 'Password must contain at least one special character.';
    }

    if (!empty($errors)) {
        $message = implode(' ', $errors);
        $show_form = true;
        $current_token = $post_token;
    } else {
        // Verify token is still valid and not expired, and bind update to token (prevents user_id tampering)
        $stmt = $pdo->prepare("SELECT user_id, token_expiry FROM users WHERE password_reset_token = ? LIMIT 1");
        $stmt->execute([$post_token]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row && strtotime($row['token_expiry']) > time()) {
            $hashed = password_hash($new_password, PASSWORD_DEFAULT);
            $update = $pdo->prepare("UPDATE users SET password = ?, password_reset_token = NULL, token_expiry = NULL WHERE password_reset_token = ?");
            $update->execute([$hashed, $post_token]);
            $message = 'Your password has been reset successfully.';
            $show_form = false;
            $current_token = '';
        } else {
            $message = 'This password reset link is invalid or has expired.';
            $show_form = false;
            $current_token = '';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - PUP e-IPMO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/login.css'); ?>">
</head>
<body class="login-page-body">
    <nav class="navbar navbar-expand-lg bg-white border-bottom w-100">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="<?php echo asset_url('index.php'); ?>">
                <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" width="50" class="me-2">
                <span>PUP e-IPMO</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <span>Reset Password Page</span>
        </div>
    </nav>
    <div class="container-fluid d-flex justify-content-center align-items-center login-container">
        <div class="card shadow-sm p-4 login-card-custom">
            <div class="text-center mb-3">
                <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" class="login-logo">
            </div>
            <div class="text-center mb-3">
                <span class="fw-normal student-login-text">Reset Password</span>
            </div>
            <?php if ($show_form): ?>
            <form method="post" action="" id="resetForm">
                <?php if (function_exists('csrf_input')) { csrf_input(); } ?>
                <input type="hidden" name="token" value="<?php echo htmlspecialchars($current_token); ?>">
                <div class="mb-3 position-relative">
                    <input type="password" class="form-control rounded-pill ps-4 pe-5" name="new_password" id="new_password" placeholder="New Password" required style="border: 2px solid #222; position:relative; z-index:1;">
                    <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary" id="toggleNew" aria-label="Toggle password visibility" style="z-index:9999; cursor:pointer; pointer-events:auto; background:transparent; border:none;">
                        <svg id="eyeNew" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" style="pointer-events:none; display:block;">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
                            <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
                        </svg>
                    </button>
                </div>
                <div class="mb-3 position-relative">
                    <input type="password" class="form-control rounded-pill ps-4 pe-5" name="confirm_password" id="confirm_password" placeholder="Confirm Password" required style="border: 2px solid #222; position:relative; z-index:1;">
                    <button type="button" class="btn btn-link position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary" id="toggleConfirm" aria-label="Toggle confirm password visibility" style="z-index:9999; cursor:pointer; pointer-events:auto; background:transparent; border:none;">
                        <svg id="eyeConfirm" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" style="pointer-events:none; display:block;">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
                            <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
                        </svg>
                    </button>
                </div>

                <div id="passwordRules" class="mb-3 small text-start">
                    <p class="mb-1">Password must contain:</p>
                    <ul class="mb-0" style="list-style: none; padding-left: 0;">
                        <li id="ruleLength">• 12 or more characters</li>
                        <li id="ruleUpper">• At least one uppercase letter (A-Z)</li>
                        <li id="ruleLower">• At least one lowercase letter (a-z)</li>
                        <li id="ruleNumber">• At least one number (0-9)</li>
                        <li id="ruleSpecial">• At least one special character (!@#$%^&*)</li>
                        <li id="ruleMatch">• Passwords must match</li>
                    </ul>
                </div>

                <button type="submit" class="btn w-100 login-btn-custom" id="submitBtn">Reset Password</button>
            </form>
            <?php endif; ?>
            <p class="mt-3 text-center text-danger"><?php echo htmlspecialchars($message); ?></p>
        </div>
    </div>
    <?php include __DIR__ . '/partials/standard_footer.php'; ?>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>
    <script nonce="<?php echo SecurityHeaders::getCSPNonce(); ?>">
    (function () {
        // init function runs immediately if DOM is ready, or on DOMContentLoaded
        function initResetPassword() {
            // Helper to safely get element by id
            const $ = (id) => document.getElementById(id);

            // Toggle password visibility
            function togglePassword(inputId, eyeSvgId) {
                const input = $(inputId);
                const eye = $(eyeSvgId);
                if (!input) return;
                input.type = input.type === 'password' ? 'text' : 'password';
                if (eye) eye.style.opacity = input.type === 'text' ? '0.6' : '1';
            }

            const toggleNewBtn = $('toggleNew');
            const toggleConfirmBtn = $('toggleConfirm');
            if (toggleNewBtn) toggleNewBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                try { togglePassword('new_password', 'eyeNew'); } catch (err) { console.error(err); }
            });
            if (toggleConfirmBtn) toggleConfirmBtn.addEventListener('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                try { togglePassword('confirm_password', 'eyeConfirm'); } catch (err) { console.error(err); }
            });

            // Password rules validation
            const newInput = $('new_password');
            const confirmInput = $('confirm_password');
            const submitBtn = $('submitBtn');

            // If inputs are not present, nothing to do (prevents runtime errors when form not shown)
            if (!newInput || !confirmInput || !submitBtn) return;

            const ruleLength = $('ruleLength');
            const ruleUpper = $('ruleUpper');
            const ruleLower = $('ruleLower');
            const ruleNumber = $('ruleNumber');
            const ruleSpecial = $('ruleSpecial');
            const ruleMatch = $('ruleMatch');

            function checkRules() {
                const val = newInput.value || '';
                const matchVal = confirmInput.value || '';
                const okLength = val.length >= 12;
                const okUpper = /[A-Z]/.test(val);
                const okLower = /[a-z]/.test(val);
                const okNumber = /[0-9]/.test(val);
                const okSpecial = /[^a-zA-Z0-9]/.test(val);
                const okMatch = val === matchVal && val.length > 0;

                if (ruleLength) ruleLength.style.color = okLength ? 'green' : '#6c757d';
                if (ruleUpper) ruleUpper.style.color = okUpper ? 'green' : '#6c757d';
                if (ruleLower) ruleLower.style.color = okLower ? 'green' : '#6c757d';
                if (ruleNumber) ruleNumber.style.color = okNumber ? 'green' : '#6c757d';
                if (ruleSpecial) ruleSpecial.style.color = okSpecial ? 'green' : '#6c757d';
                if (ruleMatch) ruleMatch.style.color = okMatch ? 'green' : '#6c757d';

                // Enable submit only when all rules pass
                const allOk = okLength && okUpper && okLower && okNumber && okSpecial && okMatch;
                submitBtn.disabled = !allOk;
            }

            newInput.addEventListener('input', checkRules);
            confirmInput.addEventListener('input', checkRules);

            // Initialize state
            submitBtn.disabled = true;
            checkRules();
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initResetPassword);
        } else {
            // DOM already ready
            initResetPassword();
        }
    })();
    </script>
</body>
</html>
