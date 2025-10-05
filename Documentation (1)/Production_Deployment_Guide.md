# Production Deployment Guide - Environment Variables

## Overview
This guide explains how to deploy the IPMO system securely using environment variables to protect sensitive configuration data.

## Step 1: Create Production Environment File

1. Copy the template:
   ```bash
   cp .env.example .env
   ```

2. Edit `.env` with your production values:
   ```bash
   nano .env
   ```

## Step 2: Required Environment Variables

### Database Configuration
```
DB_HOST=your_production_database_host
DB_NAME=ipmo_users
DB_USERNAME=your_database_user
DB_PASSWORD=your_secure_database_password
DB_PORT=3306
```

### Email Configuration (SMTP)
```
SMTP_HOST=smtp.gmail.com
SMTP_PORT=587
SMTP_USERNAME=your_production_email@domain.com
SMTP_PASSWORD=your_app_password
SMTP_FROM_EMAIL=noreply@yourdomain.com
SMTP_FROM_NAME="Your Organization IPMO"
```

### Security Configuration
```
ENVIRONMENT=production
SESSION_LIFETIME=28800
CSRF_SECRET=your_32_character_random_string
SESSION_ENCRYPT_KEY=another_32_character_random_string
TICKET_SIGNING_KEY=yet_another_secure_random_string
```

### Application Configuration
```
APP_NAME="Your Organization IPMO"
APP_URL=https://yourdomain.com
ADMIN_EMAIL=admin@yourdomain.com
DEBUG_MODE=false
ERROR_REPORTING=false
```

## Step 3: Generate Security Keys

Use these commands to generate secure random keys:

```bash
# CSRF Secret (32 characters)
openssl rand -hex 32

# Session Encrypt Key (32 characters)
openssl rand -hex 32

# Ticket Signing Key (64 characters)
openssl rand -hex 64
```

## Step 4: Server Environment Variables (Alternative)

Instead of using .env file, you can set server environment variables:

### Apache (.htaccess)
```apache
SetEnv DB_HOST "your_database_host"
SetEnv DB_PASSWORD "your_secure_password"
SetEnv SMTP_PASSWORD "your_email_password"
```

### Nginx (via PHP-FPM)
```nginx
location ~ \.php$ {
    fastcgi_param DB_HOST "your_database_host";
    fastcgi_param DB_PASSWORD "your_secure_password";
    fastcgi_param SMTP_PASSWORD "your_email_password";
}
```

### System Environment Variables (Linux)
```bash
export DB_HOST="your_database_host"
export DB_PASSWORD="your_secure_password"
export SMTP_PASSWORD="your_email_password"
```

## Step 5: File Permissions

Set secure file permissions:

```bash
# Make .env file readable only by web server
chmod 600 .env
chown www-data:www-data .env

# Protect configuration files
chmod 644 env_config.php
chmod 644 email_config.php
chmod 644 conn.php
```

## Step 6: Verification

1. Test configuration:
   ```
   Visit: https://yourdomain.com/test_environment.php
   ```

2. Verify database connection:
   ```
   Check that all database operations work correctly
   ```

3. Test email functionality:
   ```
   Try password reset or registration email
   ```

4. Remove test files:
   ```bash
   rm test_environment.php
   rm test_session_security.php
   rm security_test.php
   ```

## Step 7: Security Checklist

- [ ] `.env` file is not accessible via web browser
- [ ] Database credentials are not hardcoded in files
- [ ] SMTP credentials are not hardcoded in files
- [ ] All security keys are randomly generated
- [ ] `ENVIRONMENT=production` is set
- [ ] `DEBUG_MODE=false` is set
- [ ] Test files are removed from production
- [ ] File permissions are properly configured

## Troubleshooting

### Environment Variables Not Loading
1. Check .env file syntax (no spaces around =)
2. Verify file permissions
3. Check server logs for PHP errors

### Database Connection Issues
1. Verify DB_HOST, DB_USERNAME, DB_PASSWORD
2. Check database server accessibility
3. Confirm database exists and user has privileges

### Email Not Working
1. Verify SMTP credentials
2. Check if Gmail App Password is used (not regular password)
3. Test SMTP connection separately

## Backup Strategy

1. Backup .env file securely (encrypted)
2. Store security keys in secure password manager
3. Document all environment variable values
4. Test restore procedures regularly

## Development vs Production

| Setting | Development | Production |
|---------|------------|------------|
| ENVIRONMENT | development | production |
| DEBUG_MODE | true | false |
| ERROR_REPORTING | true | false |
| HTTPS | optional | required |
| .env location | project root | secure location |

## Support

For production deployment assistance:
- Review security_audit.php output
- Check audit.log for security events
- Monitor error logs regularly
- Update security keys quarterly