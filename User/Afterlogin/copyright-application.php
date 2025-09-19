<?php require __DIR__ . '/../../config.php'; ?>
<?php require __DIR__ . '/../../auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Copyright Application | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="Photos/pup-logo.png">
  <link rel="stylesheet" href="<?php echo asset_url('css/copyright-application.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="../../index.php">
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
          <li class="nav-item"><a class="nav-link" href="student-profile.php">My Profile</a></li>
        </ul>
          <a href="e-services.php" class="btn btn-success ms-3">Proceed to e-Services</a>
          <form method="POST" action="<?php echo asset_url('User/Beforelogin/logout.php'); ?>" class="d-inline ms-2">
            <?php if (function_exists('csrf_input')) { csrf_input(); } ?>
            <button type="submit" class="btn btn-danger">Logout</button>
          </form>
      </div>
    </div>
  </nav>
   
  <!-- Back Button Below Navbar, scrolls with content, transparent background -->
    <div class="container d-flex justify-content-end mt-3 mb-2">
        <?php if (function_exists('render_back_link')) { render_back_link('User/Afterlogin/e-services.php'); } ?>
      </div>

  <main class="container py-4">

    <div class=" justify-content-between mb-4">
      <h2 class="fw-bold mb-0">Copyright Application</h2>

    </div>

    <ol>
      <li class="mb-4">
        This ZIP file contains clean copies of all the files listed below.<br>
        Files <strong>required</strong> to be printed have <span class="text-success fw-bold">'[Print]'</span> in their filenames.<br>
        <a href="#" class="btn btn-primary mt-2 mb-2" style="width:180px;">Copyright.zip</a>
      </li>
      <li class="mb-4">
        Once printed and accomplished, you may proceed to the <strong>submission form.</strong><br>
        <a href="student-copyright-forms.php" class="btn submission-btn mt-2 mb-2" style="width:180px;">Submission Form</a>
      </li>
    </ol>
    <p class="text-muted mb-3" style="font-size:0.95em;">
      You may view and download the files individually, but the ZIP file ensures you don’t miss any.
    </p>
    <div class="file-list">
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">1. Copyright Procedure</span>
        </div>
        <div class="file-actions">
          <a href="#" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="#" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">2. Copyright Application Form <span class="text-success">[Print]</span></span><br>
          <span style="font-size:0.95em;">For Multiple or Single Authorship</span>
        </div>
        <div class="file-actions">
          <a href="#" class="btn view-btn btn-sm mb-1">View File</a>
          <a href="#" class="btn download-btn btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">3. Copyright Co-Authorship Agreement <span class="text-success">[Print]</span></span>
        </div>
        <div class="file-actions">
          <a href="#" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="#" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">4. Memorandum Order</span><br>
          <span style="font-size:0.95em;">No. 031, Series of 2013</span><br>
          <span style="font-size:0.95em;">Policy on Copyright Registration of Undergraduate and Graduate Academic Works</span>
        </div>
        <div class="file-actions">
          <a href="#" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="#" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
    </div>
  </main>
   <p me-3-3 class="text-center text-muted" style="font-size:0.9em;">
      For any questions or concerns, feel free to contact us via:<br>
      📧 ipmo@pup.edu.ph &nbsp; 📞 (+632) 5335-1787
    </p>
    
    <!-- Footer -->
    <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>

    <!-- Bootstrap JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>