<?php
// Production Security Headers Configuration
// Comprehensive security headers for production deployment

require_once __DIR__ . '/env_config.php';
require_once __DIR__ . '/secure_session_config.php';

class SecurityHeaders {
    
    private static $isProduction;
    private static $isHttps;
    private static $baseUrl;
    
    public static function init() {
        self::$isProduction = Environment::isProduction();
        self::$isHttps = function_exists('is_running_https') ? is_running_https() : false;
        // Prefer explicit APP_URL, otherwise use global BASE_URL if defined, fallback to localhost
        $appUrl = Environment::get('APP_URL');
        if (!$appUrl && defined('BASE_URL')) {
            $appUrl = BASE_URL;
        }
        self::$baseUrl = $appUrl ?: 'http://localhost/Capstone/';
    }
    
    /**
     * Apply comprehensive security headers based on environment
     */
    public static function applyHeaders() {
        self::init();
        
        if (headers_sent()) {
            return false;
        }
        
        // Basic security headers (always apply)
        self::applyBasicHeaders();
        
        // HTTPS-specific headers
        if (self::$isHttps) {
            self::applyHttpsHeaders();
        }
        
        // Production-specific headers
        if (self::$isProduction) {
            self::applyProductionHeaders();
        } else {
            self::applyDevelopmentHeaders();
        }
        
        // Content Security Policy
        self::applyCSP();
        
        return true;
    }
    
    /**
     * Basic security headers for all environments
     */
    private static function applyBasicHeaders() {
        // Prevent content type sniffing
        header('X-Content-Type-Options: nosniff');
        
    // Prevent clickjacking (allow same-origin embedding for internal PDF iframes)
    header('X-Frame-Options: SAMEORIGIN');
        
        // Control referrer information
        header('Referrer-Policy: strict-origin-when-cross-origin');
        
        // Prevent MIME type confusion attacks
        header('X-Content-Type-Options: nosniff');
        
        // Disable potentially dangerous browser features
        header('Permissions-Policy: camera=(), microphone=(), geolocation=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()');
        
        // XSS Protection (legacy but still useful)
        header('X-XSS-Protection: 1; mode=block');
        
        // Prevent caching of sensitive pages (can be overridden per page)
        header('Cache-Control: no-cache, no-store, must-revalidate, max-age=0');
        header('Pragma: no-cache');
        header('Expires: Thu, 01 Jan 1970 00:00:00 GMT');
    }
    
    /**
     * HTTPS-specific security headers
     */
    private static function applyHttpsHeaders() {
        // HTTP Strict Transport Security
        if (self::$isProduction) {
            // Production: longer max-age with includeSubDomains and preload
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        } else {
            // Development: shorter max-age without preload
            header('Strict-Transport-Security: max-age=86400; includeSubDomains');
        }
        
        // Ensure cookies are secure in HTTPS (only when session not started yet)
        if (session_status() === PHP_SESSION_NONE) {
            @ini_set('session.cookie_secure', '1');
        }
    }
    
    /**
     * Production-specific security headers
     */
    private static function applyProductionHeaders() {
        // More restrictive caching for production
        header('Cache-Control: no-cache, no-store, must-revalidate, private, max-age=0');
        
        // Additional security headers for production
        header('X-Permitted-Cross-Domain-Policies: none');
        header('Cross-Origin-Embedder-Policy: require-corp');
        header('Cross-Origin-Opener-Policy: same-origin');
        header('Cross-Origin-Resource-Policy: same-origin');
        
        // Prevent DNS rebinding attacks
        if (isset($_SERVER['HTTP_HOST'])) {
            $allowedHosts = [
                parse_url(self::$baseUrl, PHP_URL_HOST),
                'www.' . parse_url(self::$baseUrl, PHP_URL_HOST)
            ];
            
            if (!in_array($_SERVER['HTTP_HOST'], $allowedHosts)) {
                http_response_code(400);
                exit('Invalid host header');
            }
        }
    }
    
    /**
     * Development-specific headers (more permissive for debugging)
     */
    private static function applyDevelopmentHeaders() {
        // Allow some caching in development for performance
        header('Cache-Control: no-cache, must-revalidate, max-age=0');
        
        // Add development indicator (remove in production)
        header('X-Environment: development');
    }
    
    /**
     * Content Security Policy configuration
     */
    private static function applyCSP() {
        $cspDirectives = [];
        
        // Default source restrictions
        $cspDirectives[] = "default-src 'self'";
        
        // Script sources
        if (self::$isProduction) {
            // Production: strict script policy
            $scriptSrc = "script-src 'self' 'nonce-" . self::generateCSPNonce() . "' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com";
        } else {
            // Development: allow unsafe-inline for easier development
            $scriptSrc = "script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com";
        }
        $cspDirectives[] = $scriptSrc;
        // Explicitly set script-src-elem to mirror script-src to avoid browser fallback warnings
        $cspDirectives[] = str_replace('script-src', 'script-src-elem', $scriptSrc);
        
        // Style sources
    $styleSrc = "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com https://fonts.googleapis.com";
    $cspDirectives[] = $styleSrc;
    // Explicitly set style-src-elem to mirror style-src
    $cspDirectives[] = str_replace('style-src', 'style-src-elem', $styleSrc);
        
        // Image sources
        $cspDirectives[] = "img-src 'self' data: https://cdn.jsdelivr.net https://cdnjs.cloudflare.com";
        
        // Font sources
        $cspDirectives[] = "font-src 'self' data: https://fonts.gstatic.com https://fonts.googleapis.com";
        
    // Object/embed restrictions
    // Note: 'embed-src' is not a valid CSP Level 3 directive; use object-src and frame-src/child-src instead
    $cspDirectives[] = "object-src 'none'";
        
    // Frame restrictions (allow internal certificate iframe only)
        $currentPath = $_SERVER['REQUEST_URI'] ?? '';
        $isCertViewer = (strpos($currentPath, 'admin/view_certificate.php') !== false);
        // We embed the PDF inside an iframe from admin/ticket.php, so permit same-origin frame embedding for that viewer
        if ($isCertViewer) {
            // The viewer itself does not need to be framed by other sites, only by same-origin admin pages
            $cspDirectives[] = "frame-ancestors 'self'"; // allow same-origin only
            $cspDirectives[] = "frame-src 'self'";       // allow loading internal frames (future expansion)
        } else {
            $cspDirectives[] = "frame-ancestors 'self'"; // keep same-origin so internal pages can iframe certs
            // Default: no external frames. If later you need YouTube/Vimeo add them here.
            $cspDirectives[] = "frame-src 'self'";
        }
        
        // Base URI restrictions
        $cspDirectives[] = "base-uri 'self'";
        
        // Form action restrictions
        $cspDirectives[] = "form-action 'self'";
        
        // Media sources
        $cspDirectives[] = "media-src 'self'";
        
    // Connection sources (allow same-origin and approved CDNs used for scripts/styles)
    $cspDirectives[] = "connect-src 'self' https://cdn.jsdelivr.net https://cdnjs.cloudflare.com";
        
        // Worker sources
        $cspDirectives[] = "worker-src 'none'";
        
        // Manifest sources
        $cspDirectives[] = "manifest-src 'self'";
        
    // Compute report URI using absolute URL to avoid host rewrites on shared hosting
    $absBase = rtrim(self::$baseUrl, '/');
    $defaultReport = $absBase . '/csp_report.php';
    $reportUri = Environment::get('CSP_REPORT_URI', $defaultReport);

        if (self::$isProduction) {
            // Production: enforce CSP with reporting
            $cspDirectives[] = "report-uri $reportUri";
            header('Content-Security-Policy: ' . implode('; ', $cspDirectives));
        } else {
            // Development: report-only mode for testing (with report URI to suppress browser warnings)
            $cspDirectives[] = "report-uri $reportUri";
            header('Content-Security-Policy-Report-Only: ' . implode('; ', $cspDirectives));
        }
    }
    
    /**
     * Generate CSP nonce for inline scripts
     */
    public static function generateCSPNonce() {
        if (!isset($_SESSION['csp_nonce'])) {
            $_SESSION['csp_nonce'] = base64_encode(random_bytes(16));
        }
        return $_SESSION['csp_nonce'];
    }
    
    /**
     * Get CSP nonce for use in templates
     */
    public static function getCSPNonce() {
        return self::generateCSPNonce();
    }
    
    /**
     * Apply headers for static file serving
     */
    public static function applyStaticFileHeaders($fileType = null) {
        self::init();
        
        if (headers_sent()) {
            return false;
        }
        
        // Basic security headers
        header('X-Content-Type-Options: nosniff');
    // X-Frame-Options: use SAMEORIGIN so admin pages can embed internal certificate PDF
    header('X-Frame-Options: SAMEORIGIN');
        
        // Cache control for static files
        if ($fileType) {
            switch ($fileType) {
                case 'css':
                case 'js':
                    // Cache CSS/JS for 1 hour in dev, 1 week in production
                    $maxAge = self::$isProduction ? 604800 : 3600;
                    header("Cache-Control: public, max-age=$maxAge");
                    break;
                    
                case 'image':
                    // Cache images for 1 day in dev, 1 month in production
                    $maxAge = self::$isProduction ? 2592000 : 86400;
                    header("Cache-Control: public, max-age=$maxAge");
                    break;
                    
                case 'document':
                    // Don't cache documents
                    header('Cache-Control: no-cache, no-store, must-revalidate');
                    break;
            }
        }
        
        return true;
    }
    
    /**
     * Check if security headers are properly configured
     */
    public static function validateHeaders() {
        $issues = [];
        
        // Check if HTTPS is available in production
        if (self::$isProduction && !self::$isHttps) {
            $issues[] = 'HTTPS should be enabled in production';
        }
        
        // Check if environment variables are set
        $requiredVars = ['APP_URL', 'ENVIRONMENT'];
        foreach ($requiredVars as $var) {
            if (!Environment::get($var)) {
                $issues[] = "Environment variable $var is not set";
            }
        }
        
        return [
            'valid' => empty($issues),
            'issues' => $issues,
            'environment' => self::$isProduction ? 'production' : 'development',
            'https' => self::$isHttps
        ];
    }
    
    /**
     * Get security headers configuration summary
     */
    public static function getConfigSummary() {
        self::init();
        
        return [
            'environment' => self::$isProduction ? 'production' : 'development',
            'https_enabled' => self::$isHttps,
            'base_url' => self::$baseUrl,
            'hsts_enabled' => self::$isHttps,
            'csp_mode' => self::$isProduction ? 'enforced' : 'report-only',
            'headers_applied' => !headers_sent()
        ];
    }
}

// Auto-initialize when file is included
SecurityHeaders::init();
?>