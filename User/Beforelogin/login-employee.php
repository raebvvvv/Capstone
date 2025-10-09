<?php
// Unified bootstrap: loads config (paths), security (session hardening), DB connection
require __DIR__ . '/../../config.php';
require app_path('conn.php');
// secure_bootstrap already invoked inside config.php if available.
// If not for some reason, call conditionally:
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_number = trim(htmlspecialchars($_POST['student_number']));
    $password = $_POST['password'];

    if (!empty($student_number) && !empty($password)) {
        $query = "SELECT user_id, student_number, email, password, role, status FROM users WHERE student_number = ?";
        $stmt = $pdo->prepare($query);
        $stmt->execute([$student_number]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] == 'pending' && $user['role'] !== 'admin') {
                $error = "Please wait for the confirmation of your account.";
            } else {
                // Only allow employees (and admins) to use this login page
                $roleLower = strtolower($user['role']);
                if (!in_array($roleLower, ['employee','admin'], true)) {
                    $error = 'This login is only for employees. Please use the Student Login page.';
                } else {
                session_regenerate_id(true); // Security: Prevent session fixation attacks
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['student_number'] = $user['student_number'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = ($user['role'] === 'admin') ? 1 : 0; // Set admin status
                $_SESSION['role'] = $user['role']; // Persist role

                if ($user['role'] === 'admin') {
                    // Redirect administrators to the admin dashboard
                    redirect('admin/admin.php');
                } else {
                    // Redirect regular users to unified index page (dynamic content based on session)
                    redirect('index.php');
                }
                exit();
                }
            }
        } else {
            $error = "Invalid student number or password.";
        }
    } else {
        $error = "Please fill in all fields.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - PUP e-IPMO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
    <link rel="stylesheet" href="<?php echo asset_url('css/login.css'); ?>">
</head>
<body class="login-page-body">
    <!-- Navbar (matches index.php) -->
        <nav class="navbar navbar-expand-lg bg-white border-bottom w-100">
            <div class="container">
                <a class="navbar-brand d-flex align-items-center" href="<?php echo asset_url('index.php'); ?>">
                    <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" width="50" class="me-2">
                    <span>PUP e-IPMO</span>
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse" id="navbarNav">
                    <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                        <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('index.php'); ?>">Home</a></li>
                        <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('User/Beforelogin/about.php'); ?>">About Us</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    <!-- Centered login card -->
    <div class="container-fluid d-flex justify-content-center align-items-center login-container">
        <div class="card shadow-sm p-4 login-card-custom">
            <div class="text-center mb-3">
                <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" class="login-logo">
            </div>
            <div class="text-center mb-3">
                <span class="fw-normal student-login-text">Employee Login</span>
            </div>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2 mb-3"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
                <div class="mb-3 position-relative">
                    <input type="text" class="form-control rounded-pill ps-4 pe-5" id="student_number" name="student_number" placeholder="Employee ID" required style="border: 2px solid #222;">
                    <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary" aria-hidden="true">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor">
                            <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                            <path d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/>
                        </svg>
                    </span>
                </div>
                <div class="mb-2 position-relative">
                    <input type="password" class="form-control rounded-pill ps-4 pe-5" id="password" name="password" placeholder="Password" required style="border: 2px solid #222;">
                    <button type="button" class="position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary bg-transparent border-0 toggle-password" data-target="#password" aria-label="Show password" style="text-decoration:none; box-shadow:none;">
                        <svg class="icon-eye" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M16 8s-3-5.5-8-5.5S0 8 0 8s3 5.5 8 5.5S16 8 16 8z"/>
                            <path d="M8 5a3 3 0 1 0 0 6 3 3 0 0 0 0-6z"/>
                        </svg>
                        <svg class="icon-eye-off d-none" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 16 16" fill="currentColor" aria-hidden="true">
                            <path d="M13.359 11.238C15.06 9.87 16 8 16 8s-3-5.5-8-5.5a8.06 8.06 0 0 0-2.79.533l1.262 1.262A6.52 6.52 0 0 1 8 4.5c3.5 0 5.8 3.5 5.8 3.5a13.2 13.2 0 0 1-1.65 1.987l-0.791-.749z"/>
                            <path d="M3.35 4.354l.707-.708 9.9 9.9-.707.707-1.68-1.68A7.74 7.74 0 0 1 8 13.5C3 13.5 0 8 0 8a17.28 17.28 0 0 1 3.35-3.646zM1.2 8s2.3 3.5 6.8 3.5c.648 0 1.248-.08 1.8-.224L7.06 8.036A3 3 0 0 1 6.5 8a3 3 0 0 1 2.95-3l-1.55-1.55C7.5 3.32 7.26 3.3 7 3.3 2.5 3.3 1.2 8 1.2 8z"/>
                        </svg>
                    </button>
                </div>
                <div class="mb-3 text-end">
                    <a href="#" class="small forgot-password-link" style="font-size: 0.95rem;">Forgot password?</a>
                </div>
                <button type="submit" class="btn w-100 login-btn-custom">Login</button>
            </form>
            <a href="<?php echo asset_url('User/Beforelogin/register-student-v2.php'); ?>" class="w-100 d-block"><button class="btn w-100 mt-1 register-btn-custom" type="button">Register</button></a>
        </div>
    </div>
   
   <!-- Footer -->
   <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>
   
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo asset_url('javascript/show-password.js'); ?>" defer></script>
</body>
</html>