<?php require __DIR__ . '/../../config.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Originality Check Form | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <!-- Bootstrap CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="icon" type="image/png" href="<?php echo asset_url('Photos/pup-logo.png'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/originality-form.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <!-- Navbar (copied from index.php) -->
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
          <li class="nav-item"><a class="nav-link active" aria-current="page" href="../../index.php">Home</a></li>
          <li class="nav-item"><a class="nav-link" href="about.php">About Us</a></li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container d-flex justify-content-end mt-3 mb-2">
  <?php if (function_exists('render_back_link')) { render_back_link('index.php'); } ?>
  </div>

  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="fw-bold m-0">Originality Check Form</h2>
    </div>
    <div class="form-bg mx-auto shadow-sm p-4 rounded-4 mb-4 d-flex justify-content-center">
      <!-- Form image as in your screenshot -->
  <img src="<?php echo asset_url('Photos/originality-check-form.jpg'); ?>" alt="Originality Check Form" class="form-img" style="width: auto; height: 645px;" />
    </div>
  </div>

  <!-- Footer -->
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?> 

  <!-- Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo asset_url('javascript/originality-form.js'); ?>"></script>
</body>
</html>