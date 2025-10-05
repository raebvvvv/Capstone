<?php
// Enhanced File Upload Validation
// Comprehensive security validation for file uploads

require_once __DIR__ . '/env_config.php';

class UploadValidator {
    
    // Configuration from environment variables
    private static $maxFileSize;
    private static $allowedExtensions;
    private static $allowedMimeTypes;
    private static $maxFilenameLength = 100;
    private static $quarantineDir;
    
    public static function init() {
        self::$maxFileSize = Environment::getInt('MAX_UPLOAD_SIZE', 10485760); // 10MB default
        self::$allowedExtensions = explode(',', Environment::get('ALLOWED_EXTENSIONS', 'pdf,doc,docx,jpg,jpeg,png'));
        self::$allowedMimeTypes = [
            'pdf' => ['application/pdf'],
            'doc' => ['application/msword'],
            'docx' => ['application/vnd.openxmlformats-officedocument.wordprocessingml.document'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png']
        ];
        self::$quarantineDir = __DIR__ . '/quarantine';
        
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
            $maxSizeMB = round(self::$maxFileSize / 1048576, 1);
            $errors[] = "File $fieldName exceeds maximum size of {$maxSizeMB}MB";
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
        $detectedMimeType = mime_content_type($file['tmp_name']);
        if (!isset(self::$allowedMimeTypes[$extension]) || 
            !in_array($detectedMimeType, self::$allowedMimeTypes[$extension])) {
            $errors[] = "File content does not match extension for $fieldName";
        }
        
        // File content validation
        $contentValidation = self::validateFileContent($file, $extension);
        if (!$contentValidation['valid']) {
            $errors = array_merge($errors, $contentValidation['errors']);
        }
        
        // Filename sanitization
        $sanitizedName = self::sanitizeFilename($originalName, $fieldName);
        
        // Malware/script detection
        $malwareCheck = self::scanForMalware($file);
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
                    // Check PDF header
                    if (substr($fileContent, 0, 4) !== '%PDF') {
                        $errors[] = 'Invalid PDF file format';
                    }
                    // Check for embedded scripts (basic detection)
                    if (preg_match('/\/JavaScript|\/JS|\/S#|\/Launch/i', $fileContent)) {
                        $errors[] = 'PDF contains potentially dangerous JavaScript';
                    }
                    break;
                    
                case 'jpg':
                case 'jpeg':
                    // Check JPEG header
                    if (substr($fileContent, 0, 2) !== "\xFF\xD8") {
                        $errors[] = 'Invalid JPEG file format';
                    }
                    break;
                    
                case 'png':
                    // Check PNG header
                    if (substr($fileContent, 0, 8) !== "\x89PNG\r\n\x1a\n") {
                        $errors[] = 'Invalid PNG file format';
                    }
                    break;
                    
                case 'doc':
                case 'docx':
                    // Basic validation - check if file can be read
                    if (empty($fileContent)) {
                        $errors[] = 'Document file appears to be empty or corrupted';
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
    private static function scanForMalware($file) {
        $errors = [];
        
        try {
            $fileContent = file_get_contents($file['tmp_name']);
            
            // Common malware/script signatures
            $dangerousPatterns = [
                '/<?php/i',
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
            file_put_contents(__DIR__ . '/quarantine.log', $logEntry, FILE_APPEND);
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
            file_put_contents(__DIR__ . '/upload.log', $logEntry, FILE_APPEND);
            
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