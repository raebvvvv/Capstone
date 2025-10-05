<?php
// Environment Configuration
// Loads environment variables from .env file or system environment

class Environment {
    private static $loaded = false;
    private static $env_vars = [];
    
    public static function load() {
        if (self::$loaded) {
            return;
        }
        
        // Try to load from .env file
        $env_file = __DIR__ . '/.env';
        if (file_exists($env_file)) {
            self::loadFromFile($env_file);
        }
        
        // Load from system environment (overrides .env values)
        self::loadFromSystem();
        
        self::$loaded = true;
    }
    
    private static function loadFromFile($file) {
        $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            // Skip comments
            if (strpos(trim($line), '#') === 0) {
                continue;
            }
            
            // Parse KEY=VALUE format
            if (strpos($line, '=') !== false) {
                list($key, $value) = explode('=', $line, 2);
                $key = trim($key);
                $value = trim($value);
                
                // Remove quotes if present
                if (preg_match('/^"(.*)"$/', $value, $matches)) {
                    $value = $matches[1];
                } elseif (preg_match("/^'(.*)'$/", $value, $matches)) {
                    $value = $matches[1];
                }
                
                self::$env_vars[$key] = $value;
            }
        }
    }
    
    private static function loadFromSystem() {
        // Common environment variable names to check
        $env_keys = [
            'DB_HOST', 'DB_NAME', 'DB_USERNAME', 'DB_PASSWORD',
            'SMTP_HOST', 'SMTP_PORT', 'SMTP_USERNAME', 'SMTP_PASSWORD',
            'SMTP_FROM_EMAIL', 'SMTP_FROM_NAME',
            'SESSION_LIFETIME', 'ENVIRONMENT', 'DEBUG_MODE',
            'APP_NAME', 'APP_URL', 'ADMIN_EMAIL',
            'CSRF_SECRET', 'SESSION_ENCRYPT_KEY'
        ];
        
        foreach ($env_keys as $key) {
            $value = getenv($key);
            if ($value !== false) {
                self::$env_vars[$key] = $value;
            }
        }
    }
    
    public static function get($key, $default = null) {
        self::load();
        
        // Check custom loaded vars first
        if (isset(self::$env_vars[$key])) {
            return self::$env_vars[$key];
        }
        
        // Check system environment
        $value = getenv($key);
        if ($value !== false) {
            return $value;
        }
        
        return $default;
    }
    
    public static function getBool($key, $default = false) {
        $value = self::get($key, $default);
        if (is_bool($value)) {
            return $value;
        }
        return in_array(strtolower($value), ['true', '1', 'yes', 'on']);
    }
    
    public static function getInt($key, $default = 0) {
        return (int) self::get($key, $default);
    }
    
    public static function isProduction() {
        return strtolower(self::get('ENVIRONMENT', 'development')) === 'production';
    }
    
    public static function isDevelopment() {
        return !self::isProduction();
    }
    
    public static function getAllLoaded() {
        self::load();
        return self::$env_vars;
    }
}

// Auto-load environment when this file is included
Environment::load();
?>