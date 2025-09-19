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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/after-originality-check.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <?php include __DIR__ . '/../../partials/navbar_afterlogin_fallback.php'; ?>
  <div class="container d-flex justify-content-end mt-3 mb-2">
    <?php if (function_exists('render_back_link')) { render_back_link('index.php'); } ?>
  </div>
  <main class="container pb-5">
    <h2 class="fw-bold mt-2 mb-3">How to get Originality Check Certificate?</h2>
    <p class="fs-5 mb-2">
      IPMO offers Originality Checking through 
      <a href="https://www.turnitin.com/" target="_blank">
        <img src="<?php echo asset_url('Photos/Icons/turnitin-icon.png'); ?>" alt="Turnitin" class="turnitin-logo" />
      </a>
    </p>
    <h4 class="fw-bold mb-3">FAQs and Guide:</h4>
    <section class="card shadow-sm guide-card mb-4">
      <div class="card-body d-flex align-items-center flex-wrap">
        <img src="<?php echo asset_url('Photos/Icons/requirement-icon.png'); ?>" alt="FAQ Icon" class="step-icon me-3" />
        <div>
          <h5 class="fw-bold mb-2">Is acquiring an Originality Check Certificate through IPMO mandatory?</h5>
          <p class="mb-0">Acquiring an Originality Check Certificate from IPMO is optional except for PUP Graduate School. Required similarity thresholds:</p>
          <ul class="mb-0">
            <li><strong>15% and below:</strong> Graduate School (Doctorate & Masteral)</li>
            <li><strong>20% and below:</strong> Undergraduate Degree</li>
          </ul>
        </div>
      </div>
    </section>
    <section class="card shadow-sm guide-card mb-4">
      <div class="card-body d-flex align-items-center flex-wrap pb-0">
        <img src="<?php echo asset_url('Photos/Icons/desk-icon.png'); ?>" alt="Submission to Adviser" class="step-icon me-3" />
        <div>
          <h5 class="fw-bold mb-2">How does the Originality Checking Process work?</h5>
          <div class="step-box mt-3 mb-3">
            <div class="step-item mb-3">
              <div class="step-title fw-bold mb-1"><span class="step-number">1.</span> Submission to Adviser</div>
              <div class="step-desc">Applicant submits draft (thesis, dissertation, major paper) to adviser.</div>
            </div>
            <div class="step-item mb-3">
              <div class="step-title fw-bold mb-1"><span class="step-number">2.</span> Adviser Conducts Originality Check</div>
              <div class="step-desc">Using <strong>Turnitin</strong>, the adviser reviews similarity and records results in the form.<br>
                <a href="originality-form.php" class="btn btn-originality btn-sm mt-2">Originality Check Form</a>
              </div>
            </div>
            <div class="step-item mb-3">
              <div class="step-title fw-bold mb-1"><span class="step-number">3.</span> Evaluation & Feedback</div>
              <div class="step-desc">Adviser may request revisions until acceptable similarity score is achieved.</div>
            </div>
            <div class="step-desc fw-bold text-danger" style="font-weight:500;">Steps 4 and 5 apply only to those required to obtain an IPMO-issued certificate.</div>
            <div class="step-item mb-3">
              <div class="step-title fw-bold mb-1"><span class="step-number">4.</span> Payment (P500.00) via PUP Cashier</div>
              <div class="step-desc">Secure official receipt—required for certificate issuance.</div>
            </div>
            <div class="step-item mb-1">
              <div class="step-title fw-bold mb-1"><span class="step-number">5.</span> Certificate Issuance by IPMO</div>
              <div class="step-desc">Present Official Receipt and validated Originality Check Form at IPMO for certificate release.</div>
            </div>
          </div>
        </div>
      </div>
    </section>
  </main>

  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo asset_url('javascript/after-originality-check.js'); ?>"></script>
</body>
</html>
