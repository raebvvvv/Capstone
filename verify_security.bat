@echo off
echo ============================================
echo    IPMO Security Updates Verification
echo ============================================
echo.

echo Checking security files...
echo.

REM Check environment configuration
if exist "env_config.php" (
    echo [✓] Environment configuration: Found
) else (
    echo [X] Environment configuration: Missing
)

if exist ".env.example" (
    echo [✓] Environment template: Found
) else (
    echo [X] Environment template: Missing
)

REM Check session security
if exist "secure_session_config.php" (
    echo [✓] Session security config: Found
) else (
    echo [X] Session security config: Missing
)

REM Check upload security
if exist "upload_validator.php" (
    echo [✓] Upload validator: Found
) else (
    echo [X] Upload validator: Missing
)

if exist "upload_helpers.php" (
    echo [✓] Upload helpers: Found
) else (
    echo [X] Upload helpers: Missing
)

if exist "uploads\.htaccess" (
    echo [✓] Upload protection: Found
) else (
    echo [X] Upload protection: Missing
)

REM Check security headers
if exist "security_headers.php" (
    echo [✓] Security headers: Found
) else (
    echo [X] Security headers: Missing
)

if exist "csp_report.php" (
    echo [✓] CSP reporting: Found
) else (
    echo [X] CSP reporting: Missing
)

REM Check updated configurations
if exist "conn.php" (
    echo [✓] Database config: Found
) else (
    echo [X] Database config: Missing
)

if exist "email_config.php" (
    echo [✓] Email config: Found
) else (
    echo [X] Email config: Missing
)

REM Check documentation
if exist "PRODUCTION_DEPLOYMENT.md" (
    echo [✓] Production guide: Found
) else (
    echo [X] Production guide: Missing
)

if exist "SECURITY_HEADERS_GUIDE.md" (
    echo [✓] Security headers guide: Found
) else (
    echo [X] Security headers guide: Missing
)

REM Check main verification script
if exist "verify_security_updates.php" (
    echo [✓] Main verification script: Found
) else (
    echo [X] Main verification script: Missing
)

echo.
echo ============================================
echo File permissions check (manual verification needed):
echo.
echo Run these commands to check file security:
echo   attrib conn.php
echo   attrib email_config.php  
echo   attrib config.php
echo.
echo Files should show 'R' (read-only) attribute
echo ============================================
echo.

echo Testing web access...
echo.
echo Open these URLs in your browser to test:
echo   http://localhost/Capstone/verify_security_updates.php
echo   http://localhost/Capstone/test_environment.php
echo   http://localhost/Capstone/test_security_headers.php
echo   http://localhost/Capstone/test_upload_validation.php
echo.

echo ============================================
echo Security verification complete!
echo Check the web interface for detailed results.
echo ============================================
pause