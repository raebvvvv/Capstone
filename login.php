<?php
session_start();
require 'conn.php'; // Include your database connection file

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_number = trim(htmlspecialchars($_POST['student_number']));
    $password = $_POST['password'];

    if (!empty($student_number) && !empty($password)) {
        $query = "SELECT user_id, username, student_number, email, password, is_admin, status FROM users WHERE student_number = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $student_number);
        $stmt->execute();
        $result = $stmt->get_result();
        $user = $result->fetch_assoc();

        if ($user && password_verify($password, $user['password'])) {
            if ($user['status'] == 'pending' && !$user['is_admin']) {
                $error = "Please wait for the confirmation of your account.";
            } else {
                session_regenerate_id(true); // Security: Prevent session fixation attacks
                $_SESSION['user_logged_in'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['student_number'] = $user['student_number'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['is_admin'] = $user['is_admin']; // Set admin status

                if ($user['is_admin']) {
                    header("Location: admin/admin.php"); // Redirect to admin dashboard inside admin folder
                } else {
                    header("Location: after-landing.php"); // Redirect to user landing page (root file)
                }
                exit();
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="icon" type="image/png" href="Photos/pup-logo.png">
    <link rel="stylesheet" href="css/main.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>
    <!-- Navbar (matches index.php) -->
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
                        <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
                    </ul>
                </div>
            </div>
        </nav>
    <!-- Centered login card -->
    <div class="container-fluid d-flex justify-content-center align-items-center" style="min-height: calc(100vh - 70px);">
        <div class="card shadow-sm p-4 login-card-custom">
            <div class="text-center mb-3">
                <img src="Photos/pup-logo.png" alt="PUP Logo" class="login-logo">
            </div>
            <div class="text-center mb-3">
                <span class="fw-normal student-login-text">Student Login.</span>
            </div>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger py-2 mb-3"><?php echo $error; ?></div>
            <?php endif; ?>
            <form method="POST" action="login.php">
                <div class="mb-3 position-relative">
                    <input type="text" class="form-control rounded-pill ps-4" id="student_number" name="student_number" placeholder="Webmail" required style="border: 2px solid #222;">
                    <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary"><i class="fa fa-user-o"></i></span>
                </div>
                <div class="mb-2 position-relative">
                    <input type="password" class="form-control rounded-pill ps-4" id="password" name="password" placeholder="Password" required style="border: 2px solid #222;">
                    <span class="position-absolute top-50 end-0 translate-middle-y pe-3 text-secondary"><i class="fa fa-lock"></i></span>
                </div>
                <div class="mb-3 text-end">
                    <a href="#" class="small forgot-password-link" style="font-size: 0.95rem;">Forgot password?</a>
                </div>
                <button type="submit" class="btn w-100 login-btn-custom">Login</button>
            </form>
            <a href="register-student-v2.php" class="w-100 d-block"><button class="btn w-100 mt-1 register-btn-custom">Register</button></a>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>