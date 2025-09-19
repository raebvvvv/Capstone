# Student Registration Page Documentation

## File: `register-student-fresh.php`

### Overview
This page provides a modern, user-friendly registration form for students of PUP e-IPMO. It is designed for clarity, security, and ease of use, following best practices for UI/UX and validation. The form collects essential student information and enforces strict validation rules for all fields, including file uploads.

---

## Features
- **Responsive, two-column layout** using Bootstrap 5
- **Custom, compact card design** for a modern look
- **Home/About Us navbar** matching the site's main navigation
- **Fields:**
  - Last Name, First Name, Middle Initial, Suffix
  - Student Number (letters, numbers, hyphens only)
  - Email (valid format, personal or webmail)
  - Password (with rules and real-time validation)
  - Re-enter Password (enabled only when password is valid)
  - Upload COR (PDF only, max 1MB)
- **Password field** with show/hide (eye icon) toggle
- **Automatic capitalization** for name fields
- **Real-time validation** for all fields with custom error messages
- **Client-side file validation** for COR upload
- **Accessible and mobile-friendly**

---

## Validation Rules
- **Name Fields:**
  - Last Name: Letters and hyphens only
  - First Name: Letters, spaces, hyphens
  - Middle Initial: Letters, hyphens, dot
  - Suffix: Letters, numbers, hyphens, dot
- **Student Number:** Letters, numbers, hyphens; no spaces
- **Email:** Must be a valid email address with a domain
- **Password:**
  - At least 12 characters
  - Must include both uppercase and lowercase letters
  - Must include at least one number and one special character (not forbidden)
  - Forbidden: “ \ < > ` ; | %
- **Re-enter Password:**
  - Disabled until password is valid
  - Must match the password
- **Upload COR:**
  - Only PDF files accepted
  - Max file size: 1MB

---

## JavaScript Functionality
- **Validation:**
  - Uses regular expressions and custom logic for all fields
  - Shows custom error messages below each field
  - Password and re-enter password fields are tightly coupled for validation
- **Password Toggle:**
  - Eye icon toggles password visibility
  - Stays aligned and visually clean even when field is invalid
- **File Upload:**
  - Checks file type and size before submission
- **Auto-capitalization:**
  - Name fields auto-capitalize first letter of each word

---

## CSS/Styling
- Uses Bootstrap 5 for layout and responsiveness
- Custom CSS in `css/register-student-custom.css` for card, form, and side image
- Consistent spacing, rounded corners, and compact design
- Error messages styled for clarity and accessibility

---

## Accessibility & Usability
- All fields have clear labels and required indicators
- Error messages are screen-reader friendly
- Form is keyboard accessible
- Visual feedback for invalid fields

---

## Maintenance Notes
- All JavaScript is inline for simplicity, but can be moved to a separate file if needed
- To update validation rules, edit the relevant regex or logic in the script section
- To change the design, update the custom CSS or Bootstrap classes
- For additional fields or requirements, follow the existing structure for validation and UI

---

## Dependencies
- Bootstrap 5 (CDN)
- Custom CSS: `css/register-student-custom.css`, `css/main.css`
- No external JS dependencies

---

## Author & Version
- Last updated: September 15, 2025
- Author: raebvvvv / PUP e-IPMO

---

## Example Usage
1. User fills out all required fields.
2. Password must meet all rules before re-enter password is enabled.
3. User uploads a PDF COR file (max 1MB).
4. On submit, all fields are validated; errors are shown inline if any.
5. If all is valid, form is submitted for server-side processing.

---

## Contact
For questions or issues, contact the PUP e-IPMO web team.
