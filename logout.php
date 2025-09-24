<?php
// Central logout entry point for the whole app.
// Admin pages post here (../logout.php). We simply delegate to the existing
// secure logout handler under User/Beforelogin to keep logic in one place.
// This preserves POST + CSRF validation and consistent redirects.

require __DIR__ . '/User/Beforelogin/logout.php';
