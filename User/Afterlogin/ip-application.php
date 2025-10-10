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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
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
              <div class="fw-bold text-danger">Pending Tab</div>
              <div class="ms-3 mt-2">
                <div class="fw-bold">Remarks: <span class="text-danger">For Evaluation</span></div>
                <div class="small mb-2">
                  Upon submission, your application will be subject for evaluation.
                </div>
                
                <div class="fw-bold">Remarks: <span class="text-danger">Pending Review</span></div>
                <div class="small mb-2">
                  Your resubmitted files is currently pending and under review.
                </div>
                
                <div class="fw-bold">Remarks: <span class="text-danger">Incorrect Document/Upload</span>, <span class="text-danger">Error in Document/Upload</span></div>
                <div class="small mb-2">
                  Read comments regarding your application by clicking the <b>Comments</b> button. Upload the necessary files that will need to be resubmitted by clicking the <b>View Details</b> button.
                </div>
                
                <div class="fw-bold">Remarks: <span class="text-danger">Others</span></div>
                <div class="small mb-2">
                  Read comments regarding your application. Other issue/s regarding your application that may or may not require resubmission.
                </div>
              </div>
            </div>

            <!-- Approved Tab -->
            <div class="mt-4">
              <div class="fw-bold text-danger">Approved Tab</div>
              <div class="ms-3 mt-2">
                <div class="fw-bold">Remarks: <span class="text-danger">For Physical Submission</span></div>
                <div class="small mb-2">
                  Approved applications may proceed to submission of hardcopy:<br>
                  <b>Envelope</b> (in your department's designated color) inside the envelope are:
                  <ul class="mb-1">
                    <li>2 copies of application forms with 1 document stamp</li>
                    <li>2 pieces of flashdrives containing the theses</li>
                  </ul>
                  <b>Note:</b> Please show the <b>Request ID</b> to the IPMO staff, you may either show the Request ID directly from website or download the Request ID as PDF file.
                </div>
                
                <div class="fw-bold">Remarks: <span class="text-danger">Missing Document</span>, <span class="text-danger">Error in Document</span>, <span class="text-danger">Documents don't match</span></div>
                <div class="small mb-2">
                  Read comments regarding your application by clicking the <b>Comments</b> button. You may see the documents that has issue by clicking the <b>View Details</b> that may need for resubmission.
                </div>
                
                <div class="fw-bold">Remarks: <span class="text-danger">Others</span></div>
                <div class="small mb-2">
                  Read comments regarding your application. Other issue/s regarding your application that may or may not require resubmission.
                </div>
              </div>
            </div>

            <!-- Completed Tab -->
            <div class="mt-4">
              <div class="fw-bold text-danger">Completed Tab</div>
              <div class="ms-3 mt-2">
                <div class="fw-bold">Remarks: <span class="text-danger">Complete</span></div>
                <div class="small mb-2">
                  Your application has been approved. You may now download your <b>Certificate of Copyright Application</b> by clicking the <b>View Certificate</b> button.
                </div>
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
</body>
</html>
