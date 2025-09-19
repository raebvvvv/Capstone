<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php'; // enforce auth
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>IP Application Guide | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/after-ip-application.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <?php include __DIR__ . '/../../partials/navbar_afterlogin_fallback.php'; ?>
  <main class="container py-4">
    <div class="d-flex justify-content-end mb-2">
      <?php if (function_exists('render_back_link')) { render_back_link('index.php', '↶ Back', 'text-dark fs-5 text-decoration-none back-btn-content'); } ?>
    </div>
    <h2 class="fw-bold mb-2" style="font-size:2rem;">How do I apply for IP?</h2>
    <h5 class="fw-normal mb-4">Below is the IP Application Guide:</h5>
    <section class="guide-section">
      <!-- Steps replicated from legacy after-ip-application.php -->
      <?php /* Keeping same structure for now for minimal diff; consider extracting to partial later. */ ?>
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
            <div class="mb-2 small text-center">Proceed when ready.</div>
            <a href="e-services.php" class="btn btn-warning btn-sm fw-bold px-4">Go to e-Services</a>
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
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/complaint.png'); ?>" alt="Evaluation Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div><span class="step-number">6.</span> <span class="step-title fw-bold">Application evaluation</span></div>
          <div class="step-desc mb-1">Your submission will be evaluated; address remarks if flagged Incomplete; Approved applications proceed to physical submission of required hardcopies.</div>
        </div>
      </div>
    </section>
  </main>
  <?php include __DIR__ . '/../../partials/footer_fallback.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo asset_url('javascript/after-ip-application.js'); ?>"></script>
</body>
</html>
