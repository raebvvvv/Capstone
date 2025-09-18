<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PUP e-IPMO</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="Photos/pup-logo.png">
  <link rel="stylesheet" href="css/landing.css">
  <link rel="stylesheet" href="css/main.css">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="#">
  <img src="Photos/pup-logo.png" alt="PUP Logo" width="50" class="me-2">
        <span>PUP e-IPMO</span>
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
          <li class="nav-item"><a class="nav-link active maroon-underline" aria-current="page" href="index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Section -->
  <section class="container py-5">
  <!-- Top row: intro (left) and office hours (right) side by side, matching about.php -->
  <div class="row g-5 align-items-start">
    <div class="d-flex flex-row justify-content-between align-items-start w-100 flex-wrap main-intro-row">
      <div class="d-flex flex-row justify-content-between align-items-start w-100 flex-wrap">
        <div class="flex-grow-1" style="max-width: 60%;">
          <h1 class="fw-bold mb-2">e-IPMO Services</h1>
          <p class="fs-5 mb-3"><span class="fw-bold">e-IPMO</span> is an online system for students to easily register, submit, and track IP applications in one secure platform. Start your application process smoothly and efficiently.</p>
          <p class="mb-2">
            <span class="me-3"><img src="Photos/Icons/Email-icon.png" alt="email" class="icon-sm"> ipmo@pup.edu.ph</span>
            <span><img src="Photos/Icons/Landline-icon.png" alt="phone" class="icon-sm"> (+632) 5335-1787</span>
          </p>
          <p class="ip-purpose">The purpose of IP is to protect works   from misuse or theft. Click <a href="https://www.wto.org/english/tratop_e/trips_e/intel1_e.htm" class="fw-bold text-primary" target="_blank">here</a> to read about Intellectual Property Rights.</p>
        </div>
        <div class="ms-5">  
          <h2 class="fw-bold">Office Hours</h2>
          <div class="mb-2">
            <div>Monday - Friday<br>8:00 AM - 8:00 PM</div>
            <div class="text-danger fw-bold mt-1">NO SATURDAY SERVICES.</div>
          </div>
          <div class="mt-3">
            <div><span class="fw-bold">Location:</span></div>
            <div>PUP Main Building<br>S407, South Wing, 4th Floor<br>Anonas St. Sta. Mesa, Manila</div>
          </div>
        </div>
      </div>
    </div>
  </div>

    <!-- Below: centered cards and login -->
    <div class="d-flex flex-column align-items-center w-100 mt-4">
      <div class="cards-center-row">
        <div class="card shadow-sm h-100">
          <div class="card-body text-center">
            <img src="Photos/Icons/who.png" alt="users" class="mb-3 mx-auto d-block" style="height:40px;">
            <h5 class="fw-bold mb-2">Who can use e-IPMO?</h5>
            <p class="mb-0">Bonafide students and Non-students (Faculty and Staff) of all PUP branches.</p>
          </div>
        </div>
        <div class="card shadow-sm h-100">
          <div class="card-body text-center">
            <img src="Photos/Icons/how-apply.png" alt="apply" class="mb-3 mx-auto d-block" style="height:40px;">
            <h5 class="fw-bold mb-2">How do I apply for IP?</h5>
            <div class="d-flex justify-content-center gap-2 mb-2">
              <a href="ip-application.php" class="btn btn-warning fw-bold">IP Application</a>
              <a href="before-originality-check.php" class="btn btn-warning fw-bold">Originality Check</a>
            </div>
          </div>
        </div>
        <div class="card shadow-sm h-100">
          <div class="card-body text-center">
            <img src="Photos/Icons/what-ip.png" alt="types" class="mb-3 mx-auto d-block" style="height:40px;">
            <h5 class="fw-bold mb-2">What types of IP can I apply for?</h5>
            <p class="mb-0">Patent, Utility Model, Copyright, Industrial Design, and Trademark.</p>
          </div>
        </div>
      </div>
      <div class="login-section-center">
        <h5 class="fw-bold mb-3">Register or Login here!</h5>
        <div class="d-flex">
          <a href="login.php" class="btn btn-login fw-bold px-5 py-2">STUDENT</a>
          <a href="login.php" class="btn btn-login fw-bold px-5 py-2">EMPLOYEE</a>
        </div>
      </div>
    </div>
  </section>

  <!-- Announcements Section -->
  <section class="container py-1 mb-5" id="announcements">
    <h2 class="fw-bold mb-4">Announcements</h2>
    <!-- Announcement images only; link removed for admin simplicity -->
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card announcement-card">
          <img src="Photos/hiring.jpg" class="announcement-img" alt="Hiring Announcement">
        </div>
      </div>
      <div class="col-md-4">
        <div class="card announcement-card">
          <img src="Photos/hiring2.jpg" class="announcement-img" alt="Application Process">
        </div>
      </div>
      <div class="col-md-4">
        <div class="card announcement-card">
          <img src="Photos/hiring3.jpg" class="announcement-img" alt="Technical Assistant I">
        </div>
      </div>
      <div class="col-md-4">
        <div class="card announcement-card">
          <img src="Photos/hiring4.jpg" class="announcement-img" alt="Technical Assistant III">
        </div>
      </div>
      <div class="col-md-4">
        <div class="card announcement-card">
          <img src="Photos/hiring5.jpg" class="announcement-img" alt="Technical Expert">
        </div>
      </div>
      <div class="col-md-4">
        <div class="card announcement-card">
          <img src="Photos/hirinh6.jpg" class="announcement-img" alt="Technical Expert">
        </div>
      </div>
    </div>
  </section>

  <!-- Policy Section -->
  <section class="container py-5" id="policy">
    <h2 class="fw-bold mb-3">Intellectual Property Management Office (IPMO) Policy</h2>
    <h5 class="fw-bold mb-2">Copyright Registration Application Procedure and Forms</h5>
    <p>As approved in the 141st Regular Meeting of the University Board of Regents and pursuant to the <span class="fw-bold">PUP Memorandum Order No. 031, series of 2013</span>, all academic works (<span class="fw-bold">theses, dissertations, feasibility studies, design prototypes, computer programs and software, audiovisual and cinematographic works, literary and creative works, etc.</span>) submitted in partial fulfillment of the requirements of undergraduate and graduate courses in the University shall be applied for Copyright Registration with the Polytechnic University of the Philippines as copyright co-owner.</p>
    <h5 class="fw-bold mt-4 mb-3">MEMORANDUM ORDER<br>No. 031, Series of 2013</h5>
    <div class="row g-3">
      <div class="col-md-6">
  <img src="Photos/memo 1.png" class="img-fluid rounded shadow-sm" alt="Memo 1">
      </div>
      <div class="col-md-6">
  <img src="Photos/memo 2.png" class="img-fluid rounded shadow-sm" alt="Memo 2">
      </div>
    </div>
  </section>

  <!-- Articles Section -->
  <section class="container py-6" id="articles">
    <h2 class="fw-bold mb-4">Articles</h2>
    <div class="row g-4">
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="Photos/article 1.png" class="card-img-top" alt="Article 1">
          <div class="card-body">
            <p class="fw-bold mb-0">IPMO holds 2nd APEAR to facilitate student innovation</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="Photos/article 2.png" class="card-img-top" alt="Article 2">
          <div class="card-body">
            <p class="fw-bold mb-0">Entrep seminar for students organized</p>
          </div>
        </div>
      </div>
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="Photos/article 3.png" class="card-img-top" alt="Article 3">
          <div class="card-body">
            <p class="fw-bold mb-0">Jewelry-Making and Basic Marketing Strategies discussed by CTTED</p>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- Footer -->
  <footer class="bg-white border-top py-3 mt-4 footer-minimal">
    <div class="container text-center">
      © 2025 Polytechnic University of the Philippines &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/terms/" class="text-decoration-none" target="_blank">Terms of Service</a> &nbsp;|&nbsp;
      <a href="https://www.pup.edu.ph/privacy/" class="text-decoration-none" target="_blank">Privacy Statement</a>
    </div>
  </footer>

  <!-- Bootstrap JS + Icons -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="javascript/landing.js"></script>
</body>
</html>