<?php 
// === PHP: Load Config ===
require __DIR__ . '/config.php'; 

// === PHP: Check User Login Status ===
$isLoggedIn = isset($_SESSION['user_logged_in']) && $_SESSION['user_logged_in'] === true;
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>PUP e-IPMO</title>

  <!-- === CSS: Bootstrap Framework === -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" 
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">

  <!-- === HTML: Favicon === -->
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">

  <!-- === CSS: Custom Styles === -->
  <link rel="stylesheet" href="<?php echo asset_url('css/landing.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <!-- ========================== HTML: NAVBAR ========================== -->
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
          <!-- HTML: Static Home Link -->
          <li class="nav-item"><a class="nav-link active maroon-underline" aria-current="page" href="index.php">Home</a></li>

          <!-- PHP: Conditional Links based on Login Status -->
          <?php if ($isLoggedIn): ?>
            <li class="nav-item"><a class="nav-link" href="User/Afterlogin/about.php">About Us</a></li>
            <?php $isEmployee = (($_SESSION['role'] ?? '') === 'employee'); ?>
            <li class="nav-item"><a class="nav-link" href="<?php echo 'User/Afterlogin/' . ($isEmployee ? 'employee-application.php' : 'student-application.php'); ?>">My Application</a></li>
            <li class="nav-item"><a class="nav-link" href="<?php echo 'User/Afterlogin/' . ($isEmployee ? 'employee-profile.php' : 'student-profile.php'); ?>">My Profile</a></li>
          <?php else: ?>
            <li class="nav-item"><a class="nav-link" href="User/Beforelogin/about.php">About Us</a></li>
          <?php endif; ?>
        </ul>

        <!-- PHP: Conditional Button for Logged-in Users -->
        <?php if ($isLoggedIn): ?>
          <a href="User/Afterlogin/e-services.php" class="btn ms-3" style="background: #900c0c; color: #fff; border: none;">Proceed to e-Services</a>
        <?php endif; ?>
      </div>
    </div>
  </nav>
  <!-- ======================== END NAVBAR ======================== -->

  <!-- ========================== HTML: MAIN SECTION ========================== -->
  <section class="container py-5">
    <div class="row g-5 align-items-start">
      <div class="d-flex flex-row justify-content-between align-items-start w-100 flex-wrap main-intro-row">

        <!-- Left Column: Introduction & Contact -->
        <div class="flex-grow-1 intro-left">
          <h1 class="fw-bold mb-2">e-IPMO Services</h1>
          <p class="fs-5 mb-3" style="max-width: 550px;">
            <span class="fw-bold">e-IPMO</span> is an online system for students to easily register, submit, and track 
            <strong>Intellectual Property</strong> applications in one secure platform. 
            Start your application with ease.
          </p>

          <!-- HTML: Contact Info -->
          <p class="mb-2">
            <span class="me-3"><img src="<?php echo asset_url('Photos/Icons/Email-icon.png'); ?>" alt="email" class="icon-sm"> ipmo@pup.edu.ph</span> 
            <span><img src="<?php echo asset_url('Photos/Icons/Landline-icon.png'); ?>" alt="phone" class="icon-sm"> (+632) 5335-1787</span>
          </p>

          <!-- HTML: IP Purpose & External Link -->
          <p class="ip-purpose">
            The purpose of IP is to protect works from misuse or theft. Click 
            <a href="https://www.wto.org/english/tratop_e/trips_e/intel1_e.htm" class="fw-bold text-primary" target="_blank">here</a> to read about Intellectual Property Rights.
          </p>
        </div>

        <!-- Right Column: Office Hours & Location -->
        <div class="ms-5">  
          <h2 class="fw-bold">Office Hours</h2>
          <div class="mb-2">
            <div>Monday - Friday<br>8:00 AM - 7:30 PM</div>
            <div class="text-danger fw-bold mt-1">NO SATURDAY SERVICES.</div>
          </div>
          <div class="mt-3">
            <div><span class="fw-bold">Location:</span></div>
            <div>PUP Main Building<br>S423, South Wing, 4th Floor<br>Anonas St. Sta. Mesa, Manila</div>
          </div>
        </div>
      </div>
    </div>

    <!-- ===================== HTML: Centered Cards Section ===================== -->
    <div class="d-flex flex-column align-items-center w-100 mt-4">
      <div class="cards-center-row">

        <!-- Card 1: Who Can Use e-IPMO -->
        <div class="card shadow-sm h-100">
          <div class="card-body text-center">
            <img src="<?php echo asset_url('Photos/Icons/who.png'); ?>" alt="users" class="mb-3 mx-auto d-block" style="height:40px;">
            <h5 class="fw-bold mb-2">Who can use e-IPMO?</h5>
            <p class="mb-0">Bonafide students and Non-students (Faculty and Staff) of all PUP branches.</p>
          </div>
        </div>

        <!-- Card 2: Types of IP -->
        <div class="card shadow-sm h-100">
          <div class="card-body text-center">
            <img src="<?php echo asset_url('Photos/Icons/what-ip.png'); ?>" alt="types" class="mb-3 mx-auto d-block" style="height:40px;">
            <h5 class="fw-bold mb-2">What types of IP can I protect?</h5>
            <div class="row g-2 mb-2">

              <!-- PHP: Conditional Links based on Login Status -->
              <div class="col-6">
                <a href="<?php echo $isLoggedIn ? 'User/Afterlogin/copyright-info.php' : 'User/Beforelogin/copyright-info.php'; ?>" class="btn btn-warning fw-bold w-100">Copyright</a>
                <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">Artistic & Literary Property</small>
              </div>
              <div class="col-6">
                <a href="<?php echo $isLoggedIn ? 'User/Afterlogin/industrial-property-info.php' : 'User/Beforelogin/industrial-property-info.php'; ?>" class="btn btn-warning fw-bold w-100">Industrial Property</a>
                <small class="d-block text-muted mt-1" style="font-size: 0.7rem;">Technical & Commercial Property</small>
              </div>
            </div>
          </div>
        </div>

        <!-- Card 3: How to Apply -->
        <div class="card shadow-sm h-100">
          <div class="card-body text-center">
            <img src="<?php echo asset_url('Photos/Icons/how-apply.png'); ?>" alt="apply" class="mb-3 mx-auto d-block" style="height:40px;">
            <h5 class="fw-bold mb-2">How do I apply for protection?</h5>
            <div class="d-flex justify-content-center gap-2 mb-2">
              <a href="<?php echo $isLoggedIn ? 'User/Afterlogin/ip-application.php' : 'User/Beforelogin/ip-application.php'; ?>" class="btn btn-warning fw-bold">Protect your IP now!</a>
            </div>
          </div>
        </div>
      </div>

      <!-- PHP: Login Section for Guests -->
      <?php if (!$isLoggedIn): ?>
      <div class="login-section-center">
        <h5 class="fw-bold mb-3">Register or Login here!</h5>
        <div class="d-flex">
          <a href="User/Beforelogin/login.php" class="btn btn-login fw-bold px-5 py-2">STUDENT</a>
          <a href="User/Beforelogin/login.php?role=employee" class="btn btn-login fw-bold px-5 py-2">EMPLOYEE</a>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </section>

  <!-- ===================== HTML: Policy Section ===================== -->
  <section class="container py-5" id="policy">
    <h2 class="fw-bold mb-3">Intellectual Property Management Office (IPMO) Policy</h2>
    <h5 class="fw-bold mb-2">Copyright Registration Application Procedure and Forms</h5>
    <p>As approved in the 141st Regular Meeting of the University Board of Regents and pursuant to the <span class="fw-bold">PUP Memorandum Order No. 031, series of 2013</span>, all academic works (<span class="fw-bold">theses, dissertations, feasibility studies, design prototypes, computer programs and software, audiovisual and cinematographic works, literary and creative works, etc.</span>) submitted in partial fulfillment of the requirements of undergraduate and graduate courses in the University shall be applied for Copyright Registration with the Polytechnic University of the Philippines as copyright co-owner.</p>

    <h5 class="fw-bold mt-4 mb-3">MEMORANDUM ORDER<br>No. 031, Series of 2013</h5>
    <div class="row g-3">
      <div class="col-md-6">
        <img src="<?php echo asset_url('Photos/memo 1.png'); ?>" class="img-fluid rounded shadow-sm" alt="Memo 1">
      </div>
      <div class="col-md-6">
        <img src="<?php echo asset_url('Photos/memo 2.png'); ?>" class="img-fluid rounded shadow-sm" alt="Memo 2">
      </div>
    </div>
  </section>

  <!-- ===================== HTML: Footer ===================== -->
  <?php include __DIR__ . '/partials/standard_footer.php'; ?>

  <!-- === JS: Bootstrap Scripts === -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"></script>

  <!-- === JS: Conditional Scripts based on Login === -->
  <?php if ($isLoggedIn): ?>
    <script src="<?php echo asset_url('javascript/after-landing.js'); ?>"></script>
  <?php else: ?>
    <script src="<?php echo asset_url('javascript/landing.js'); ?>"></script>
  <?php endif; ?>
</body>
</html>
