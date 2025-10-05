<?php
// Secure Session Configuration
// This file configures session security settings based on environment

function configure_secure_sessions() {
    // Check if we're running on HTTPS
    $is_https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
                (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
                (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    
    // Basic security settings (always apply)
    ini_set('session.cookie_httponly', 1);     // Prevent XSS access to cookies
    ini_set('session.use_strict_mode', 1);     // Prevent session fixation
    ini_set('session.cookie_samesite', 'Strict'); // CSRF protection
    ini_set('session.use_only_cookies', 1);    // No URL-based sessions
    
    // HTTPS-specific settings
    if ($is_https) {
        ini_set('session.cookie_secure', 1);   // Only send over HTTPS
        $session_name = 'SECURE_SESSID';
    } else {
        // Development environment - HTTP allowed but warn
        ini_set('session.cookie_secure', 0);
        $session_name = 'DEV_SESSID';
        if (function_exists('error_log')) {
            error_log('WARNING: Session running without HTTPS in development mode');
        }
    }
    
    // Set session name
    session_name($session_name);
    
    // Session lifetime (2 hours)
    ini_set('session.gc_maxlifetime', 7200);
    ini_set('session.cookie_lifetime', 7200);
    
    return [
        'https_enabled' => $is_https,
        'session_name' => $session_name,
        'secure_cookies' => $is_https
    ];
}

// Function to detect HTTPS
if (!function_exists('is_running_https')) {
    function is_running_https() {
        return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || 
               (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') ||
               (!empty($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);
    }
}

// Auto-configure when this file is included
$session_config = configure_secure_sessions();

// Function to check session security status
function get_session_security_status() {
    global $session_config;
    
    return [
        'httponly' => ini_get('session.cookie_httponly') ? '✅' : '❌',
        'secure' => ini_get('session.cookie_secure') ? '✅' : ($session_config['https_enabled'] ? '❌' : '⚠️ HTTP Dev'),
        'strict_mode' => ini_get('session.use_strict_mode') ? '✅' : '❌',
        'samesite' => ini_get('session.cookie_samesite') ? '✅' : '❌',
        'environment' => $session_config['https_enabled'] ? 'HTTPS Production' : 'HTTP Development'
    ];
}
?>