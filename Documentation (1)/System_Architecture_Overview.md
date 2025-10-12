# 🏗️ IPMO System Architecture Overview

## **SYSTEM OVERVIEW**

The **Intellectual Property Management Office (IPMO) System** is a comprehensive web-based application designed to manage intellectual property applications, user registrations, and administrative functions for educational institutions.

---

## 📊 **SYSTEM ARCHITECTURE**

### **Architecture Pattern: MVC-Style with Security Layers**

```
┌─────────────────────────────────────────────────────────────────┐
│                    PRESENTATION LAYER                           │
├─────────────────────────────────────────────────────────────────┤
│  Bootstrap 5 Frontend  │  Responsive UI  │  JavaScript/jQuery  │
├─────────────────────────────────────────────────────────────────┤
│                     SECURITY LAYER                              │
├─────────────────────────────────────────────────────────────────┤
│ Session Security │ Upload Validation │ Security Headers │ CSRF │
├─────────────────────────────────────────────────────────────────┤
│                    APPLICATION LAYER                            │
├─────────────────────────────────────────────────────────────────┤
│    Auth System   │   File Management  │   Admin Functions    │
├─────────────────────────────────────────────────────────────────┤
│                      DATA LAYER                                 │
├─────────────────────────────────────────────────────────────────┤
│  Authentication  │  Student Profiles  │  Employee Profiles   │
│     (users)      │ (student_profiles) │ (employee_profiles)  │
└─────────────────────────────────────────────────────────────────┘
```

---

## 🗂️ **FILE STRUCTURE**

### **Root Directory Structure**
```
Capstone/
├── 📁 admin/                    # Admin interface files
├── 📁 css/                      # Stylesheets
├── 📁 javascript/               # Client-side scripts
├── 📁 includes/                 # Shared PHP includes
├── 📁 partials/                 # Reusable UI components
├── 📁 User/                     # User interface files
├── 📁 uploads/                  # File upload storage (secured)
├── 📁 Documentation (1)/        # System documentation
├── 📁 PHPMailer/               # Email functionality
└── 📄 Core system files        # Main application files
```

### **Core System Files**
```
🔧 Configuration Files:
├── config.php                  # Main configuration
├── conn.php                    # Database connection
├── env_config.php              # Environment variables
├── email_config.php            # Email settings
└── .env.example                # Environment template

🔐 Security Files:
├── security_bootstrap.php      # Security initialization
├── security_headers.php        # Security headers management
├── secure_session_config.php   # Session security
├── upload_validator.php        # File upload validation
├── includes/upload_helpers.php # Upload utility functions (non-public)
└── includes/profile_helpers.php# Profile security helpers (non-public)

👤 Authentication & User Management:
├── register.php                # User registration
├── login.php                   # User authentication
├── logout.php                  # Session termination
├── auth_check.php              # Authentication verification
└── update_profile.php          # Profile management

🗄️ Database & Data:
├── ipmo_users.sql              # Database schema
└── database.txt                # Database information

📧 Email & Communication:
├── csp_report.php              # Security violation reporting
└── PHPMailer/                  # Email library
```

---

## 🗄️ **DATABASE ARCHITECTURE**

### **Database Design: Separated Authentication Model**

#### **Core Tables Structure**
```sql
┌─────────────────┐    ┌─────────────────────┐
│     users       │    │   student_profiles  │
├─────────────────┤    ├─────────────────────┤
│ id (PK)         │◄───┤ user_id (FK)        │
│ email           │    │ student_number      │
│ password        │    │ first_name          │
│ role            │    │ last_name           │
│ status          │    │ college             │
│ created_at      │    │ cor_file_path       │
│ updated_at      │    │ created_at          │
└─────────────────┘    └─────────────────────┘
         │
         ├──────────────┐
         │              ▼
         │    ┌─────────────────────┐
         │    │  employee_profiles  │
         │    ├─────────────────────┤
         │    │ user_id (FK)        │
         │    │ employee_id         │
         │    │ first_name          │
         │    │ last_name           │
         │    │ department          │
         │    │ position            │
         │    │ created_at          │
         │    └─────────────────────┘
         │
         └──────────────┐
                        ▼
              ┌─────────────────────┐
              │   admin_profiles    │
              ├─────────────────────┤
              │ user_id (FK)        │
              │ admin_name          │
              │ permissions         │
              │ created_at          │
              └─────────────────────┘
```

#### **Key Database Features**
- **Separated Authentication:** Core authentication in `users` table
- **Role-Based Profiles:** Separate tables for different user types
- **Referential Integrity:** Foreign key relationships maintained
- **Optimized Queries:** Indexed fields for fast searches
- **Audit Trail:** Created/updated timestamps on all tables

---

## 🔐 **SECURITY ARCHITECTURE**

### **Multi-Layer Security Implementation**

#### **1. Environment Security**
```php
Environment Variables System:
├── Database credentials → Environment::get('DB_PASSWORD')
├── Email credentials    → Environment::get('SMTP_PASSWORD') 
├── Security keys       → Environment::get('CSRF_SECRET')
└── Application config  → Environment::get('APP_URL')
```

#### **2. Session Security**
```php
Session Protection:
├── HTTPS detection and enforcement
├── Secure cookie attributes (HttpOnly, Secure, SameSite)
├── Session regeneration on login
├── Idle timeout and absolute timeout
└── CSRF token protection
```

#### **3. Upload Security**
```php
File Upload Validation:
├── File type verification (MIME + extension)
├── Malware detection patterns
├── File size limits
├── Filename sanitization
├── Content scanning
└── Quarantine system for suspicious files
```

#### **4. Security Headers**
```php
HTTP Security Headers:
├── Content-Security-Policy (CSP)
├── HTTP Strict Transport Security (HSTS)
├── X-Frame-Options: DENY
├── X-Content-Type-Options: nosniff
├── X-XSS-Protection: 1; mode=block
└── Referrer-Policy controls
```

---

## 🔄 **APPLICATION WORKFLOW**

### **User Registration Flow**
```
1. User accesses registration form
2. Security headers applied
3. Form validation (client + server)
4. File upload validation
5. Database transaction:
   ├── Insert into users table
   └── Insert into profile table
6. Email confirmation (if configured)
7. Success/error response
```

### **Admin Management Flow**
```
1. Admin authentication
2. Session security verification
3. Access control check
4. Search/filter operations
5. Profile data retrieval
6. Action execution (approve/reject/edit)
7. Audit logging
8. Response generation
```

### **File Upload Security Flow**
```
1. Pre-upload safety check
2. File type validation
3. MIME type verification
4. Content scanning
5. Malware detection
6. Filename sanitization
7. Secure file storage
8. Database path recording
9. Success confirmation
```

---

## 🛠️ **TECHNOLOGY STACK**

### **Backend Technologies**
- **Language:** PHP 7.4+
- **Database:** MySQL/MariaDB
- **Email:** PHPMailer library
- **Session:** Native PHP sessions with security enhancements
- **File Handling:** Native PHP with security validation

### **Frontend Technologies**
- **Framework:** Bootstrap 5.3+
- **JavaScript:** jQuery + Vanilla JS
- **CSS:** Custom CSS with Bootstrap
- **Icons:** Bootstrap Icons
- **Fonts:** Google Fonts integration

### **Security Technologies**
- **CSP:** Content Security Policy implementation
- **HSTS:** HTTP Strict Transport Security
- **Environment Variables:** Secure configuration management
- **File Validation:** Multi-layer upload security
- **Session Protection:** Advanced session security

### **Development Tools**
- **Environment:** XAMPP (development)
- **Version Control:** Git
- **Documentation:** Markdown
- **Testing:** Custom PHP validation scripts

---

## 📈 **PERFORMANCE CONSIDERATIONS**

### **Database Optimization**
- **Indexed Fields:** Email, student_number, employee_id
- **Query Optimization:** Prepared statements, parameter binding
- **Connection Management:** PDO with connection reuse
- **Search Efficiency:** Optimized JOIN queries across profile tables

### **File Handling**
- **Upload Limits:** Configurable via environment variables
- **Storage Optimization:** Organized directory structure
- **Security Scanning:** Efficient pattern matching
- **Error Handling:** Graceful failure management

### **Session Management**
- **Memory Efficiency:** Minimal session data storage
- **Timeout Management:** Configurable idle and absolute timeouts
- **Security Overhead:** Balanced security vs. performance

---

## 🔧 **CONFIGURATION MANAGEMENT**

### **Environment-Based Configuration**
```php
Development Environment:
├── Debug mode enabled
├── Detailed error reporting
├── Permissive CSP (report-only)
└── Local database connections

Production Environment:
├── Debug mode disabled
├── Error logging only
├── Strict CSP enforcement
└── Secure database connections
```

### **Configuration Files Hierarchy**
```
1. .env file (highest priority)
2. Environment variables
3. Default values in code
4. Fallback configurations
```

---

## 📊 **MONITORING & LOGGING**

### **Security Monitoring**
- **CSP Violations:** Logged to `csp_violations.log`
- **Upload Failures:** Logged to `upload_errors.log`
- **Security Events:** Logged to `audit.log`
- **Session Security:** Status monitoring available

### **Application Monitoring**
- **User Activities:** Registration, login, profile updates
- **Admin Actions:** User management, status changes
- **System Events:** Configuration changes, errors

### **Performance Monitoring**
- **Database Queries:** Execution time tracking
- **File Operations:** Upload/download monitoring
- **Memory Usage:** Session and file handling efficiency

---

## 🔄 **DEPLOYMENT ARCHITECTURE**

### **Development → Production Pipeline**
```
1. Development Environment (localhost)
   ├── Full debugging enabled
   ├── Test data and users
   └── All test files available

2. Staging Environment (optional)
   ├── Production-like configuration
   ├── Real data testing
   └── Security validation

3. Production Environment
   ├── Security hardened
   ├── Performance optimized
   └── Monitoring enabled
```

### **Security Deployment Checklist**
- ✅ Environment variables configured
- ✅ HTTPS certificate installed
- ✅ Security headers active
- ✅ File permissions secured
- ✅ Test files removed
- ✅ Monitoring systems active

---

## 📞 **SYSTEM MAINTENANCE**

### **Regular Maintenance Tasks**
- **Weekly:** Security log review, backup verification
- **Monthly:** Performance analysis, security updates
- **Quarterly:** Security audit, configuration review
- **Annually:** Full system review, architecture updates

### **Emergency Procedures**
- **Security Incident:** Isolation, analysis, remediation
- **Performance Issues:** Monitoring, optimization, scaling
- **Data Recovery:** Backup restoration, data verification

---

**🔗 Related Documentation:**
- [Security Implementation Guide](Security_Implementation_Guide.md)
- [Database Schema Documentation](Database_Schema_Documentation.md)  
- [Production Deployment Guide](Production_Deployment_Guide.md)
- [Development Setup Guide](Development_Setup_Guide.md)