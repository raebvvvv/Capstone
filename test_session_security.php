<?php
// Test session security configuration
require_once __DIR__ . '/secure_session_config.php';

echo "<h3>Session Security Test</h3>\n";
echo "<pre>\n";

// Test the security configuration
$status = get_session_security_status();
echo "Session Security Status:\n";
foreach ($status as $key => $value) {
    echo "  $key: $value\n";
}

echo "\nHTTPS Detection: " . (is_running_https() ? "✅ HTTPS" : "⚠️ HTTP") . "\n";
echo "Session Name: " . session_name() . "\n";
echo "Session ID: " . (session_id() ?: "Not started") . "\n";

// Test session values
echo "\nSession Variables:\n";
if (isset($_SESSION)) {
    foreach ($_SESSION as $key => $value) {
        echo "  $key: " . (is_string($value) ? $value : gettype($value)) . "\n";
    }
} else {
    echo "  No session variables set\n";
}

echo "</pre>\n";
?>