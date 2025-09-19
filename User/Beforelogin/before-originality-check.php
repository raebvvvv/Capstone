<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>How to get Originality Check Certificate | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/originality-check-before.css?v=5'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css?v=5'); ?>">
</head>
<body>
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
        </ul>
      </div>
    </div>
  </nav>

  <!-- Back Button Below Navbar, scrolls with content, transparent background -->
  <div class="container d-flex justify-content-end mt-3 mb-2">
    <?php if (function_exists('render_back_link')) { 
      render_back_link('index.php'); 
    } ?>
  </div>

  <main class="container pb-5">
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
                <a href="originality-form.php" class="btn btn-originality btn-sm mt-2">Originality Check Form</a>
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

  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?> 

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="javascript/originality-check.js"></script>
</body>
</html>