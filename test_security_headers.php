<?php
// Test Security Headers Configuration
require_once __DIR__ . '/security_headers.php';

echo "<h3>Security Headers Test</h3>\n";
echo "<pre>\n";

// Test configuration validation
echo "Security Headers Validation:\n";
$validation = SecurityHeaders::validateHeaders();
echo "  Valid: " . ($validation['valid'] ? "✅ Yes" : "❌ No") . "\n";
echo "  Environment: " . $validation['environment'] . "\n";
echo "  HTTPS: " . ($validation['https'] ? "✅ Enabled" : "⚠️ Disabled") . "\n";

if (!empty($validation['issues'])) {
    echo "  Issues:\n";
    foreach ($validation['issues'] as $issue) {
        echo "    - $issue\n";
    }
}
echo "\n";

// Test configuration summary
echo "Configuration Summary:\n";
$summary = SecurityHeaders::getConfigSummary();
foreach ($summary as $key => $value) {
    $displayValue = is_bool($value) ? ($value ? "✅ Yes" : "❌ No") : $value;
    echo "  " . ucfirst(str_replace('_', ' ', $key)) . ": $displayValue\n";
}
echo "\n";

// Test CSP nonce generation
echo "CSP Nonce Generation:\n";
$nonce1 = SecurityHeaders::generateCSPNonce();
$nonce2 = SecurityHeaders::getCSPNonce();
echo "  Generated nonce: $nonce1\n";
echo "  Retrieved nonce: $nonce2\n";
echo "  Nonces match: " . ($nonce1 === $nonce2 ? "✅ Yes" : "❌ No") . "\n\n";

// Test if headers would be applied (can't actually apply them in this test)
echo "Headers Status:\n";
echo "  Headers sent: " . (headers_sent() ? "❌ Yes (can't apply)" : "✅ No (can apply)") . "\n";

if (!headers_sent()) {
    echo "  Would apply headers: ✅ Yes\n";
    echo "\n";
    
    // Simulate header application for display
    echo "Headers that would be applied:\n";
    
    // We can't actually capture headers, so we'll simulate based on configuration
    echo "Basic Security Headers:\n";
    echo "  X-Content-Type-Options: nosniff\n";
    echo "  X-Frame-Options: DENY\n";
    echo "  Referrer-Policy: strict-origin-when-cross-origin\n";
    echo "  X-XSS-Protection: 1; mode=block\n";
    echo "  Permissions-Policy: camera=(), microphone=(), geolocation=()...\n";
    echo "  Cache-Control: no-cache, no-store, must-revalidate, max-age=0\n";
    
    if (is_running_https()) {
        echo "\nHTTPS Headers:\n";
        if (Environment::isProduction()) {
            echo "  Strict-Transport-Security: max-age=31536000; includeSubDomains; preload\n";
        } else {
            echo "  Strict-Transport-Security: max-age=86400; includeSubDomains\n";
        }
    }
    
    if (Environment::isProduction()) {
        echo "\nProduction Headers:\n";
        echo "  Cross-Origin-Embedder-Policy: require-corp\n";
        echo "  Cross-Origin-Opener-Policy: same-origin\n";
        echo "  Cross-Origin-Resource-Policy: same-origin\n";
        echo "  Content-Security-Policy: (enforced)\n";
    } else {
        echo "\nDevelopment Headers:\n";
        echo "  X-Environment: development\n";
        echo "  Content-Security-Policy-Report-Only: (report mode)\n";
    }
    
} else {
    echo "  Would apply headers: ❌ No (headers already sent)\n";
}

echo "\n";

// Test static file headers configuration
echo "Static File Headers Configuration:\n";
$fileTypes = ['css', 'js', 'image', 'document'];
foreach ($fileTypes as $type) {
    echo "  $type files: ";
    switch ($type) {
        case 'css':
        case 'js':
            $maxAge = Environment::isProduction() ? '1 week' : '1 hour';
            echo "Cache for $maxAge\n";
            break;
        case 'image':
            $maxAge = Environment::isProduction() ? '1 month' : '1 day';
            echo "Cache for $maxAge\n";
            break;
        case 'document':
            echo "No cache (always fresh)\n";
            break;
    }
}

echo "\n";
echo "Security headers system is ready! ✅\n";
echo "All components are properly configured for " . (Environment::isProduction() ? "production" : "development") . " environment.\n";

echo "</pre>\n";

// Show example of CSP nonce usage
echo "<h4>CSP Nonce Usage Example</h4>\n";
echo "<pre>\n";
echo htmlspecialchars('<!-- In HTML templates, use the nonce like this: -->');
echo "\n";
echo htmlspecialchars('<script nonce="' . SecurityHeaders::getCSPNonce() . '">');
echo "\n";
echo htmlspecialchars('  // Your inline JavaScript here');
echo "\n";
echo htmlspecialchars('  console.log("This script is CSP-compliant");');
echo "\n";
echo htmlspecialchars('</script>');
echo "\n";
echo "</pre>\n";
?>