<?php require __DIR__ . '/../../config.php'; ?>
<?php require __DIR__ . '/../../auth_check.php'; ?>
<?php $isEmployee = ((($_SESSION['role'] ?? '') === 'employee')); ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Copyright Application | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/copyright-application.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  
</head>
<body>
  <!-- Navigation Bar -->
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
          <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('index.php'); ?>">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="employee-application.php">My Application</a></li>
          <li class="nav-item"><?php include __DIR__ . '/../../partials/user_notifications.php'; ?></li>

          <!-- Add this inside your <ul class="navbar-nav ms-auto mb-2 mb-lg-0"> -->
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <!-- User icon (SVG only, no text) -->
              <svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" fill="currentColor" viewBox="0 0 16 16">
                <path d="M8 8a3 3 0 1 0 0-6 3 3 0 0 0 0 6z"/>
                <path d="M14 14s-1-1.5-6-1.5S2 14 2 14s1-4 6-4 6 4 6 4z"/>
              </svg>
            </a>

            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown">
              <?php $isEmployee = (($_SESSION['role'] ?? '') === 'employee'); ?>
              <li>
                <a class="dropdown-item" href="<?php echo $isEmployee ? 'employee-profile.php' : 'student-profile.php'; ?>">
                  My Profile
                </a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li>
                <a class="dropdown-item text-danger" data-bs-toggle="modal" data-bs-target="#logoutModal">Logout</a>
              </li>
            </ul>
          </li>


        </ul>
        <a href="e-services.php" class="btn btn-success ms-3" style="background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Proceed to e-Services</a>
        <!-- Logout button triggers confirmation modal 
        <button type="button" class="btn btn-danger ms-2" data-bs-toggle="modal" data-bs-target="#logoutModal"> 
          Logout
        </button> -->
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
        <a href="https://drive.google.com/uc?export=download&id=1njbAmgm2LrUSm2I5NPM9sFgPLbwnP_UH" class="btn btn-primary mt-2 mb-2" style="width:180px; background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Copyright.zip</a>
      </li>
      <li class="mb-4">
        Once printed and accomplished, you may proceed to the <strong>submission form.</strong><br> 
  <a href="<?php echo $isEmployee ? 'employee-copyright-forms.php' : 'student-copyright-forms.php'; ?>" class="btn submission-btn mt-2 mb-2" style="width:180px;">Submission Form</a>
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

<!-- Logout Confirmation Modal -->
<div class="modal fade" id="logoutModal" tabindex="-1" aria-labelledby="logoutModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="logoutModalLabel">Confirm Logout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        Are you sure you want to log out?
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <form method="POST" action="<?php echo asset_url('logout.php'); ?>" class="d-inline">
          <?php csrf_input(); ?>
          <button type="submit" class="btn btn-danger">Yes, log me out</button>
        </form>
      </div>
    </div>
  </div>
</div>
</body>
</html>