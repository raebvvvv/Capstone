<?php require_once dirname(__DIR__, 2) . '/public_bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>How do I apply for IP? | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?= APP_BASE_URL ?>/Photos/pup-logo.png">
  <link rel="stylesheet" href="<?= APP_BASE_URL ?>/css/main.css">
  <link rel="stylesheet" href="<?= APP_BASE_URL ?>/css/ip-application.css">

</head>
<body>
  <!-- Navbar (matches index.php) -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
  <a class="navbar-brand d-flex align-items-center" href="<?= APP_BASE_URL ?>/index.php">
  <img src="<?= APP_BASE_URL ?>/Photos/pup-logo.png" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="<?= APP_BASE_URL ?>/user/BeforeLogin/about.php">About Us</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <!-- Main Content -->
  <main class="container py-4">
    <div class="d-flex justify-content-end mb-2">
      <a href="#" onclick="window.history.back()" class="text-dark fs-5 text-decoration-none back-btn-content"><span>&#x21B6;</span> Back</a>
    </div>
    <h2 class="fw-bold mb-2" style="font-size:2rem;">How do I apply for IP?</h2>
    <h5 class="fw-normal mb-4">Below is the IP Application Guide:</h5>
    <!-- Guide Steps -->
    <section class="guide-section">
      <!-- Step 1 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?= APP_BASE_URL ?>/Photos/Icons/security.png" alt="Register Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1 d-flex flex-column flex-md-row w-100">
          <div class="card-desc flex-grow-1">
            <div>
              <span class="step-number">1.</span>
              <span class="step-title fw-bold">Register or Login</span>
            </div>
            <div class="step-desc mb-1">
              To get started, please <b>create an account</b> or <b>login</b> with your existing one. Choose whether you are a <a href="#" class="text-dark text-decoration-underline fw-bold">Student</a> or <a href="#" class="text-dark text-decoration-underline fw-bold">Employee</a>.
            </div>
          </div>
          <div class="step-actions d-flex flex-column justify-content-center align-items-md-end align-items-start ms-md-3 mt-3 mt-md-0">
            <div class="mb-2 small text-center">Click here to go to the Registration/Login form.</div>
            <div class="d-flex gap-2">
              <a href="<?= APP_BASE_URL ?>/login.php" class="btn btn-danger btn-sm fw-bold px-4">STUDENT</a>
              <a href="<?= APP_BASE_URL ?>/login.php" class="btn btn-danger btn-sm fw-bold px-4">EMPLOYEE</a>
            </div>
          </div>
        </div>
      </div>
      <!-- Step 2 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?= APP_BASE_URL ?>/Photos/Icons/choice.png" alt="Choose Service Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1 d-flex flex-column flex-md-row w-100">
          <div class="card-desc flex-grow-1">
            <div>
              <span class="step-number">2.</span>
              <span class="step-title fw-bold">Choose an e-Service to apply for</span>
            </div>
            <div class="step-desc mb-1">
              In the dashboard, choose an e-Service to apply for.<br>
              Note that you still have to <a href="#" class="text-dark text-decoration-underline fw-bold">login first</a> in order to have <a href="#" class="text-dark text-decoration-underline fw-bold">access to the submission form.</a>
            </div>
          </div>
          <div class="step-actions d-flex flex-column justify-content-center align-items-md-end align-items-start ms-md-3 mt-3 mt-md-0">
            <div class="mb-2 small text-center">Click here for a preview of the IP Application forms.</div>
            <a href="<?= APP_BASE_URL ?>/user/BeforeLogin/before-e-services.php" class="btn btn-warning btn-sm fw-bold px-4">Go to e-Services</a>
          </div>
        </div>
      </div>
      <!-- Step 3 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?= APP_BASE_URL ?>/Photos/Icons/online-library.png" alt="Read Guide Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div>
            <span class="step-number">3.</span>
            <span class="step-title fw-bold">Read the guide and requirements</span>
          </div>
          <div class="step-desc mb-1">
            Read the guide and requirements for the IP you selected.<br>
            <b>Prepare the requirements.</b>
          </div>
        </div>
      </div>
      <!-- Step 4 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?= APP_BASE_URL ?>/Photos/Icons/attachment.png" alt="Download Forms Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div>
            <span class="step-number">4.</span>
            <span class="step-title fw-bold">Download and accomplish the forms</span>
          </div>
          <div class="step-desc mb-1">
            A preview of the forms is provided.<br>
            <b>Download</b> and fill up the required information.
          </div>
        </div>
      </div>
      <!-- Step 5 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?= APP_BASE_URL ?>/Photos/Icons/online.png" alt="Submission Form Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div>
            <span class="step-number">5.</span>
            <span class="step-title fw-bold">Proceed to the Submission Form</span>
          </div>
          <div class="step-desc mb-1">
            Fill in the required information inside the submission form.<br>
            Attach the necessary <b>PDF files</b> in their respective dropboxes.
          </div>
        </div>
      </div>
      <!-- Step 6 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?= APP_BASE_URL ?>/Photos/Icons/complaint.png" alt="Evaluation Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div>
            <span class="step-number">6.</span>
            <span class="step-title fw-bold">Application will be subject to evaluation</span>
          </div>
          <div class="step-desc mb-1">
            Upon submission, your application will be subject for evaluation.
            <div class="mt-2 ms-1">
              <div class="fw-bold">6.1 Remarks: <span class="text-danger">Incomplete</span></div>
              <div class="small">
                Read comments regarding your application.<br>
                <span class="fw-bold">"Incomplete"</span> can vary from <span class="fw-bold">typographical errors or missing/incorrect information or document, etc.</span>
              </div>
              <div class="fw-bold mt-2">6.2 Remarks: <span class="text-success">Approved</span></div>
              <div class="small">
                Approved applications may proceed to submission of hardcopy:<br>
                <b>Envelope</b> (in your department's designated color) inside the envelope are:
                <ul class="mb-1">
                  <li>2 copies of application forms with 1 document stamp</li>
                  <li>2 pieces of flashdrives containing the theses</li>
                </ul>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>

  <!-- Footer -->
  <footer class="bg-white border-top py-3 mt-4">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none" target="_blank">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none" target="_blank">Privacy Statement</a>
    </div>
  </footer> 

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= APP_BASE_URL ?>/javascript/ip-application.js"></script>
</body>
</html>