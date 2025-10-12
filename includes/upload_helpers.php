<?php
// Upload Helper Functions
// Integration layer for enhanced upload validation

// Ensure storage_path() and Environment are available
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/upload_validator.php';

/**
 * Enhanced file upload with comprehensive validation
 * Replaces basic move_uploaded_file() calls with secure validation
 */
function secure_upload_file($fileInput, $fieldName, $destinationDir) {
    // Validate the uploaded file
    $validation = UploadValidator::validateFile($fileInput, $fieldName);
    
    if (!$validation['valid']) {
        return [
            'success' => false,
            'errors' => $validation['errors'],
            'filename' => null
        ];
    }
    
    // Ensure destination directory exists
    if (!is_dir($destinationDir)) {
        if (!@mkdir($destinationDir, 0755, true)) {
            return [
                'success' => false,
                'errors' => ['Cannot create upload directory'],
                'filename' => null
            ];
        }
    }
    
    // Create destination path with sanitized filename
    $destination = $destinationDir . DIRECTORY_SEPARATOR . $validation['sanitized_name'];
    
    // Perform secure move
    $moveResult = UploadValidator::secureMove($fileInput, $destination, $validation);
    
    if ($moveResult['success']) {
        return [
            'success' => true,
            'errors' => [],
            'filename' => $validation['sanitized_name'],
            'original_name' => $validation['original_name'],
            'size' => $validation['size'],
            'type' => $validation['mime_type']
        ];
    } else {
        return [
            'success' => false,
            'errors' => $moveResult['errors'],
            'filename' => null
        ];
    }
}

/**
 * Validate multiple files (for forms with multiple file uploads)
 */
function validate_multiple_files($files, $fieldNames = null) {
    $results = [];
    $allValid = true;
    $allErrors = [];
    
    foreach ($files as $fieldName => $fileInput) {
        $displayName = $fieldNames[$fieldName] ?? $fieldName;
        
        if (!isset($fileInput['error']) || $fileInput['error'] === UPLOAD_ERR_NO_FILE) {
            $results[$fieldName] = [
                'valid' => false,
                'errors' => ["No file uploaded for $displayName"],
                'filename' => null
            ];
            $allValid = false;
            $allErrors = array_merge($allErrors, $results[$fieldName]['errors']);
            continue;
        }
        
        $validation = UploadValidator::validateFile($fileInput, $displayName);
        $results[$fieldName] = $validation;
        
        if (!$validation['valid']) {
            $allValid = false;
            $allErrors = array_merge($allErrors, $validation['errors']);
        }
    }
    
    return [
        'all_valid' => $allValid,
        'all_errors' => $allErrors,
        'individual_results' => $results
    ];
}

/**
 * Get user-friendly upload limits for display in forms
 */
function get_upload_limits_display() {
    $limits = UploadValidator::getUploadLimits();
    
    return [
        'max_size_text' => $limits['max_size_mb'] . 'MB',
        'allowed_types_text' => strtoupper(implode(', ', $limits['allowed_extensions'])),
        'max_filename_length' => $limits['max_filename_length']
    ];
}

/**
 * Generate HTML for upload requirements display
 */
function display_upload_requirements() {
    $limits = get_upload_limits_display();
    
    $html = '<div class="upload-requirements text-muted small mb-3">';
    $html .= '<strong>Upload Requirements:</strong><br>';
    $html .= '• Maximum file size: ' . $limits['max_size_text'] . '<br>';
    $html .= '• Allowed file types: ' . $limits['allowed_types_text'] . '<br>';
    $html .= '• Maximum filename length: ' . $limits['max_filename_length'] . ' characters<br>';
    $html .= '• Files are scanned for security threats';
    $html .= '</div>';
    
    return $html;
}

/**
 * Enhanced error handling for upload failures
 */
function handle_upload_errors($errors, $uploadedFiles = []) {
    // Clean up any successfully uploaded files if there were errors
    foreach ($uploadedFiles as $filename) {
        if (file_exists($filename)) {
            @unlink($filename);
        }
    }
    
    // Log upload failures for security monitoring
    $logEntry = date('c') . ' | UPLOAD_FAILURE | Errors: ' . implode('; ', $errors) . PHP_EOL;
    $logFile = storage_path('logs/upload_errors.log');
    $logDir = dirname($logFile);
    if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
    file_put_contents($logFile, $logEntry, FILE_APPEND);
    
    return $errors;
}

/**
 * Check if file upload is safe to proceed
 * This can be used as a pre-check before processing forms
 */
function is_upload_safe($maxFiles = 10, $totalSizeLimit = null) {
    // Check if we're under attack (too many files)
    if (count($_FILES) > $maxFiles) {
        return [
            'safe' => false,
            'reason' => 'Too many files in upload request'
        ];
    }
    
    // Check total upload size
    if ($totalSizeLimit) {
        $totalSize = 0;
        foreach ($_FILES as $file) {
            if (isset($file['size'])) {
                $totalSize += $file['size'];
            }
        }
        
        if ($totalSize > $totalSizeLimit) {
            return [
                'safe' => false,
                'reason' => 'Total upload size exceeds limit'
            ];
        }
    }
    
    // Check for suspicious patterns in filenames
    foreach ($_FILES as $fieldName => $file) {
        if (isset($file['name'])) {
            // Check for directory traversal attempts
            if (strpos($file['name'], '..') !== false || strpos($file['name'], '/') !== false) {
                return [
                    'safe' => false,
                    'reason' => "Suspicious filename detected: {$file['name']}"
                ];
            }
            
            // Check for extremely long filenames (potential DoS)
            if (strlen($file['name']) > 255) {
                return [
                    'safe' => false,
                    'reason' => 'Filename too long'
                ];
            }
        }
    }
    
    return ['safe' => true];
}

/**
 * Generate secure upload form HTML with validation
 */
function secure_file_input($fieldName, $displayName, $required = true, $accept = null) {
    $limits = get_upload_limits_display();
    $acceptTypes = $accept ?: '.' . implode(',.', UploadValidator::getUploadLimits()['allowed_extensions']);
    $requiredAttr = $required ? 'required' : '';
    
    $html = '<div class="mb-3">';
    $html .= '<label for="' . htmlspecialchars($fieldName) . '" class="form-label">';
    $html .= htmlspecialchars($displayName);
    if ($required) $html .= ' <span class="text-danger">*</span>';
    $html .= '</label>';
    $html .= '<input type="file" class="form-control" id="' . htmlspecialchars($fieldName) . '" ';
    $html .= 'name="' . htmlspecialchars($fieldName) . '" ';
    $html .= 'accept="' . htmlspecialchars($acceptTypes) . '" ';
    $html .= $requiredAttr . '>';
    $html .= '<div class="form-text">Max size: ' . $limits['max_size_text'] . '</div>';
    $html .= '</div>';
    
    return $html;
}
?>