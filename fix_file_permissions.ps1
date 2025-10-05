# Security File Permissions Fix Script
# Run this as Administrator to secure sensitive configuration files

Write-Host "🔒 Fixing Configuration File Permissions..." -ForegroundColor Yellow

$capstoneDir = "c:\xampp\htdocs\Capstone"
$sensitiveFiles = @("config.php", "email_config.php", "conn.php")

foreach ($file in $sensitiveFiles) {
    $filePath = Join-Path $capstoneDir $file
    
    if (Test-Path $filePath) {
        Write-Host "Securing $file..." -ForegroundColor Cyan
        
        try {
            # Remove inheritance and preserve existing permissions
            $acl = Get-Acl $filePath
            $acl.SetAccessRuleProtection($true, $true)
            
            # Remove Everyone and Users groups for security
            $acl.Access | Where-Object { 
                $_.IdentityReference -like "*Everyone*" -or 
                $_.IdentityReference -like "*Users*" 
            } | ForEach-Object { 
                $acl.RemoveAccessRule($_) 
            }
            
            # Add specific permissions for necessary accounts only
            # IUSR (for IIS) - Read only
            $readRule = New-Object System.Security.AccessControl.FileSystemAccessRule("IUSR", "Read", "Allow")
            $acl.SetAccessRule($readRule)
            
            # Current user - Full control
            $currentUser = [System.Security.Principal.WindowsIdentity]::GetCurrent().Name
            $fullControlRule = New-Object System.Security.AccessControl.FileSystemAccessRule($currentUser, "FullControl", "Allow")
            $acl.SetAccessRule($fullControlRule)
            
            # Apply the changes
            Set-Acl -Path $filePath -AclObject $acl
            
            Write-Host "✅ $file permissions secured" -ForegroundColor Green
        }
        catch {
            Write-Host "❌ Failed to secure $file : $($_.Exception.Message)" -ForegroundColor Red
        }
    }
    else {
        Write-Host "⚠️ $file not found" -ForegroundColor Yellow
    }
}

Write-Host "`n🔍 Checking final permissions..." -ForegroundColor Yellow
foreach ($file in $sensitiveFiles) {
    $filePath = Join-Path $capstoneDir $file
    if (Test-Path $filePath) {
        $acl = Get-Acl $filePath
        Write-Host "`n📁 $file permissions:" -ForegroundColor Cyan
        $acl.Access | Select-Object IdentityReference, FileSystemRights, AccessControlType | Format-Table
    }
}

Write-Host "🔒 File permissions security completed!" -ForegroundColor Green
Write-Host "ℹ️ Note: Run this script as Administrator for best results" -ForegroundColor Blue