<?php
// Global path + URL configuration and helper functions.
// Include this file via: require __DIR__ . '/config.php'; as early as possible (before output).

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__); // Physical root of the project
}

// Try to derive base URL automatically (works for typical XAMPP localhost setups)
if (!defined('BASE_URL')) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Assume the project directory name is the web root folder (capstonks)
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
    // If accessed from a nested script (e.g. /capstonks/admin/page.php) remove trailing segment(s) until we find folder name
    $parts = explode('/', trim($scriptDir, '/'));
    // Attempt to detect project folder by matching physical directory name
    $projectFolder = basename(BASE_PATH);
    $idx = array_search($projectFolder, $parts, true);
    if ($idx !== false) {
        $basePathParts = array_slice($parts, 0, $idx + 1);
        $basePathUrl = implode('/', $basePathParts);
    } else {
        $basePathUrl = $projectFolder; // Fallback
    }
    define('BASE_URL', rtrim($https . $host . '/' . $basePathUrl, '/') . '/');
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string {
        return $path ? BASE_PATH . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : BASE_PATH;
    }
}

if (!function_exists('asset_url')) {
    function asset_url(string $path = ''): string {
        return BASE_URL . ltrim($path, '/');
    }
}

if (!function_exists('redirect')) {
    function redirect(string $relative, int $code = 302): void {
        // Normalize relative path to avoid '//' issues
        $target = BASE_URL . ltrim($relative, '/');
        header('Location: ' . $target, true, $code);
        exit();
    }
}

// Safe back navigation helper: prefers a validated referrer within BASE_URL, else fallback provided (default homepage)
if (!function_exists('back_url')) {
    function back_url(string $fallbackRelative = 'index.php'): string {
        $ref = $_SERVER['HTTP_REFERER'] ?? '';
        if ($ref) {
            // Only allow if starts with BASE_URL (prevents open redirect) and not the same page
            if (strpos($ref, BASE_URL) === 0) {
                return $ref;
            }
        }
        return BASE_URL . ltrim($fallbackRelative, '/');
    }
}

// Convenience: generate escaped href for back link
if (!function_exists('back_href')) {
    function back_href(string $fallbackRelative = 'index.php'): string {
        return htmlspecialchars(back_url($fallbackRelative), ENT_QUOTES, 'UTF-8');
    }
}

// Renderer for a standardized back link (optional use in templates)
if (!function_exists('render_back_link')) {
    function render_back_link(string $fallbackRelative = 'index.php', string $label = '↶ Back', string $classes = 'back-btn-content text-dark fs-5 text-decoration-none'): void {
        echo '<a href="' . back_href($fallbackRelative) . '" class="' . htmlspecialchars($classes, ENT_QUOTES, 'UTF-8') . '">' . htmlspecialchars($label, ENT_QUOTES, 'UTF-8') . '</a>';
    }
}

// Optional: unify session + security bootstrap
if (file_exists(__DIR__ . '/security_bootstrap.php')) {
    require_once __DIR__ . '/security_bootstrap.php';
    if (function_exists('secure_bootstrap')) {
        secure_bootstrap();
    }
}
