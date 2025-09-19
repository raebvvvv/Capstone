<?php
// Secure logout script
// Best practices:
// 1. Only accept POST (prevents CSRF via simple link/img) with a valid CSRF token.
// 2. Regenerate session ID earlier in the app on privilege changes; here we just destroy safely.
// 3. Unset all session variables, then destroy the session cookie explicitly.
// 4. Redirect using a relative path (avoid open redirect) and stop execution.

require __DIR__ . '/../../config.php';
if (function_exists('secure_bootstrap')) { secure_bootstrap(); } else { session_start(); }

// Reject non-POST methods
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
	// Optionally you can show a 405 page. Simplicity: redirect to login.
	if (function_exists('redirect')) { redirect('User/Beforelogin/login.php'); }
	header('Location: login.php');
	exit();
}

// Basic CSRF token validation (token should be stored in session when rendering form)
if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
	// Token invalid: deny and redirect.
	if (function_exists('redirect')) { redirect('User/Beforelogin/login.php'); }
	header('Location: login.php');
	exit();
}

// Unset all session variables
$_SESSION = [];

// Delete the session cookie if it exists
if (ini_get('session.use_cookies')) {
	$params = session_get_cookie_params();
	setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
}

// Destroy the session
session_destroy();

// Prevent caching of the logout response
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');
header('Expires: 0');

if (function_exists('redirect')) { redirect('User/Beforelogin/login.php'); }
header('Location: login.php');
exit();
?>
