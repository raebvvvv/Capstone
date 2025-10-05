# 👥 Admin User Manual

## **ADMINISTRATOR INTERFACE GUIDE**

This manual provides comprehensive guidance for administrators managing the IPMO System, including user management, application processing, and system administration.

---

## 🚀 **GETTING STARTED**

### **Accessing the Admin Interface**
1. Navigate to: `https://yourdomain.com/admin/login.php`
2. Enter your administrator credentials
3. The system will redirect you to the admin dashboard

### **Admin Dashboard Overview**
- **User Management:** Manage student and employee accounts
- **Application Processing:** Review and process IP applications
- **Search & Filter:** Find users and applications quickly
- **System Monitoring:** View system status and security

---

## 👤 **USER MANAGEMENT**

### **Managing Users - Main Interface**
**Access:** Admin Dashboard → Manage Users

#### **User Search and Filtering**
The system provides powerful search capabilities across all user types:

```
Search Options:
├── By Name (First/Last)
├── By Student Number  
├── By Employee ID
├── By Email Address
├── By Role (Student/Employee/Admin)
└── By Status (Pending/Approved/Rejected)
```

#### **Search Interface Usage:**
1. **Quick Search:** Enter any name, number, or email in the search box
2. **Advanced Filters:** Use the filter dropdowns to refine results
3. **Real-time Results:** Search updates automatically as you type
4. **Export Results:** Download search results as CSV

#### **User List Display:**
- **User ID:** Unique system identifier
- **Name:** First and Last name
- **Email:** User's email address
- **Role:** Student, Employee, or Admin
- **Status:** Account status (Pending, Approved, Rejected)
- **Date Registered:** When the account was created
- **Actions:** Available operations for each user

---

### **User Status Management**

#### **Approving Users:**
1. Search for the user in the user management interface
2. Click the **"Approve"** button next to the user
3. Confirm the action in the dialog box
4. The user status changes to "Approved"
5. User receives email notification (if configured)

#### **Rejecting Users:**
1. Locate the user in the search results
2. Click the **"Reject"** button
3. Provide a reason for rejection (optional)
4. Confirm the action
5. User status changes to "Rejected"

#### **Suspending Users:**
1. Find the approved user
2. Click **"Suspend"** from the actions menu
3. Enter suspension reason
4. Confirm suspension
5. User loses access immediately

#### **Bulk Actions:**
- **Select Multiple Users:** Use checkboxes to select users
- **Bulk Approve:** Approve multiple pending users at once
- **Bulk Export:** Export selected user data
- **Bulk Status Change:** Change status of multiple users

---

## 📄 **APPLICATION MANAGEMENT**

### **Processing IP Applications**

#### **Application Dashboard:**
**Access:** Admin Dashboard → Applications

#### **Application Status Types:**
- **Pending:** New applications awaiting review
- **Under Review:** Currently being processed
- **Approved:** Applications accepted
- **Rejected:** Applications denied
- **Completed:** Fully processed applications

#### **Reviewing Applications:**
1. **Access Application:** Click on application ID or user name
2. **Review Documents:** Check all uploaded files
3. **Verify Information:** Confirm user details and application data
4. **Document Review:**
   - Journal Publication Format
   - Notarized Copyright Documents
   - Receipt of Payment
   - Full Manuscript (Students) / Presentation (Employees)
   - Approval Sheet (Students only)
   - Notarized Co-authorship Agreement
   - Record of Copyright

#### **Document Verification Process:**
1. **Download Document:** Click the document link to download
2. **Verify Authenticity:** Check document validity
3. **Check Requirements:** Ensure all required documents are present
4. **Quality Review:** Verify document quality and completeness

#### **Application Actions:**
- **Approve Application:** Mark as approved and notify user
- **Request Corrections:** Ask user to resubmit with corrections
- **Reject Application:** Deny with reason
- **Add Notes:** Internal notes for tracking
- **Set Priority:** Mark urgent applications
- **Assign Reviewer:** Delegate to another admin

---

## 🔍 **SEARCH FUNCTIONALITY**

### **Advanced Search Features**

#### **Multi-Field Search:**
The system searches across multiple fields simultaneously:
- User names (first and last)
- Student numbers
- Employee IDs  
- Email addresses
- Department/College information

#### **Search Syntax:**
```
Examples:
"John Doe"          → Search for specific name
"12345"             → Search student/employee number
"@gmail.com"        → Search by email domain
"Engineering"       → Search by department/college
"status:pending"    → Search by status
```

#### **Filter Combinations:**
- **Role + Status:** Show only pending students
- **Date Range:** Applications from specific time period
- **Department:** Filter by college or department
- **Document Status:** Filter by document completeness

#### **Export Options:**
- **CSV Export:** Download search results as spreadsheet
- **PDF Report:** Generate formatted report
- **Email List:** Export email addresses for communication
- **Summary Report:** Statistical overview

---

## 👥 **PROFILE MANAGEMENT**

### **Viewing User Profiles**

#### **Student Profiles:**
- **Personal Information:** Name, email, contact details
- **Academic Information:** Student number, college, course, year level
- **Documents:** Certificate of Registration and other files
- **Application History:** Previous applications and status
- **System Activity:** Login history and account activity

#### **Employee Profiles:**
- **Personal Information:** Name, email, contact details
- **Employment Information:** Employee ID, department, position
- **Employment Type:** Faculty, Staff, or Contractor
- **Documents:** Employment verification and other files
- **Application History:** IP applications submitted

#### **Profile Actions:**
- **Edit Profile:** Update user information (admin only)
- **Reset Password:** Generate new password for user
- **Change Role:** Modify user role (with caution)
- **View Activity:** See user login and application history
- **Send Message:** Communicate with user
- **Download Data:** Export user data

---

## 🔒 **SECURITY MANAGEMENT**

### **System Security Features**

#### **Access Control:**
- **Role-Based Permissions:** Admins have full system access
- **Session Security:** Automatic logout after inactivity
- **Audit Logging:** All admin actions are logged
- **IP Monitoring:** Track admin login locations
- **Failed Login Tracking:** Monitor unauthorized access attempts

#### **Security Monitoring:**
**Access:** Admin Dashboard → Security

- **Recent Logins:** View recent admin and user logins
- **Failed Attempts:** Monitor failed login attempts
- **Security Violations:** CSP violations and security events
- **Upload Security:** Monitor suspicious file uploads
- **System Integrity:** Check system file integrity

#### **Security Logs:**
- **Audit Log:** All administrative actions
- **CSP Violations:** Content Security Policy violations
- **Upload Errors:** File upload security failures
- **Session Events:** Session security events

---

## 📊 **REPORTING & ANALYTICS**

### **System Reports**

#### **User Statistics:**
- **Total Users:** Count by role and status
- **Registration Trends:** New registrations over time
- **Activity Reports:** User engagement metrics
- **Geographic Distribution:** User locations (if available)

#### **Application Reports:**
- **Application Volume:** Applications by time period
- **Processing Times:** Average time to process applications
- **Approval Rates:** Success rates by user type
- **Document Analysis:** Most common document issues

#### **System Performance:**
- **Response Times:** Page load and query performance
- **Security Events:** Security incident summaries
- **Storage Usage:** File storage and database size
- **Error Rates:** System error frequency

---

## 🛠️ **SYSTEM ADMINISTRATION**

### **Configuration Management**

#### **System Settings:**
**Access:** Admin Dashboard → Settings

- **Email Configuration:** SMTP settings and templates
- **Upload Limits:** File size and type restrictions
- **Security Settings:** Session timeouts and security policies
- **User Registration:** Enable/disable registration for roles
- **Notification Settings:** Email and system notifications

#### **User Role Management:**
- **Create Admin Accounts:** Add new administrators
- **Permission Management:** Set admin permissions
- **Role Assignments:** Change user roles
- **Account Deactivation:** Disable user accounts

#### **Backup and Maintenance:**
- **Database Backup:** Regular data backups
- **File Backup:** Upload and system file backups
- **System Updates:** Apply security and feature updates
- **Log Maintenance:** Archive and clean old logs

---

## 📧 **COMMUNICATION TOOLS**

### **User Communication**

#### **Email Notifications:**
- **Status Updates:** Automatic emails for status changes
- **Bulk Communications:** Send messages to multiple users
- **Custom Templates:** Create reusable email templates
- **Notification Settings:** Configure when emails are sent

#### **System Announcements:**
- **Global Messages:** Display messages to all users
- **Role-Specific:** Messages for specific user types
- **Priority Alerts:** Important system notifications
- **Maintenance Notices:** Scheduled downtime announcements

---

## 🔧 **TROUBLESHOOTING**

### **Common Issues**

#### **User Access Issues:**
- **Password Resets:** Help users who can't login
- **Account Lockouts:** Unlock suspended accounts
- **Email Issues:** Resolve email delivery problems
- **Document Access:** Fix file download issues

#### **Search Problems:**
- **No Results:** Check search syntax and filters
- **Slow Search:** Optimize search queries
- **Missing Data:** Verify data synchronization

#### **Application Issues:**
- **Missing Documents:** Help users upload required files
- **Status Confusion:** Clarify application status
- **Processing Delays:** Identify bottlenecks

### **Emergency Procedures:**
- **System Outage:** Emergency contact procedures
- **Security Incident:** Immediate response steps
- **Data Loss:** Recovery procedures
- **Performance Issues:** Escalation process

---

## 📚 **BEST PRACTICES**

### **Daily Administrative Tasks:**
1. **Check Pending Applications:** Review new submissions
2. **Monitor System Security:** Check security logs
3. **Respond to User Issues:** Address support requests
4. **Update Application Status:** Process pending items
5. **Review System Performance:** Monitor for issues

### **Weekly Administrative Tasks:**
1. **User Management Review:** Check new registrations
2. **Generate Reports:** Create weekly statistics
3. **Security Audit:** Review security events
4. **System Backup Verification:** Confirm backups are working
5. **Documentation Updates:** Update procedures as needed

### **Monthly Administrative Tasks:**
1. **Full System Review:** Comprehensive system check
2. **Performance Analysis:** Review system metrics
3. **Security Assessment:** Conduct security review
4. **User Feedback Review:** Analyze user feedback
5. **System Updates:** Apply necessary updates

---

## 📞 **SUPPORT & RESOURCES**

### **Getting Help:**
- **System Documentation:** Comprehensive guides available
- **Technical Support:** Contact system administrator
- **User Training:** Training materials for new admins
- **Emergency Contact:** 24/7 emergency support information

### **Additional Resources:**
- **Video Tutorials:** Step-by-step video guides
- **FAQ Database:** Common questions and answers
- **Best Practice Guides:** Recommended procedures
- **System Updates:** Release notes and change logs

---

**🔗 Related Documentation:**
- [User Manual - Students](User_Manual_Students.md)
- [User Manual - Employees](User_Manual_Employees.md)
- [Security Implementation Guide](Security_Implementation_Guide.md)
- [System Architecture Overview](System_Architecture_Overview.md)