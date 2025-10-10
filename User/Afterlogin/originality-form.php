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
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuqkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
  <link rel="stylesheet" href="<?php echo asset_url('css/after-originality-form.css'); ?>">
  <link rel="stylesheet" href="<?php echo asset_url('css/main.css'); ?>">
</head>
<body>
  <?php include __DIR__ . '/../../partials/navbar_afterlogin_fallback.php'; ?>
  <div class="container d-flex justify-content-end mt-3 mb-2">
    <?php if (function_exists('render_back_link')) { render_back_link('User/Afterlogin/originality-check.php'); } ?>
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
  <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.8/dist/umd/popper.min.js" integrity="sha384-I7E8VVD/ismYTF4hNIPjVp/Zjvgyol6VFvRkX/vR+Vc4jQkC+hVqc2pM8ODewa9r" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/js/bootstrap.min.js" integrity="sha384-G/EV+4j2dNv+tEPo3++6LCgdCROaejBqfUeNjuKAiuXbjrxilcCdDz6ZAVfHWe1Y" crossorigin="anonymous"></script>
  <script src="<?php echo asset_url('javascript/after-originality-form.js'); ?>"></script>
</body>
</html>
