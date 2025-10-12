<?php require __DIR__ . '/../../config.php'; ?>
<?php require __DIR__ . '/../../auth_check.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Copyright Application | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  <link rel="icon" type="image/png" href="Photos/pup-logo.png">
  <link rel="stylesheet" href="<?php echo asset_url('css/copyright-application.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  
</head>
<body>
  <!-- Navigation Bar -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="../../index.php">
  <img src="../../Photos/pup-logo.png" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="../../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
          <?php $isEmployee = (($_SESSION['role'] ?? '') === 'employee'); ?>
          <li class="nav-item"><a class="nav-link" href="<?php echo $isEmployee ? 'employee-application.php' : 'student-application.php'; ?>">My Application</a></li>
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
        
        <a href="#" class="btn btn-success ms-3 disabled-btn" style="background-color: #B8464A !important; border-color: #B8464A !important; color: #fff !important; cursor: not-allowed; pointer-events: none;">You are in e-Services</a>
      </div>
    </div>
  </nav>
   
  <!-- Back Button Below Navbar, scrolls with content, transparent background -->
    <div class="container d-flex justify-content-end mt-3 mb-2">
        <?php if (function_exists('render_back_link')) { render_back_link('User/Afterlogin/e-services.php'); } ?>
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