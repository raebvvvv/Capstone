<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Originality Check Form | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="css/after-originality-form.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center gap-2" href="#">
        <img src="Photos/pup-logo.png" alt="PUP Logo" style="width:40px;height:40px;">
        <span class="fw-bold fs-5">PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
        data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false"
        aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse flex-grow-0" id="navbarNav">
        <ul class="navbar-nav gap-2 align-items-center ms-auto">
          <li class="nav-item">
            <a class="nav-link fw-bold" href="after-landing.php">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold" href="after-about.php">About Us</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold" href="#">My Application</a>
          </li>
          <li class="nav-item">
            <a class="nav-link fw-bold" href="#">My Profile <span class="ms-1"><i class="bi bi-person"></i></span></a>
          </li>
        </ul>
        <div class="ms-3 d-flex gap-2">
          <a href="#" class="btn btn-success btn-sm px-3">Proceed to<br>e-Services</a>
        </div>
      </div>
    </div>
  </nav>

  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="fw-bold m-0">Originality Check Form</h2>
      <div class="d-flex gap-2">
        <a href="#" onclick="window.history.back()" class="text-dark fs-5 text-decoration-none back-btn-content">&#x21B6; Back</a>
        <button class="btn btn-danger btn-sm" id="download-pdf-btn">Download as PDF</button>
      </div>
    </div>
    <div class="form-bg mx-auto shadow-sm p-4 rounded-4 mb-4 d-flex justify-content-center">
      <!-- Form image as in your screenshot -->
      <img src="Photos/originality-check-form.jpg" alt="Originality Check Form" class="form-img" style="width: auto; height: 645px;" />
    </div>
  </div>

  <!-- Footer -->
  <footer class="bg-white border-top py-3">
    <div class="container text-center small">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none">Privacy Statement</a>
    </div>
  </footer>

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="javascript/after-originality-form.js"></script>
</body>
</html>