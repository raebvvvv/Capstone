<?php
// Central security bootstrap: session hardening, security headers, CSRF helpers.

// Include environment and secure session configuration
require_once __DIR__ . '/env_config.php';
require_once __DIR__ . '/secure_session_config.php';

// --- mbstring polyfills (graceful fallbacks when mbstring extension is missing) ---
if (!function_exists('mb_strtolower')) {
    function mb_strtolower($string, $encoding = null) { return strtolower((string)$string); }
}
if (!function_exists('mb_strtoupper')) {
    function mb_strtoupper($string, $encoding = null) { return strtoupper((string)$string); }
}
if (!function_exists('mb_strlen')) {
    function mb_strlen($string, $encoding = null) { return strlen((string)$string); }
}
if (!function_exists('mb_substr')) {
    function mb_substr($string, $start, $length = null, $encoding = null) {
        $s = (string)$string;
        return ($length === null) ? substr($s, (int)$start) : substr($s, (int)$start, (int)$length);
    }
}
if (!function_exists('mb_convert_case')) {
    if (!defined('MB_CASE_UPPER')) define('MB_CASE_UPPER', 0);
    if (!defined('MB_CASE_LOWER')) define('MB_CASE_LOWER', 1);
    if (!defined('MB_CASE_TITLE')) define('MB_CASE_TITLE', 2);
    function mb_convert_case($string, $mode, $encoding = null) {
        $s = (string)$string;
        switch ((int)$mode) {
            case MB_CASE_UPPER: return strtoupper($s);
            case MB_CASE_TITLE: return ucwords(strtolower($s));
            case MB_CASE_LOWER:
            default: return strtolower($s);
        }
    }
}

if (!function_exists('secure_bootstrap')) {
    function secure_bootstrap(): void {
        // Basic config constants with environment variable support
        if (!defined('SESSION_IDLE_TIMEOUT')) {
            define('SESSION_IDLE_TIMEOUT', Environment::getInt('SESSION_IDLE_TIMEOUT', 1800)); // default 30 minutes
        }
        // Admin pages stricter idle window (5 minutes) unless overridden by env ADMIN_SESSION_IDLE_TIMEOUT
        if (!defined('ADMIN_SESSION_IDLE_TIMEOUT')) {
            define('ADMIN_SESSION_IDLE_TIMEOUT', Environment::getInt('ADMIN_SESSION_IDLE_TIMEOUT', 300)); // 5 minutes
        }
        if (!defined('SESSION_ABSOLUTE_LIFETIME')) {
            define('SESSION_ABSOLUTE_LIFETIME', Environment::getInt('SESSION_LIFETIME', 28800)); // 8 hours
        }
        if (!defined('AUDIT_LOG_FILE')) {
            $audit_log = Environment::get('AUDIT_LOG_FILE', __DIR__ . DIRECTORY_SEPARATOR . 'audit.log');
            define('AUDIT_LOG_FILE', $audit_log);
        }
        // Configure secure sessions using centralized configuration
        configure_secure_sessions();
        
        // Apply comprehensive security headers
        require_once __DIR__ . '/security_headers.php';
        SecurityHeaders::applyHeaders();
        // CSRF token
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Session timing checks
        $now = time();
        // Determine appropriate login target (admin vs. public) based on current route
        $scriptName = $_SERVER['SCRIPT_NAME'] ?? ($_SERVER['PHP_SELF'] ?? '');
        $isAdminRoute = stripos($scriptName, '/admin/') !== false;
        $loginTarget = $isAdminRoute ? 'admin/login.php' : 'User/Beforelogin/login.php';

        if (isset($_SESSION['session_created_at']) && ($now - (int)$_SESSION['session_created_at']) > SESSION_ABSOLUTE_LIFETIME) {
            session_unset(); session_destroy();
            if (function_exists('redirect')) { redirect($loginTarget); }
            header('Location: ' . $loginTarget); exit();
        }
        if (!isset($_SESSION['session_created_at'])) {
            $_SESSION['session_created_at'] = $now;
        }
        // Use stricter admin idle timeout on admin routes
        $effectiveIdleTimeout = $isAdminRoute ? ADMIN_SESSION_IDLE_TIMEOUT : SESSION_IDLE_TIMEOUT;
        if (isset($_SESSION['last_activity']) && ($now - (int)$_SESSION['last_activity']) > $effectiveIdleTimeout) {
            session_unset(); session_destroy();
            if (function_exists('redirect')) { redirect($loginTarget); }
            header('Location: ' . $loginTarget); exit();
        }
        $_SESSION['last_activity'] = $now;
    }
}

// Helper to enforce admin-only access
if (!function_exists('require_admin')) {
    function require_admin(): void {
        $isLoggedIn = !empty($_SESSION['user_logged_in']);
        $isAdmin = !empty($_SESSION['is_admin']) && (int)$_SESSION['is_admin'] === 1;
        if ($isLoggedIn && !$isAdmin) {
            // Authenticated but not admin -> block with friendly 404 (do not reveal admin area)
            $location = '404.php';
            if (function_exists('redirect')) { redirect($location); }
            $abs = (defined('BASE_URL') ? BASE_URL : '/') . ltrim($location, '/');
            header('Location: ' . $abs);
            exit();
        }
        if (!$isLoggedIn) {
            // Not logged in -> send to admin login (hidden page)
            $location = 'admin/login.php';
            if (function_exists('redirect')) { redirect($location); }
            $abs = (defined('BASE_URL') ? BASE_URL : '/') . ltrim($location, '/');
            header('Location: ' . $abs);
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

// ---------- Output escaping and input sanitization helpers ----------
if (!function_exists('e')) {
    // Escape for HTML context (text, attribute). Always UTF-8, quote-safe.
    function e($value): string {
        return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
    }
}

if (!function_exists('sanitize_text')) {
    // Basic text sanitizer: trim, collapse whitespace, strip tags; optionally cap length
    function sanitize_text($value, int $maxLen = 255): string {
        $s = (string)$value;
        // Normalize newlines/tabs to spaces then collapse multiple spaces
        $s = preg_replace('/[\r\n\t]+/u', ' ', $s);
        $s = trim($s);
        // Strip any HTML tags
        $s = strip_tags($s);
        // Collapse repeated spaces
        $s = preg_replace('/\s{2,}/u', ' ', $s);
        if ($maxLen > 0 && mb_strlen($s, 'UTF-8') > $maxLen) {
            $s = mb_substr($s, 0, $maxLen, 'UTF-8');
        }
        return $s;
    }
}

if (!function_exists('clean_enum')) {
    // Constrain string to one of the allowed values (case-insensitive). Returns default if not matched.
    function clean_enum($value, array $allowed, $default) {
        $v = strtolower(trim((string)$value));
        $allowedLower = array_map(function ($x) { return strtolower((string)$x); }, $allowed);
        $idx = array_search($v, $allowedLower, true);
        return $idx !== false ? $allowed[$idx] : $default;
    }
}
