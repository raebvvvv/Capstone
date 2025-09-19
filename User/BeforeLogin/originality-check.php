<?php require_once dirname(__DIR__, 2) . '/public_bootstrap.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Originality Check | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?= APP_BASE_URL ?>/Photos/pup-logo.png">
  <link rel="stylesheet" href="<?= APP_BASE_URL ?>/css/main.css">
  <link rel="stylesheet" href="<?= APP_BASE_URL ?>/css/originality-check.css">
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-white border-bottom">
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
          <li class="nav-item"><a class="nav-link nav-bold" href="<?= APP_BASE_URL ?>/index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link nav-bold" href="<?= APP_BASE_URL ?>/user/BeforeLogin/about.php">About Us</a></li>
        </ul>
        <a href="<?= APP_BASE_URL ?>/user/BeforeLogin/before-e-services.php" class="btn btn-success ms-3">Proceed to e-Services</a>
      </div>
    </div>
  </nav>

  <main class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
      <h2 class="fw-bold mb-0">How to get Originality Check Certificate?</h2>
  <a href="<?= APP_BASE_URL ?>/user/BeforeLogin/before-e-services.php" style="color:black;" class="text-decoration-none">&#x21B6; Back</a>
    </div>
    <p class="mb-2 fs-5">IPMO offers Originality Checking through 
  <img src="<?= APP_BASE_URL ?>/Photos/Icons/turnitin-icon.png" alt="Turnitin" height="40" width="120" style="vertical-align:middle;">
    </p>
    <h4 class="fw-bold mt-4 mb-3">FAQs and Guide:</h4>
    <div class="bg-light rounded-3 p-3 mb-4">
      <div class="d-flex align-items-center mb-2">
  <img src="<?= APP_BASE_URL ?>/Photos/Icons/Originality-icon.png" alt="Originality Icon" height="45" width="45" class="me-2">
        <span class="fw-bold">Is acquiring an Originality Check Certificate through IPMO mandatory?</span>
      </div>
      <div>
        Acquiring an Originality Check Certificate from IPMO is not required/optional, except for PUP Graduate School. Below are the similarity percentage required for the Originality Check Certificate:
        <ul class="mt-2 mb-0">
          <li><b>15% and below: Graduate School (Doctorate and Masteral)</b></li>
          <li><b>20% and below: Undergraduate Degree</b></li>
        </ul>
      </div>
    </div>
    <div class="bg-light rounded-3 p-3 mb-4">
      <div class="d-flex align-items-center mb-2">
  <img src="<?= APP_BASE_URL ?>/Photos/Icons/desk-icon.png" alt="Desk Icon" height="40" width="42" class="me-2">
        <h4 class="fw-bold">How does the Originality Checking Process work?</h4>
      </div>
      <div class="mb-3">
        <div class="bg-white rounded shadow-sm p-3 mb-3">
          <span class="fw-bold">1. Submission to Adviser</span>
          <div class="ms-3 text-muted" style="font-size:0.98em;">
            The applicants submit their draft (e.g., thesis, dissertation, or major paper) to their assigned adviser.
          </div>
        </div>
        <div class="bg-white rounded shadow-sm p-3 mb-3">
          <span class="fw-bold">2. Adviser Conducts Originality Check</span>
          <div class="ms-3 text-muted" style="font-size:0.98em;">
            Using <b>Turnitin</b>, a plagiarism detection software, the adviser reviews the document and records the results in the Originality Check Form.
          </div>
          <a href="#" class="btn btn-danger btn-sm mt-2 ms-3">Originality Check Form</a>
        </div>
        <div class="bg-white rounded shadow-sm p-3 mb-3">
          <span class="fw-bold">3. Evaluation and Feedback</span>
          <div class="ms-3 text-muted" style="font-size:0.98em;">
            The adviser assesses the similarity report. If issues are found, the student may be asked to revise and resubmit. Once the work meets the originality standards, the adviser signs and validates the form.
          </div>
        </div>&nbsp;
        <div class="text-danger mt-2" style="font-size:0.98em;">
        <b>Steps 4 and 5 are only for those who wish to, and are required to acquire an Originality Check Certificate from PUP IPMO. </b>   
      </div> &nbsp;
        <div class="bg-white rounded shadow-sm p-3 mb-3">
          <span class="fw-bold">4. Payment of P500.00 through PUP Cashier</span>
          <div class="ms-3 text-muted" style="font-size:0.98em;">
            A fee of P500.00 is required for the Originality Check Certificate. An official receipt must be secured as part of the certificate application requirements.
          </div>
        </div>
        <div class="bg-white rounded shadow-sm p-3">
          <span class="fw-bold">5. Certificate Issuance by IPMO</span>
          <div class="ms-3 text-muted" style="font-size:0.98em;">
            Only the IPMO can issue the Originality Check Certificate, which is required for final submission and clearance.<br>
            <span class="fw-bold">Present the following upon arrival at PUP IPMO:</span>
            <ul class="mt-2 mb-0">
              <li>Official Receipt of P500.00</li>
              <li>Originality Check Form accomplished and validated by Adviser</li>
            </ul>
          </div>
        </div>
      </div>
    </div>
  </main>
  <footer class="text-center mt-5">
    <p>
      For any questions or concerns, feel free to contact us via:<br>
      📧 ipmo@pup.edu.ph &nbsp; 📞 (+632) 5335-1787
    </p>
    <small>
      © 2025 Polytechnic University of the Philippines | 
      <a href="https://www.pup.edu.ph/terms" class="footer-link-blue" target="_blank">Terms of Service</a> | 
      <a href="https://www.pup.edu.ph/privacy" class="footer-link-blue" target="_blank">Privacy Statement</a>
    </small>
  </footer>
  <!-- Bootstrap JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>