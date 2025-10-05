<?php
// Quick test to verify security_headers.php loads correctly
echo "Testing security_headers.php loading...\n";

try {
    require_once __DIR__ . '/security_headers.php';
    echo "✅ security_headers.php loaded successfully\n";
    
    // Test SecurityHeaders class
    if (class_exists('SecurityHeaders')) {
        echo "✅ SecurityHeaders class available\n";
        
        // Test init method
        SecurityHeaders::init();
        echo "✅ SecurityHeaders::init() executed successfully\n";
        
        // Test configuration summary
        $summary = SecurityHeaders::getConfigSummary();
        echo "✅ Configuration summary retrieved\n";
        
        // Display summary
        echo "\nConfiguration Summary:\n";
        foreach ($summary as $key => $value) {
            $displayValue = is_bool($value) ? ($value ? "Yes" : "No") : $value;
            echo "  " . ucfirst(str_replace('_', ' ', $key)) . ": $displayValue\n";
        }
        
    } else {
        echo "❌ SecurityHeaders class not found\n";
    }
    
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
} catch (Error $e) {
    echo "❌ Fatal Error: " . $e->getMessage() . "\n";
}

echo "\nTest complete.\n";
?>