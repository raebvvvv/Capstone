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
                    header("Location: admin.php"); // Redirect to admin home page
                } else {
                    header("Location: after-landing.php"); // Redirect to user landing page
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
    <title>Login - HM Kitchen Tool</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="css/style.css?v=2">
</head>
<body>
    <div class="container">
        <a href="index.php"><i class="fa fa-home" style="font-size:48px;color:blue;"></i></a>
        <h2>Login</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="login.php">
            <div class="mb-3">
                <label for="student_number" class="form-label">Student Number</label>
                <input type="text" class="form-control" id="student_number" name="student_number" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>
        <a href="register.php"><button class="btn btn-secondary mt-2">Register</button></a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>