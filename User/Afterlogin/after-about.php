
<?php
require __DIR__ . '/../../config.php';
require __DIR__ . '/../../auth_check.php';
// Permanent redirect (301) could be used after confirming all references updated.
redirect('User/Afterlogin/about.php');
exit;
?>