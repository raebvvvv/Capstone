<?php
require __DIR__ . '/security_bootstrap.php';
secure_bootstrap();
require 'conn.php'; // Include your database connection file


if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Simple rate limit (low issue #22): limit 5 registrations per IP per 10 minutes in session scope
    $now = time();
    if (!isset($_SESSION['reg_rl'])) { $_SESSION['reg_rl'] = []; }
    // Purge old entries
    $_SESSION['reg_rl'] = array_filter($_SESSION['reg_rl'], function($ts) use ($now){ return ($now - $ts) < 600; });
    if (count($_SESSION['reg_rl']) >= 5) {
        $error = 'Too many registration attempts. Please wait and try again.';
    } else {
        $_SESSION['reg_rl'][] = $now;
    }
    if (!isset($error)) {
    verify_csrf_post();
    $student_number = trim(htmlspecialchars($_POST['student_number']));
    $username = trim(htmlspecialchars($_POST['username']));
    $email = trim(htmlspecialchars($_POST['email']));
    $password = $_POST['password'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;

    // Validate student number format
    if (!preg_match('/^20\d{2}-\d{5}-MN-0$/', $student_number)) {
        $error = "Student number should be formatted as 20XX-XXXXX-MN-0";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif (strlen($password) < 8) {
        $error = "Password should be at least 8 characters long.";
    } elseif (!isset($_FILES['cor']) || $_FILES['cor']['error'] !== UPLOAD_ERR_OK) {
        $error = "Error uploading the COR file.";
    } elseif ($_FILES['cor']['type'] != 'application/pdf') {
        $error = "Only PDF files are allowed for COR.";
    } elseif ($_FILES['cor']['size'] > 5 * 1024 * 1024) { // Maximum 5MB file size
        $error = "COR file size should not exceed 5MB.";
    } else {
        if (!empty($student_number) && !empty($username) && !empty($email) && !empty($password) && !empty($_FILES['cor']['name'])) {
            // Check if the student number already exists (limit data exposure)
            $query = "SELECT 1 FROM users WHERE student_number = ? LIMIT 1";
            $stmt = $conn->prepare($query);
            if (!$stmt) {
                $error = "Registration unavailable. Please try again later.";
                log_event('DB_ERROR', 'Prepare duplicate student check failed', ['error' => $conn->error]);
            } else {
                $stmt->bind_param("s", $student_number);
                if (!$stmt->execute()) {
                    $error = "Registration unavailable. Please try again later.";
                    log_event('DB_ERROR', 'Execute duplicate student check failed', ['error' => $stmt->error]);
                } else {
                    $result = $stmt->get_result();
                }
            }

            if (isset($result) && $result->num_rows > 0) {
                $error = "This student number already exists!";
                log_event('REGISTER_DUP', 'Duplicate student number registration attempt', ['student_number' => $student_number]);
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Handle file upload
                $upload_dir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR;  // Directory to store uploaded files
                if (!is_dir($upload_dir)) { mkdir($upload_dir, 0700, true); }
                $finfo = new finfo(FILEINFO_MIME_TYPE);
                $mime  = $finfo->file($_FILES['cor']['tmp_name']);
                if ($mime !== 'application/pdf') {
                    $error = 'Invalid file content type.';
                } else {
                    $randomName = bin2hex(random_bytes(16)) . '.pdf';
                    $cor_file_path = $upload_dir . $randomName;
                    if (move_uploaded_file($_FILES['cor']['tmp_name'], $cor_file_path)) {
                        // Store relative path outside of absolute system path
                        $cor_file_path = 'uploads/' . $randomName;
                    // Prepare the SQL statement
                    // Immediately activate (approve) all successfully registered users
                    $status = 'approved';
                    $sql = "INSERT INTO users (student_number, username, email, password, status, created_at, is_admin, COR) 
                            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";

                    $stmt = $conn->prepare($sql);
                    if (!$stmt) {
                        $error = "Registration unavailable. Please try again later.";
                        log_event('DB_ERROR', 'Prepare registration insert failed', ['error' => $conn->error]);
                    } else {
                        // Bind parameters for prepared statement
                        $stmt->bind_param("sssssis", $student_number, $username, $email, $hashed_password, $status, $is_admin, $cor_file_path);
                        if ($stmt->execute()) {
                            log_event('REGISTER', 'User registered', ['student_number' => $student_number]);
                            echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
                        } else {
                            $error = "Registration failed. Please try again later.";
                            log_event('REGISTER_FAIL', 'Registration DB error', ['student_number' => $student_number, 'error' => $stmt->error]);
                        }
                    }
                    } else {
                        $error = 'Error moving the COR file.';
                        log_event('REGISTER_FAIL', 'File move error', ['student_number' => $student_number]);
                    }
                }
            }

            // Close the statement and connection
            $stmt->close();
            $conn->close();
        } else {
            $error = "Please fill in all fields.";
            log_event('REGISTER_FAIL', 'Missing fields');
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
    <title>Register</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <link rel="stylesheet" href="css/style.css?v=2">
    <script>
    function validateForm() {
        var password = document.getElementById("password").value;
        var confirmPassword = document.getElementById("confirm_password").value;
        if (password != confirmPassword) {
            alert("Passwords do not match.");
            return false;
        }
        return true;
    }
    </script>
</head>
<body>
    <div class="container">
        <h2>Register</h2>
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="register.php" onsubmit="return validateForm();" enctype="multipart/form-data" autocomplete="off">
            <?php csrf_input(); ?>
            <div class="mb-3">
                <label for="student_number" class="form-label">Student Number</label>
                <input type="text" class="form-control" id="student_number" name="student_number" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Confirm Password</label>
                <input type="password" class="form-control" id="confirm_password" required>
            </div>
            <div class="mb-3">
                <label for="cor" class="form-label">Upload COR (PDF only)</label>
                <input type="file" class="form-control" id="cor" name="cor" accept="application/pdf" required>
            </div>
            
            <button type="submit" class="btn btn-primary">Register</button>
        </form>
        <a href="login.php"><button class="btn btn-secondary mt-2">Login</button></a>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
