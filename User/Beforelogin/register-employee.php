<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
require __DIR__ . '/../../config.php';

// Include PHPMailer
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;
require __DIR__ . '/../../PHPMailer/vendor/autoload.php'; // Adjust path as needed

$success = '';
$error = '';
// Collect detailed error messages (e.g., password requirement failures)
$errorDetails = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $lastName      = ucwords(strtolower(trim($_POST['lastName'])));
    $firstName     = ucwords(strtolower(trim($_POST['firstName'])));
    $middleName    = ucwords(strtolower(trim($_POST['middleName'])));
    $suffix        = ucwords(strtolower(trim($_POST['suffix'])));
    $homeAddress   = trim($_POST['homeAddress']);
    $employeeNumber = trim($_POST['employeeNumber']);
    $mobileNumber  = trim($_POST['mobileNumber']);
    $campus        = trim($_POST['campus']);
    $college       = trim($_POST['college']);
    $department    = trim($_POST['department']);
    $email         = trim($_POST['email']);
    $password      = $_POST['password'];
    $repassword    = $_POST['repassword'];

    // Basic validation
    if (
        !$lastName || !$firstName || !$middleName || !$homeAddress ||
        !$employeeNumber || !$mobileNumber || !$campus || !$college ||
        !$department || !$email || !$password || !$repassword
    ) {
        $error = "All required fields must be filled.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $repassword) {
        $error = "Passwords do not match.";
    } else {
        // Robust server-side password policy checks with granular messages
        $pwdErrors = [];
        if (strlen($password) < 12) { $pwdErrors[] = 'Must be at least 12 characters long.'; }
        if (!preg_match('/[a-z]/', $password)) { $pwdErrors[] = 'Must contain at least one lowercase letter.'; }
        if (!preg_match('/[A-Z]/', $password)) { $pwdErrors[] = 'Must contain at least one uppercase letter.'; }
        if (!preg_match('/\d/', $password)) { $pwdErrors[] = 'Must contain at least one number.'; }
        if (!preg_match('/[^a-zA-Z\d]/', $password)) { $pwdErrors[] = 'Must contain at least one special character.'; }

        if (!empty($pwdErrors)) {
            $error = "Password does not meet requirements:";
            $errorDetails = $pwdErrors;
        } else {
            // Check for duplicate employee number
            $stmt = $pdo->prepare("SELECT employee_number FROM employee_profiles WHERE employee_number = ?");
            $stmt->execute([$employeeNumber]);
            if ($stmt->fetch()) {
                $error = "Employee number already exists.";
            } else {
                // Check for duplicate email
                $stmt = $pdo->prepare("SELECT email FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    $error = "Email already exists.";
                } else {
                    try {
                        $pdo->beginTransaction();

                        // Hash password
                        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

                        // Generate verification code
                        $verification_code = bin2hex(random_bytes(8));
                        $code_expires_at = date('Y-m-d H:i:s', strtotime('+1 day'));

                        // Insert user securely
                        $stmt = $pdo->prepare("INSERT INTO users (email, password, role, status, verification_code, code_expires_at) VALUES (?, ?, 'employee', 'pending', ?, ?)");
                        $stmt->execute([$email, $hashedPassword, $verification_code, $code_expires_at]);
                        $user_id = $pdo->lastInsertId();

                        // Insert into employee_profiles table
                        $stmt = $pdo->prepare("INSERT INTO employee_profiles (user_id, employee_number, last_name, first_name, middle_name, suffix, home_address, mobile_number, campus, college, department) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                        $stmt->execute([
                            $user_id,
                            $employeeNumber,
                            $lastName,
                            $firstName,
                            $middleName,
                            $suffix,
                            $homeAddress,
                            $mobileNumber,
                            $campus,
                            $college,
                            $department
                        ]);

                        // Send verification email
                        // Load secure email configuration
                        $email_config = require __DIR__ . '/../../email_config.php';
                        $smtp_config = $email_config['smtp'];
                        
                        $mail = new PHPMailer(true);
                        try {
                            $mail->isSMTP();
                            $mail->Host = $smtp_config['host'];
                            $mail->SMTPAuth = true;
                            $mail->Username = $smtp_config['username'];
                            $mail->Password = $smtp_config['password'];
                            $mail->SMTPSecure = $smtp_config['encryption'] === 'tls' ? PHPMailer::ENCRYPTION_STARTTLS : PHPMailer::ENCRYPTION_SMTPS;
                            $mail->Port = $smtp_config['port'];

                            $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);
                            $mail->addAddress($email, $firstName . ' ' . $lastName);

                            $mail->isHTML(true);
                            $mail->Subject = 'Verify your email address';
                            $mail->Body    = "Dear $firstName,<br><br>Please verify your email by clicking the link below:<br>
                            <a href='http://localhost/Capstone/User/Beforelogin/verify.php?code=$verification_code&email=$email'>Verify Email</a><br><br>
                            This link will expire in 24 hours.<br><br>Thank you!";

                            $mail->send();
                            $success = "Registration successful! Please check your email to verify your account.";
                            
                            $pdo->commit();
                        } catch (Exception $e) {
                            $pdo->rollback();
                            $error = "Registration failed. Email could not be sent: " . $mail->ErrorInfo;
                        }
                    } catch (PDOException $e) {
                        $pdo->rollback();
                        $error = "Registration failed. Please try again.";
                        error_log("Employee registration error: " . $e->getMessage());
                    }
                }
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
    <title>Employee Registration | PUP e-IPMO</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="../../css/register-student-custom.css">
  <link rel="stylesheet" href="../../css/main.css">
</head>
<body class="bg-light d-flex flex-column min-vh-100">
  <nav class="navbar navbar-expand-lg bg-white border-bottom">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
  <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <main class="flex-grow-1 d-flex justify-content-center align-items-center py-4">
    <div class="register-card-custom">  
      <div class="register-form-section-custom">
        <div class="form-section-title-custom mb-4">Employee Registration</div>
        
        <!-- Success/Error Message -->
        <?php if ($error): ?>
          <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
            <?php if (!empty($errorDetails)): ?>
              <ul class="mt-2 mb-0">
                <?php foreach ($errorDetails as $msg): ?>
                  <li><?php echo htmlspecialchars($msg); ?></li>
                <?php endforeach; ?>
              </ul>
            <?php endif; ?>
          </div>
        <?php elseif ($success): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="#" autocomplete="off" id="employeeRegForm" class="needs-validation" novalidate>
          <div class="row g-3 mb-2">
            <div class="col-md-3">
              <label for="lastName" class="form-label">Last Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="lastName" name="lastName" required pattern="[A-Za-z\- ]+" title="Only letters and hyphens (-) allowed">
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
            <div class="col-md-5">
              <label for="firstName" class="form-label">First Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="firstName" name="firstName" required pattern="[A-Za-z\- ]+" title="Only letters, spaces, and hyphens (-) allowed">
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
            <div class="col-md-2">
              <label for="middleName" class="form-label">Middle Name <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="middleName" name="middleName"  required pattern="[A-Za-z\-.]+" title="Only letters, hyphens (-), and dot (.) allowed">
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
            <div class="col-md-2">
              <label for="suffix" class="form-label">Suffix <span class="text-danger"></span></label>
              <input type="text" class="form-control" id="suffix" name="suffix" maxlength="2"  pattern="[A-Za-z\.]+" title="Up to 2 letters or dot (.) allowed">
              
            </div>
          </div>
          <div class="mb-2">
            <label for="homeAddress" class="form-label">Home Address <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="homeAddress" name="homeAddress" required>
            <div class="invalid-feedback">Please fill in this field.</div>
          </div>
          <div class="row g-3 mb-3">
            <div class="col-md-5">
              <label for="employeeNumber" class="form-label">Employee Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="employeeNumber" name="employeeNumber" required title="Employee ID Number">
              <div class="invalid-feedback">Please enter your employee number.</div>
            </div>
            <div class="col-md-7">
              <label for="mobileNumber" class="form-label">Mobile Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" required pattern="^09\d{9}$" title="Format: 09XXXXXXXXX">
              <div class="invalid-feedback">Format: 09XXXXXXXXX</div>
            </div>
          </div>
          <div class="row g-3 mb-2">
            <div class="col-md-4">
              <label for="campus" class="form-label">Campus <span class="text-danger">*</span></label>
              <select class="form-select" id="campus" name="campus" required></select>
              <div class="invalid-feedback">Please select a campus.</div>
            </div>
            <div class="col-md-4">
              <label for="college" class="form-label">College <span class="text-danger">*</span></label>
              <select class="form-select" id="college" name="college" required></select>
              <div class="invalid-feedback">Please select a college.</div>
            </div>
            <div class="col-md-4">
              <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="department" name="department" required>
              <div class="invalid-feedback">Please enter your department.</div>
            </div>
          </div>
          <div class="mb-2">
            <label for="email" class="form-label"><b>Email Address</b> <span class="text-danger">*</span></label>
            <input type="email" class="form-control" id="email" name="email" required>
            <div class="invalid-feedback">Please fill in this field.</div>
          </div>
          <div class="row g-3 mb-1">
            <div class="col-md-6">
              <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
              <div class="input-group mb-2">
                <input
                  type="password"
                  class="form-control rounded-start"
                  id="password"
                  name="password"
                  required
                  minlength="12"
                  pattern="^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};':&quot;\\|,.<>\/?~]).{12,}$"
                  title="At least 12 characters, with uppercase, lowercase, number, and special character"
                  aria-describedby="togglePassword"
                />
                <button type="button" id="togglePassword" tabindex="-1" class="input-group-text rounded-end" style="background:transparent;border:none;outline:none;box-shadow:none;" aria-label="Show password">
                  <svg id="eyeIcon" xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 3.866-4.477 7-10 7S1 15.866 1 12 5.477 5 11 5s10 3.134 10 7z"/></svg>
                </button>
              </div>
              <!-- Dynamic password error details (client-side, no refresh) -->
              <ul id="passwordErrors" class="text-danger small ps-3 mb-2 d-none" aria-live="polite"></ul>
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
            <div class="col-md-6">
              <label for="repassword" class="form-label">Re-enter Password <span class="text-danger">*</span></label>
              <div class="input-group mb-2">
                <input
                  type="password"
                  class="form-control rounded-start"
                  id="repassword"
                  name="repassword"
                  required
                  minlength="12"
                  placeholder="Password must match"
                  aria-describedby="toggleRepassword"
                />
                <button type="button" id="toggleRepassword" tabindex="-1" class="input-group-text rounded-end" style="background:transparent;border:none;outline:none;box-shadow:none;" aria-label="Show password">
                  <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0zm6 0c0 3.866-4.477 7-10 7S1 15.866 1 12 5.477 5 11 5s10 3.134 10 7z"/></svg>
                </button>
              </div>
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
          </div>
          <button type="submit" class="btn register-btn-custom">Register</button>
        </form>
      </div>
      <div class="register-side-custom">
        <img src="<?php echo asset_url('Photos/account-registration-side (1).png'); ?>" alt="Employee Registration Design" />
      </div>
    </div>
  </main>


  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?> 
 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo asset_url('javascript/employee-registration.js'); ?>" defer></script>
   <script src="<?php echo asset_url('javascript/forms/academic-dropdowns.js'); ?>" defer></script>

</body>
</html>