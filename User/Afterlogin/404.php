<?php
// Simple 404 page shown when an authenticated page is accessed without a valid session via back navigation.
// Sends 404 status and offers navigation options.
http_response_code(404);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>404 Not Found | PUP e-IPMO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="icon" type="image/png" href="<?php echo '../Photos/pup-logo.png'; ?>">
  <style>
    body { background:#f8f9fa; }
    .code { font-size:4rem; font-weight:700; }
  </style>
</head>
<body>
  <div class="container py-5">
    <div class="row justify-content-center">
      <div class="col-md-8">
        <div class="card shadow-sm border-0">
          <div class="card-body text-center py-5">
            <div class="code text-danger mb-2">404</div>
            <h1 class="h4 fw-bold mb-3">Page Not Found</h1>
            <p class="text-muted mb-4">The page you attempted to access is no longer available or requires an active session.</p>
            <div class="d-flex flex-column flex-sm-row justify-content-center gap-2">
              <a href="../../index.php" class="btn btn-primary">Return Home</a>
              <a href="../Beforelogin/login.php" class="btn btn-outline-secondary">Login Again</a>
            </div>
          </div>
        </div>
        <p class="text-center text-muted small mt-3 mb-0">If you believe this is an error, please contact support.</p>
      </div>
    </div>
  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>