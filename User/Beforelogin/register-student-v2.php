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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get and sanitize inputs
    $lastName      = trim($_POST['lastName']);
    $firstName     = trim($_POST['firstName']);
    $middleInitial = trim($_POST['middleInitial']);
    $suffix        = trim($_POST['suffix']);
    $homeAddress   = trim($_POST['homeAddress']);
    $studentNumber = trim($_POST['studentNumber']);
    $mobileNumber  = trim($_POST['mobileNumber']);
    $campus        = trim($_POST['campus']);
    $college       = trim($_POST['college']);
    $department    = trim($_POST['department']);
    $program       = trim($_POST['program']);
    $email         = trim($_POST['email']);
    $password      = $_POST['password'];
    $repassword    = $_POST['repassword'];

    // Basic validation
    if (
        !$lastName || !$firstName || !$middleInitial || !$homeAddress ||
        !$studentNumber || !$mobileNumber || !$campus || !$college ||
        !$department || !$program || !$email || !$password || !$repassword
    ) {
        $error = "All required fields must be filled.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Invalid email format.";
    } elseif ($password !== $repassword) {
        $error = "Passwords do not match.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[!@#$%^&*()_+\-=\[\]{};\'":\\|,.<>\/?~]).{12,}$/', $password)) {
        $error = "Password does not meet requirements.";
    } else {
        // Check for duplicate student number or email
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE student_number = ? OR email = ?");
        $stmt->execute([$studentNumber, $email]);
        if ($stmt->fetchColumn() > 0) {
            $error = "Student number or email already registered.";
        } else {
            // Hash password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Generate verification code
            $verification_code = bin2hex(random_bytes(8));
            $code_expires_at = date('Y-m-d H:i:s', strtotime('+1 day'));

            // Insert user securely
            $stmt = $pdo->prepare("INSERT INTO users (student_number, email, password, role, status, verification_code, code_expires_at) VALUES (?, ?, ?, 'student', 'pending', ?, ?)");
            $stmt->execute([$studentNumber, $email, $hashedPassword, $verification_code, $code_expires_at]);
            $user_id = $pdo->lastInsertId();

            // Insert into student_profiles table
            $stmt = $pdo->prepare("INSERT INTO student_profiles (user_id, last_name, first_name, middle_initial, suffix, home_address, mobile_number, campus, college, department, program) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([
                $user_id,
                $lastName,
                $firstName,
                $middleInitial,
                $suffix,
                $homeAddress,
                $mobileNumber,
                $campus,
                $college,
                $department,
                $program
            ]);

            // Send verification email
            $mail = new PHPMailer(true);
            try {
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Set your SMTP server
                $mail->SMTPAuth = true;
                $mail->Username = 'znixossoxinz@gmail.com'; // SMTP username
                $mail->Password = 'xkwmdpxqzhwyrcok';   // SMTP password
                $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                $mail->Port = 587;

                $mail->setFrom('no-reply@yourdomain.com', 'PUP e-IPMO');
                $mail->addAddress($email, $firstName . ' ' . $lastName);

                $mail->isHTML(true);
                $mail->Subject = 'Verify your email address';
                $mail->Body    = "Dear $firstName,<br><br>Please verify your email by clicking the link below:<br>
                <a href='http://localhost/Capstone/User/Beforelogin/verify.php?code=$verification_code&email=$email'>Verify Email</a><br><br>
                This link will expire in 24 hours.<br><br>Thank you!";

                $mail->send();
                $success = "Registration successful! Please check your email to verify your account.";
            } catch (Exception $e) {
                $error = "Registration successful, but email could not be sent. Mailer Error: {$mail->ErrorInfo}";
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
    <title>Student Registration | PUP e-IPMO</title>
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
        <div class="form-section-title-custom mb-4">Student Registration</div>
        
        <!-- Success/Error Message -->
        <?php if ($error): ?>
          <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($success): ?>
          <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="#" enctype="multipart/form-data" autocomplete="off" id="studentRegForm" >
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
              <label for="middleInitial" class="form-label">Middle Initial <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="middleInitial" name="middleInitial" maxlength="2" required pattern="[A-Za-z\-.]+" title="Only letters, hyphens (-), and dot (.) allowed">
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
              <label for="studentNumber" class="form-label">Student Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="studentNumber" name="studentNumber" required pattern="^\d{4}-\d{5}-MN-0$" title="Format: YYYY-XXXXX-MN-0">
              <div class="invalid-feedback">Format: YYYY-XXXXX-MN-0</div>
            </div>
            <div class="col-md-7">
              <label for="mobileNumber" class="form-label">Mobile Number <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="mobileNumber" name="mobileNumber" required pattern="^09\d{9}$" title="Format: 09XXXXXXXXX">
              <div class="invalid-feedback">Format: 09XXXXXXXXX</div>
            </div>
          </div>
          <div class="row g-3 mb-2">
            <div class="col-md-3">
              <label for="campus" class="form-label">Campus <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="campus" name="campus" required pattern="[A-Za-z ]+" title="Only letters and spaces allowed">
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
            <div class="col-md-3">
              <label for="college" class="form-label">College <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="college" name="college" required pattern="[A-Za-z ]+" title="Only letters and spaces allowed">
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
            <div class="col-md-3">
              <label for="department" class="form-label">Department <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="department" name="department" required pattern="[A-Za-z ]+" title="Only letters and spaces allowed">
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
            <div class="col-md-3">
              <label for="program" class="form-label">Program <span class="text-danger">*</span></label>
              <input type="text" class="form-control" id="program" name="program" required pattern="[A-Za-z ]+" title="Only letters and spaces allowed">
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
          </div>
          <div class="mb-2">
            <label for="email" class="form-label"><b>Webmail</b> <span class="text-danger">*</span></label>
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
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
            <div class="col-md-6">
              <label for="repassword" class="form-label">Re-enter Password <span class="text-danger">*</span></label>
              <input type="password" class="form-control" id="repassword" name="repassword" required minlength="12" placeholder="Password must match" />
              <div class="invalid-feedback">Please fill in this field.</div>
            </div>
          </div>
          <button type="submit" class="btn register-btn-custom">Register</button>
        </form>
      </div>
      <div class="register-side-custom">
  <img src="<?php echo asset_url('Photos/account-registration-side (1).png'); ?>" alt="Account Registration Design" />
      </div>
    </div>
  </main>


  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?> 
 
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
const form       = document.getElementById('studentRegForm');
const password   = document.getElementById('password');
const repassword = document.getElementById('repassword');
const togglePass = document.getElementById('togglePassword');

// Show/Hide password
togglePass.addEventListener('click', () => {
  const type = password.type === 'password' ? 'text' : 'password';
  password.type = type;
  
});

// Show validation feedback on submit
form.addEventListener('submit', function (event) {
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        document.querySelectorAll('#studentRegForm input').forEach(input => {
            if (!input.checkValidity()) {
                input.classList.add('is-invalid');
            } else {
                input.classList.remove('is-invalid');
            }
        });
    }

    // Password match check
    if (password.value !== repassword.value) {
        repassword.setCustomValidity("Passwords do not match");
        event.preventDefault();
        event.stopPropagation();
        repassword.classList.add('is-invalid');
    } else {
        repassword.setCustomValidity("");
    }

    form.classList.add('was-validated');
});

</script>
</body>
</html>