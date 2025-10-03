# DB-driven dropdowns – session notes (2025-10-03)

These notes capture the plan and decisions so we can pick up quickly later.

## Context snapshot
- Goal: Replace hard-coded dropdowns with database-driven lists that admins can manage.
- Scope candidates: academic levels, campuses, colleges, programs, work classifications; later: doc types, issue labels, roles.

## Agreed approach (simple)
- Add small lookup tables (with `is_active`):
  - `academic_levels`, `campuses`, `colleges(code,name)`, `programs(college_id, level, name)`, `work_classifications(code?, name)`.
- API: `/api/vocab` – GET lists (public), POST mutations (admin-only + CSRF).
- Frontend: `academic-dropdowns.js` fetches from API with fallback to current static dataset.
- Admin UI: `admin/vocab_manager.php` to add/deactivate/restore items.

## Open todos
- [ ] Build `api/vocab_api.php` (GET list, POST add/update/delete; ensure tables exist).
- [ ] Update `javascript/forms/academic-dropdowns.js` to consume API + fallback.
- [ ] Create `admin/vocab_manager.php` (minimal CRUD, CSRF, role-check).
- [ ] Seed tables from the current canonical lists.
- [ ] Optional next: move document types + upload rules to DB; convert issue labels to DB.

## Nice-to-haves
- Server indexes for fast filters: submissions(status, created_at, college, campus, work_classification), submission_documents(submission_id, doc_type), submission_notes(submission_id, created_at).
- Lock down `/uploads` via secure download endpoint.

## How we’ll use this note
- We can append any new decisions or tasks here as we go.
- When ready, I’ll implement items in the order above and check them off.

-- End of note --
                                                