<?php
// CSP Violation Report Handler
// Receives and logs Content Security Policy violation reports

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed');
}

// Check content type
$contentType = $_SERVER['CONTENT_TYPE'] ?? '';
if (strpos($contentType, 'application/csp-report') === false && 
    strpos($contentType, 'application/json') === false) {
    http_response_code(400);
    exit('Invalid content type');
}

try {
    // Read the raw POST data
    $input = file_get_contents('php://input');
    
    if (empty($input)) {
        http_response_code(400);
        exit('No data received');
    }
    
    // Parse JSON
    $report = json_decode($input, true);
    
    if (json_last_error() !== JSON_ERROR_NONE) {
        http_response_code(400);
        exit('Invalid JSON');
    }
    
    // Validate report structure
    if (!isset($report['csp-report'])) {
        http_response_code(400);
        exit('Invalid CSP report structure');
    }
    
    $cspReport = $report['csp-report'];
    
    // Required fields check
    $requiredFields = ['document-uri', 'violated-directive'];
    foreach ($requiredFields as $field) {
        if (!isset($cspReport[$field])) {
            http_response_code(400);
            exit("Missing required field: $field");
        }
    }
    
    // Rate limiting - prevent spam
    $clientIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    $rateLimitFile = __DIR__ . '/csp_rate_limit.json';
    $rateLimit = [];
    
    if (file_exists($rateLimitFile)) {
        $rateLimit = json_decode(file_get_contents($rateLimitFile), true) ?: [];
    }
    
    $now = time();
    $windowSize = 300; // 5 minutes
    $maxReports = 10;  // Max 10 reports per IP per 5 minutes
    
    // Clean old entries
    foreach ($rateLimit as $ip => $data) {
        if ($data['timestamp'] < ($now - $windowSize)) {
            unset($rateLimit[$ip]);
        }
    }
    
    // Check current IP rate limit
    if (isset($rateLimit[$clientIP])) {
        if ($rateLimit[$clientIP]['count'] >= $maxReports) {
            http_response_code(429);
            exit('Rate limit exceeded');
        }
        $rateLimit[$clientIP]['count']++;
    } else {
        $rateLimit[$clientIP] = ['timestamp' => $now, 'count' => 1];
    }
    
    // Save rate limit data
    file_put_contents($rateLimitFile, json_encode($rateLimit));
    
    // Log the CSP violation
    $logEntry = [
        'timestamp' => date('c'),
        'client_ip' => $clientIP,
        'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? 'unknown',
        'document_uri' => $cspReport['document-uri'],
        'violated_directive' => $cspReport['violated-directive'],
        'blocked_uri' => $cspReport['blocked-uri'] ?? 'unknown',
        'source_file' => $cspReport['source-file'] ?? 'unknown',
        'line_number' => $cspReport['line-number'] ?? 'unknown',
        'column_number' => $cspReport['column-number'] ?? 'unknown',
        'effective_directive' => $cspReport['effective-directive'] ?? 'unknown'
    ];
    
    // Write to CSP violation log
    $logLine = json_encode($logEntry) . PHP_EOL;
    file_put_contents(__DIR__ . '/csp_violations.log', $logLine, FILE_APPEND | LOCK_EX);
    
    // For high-severity violations, also log to security audit
    $highSeverityDirectives = [
        'script-src',
        'object-src',
        'base-uri',
        'frame-ancestors'
    ];
    
    $violatedDirective = $cspReport['violated-directive'];
    foreach ($highSeverityDirectives as $directive) {
        if (strpos($violatedDirective, $directive) !== false) {
            $auditEntry = date('c') . ' | CSP_VIOLATION_HIGH | ' . 
                         'IP: ' . $clientIP . ' | ' .
                         'Directive: ' . $violatedDirective . ' | ' .
                         'URI: ' . $cspReport['document-uri'] . ' | ' .
                         'Blocked: ' . ($cspReport['blocked-uri'] ?? 'unknown') . PHP_EOL;
            file_put_contents(__DIR__ . '/audit.log', $auditEntry, FILE_APPEND | LOCK_EX);
            break;
        }
    }
    
    // Return success response
    http_response_code(204); // No Content
    
} catch (Exception $e) {
    // Log the error
    error_log('CSP Report Handler Error: ' . $e->getMessage());
    http_response_code(500);
    exit('Internal server error');
}
?>