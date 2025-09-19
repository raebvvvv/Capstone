# User Folder Documentation

## Folder: `User/`

### Overview
The `User` folder contains all user-facing pages for the Capstonks (PUP e-IPMO) web application. It is divided into two main subfolders:
- `BeforeLogin/`: Pages accessible to users who are not logged in (e.g., landing, registration, login, info pages).
- `AfterLogin/`: Pages accessible only after a successful login (e.g., dashboard, application forms, profile, e-services).

This structure ensures a clear separation between public and authenticated user experiences, improving maintainability and security.

---

## Structure & Contents

### `User/BeforeLogin/`
- **index.php**: Main landing page for unauthenticated users.
- **about.php**: About the IPMO and the system.
- **before-e-services.php**: Info page about e-services before login.
- **before-originality-check.php**: Info page about originality check before login.
- **originality-form.php**: View/download originality check form (public).
- **ip-application.php**: Info and guide for IP application (public).
- **register-student-v2.php**: Student registration form (modern, validated).
- **login.php**: Login form for students and employees.

### `User/AfterLogin/`
- **after-landing.php**: Dashboard landing page after login.
- **after-about.php**: About page for logged-in users.
- **after-ip-application.php**: IP application guide for authenticated users.
- **after-originality-check.php**: Originality check guide for authenticated users.
- **after-originality-form.php**: View/download originality check form (authenticated).
- **copyright-application.php**: Copyright application and download forms.
- **e-services.php**: Main e-services dashboard (choose service to apply for).
- **student-application.php**: View and track user's IP applications.
- **student-profile.php**: User profile page (view/edit personal info).

---

## Features
- **Separation of concerns**: Public and authenticated pages are clearly separated.
- **Consistent navigation**: Navbar and footer are uniform across all user pages.
- **Bootstrap 5**: All pages use Bootstrap 5 for responsive, modern UI.
- **Custom CSS**: Each page may include its own CSS for specific styling, plus global `main.css`.
- **Asset management**: All asset paths are relative to the new folder structure for maintainability.
- **Security**: AfterLogin pages should be protected by session checks (not shown here, but recommended in PHP includes).

---

## Maintenance Notes
- When adding new user-facing pages, place them in the appropriate subfolder.
- Update navigation links and asset paths to match the folder structure.
- For shared components (headers, footers), consider using PHP includes for DRY code.
- Always validate user input on both client and server sides.
- Protect AfterLogin pages with session authentication.

---

## Dependencies
- Bootstrap 5 (CDN)
- Custom CSS: `css/main.css`, plus page-specific CSS files
- JavaScript: Bootstrap bundle, plus page-specific JS files in `javascript/`

---

## Author & Version
- Last updated: September 16, 2025
- Author: raebvvvv / PUP e-IPMO

---

## Example Usage
1. User visits `User/BeforeLogin/index.php` to learn about the system or register.
2. After login, user is redirected to `User/AfterLogin/after-landing.php`.
3. User can access e-services, view applications, or edit their profile from the AfterLogin dashboard.

---

## Contact
For questions or issues, contact the PUP e-IPMO web team.
