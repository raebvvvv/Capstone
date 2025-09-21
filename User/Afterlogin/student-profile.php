<?php 
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
$isLoggedIn = true;

$user_id = $_SESSION['user_id'];

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Get and trim inputs once
    $lastName      = trim($_POST['last_name'] ?? '');
    $firstName     = trim($_POST['first_name'] ?? '');
    $middleInitial = trim($_POST['middle_initial'] ?? '');
    $suffix        = trim($_POST['suffix'] ?? '');
    $homeAddress   = trim($_POST['home_address'] ?? '');
    $mobileNumber  = trim($_POST['mobile_number'] ?? '');
    $campus        = trim($_POST['campus'] ?? '');
    $college       = trim($_POST['college'] ?? '');
    $department    = trim($_POST['department'] ?? '');
    $program       = trim($_POST['program'] ?? '');

    $errors = [];

    // Validation logic
    if (!preg_match('/^[A-Za-z]+(?:\s[A-Za-z]+)*$/', $firstName)) {
        $errors[] = "First name should only contain letters and single spaces between words";
    }
    if (!preg_match('/^[A-Za-z]+(?:\s[A-Za-z]+)*$/', $lastName)) {
        $errors[] = "Last name should only contain letters and single spaces between words";
    }

    // Validate middle initial (single uppercase letter, optional)
    if ($middleInitial && !preg_match('/^[A-Z]$/', $middleInitial)) {
        $errors[] = "Middle initial must be a single uppercase letter";
    }

    // Validate suffix (optional, but must be valid if provided)
    if ($suffix) {
        $validSuffixes = ['Jr.', 'Sr.', 'I', 'II', 'III', 'IV', 'V'];
        if (!in_array($suffix, $validSuffixes)) {
            $errors[] = "Enter a valid suffix (Jr., Sr., I, II, III, IV, V) or leave it blank";
        }
    }

    // Validate mobile number (PH format: 09xxxxxxxxx or +639xxxxxxxxx)
    if (!preg_match('/^(09\d{9}|\+639\d{9})$/', $mobileNumber)) {
        $errors[] = "Invalid mobile number format (use 09XXXXXXXXX or +639XXXXXXXXX)";
    }

    // Required fields (suffix & middle initial optional)
    if (empty($firstName) || empty($lastName) || empty($homeAddress) ||
        empty($mobileNumber) || empty($campus) || empty($college) ||
        empty($department) || empty($program)) {
        $errors[] = "All fields except suffix and middle initial are required";
    }

    if (empty($errors)) {
        // Proceed with update
        $stmt = $pdo->prepare("UPDATE student_profiles SET 
            last_name=?, first_name=?, middle_initial=?, suffix=?, 
            home_address=?, mobile_number=?, campus=?, college=?, 
            department=?, program=? WHERE user_id=?");
        $stmt->execute([
            $lastName, $firstName, $middleInitial, $suffix,
            $homeAddress, $mobileNumber, $campus, $college,
            $department, $program, $user_id
        ]);
        $success = "✅ Profile updated successfully!";
    } else {
        // Show errors as list
        $error = "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>";
    }
}

// Fetch profile data for display
$stmt = $pdo->prepare("SELECT u.email, u.student_number, sp.* FROM users u 
    JOIN student_profiles sp ON u.user_id = sp.user_id WHERE u.user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | PUP e-IPMO</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/student-profile.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <!-- Toast container -->
  <div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div id="profileToast" class="toast align-items-center text-white" 
         role="alert" 
         aria-live="assertive" 
         aria-atomic="true"
         data-bs-delay="2000"
         data-bs-autohide="true">
        <div class="d-flex">
            <div class="toast-body"></div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
        </div>
    </div>
</div>
  <!-- Navbar (uniform across project) -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
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
          <li class="nav-item"><a class="nav-link" href="student-application.php">My Application</a></li>
          <li class="nav-item"><a class="nav-link active" href="student-profile.php">My Profile</a></li>
        </ul>
        <a href="e-services.php" class="btn btn-success ms-3">Proceed to e-Services</a>
      </div>
    </div>
  </nav>


  
  <!-- Main content -->
  <main class="container py-4">
    &nbsp;
    <div class="d-flex align-items-center justify-content-between mb-2">
      <div class="d-flex align-items-center">
        <h1 class="fw-bold mb-0" style="font-size:2.5rem;">My Profile</h1>
        <button id="editProfileBtn" type="button" class="btn btn-primary btn-sm ms-3" style="background-color:#3b36ff;">
          Edit Profile
        </button>           
      </div>
  <?php if ($isLoggedIn): ?>
          <form method="POST" action="<?php echo asset_url('User/Beforelogin/logout.php'); ?>" class="d-inline ms-2">
            <?php csrf_input(); ?>
            <button type="submit" class="btn btn-danger">Logout</button>
          </form>
        <?php endif; ?>
    </div>
    <p class="text-danger fw-semibold mb-4" style="font-size:1.1rem;">(Student)</p>
    <?php if (!empty($success) || !empty($error)): ?>
    <div class="small mb-2">
        <?php if (!empty($success)): ?>
            <span class="badge bg-success">✓ Profile updated</span>
        <?php endif; ?>
        <?php if (!empty($error)): ?>
            <span class="badge bg-danger">Please fix the errors</span>
        <?php endif; ?>
    </div>
<?php endif; ?>
    <div class="bg-white rounded-3 shadow-sm p-4 mx-auto" style="max-width: 1100px;">
      <form id="profileForm" method="POST" action="">
        <input type="hidden" name="update_profile" value="1">
        <div class="row mb-3">
  <div class="col-md-4">
    <label class="form-label">Last Name</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" 
           name="last_name" id="lastName" 
           value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['last_name'])): ?>
      <div class="invalid-feedback"><?php echo $errors['last_name']; ?></div>
    <?php endif; ?>
  </div>
  
  <div class="col-md-4">
    <label class="form-label">First Name</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" 
           name="first_name" id="firstName" 
           value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['first_name'])): ?>
      <div class="invalid-feedback"><?php echo $errors['first_name']; ?></div>
    <?php endif; ?>
  </div>

  <div class="col-md-4">
    <label class="form-label">Middle Initial</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['middle_initial']) ? 'is-invalid' : ''; ?>" 
           name="middle_initial" id="middleInitial" 
           value="<?php echo htmlspecialchars($profile['middle_initial'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['middle_initial'])): ?>
      <div class="invalid-feedback"><?php echo $errors['middle_initial']; ?></div>
    <?php endif; ?>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-4">
    <label class="form-label">Suffix</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['suffix']) ? 'is-invalid' : ''; ?>" 
           name="suffix" id="suffix" 
           value="<?php echo htmlspecialchars($profile['suffix'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['suffix'])): ?>
      <div class="invalid-feedback"><?php echo $errors['suffix']; ?></div>
    <?php endif; ?>
  </div>

  <div class="col-md-8">
    <label class="form-label">Home Address</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['home_address']) ? 'is-invalid' : ''; ?>" 
           name="home_address" id="homeAddress" 
           value="<?php echo htmlspecialchars($profile['home_address'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['home_address'])): ?>
      <div class="invalid-feedback"><?php echo $errors['home_address']; ?></div>
    <?php endif; ?>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-6">
    <label class="form-label fw-bold">Student ID/Number <span class="text-danger">*</span></label>
    <input type="text" class="form-control lock" 
           id="studentId" 
           value="<?php echo htmlspecialchars($profile['student_number'] ?? ''); ?>" 
           readonly>
  </div>

  <div class="col-md-6">
    <label class="form-label">Mobile Number</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['mobile_number']) ? 'is-invalid' : ''; ?>" 
           name="mobile_number" id="mobileNumber" 
           value="<?php echo htmlspecialchars($profile['mobile_number'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['mobile_number'])): ?>
      <div class="invalid-feedback"><?php echo $errors['mobile_number']; ?></div>
    <?php endif; ?>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-3">
    <label class="form-label">Campus</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['campus']) ? 'is-invalid' : ''; ?>" 
           name="campus" id="campus" 
           value="<?php echo htmlspecialchars($profile['campus'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['campus'])): ?>
      <div class="invalid-feedback"><?php echo $errors['campus']; ?></div>
    <?php endif; ?>
  </div>

  <div class="col-md-3">
    <label class="form-label">College</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['college']) ? 'is-invalid' : ''; ?>" 
           name="college" id="college" 
           value="<?php echo htmlspecialchars($profile['college'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['college'])): ?>
      <div class="invalid-feedback"><?php echo $errors['college']; ?></div>
    <?php endif; ?>
  </div>

  <div class="col-md-3">
    <label class="form-label">Department</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['department']) ? 'is-invalid' : ''; ?>" 
           name="department" id="department" 
           value="<?php echo htmlspecialchars($profile['department'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['department'])): ?>
      <div class="invalid-feedback"><?php echo $errors['department']; ?></div>
    <?php endif; ?>
  </div>

  <div class="col-md-3">
    <label class="form-label">Program</label>
    <input type="text" 
           class="form-control <?php echo isset($errors['program']) ? 'is-invalid' : ''; ?>" 
           name="program" id="program" 
           value="<?php echo htmlspecialchars($profile['program'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['program'])): ?>
      <div class="invalid-feedback"><?php echo $errors['program']; ?></div>
    <?php endif; ?>
  </div>
</div>

        <div class="mb-3">
          <label class="form-label fw-bold">Webmail <span class="text-danger">*</span></label>
          <input type="email" class="form-control lock" id="webmail" 
    value="<?php echo htmlspecialchars($profile['email'] ?? ''); ?>" readonly>
        </div>
        <div class="mt-4">
          <button id="saveProfileBtn" type="submit" class="btn btn-primary btn-sm" style="display:none;">
            Save Changes
          </button>
          <button id="cancelEditBtn" type="button" class="btn btn-secondary btn-sm ms-2" style="display:none;">
            Cancel
          </button>
        </div>
      </form>
    </div>
  </main>
  
  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>

  <!-- Scripts -->
  
<script src="<?php echo asset_url('javascript/student-profile.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

