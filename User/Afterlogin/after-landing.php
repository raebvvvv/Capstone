<?php
// Deprecated landing path. All functionality moved to root index.php.
// Keep as lean redirect to preserve old bookmarks / external links.
require __DIR__ . '/../../config.php';

// Prevent caching of this stub so browsers don't keep obsolete copies.
if (!headers_sent()) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
    header('Expires: 0');
}

// Use 301 (permanent) only after you're sure no environment still depends dynamically; 302 for now.
$permanent = false; // Set to true later to emit 301.
header('Location: ' . asset_url('index.php'), true, $permanent ? 301 : 302);
exit;
?>