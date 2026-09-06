# AGENTS.md

## 1. Project Identity

Nama project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Project ini adalah aplikasi skripsi berbasis Laravel yang digunakan untuk membantu proses pra-pendaftaran perkara secara online, mulai dari registrasi Klien, pengajuan data perkara, unggah dokumen pendukung, verifikasi berkas oleh Staf Legal, pemantauan status pengajuan, unggah ulang dokumen apabila terdapat catatan perbaikan, pemilihan jadwal konsultasi, hingga pembuatan laporan pra-pendaftaran oleh Admin.

## 2. Instruction Priority / Single Source of Truth

`AGENTS.md` adalah sumber instruksi utama dan single source of truth untuk semua AI agent pada project ini.

File `CLAUDE.md`, `GEMINI.md`, dan `GPT.md` hanya berfungsi sebagai adapter. Jika ada perbedaan aturan, `AGENTS.md` adalah yang paling benar.

## 3. Main Tech Stack

- **Backend:** Laravel 13.x
- **Frontend:** Blade + Tailwind CSS + Vite
- **Authentication:** Laravel Session Authentication (awalnya di-scaffold menggunakan Breeze)
- **Database Engine:** MySQL
- **File Storage:** Laravel Filesystem Abstraction
- **Version Control:** Git dan GitHub

## 4. Current Staging Environment

Current staging environment diverifikasi dari repository configuration, CI/CD workflow, dan runtime/deployment evidence.

- **Hosting:** Azure App Service (Linux)
- **Web Server:** Nginx + PHP-FPM
- **PHP Runtime:** PHP 8.4
- **Node.js:** Versi 20
- **Database:** MySQL
- **Document Storage:** Azure Blob Storage

## 5. Main Roles

Role disimpan dalam database menggunakan slug lowercase:

- `klien` (Klien)
- `admin` (Admin)
- `staf_legal` (Staf Legal)

## 6. Main Actors and Features

### Klien

- Registrasi akun, Login, Mengelola profil
- Mengajukan pra-pendaftaran perkara
- Mengunggah dokumen pendukung
- Memantau status pengajuan & Melihat catatan verifikasi
- Mengunggah ulang dokumen apabila terdapat catatan perbaikan
- Memilih jadwal konsultasi jika berkas sudah memenuhi syarat
- Melihat detail booking konsultasi
- Mengajukan permintaan reschedule konsultasi
- Melihat status/detail permintaan reschedule

### Admin

- Login, Mengelola data pengguna, Membuat akun Staf Legal
- Mengelola kategori perkara
- Mengelola data pra-pendaftaran
- Mengelola slot jadwal konsultasi
- Mengelola booking konsultasi
- Mengonfirmasi detail teknis konsultasi
- Memproses permintaan reschedule konsultasi
- Mencetak laporan pra-pendaftaran dari tampilan tabel dan print browser
- Melihat dashboard statistik ringkas

### Staf Legal

- Login, Melihat daftar pengajuan
- Memeriksa detail perkara & dokumen pendukung
- Memberikan status verifikasi & catatan verifikasi (umum/per dokumen)
- Memperbarui status pengajuan berdasarkan hasil pemeriksaan

## 7. Documentation Reference Policy

Agent wajib membaca dokumentasi secara kontekstual sesuai task. Jangan asumsikan file ada jika tidak terbukti ada.

**Always Read:**

- `AGENTS.md`
- `docs/PROJECT_CONTEXT.md`
- `docs/FEATURE_LIST.md`

**Database / Model Tasks:**

- `docs/DATABASE_PLAN.md`
- `docs/MODEL_RELATION_PLAN.md`

**Business / Status Tasks:**

- `docs/STATUS_RULES.md`
- `docs/FEATURE_LIST.md`

**Validation / Security Tasks:**

- `docs/VALIDATION_RULES.md`
- `docs/SECURITY_RULES.md`

**Testing Tasks:**

- `docs/testing/TEST_PLAN.md`, `TEST_CASES.md`, `TESTING_STATE.md`, raw evidence
- task-specific execution specification

**Deployment Tasks:**

- `.github/workflows/*`
- deployment docs, runtime config

## 8. Zero-Assumption Verification Rules

Jika ada informasi atau file yang tidak ditemukan, Agent **tidak boleh membuat asumsi**.
Gunakan alur berikut:

1. Cari fakta dari source code aktual.
2. Cari dari migration yang committed.
3. Cari dari testing documentation.
4. Cari dari runtime configuration dan evidence (contoh: GitHub Actions).
5. Jika fakta ditemukan dan dapat diverifikasi: lanjut kerjakan task.
6. Jika tidak dapat diverifikasi dan membutuhkan keputusan bisnis/desain: stop, mark sebagai `NOT VERIFIED`, dan minta keputusan dari pemilik project.

## 9. Database and Schema Rules

**A. Locked Schema Naming Conventions**

- Semua tabel menggunakan nama spesifik sesuai rancangan skripsi (contoh: `users`, `profil_klien`, `pra_pendaftaran_perkara`).
- Jangan mengandalkan penebakan nama tabel Laravel.
- Tidak boleh membuat tabel laporan. (Laporan dihasilkan dari query data).
- Tidak boleh menggunakan ENUM di database (Role dan status menggunakan VARCHAR dan divalidasi di aplikasi).

**B. Existing Schema Inventory (Core)**

- `users`
- `profil_klien`
- `kategori_perkara`
- `pra_pendaftaran_perkara`
- `dokumen_perkara`
- `verifikasi_berkas`
- `catatan_verifikasi`
- `riwayat_status`
- `jadwal_konsultasi`
- `booking_konsultasi`
- `permintaan_reschedule`

**C. Rules for Future Migration Changes**
Setiap perubahan struktur harus disetujui terlebih dahulu, dan dilarang mengubah primary key default menjadi `id`.

## 10. Custom PK / FK Rules

Setiap model Eloquent wajib mendefinisikan nama tabel dan primary key secara eksplisit karena project ini tidak memakai default `id` milik Laravel.

Contoh Model:

```php
protected $table = 'users';
protected $primaryKey = 'id_user';
public $incrementing = true;
protected $keyType = 'int';
```

Primary Key Map:

- `users` -> `id_user`
- `profil_klien` -> `id_profil`
- `kategori_perkara` -> `id_kategori`
- `pra_pendaftaran_perkara` -> `id_pendaftaran`
- `dokumen_perkara` -> `id_dokumen`
- `verifikasi_berkas` -> `id_verifikasi`
- `catatan_verifikasi` -> `id_catatan`
- `riwayat_status` -> `id_riwayat`
- `jadwal_konsultasi` -> `id_jadwal`
- `booking_konsultasi` -> `id_booking`
- `permintaan_reschedule` -> `id_reschedule`

Karena menggunakan custom primary key, route model binding juga harus didefinisikan jika menggunakan implicit binding (menggunakan method `getRouteKeyName()`).

Contoh Penulisan Foreign Key pada Migration:

```php
$table->unsignedBigInteger('id_user');
$table->foreign('id_user')->references('id_user')->on('users');
```

Hindari penggunaan `$table->id()` atau `foreignId('user_id')->constrained()` karena tidak sesuai skema skripsi.

## 11. Migration Safety Rules

**A. Creating a New Migration**
Selalu gunakan konvensi custom PK/FK secara manual.

**B. Running Existing Migrations in CI/CD**
Pada deployment staging, pipeline menjalankan `php artisan migrate --force` saat container start. Migration baru yang dijalankan pada staging deployment MUST dirancang non-destructive terhadap existing data. Perlu dipahami bahwa `migrate --force` hanya menjalankan pending migrations dan tidak menjamin isi migration aman.

**C. Destructive Commands**
Dilarang keras menjalankan secara lokal maupun menyarankan:

- `php artisan migrate:fresh`
- `php artisan migrate:refresh`
- `php artisan migrate:rollback` (tanpa alasan sangat darurat)
- `php artisan db:wipe`

## 12. Authentication and Authorization Rules

- Aplikasi ini menggunakan mekanisme Session Authentication milik Laravel. Scaffolding awal dibuat menggunakan Laravel Breeze, namun arsitektur autentikasinya murni berdasarkan email & password, custom `id_user`, session regeneration, dan controller auth.
- Akses ke sistem, termasuk fitur dan resource/dokumen, harus diproteksi menggunakan middleware dan Policy/Gate berbasis Role.

## 13. Document Storage Rules

Operasi file (unggah, akses, hapus) wajib mematuhi aturan berikut:

- Seluruh operasi menggunakan **Laravel Filesystem abstraction**. Business document operations SHOULD use `Storage::disk(config('filesystems.document_disk'))` atau equivalent abstraction.
- Jangan hardcode `public`, `local`, atau `azure` di business logic kecuali ada alasan infrastructure-specific yang terdokumentasi.
- Physical storage path tidak boleh dijadikan business contract.
- Pada lingkungan _staging_, storage menggunakan Azure Blob Storage.
- Database hanya menyimpan metadata file dan reference path-nya.
- Akses ke file dokumen **wajib** melalui mekanisme authorization/ownership (tidak boleh bisa diakses publik secara bebas).
- Nama file asli dari user tidak boleh dipercaya. Gunakan nama file unik/random.
- Harus ada validasi extension, MIME type, dan ukuran file untuk setiap operasi upload sesuai dengan aturan yang berlaku.

## 14. Business Rules

1. Klien tidak boleh mengubah data pengajuan setelah dikirim.
2. Klien hanya boleh mengunggah ulang dokumen jika ada catatan perbaikan. File lama saat unggah ulang **tidak boleh ditimpa**. Dokumen lama disimpan sebagai file berbeda.
3. Staf Legal dapat memberikan catatan umum atau catatan per dokumen.
4. Satu pengajuan hanya boleh memiliki satu booking aktif.
5. Jadwal konsultasi hanya boleh dipilih jika status pengajuan adalah `berkas_lengkap`.
6. Laporan menggunakan filter tanggal, status, dan kategori.
7. Dilarang menambahkan email notification / transactional email integration tanpa explicit approval. (Authentication via email tidak termasuk larangan ini).
8. Admin dapat membuat akun Staf Legal.
9. Dashboard menampilkan statistik ringkas.

## 15. Database Transaction Rules

Proses yang melakukan multiple persistent writes MUST menjaga atomicity. Gunakan database transaction apabila satu business operation melakukan beberapa perubahan database yang harus berhasil/gagal bersama.
Contoh kasus utama:

- Membuat pra-pendaftaran perkara beserta dokumen dan riwayat status.
- Verifikasi berkas oleh Staf Legal.
- Pembuatan catatan verifikasi umum atau per dokumen.
- Unggah ulang dokumen oleh Klien.
- Booking jadwal konsultasi oleh Klien.
- Perubahan status pengajuan yang harus disertai pencatatan riwayat status.

## 16. Coding / Architecture Rules

1. Gunakan service class untuk logika bisnis yang kompleks (Business logic tidak boleh hanya berada di Controller).
2. Form Request wajib digunakan untuk request yang menerima user input dan memiliki non-trivial validation rules, kecuali ada alasan teknis yang terdokumentasi.
3. Gunakan Laravel best practices (Eloquent, Middleware, Policies).
4. Bangun fitur secara bertahap.
5. Sediakan empty state ketika data kosong.
6. Gunakan flash message untuk notifikasi aksi berhasil/gagal.
7. Halaman list harus menggunakan pagination (dan filter/search untuk data penting).

## 17. Environment Safety

- Current implementation / testing target adalah **STAGING**.
- Lingkungan **PRODUCTION** sepenuhnya _OUT OF SCOPE_ kecuali mendapat autorisasi eksplisit.
- Dilarang keras melakukan reset/wipe pada database staging tanpa explicit approval.
- Data testing yang digunakan harus data anonim / non-production.
- Security scans hanya boleh menargetkan lingkungan/target yang diotorisasi.
- Jangan asumsikan environment lokal akan sama persis konfigurasinya dengan staging/production, harus diverifikasi.

## 18. Secrets and Credentials

AI Agent **DILARANG KERAS**:

- Mencetak secret (token, password) ke chat atau logs.
- Melakukan commit yang mengandung file `.env` atau hardcoded secrets.
- Menyertakan credentials pada reports Markdown.
- Mengekspos publish profiles, DB passwords, Azure storage keys, API tokens.

Cara yang benar:

- Gunakan environment variables.
- Gunakan GitHub Secrets.
- Gunakan Azure App Service settings.
  Jika secara tidak sengaja membaca secret, agent harus me-redact nilainya dalam respons atau laporan.

## 19. CI/CD Rules

Pipeline otomatis dikelola melalui GitHub Actions.
Konsep Flow (berdasarkan file `.github/workflows/cd.yml` dan `startup.sh`):
`GitHub Actions → Build → Package → Deploy Azure → Azure startup → Nginx setup → migration → smoke test`.

Aturan:

- Gunakan GitHub Actions pipeline yang sudah ada sebagai metode deploy utama.
- Production deployment adalah out of scope.

## 20. Testing Evidence Integrity

- **Existing BEFORE testing evidence MUST be treated as immutable.** AI Agent dilarang: overwrite, edit, delete, regenerate raw BEFORE evidence dengan nama/path yang sama. AFTER/retest evidence harus disimpan terpisah.
- "Actual Result" pada saat testing **harus** berasal dari eksekusi nyata, bukan sekadar asumsi atau claim fiktif.
- **Never fabricate:** response time, throughput, request count, security alert, PASS/FAIL status, atau screenshot/log.
- Hasil dari scan otomatis (misal: OWASP ZAP) tidak otomatis menjadi kerentanan terkonfirmasi, perlu ada root-cause analysis dan verifikasi.

## 21. Testing Specification Precedence

Jika terdapat beberapa testing docs:

- Locked Test Plan/Test Case specification mempertahankan scope.
- Task-specific execution specification yang lebih baru boleh mengatur execution detail.
- Execution spec tidak boleh diam-diam mengubah scope/test case resmi.
  Jika terjadi conflict: verifikasi dan documentasikan.

## 22. Performance Improvement Rules

- Optimisasi hanya boleh dilakukan berdasarkan evidence/profiling.
- Business behavior eksisting wajib dipertahankan setelah optimisasi.
- Harus menampilkan komparasi before/after dengan parameter yang sama.
- **No Benchmark Gaming:** AI Agent MUST NOT menurunkan jumlah virtual users, mengubah workload hanya agar hasil lebih bagus, menghapus HTTP request dari scenario, bypass database/storage operation yang memang bagian dari business flow, menonaktifkan validation/security, mendeteksi JMeter lalu memberi special response, atau menggunakan data artifisial yang membuat workload tidak comparable. Retest harus comparable dengan baseline.

## 23. Security Improvement Rules

- Temuan wajib dipetakan ke baris/kode aktual di aplikasi.
- Perbaikan harus sebisa mungkin _minimal_ tanpa merusak otorisasi eksisting.
- Tetap harus diuji _direct access/ownership_ (apakah resource tetap terproteksi).
- Harus diretest setelah diperbaiki.
- Production scanning secara eksplisit dilarang.

## 24. Git Safety

- Dilarang menjalankan `git reset --hard` atau `git clean -fd` tanpa alasan mendesak dan izin.
- Dilarang keras `git push --force`.
- Jangan commit file yang tidak relevan dengan task (unrelated file).
- Review hasil `git status` dan `git diff` secara reguler.

## 25. Debugging Protocol

Saat menemui error, Agent harus:

1. Membaca pesan error yang akurat (stack trace/logs).
2. Menemukan titik lokasi (file/line) masalahnya.
3. Menganalisis penyebab utama (root cause).
4. Menyuguhkan solusi (fix) terkecil/paling aman, menghindari _large refactoring_ untuk masalah sepele.
5. Menjelaskan cara memvalidasi/testing setelah fix diterapkan.

## 26. Approval Boundaries

Jika task sudah eksplisit diminta, memiliki spesifikasi eksekusi, dan ruang lingkupnya jelas, AI Agent **boleh** beroperasi secara mandiri:
`READ → PLAN INTERNALLY → EXECUTE → VALIDATE → REPORT` tanpa meminta persetujuan iteratif.

AI Agent hanya **wajib** berhenti dan meminta persetujuan untuk:

- Destructive database actions (wipe, migrate:fresh).
- Perubahan di Production environment.
- Perubahan pada locked business rule (misalnya mengubah alur verifikasi).
- Perubahan semantik role atau permission.
- Redesain skema database / auth architecture.
- Ruang lingkup tugas yang membesar di luar spesifikasi awal secara drastis.
- Temuan bug vs redesign (Jika sekadar bug/security missing check, langsung perbaiki. Jika mengubah logic permission, minta approval).

## 27. Task-Specific Definition of Done (DoD)

Kriteria selesai bergantung pada tipe task:

- **Feature:** Implementasi selesai, ada Form Request (jika sesuai rule), Model relasi benar, Authorization/Policy diterapkan, Blade (UI state/empty state) ada, dites/divalidasi.
- **Bug Fix:** Error berhasil di-reproduce, root cause ketemu, fix minimal diterapkan, regression check aman.
- **Performance:** Ada before evidence, bottleneck diidentifikasi, optimisasi dilakukan, before/after comparison dengan retest.
- **Security Fix:** Evidence kerentanan valid, root cause ada, fix minimal diterapkan tanpa merusak fungsi bisnis, hasil retest membuktikan perbaikan.
- **Deployment:** Build sukses, deploy ke staging sukses, migration jalan, smoke test (HTTP 200) sukses.
- **Documentation:** Factual consistency, sumber divalidasi dari source/konfigurasi asli, tidak ada asumsi fiktif.

## 28. Forbidden Actions

AI Agent DILARANG KERAS:

- Mengubah nama tabel atau nama kolom tanpa izin.
- Menghapus migration file yang sudah committed dan dijalankan.
- Menyimpan file upload memakai nama file asli user tanpa proses pengamanan.
- Membuka akses dokumen perkara ke publik/tanpa otorisasi.
- Menambahkan integrasi e-Court.
- Menyarankan command berbahaya tanpa izin eksplisit.

## 29. Documentation Modification Rules

Dokumentasi ini (`AGENTS.md`, dsb) tidak boleh dimodifikasi selama pengerjaan task koding harian.

**Pengecualian:** Jika pemilik project memberikan task secara _eksplisit_ (explicitly requested documentation maintenance/update), Agent dipersilakan untuk memodifikasi dokumentasi terkait, hanya setelah melakukan verifikasi fakta pada project aktual.
