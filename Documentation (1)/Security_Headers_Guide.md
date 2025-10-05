# Production Security Headers Guide

## Overview
This guide provides comprehensive security headers configuration for production deployment of the IPMO system.

## Security Headers Implementation

### 1. Automatic Header Configuration

The system automatically applies security headers based on environment:

**Development Environment:**
- Permissive CSP (report-only mode)
- Shorter HSTS max-age
- Development indicators
- More permissive caching

**Production Environment:**
- Strict CSP enforcement
- Long HSTS max-age with preload
- Cross-origin policies
- Host header validation
- Comprehensive caching controls

### 2. Environment Variables

Add these to your `.env` file:

```bash
# Security Configuration
ENVIRONMENT=production
APP_URL=https://yourdomain.com
CSP_REPORT_URI=/csp_report.php

# HTTPS Configuration (production only)
FORCE_HTTPS=true
HSTS_MAX_AGE=31536000
```

### 3. Web Server Configuration

#### Apache (.htaccess)

Create or update `.htaccess` in your web root:

```apache
# Force HTTPS in production
RewriteEngine On
RewriteCond %{HTTPS} off
RewriteRule ^(.*)$ https://%{HTTP_HOST}%{REQUEST_URI} [L,R=301]

# Additional security headers (backup to PHP headers)
<IfModule mod_headers.c>
    # Prevent MIME sniffing
    Header always set X-Content-Type-Options nosniff
    
    # Prevent clickjacking
    Header always set X-Frame-Options DENY
    
    # XSS Protection
    Header always set X-XSS-Protection "1; mode=block"
    
    # Hide server information
    Header always unset Server
    Header always unset X-Powered-By
    
    # HTTPS enforcement (if not handled by PHP)
    Header always set Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" env=HTTPS
</IfModule>

# Hide sensitive files
<Files ".env">
    Order Allow,Deny
    Deny from all
</Files>

<Files "*.log">
    Order Allow,Deny
    Deny from all
</Files>

<Files "config.php">
    Order Allow,Deny
    Deny from all
</Files>
```

#### Nginx

Add to your Nginx server block:

```nginx
server {
    listen 443 ssl http2;
    server_name yourdomain.com;
    
    # SSL Configuration
    ssl_certificate /path/to/your/certificate.crt;
    ssl_certificate_key /path/to/your/private.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    
    # Security headers (backup to PHP headers)
    add_header X-Frame-Options DENY always;
    add_header X-Content-Type-Options nosniff always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains; preload" always;
    
    # Hide server information
    server_tokens off;
    
    # PHP configuration
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.1-fpm.sock;
        fastcgi_param HTTPS on;
        fastcgi_param SERVER_PORT 443;
        include fastcgi_params;
    }
    
    # Protect sensitive files
    location ~ /\.(env|log)$ {
        deny all;
        return 404;
    }
    
    location ~ /(config|conn|email_config)\.php$ {
        deny all;
        return 404;
    }
}

# HTTP to HTTPS redirect
server {
    listen 80;
    server_name yourdomain.com;
    return 301 https://$server_name$request_uri;
}
```

### 4. Content Security Policy (CSP)

The system implements a comprehensive CSP that:

- Blocks inline scripts in production (use nonces)
- Restricts resource loading to trusted domains
- Prevents XSS attacks
- Reports violations to `/csp_report.php`

#### CSP Nonce Usage

For inline scripts that must be used:

```php
<script nonce="<?php echo SecurityHeaders::getCSPNonce(); ?>">
    // Your inline JavaScript here
    console.log('This script is CSP-compliant');
</script>
```

### 5. HTTPS Strict Transport Security (HSTS)

Automatically configured based on environment:

**Development:**
- `max-age=86400` (1 day)
- `includeSubDomains`

**Production:**
- `max-age=31536000` (1 year)
- `includeSubDomains`
- `preload` (for HSTS preload list)

### 6. CSP Violation Monitoring

The system includes automatic CSP violation reporting:

- Reports sent to `/csp_report.php`
- Rate limiting to prevent spam
- High-severity violations logged to audit log
- Violations stored in `csp_violations.log`

#### Monitoring CSP Violations

```bash
# View recent CSP violations
tail -f csp_violations.log

# Count violations by directive
grep '"violated_directive"' csp_violations.log | sort | uniq -c

# Find high-severity violations
grep 'CSP_VIOLATION_HIGH' audit.log
```

### 7. Security Headers Validation

Test your security headers:

1. **Local Testing:**
   ```
   Visit: https://yourdomain.com/test_security_headers.php
   ```

2. **Online Tools:**
   - [Security Headers](https://securityheaders.com/)
   - [Mozilla Observatory](https://observatory.mozilla.org/)
   - [SSL Labs](https://www.ssllabs.com/ssltest/)

3. **Command Line Testing:**
   ```bash
   curl -I https://yourdomain.com
   ```

### 8. Production Checklist

Before deploying to production:

- [ ] `ENVIRONMENT=production` in `.env`
- [ ] HTTPS certificate installed and configured
- [ ] Web server security headers configured
- [ ] CSP violations monitoring setup
- [ ] Security headers testing completed
- [ ] All test files removed from production
- [ ] Sensitive files protected from web access
- [ ] HSTS preload submitted (optional)

### 9. Maintenance

#### Regular Tasks

1. **Monitor CSP violations** (weekly)
2. **Review security logs** (weekly)
3. **Update security headers** (quarterly)
4. **Test header configuration** (monthly)
5. **Review and update CSP** (quarterly)

#### Log Rotation

Set up log rotation for security logs:

```bash
# Add to /etc/logrotate.d/ipmo-security
/path/to/capstone/*.log {
    weekly
    rotate 52
    compress
    delaycompress
    missingok
    notifempty
    create 644 www-data www-data
}
```

### 10. Troubleshooting

#### Common Issues

**CSP Violations:**
- Check `csp_violations.log` for details
- Use browser developer tools to see blocked resources
- Add necessary sources to CSP whitelist

**HSTS Issues:**
- Clear browser HSTS cache: `chrome://net-internals/#hsts`
- Ensure HTTPS is properly configured
- Check certificate validity

**Performance Impact:**
- Monitor page load times after enabling headers
- Adjust cache policies if needed
- Consider CDN for static resources

#### Header Conflicts

If you see duplicate headers:
1. Check web server configuration
2. Verify PHP header configuration
3. Remove duplicate header sources

### 11. Security Contacts

For security-related issues:
- Review `audit.log` for security events
- Monitor `csp_violations.log` for CSP issues
- Check `upload_errors.log` for upload security issues
- Contact system administrator for security incidents

## Additional Resources

- [OWASP Secure Headers Project](https://owasp.org/www-project-secure-headers/)
- [Mozilla Web Security Guidelines](https://infosec.mozilla.org/guidelines/web_security)
- [Content Security Policy Reference](https://content-security-policy.com/)
- [HTTP Strict Transport Security](https://developer.mozilla.org/en-US/docs/Web/HTTP/Headers/Strict-Transport-Security)