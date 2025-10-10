<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>How do I apply for IP? | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/ip-application.css'); ?>">

</head>
<body>
  <!-- Navbar (matches index.php) -->
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
  <!-- Main Content -->
  <main class="container py-4">
    <div class="d-flex justify-content-end mb-2">
  <?php if (function_exists('render_back_link')) { render_back_link('index.php', '↶ Back', 'text-dark fs-5 text-decoration-none back-btn-content'); } ?>
    </div>
    <h2 class="fw-bold mb-2" style="font-size:2rem;">How do I apply for IP?</h2>
    <h5 class="fw-normal mb-4">Below is the IP Application Guide:</h5>
    <!-- Guide Steps -->
    <section class="guide-section">
      <!-- Step 1 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/security.png'); ?>" alt="Register Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1 d-flex flex-column flex-md-row w-100">
          <div class="card-desc flex-grow-1">
            <div>
              <span class="step-number">1.</span>
              <span class="step-title fw-bold">Register or Login</span>
            </div>
            <div class="step-desc mb-1">
              To get started, please <b>create an account</b> or <b>login</b> with your existing one. Choose whether you are a <a href="#" class="text-dark text-decoration-underline fw-bold">Student</a> or <a href="#" class="text-dark text-decoration-underline fw-bold">Non-student</a>.
            </div>
          </div>
          <div class="step-actions d-flex flex-column justify-content-center align-items-md-end align-items-start ms-md-3 mt-3 mt-md-0">
            <div class="mb-2 small text-center">Click here to go to the Registration/Login form.</div>
            <div class="d-flex gap-2">
              <a href="login.php" class="btn btn-danger btn-sm fw-bold px-4">STUDENT</a>
              <a href="login.php?role=employee" class="btn btn-danger btn-sm fw-bold px-4">EMPLOYEE</a>
            </div>
          </div>
        </div>
      </div>
      <!-- Step 2 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/choice.png'); ?>" alt="Choose Service Icon" class="img-fluid">
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
            <a href="before-e-services.php" class="btn btn-warning btn-sm fw-bold px-4">Go to e-Services</a>
          </div>
        </div>
      </div>
      <!-- Step 3 -->
      <div class="guide-card d-flex flex-wrap flex-xl-nowrap align-items-start mb-4">
        <div class="step-icon">
          <img src="<?php echo asset_url('Photos/Icons/online-library.png'); ?>" alt="Read Guide Icon" class="img-fluid">
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
          <img src="<?php echo asset_url('Photos/Icons/attachment.png'); ?>" alt="Download Forms Icon" class="img-fluid">
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
          <img src="<?php echo asset_url('Photos/Icons/online.png'); ?>" alt="Submission Form Icon" class="img-fluid">
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
          <img src="<?php echo asset_url('Photos/Icons/complaint.png'); ?>" alt="Evaluation Icon" class="img-fluid">
        </div>
        <div class="flex-grow-1">
          <div>
            <span class="step-number">6.</span>
            <span class="step-title fw-bold">Application Status and Remarks</span>
          </div>
          <div class="step-desc mb-1">
          <!-- Pending Tab -->

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

  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?> 

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
  <script src="<?php echo asset_url('javascript/ip-application.js'); ?>"></script>
</body>
</html>