<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PUP e-IPMO</title>
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="Photos/pup-logo.png">
  <link rel="stylesheet" href="css/main.css">
  <link rel="stylesheet" href="css/landing.css?v=5">
</head>
<body>
  <nav class="navbar navbar-expand-lg bg-white border-bottom">
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
          <li class="nav-item"><a class="nav-link nav-bold active-page" aria-current="page" href="after-landing.php">Home</a></li>
          <li class="nav-item"><a class="nav-link nav-bold" href="after-about.php">About Us</a></li>
          <li class="nav-item"><a class="nav-link nav-bold" href="student-application.php">My Application</a></li>
          <li class="nav-item"><a class="nav-link nav-bold" href="student-profile.php">My Profile</a></li>
        </ul>
        <a href="e-services.php" class="btn btn-success ms-3">Proceed to e-Services</a>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main Section -->
  <main class="container py-4">
    <div class="row g-5 align-items-start">
      <div class="col-lg-5">
        <h1 class="fw-bold mb-2">e-IPMO Services</h1>
        <p class="mb-3 p-font-size"><span class="fw-bold">e-IPMO</span> is an online system that lets students easily register, submit, and track their IP applications—all in one secure and user-friendly platform. It simplifies the first step of the application process for a smoother start.</p>
        <p class="mb-2"> <p></p>
          <span class="me-3"><img src="Photos/Icons/Email-icon.png" alt="email" class="icon-sm"> ipmo@pup.edu.ph</span>
          <span><img src="Photos/Icons/Landline-icon.png" alt="phone" class="icon-sm"> (+632) 5335-1787</span>
        </p>
        <p>The purpose of IP is to protect works from misuse or theft. Click <a href="https://www.wto.org/english/tratop_e/trips_e/intel1_e.htm" class="fw-bold text-primary" target="_blank">here</a> to read about Intellectual Property Rights.</p>
      </div>
      <div class="col-lg-7">
        <div class="office-right">
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
  <section class="container py-5" id="announcements">
    <h2 class="fw-bold mb-4">Announcements</h2>
    <div class="row g-4">
      <!-- Announcement Card 1 -->
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="Photos/hiring.jpg" class="card-img-top" alt="Hiring Announcement">
        </div>
      </div>
      <!-- Announcement Card 2 -->
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="Photos/hiring2.jpg" class="card-img-top" alt="Application Process">
        </div>
      </div>
      <!-- Announcement Card 3 -->
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="Photos/hiring3.jpg" class="card-img-top" alt="Technical Assistant I">
        </div>
      </div>
      <!-- Announcement Card 4 -->
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="Photos/hiring4.jpg" class="card-img-top" alt="Technical Assistant III">
        </div>
      </div>
      <!-- Announcement Card 5 -->
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="Photos/hiring5.jpg" class="card-img-top" alt="Technical Expert">
        </div>
      </div>
      <!-- Announcement Card 6 -->
      <div class="col-md-4">
        <div class="card shadow-sm h-100">
          <img src="Photos/hirinh6.jpg" class="card-img-top" alt="Technical Expert">
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
  <section class="container py-5" id="articles">
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
</main>

  <!-- Footer -->
  <footer class="text-center mt-5">
    <small>
      © 2025 Polytechnic University of the Philippines | 
      <a href="https://www.pup.edu.ph/terms" class="footer-link-blue" target="_blank">Terms of Service</a> | 
      <a href="https://www.pup.edu.ph/privacy" class="footer-link-blue" target="_blank">Privacy Statement</a> 
    </small>
  </footer>

  <!-- Bootstrap JS + Icons -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="javascript/landing.js"></script>
</body>
</html>