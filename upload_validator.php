<?php
// Enhanced File Upload Validation
// Comprehensive security validation for file uploads

// Use full app config to access storage_path() so quarantine/uploads are outside webroot
require_once __DIR__ . '/config.php';

class UploadValidator {
    
    // Configuration from environment variables
    private static $maxFileSize;
    private static $allowedExtensions;
    private static $allowedMimeTypes;
    private static $maxFilenameLength = 100;
    private static $quarantineDir;
    
    public static function init() {
        self::$maxFileSize = Environment::getInt('MAX_UPLOAD_SIZE', 52428800); // 50MB default
        // Only allow PDF files site-wide by default. Override via ALLOWED_EXTENSIONS if needed.
        self::$allowedExtensions = explode(',', Environment::get('ALLOWED_EXTENSIONS', 'pdf'));
        self::$allowedExtensions = array_values(array_filter(array_map('strtolower', array_map('trim', self::$allowedExtensions))));
        self::$allowedMimeTypes = [
            'pdf' => ['application/pdf', 'application/x-pdf', 'application/acrobat', 'applications/pdf']
        ];
    // Keep quarantine outside webroot
    self::$quarantineDir = storage_path('quarantine');
        
        // Create quarantine directory if it doesn't exist
        if (!is_dir(self::$quarantineDir)) {
            @mkdir(self::$quarantineDir, 0700, true);
        }
    }
    
    /**
     * Comprehensive file validation
     * @param array $file $_FILES array element
     * @param string $fieldName Field name for error reporting
     * @return array ['valid' => bool, 'errors' => array, 'sanitized_name' => string]
     */
    public static function validateFile($file, $fieldName = 'file') {
        self::init();
        
        $errors = [];
        $sanitizedName = '';
        
        // Basic upload error check
        if (!isset($file['error']) || is_array($file['error'])) {
            $errors[] = "Invalid file upload for $fieldName";
            return ['valid' => false, 'errors' => $errors, 'sanitized_name' => ''];
        }
        
        // Check upload errors
        switch ($file['error']) {
            case UPLOAD_ERR_OK:
                break;
            case UPLOAD_ERR_NO_FILE:
                $errors[] = "No file uploaded for $fieldName";
                return ['valid' => false, 'errors' => $errors, 'sanitized_name' => ''];
            case UPLOAD_ERR_INI_SIZE:
            case UPLOAD_ERR_FORM_SIZE:
                $errors[] = "File too large for $fieldName";
                return ['valid' => false, 'errors' => $errors, 'sanitized_name' => ''];
            default:
                $errors[] = "Upload error for $fieldName";
                return ['valid' => false, 'errors' => $errors, 'sanitized_name' => ''];
        }
        
        // File size validation
        if ($file['size'] > self::$maxFileSize) {
            // Align with requested UX copy exactly
            $errors[] = "File exceeds 50mb limit";
        }
        
        // Filename validation and sanitization
        $originalName = $file['name'];
        if (strlen($originalName) > self::$maxFilenameLength) {
            $errors[] = "Filename too long for $fieldName (max " . self::$maxFilenameLength . " characters)";
        }
        
        // Get file extension
        $extension = strtolower(pathinfo($originalName, PATHINFO_EXTENSION));
        if (!in_array($extension, self::$allowedExtensions)) {
            $allowedList = implode(', ', self::$allowedExtensions);
            $errors[] = "File type not allowed for $fieldName. Allowed types: $allowedList";
        }
        
        // MIME type validation (more secure than extension checking)
        // Prefer finfo for MIME detection; fall back to mime_content_type
        $detectedMimeType = 'application/octet-stream';
        if (function_exists('finfo_open')) {
            $fi = @finfo_open(FILEINFO_MIME_TYPE);
            if ($fi) {
                $dm = @finfo_file($fi, $file['tmp_name']);
                if (!empty($dm)) { $detectedMimeType = $dm; }
                @finfo_close($fi);
            }
        } elseif (function_exists('mime_content_type')) {
            $dm = @mime_content_type($file['tmp_name']);
            if (!empty($dm)) { $detectedMimeType = $dm; }
        }
        if (!isset(self::$allowedMimeTypes[$extension]) || 
            !in_array($detectedMimeType, self::$allowedMimeTypes[$extension])) {
            $allowByHeader = false;
            if ($extension === 'pdf') {
                $hdr = @file_get_contents($file['tmp_name'], false, null, 0, 4);
                if ($hdr === '%PDF') { $allowByHeader = true; }
            }
            if (!$allowByHeader) {
                $errors[] = "File content does not match extension for $fieldName";
            }
        }
        
        // File content validation
        $contentValidation = self::validateFileContent($file, $extension);
        if (!$contentValidation['valid']) {
            $errors = array_merge($errors, $contentValidation['errors']);
        }
        
        // Filename sanitization
        $sanitizedName = self::sanitizeFilename($originalName, $fieldName);
        
        // Malware/script detection (less aggressive for PDFs to reduce false positives)
        $malwareCheck = self::scanForMalware($file, $extension);
        if (!$malwareCheck['valid']) {
            $errors = array_merge($errors, $malwareCheck['errors']);
            // Move suspicious file to quarantine
            self::quarantineFile($file, $sanitizedName, 'Potential malware detected');
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors,
            'sanitized_name' => $sanitizedName,
            'original_name' => $originalName,
            'extension' => $extension,
            'mime_type' => $detectedMimeType,
            'size' => $file['size']
        ];
    }
    
    /**
     * Validate file content based on type
     */
    private static function validateFileContent($file, $extension) {
        $errors = [];
        
        try {
            $fileContent = file_get_contents($file['tmp_name']);
            
            switch ($extension) {
                case 'pdf':
                    // Check PDF header signature
                    if (substr($fileContent, 0, 4) !== '%PDF') {
                        $errors[] = 'Invalid PDF file format';
                    }
                    // Detect JavaScript and potential auto-execution contexts; only block if auto-exec or Launch is present
                    $hasJsAction = (
                        preg_match('/\/S\s*\/JavaScript\b/i', $fileContent) ||   // Action dictionary: /S /JavaScript
                        preg_match('/\/JS\s*(\(|<)/i', $fileContent)               // JS code provided as literal or hex string: /JS ( ... ) or /JS <...>
                    );
                    $hasAutoExec = preg_match('/\/(OpenAction|AA)\b/i', $fileContent) === 1; // Auto-executing contexts
                    $hasLaunch = preg_match('/\/Launch\b/i', $fileContent) === 1;            // Launch action is dangerous
                    if ($hasLaunch || ($hasJsAction && $hasAutoExec)) {
                        $errors[] = 'PDF contains auto-executing JavaScript or Launch actions';
                    }
                    // Heuristic: ensure EOF marker exists somewhere (not exhaustive)
                    if (strpos($fileContent, '%%EOF') === false) {
                        $errors[] = 'Malformed PDF: missing EOF marker';
                    }
                    // Reject extremely large object count hints to reduce DoS risk (heuristic)
                    if (preg_match('/\/Count\s+(\d{6,})/i', $fileContent, $m)) {
                        $errors[] = 'PDF appears to contain an unusually large object count';
                    }
                    break;
                default:
                    // Any extension not explicitly handled (but allowed) must still have content
                    if (empty($fileContent)) {
                        $errors[] = 'File appears to be empty or corrupted';
                    }
                    break;
            }
            
        } catch (Exception $e) {
            $errors[] = 'Error reading file content';
        }
        
        return ['valid' => empty($errors), 'errors' => $errors];
    }
    
    /**
     * Scan for potential malware signatures
     */
    private static function scanForMalware($file, $extension = null) {
        $errors = [];
        
        try {
            $fileContent = file_get_contents($file['tmp_name']);
            
            // Common malware/script signatures
            // Note: For PDFs we skip generic web/script pattern scanning to reduce false positives,
            // relying on PDF-specific checks above. We still check for executable headers below.
            if (strtolower((string)$extension) !== 'pdf') {
                $dangerousPatterns = [
                    '/<\?php/i',
                    '/<%/i',
                    '/<script/i',
                    '/javascript:/i',
                    '/vbscript:/i',
                    '/onload=/i',
                    '/onerror=/i',
                    '/eval\(/i',
                    '/system\(/i',
                    '/exec\(/i',
                    '/shell_exec/i',
                    '/base64_decode/i',
                    '/\x00/' // Null bytes
                ];

                foreach ($dangerousPatterns as $pattern) {
                    if (preg_match($pattern, $fileContent)) {
                        $errors[] = 'File contains potentially malicious content';
                        break;
                    }
                }
            }
            
            // Check for executable file signatures
            $executableHeaders = [
                'MZ',        // Windows executable
                "\x7fELF",   // Linux executable
                "\xCA\xFE\xBA\xBE", // Java class file
                "\xFE\xED\xFA",     // Mach-O executable
            ];
            
            foreach ($executableHeaders as $header) {
                if (substr($fileContent, 0, strlen($header)) === $header) {
                    $errors[] = 'File appears to be an executable';
                    break;
                }
            }
            
        } catch (Exception $e) {
            // If we can't read the file, treat it as suspicious
            $errors[] = 'Unable to scan file content';
        }
        
        return ['valid' => empty($errors), 'errors' => $errors];
    }
    
    /**
     * Sanitize filename to prevent directory traversal and other attacks
     */
    private static function sanitizeFilename($filename, $fieldName) {
        // Remove directory traversal attempts
        $filename = basename($filename);
        
        // Remove or replace dangerous characters
        $filename = preg_replace('/[^a-zA-Z0-9._-]/', '_', $filename);
        
        // Prevent multiple dots (potential hiding extensions)
        $filename = preg_replace('/\.+/', '.', $filename);
        
        // Ensure filename doesn't start with dot
        $filename = ltrim($filename, '.');
        
        // Get extension
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        // Create safe filename with timestamp and random component
        $safeName = $fieldName . '_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4));
        if ($extension) {
            $safeName .= '.' . $extension;
        }
        
        return $safeName;
    }
    
    /**
     * Move suspicious file to quarantine
     */
    private static function quarantineFile($file, $filename, $reason) {
        $quarantinePath = self::$quarantineDir . '/' . 'quarantine_' . date('Ymd_His') . '_' . $filename;
        if (move_uploaded_file($file['tmp_name'], $quarantinePath)) {
            $logEntry = date('c') . " | QUARANTINE | File: $filename | Reason: $reason" . PHP_EOL;
            $logFile = storage_path('logs/quarantine.log');
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
            file_put_contents($logFile, $logEntry, FILE_APPEND);
        }
    }
    
    /**
     * Secure file move with additional checks
     */
    public static function secureMove($file, $destination, $validation = null) {
        if (!$validation) {
            $validation = self::validateFile($file);
        }
        
        if (!$validation['valid']) {
            return ['success' => false, 'errors' => $validation['errors']];
        }
        
        // Ensure destination directory exists and is secure
        $destDir = dirname($destination);
        if (!is_dir($destDir)) {
            if (!@mkdir($destDir, 0755, true)) {
                return ['success' => false, 'errors' => ['Cannot create destination directory']];
            }
        }
        
        // Move file securely
        if (move_uploaded_file($file['tmp_name'], $destination)) {
            // Set secure permissions
            chmod($destination, 0644);
            
            // Log successful upload
            $logEntry = date('c') . " | UPLOAD_SUCCESS | File: " . basename($destination) . 
                       " | Size: " . $validation['size'] . " | Type: " . $validation['mime_type'] . PHP_EOL;
            $logFile = storage_path('logs/upload.log');
            $logDir = dirname($logFile);
            if (!is_dir($logDir)) { @mkdir($logDir, 0775, true); }
            file_put_contents($logFile, $logEntry, FILE_APPEND);
            
            return ['success' => true, 'filename' => basename($destination)];
        } else {
            return ['success' => false, 'errors' => ['Failed to move uploaded file']];
        }
    }
    
    /**
     * Get upload configuration for display
     */
    public static function getUploadLimits() {
        self::init();
        return [
            'max_size_mb' => round(self::$maxFileSize / 1048576, 1),
            'allowed_extensions' => self::$allowedExtensions,
            'max_filename_length' => self::$maxFilenameLength
        ];
    }
}

// Auto-initialize when file is included
UploadValidator::init();
?>