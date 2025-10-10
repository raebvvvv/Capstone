<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Copyright Application | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/before-copyright-application.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  
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
        <?php if (function_exists('render_back_link')) { render_back_link('User/Beforelogin/before-e-services.php'); } ?>
      </div>

  <main class="container py-4">

    <div class=" justify-content-between mb-4">
      <h2 class="fw-bold mb-0">Copyright Application</h2>

    </div>

    <ol>
      <li class="mb-4">
        This ZIP file contains clean copies of all the files listed below.<br>
        Files <strong>required</strong> to be printed have <span class="text-success fw-bold">'[Print]'</span> in their filenames.<br>
        <a href="https://drive.google.com/uc?export=download&id=1njbAmgm2LrUSm2I5NPM9sFgPLbwnP_UH" class="btn btn-primary mt-2 mb-2" style="width:180px; background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Copyright.zip</a>
      </li>
      <li class="mb-4">
        Once printed and accomplished, you may proceed to the <strong>submission form.</strong><br> 
        <a href="login.php" class="btn submission-btn mt-2 mb-2" style="width:180px;">Submission Form</a>
      </li>
    </ol>
    <p class="text-muted mb-3" style="font-size:0.95em;">
      You may view and download the files individually, but the ZIP file ensures you don’t miss any.
    </p>
    <div class="file-list">
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">1. Copyright Application and Procedures</span>
        </div>
        <div class="file-actions">
          <a href="https://drive.google.com/file/d/11KW9MmxtVCzsfsWa1r9ifg-0RBFP-hG-/view?usp=sharing" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://drive.google.com/uc?export=download&id=11KW9MmxtVCzsfsWa1r9ifg-0RBFP-hG-" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">2. Copyright Application Form <span class="text-success">[Print]</span></span><br>
          <span style="font-size:0.95em;">For Multiple or Single Authorship</span>
        </div>
        <div class="file-actions">
          <a href="https://docs.google.com/document/d/1py-jLmvd_jbNyk4fCUZkwBVq28C6F4l6/edit?usp=sharing&ouid=104730774923126836253&rtpof=true&sd=true" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://docs.google.com/document/d/1py-jLmvd_jbNyk4fCUZkwBVq28C6F4l6/export?format=docx" download class="btn download-btn btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">3. Copyright Co-Authorship Agreement <span class="text-success">[Print]</span></span>
        </div>
        <div class="file-actions">
          <a href="https://docs.google.com/document/d/11K-MlpSodpghHhPiB_q5YUpXlSdb_vgb/edit?usp=drive_link&ouid=104730774923126836253&rtpof=true&sd=true" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://docs.google.com/document/d/11K-MlpSodpghHhPiB_q5YUpXlSdb_vgb/export?format=docx" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
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
          <a href="https://drive.google.com/file/d/1FQVXIxyHQtpAp0dYZwxp5Rop850XRrIW/view?usp=drive_link" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://drive.google.com/uc?export=download&id=1FQVXIxyHQtpAp0dYZwxp5Rop850XRrIW" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
        <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">5. Flash Drive Label  <span class="text-success">[Print]</span></span><br>
        </div>
        <div class="file-actions">
          <a href="https://docs.google.com/document/d/18Eq88--hxUbixZWGvd3LdEl6BzszfG0S/edit?usp=drive_link&ouid=104730774923126836253&rtpof=true&sd=true" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://docs.google.com/document/d/18Eq88--hxUbixZWGvd3LdEl6BzszfG0S/export?format=docx" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
    </div>
  </main>

    <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?> 

  <!-- Bootstrap JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
</body>
</html>