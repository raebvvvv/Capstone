<?php
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Originality Check Form | PUP e-IPMO</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="<?php echo asset_url('css/after-originality-form.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <?php include __DIR__ . '/../../partials/navbar_afterlogin_fallback.php'; ?>
  <div class="container d-flex justify-content-end mt-3 mb-2">
    <?php if (function_exists('render_back_link')) { render_back_link('originality-check.php'); } ?>
  </div>
  <div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
      <h2 class="fw-bold m-0">Originality Check Form</h2>
    </div>
    <div class="form-bg mx-auto shadow-sm p-4 rounded-4 mb-4 d-flex justify-content-center">
      <img src="<?php echo asset_url('Photos/originality-check-form.jpg'); ?>" alt="Originality Check Form" class="form-img" style="width:auto; height:645px;" />
    </div>
  </div>
  <?php include __DIR__ . '/../../partials/standard_footer.php'; ?>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?php echo asset_url('javascript/after-originality-form.js'); ?>"></script>
</body>
</html>
