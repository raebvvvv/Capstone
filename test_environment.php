<?php
// Test Environment Configuration
require_once __DIR__ . '/env_config.php';

echo "<h3>Environment Configuration Test</h3>\n";
echo "<pre>\n";

echo "Environment Detection:\n";
echo "  Environment: " . Environment::get('ENVIRONMENT', 'development') . "\n";
echo "  Is Production: " . (Environment::isProduction() ? "Yes" : "No") . "\n";
echo "  Is Development: " . (Environment::isDevelopment() ? "Yes" : "No") . "\n\n";

echo "Database Configuration:\n";
echo "  Host: " . Environment::get('DB_HOST', 'localhost') . "\n";
echo "  Database: " . Environment::get('DB_NAME', 'ipmo_users') . "\n";
echo "  Username: " . Environment::get('DB_USERNAME', 'root') . "\n";
echo "  Password: " . (Environment::get('DB_PASSWORD') ? "[SET]" : "[EMPTY]") . "\n";
echo "  Port: " . Environment::get('DB_PORT', '3306') . "\n\n";

echo "Email Configuration:\n";
echo "  SMTP Host: " . Environment::get('SMTP_HOST', 'smtp.gmail.com') . "\n";
echo "  SMTP Port: " . Environment::get('SMTP_PORT', '587') . "\n";
echo "  SMTP Username: " . Environment::get('SMTP_USERNAME', 'not set') . "\n";
echo "  SMTP Password: " . (Environment::get('SMTP_PASSWORD') ? "[SET]" : "[NOT SET]") . "\n";
echo "  From Email: " . Environment::get('SMTP_FROM_EMAIL', 'not set') . "\n";
echo "  From Name: " . Environment::get('SMTP_FROM_NAME', 'PUP e-IPMO') . "\n\n";

echo "Security Configuration:\n";
echo "  Session Lifetime: " . Environment::get('SESSION_LIFETIME', '28800') . " seconds\n";
echo "  Debug Mode: " . (Environment::getBool('DEBUG_MODE', false) ? "Enabled" : "Disabled") . "\n";
echo "  CSRF Secret: " . (Environment::get('CSRF_SECRET') ? "[SET]" : "[NOT SET]") . "\n\n";

echo "Application Configuration:\n";
echo "  App Name: " . Environment::get('APP_NAME', 'IPMO System') . "\n";
echo "  App URL: " . Environment::get('APP_URL', 'http://localhost/Capstone') . "\n";
echo "  Admin Email: " . Environment::get('ADMIN_EMAIL', 'not set') . "\n\n";

// Test database connection with environment config
try {
    require_once __DIR__ . '/conn.php';
    echo "Database Connection: ✅ SUCCESS\n";
} catch (Exception $e) {
    echo "Database Connection: ❌ FAILED - " . $e->getMessage() . "\n";
}

// Test email configuration loading
try {
    $email_config = require_once __DIR__ . '/email_config.php';
    echo "Email Config Load: ✅ SUCCESS\n";
    echo "  SMTP Host from config: " . $email_config['smtp']['host'] . "\n";
} catch (Exception $e) {
    echo "Email Config Load: ❌ FAILED - " . $e->getMessage() . "\n";
}

echo "\nAll loaded environment variables:\n";
$all_vars = Environment::getAllLoaded();
foreach ($all_vars as $key => $value) {
    // Hide sensitive values
    if (in_array($key, ['DB_PASSWORD', 'SMTP_PASSWORD', 'CSRF_SECRET', 'SESSION_ENCRYPT_KEY'])) {
        $display_value = "[HIDDEN]";
    } else {
        $display_value = $value;
    }
    echo "  $key: $display_value\n";
}

echo "</pre>\n";
?>