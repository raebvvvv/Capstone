<?php
// Public (pre-login) bootstrap for path and basic constants.
// Define application base path (filesystem) and base URL segment.
if (!defined('APP_BASE_PATH')) {
    define('APP_BASE_PATH', __DIR__); // Root path of the Capstone app
}
if (!defined('APP_BASE_URL')) {
    // Infer if app is accessed via a subdirectory like /Capstone; adjust if deploying elsewhere.
    $script = $_SERVER['SCRIPT_NAME'] ?? '';
    $guess = (strpos($script, '/Capstone/') !== false) ? '/Capstone' : '';
    define('APP_BASE_URL', $guess);
}
// Helper to build asset URLs safely
if (!function_exists('asset')) {
    function asset(string $rel): string {
        $rel = ltrim($rel, '/');
        return APP_BASE_URL . '/' . $rel;
    }
}
?>
