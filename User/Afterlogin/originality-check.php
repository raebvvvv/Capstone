<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Originality Check Guide | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
  <link rel="icon" type="image/png" href="../../Photos/pup-logo.png">
  <link rel="stylesheet" href="../../css/originality-check-before.css?v=6">
  <link rel="stylesheet" href="../../css/main.css?v=5">
</head>
<body>
    <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="../../index.php">
  <img src="../../Photos/pup-logo.png" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <?php $isEmployee = (($_SESSION['role'] ?? '') === 'employee'); ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo $isEmployee ? 'employee-application.php' : 'student-application.php'; ?>">My Application</a></li>
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
        
          <a href="e-services.php" class="btn btn-success ms-3" style="background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Proceed to e-Services</a>
      </div>
    </div>
  </nav>
  <div class="container d-flex justify-content-end mt-3 mb-2">
    <?php if (function_exists('render_back_link')) { render_back_link('User/Afterlogin/e-services.php'); } ?>
  </div>
  <main class="container-fluid pb-5" style="max-width: 1400px; margin: 0 auto; padding-left: 2.5rem; padding-right: 2.5rem;">
    <h2 class="fw-bold mt-2 mb-3">How to get Originality Check Certificate?</h2>
    <p class="fs-5 mb-2">
      IPMO offers Originality Checking through 
      <a href="https://www.turnitin.com/" target="_blank">
  <img src="<?php echo asset_url('Photos/Icons/turnitin-icon.png'); ?>" alt="Turnitin" class="turnitin-logo">
      </a>
    </p>
    <h4 class="fw-bold mb-3">FAQs and Guide:</h4>

    <!-- FAQ Card -->
    <section class="card shadow-sm guide-card mb-4">
      <div class="card-body d-flex align-items-center flex-wrap">
  <img src="<?php echo asset_url('Photos/Icons/requirement-icon.png'); ?>" alt="FAQ Icon" class="step-icon me-3">
        <div>
          <h5 class="fw-bold mb-2">Is acquiring an Originality Check Certificate through IPMO mandatory?</h5>
          <p class="mb-0">Acquiring an Originality Check Certificate from IPMO is not required/optional, except for PUP Graduate School. Below are the similarity percentage required for the Originality Check Certificate:</p>
          <ul class="mb-0">
            <li><strong>15% and below:</strong> Graduate School (Doctorate and Masteral)</li>
            <li><strong>20% and below:</strong> Undergraduate Degree</li>
          </ul>
        </div>
      </div>
    </section>

    <!-- Process Section -->
    <section class="card shadow-sm guide-card mb-4">
      <div class="card-body d-flex align-items-center flex-wrap pb-0">
  <img src="<?php echo asset_url('Photos/Icons/desk-icon.png'); ?>" alt="Submission to Adviser" class="step-icon me-3">
        <div>
          <h5 class="fw-bold mb-2">How does the Originality Checking Process work?</h5>
          <div class="step-box mt-3 mb-3">
            <div class="step-item mb-3">
              <div class="step-title fw-bold mb-1"><span class="step-number">1.</span> Submission to Adviser</div>
              <div class="step-desc">The applicants submits their draft (e.g., thesis, dissertation, or major paper) to their assigned adviser.</div>
            </div>
            <div class="step-item mb-3">
              <div class="step-title fw-bold mb-1"><span class="step-number">2.</span> Adviser Conducts Originality Check</div>
              <div class="step-desc">
                Using <strong>Turnitin</strong>, a plagiarism detection software, the adviser reviews the document and records the results in the Originality Check Form.<br>
                <a href="https://drive.google.com/uc?export=download&id=1tMiLMefbv2VjzbQfrbbi9-oZCmpCHqCW" class="btn btn-originality btn-sm mt-2">Originality Check Form</a>
              </div>
            </div>
            <div class="step-item mb-3">
              <div class="step-title fw-bold mb-1"><span class="step-number">3.</span> Evaluation and Feedback</div>
              <div class="step-desc">
                The adviser assesses the similarity report. If issues are found, the student may be asked to revise and resubmit. Once the work meets the originality standards, the adviser signs and validates the form.
              </div>
            </div>

              <div class="step-desc fw-bold text-danger" style="font-weight: 500;">
                Steps 4 and 5 are only for those who wish to, and are required to acquire an Originality Check Certificate from PUP IPMO.
              </div>

            <div class="step-item mb-3">
              <div class="step-title fw-bold mb-1"><span class="step-number">4.</span> Payment of P500.00 through PUP Cashier</div>
              <div class="step-desc">
                A fee of P500.00 is required for the Originality Check Certificate. An official receipt must be secured as part of the certificate application requirements.
              </div>
            </div>
            <div class="step-item mb-1">
              <div class="step-title fw-bold mb-1"><span class="step-number">5.</span> Certificate Issuance by IPMO</div>
              <div class="step-desc">
                Only the IPMO can issue the Originality Check Certificate, which is required for final submission and clearance.<br>
                Present the following upon arrival at PUP IPMO:
                <ul class="mt-2 mb-1">
                  <li><span class="text-dark fw-bold">Official Receipt of P500.00</span></li>
                  <li><span class="text-dark fw-bold">Originality Check Form accomplished and validated by Adviser</span></li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>


  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
  <script src="../../javascript/after-originality-check.js"></script>
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
