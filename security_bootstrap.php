<?php
// Central security bootstrap: session hardening, security headers, CSRF helpers.

if (!function_exists('secure_bootstrap')) {
    function secure_bootstrap(): void {
        // Basic config constants (define once)
        if (!defined('SESSION_IDLE_TIMEOUT')) {
            define('SESSION_IDLE_TIMEOUT', 1800); // 30 minutes
        }
        if (!defined('SESSION_ABSOLUTE_LIFETIME')) {
            define('SESSION_ABSOLUTE_LIFETIME', 28800); // 8 hours
        }
        if (!defined('AUDIT_LOG_FILE')) {
            define('AUDIT_LOG_FILE', __DIR__ . DIRECTORY_SEPARATOR . 'audit.log');
        }
        // Detect HTTPS early so we can set cookie params BEFORE session_start
        $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

        // Session hardening (must occur before session_start for ini settings to apply)
        if (session_status() !== PHP_SESSION_ACTIVE) {
            ini_set('session.use_strict_mode', '1');
            ini_set('session.cookie_httponly', '1');
            ini_set('session.cookie_samesite', 'Lax');
            if ($isHttps) {
                ini_set('session.cookie_secure', '1');
            }
            session_start();
        } else {
            // Session already active (legacy include order). Try to enforce cookie flags retroactively.
            if ($isHttps && ini_get('session.cookie_secure') != 1) {
                // Re-issue session cookie with secure flag.
                $params = session_get_cookie_params();
                setcookie(session_name(), session_id(), [
                    'expires' => $params['lifetime'] ? time() + $params['lifetime'] : 0,
                    'path' => $params['path'] ?? '/',
                    'domain' => $params['domain'] ?? '',
                    'secure' => true,
                    'httponly' => true,
                    'samesite' => 'Lax'
                ]);
            }
        }
        // Security headers (idempotent)
        if (!headers_sent()) {
            header('X-Frame-Options: DENY');
            header('X-Content-Type-Options: nosniff');
            header('Referrer-Policy: no-referrer');
            header('Permissions-Policy: camera=(), microphone=(), geolocation=()');
            // Light CSP (adjust as you reduce inline styles/scripts)
            if (!headers_sent()) {
                header("Content-Security-Policy: default-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; img-src 'self' data: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com; font-src 'self' data: https://fonts.gstatic.com https://fonts.googleapis.com; script-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com; object-src 'none'; frame-ancestors 'none'; base-uri 'self';");
            }
            // If request is HTTPS, enforce HSTS (cookie secure already handled above)
            if ($isHttps) {
                header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
            }
        }
        // CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Session timing checks
        $now = time();
        if (isset($_SESSION['session_created_at']) && ($now - (int)$_SESSION['session_created_at']) > SESSION_ABSOLUTE_LIFETIME) {
            session_unset(); session_destroy();
            if (function_exists('redirect')) { redirect('User/Beforelogin/login.php'); }
            header('Location: User/Beforelogin/login.php'); exit();
        }
        if (!isset($_SESSION['session_created_at'])) {
            $_SESSION['session_created_at'] = $now;
        }
        if (isset($_SESSION['last_activity']) && ($now - (int)$_SESSION['last_activity']) > SESSION_IDLE_TIMEOUT) {
            session_unset(); session_destroy();
            if (function_exists('redirect')) { redirect('User/Beforelogin/login.php'); }
            header('Location: User/Beforelogin/login.php'); exit();
        }
        $_SESSION['last_activity'] = $now;
    }
}

// Helper to enforce admin-only access
if (!function_exists('require_admin')) {
    function require_admin(): void {
        if (empty($_SESSION['user_logged_in']) || empty($_SESSION['is_admin']) || (int)$_SESSION['is_admin'] !== 1) {
            // Redirect to login from both root and /admin context
            $script = $_SERVER['SCRIPT_NAME'] ?? '';
            $inAdmin = strpos($script, '/admin/') !== false || basename(dirname($script)) === 'admin';
            $location = $inAdmin ? '../User/Beforelogin/login.php' : 'User/Beforelogin/login.php';
            if (function_exists('redirect')) { redirect($location); }
            header('Location: ' . $location);
            exit();
        }
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string {
        return $_SESSION['csrf_token'] ?? '';
    }
}

if (!function_exists('csrf_input')) {
    function csrf_input(): void {
        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES, 'UTF-8') . '">';
    }
}

if (!function_exists('verify_csrf_post')) {
    function verify_csrf_post(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!isset($_POST['csrf_token'], $_SESSION['csrf_token']) || !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])) {
                http_response_code(400);
                exit('Invalid CSRF token');
            }
        }
    }
}

if (!function_exists('verify_csrf_header')) {
    function verify_csrf_header(): void {
        if ($_SERVER['REQUEST_METHOD'] === 'POST' || $_SERVER['REQUEST_METHOD'] === 'PUT' || $_SERVER['REQUEST_METHOD'] === 'DELETE' || $_SERVER['REQUEST_METHOD'] === 'PATCH') {
            $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? ($_SERVER['HTTP_X_CSRFTOKEN'] ?? null);
            if ($token === null || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
                http_response_code(400);
                exit('Invalid CSRF token (header)');
            }
        }
    }
}

if (!function_exists('log_event')) {
    function log_event(string $type, string $message, array $context = []): void {
        try {
            // Sanitize sensitive keys
            $sensitive = ['password','current_password','new_password','confirm_password','csrf_token'];
            $sanitized = [];
            foreach ($context as $k => $v) {
                if (in_array(strtolower($k), $sensitive, true)) {
                    $sanitized[$k] = '*';
                } else {
                    // Truncate very long values to prevent log flooding
                    if (is_string($v) && strlen($v) > 200) {
                        $sanitized[$k] = substr($v,0,200) . '…';
                    } else {
                        $sanitized[$k] = $v;
                    }
                }
            }
            $entry = [
                'ts' => date('c'),
                'type' => $type,
                'user_id' => $context['user_id'] ?? ($_SESSION['user_id'] ?? null),
                'ip' => $_SERVER['REMOTE_ADDR'] ?? null,
                'msg' => $message,
                'ctx' => $sanitized,
            ];
            file_put_contents(AUDIT_LOG_FILE, json_encode($entry) . PHP_EOL, FILE_APPEND | LOCK_EX);
        } catch (Throwable $e) {
            // fail closed silently
        }
    }
}
