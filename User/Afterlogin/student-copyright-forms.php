<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';

// Fetch user profile data
$stmt = $pdo->prepare("\n    SELECT sp.*, u.email\n    FROM student_profiles sp \n    JOIN users u ON sp.user_id = u.user_id \n    WHERE sp.user_id = ?\n");
$stmt->execute([$_SESSION['user_id']]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>How do I apply for IP? | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/forms.css'); ?>">
</head>
<body class="forms-page">
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom">
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
        <li class="nav-item"><?php include __DIR__ . '/../../partials/user_notifications.php'; ?></li>

          <!-- Add this inside your <ul class="navbar-nav ms-auto mb-2 mb-lg-0"> -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <!-- User icon (SVG only, no text) -->
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                <path d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/>
              </svg>
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
                <a class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
              </li>
            </ul>
          </li>

        </ul>
      </div>
    </div>
  </nav>

  <main class="py-4">
    <div class="container">
      <div class="row justify-content-center">
        <div class="col-lg-8 col-xl-7">
          <!-- Terms & Conditions -->
          <div id="termsGate" class="card shadow-sm mb-4">
        <div class="card-header bg-primary text-white py-2">
          <strong>Terms &amp; Conditions</strong>
        </div>
        <div class="card-body small">
          <p class="mb-2">By filling in this form, you agree to the following:</p>
          <ol class="mb-3 ps-3">
            <li class="mb-2">
              <strong>Purpose</strong><br>
              This form is for disclosing inventions, creative works, or other intellectual property created by students, faculty, staff, or collaborators of the Polytechnic University of the Philippines.
            </li>
            <li class="mb-2">
              <strong>Accuracy</strong><br>
              All information provided is true and complete to the best of your knowledge.
            </li>
            <li class="mb-2">
              <strong>Confidentiality</strong><br>
              Submitted information may be reviewed internally for IP evaluation and protection.
            </li>
            <li class="mb-2">
              <strong>Compliance</strong><br>
              You agree to comply with PUP policies, national IP laws, and related regulations.
            </li>
            <li class="mb-2">
              <strong>Authorization</strong><br>
              You authorize PUP to process the data for evaluation, filing, and administrative purposes.
            </li>
          </ol>
          <div class="d-flex align-items-center flex-wrap gap-3">
            <div class="form-check m-0">
              <input class="form-check-input" type="checkbox" id="termsAgree">
              <label class="form-check-label fw-semibold" for="termsAgree">
                I accept and agree to the Terms &amp; Conditions.
              </label>
            </div>
            <button type="button" id="termsNext" class="btn btn-primary btn-sm px-4" disabled>Next</button>
          </div>
        </div>
      </div>

      <!-- Form Section (hidden until acceptance) -->
      <div id="formSection" class="d-none">
        <h1 class="fw-bold">Submission Form <small class="text-danger fw-normal" style="font-size:.55em;">(Student)</small></h1>
        <p class="text-muted small">Please fill out the form below to submit your intellectual property for evaluation and processing. All fields marked with an asterisk (*) are required.</p>
        <div class="card shadow-sm">
          <div class="card-body">
            <!-- Student Information  -->
            <?php $perFileMB = 50; ?>
            <form id="submissionForm" method="POST" action="submit-form.php" enctype="multipart/form-data" data-file-max-mb="<?php echo (int)$perFileMB; ?>">
              <?php if (function_exists('csrf_input')) { csrf_input(); } ?>
              <!-- NEW hidden acceptance flag -->
              <input type="hidden" name="accepted_terms" id="accepted_terms" value="">
              <h2 class="section-heading mb-3">Student Information</h2>
              <fieldset>
                <legend>Personal Details</legend>
                <div class="row g-3">
                  <div class="col-md-3">
                    <label class="form-label required">First name</label>
                    <input type="text" name="first_name" class="form-control" 
                   value="<?php echo htmlspecialchars($profile['first_name']); ?>" readonly>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Middle Name</label>
                    <input type="text" name="middle_name" class="form-control" 
                   value="<?php echo htmlspecialchars($profile['middle_name']); ?>" readonly>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Last name</label>
                    <input type="text" name="last_name" class="form-control" 
                   value="<?php echo htmlspecialchars($profile['last_name']); ?>" readonly>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Student Number</label>
                    <input type="text" name="student_number" class="form-control" 
                   value="<?php echo htmlspecialchars($profile['student_number']); ?>" readonly>
                  </div>
                </div>
                
              </fieldset>
              <fieldset>
                <legend>Contact</legend>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">Home Address</label>
                    <input type="text" name="home_address" class="form-control" 
                   value="<?php echo htmlspecialchars($profile['home_address']); ?>" readonly>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Mobile Number</label>
                    <input type="tel" name="mobile_number" class="form-control" 
                   value="<?php echo htmlspecialchars($profile['mobile_number']); ?>" readonly>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">PUP Webmail</label>
                    <input type="email" name="webmail" class="form-control" 
                           value="<?php echo htmlspecialchars($profile['email']); ?>" readonly>
                    <small class="form-text text-muted">
                        Students: 2020-00000-XX-0@iskolar.pup.edu.ph<br>
                    </small>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <legend>Academic Affiliation</legend>
                <div class="row g-3">
                  <div class="col-md-4">
                    <label class="form-label required">Campus</label>
                    <?php $campusVal = trim((string)($profile['campus'] ?? '')); ?>
                    <?php if ($campusVal !== ''): ?>
                      <select id="campus" class="form-select" disabled data-fixed="1">
                        <option value="<?php echo htmlspecialchars($campusVal, ENT_QUOTES, 'UTF-8'); ?>" selected><?php echo htmlspecialchars($campusVal, ENT_QUOTES, 'UTF-8'); ?></option>
                      </select>
                      <input type="hidden" name="campus" value="<?php echo htmlspecialchars($campusVal, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php else: ?>
                      <select name="campus" id="campus" class="form-select" required>
                        <option value="" disabled selected>Choose...</option>
                      </select>
                    <?php endif; ?>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Academic Level</label>
                    <?php $acadLevelVal = trim((string)($profile['academic_level'] ?? '')); ?>
                    <?php if ($acadLevelVal !== ''): ?>
                      <select id="academicLevel" class="form-select" disabled data-fixed="1">
                        <option value="<?php echo htmlspecialchars($acadLevelVal, ENT_QUOTES, 'UTF-8'); ?>" selected><?php echo htmlspecialchars($acadLevelVal, ENT_QUOTES, 'UTF-8'); ?></option>
                      </select>
                      <input type="hidden" name="academicLevel" value="<?php echo htmlspecialchars($acadLevelVal, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php else: ?>
                      <select name="academicLevel" id="academicLevel" class="form-select" required>
                        <option value="" disabled selected>Choose...</option>
                        <!-- Will be populated from JS -->
                      </select>
                    <?php endif; ?>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">College</label>
                    <?php $collegeVal = trim((string)($profile['college'] ?? '')); ?>
                    <?php if ($collegeVal !== ''): ?>
                      <select id="college" class="form-select" disabled data-fixed="1">
                        <option value="<?php echo htmlspecialchars($collegeVal, ENT_QUOTES, 'UTF-8'); ?>" selected><?php echo htmlspecialchars($collegeVal, ENT_QUOTES, 'UTF-8'); ?></option>
                      </select>
                      <input type="hidden" name="college" value="<?php echo htmlspecialchars($collegeVal, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php else: ?>
                      <select name="college" id="college" class="form-select" required>
                        <option value="" disabled selected>Choose...</option>
                        <!-- Will be populated from JS -->
                      </select>
                    <?php endif; ?>
                  </div>
                </div>
                <div class="row g-3 mt-2">
                  <div class="col-md-6">
                    <label class="form-label required">Program</label>
                    <?php $programVal = trim((string)($profile['program'] ?? '')); ?>
                    <?php if ($programVal !== ''): ?>
                      <select id="program" class="form-select" disabled data-fixed="1">
                        <option value="<?php echo htmlspecialchars($programVal, ENT_QUOTES, 'UTF-8'); ?>" selected><?php echo htmlspecialchars($programVal, ENT_QUOTES, 'UTF-8'); ?></option>
                      </select>
                      <input type="hidden" name="program" value="<?php echo htmlspecialchars($programVal, ENT_QUOTES, 'UTF-8'); ?>">
                    <?php else: ?>
                      <select name="program" id="program" class="form-select" required>
                        <option value="" disabled selected>Choose...</option>
                        <!-- Will be populated from JS -->
                      </select>
                    <?php endif; ?>
                  </div>
                  <div class="col-md-4">
                    <label class="form-label required">Work Classification</label>
                    <select name="workClassification" id="workClassification" class="form-select" required>
                      <option value="" disabled selected>Choose...</option>
                      <!-- Will be populated from JS -->
                    </select>
                    <small class="text-muted">Type O (Original), Type A (Adaptation), Type B (Based on Public Domain)</small>
                  </div>
                </div>
              </fieldset>
              <!-- Document Information -->
              <h2 class="section-heading">Document Information</h2>
              <fieldset>
                <legend>Work Metadata</legend>
                <div class="row g-3">
                  <div class="col-md-6">
                    <label class="form-label required">Title</label>
                    <input type="text" name="title" class="form-control" required>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label">Author(s)</label>
                    <div id="authorsList" class="mb-2 small text-muted">No authors added yet.</div>
                    <div id="authorsHidden"></div>
                    <button type="button" id="addAuthorBtn" class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#authorModal">+ Add Author</button>
                    <small class="text-muted d-block mt-1">Use the button to add each author.</small>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Adviser</label>
                    <input type="text" name="adviser" class="form-control required" placeholder="Add an Adviser" required>
                    <div class="form-check mt-2">
                      <input class="form-check-input" type="checkbox" name="adviser_Coauthor" id="adviser_Coauthor">
                      <label class="form-check-label" for="adviser_Coauthor">
                        Adviser is a Co-author
                      </label>
                    </div>
                  </div>
                  <div class="col-md-3">
                    <label class="form-label required">Date Accomplished</label>
                    <input type="date" name="date_accomplished" class="form-control" required max="<?php echo date('Y-m-d'); ?>">
                    <small class="text-muted">Finalization date.</small>
                  </div>
                </div>
              </fieldset>
              <fieldset>
                <legend>Upload Documents (PDF)</legend>
                <div class="row g-3" id="dynamicDocuments"></div>
                <small class="text-muted d-block mt-2">All PDFs must be clear, complete, and properly signed where applicable.</small>
              </fieldset>
              <div class="text-center mt-4">
                <button type="submit" class="btn btn-success px-5">Submit</button>
              </div>
            </form>
            <?php
              // Optional: display upload requirements
              $upl = __DIR__ . '/../../includes/upload_helpers.php';
              if (file_exists($upl)) {
                require_once $upl;
                echo display_upload_requirements();
              }
            ?>
          </div>
        </div>
        </div>
      </div>
    </div>
  </main>

  <!-- Author Modal -->
  <div class="modal fade" id="authorModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Add Co-Author</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <form id="authorForm" class="needs-validation" novalidate>
          <!-- Name Fields -->
          <div class="row g-3 mb-3">
            <div class="col-md-4">
              <label class="form-label">First Name</label>
              <input type="text" name="first_name" class="form-control">
            </div>
            <div class="col-md-4">
              <label class="form-label">Middle Name</label>
              <input type="text" name="middle_name" class="form-control" maxlength="50">
            </div>
            <div class="col-md-4">
              <label class="form-label">Last Name</label>
              <input type="text" name="last_name" class="form-control">
            </div>
          </div>

          <!-- Student Number -->
          <div class="mb-3">
            <label class="form-label">Student Number</label>
            <input type="text" name="student_id" class="form-control"
                   pattern="\d{4}-\d{5}-[A-Z]{2}-\d{1}">
            <div class="form-text">Format: YYYY-XXXXX-MN-0</div>
          </div>

          <!-- Contact Details -->
          <div class="mb-3">
            <label class="form-label">Mobile Number</label>
            <input type="tel" name="mobile" class="form-control"
                   pattern="^09\d{9}$">
            <div class="form-text">Format: 09XXXXXXXXX</div>
          </div>

          <div class="mb-3">
            <label class="form-label">Home Address</label>
            <input type="text" name="home_address" class="form-control">
          </div>

          <div class="mb-3">
            <label class="form-label">PUP Webmail</label>
            <input type="email" name="webmail" class="form-control">
            <div class="form-text">Format: firstnamelastname@iskolarngbayan.pup.edu.ph</div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveAuthorBtn">Add Author</button>
      </div>
    </div>
  </div>
</div>


  
  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>
  
 <!-- Load external JS files compliant with Content Security Policy -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
 <script src="<?php echo asset_url('javascript/forms/terms-accept.js'); ?>"></script>
 <script src="<?php echo asset_url('javascript/forms/author-modal.js'); ?>"></script>
 <script src="<?php echo asset_url('javascript/forms/student-academic-dropdowns.js'); ?>"></script>
 <script nonce="<?php echo SecurityHeaders::getCSPNonce(); ?>">
   window.USER_ROLE = 'student';
   window.CATALOGS_API_URL = '<?php echo rtrim(asset_url('catalogs_public_api.php'), '/'); ?>';
 </script>
 <script src="<?php echo asset_url('javascript/forms/dynamic-documents.js'); ?>" defer></script>
 <script src="<?php echo asset_url('javascript/forms/upload-size-guard.js'); ?>" defer></script>
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