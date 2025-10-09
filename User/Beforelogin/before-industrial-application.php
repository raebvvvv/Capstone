<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Copyright Application | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
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
      <h2 class="fw-bold mb-0">Industrial Property Application</h2>

    </div>

    <ol>
      <li class="mb-4">
        This ZIP file contains clean copies of all the files listed below.<br>
        Files <strong>required</strong> to be printed have <span class="text-success fw-bold">'[Print]'</span> in their filenames.<br>
        <a href="https://drive.google.com/uc?export=download&id=1JtRlzBU0S7DDaaXSr6yJJlZLo8C7pigy" class="btn btn-primary mt-2 mb-2" style="width:180px; background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Industrial-Property.zip</a>
      </li>
      <li class="mb-4">
        Once printed and accomplished, please proceed to the <strong>physical submission at the RMIPO in room S423, PUP Main.</strong><br> 
      <!-- <a href="login.php" class="btn submission-btn mt-2 mb-2" style="width:180px;">Submission Form</a> -->
        <br> 
      </li>
    </ol>
    <p class="text-muted mb-3" style="font-size:0.95em;">
      You may view and download the files individually, but the ZIP file ensures you don’t miss any.
    </p>
    <div class="file-list">
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">1. Request letter / Letter of intent for IP application addressed to the director of IPMO <span class="text-success">[Print]</span></span>
        </div>
        <div class="file-actions">
          
        <!-- <a href="https://drive.google.com/file/d/11KW9MmxtVCzsfsWa1r9ifg-0RBFP-hG-/view?usp=sharing" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://drive.google.com/uc?export=download&id=11KW9MmxtVCzsfsWa1r9ifg-0RBFP-hG-" class="btn btn-danger btn-sm mb-1">Download as PDF</a> -->
        
        </div>
      </div>
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">2.	Proof of copyright application / Certificate of copyright application from IPMO <span class="text-success">[Print]</span></span><br>
          <span style="font-size:0.95em;">You must first secure a copyright application or certificate from the IPMO.</span>
        </div>
        <div class="file-actions">
          <!-- <a href="https://docs.google.com/document/d/1py-jLmvd_jbNyk4fCUZkwBVq28C6F4l6/edit?usp=sharing&ouid=104730774923126836253&rtpof=true&sd=true" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://docs.google.com/document/d/1py-jLmvd_jbNyk4fCUZkwBVq28C6F4l6/export?format=docx" download class="btn download-btn btn-sm mb-1">Download as PDF</a> -->
        </div>
      </div>
      <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">3.	Notarized deed of Assignment <span class="text-success">[Print]</span></span>
        </div>
        <div class="file-actions">
          <a href="https://docs.google.com/document/d/1eufbQVVlI09adpwep406tXTiL_ksmUV5/view" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://docs.google.com/document/d/1eufbQVVlI09adpwep406tXTiL_ksmUV5/export?format=docx" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
        <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">4. Invention Disclosure Form  <span class="text-success">[Print]</span></span><br>
        </div>
        <div class="file-actions">
          <a href="https://docs.google.com/document/d/1tiY3U6vEubB69x0kY_Bqm1UMIBvwqXb9/view" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://docs.google.com/document/d/1tiY3U6vEubB69x0kY_Bqm1UMIBvwqXb9/export?format=docx" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
          <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">5. Preliminary Search Report <span class="text-success">[Print]</span></span><br>
        </div>
        <div class="file-actions">
          <a href="https://docs.google.com/document/d/1g2eyCiWMMXDUP72nNh4KZJvkClkmu3OO/view" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://docs.google.com/document/d/1tiY3U6vEubB69x0kY_Bqm1UMIBvwqXb9/export?format=docx" class="btn btn-danger btn-sm mb-1">Download as PDF</a>
        </div>
      </div>
        <div class="file-card">
        <div class="file-icon">&#128196;</div>
        <div class="file-info">
          <span class="fw-bold">6. Two (2) copies of acknowledgement receipt of IP Application  <span class="text-success">[Print]</span></span><br>
        </div>
        <div class="file-actions">
          <!-- <a href="https://docs.google.com/document/d/18Eq88--hxUbixZWGvd3LdEl6BzszfG0S/edit?usp=drive_link&ouid=104730774923126836253&rtpof=true&sd=true" target="_blank" class="btn btn-warning btn-sm mb-1">View File</a>
          <a href="https://docs.google.com/document/d/18Eq88--hxUbixZWGvd3LdEl6BzszfG0S/export?format=docx" class="btn btn-danger btn-sm mb-1">Download as PDF</a> -->
        </div>
      </div>
    </div>
  </main>

    <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?> 

  <!-- Bootstrap JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>