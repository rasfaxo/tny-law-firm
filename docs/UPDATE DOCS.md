Anda bertindak sebagai Senior Software Architect + Repository Documentation Maintainer untuk project TNY Law Firm.

TASK
====

Update file `AGENTS.md` agar sesuai dengan KONDISI PROJECT AKTUAL saat ini.

Tujuan utama:
- menghapus aturan yang sudah outdated;
- memperbarui arsitektur, deployment, storage, testing, CI/CD, dan workflow AI Agent;
- mempertahankan aturan lama yang masih valid;
- menghindari asumsi;
- menjadikan `AGENTS.md` sebagai single source of truth yang benar-benar relevan untuk semua AI Agent berikutnya.

Jangan rewrite secara sembarangan.

Gunakan prinsip:

VERIFY FIRST
→ COMPARE OLD DOC VS ACTUAL PROJECT
→ CLASSIFY
→ UPDATE
→ VALIDATE


==================================================
1. SOURCE OF TRUTH HIERARCHY
==================================================

Untuk menentukan kondisi project aktual, gunakan urutan prioritas:

1. Source code aktual.
2. Migration yang committed.
3. Configuration files.
4. GitHub Actions workflows.
5. composer.json / composer.lock.
6. package.json / package-lock.json.
7. Runtime/deployment configuration yang dapat diverifikasi.
8. Testing documentation terbaru.
9. Existing project docs.
10. Existing `AGENTS.md`.

Jika `AGENTS.md` bertentangan dengan implementation aktual:
jangan mempertahankan informasi lama hanya karena sudah tertulis di sana.

Namun:
jangan otomatis menganggap implementation sebagai keputusan desain yang benar jika jelas merupakan bug atau temporary workaround.

Bedakan:

ACTUAL IMPLEMENTATION
vs
LOCKED PROJECT DECISION
vs
OUTDATED DOCUMENTATION


==================================================
2. READ BEFORE MODIFY
==================================================

Sebelum mengubah `AGENTS.md`, baca dan inspect seluruh sumber relevan.

Minimal:

- `AGENTS.md`
- `CLAUDE.md` jika ada
- `GEMINI.md` jika ada
- `GPT.md` jika ada

Project docs yang tersedia, misalnya:
- `docs/PROJECT_CONTEXT.md`
- `docs/DATABASE_PLAN.md`
- `docs/MODEL_RELATION_PLAN.md`
- `docs/STATUS_RULES.md`
- `docs/VALIDATION_RULES.md`
- `docs/SECURITY_RULES.md`
- `docs/FEATURE_LIST.md`
- `docs/ROUTES_PLAN.md`
- deployment docs
- testing docs terbaru

Testing:
- `TEST_PLAN.md`
- `TEST_CASES.md`
- `TESTING_STATE.md`
- `FINAL_TEST_REPORT.md` jika ada
- `PERFORMANCE_SECURITY_IMPROVEMENT.md` jika ada

Repository/config:
- `composer.json`
- `composer.lock`
- `package.json`
- `package-lock.json`
- `config/filesystems.php`
- `config/session.php`
- `bootstrap/app.php`
- `.github/workflows/*`
- `routes/*`
- relevant service providers
- migrations
- models
- middleware
- policies
- Form Requests
- storage/document handling implementation

Jangan mengubah `AGENTS.md` sebelum reconnaissance selesai.


==================================================
3. CLASSIFY EACH EXISTING RULE
==================================================

Audit `AGENTS.md` lama per section dan klasifikasikan sebagai:

KEEP
= masih benar dan relevan.

UPDATE
= konsep benar tetapi detail sudah berubah.

REMOVE
= sudah tidak berlaku.

SPLIT
= terlalu umum dan perlu dipisahkan menjadi global vs environment-specific.

VERIFY
= belum cukup evidence untuk memutuskan.

Jangan hapus rule hanya karena terlihat lama.
Pastikan rule memang tidak lagi valid.


==================================================
4. UPDATE PROJECT TECH STACK
==================================================

Perbarui tech stack berdasarkan repository/runtime aktual.

Verifikasi minimal:

- Laravel version
- PHP requirement
- PHP runtime staging
- frontend stack
- Vite
- Node.js version
- database engine
- database provider staging
- document storage abstraction
- staging storage provider
- hosting
- web server/runtime
- CI/CD
- Git/GitHub

Expected direction berdasarkan project saat ini harus diverifikasi, bukan diasumsikan:

- Laravel 13.x
- PHP requirement ^8.3
- staging PHP 8.4.x
- Blade + Tailwind CSS + Vite
- Node.js 20
- Azure App Service Linux
- Nginx + PHP-FPM
- Aiven MySQL
- Laravel Filesystem abstraction
- Azure Blob Storage for staging document storage
- GitHub Actions CI/CD

Pisahkan:

## Main Tech Stack

dari:

## Current Staging Environment

Agar dokumentasi tidak cepat outdated.


==================================================
5. UPDATE FILE STORAGE RULES
==================================================

Ini PRIORITAS TINGGI.

Hapus aturan global yang menganggap file dokumen selalu berada di:

`storage/app/public/dokumen-perkara`

Verifikasi implementation aktual.

Dokumentasikan prinsip berikut jika sesuai source code:

- seluruh operasi dokumen menggunakan Laravel Filesystem abstraction;
- document disk diambil dari configuration;
- business logic tidak hardcode `local`, `public`, atau `azure`;
- staging menggunakan Azure Blob Storage jika benar;
- database hanya menyimpan metadata/path/reference;
- authorization/ownership wajib untuk akses dokumen;
- nama file asli user tidak dipercaya;
- MIME, extension, dan size divalidasi;
- storage physical path bukan bagian dari business contract.

Jika ada config seperti:

`config('filesystems.document_disk')`

documentasikan sebagai preferred access mechanism.


==================================================
6. DATABASE DOCUMENTATION RULE
==================================================

Jangan mempertahankan daftar tabel statis jika sudah tidak lengkap.

Audit migration aktual.

Jika daftar `Locked Database Tables` sudah outdated:
ubah menjadi struktur yang lebih aman.

Pisahkan:

A. Locked schema naming conventions
B. Existing schema inventory
C. Rules for future migration changes

Pertahankan jika masih valid:
- custom primary keys;
- custom foreign key naming;
- no ENUM;
- no default Laravel `id` assumptions;
- explicit table/PK definition;
- no destructive migration commands.

Jangan mengubah nama table/column hanya karena default Laravel berbeda.


==================================================
7. MIGRATION RULE UPDATE
==================================================

Perbarui aturan migration agar kompatibel dengan deployment saat ini.

Bedakan:

A. Creating a new migration
B. Running existing migrations in CI/CD
C. Destructive migration commands

Jika existing staging deployment memang menjalankan:

`php artisan migrate --force`

setelah deploy,
documentasikan sebagai approved deployment behavior.

Namun tetap larang:

- migrate:fresh
- migrate:refresh
- db:wipe
- destructive rollback
- edit migration lama yang sudah deployed

kecuali ada explicit approval.


==================================================
8. DOCUMENT REFERENCE POLICY
==================================================

Refactor bagian `Required Documentation References`.

Jangan mewajibkan AI membaca semua docs untuk semua task.

Gunakan contextual references.

Contoh struktur:

## Always Read
- AGENTS.md
- PROJECT_CONTEXT
- FEATURE_LIST

## Database / Model Tasks
- DATABASE_PLAN
- MODEL_RELATION_PLAN

## Business / Status Tasks
- STATUS_RULES
- FEATURE_LIST

## Validation / Security Tasks
- VALIDATION_RULES
- SECURITY_RULES

## Testing Tasks
- TEST_PLAN
- TEST_CASES
- TESTING_STATE
- task-specific execution specification
- raw evidence

## Deployment Tasks
- `.github/workflows/*`
- deployment docs
- runtime config

Jangan menganggap file tertentu ada jika repository menunjukkan tidak ada.


==================================================
9. MISSING DOCUMENT BEHAVIOR
==================================================

Hapus rule lama:

"Jika documentation file tidak tersedia, Agent harus stop dan meminta file dibuat."

Ganti dengan zero-assumption workflow:

1. Cari fakta dari source code.
2. Cari migration.
3. Cari tests.
4. Cari config.
5. Cari runtime evidence.
6. Jika dapat diverifikasi:
   lanjut.
7. Jika tidak dapat diverifikasi dan membutuhkan business/design decision:
   mark `NOT VERIFIED` dan minta owner decision.

Agent tidak boleh membuat asumsi.


==================================================
10. UPDATE AI AGENT EXECUTION BEHAVIOR
==================================================

AGENTS lama terlalu sering meminta confirmation.

Perbarui rule agar cocok dengan autonomous execution.

Jika task:
- sudah explicitly approved;
- memiliki execution specification;
- scope sudah jelas;

maka Agent boleh:

READ
→ PLAN INTERNALLY
→ EXECUTE
→ VALIDATE
→ REPORT

tanpa meminta confirmation berulang.

Agent hanya wajib stop/ask approval untuk:

- destructive database actions;
- production changes;
- business rule changes;
- role/status semantic changes;
- schema redesign;
- authentication architecture changes;
- destructive storage operation;
- irreversible external actions;
- scope expansion besar.


==================================================
11. UPDATE DESIGN CHANGE BOUNDARY
==================================================

Bedakan:

BUG FIX / SECURITY FIX
dengan
DESIGN CHANGE

Contoh:
missing authorization check yang jelas melanggar existing rule
= bug/security fix
= tidak perlu design approval tambahan.

Mengubah role model atau permission semantics
= design change
= perlu approval.

Documentasikan boundary ini dengan jelas.


==================================================
12. TASK-SPECIFIC DEFINITION OF DONE
==================================================

Hapus atau refactor DoD lama yang menganggap semua task harus punya:

- route;
- controller;
- Blade;
- pagination;
- search;
- flash message.

Buat DoD per task type:

### Feature
- implementation
- validation
- authorization
- tests
- UI states jika applicable

### Bug Fix
- reproduction
- root cause
- minimal fix
- regression verification

### Performance Improvement
- before evidence
- bottleneck evidence
- implementation
- retest same workload
- before/after comparison
- regression check

### Security Improvement
- finding evidence
- root cause
- fix
- retest
- regression
- no business/authorization regression

### Deployment
- build
- deploy
- migration if applicable
- smoke test
- staging verification

### Documentation
- factual consistency
- source verification
- no fabricated claims


==================================================
13. AUTOMATED TESTING RULE
==================================================

Update manual-testing-centric rule.

Gunakan:

AUTOMATED FIRST
+
MANUAL WHERE NEEDED

Before commit:

- run relevant automated tests;
- run `php artisan test` when practical;
- run `npm run build` if build/frontend affected;
- perform manual verification where automation is insufficient;
- review git diff;
- verify no secret/unrelated file.


==================================================
14. ADD ENVIRONMENT SAFETY
==================================================

Tambahkan section:

## Environment Safety

Rules:

- current implementation/testing target = STAGING;
- production = OUT OF SCOPE unless explicitly authorized;
- never reset staging DB without explicit approval;
- test data must be non-production;
- security scans only against authorized targets;
- do not replicate destructive staging behavior to production;
- environment-specific assumptions must be verified.


==================================================
15. ADD SECRETS AND CREDENTIALS RULES
==================================================

Tambahkan section:

## Secrets and Credentials

AI Agent must never:

- print secrets;
- commit secrets;
- include credentials in reports;
- expose DB passwords;
- expose publish profiles;
- expose Azure storage keys;
- expose API tokens;
- copy secret values to Markdown.

Use:
- environment variables;
- GitHub Secrets;
- Azure App Service settings;
- approved secret stores.

If a secret appears:
- redact;
- do not propagate;
- flag rotation if needed.


==================================================
16. ADD CI/CD RULES
==================================================

Tambahkan:

## CI/CD Rules

Document current staging flow based on actual YAML.

Expected conceptual flow:

Checkout
→ PHP / Node setup
→ frontend build
→ Composer install
→ release packaging
→ Azure App Service deployment
→ runtime migration
→ smoke test

Do not hardcode exact step names unless verified.

Rules:

- existing GitHub Actions pipeline is preferred deployment path;
- do not create alternative deployment method without need;
- production deployment is out of scope;
- do not expose secrets in logs;
- post-deploy migration must be non-destructive.


==================================================
17. ADD TESTING EVIDENCE INTEGRITY
==================================================

Tambahkan:

## Testing Evidence Integrity

Rules:

- Actual Result must come from actual execution.
- Never fabricate:
  - response time;
  - throughput;
  - error rate;
  - request count;
  - security alert;
  - PASS/FAIL;
  - screenshot;
  - evidence path.
- Preserve raw evidence.
- Keep before/after evidence separate.
- Retest must use comparable workload.
- Report regression honestly.
- OWASP ZAP alert is not automatically a confirmed vulnerability.
- Security findings require validation.


==================================================
18. ADD PERFORMANCE & SECURITY IMPROVEMENT RULES
==================================================

Tambahkan section khusus karena project sedang berada pada fase ini.

## Performance Improvement Rules

- optimize only with evidence;
- no benchmark gaming;
- no removing validation/security for speed;
- no fake responses;
- no changing VU/workload to improve numbers;
- preserve business behavior;
- prioritize high-impact, low-risk change;
- before/after comparison required.

## Security Improvement Rules

- map finding to actual code;
- minimal secure fix;
- authorization must remain strict;
- test ownership/direct access;
- retest after fix;
- do not claim "fully secure";
- production scanning prohibited without approval.


==================================================
19. AUTHENTICATION RULE REVIEW
==================================================

Verify whether Laravel Breeze is still a meaningful current dependency/scaffolding.

Do not keep "Laravel Breeze is the architecture" if it is only scaffolding history.

Prefer documenting actual behavior:

- Laravel authentication/session mechanism;
- email/password login if still true;
- custom `id_user`;
- role used after authentication;
- session regeneration;
- authorization middleware/policy.

Keep Breeze mention only if still relevant.


==================================================
20. BUSINESS RULE VALIDATION
==================================================

Audit all existing business rules against actual implementation and latest project docs.

For each:
KEEP / UPDATE / VERIFY.

Do not silently rewrite business behavior.

If source implementation and locked thesis design conflict:
document conflict and preserve locked design unless owner already approved the implementation change.


==================================================
21. UPLOAD / RE-UPLOAD RULE VALIDATION
==================================================

Verify whether rules such as:

- old file cannot be overwritten;
- old document is preserved;
- re-upload only after correction note;

are still actual intended business rules.

If verified:
keep.

If implementation differs but no design approval exists:
flag discrepancy instead of silently changing AGENTS.md.


==================================================
22. GIT SAFETY
==================================================

Keep or strengthen:

- no `git reset --hard`
- no `git clean -fd`
- no force push
- no unrelated overwrite
- inspect `git status`
- review `git diff`

Do not commit unrelated files.


==================================================
23. LOCKED DOCUMENTATION POLICY UPDATE
==================================================

Refactor rule agar tidak menghalangi explicitly requested documentation tasks.

Use:

Locked documentation must not be modified during ordinary coding tasks.

Exception:
if owner explicitly requests documentation maintenance/update,
Agent may modify only the requested docs after verification.

For this task:
`AGENTS.md` modification is explicitly authorized.


==================================================
24. FORMAT OF NEW AGENTS.MD
==================================================

Target structure:

# AGENTS.md

## 1. Project Identity

## 2. Instruction Priority / Single Source of Truth

## 3. Main Tech Stack

## 4. Current Staging Environment

## 5. Main Roles

## 6. Main Actors and Features

## 7. Documentation Reference Policy

## 8. Zero-Assumption Verification Rules

## 9. Database and Schema Rules

## 10. Custom PK / FK Rules

## 11. Migration Safety Rules

## 12. Authentication and Authorization Rules

## 13. Document Storage Rules

## 14. Business Rules

## 15. Database Transaction Rules

## 16. Coding / Architecture Rules

## 17. Environment Safety

## 18. Secrets and Credentials

## 19. CI/CD Rules

## 20. Testing Evidence Integrity

## 21. Performance Improvement Rules

## 22. Security Improvement Rules

## 23. Git Safety

## 24. Debugging Protocol

## 25. Approval Boundaries

## 26. Task-Specific Definition of Done

## 27. Forbidden Actions

## 28. Documentation Modification Rules

Do not force this exact numbering if a clearer structure exists,
but preserve equivalent coverage.


==================================================
25. WRITING STYLE
==================================================

AGENTS.md harus:

- concise;
- operational;
- unambiguous;
- easy for AI Agent to follow;
- avoid redundant rules;
- avoid stale implementation detail unless environment-specific;
- distinguish mandatory vs preferred;
- distinguish global rule vs staging-specific fact.

Use MUST / MUST NOT / SHOULD where helpful.


==================================================
26. VALIDATION AFTER EDIT
==================================================

Setelah update:

1. Compare new AGENTS.md against source/config/workflow.
2. Search for stale references such as:
   - Cloud VPS
   - Apache as assumed hosting
   - `storage/app/public/dokumen-perkara`
   - PHP 8.2 if outdated
   - Azure Database for MySQL if not used
   - mandatory manual-only testing
   - unconditional stop on missing docs

3. Verify no business rule was silently changed.
4. Verify no secret appears.
5. Verify current CI/CD behavior is represented correctly.
6. Verify storage abstraction is represented correctly.
7. Verify staging/production boundary exists.
8. Verify improvement/testing evidence rules exist.
9. Verify dangerous commands remain prohibited.


==================================================
27. DO NOT MODIFY OTHER PROJECT FILES
==================================================

Scope task ini adalah documentation maintenance.

Do not modify:

- application source code;
- migrations;
- CI/CD;
- tests;
- database;
- storage;
- environment variables;

unless strictly needed to inspect them.

Only modify:
`AGENTS.md`

If adapter files:
- `CLAUDE.md`
- `GEMINI.md`
- `GPT.md`

need synchronization,
do NOT edit them yet.

Report that they require follow-up synchronization.


==================================================
28. FINAL REPORT
==================================================

After update, provide:

# AGENTS.MD UPDATE REPORT

## Sources Inspected

## Outdated Rules Found

For each:
- Old rule
- Status: UPDATE / REMOVE / KEEP
- Evidence
- New rule

## Sections Added

## Sections Removed

## Sections Updated

## Unverified Items

## Conflicts Detected

## Files Modified

Expected:
- AGENTS.md only

## Final Validation

- Tech Stack: VERIFIED / NOT VERIFIED
- Staging Environment: VERIFIED / NOT VERIFIED
- Database Rules: VERIFIED / NOT VERIFIED
- Storage Rules: VERIFIED / NOT VERIFIED
- CI/CD Rules: VERIFIED / NOT VERIFIED
- Security Rules: VERIFIED / NOT VERIFIED
- Testing Rules: VERIFIED / NOT VERIFIED
- Secrets Rules: VERIFIED / NOT VERIFIED
- Production Boundary: VERIFIED / NOT VERIFIED

## Final Verdict

Use one:

AGENTS.md UPDATED AND VERIFIED

or

AGENTS.md UPDATED WITH UNRESOLVED ITEMS

Do not claim fully verified if some facts remain uncertain.


==================================================
FINAL INSTRUCTION
==================================================

This is NOT a rewriting exercise.

The objective is:

OLD AGENTS.md
      ↓
REPOSITORY / CONFIG / RUNTIME VERIFICATION
      ↓
OUTDATED RULE DETECTION
      ↓
CURRENT PROJECT MODEL
      ↓
UPDATED AGENTS.md
      ↓
VALIDATION

Do not preserve outdated rules for compatibility.

Do not introduce new architecture based on preference.

Document the project that actually exists today while preserving explicit locked thesis/business decisions.

Stop after AGENTS.md and the update report are complete.