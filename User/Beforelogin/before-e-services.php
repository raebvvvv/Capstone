<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PUP e-IPMO e-Services (Guide)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS CDN -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="Photos/pup-logo.png">
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/e-services.css">
</head>
<body>
  <!-- Navbar copied from index.php -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="index.php">
  <img src="Photos/pup-logo.png" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        </ul>
      </div>
    </div>
  </nav>
  <main class="container py-4">
    <h2 class="text-center mb-2"><strong>Instructions</strong> are provided in each e-Service.</h2>
    <p class="text-center text-muted mb-4">
      Inside each e-Service are <strong>registration forms and application guidelines.</strong>
    </p>
    <div class="row justify-content-center g-4">
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center">
            <img src="Photos/Icons/Originality-icon.png" alt="Originality Check" class="mb-2" width="60">
            <h5 class="card-title">Originality Check</h5>
            <p class="card-text">
              PUP IPMO offers Originality Checking through <strong>Turnit-in</strong>.<br>
              <span class="text-danger oc-rule-small"><b>15% - Graduate School (Doctorate and Masterals)</b></span><br>
              <span class="text-danger oc-rule-small"><b>20% and below - Undergraduate Degree</b></span>
            </p>
            <button class="btn btn-warning fw-bold text-dark" onclick="window.location.href='before-originality-check.php'">Guide</button>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center">
            <img src="Photos/Icons/Copyright-icon.png" alt="Copyright" class="mb-2" width="60">
            <h5 class="card-title">Copyright</h5>
            <p class="card-text">
              Copyright is the legal protection extended to the owner of the rights in an original work, such as <b>intellectual creations in the
              literary, scientific and artistic domain.</b>
            </p>
            <button class="btn btn-warning fw-bold text-dark" onclick="window.location.href='before-copyright-application.php'">Guide</button>
          </div>
        </div>
      </div>
      <div class="col-md-8">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center">
            <img src="Photos/Icons/Patent-icon.png" alt="Patent" class="mb-2" width="60">
            <h5 class="card-title">Patent | Trademark | Utility Model | Industrial Design</h5>
            <p class="card-text">
              All four are subject to a government grant giving an inventor exclusive rights to a product or process that solves a technical problem in any human activity field.<br>
              <strong>They must be new, inventive, and industrially applicable.</strong>
            </p>
            <button class="btn btn-warning fw-bold text-dark" onclick="apply('patent')">Guide</button>
          </div>
        </div>
      </div>
        <div class="text-center mt-5">
          <p class="contact-info-small mb-0">
            For any questions or concerns, feel free to contact us via:<br>
            <span class="me-3">
              <img src="Photos/Icons/Email-icon.png" alt="email" class="icon-sm align-text-bottom" style="width:20px;height:20px;"> ipmo@pup.edu.ph
            </span>
            <span>
              <img src="Photos/Icons/Landline-icon.png" alt="phone" class="icon-sm align-text-bottom" style="width:20px;height:20px;"> (+632) 5335-1787
            </span>
          </p>
        </div>
    </div>
  </main>

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
  <script src="javascript/e-services.js"></script>
</body>
</html>
