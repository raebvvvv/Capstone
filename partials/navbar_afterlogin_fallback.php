<?php
// Authenticated navbar partial.
// Assumes session already started and helper functions available (asset_url, csrf_input, etc.)
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
?>
<nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
  <div class="container">
    <a class="navbar-brand d-flex align-items-center" href="<?php echo asset_url('index.php'); ?>">
      <img src="<?php echo asset_url('Photos/pup-logo.png'); ?>" alt="PUP Logo" width="50" class="me-2">
      <span>PUP e-IPMO</span>
    </a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
        <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('index.php'); ?>">Home</a></li>
        <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('User/Afterlogin/about.php'); ?>">About Us</a></li>
  <?php $isEmployee = (($_SESSION['role'] ?? '') === 'employee'); ?>
  <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('User/Afterlogin/' . ($isEmployee ? 'employee-application.php' : 'student-application.php')); ?>">My Application</a></li>
  <li class="nav-item"><a class="nav-link" href="<?php echo asset_url('User/Afterlogin/' . ($isEmployee ? 'employee-profile.php' : 'student-profile.php')); ?>">My Profile</a></li>
      </ul>
      <a href="<?php echo asset_url('User/Afterlogin/e-services.php'); ?>" class="btn btn-success ms-3" style="background-color: #900c0c !important; border-color: #900c0c !important; color: #fff !important;">Proceed to e-Services</a>
    </div>
  </div>
</nav>
