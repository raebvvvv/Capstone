<?php
// CSP Violation Report Handler
// Receives and logs Content Security Policy violation reports

// Only allow POST requests
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit('Method not allowed');
}

// Check content type (be lenient on shared hosts which may omit/mangle headers)
$contentType = $_SERVER['CONTENT_TYPE'] ?? ($_SERVER['HTTP_CONTENT_TYPE'] ?? '');
if ($contentType && strpos($contentType, 'application/csp-report') === false && strpos($contentType, 'application/json') === false) {
    // Don't reject if header missing; only reject when header present and clearly wrong
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
    $canWrite = is_writable(__DIR__);
    
    if ($canWrite && file_exists($rateLimitFile)) {
        $rateLimit = json_decode(@file_get_contents($rateLimitFile), true) ?: [];
    }
    
    $now = time();
    $windowSize = 300; // 5 minutes
    $maxReports = 10;  // Max 10 reports per IP per 5 minutes
    
    // Clean old entries
    if ($canWrite) {
        foreach ($rateLimit as $ip => $data) {
            if (($data['timestamp'] ?? 0) < ($now - $windowSize)) {
                unset($rateLimit[$ip]);
            }
        }
    }
    
    // Check current IP rate limit
    if ($canWrite) {
        if (isset($rateLimit[$clientIP])) {
            if (($rateLimit[$clientIP]['count'] ?? 0) >= $maxReports) {
                http_response_code(204); // Quietly drop when rate-limited
                exit();
            }
            $rateLimit[$clientIP]['count'] = ($rateLimit[$clientIP]['count'] ?? 0) + 1;
        } else {
            $rateLimit[$clientIP] = ['timestamp' => $now, 'count' => 1];
        }
    }
    
    // Save rate limit data
    if ($canWrite) { @file_put_contents($rateLimitFile, json_encode($rateLimit)); }
    
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
    if ($canWrite) { @file_put_contents(__DIR__ . '/csp_violations.log', $logLine, FILE_APPEND | LOCK_EX); }
    
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
            if ($canWrite) { @file_put_contents(__DIR__ . '/audit.log', $auditEntry, FILE_APPEND | LOCK_EX); }
            break;
        }
    }
    
    // Return success response with no body
    http_response_code(204); // No Content
    header('Content-Length: 0');
    exit();
    
} catch (Exception $e) {
    // Log the error
    // Fail closed but silent in production-like hosting; avoid provider 302 error pages
    http_response_code(204);
    header('Content-Length: 0');
    exit();
}
?>