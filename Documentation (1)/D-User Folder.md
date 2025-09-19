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
Canonical (active) authenticated pages:
- **index.php (root)**: Unified guest + authenticated dashboard (replaces `after-landing.php`).
- **about.php (root)**: Unified About page (replaces `after-about.php`).
- **ip-application.php**: IP application guide (replaces `after-ip-application.php`).
- **originality-check.php**: Originality check guide (replaces `after-originality-check.php`).
- **originality-form.php**: Originality check form view (replaces `after-originality-form.php`).
- **copyright-application.php**: Copyright application & forms.
- **e-services.php**: Service selection dashboard.
- **student-application.php**: Track user's IP applications.
- **student-profile.php**: View / edit user profile.

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
1. User visits `index.php` (root) and sees the public landing experience (guest view).
2. User logs in via `login.php`; upon success they are redirected back to the same `index.php`, which now renders the authenticated dashboard (no separate after-landing URL).
3. User navigates to `about.php`, `e-services.php`, or application-related pages. Logout returns them to the guest view on `index.php`.

---

## Deprecations & Redirects
| Legacy File | Status | Replacement | Notes |
|-------------|--------|-------------|-------|
| `User/AfterLogin/after-landing.php` | Stub redirect | `index.php` | Will be removable after external links updated; upgrade to 301 when stable. |
| `User/AfterLogin/after-about.php` | Stub redirect | `about.php` | Safe to delete after redirect grace period. |
| `User/AfterLogin/after-ip-application.php` | Stub redirect | `ip-application.php` | New canonical page adds auth gate. |
| `User/AfterLogin/after-originality-check.php` | Stub redirect | `originality-check.php` | Content migrated. |
| `User/AfterLogin/after-originality-form.php` | Stub redirect | `originality-form.php` | Form view migrated. |

All legacy stubs emit `Cache-Control: no-store` and temporary (302) redirects. After monitoring (e.g., access logs show no hits for 2+ weeks), switch to 301 and optionally remove the stub files.

### Redirect Strategy
- Current stubs issue 302 (temporary) until confident all references are updated; then switch to 301 (permanent) and schedule deletion after a grace period.
- Stubs send `Cache-Control: no-store` to prevent browsers caching obsolete HTML.

### Validation Checklist
- Searching for `after-ip-application.php`, `after-originality-check.php`, `after-originality-form.php` returns only stub redirect files + documentation.
- Buttons and nav links now point to `ip-application.php`, `originality-check.php`, `originality-form.php`.
- Accessing any legacy URL results in a 302 to the canonical page with no-store headers.

---

## Session & Cache Notes
- Authenticated pages include headers to prevent back/forward cache (BFCache) serving logged-in views after logout (`Cache-Control: no-store, no-cache, must-revalidate`).
- Redirect stubs also emit no-store headers for consistency.

---

## Contact
For questions or issues, contact the PUP e-IPMO web team.
