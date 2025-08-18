<?php
session_start();
require 'conn.php'; // Include your database connection file


if ($_SERVER["REQUEST_METHOD"] == "POST") {
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
            // Check if the student number already exists
            $query = "SELECT * FROM users WHERE student_number = ?";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("s", $student_number);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                $error = "This student number already exists!";
            } else {
                // Hash the password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);

                // Handle file upload
                $upload_dir = 'uploads/';  // Directory to store uploaded files
                $cor_file_name = $_FILES['cor']['name'];
                $cor_file_path = $upload_dir . basename($cor_file_name);

                // Move the uploaded file to the target directory
                if (move_uploaded_file($_FILES['cor']['tmp_name'], $cor_file_path)) {
                    // Prepare the SQL statement
                    $status = $is_admin ? 'approved' : 'pending';
                    $sql = "INSERT INTO users (student_number, username, email, password, status, created_at, is_admin, COR) 
                            VALUES (?, ?, ?, ?, ?, NOW(), ?, ?)";

                    $stmt = $conn->prepare($sql);
                    // Bind parameters for prepared statement
                    $stmt->bind_param("sssssis", $student_number, $username, $email, $hashed_password, $status, $is_admin, $cor_file_path);

                    // Execute the statement
                    if ($stmt->execute()) {
                        echo "<script>alert('Registration successful!'); window.location.href='login.php';</script>";
                    } else {
                        $error = "Error: " . $stmt->error;
                    }
                } else {
                    $error = "Error moving the COR file.";
                }
            }

            // Close the statement and connection
            $stmt->close();
            $conn->close();
        } else {
            $error = "Please fill in all fields.";
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
        <form method="POST" action="register.php" onsubmit="return validateForm();" enctype="multipart/form-data">
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
