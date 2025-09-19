<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PUP e-IPMO e-Services</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="Photos/pup-logo.png">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/e-services.css'); ?>">
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
          <li class="nav-item"><a class="nav-link" href="after-landing.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="after-about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link" href="student-application.php">My Application</a></li>
          <li class="nav-item"><a class="nav-link" href="student-profile.php">My Profile</a></li>
        </ul>
        <a href="#" class="btn btn-success ms-3 disabled-btn">You are in e-Services</a>
      </div>
    </div>
  </nav>
  <div class="container d-flex justify-content-end mt-3 mb-2">
    <?php if (function_exists('render_back_link')) { render_back_link('User/Afterlogin/after-landing.php'); } ?>
  </div>
  <main class="container py-4">
    <h2 class="text-center mb-2"><strong>Instructions</strong> are provided in each e-Service.</h2>
    <p class="text-center text-muted mb-4">
      Inside each e-Service are <strong>registration forms and application guidelines.</strong>
    </p>
    <div class="row justify-content-center g-4">
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center">
            <img src="<?php echo asset_url('Photos/Icons/Originality-icon.png'); ?>" alt="Originality Check" class="mb-2" width="60">
            <h5 class="card-title">Originality Check</h5>
            <p class="card-text">
              PUP IPMO offers Originality Checking through <strong>Turnit-in</strong>.<br>
              <span class="text-danger oc-rule-small"><b>15% - Graduate School (Doctorate and Masterals)</b></span><br>
              <span class="text-danger oc-rule-small"><b>20% and below - Undergraduate Degree</b></span>
            </p>
            <a href="after-originality-check.php"><button class="btn btn-success">Apply</button></a>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center">
            <img src="<?php echo asset_url('Photos/Icons/Copyright-icon.png'); ?>" alt="Copyright" class="mb-2" width="60">
            <h5 class="card-title">Copyright</h5>
            <p class="card-text">
              Copyright is the legal protection extended to the owner of the rights in an original work, such as <b>intellectual creations in the
              literary, scientific and artistic domain.</b>
            </p>
           <a href="student-copyright-forms.php"><button class="btn btn-success">Apply</button></a>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center">
            <img src="<?php echo asset_url('Photos/Icons/Patent-icon.png'); ?>" alt="Patent" class="mb-2" width="60">
            <h5 class="card-title">Patent | Trademark | Utility Model | Industrial Design</h5>
            <p class="card-text">
              All four are subject to a government grant giving an inventor exclusive rights to a product or process that solves a technical problem in any human activity field.<br>
              <strong>They must be new, inventive, and industrially applicable.</strong>
            </p>
            <button class="btn btn-success" onclick="apply('patent')">Apply</button>
          </div>
        </div>
      </div>
  </main>

    <!-- Footer -->
   <p me-3-3 class="text-center text-muted" style="font-size:0.9em;">
      For any questions or concerns, feel free to contact us via:<br>
      📧 ipmo@pup.edu.ph &nbsp; 📞 (+632) 5335-1787
    </p>
    <!-- Footer -->
  <footer class="bg-white border-top py-3 mt-4">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none" target="_blank">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none" target="_blank">Privacy Statement</a>
    </div>
  </footer> 

  <!-- Bootstrap JS CDN -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo asset_url('javascript/e-services.js'); ?>"></script>
</body>
</html>