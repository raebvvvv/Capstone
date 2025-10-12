<?php 
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
$isLoggedIn = true;

// DEBUG: Temporarily unlock profile editing regardless of 30-day restriction
// Set to false to restore normal behavior
$DEBUG_UNLOCK_EDIT_PROFILE = true;

$user_id = $_SESSION['user_id'];
// Initialize common vars to avoid notices
$errors = [];
$success = '';

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    // Get and trim inputs once
    $lastName      = trim($_POST['last_name'] ?? '');
    $firstName     = trim($_POST['first_name'] ?? '');
    $middleName    = trim($_POST['middle_name'] ?? '');
    $suffix        = trim($_POST['suffix'] ?? '');
    $homeAddress   = trim($_POST['home_address'] ?? '');
    $mobileNumber  = trim($_POST['mobile_number'] ?? '');
  $campus        = trim($_POST['campus'] ?? '');
  $college       = trim($_POST['college'] ?? '');
  // Department removed from profile editing
  $department    = '';
  $program       = trim($_POST['program'] ?? '');
  $academicLevel = trim($_POST['academic_level'] ?? '');

  // reset errors for this POST
  $errors = [];

  // Check last update timestamp (skipped in debug mode)
  if (!$DEBUG_UNLOCK_EDIT_PROFILE) {
    $stmt = $pdo->prepare("SELECT last_updated_at FROM student_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $lastUpdate = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($lastUpdate && $lastUpdate['last_updated_at']) {
      $lastUpdateDate = new DateTime($lastUpdate['last_updated_at']);
      $now = new DateTime();
      $diff = $lastUpdateDate->diff($now)->days;

      if ($diff < 30) {
        $daysLeft = 30 - $diff;
        $errors[] = "Profile can only be updated once every 30 days. Please wait {$daysLeft} more days.";
      }
    }
  }

  // Validation logic (allow letters, optional dots, and single spaces between words; e.g., "Ma. Criselle")
  $namePattern = '/^[A-Za-z]+(?:\.)?(?:\s[A-Za-z]+(?:\.)?)*$/';
  if (!preg_match($namePattern, $firstName)) {
    $errors[] = "First name should only contain letters, optional dots, and single spaces between words";
  }
  if (!preg_match($namePattern, $lastName)) {
    $errors[] = "Last name should only contain letters, optional dots, and single spaces between words";
  }

  // Validate middle name (optional)
  if ($middleName && !preg_match($namePattern, $middleName)) {
    $errors[] = "Middle name should only contain letters, optional dots, and single spaces between words";
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
    empty($program) || empty($academicLevel)) {
    $errors[] = "All fields except suffix and middle name are required";
  }

    // Fetch current profile data
  $stmt = $pdo->prepare("SELECT last_name, first_name, middle_name, suffix, home_address, mobile_number, campus, college, program, academic_level FROM student_profiles WHERE user_id = ?");
    $stmt->execute([$user_id]);
    $current = $stmt->fetch(PDO::FETCH_ASSOC);

    // Compare new data with current data
    $hasChanges = (
        $lastName      !== $current['last_name'] ||
        $firstName     !== $current['first_name'] ||
        $middleName    !== $current['middle_name'] ||
        $suffix        !== $current['suffix'] ||
        $homeAddress   !== $current['home_address'] ||
        $mobileNumber  !== $current['mobile_number'] ||
  $campus        !== $current['campus'] ||
  $college       !== $current['college'] ||
  $program       !== $current['program'] ||
  $academicLevel !== $current['academic_level']
    );

    if (!$hasChanges) {
        $errors[] = "No changes detected in your profile. Please modify at least one field before saving.";
    }

    if (empty($errors)) {
        // Proceed with update: allow updating name fields and academic fields per request
        $stmt = $pdo->prepare("UPDATE student_profiles SET 
          last_name=?, first_name=?, middle_name=?, suffix=?,
          home_address=?, mobile_number=?, campus=?, college=?, program=?, academic_level=?,
          last_updated_at=NOW()
          WHERE user_id=?");
        $stmt->execute([
          $lastName, $firstName, $middleName, $suffix,
          $homeAddress, $mobileNumber, $campus, $college, $program, $academicLevel,
          $user_id
        ]);
        $success = "✅ Profile updated successfully!";
    } else {
        // Show errors as list
        $error = "<ul><li>" . implode("</li><li>", $errors) . "</li></ul>";
    }
}

// Fetch profile data for display
$stmt = $pdo->prepare("SELECT u.email, sp.* FROM users u 
    JOIN student_profiles sp ON u.user_id = sp.user_id WHERE u.user_id = ?");
$stmt->execute([$user_id]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);

$now = new DateTime(); // Add this line before any use of $now

// Calculate next edit date
$nextEditAllowed = null;
if (!empty($profile['last_updated_at'])) {
    $lastUpdate = new DateTime($profile['last_updated_at']);
    $nextEditAllowed = $lastUpdate->modify('+30 days');
    $daysUntilEdit = $now->diff($nextEditAllowed)->days;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile | PUP e-IPMO</title>
    <!-- Bootstrap CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
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
          <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('index.php'); ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <!-- Add this inside your <ul class="navbar-nav ms-auto mb-2 mb-lg-0"> -->
<li class="nav-item dropdown">
  <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
    <img src="<?php echo asset_url('Photos/Icons/user-icon.png'); ?>" alt="User" width="25" height="25" class="rounded-circle me-2">
    <span><?php echo htmlspecialchars($_SESSION['username'] ?? 'Account'); ?></span>
  </a>
  <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
    <?php $isEmployee = (($_SESSION['role'] ?? '') === 'employee'); ?>
    <li>
      <a class="dropdown-item" href="<?php echo $isEmployee ? 'employee-profile.php' : 'student-profile.php'; ?>">
        My Profile
      </a>
    </li>
    <li><hr class="dropdown-divider"></li>
    <li>
      <a class="dropdown-item text-danger" href="../../logout.php">Logout</a>
    </li>
  </ul>
</li>

          <li><?php include __DIR__ . '/../../partials/user_notifications.php'; ?></li>
        </ul>
        <a href="e-services.php" class="btn btn-success ms-3" style="background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Proceed to e-Services</a>
        <!-- Logout button triggers confirmation modal -->
        <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#logoutModal">
          Logout
        </button>
      </div>
    </div>
  </nav>


  
  <!-- Main content -->
  <main class="container py-4">
    &nbsp;
    <div class="d-flex align-items-center mb-3">
    <h1 class="fw-bold mb-0" style="font-size:2.5rem;">My Profile</h1>
    <button id="editProfileBtn" type="button" class="btn btn-primary btn-sm ms-3" 
            <?php 
              $shouldDisable = false;
              if (!(isset($DEBUG_UNLOCK_EDIT_PROFILE) && $DEBUG_UNLOCK_EDIT_PROFILE)) {
                if (!empty($nextEditAllowed) && ($now < $nextEditAllowed)) {
                  $shouldDisable = true;
                }
              }
              echo $shouldDisable ? 'disabled' : '';
            ?>>
        Edit Profile
    </button>
    <?php if (!(isset($DEBUG_UNLOCK_EDIT_PROFILE) && $DEBUG_UNLOCK_EDIT_PROFILE) && !empty($nextEditAllowed) && ($now < $nextEditAllowed)): ?>
        <small class="text-muted ms-2">
            Available for editing in <?php echo $daysUntilEdit; ?> days
        </small>
    <?php endif; ?>
</div>
  <?php $roleLabel = (isset($_SESSION['role']) && strtolower($_SESSION['role']) === 'employee') ? 'Employee' : 'Student'; ?>
  <p class="text-danger fw-semibold mb-4" style="font-size:1.1rem;">(<?php echo $roleLabel; ?>)</p>
    <?php if (!empty($success)): ?>
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i> Profile updated successfully!
    </div>
<?php endif; ?>

<?php 
// Check for 30-day restriction error specifically
$hasRestrictionError = false;
if (!empty($errors)) {
    foreach ($errors as $err) {
        if (strpos($err, '30 days') !== false) {
            $hasRestrictionError = true;
            ?>
      <div class="alert alert-warning py-1 px-2 small border-0 d-inline-block" style="background-color: #fffbe6; color: #856404; font-size: 0.95rem;">
        <i class="fas fa-clock me-1"></i> <?php echo (isset($DEBUG_UNLOCK_EDIT_PROFILE) && $DEBUG_UNLOCK_EDIT_PROFILE) ? 'Debug mode: 30-day restriction is currently bypassed.' : $err; ?>
            </div>
            <?php
            break;
        }
    }
}
// Show other validation errors if any
if (!empty($errors) && !$hasRestrictionError): ?>
    <div class="alert alert-danger py-1 px-2 small border-0" style="background-color: #f8d7da; color: #721c24; font-size: 0.95rem;">
        Please fix the following errors:
        <ul class="mb-0">
            <?php foreach ($errors as $err): ?>
                <?php if (strpos($err, '30 days') === false): ?>
                    <li><?php echo $err; ?></li>
                <?php endif; ?>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>
    <div class="bg-white rounded-3 shadow-sm p-4 mx-auto" style="max-width: 1100px;">
      <form id="profileForm" method="POST" action="">
        <input type="hidden" name="update_profile" value="1">
        <div class="row mb-3">
  <div class="col-md-4">
    <label class="form-label">Last Name</label>
    <input type="text" 
      class="form-control bg-light lock <?php echo isset($errors['last_name']) ? 'is-invalid' : ''; ?>" 
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
           class="form-control bg-light lock <?php echo isset($errors['first_name']) ? 'is-invalid' : ''; ?>" 
           name="first_name" id="firstName" 
           value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['first_name'])): ?>
      <div class="invalid-feedback"><?php echo $errors['first_name']; ?></div>
    <?php endif; ?>
  </div>

  <div class="col-md-4">
  <label class="form-label">Middle Name</label>
    <input type="text" 
           class="form-control bg-light lock <?php echo isset($errors['middle_name']) ? 'is-invalid' : ''; ?>" 
           name="middle_name" id="middleName" 
           value="<?php echo htmlspecialchars($profile['middle_name'] ?? ''); ?>" 
           readonly>
    <?php if (isset($errors['middle_name'])): ?>
      <div class="invalid-feedback"><?php echo $errors['middle_name']; ?></div>
    <?php endif; ?>
  </div>
</div>

<div class="row mb-3">
  <div class="col-md-4">
    <label class="form-label">Suffix</label>
    <input type="text" 
           class="form-control bg-light lock <?php echo isset($errors['suffix']) ? 'is-invalid' : ''; ?>" 
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
    <input type="text" class="form-control lock bg-light" 
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
    <select 
      class="form-select bg-light lock <?php echo isset($errors['campus']) ? 'is-invalid' : ''; ?>" 
      name="campus" id="campus" disabled>
      <?php $campusVal = trim((string)($profile['campus'] ?? '')); ?>
      <option value="<?php echo htmlspecialchars($campusVal); ?>" selected><?php echo htmlspecialchars($campusVal ?: 'Choose...'); ?></option>
    </select>
    <?php if (isset($errors['campus'])): ?>
      <div class="invalid-feedback"><?php echo $errors['campus']; ?></div>
    <?php endif; ?>
  </div>

  <div class="col-md-3">
    <label class="form-label">College</label>
    <select 
      class="form-select bg-light lock <?php echo isset($errors['college']) ? 'is-invalid' : ''; ?>" 
      name="college" id="college" disabled>
      <?php $collegeVal = trim((string)($profile['college'] ?? '')); ?>
      <option value="<?php echo htmlspecialchars($collegeVal); ?>" selected><?php echo htmlspecialchars($collegeVal ?: 'Choose...'); ?></option>
    </select>
    <?php if (isset($errors['college'])): ?>
      <div class="invalid-feedback"><?php echo $errors['college']; ?></div>
    <?php endif; ?>
  </div>

  <div class="col-md-3">
    <label class="form-label">Program</label>
    <select 
      class="form-select bg-light lock <?php echo isset($errors['program']) ? 'is-invalid' : ''; ?>" 
      name="program" id="program" disabled>
      <?php $programVal = trim((string)($profile['program'] ?? '')); ?>
      <option value="<?php echo htmlspecialchars($programVal); ?>" selected><?php echo htmlspecialchars($programVal ?: 'Choose...'); ?></option>
    </select>
    <?php if (isset($errors['program'])): ?>
      <div class="invalid-feedback"><?php echo $errors['program']; ?></div>
    <?php endif; ?>
  </div>
  <div class="col-md-3">
    <label class="form-label">Academic Level</label>
    <select
           class="form-select bg-light lock <?php echo isset($errors['academic_level']) ? 'is-invalid' : ''; ?>"
           name="academic_level" id="academicLevel" disabled>
      <?php $levelVal = trim((string)($profile['academic_level'] ?? '')); ?>
      <option value="<?php echo htmlspecialchars($levelVal); ?>" selected><?php echo htmlspecialchars($levelVal ?: 'Choose...'); ?></option>
    </select>
    <?php if (isset($errors['academic_level'])): ?>
      <div class="invalid-feedback"><?php echo $errors['academic_level']; ?></div>
    <?php endif; ?>
  </div>
</div>

        <div class="mb-3">
          <label class="form-label fw-bold">Webmail <span class="text-danger">*</span></label>
          <input type="email" class="bg-light lock form-control lock" id="webmail" 
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
  <script>
    // Ensure catalogs API base is set for profile pages too
    window.CATALOGS_API_URL = "<?php echo asset_url('catalogs_public_api.php'); ?>";
  </script>
  <script src="<?php echo asset_url('javascript/forms/student-academic-dropdowns.js'); ?>"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
<script src="<?php echo asset_url('javascript/student-profile.js'); ?>"></script>
<script src="<?php echo asset_url('javascript/date-limit.js'); ?>"></script>
<script src="<?php echo asset_url('javascript/student-profile-inline.js'); ?>"></script>
</body>

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to log out?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="<?php echo asset_url('logout.php'); ?>" class="d-inline">
          <?php csrf_input(); ?>
          <button type="submit" class="btn btn-danger">Yes, log me out</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>
