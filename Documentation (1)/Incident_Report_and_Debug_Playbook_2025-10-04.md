# PUP e-IPMO — Incident Report and Debug Playbook (Sep 28 – Oct 4, 2025)

This document compiles the issues encountered over the past days, the root causes identified, fixes applied, and a practical methodology used to isolate and resolve problems. It also lists changed files and quick verification steps.

## Timeline Overview

- Sep 28–30: UI polish and branding alignment (tables, buttons, PUP maroon/yellow), modal alignment, textual date formatting.
- Sep 30–Oct 1: Dashboard PH time alignment and “As of” timestamps; weekly refresh instructions.
- Oct 1–Oct 3: Admin Applications search reliability improvements; debug mode added; control overlap resolved.
- Oct 3–Oct 4: Deep-dive into search edge cases (prefixes like “SRID-”, multi-word names), parameter binding fix (HY093), and robust diagnostics.

## Incidents and Fixes

### 1) PH Timezone mismatch and display consistency
- Symptom: Timestamps not aligned to Philippine time; ambiguous “As of” labels.
- Root cause: Default timezone not set; client display not normalized.
- Fix:
  - Set default timezone to `Asia/Manila` in `config.php`.
  - Standardized server “As of” and client display formats across pages.
- Validation: Cross-checked server output and UI across Admin and User pages; confirmed PH time appears consistently.

### 2) Dashboard data staleness / weekly refresh
- Symptom: Cached/derived dashboard metrics not refreshing regularly.
- Root cause: No scheduler to rebuild cached data.
- Fix:
  - Added weekly refresh scripts/instructions (PowerShell/Batch) to rebuild metrics.
  - Documented how to run/verify.
- Validation: Manual run followed by UI reload showed updated numbers; timestamps reflected new build time.

### 3) Table borders and minimalist UI alignment
- Symptom: Misaligned line between Remarks and Actions; non-minimalist visuals.
- Root cause: Table CSS inconsistencies.
- Fix:
  - Adjusted table CSS to align borders and adopt minimalist styling.
  - Standardized PUP maroon (#900c0c) and yellow (#ffd54f) across components.
- Validation: Visual inspection; no layout overlap; consistent styling.

### 4) Brand color and control styling (admin + user)
- Symptom: Inconsistent link underline colors and button palettes (e.g., “switch” button blue).
- Root cause: Mixed default Bootstrap styles and custom CSS.
- Fix:
  - Applied brand colors to links (underline) and buttons.
  - Harmonized modal buttons and Action column alignment.
- Validation: Visual diff and hover states; verified request ID links are maroon and underlined.

### 5) Student details modal date formatting
- Symptom: Numeric date format in modal; requested textual format.
- Root cause: Default date rendering.
- Fix: Added display-only formatter (no data logic changes) in `shared-details-modal.js`.
- Validation: Modal shows dates like “September 24, 2025”; data integrity preserved.

### 6) Manage Applications: Search reliability and UX
- Symptoms:
  - Enter key didn’t reliably submit; search bar and Has Notes overlapped.
  - Search didn’t default to Pending tab; confusing empty results.
  - Partial matches (names with multi-words, SRID prefixes) not returned.
- Root causes:
  - Form control order/spacing; missing explicit submit behavior.
  - Server WHERE too narrow; name matching not tokenized; ID matching hyphen-sensitive.
- Fixes:
  - Reordered controls (Search before Has Notes) with nowrap; added explicit Search button; hidden submit for Enter.
  - Default tab to Pending on search submit; client auto-switch to first non-empty tab.
  - Server-side WHERE upgrades in `admin/ticket.php`:
    - Name matching: lowercased, whitespace-normalized, AND-match across tokens via concatenated name.
    - Student number and submission code: hyphen/space-insensitive; also raw `LOWER(submission_code) LIKE` to support prefixes like “SRID-”.
  - Tab labels show counts during search.
- Validation:
  - Tested queries like “Juan Malinaw” and “SRID-”; confirmed rows appear and tabs auto-switch.

### 7) Search diagnostics (observability)
- Symptom: Hard to see what search was doing.
- Fix:
  - Added debug mode (`?debug=1`) to `admin/ticket.php`:
    - Displays GET params, normalized terms (lower/stripped), SQL WHERE, bound params, tab counts.
    - DB quick-checks: total submissions, SRID/ERID counts, sample codes.
    - Probes: `code_like` and `code_stripped_like` mini-counts.
    - Runtime info: file path and mtime to verify the executing script.
    - Filtered-count (WHERE-only) to separate WHERE from JOIN issues.
- Validation: Used during SRID debugging; provided immediate insight.

### 8) HY093 — Invalid parameter number (main blocker for SRID search)
- Symptom: Debug probes showed matches, but main query returned zero; “Main query error: SQLSTATE[HY093]”.
- Root cause:
  - PDO native prepares can throw HY093 when placeholders are reused across multiple OR branches or when extra params are bound that don’t appear in the SQL.
  - Tokenized name search reused the same placeholder across several columns per token; some drivers treat this strictly.
- Fixes:
  - Unique placeholders for non-token LIKEs: `:q_like1..:q_like5`, `:q_strip1..2`.
  - Simplified tokenized name search to use only the concatenated name expression per token (`nameExpr LIKE :tN`) to avoid duplicate usage across multiple columns.
  - Inlined `admin_id` in the unread-join and removed the stray bound `:admin_id`.
  - In debug filtered-count, bind only params that actually occur in the WHERE string.
- Validation:
  - After changes, exact code searches like `SRID-2025-20251003-1` produce rows; tab counts are non-zero; auto-switching works.

## Troubleshooting Methodology (Playbook)

1. Reproduce with minimal, explicit inputs
   - Use precise queries (e.g., `SRID-2025-20251003-1`).
   - Toggle `?debug=1` to enable diagnostics.

2. Inspect the debug panel
   - GET params, normalized values, SQL WHERE, Params.
   - Check tab counts and Active tab.
   - Runtime file path and mtime (confirm correct script/version).

3. Sanity-check the data quickly
   - DB quick-checks: `COUNT(*)`, codes beginning with SRID/ERID, sample rows.
   - If data exists but app shows zero, focus on WHERE, joins, or param binding.

4. Probe narrowly
   - Run isolated counts for `LOWER(submission_code) LIKE :q_like` and stripped variants to confirm basic match logic.
   - Add a filtered count using the same WHERE and params but without joins.

5. Look for hidden filters
   - Ensure “Has Notes” is off unless intended (adds `AND COALESCE(sn.note_count,0) > 0`).
   - Verify status→tab mapping is post-query (not filtering the SQL).

6. Parameter discipline
   - Avoid reusing placeholder names many times across ORs; prefer unique names.
   - Bind only placeholders that exist in the prepared SQL to prevent HY093.

7. Verify UI behavior
   - Ensure Search button and Enter submit the form; default tab to Pending.
   - Auto-switch to first non-empty tab after a search.

8. Clear caches and confirm executable
   - Hard reload (Ctrl+F5), restart Apache/PHP-FPM.
   - Use debug’s file mtime to ensure the running code is the edited version.

## Files Touched (high level)

- `config.php`: Default timezone → `Asia/Manila`.
- `admin/admin.php`, `admin/dashboard_refresh.php`, `javascript/admin-dashboard.js`: PH time display and reload controls.
- `scripts/refresh_dashboard.ps1/.bat`: Weekly refresh instructions.
- `User/Afterlogin/student-application.php` and related CSS/JS: UI polish, PUP branding, modal and Action alignment.
- `css/*` (student-application.css, manageuser.css, ticket.css, shared-details-modal.css): Styling and brand consistency.
- `javascript/shared-details-modal.js`: Textual date formatting for modal.
- `admin/ticket.php`: Major search fixes, debug mode, unique placeholders, tokenized name search simplification, inlined admin_id, counts and auto-tab.
- `javascript/admin-filters.js`: Submit behavior, debug flag preservation, auto-switch logic, console logs when `debug=1`.

## Verification Steps (Quick)

- Exact code search: `?search=SRID-2025-20251003-1&debug=1`
  - Probes: code_like=1, stripped_like=1
  - Filtered count (WHERE only) >= 1
  - Counts show non-zero in the target tab; UI switches to it.
- Prefix search: `?search=SRID-&debug=1`
  - Probes show 17+; tab counts non-zero.
- Name search: `?search=Juan Malinaw&debug=1`
  - AND semantics on tokens via concatenated name; results visible.

## Recommendations / Next Steps

- Add unit-like request tests for search parsing and WHERE construction (PHP integration tests).
- Optional: Highlight matched text on the client.
- Exact-ID prioritization: if search looks like full SRID/ERID, show exact matches first.
- Keep debug mode available behind an admin-only flag.

---
Prepared on 2025-10-04. This document should be kept with the repository under `Documentation (1)/` and updated when new incidents occur.
