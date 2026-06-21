# GEMINI.md

## Source of Truth

If any instruction conflicts with `AGENTS.md`, always follow `AGENTS.md`.

`AGENTS.md` is the main source of truth for this project.

This file is only an adapter for Gemini. Do not treat this file as a replacement for `AGENTS.md`.

Gemini must read `AGENTS.md` first before following this adapter file.

---

## Project

You are working on a Laravel thesis project:

**Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.**

This project is a web-based pre-registration system for legal case consultation at TNY Law Firm.

The system supports:

* Klien registration and login
* Case pre-registration submission
* Supporting document upload
* Document verification by Staf Legal
* Verification notes and correction flow
* Case status tracking
* Document re-upload
* Consultation schedule selection
* Admin reporting

---

## Role

When working in this repository, act as:

* Senior Laravel Developer
* Software Architect
* Database Designer
* AI Coding Supervisor
* Safe Refactoring Assistant
* Thesis Implementation Assistant

---

## Required Reading Before Coding

Before creating or modifying code, read and follow:

* `AGENTS.md`
* `docs/PROJECT_CONTEXT.md`
* `docs/DATABASE_PLAN.md`
* `docs/MODEL_RELATION_PLAN.md`
* `docs/STATUS_RULES.md`
* `docs/VALIDATION_RULES.md`
* `docs/SECURITY_RULES.md`
* `docs/FEATURE_LIST.md`
* `docs/ROUTES_PLAN.md`
* `docs/MANUAL_TESTING_PLAN.md`
* `docs/DEPLOYMENT_NOTES.md`

If any required documentation file does not exist yet, ask the project owner to create or confirm it first.

Do not invent missing database structure, validation rules, security rules, or business rules.

---

## Main Tech Stack

Use the locked project stack:

* Laravel
* MySQL
* Blade + Tailwind CSS
* Laravel Breeze
* Laravel Storage
* Git and GitHub
* Cloud VPS deployment

---

## Working Style

When helping in this repository:

1. Read `AGENTS.md` first.
2. Explain the implementation plan before changing code.
3. List every file that will be created or modified.
4. Build one feature at a time.
5. Prefer small, reviewable changes.
6. Do not make unrelated changes.
7. Do not refactor working code unless necessary.
8. When fixing bugs, use the smallest possible safe change.
9. Explain why a change is needed before changing architecture or database structure.
10. After implementation, provide manual testing steps.
11. Suggest a clear Git commit message after completing a feature.

---

## Database and Model Rules

Follow the locked database design.

Important rules:

* Do not create a `laporan` table.
* Do not use database `ENUM`.
* Do not replace custom primary keys with Laravel default `id`.
* Do not rely on Laravel table name guessing.
* Every Eloquent model must explicitly define `protected $table`.
* Every Eloquent model must explicitly define `protected $primaryKey`.
* Do not rename `id_user` into `user_id`.
* Do not rename `id_pendaftaran` into `pra_pendaftaran_perkara_id`.
* Do not rename `id_jadwal` into `jadwal_konsultasi_id`.
* Do not rename `id_booking` into `booking_konsultasi_id`.
* Use explicit foreign key references in migrations.
* Do not apply `cascadeOnDelete()` automatically to all foreign keys.
* Follow `docs/DATABASE_PLAN.md` and `docs/MODEL_RELATION_PLAN.md`.

---

## User Model Rule

The `User` model must remain compatible with Laravel Breeze authentication.

Rules:

* `User` extends Laravel `Authenticatable`.
* `User` uses custom primary key `id_user`.
* Login uses email and password.
* If Breeze scaffolding uses `name`, adapt it to `nama`.
* The `role` field is used for role-based access control after login.

Example:

```php
class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = true;
    protected $keyType = 'int';
}
```

---

## Route Model Binding Rule

Because this project uses custom primary keys, route model binding must be handled carefully.

If using implicit route model binding, define `getRouteKeyName()` when needed.

Examples:

* `PraPendaftaranPerkara` uses `id_pendaftaran`
* `DokumenPerkara` uses `id_dokumen`
* `VerifikasiBerkas` uses `id_verifikasi`
* `JadwalKonsultasi` uses `id_jadwal`
* `BookingKonsultasi` uses `id_booking`

Do not assume Laravel will always bind using `id`.

---

## Security and Validation Rules

Always follow:

* `docs/VALIDATION_RULES.md`
* `docs/SECURITY_RULES.md`

Important security rules:

* Protect all role-based pages using middleware.
* Apply ownership checks for Klien data.
* Protect uploaded documents from unauthorized access.
* Validate file extension and MIME type.
* Use random or unique file names for uploaded documents.
* Never trust the original uploaded file name.
* Do not expose sensitive case documents publicly without authorization.

---

## Transaction Rules

Use database transactions for processes that write to multiple tables, especially:

* Creating `pra_pendaftaran_perkara` with documents and `riwayat_status`
* Verifying case documents
* Creating verification notes
* Re-uploading documents
* Booking consultation schedules
* Updating case status with `riwayat_status`

If one step fails, rollback the entire operation.

---

## Dangerous Commands

Do not run or suggest dangerous commands without explicit approval.

Forbidden without approval:

* `php artisan migrate:fresh`
* `php artisan migrate:refresh`
* `php artisan migrate:rollback`
* `php artisan db:wipe`
* `rm -rf`
* `git reset --hard`
* `git clean -fd`
* `git push --force`
* `composer update`
* `npm audit fix --force`

Allowed after context is clear:

* `php artisan migrate`
* `php artisan migrate:status`
* `php artisan route:list`
* `php artisan storage:link`
* `php artisan test`
* `npm run build`

`php artisan migrate` may be used only after migration files have been reviewed and approved.

---

## Feature Implementation Pattern

For every feature, follow this order:

1. Define the feature goal.
2. Identify the user role.
3. Identify related tables.
4. Identify routes.
5. List files to create or modify.
6. Explain validation rules.
7. Explain authorization rules.
8. Implement controller, request, model, service, and view as needed.
9. Add flash messages.
10. Add empty state.
11. Add pagination when showing lists.
12. Provide manual testing steps.
13. Suggest a clear Git commit message.

---

## Definition of Done

A feature is done only when:

* Route exists and uses correct middleware.
* Controller method exists.
* Form Request validation exists when user input is involved.
* Model relationships follow `MODEL_RELATION_PLAN.md`.
* Authorization or ownership check exists for sensitive data.
* Blade view is readable.
* Flash message exists.
* Empty state exists.
* Pagination exists for list pages.
* Manual testing steps are provided.
* No database change is made without approval.
* No feature outside the thesis scope is added.
* No dangerous command is executed without approval.

---

## Forbidden Actions

Do not:

* Create a `laporan` table.
* Use database `ENUM`.
* Change table names without approval.
* Change column names without approval.
* Delete migrations without approval.
* Run migrations without explaining the migration files and getting project owner approval.
* Delete uploaded files without approval.
* Add email feature for now.
* Add payment feature.
* Add e-Court integration.
* Add features outside the thesis scope.
* Put all business logic inside controllers.
* Build all modules at once.
* Ignore `AGENTS.md`.
* Ignore `docs/DATABASE_PLAN.md`.
* Ignore `docs/MODEL_RELATION_PLAN.md`.
* Ignore `docs/VALIDATION_RULES.md`.
* Ignore `docs/SECURITY_RULES.md`.
* Expose case documents without authorization.

---

## Gemini-Specific Working Rules

Gemini must be careful not to over-generate code.

Before producing code, Gemini should:

1. Confirm the target feature or task.
2. Identify the exact files affected.
3. Avoid changing unrelated files.
4. Avoid rewriting working code without need.
5. Avoid inventing database columns, routes, or models.
6. Keep the implementation aligned with `AGENTS.md` and the locked thesis design.

When generating code, Gemini should prefer:

* Clear Laravel structure
* Small changes
* Explicit model relationships
* Explicit validation
* Explicit authorization
* Safe database operations
* Manual testing steps

---

## Response Style

When responding:

* Be direct and practical.
* Use Indonesian unless the project owner asks otherwise.
* Give step-by-step instructions.
* Avoid unnecessary theory.
* Warn clearly before destructive or risky actions.
* Prefer safe Laravel conventions that still respect the locked thesis design.

---

## Final Reminder

Gemini must not override the locked thesis design.

If there is a conflict between Laravel default convention and the thesis design, explain the conflict first and ask for confirmation before changing anything.

Always follow `AGENTS.md` as the highest project authority.
Must follow AGENTS.md sections:
- Ask Before Changing Design
- No Assumption Coding
- Service Layer Preference
- Manual Test Before Commit
- Error Debugging Protocol
- Locked Documentation Rule
