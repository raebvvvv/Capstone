<?php
// Global path + URL configuration and helper functions.
// Include this file via: require __DIR__ . '/config.php'; as early as possible (before output).

// Load environment configuration
require_once __DIR__ . '/env_config.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', __DIR__); // Physical root of the project
}

// Ensure all server-side times default to Philippine time
if (function_exists('date_default_timezone_set')) {
    // Set once at bootstrap; individual scripts may override if needed
    @date_default_timezone_set('Asia/Manila');
}

if (!defined('TICKET_SIGNING_KEY')) {
    $signingKey = Environment::get('TICKET_SIGNING_KEY');
    if (!$signingKey) {
        $localKeyFile = BASE_PATH . DIRECTORY_SEPARATOR . 'ticket_signing.key';
        if (is_readable($localKeyFile)) {
            $signingKey = trim((string)file_get_contents($localKeyFile));
        }
    }
    if (!$signingKey) {
        // Fallback: deterministic hash so validation works in development.
        // Override via environment variable or ticket_signing.key file for production.
        $signingKey = hash('sha256', BASE_PATH . '|' . php_uname('n'));
    }
    define('TICKET_SIGNING_KEY', $signingKey);
}

// Try to derive base URL robustly; allow explicit override via environment
if (!defined('BASE_URL')) {
    $envBaseUrl = null;
    if (class_exists('Environment') && method_exists('Environment', 'get')) {
        // Prefer APP_URL if provided; fall back to legacy BASE_URL env key
        $envBaseUrl = Environment::get('APP_URL');
        if (!$envBaseUrl) {
            $envBaseUrl = Environment::get('BASE_URL');
        }
    }
    if (is_string($envBaseUrl) && $envBaseUrl !== '') {
        $base = rtrim($envBaseUrl, '/') . '/';
        define('BASE_URL', $base);
    } else {
        $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https://' : 'http://';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Compute web path of BASE_PATH relative to DOCUMENT_ROOT when possible
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? realpath((string)$_SERVER['DOCUMENT_ROOT']) : false;
        $baseDir = realpath(BASE_PATH);
        $basePathUrl = '';
        if ($docRoot && $baseDir) {
            $docRoot = rtrim(str_replace('\\', '/', $docRoot), '/');
            $baseDir = rtrim(str_replace('\\', '/', $baseDir), '/');
            if (strpos($baseDir, $docRoot) === 0) {
                $relative = trim(substr($baseDir, strlen($docRoot)), '/');
                $basePathUrl = $relative; // may be empty if deployed at web root
            }
        }
        // Fallback: use script directory if mapping failed
        if ($basePathUrl === '') {
            $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '')), '/');
            $basePathUrl = ltrim($scriptDir, '/');
        }

        $prefix = $https . $host . '/';
        $full = rtrim($prefix . $basePathUrl, '/') . '/';
        define('BASE_URL', $full);
    }
}

if (!function_exists('app_path')) {
    function app_path(string $path = ''): string {
        return $path ? BASE_PATH . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR) : BASE_PATH;
    }
}

// Secure storage path (outside webroot) for user uploads and generated files
if (!defined('STORAGE_BASE')) {
    // Allow override via env: STORAGE_PATH=C:\\secure\\ipmo_storage or /var/ipmo_storage
    $envStorage = Environment::get('STORAGE_PATH');
    if ($envStorage && is_string($envStorage)) {
        $base = rtrim(str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $envStorage), DIRECTORY_SEPARATOR);
    } else {
        // Default: place storage outside typical webroot folders when detected
        // If BASE_PATH = C:\\xampp\\htdocs\\capstone, prefer C:\\xampp\\ipmo_storage
        $projectParent = rtrim(dirname(BASE_PATH), DIRECTORY_SEPARATOR);
        $parentOfParent = rtrim(dirname($projectParent), DIRECTORY_SEPARATOR);
        $webRootFolder = strtolower(basename($projectParent));
        $commonWebRoots = ['htdocs','www','public','public_html','wwwroot'];
        if (in_array($webRootFolder, $commonWebRoots, true) && $parentOfParent !== '' && $parentOfParent !== $projectParent) {
            $base = $parentOfParent . DIRECTORY_SEPARATOR . 'ipmo_storage';
        } else {
            // Fallback: sibling to project root
            $base = $projectParent . DIRECTORY_SEPARATOR . 'ipmo_storage';
        }
    }

    // Honor open_basedir and shared-hosting constraints (e.g., InfinityFree) by
    // falling back to a storage directory under the project root if needed.
    $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? str_replace('\\', '/', (string)$_SERVER['DOCUMENT_ROOT']) : '';
    $normBase = str_replace('\\', '/', $base);
    $basePathNorm = str_replace('\\', '/', BASE_PATH);
    $withinDocRoot = ($docRoot !== '' && strpos($normBase, rtrim($docRoot, '/')) === 0);
    $withinProject = (strpos($normBase, rtrim($basePathNorm, '/')) === 0);
    $openBaseDir = (string)ini_get('open_basedir');

    if ($openBaseDir !== '') {
        // If target base is not within document root or project path, it's likely disallowed.
        if (!$withinDocRoot && !$withinProject) {
            $base = BASE_PATH . DIRECTORY_SEPARATOR . 'storage';
        }
    }

    // As a final safety, if the computed base is outside DOC_ROOT, but DOC_ROOT exists,
    // prefer a storage folder under the project root (which is under DOC_ROOT in most deployments)
    if ($docRoot !== '' && strpos(str_replace('\\', '/', $base), rtrim($docRoot, '/')) !== 0) {
        $base = BASE_PATH . DIRECTORY_SEPARATOR . 'storage';
    }

    define('STORAGE_BASE', $base);
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string {
        $base = STORAGE_BASE;
        if (!is_dir($base)) {
            // Best-effort create base storage folder
            @mkdir($base, 0775, true);
        }
        // When storage is under webroot (shared hosting), drop a protective .htaccess and index.html
        if (is_dir($base)) {
            $ht = $base . DIRECTORY_SEPARATOR . '.htaccess';
            if (!file_exists($ht)) {
                @file_put_contents($ht, "Options -Indexes\n<IfModule mod_authz_core.c>\nRequire all denied\n</IfModule>\n<IfModule !mod_authz_core.c>\nDeny from all\n</IfModule>\n");
            }
            $idx = $base . DIRECTORY_SEPARATOR . 'index.html';
            if (!file_exists($idx)) {
                @file_put_contents($idx, '<!doctype html><title>403</title>');
            }
        }
        return $path ? ($base . DIRECTORY_SEPARATOR . ltrim($path, DIRECTORY_SEPARATOR)) : $base;
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

    // DB connection is centralized in conn.php; include it where needed.
    // If a $pdo instance is required here, require conn.php explicitly.
}

// Optional: unify session + security bootstrap
if (file_exists(__DIR__ . '/security_bootstrap.php')) {
    require_once __DIR__ . '/security_bootstrap.php';
    if (function_exists('secure_bootstrap')) {
        secure_bootstrap();
    }
}

// Centralize DB connection: include once so scripts that require config.php have $pdo
// If a script needs to avoid DB for performance, it can skip requiring config.php and include specific helpers only.
if (file_exists(__DIR__ . '/conn.php')) {
    require_once __DIR__ . '/conn.php'; // defines $pdo
}
