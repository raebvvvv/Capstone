<?php
require __DIR__ . '/config.php';
if (function_exists('secure_bootstrap')) { secure_bootstrap(); }
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>404 Not Found</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.8/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-sRIl4kxILFvY47J16cr9ZwB07vP4J8+LH7qKQnuQkuIAvNWLzeN8tE5YBujZqJLB" crossorigin="anonymous" />
</head>
<body class="bg-light d-flex min-vh-100 align-items-center">
  <div class="container text-center">
    <h1 class="display-5 fw-bold mb-3">404</h1>
    <p class="lead mb-4">The page you were trying to access isn’t available.</p>
    <div class="d-flex gap-2 justify-content-center">
      <a class="btn btn-outline-secondary" href="<?php echo asset_url('index.php'); ?>">Go to Home</a>
      <a class="btn btn-primary" href="<?php echo asset_url('User/Beforelogin/login.php'); ?>">Login</a>
    </div>
  </div>
</body>
</html>
