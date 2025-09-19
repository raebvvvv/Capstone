<?php
// Deprecated legacy page. Replaced by originality-check.php
require __DIR__ . '/../../config.php';
if (!headers_sent()) {
  header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
  header('Pragma: no-cache');
  header('Expires: 0');
}
header('Location: ' . asset_url('User/Afterlogin/originality-check.php'), true, 302);
exit;