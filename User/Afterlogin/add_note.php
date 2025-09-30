<?php
// Notes feature has been removed on the user side.
require __DIR__ . '/../../config.php';
require_once __DIR__ . '/../../auth_check.php';

http_response_code(410); // Gone
header('Content-Type: text/plain');
echo 'User notes feature is no longer available.';
exit;
?>
