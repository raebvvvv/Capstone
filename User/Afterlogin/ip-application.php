<?php 
// === PHP: Load Config & Auth Guard ===
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php'; // Enforce authenticated session for after-login pages
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>IP Application Guide | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />

  <!-- === CSS: Bootstrap Framework === -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />

  <!-- === HTML: Favicon === -->
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">

  <!-- === CSS: Custom Styles === -->
  <link rel="stylesheet" href="<?php echo asset_url('css/after-ip-application.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <!-- ========================== HTML: NAVBAR (After Login) ========================== -->
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
          <li class="nav-item"><a class="nav-link" href="employee-application.php">My Application</a></li>
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
        <a href="e-services.php" class="btn btn-success ms-3" style="background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Proceed to e-Services</a>
        <!-- Logout button triggers confirmation modal 
        <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#logoutModal"> 
          Logout
        </button> -->
      </div>
      
    </div>
  </nav>
  <main class="container py-4">
    <!-- HTML: Back link to landing/dashboard -->
    <div class="d-flex justify-content-end mb-2">
      <?php if (function_exists('render_back_link')) { render_back_link('index.php', '↶ Back', 'text-dark fs-5 text-decoration-none back-btn-content'); } ?>
    </div>
    <h2 class="fw-bold mb-2" style="font-size:2rem;">How do I apply for IP?</h2>
    <h5 class="fw-normal mb-4">Below is the IP Application Guide:</h5>
    <!-- ========================== HTML: GUIDE SECTION ========================== -->
    <section class="guide-section">
      <!-- Steps replicated from legacy after-ip-application.php -->
      <?php /* Keeping same structure for now; consider extracting to a partial if reused elsewhere. */ ?>
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/security.png'); ?>" alt="Register Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1 d-flex flex-column flex-md-row w-100">
          <div class="card-desc flex-grow-1">
            <div><span class="step-number">1.</span> <span class="step-title fw-bold">Register or Login</span></div>
            <div class="step-desc mb-1">You already have an account and are logged in.</div>
          </div>
          <div class="step-actions d-flex flex-column justify-content-center align-items-md-end align-items-start ms-md-3 mt-3 mt-md-0">
            <div class="mb-1 mt-4 small text-center">You are already logged in</div>
          </div>
        </div>
      </div>
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/choice.png'); ?>" alt="Choose Service Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1 d-flex flex-column flex-md-row w-100">
          <div class="card-desc flex-grow-1">
            <div><span class="step-number">2.</span> <span class="step-title fw-bold">Choose an e-Service to apply for</span></div>
            <div class="step-desc mb-1">In the dashboard, choose an e-Service to apply for.</div>
          </div>
          <div class="step-actions d-flex flex-column justify-content-center align-items-md-end align-items-start ms-md-3 mt-3 mt-md-0">
            <div class="mb-2 small text-center">Only proceed when you have read all the guidelines.</div>
            <button class="btn btn-secondary btn-sm fw-bold px-4" disabled>Go to e-Services</button>
          </div>
        </div>
      </div>
  <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/online-library.png'); ?>" alt="Read Guide Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div><span class="step-number">3.</span> <span class="step-title fw-bold">Read the guide and requirements</span></div>
          <div class="step-desc mb-1">Review the IP-specific requirements and prepare all needed documents.</div>
        </div>
      </div>
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/attachment.png'); ?>" alt="Download Forms Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div><span class="step-number">4.</span> <span class="step-title fw-bold">Download and accomplish the forms</span></div>
          <div class="step-desc mb-1">Download and fill out the required forms before uploading.</div>
        </div>
      </div>
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/online.png'); ?>" alt="Submission Form Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div><span class="step-number">5.</span> <span class="step-title fw-bold">Proceed to the Submission Form</span></div>
          <div class="step-desc mb-1">Fill in required information and attach all PDF files.</div>
        </div>
      </div>
   <!-- Step 6 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/complaint.png'); ?>" alt="Evaluation Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div>
            <span class="step-number">6.</span>
            <span class="step-title fw-bold">Application Status and Remarks</span>
          </div>
          <div class="step-desc mb-1">
            
        <!-- Pending Tab -->
        <div class="mt-3">
          <div class="fw-bold" style="color:#d46a00;">Pending Tab</div>
          <div class="ms-3 mt-2">
            <div class="fw-bold">Remarks: For Evaluation</div>
            <div class="small mb-2">
              Once submitted, your application will be evaluated by the reviewing team.
            </div>
            
            <div class="fw-bold">Remarks: Pending Review</div>
            <div class="small mb-2">
              Your resubmitted files are currently under review.
            </div>
            
            <div class="fw-bold">
              Remarks: 
              <span class="text-danger">Incorrect Document/Upload</span>, 
              <span class="text-danger">Error in Document/Upload</span>
            </div>
            <div class="small mb-2">
              Review the comments regarding your application by clicking the <b>Comments</b> button. Re-upload the required files by selecting the <b>View Details</b> button.
            </div>
            
            <div class="fw-bold">Remarks: Others</div>
            <div class="small mb-2">
              Review the comments regarding your application. This may include other issues that may or may not require resubmission.
            </div>
          </div>
        </div>

        <!-- Approved Tab -->
        <div class="mt-4">
          <div class="fw-bold" style="color:#1a8b1a;">Approved Tab</div>
          <div class="ms-3 mt-2">
            <div class="fw-bold">Remarks: For Physical Submission</div>
            <div class="small mb-2">
              Approved applications may now proceed with the submission of hard copies:<br>
              <b>Envelope</b> (in your department’s designated color) should contain:
              <ul class="mb-1">
                <li><i>Two (2) copies of the application form, each with one documentary stamp</i></li>
                <li><i>Two (2) flash drives containing the thesis files</i></li>
              </ul>
              <b>Note:</b> Please present your <b>Request ID</b> to the IPMO staff. You may either show it directly from the website or download it as a PDF file.
            </div>
            
            <div class="fw-bold">
              Remarks: 
              <span class="text-danger">Missing Document</span>, 
              <span class="text-danger">Error in Document</span>, 
              <span class="text-danger">Documents Don’t Match</span>
            </div>
            <div class="small mb-2">
              Review the comments regarding your application by clicking the <b>Comments</b> button. You can also identify documents with issues by selecting the <b>View Details</b> button to determine which files need to be resubmitted.
            </div>
            
            <div class="fw-bold">Remarks: Others</div>
            <div class="small mb-2">
              Review the comments regarding your application. This may include other concerns that may or may not require resubmission.
            </div>
          </div>
        </div>

        <!-- Completed Tab -->
        <div class="mt-4">
          <div class="fw-bold" style="color:#004080;">Completed Tab</div>
          <div class="ms-3 mt-2">
            <div class="fw-bold">Remarks: Complete</div>
            <div class="small mb-2">
              Your application has been fully approved. You may now download your <b>Certificate of Copyright Application</b> by clicking the <b>View Certificate</b> button.
            </div>
          </div>
        </div>
      </div>
          </div>
        </div>
      </div>
    </section>
  </main>
  <!-- ===================== HTML: Footer ===================== -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>

  <!-- === JS: Bootstrap Scripts === -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
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
