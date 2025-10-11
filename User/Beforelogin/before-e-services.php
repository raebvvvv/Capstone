<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>PUP e-IPMO e-Services (Guide)</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">

  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous">
  
  <!-- Favicon -->
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">

  <!-- Custom CSS -->
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/e-services.css'); ?>">
  <style>
    /* Only widen the Industrial Property card, keep others unchanged */
    @media (min-width: 768px) {
      .ip-card-wide {
        width: 520px !important;
        max-width: 98vw;
      }
    }
  </style>
</head>
<body>

  <!-- =============================== Navbar ================================ -->
  <nav class="navbar navbar-expand-lg bg-white border-bottom sticky-top">
    <div class="container">
      <a class="navbar-brand d-flex align-items-center" href="../../index.php">
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

  <!-- =============================== Main Content ================================ -->
  <main class="container py-4">
    <!-- Page header -->
    <h2 class="text-center mb-2"><strong>Instructions</strong> are provided in each e-Service.</h2>
    <p class="text-center text-muted mb-4">
      Inside each e-Service are <strong>registration forms and application guidelines.</strong>
    </p>

    <!-- Service Cards (Row 1) -->
    <div class="row justify-content-center g-4">
      <!-- Originality Check -->
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
            <a href="before-originality-check.php" class="btn btn-warning fw-bold text-dark">Guide</a>
          </div>
        </div>
      </div>

      <!-- Copyright -->
      <div class="col-md-4">
        <div class="card h-100 shadow-sm">
          <div class="card-body text-center">
            <img src="<?php echo asset_url('Photos/Icons/Copyright-icon.png'); ?>" alt="Copyright" class="mb-2" width="60">
            <h5 class="card-title">Copyright</h5>
            <p class="card-text">
              Copyright is the legal protection extended to the owner of rights in an original work, such as
              <b>intellectual creations in the literary, scientific, and artistic domain.</b>
            </p>
            <a href="before-copyright-application.php" class="btn btn-warning fw-bold text-dark">Guide</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Service Cards (Row 2) - centered with extra top margin -->
    <div class="row justify-content-center g-4 mt-3 mt-md-4">
      <div class="col-12 col-md-5">
        <div class="card h-100 shadow-sm ip-card-wide">
          <div class="card-body text-center">
            <img src="<?php echo asset_url('Photos/Icons/Patent-icon.png'); ?>" alt="Patent" class="mb-2" width="60">
            <h5 class="card-title">Industrial Property</h5>
            <h6><i>Patent | Trademark | Utility Model | Industrial Design</i></h6>
            <p class="card-text">
              All four are subject to a government grant giving an inventor exclusive rights to a product or process
              that solves a technical problem in any human activity field.<br>
              <strong>They must be new, inventive, and industrially applicable.</strong>
            </p>
            <a href="before-industrial-application.php" class="btn btn-warning fw-bold text-dark">Guide</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Optional contact info (commented out) -->
    <!--
    <div class="text-center mt-5">
      <p class="contact-info-small mb-0">
        For any questions or concerns, feel free to contact us via:<br>
        <span class="me-3">
          <img src="<?php echo asset_url('Photos/Icons/Email-icon.png'); ?>" alt="email" class="icon-sm align-text-bottom"> ipmo@pup.edu.ph
        </span>
        <span>
          <img src="<?php echo asset_url('Photos/Icons/Landline-icon.png'); ?>" alt="phone" class="icon-sm align-text-bottom"> (+632) 5335-1787
        </span>
      </p>
    </div>
    -->
  </main>

  <!-- =============================== Footer ================================ -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?> 

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js"
          integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r"
          crossorigin="anonymous"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js"
          integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y"
          crossorigin="anonymous"></script>

  <!-- Custom JS -->
  <script src="javascript/e-services.js"></script>
</body>
</html>
