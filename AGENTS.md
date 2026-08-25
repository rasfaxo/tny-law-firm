# AGENTS.md

## Project Identity

Nama project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Project ini adalah aplikasi skripsi berbasis Laravel yang digunakan untuk membantu proses pra-pendaftaran perkara secara online, mulai dari registrasi Klien, pengajuan data perkara, unggah dokumen pendukung, verifikasi berkas oleh Staf Legal, pemantauan status pengajuan, unggah ulang dokumen apabila terdapat catatan perbaikan, pemilihan jadwal konsultasi, hingga pembuatan laporan pra-pendaftaran oleh Admin.

## Single Source of Truth

AGENTS.md adalah sumber instruksi utama untuk semua AI agent.

File CLAUDE.md, GEMINI.md, dan GPT.md hanya berfungsi sebagai adapter atau ringkasan untuk masing-masing AI assistant. Jika ada perbedaan aturan antara AGENTS.md dan file lain, maka AGENTS.md harus dianggap sebagai aturan yang paling benar.

AI agent wajib membaca AGENTS.md terlebih dahulu sebelum membaca file dokumentasi lain.

## Main Tech Stack

- Backend: Laravel
- Frontend: Blade + Tailwind CSS
- Authentication: Laravel Breeze
- Database: MySQL
- File Storage: Laravel Storage
- Hosting Target: Cloud VPS
- Web Server: Apache atau Nginx
- Runtime: PHP 8.x / PHP-FPM
- Version Control: Git dan GitHub
- Editor: Zed

## Main Roles

Role disimpan dalam database menggunakan slug lowercase:

- klien
- admin
- staf_legal

Label tampilan:

- klien = Klien
- admin = Admin
- staf_legal = Staf Legal

## Main Actors and Features

### Klien

Klien dapat:

- Registrasi akun
- Login
- Mengelola profil
- Mengajukan pra-pendaftaran perkara
- Mengunggah dokumen pendukung
- Memantau status pengajuan
- Melihat catatan verifikasi
- Mengunggah ulang dokumen apabila terdapat catatan perbaikan
- Memilih jadwal konsultasi jika berkas sudah memenuhi syarat

### Admin

Admin dapat:

- Login
- Mengelola data pengguna
- Membuat akun Staf Legal
- Mengelola kategori perkara
- Mengelola data pra-pendaftaran
- Mengelola slot jadwal konsultasi
- Mencetak atau menyimpan laporan pra-pendaftaran melalui tampilan tabel dan print browser
- Melihat dashboard statistik ringkas

### Staf Legal

Staf Legal dapat:

- Login
- Melihat daftar pengajuan pra-pendaftaran
- Memeriksa detail perkara
- Memeriksa dokumen pendukung
- Memberikan status verifikasi
- Memberikan catatan verifikasi umum atau per dokumen
- Memperbarui status pengajuan berdasarkan hasil pemeriksaan

## Required Documentation References

Sebelum membuat fitur atau mengubah kode, AI agent wajib membaca dokumen berikut sesuai kebutuhan:

- docs/PROJECT_CONTEXT.md
- docs/DATABASE_PLAN.md
- docs/MODEL_RELATION_PLAN.md
- docs/STATUS_RULES.md
- docs/VALIDATION_RULES.md
- docs/SECURITY_RULES.md
- docs/FEATURE_LIST.md
- docs/ROUTES_PLAN.md
- docs/MANUAL_TESTING_PLAN.md
- docs/DEPLOYMENT_NOTES.md
- docs/AUDIT_FIXES.md

Apabila file dokumentasi belum tersedia, AI agent harus meminta file tersebut dibuat terlebih dahulu dan tidak boleh membuat asumsi sendiri.

## Locked Database Tables

Gunakan tabel berikut:

- users
- profil_klien
- kategori_perkara
- pra_pendaftaran_perkara
- dokumen_perkara
- verifikasi_berkas
- catatan_verifikasi
- riwayat_status
- jadwal_konsultasi
- booking_konsultasi

Jangan membuat tabel laporan.

## Locked Architecture Decisions

Wajib patuhi keputusan berikut:

1. Tidak membuat tabel laporan.
2. Laporan dihasilkan dari query dan rekap data.
3. Role dan status menggunakan VARCHAR di database, bukan ENUM.
4. Validasi role dan status dilakukan pada level aplikasi Laravel.
5. File dokumen disimpan di Laravel Storage.
6. Database hanya menyimpan metadata dokumen dan file_path.
7. uploaded_at tidak digunakan karena waktu unggah dokumen diwakili created_at.
8. tanggal_status tidak digunakan karena waktu perubahan status riwayat diwakili created_at.
9. tanggal_booking tetap digunakan sebagai atribut bisnis pada booking_konsultasi.
10. id_user digunakan sesuai konteks role:
    - pra_pendaftaran_perkara.id_user = Klien
    - booking_konsultasi.id_user = Klien
    - verifikasi_berkas.id_user = Staf Legal
    - jadwal_konsultasi.id_user = Admin
    - riwayat_status.id_user = pengguna yang mengubah status

## Laravel Table, Primary Key, and Foreign Key Rules

Project ini menggunakan nama tabel dan primary key custom sesuai rancangan skripsi.

Jangan mengandalkan penebakan nama tabel Laravel.

Semua model Eloquent wajib mendefinisikan nama tabel dan primary key secara eksplisit.

Contoh:

```php
protected $table = 'users';
protected $primaryKey = 'id_user';
public $incrementing = true;
protected $keyType = 'int';
```

Gunakan tabel dan primary key berikut:

| Model                 | Table                   | Primary Key    |
| --------------------- | ----------------------- | -------------- |
| User                  | users                   | id_user        |
| ProfilKlien           | profil_klien            | id_profil      |
| KategoriPerkara       | kategori_perkara        | id_kategori    |
| PraPendaftaranPerkara | pra_pendaftaran_perkara | id_pendaftaran |
| DokumenPerkara        | dokumen_perkara         | id_dokumen     |
| VerifikasiBerkas      | verifikasi_berkas       | id_verifikasi  |
| CatatanVerifikasi     | catatan_verifikasi      | id_catatan     |
| RiwayatStatus         | riwayat_status          | id_riwayat     |
| JadwalKonsultasi      | jadwal_konsultasi       | id_jadwal      |
| BookingKonsultasi     | booking_konsultasi      | id_booking     |

Jangan mengganti custom primary key menjadi default Laravel `id`.

Jangan mengubah:

- id_user menjadi user_id
- id_kategori menjadi kategori_id
- id_pendaftaran menjadi pra_pendaftaran_perkara_id
- id_dokumen menjadi dokumen_perkara_id
- id_verifikasi menjadi verifikasi_berkas_id
- id_jadwal menjadi jadwal_konsultasi_id
- id_booking menjadi booking_konsultasi_id

## Migration Rules

Saat membuat migration, gunakan nama primary key custom secara eksplisit.

Contoh:

```php
$table->id('id_user');
```

Untuk foreign key, gunakan referensi eksplisit.

Contoh:

```php
$table->unsignedBigInteger('id_user');

$table->foreign('id_user')
    ->references('id_user')
    ->on('users')
    ->cascadeOnDelete();
```

Contoh di atas hanya contoh teknis penulisan foreign key.

Penggunaan `cascadeOnDelete()`, `restrictOnDelete()`, atau `nullOnDelete()` harus mengikuti aturan relasi pada `docs/DATABASE_PLAN.md` dan tidak boleh diterapkan secara otomatis ke semua foreign key.

Jangan gunakan `$table->id()` jika tabel membutuhkan custom primary key.

Jangan gunakan foreign key shorthand jika menghasilkan nama kolom default Laravel yang bertentangan dengan rancangan skripsi.

Hindari ini jika menghasilkan nama kolom yang salah:

```php
$table->foreignId('user_id')->constrained();
```

Gunakan foreign key eksplisit seperti ini:

```php
$table->unsignedBigInteger('id_user');

$table->foreign('id_user')
    ->references('id_user')
    ->on('users');
```

Migration boleh dijalankan menggunakan `php artisan migrate` setelah file migration direview dan disetujui.

## User Model Authentication Rule

Model User menggunakan Laravel Authenticatable dan wajib memakai custom primary key `id_user`.

Contoh:

```php
class User extends Authenticatable
{
    protected $table = 'users';
    protected $primaryKey = 'id_user';
    public $incrementing = true;
    protected $keyType = 'int';
}
```

Jangan mengubah `users.id_user` menjadi `id`.

Laravel Breeze authentication harus tetap menggunakan `email` dan `password` untuk login.

Jika scaffolding Breeze memakai field `name`, sesuaikan menjadi `nama` sesuai rancangan final tabel `users`.

Field `role` hanya digunakan untuk role-based access control setelah autentikasi berhasil.

## Route Model Binding Rule

Karena project ini memakai custom primary key, setiap model Eloquent harus mendukung route model binding dengan benar.

Jika menggunakan implicit route model binding, definisikan `getRouteKeyName()` saat diperlukan.

Contoh:

```php
public function getRouteKeyName(): string
{
    return 'id_pendaftaran';
}
```

Jangan menganggap Laravel selalu melakukan binding menggunakan kolom default `id`.

Parameter route harus mengarah ke primary key model yang benar, misalnya:

- users.id_user
- pra_pendaftaran_perkara.id_pendaftaran
- dokumen_perkara.id_dokumen
- verifikasi_berkas.id_verifikasi
- jadwal_konsultasi.id_jadwal
- booking_konsultasi.id_booking

Contoh penting:

- `/klien/pengajuan/{pengajuan}` harus mengarah ke `pra_pendaftaran_perkara.id_pendaftaran`
- `/admin/jadwal-konsultasi/{jadwal}` harus mengarah ke `jadwal_konsultasi.id_jadwal`
- `/staf-legal/pengajuan/{pengajuan}` harus mengarah ke `pra_pendaftaran_perkara.id_pendaftaran`
- `/klien/dokumen/{dokumen}/unggah-ulang` harus mengarah ke `dokumen_perkara.id_dokumen`

## Upload Rules

- Format file yang diperbolehkan: PDF, JPG, JPEG, PNG.
- Ukuran maksimal: 5 MB per file.
- Storage path: storage/app/public/dokumen-perkara.
- File lama saat unggah ulang tidak boleh ditimpa.
- Dokumen lama disimpan sebagai file berbeda.
- Database hanya menyimpan metadata dan file_path.
- Gunakan nama file unik atau random saat menyimpan dokumen.
- Jangan percaya nama file asli dari user.
- Validasi extension dan MIME type.
- File dokumen tidak boleh diakses tanpa otorisasi.
- Akses dokumen harus mempertimbangkan role dan kepemilikan data.

## Business Rules

1. Klien tidak boleh mengubah data pengajuan setelah dikirim.
2. Klien hanya boleh mengunggah ulang dokumen jika ada catatan perbaikan.
3. Staf Legal dapat memberikan catatan umum atau catatan per dokumen.
4. Satu pengajuan hanya boleh memiliki satu booking aktif.
5. Jadwal konsultasi hanya boleh dipilih jika status pengajuan adalah berkas_lengkap.
6. Laporan menggunakan filter tanggal, status, dan kategori.
7. Sistem tidak menggunakan email untuk fase awal.
8. Admin dapat membuat akun Staf Legal.
9. Dashboard menampilkan statistik ringkas.

## Database Transaction Rules

Gunakan database transaction untuk proses yang menyimpan atau mengubah lebih dari satu tabel.

Transaction wajib digunakan pada proses berikut:

1. Membuat pra-pendaftaran perkara beserta dokumen dan riwayat status.
2. Verifikasi berkas oleh Staf Legal.
3. Pembuatan catatan verifikasi umum atau catatan per dokumen.
4. Unggah ulang dokumen oleh Klien.
5. Booking jadwal konsultasi oleh Klien.
6. Perubahan status pengajuan yang harus disertai pencatatan riwayat status.

Jika salah satu proses dalam transaction gagal, semua perubahan harus dibatalkan agar data tetap konsisten.

## Dangerous Command Rules

AI agent dilarang menjalankan atau menyarankan command berbahaya tanpa izin eksplisit dari pemilik project.

Command yang dilarang tanpa persetujuan eksplisit:

- php artisan migrate:fresh
- php artisan migrate:refresh
- php artisan migrate:rollback
- php artisan db:wipe
- rm -rf
- git reset --hard
- git clean -fd
- git push --force
- composer update
- npm audit fix --force

Command yang boleh disarankan setelah konteksnya jelas:

- php artisan migrate
- php artisan route:list
- php artisan storage:link
- php artisan test
- npm run build

Catatan: `php artisan migrate` boleh digunakan setelah file migration direview dan disetujui. Command tersebut tidak termasuk command destruktif, tetapi tetap tidak boleh dijalankan sembarangan tanpa memahami dampaknya terhadap database.

Jika command berisiko menghapus database, file, dependency, konfigurasi, atau riwayat Git, AI agent wajib menjelaskan risiko terlebih dahulu dan meminta persetujuan sebelum menjalankan atau menyarankannya.

## Coding Rules

AI agent wajib:

1. Menjelaskan rencana implementasi sebelum mengubah kode.
2. Menyebutkan file yang akan dibuat atau diubah.
3. Membangun fitur secara bertahap.
4. Menggunakan Laravel best practice.
5. Menggunakan migration untuk struktur tabel.
6. Menggunakan Eloquent model dan relasi.
7. Menggunakan Form Request untuk validasi.
8. Menggunakan middleware role untuk proteksi akses.
9. Menggunakan policy untuk proteksi data sensitif.
10. Menggunakan service class untuk logika bisnis penting.
11. Menggunakan database transaction untuk proses yang menyimpan banyak tabel.
12. Menggunakan Blade + Tailwind untuk tampilan.
13. Menggunakan pagination untuk daftar data.
14. Menggunakan search dan filter pada data penting.
15. Menggunakan flash message untuk hasil aksi.
16. Menyediakan empty state ketika data kosong.
17. Menjelaskan cara testing manual setelah fitur selesai.

## Git and Debugging Rules

- Setiap fitur sebaiknya dibuat dalam commit terpisah dengan pesan commit yang jelas.
- Perubahan besar harus dipecah menjadi beberapa commit kecil yang mudah direview.
- Saat memperbaiki error, AI agent wajib mengutamakan perubahan paling kecil yang menyelesaikan masalah.
- AI agent tidak boleh melakukan refactor besar saat sedang memperbaiki bug kecil.
- AI agent harus menjelaskan alasan perubahan sebelum mengubah struktur kode yang sudah berjalan.

## Definition of Done

Sebuah fitur dianggap selesai hanya jika:

1. Route sudah dibuat dan memakai middleware yang benar.
2. Controller method sudah dibuat.
3. Form Request validation sudah tersedia jika fitur menerima input user.
4. Model relation sudah sesuai rancangan database dan MODEL_RELATION_PLAN.md.
5. Policy atau ownership check diterapkan jika fitur menyentuh data sensitif.
6. Blade view sudah dibuat dan mudah dibaca.
7. Flash message sukses dan gagal tersedia.
8. Empty state tersedia untuk kondisi data kosong.
9. Pagination tersedia untuk daftar data.
10. Search atau filter tersedia jika dibutuhkan oleh fitur.
11. Testing manual sudah dijelaskan.
12. Tidak ada perubahan database tanpa persetujuan.
13. Tidak ada fitur tambahan di luar rancangan skripsi.
14. Tidak ada command berbahaya yang dijalankan tanpa izin.
15. Tidak ada pelanggaran terhadap AGENTS.md dan dokumen pendukung di folder docs.

## Forbidden Actions

AI agent dilarang:

1. Membuat tabel laporan.
2. Menggunakan ENUM pada database.
3. Mengubah nama tabel tanpa izin.
4. Mengubah nama kolom tanpa izin.
5. Menghapus migration yang sudah dibuat tanpa izin.
6. Menjalankan migration tanpa menjelaskan file migration yang dibuat dan tanpa persetujuan pemilik project.
7. Menghapus file upload user tanpa izin.
8. Menambahkan fitur email tanpa izin.
9. Menambahkan payment tanpa izin.
10. Menambahkan integrasi e-Court tanpa izin.
11. Menambahkan fitur di luar ruang lingkup skripsi.
12. Menggabungkan seluruh logic ke controller.
13. Membuat semua fitur sekaligus tanpa tahapan.
14. Mengabaikan ERD, LRS, Class Diagram, Sequence Diagram, Component Diagram, dan Deployment Diagram.
15. Mengubah struktur database hanya karena mengikuti default Laravel.
16. Menjalankan command berbahaya tanpa izin eksplisit.
17. Menyarankan reset database tanpa menjelaskan risiko.
18. Menyarankan reset Git tanpa menjelaskan risiko.
19. Mengabaikan VALIDATION_RULES.md.
20. Mengabaikan SECURITY_RULES.md.
21. Membuat fitur selesai tanpa memenuhi Definition of Done.
22. Membuka akses dokumen perkara tanpa otorisasi.
23. Menyimpan file upload memakai nama file asli user tanpa proses pengamanan.

## Ask Before Changing Design

AI agent must ask for confirmation before making any change that affects:

1. Database structure.
2. Table names.
3. Column names.
4. Primary keys.
5. Foreign keys.
6. Status values.
7. Role values.
8. Authentication flow.
9. Authorization rules.
10. Business rules.
11. File upload rules.
12. Report structure.

If the requested implementation conflicts with the locked thesis design, explain the conflict first and wait for approval.

## No Assumption Coding

AI agent must not create code based on assumptions.

Before implementing a feature, AI agent must verify:

1. The related feature exists in `docs/FEATURE_LIST.md`.
2. The related tables exist in `docs/DATABASE_PLAN.md`.
3. The related model relationships exist in `docs/MODEL_RELATION_PLAN.md`.
4. The related status values exist in `docs/STATUS_RULES.md`.
5. The related validation rules exist in `docs/VALIDATION_RULES.md`.
6. The related security rules exist in `docs/SECURITY_RULES.md`.

If any required rule is missing, AI agent must stop and ask the project owner.

## Service Layer Preference

Business logic should not be placed entirely inside controllers.

Use service classes for complex processes such as:

1. Creating pra-pendaftaran perkara.
2. Uploading documents.
3. Verifying case documents.
4. Re-uploading corrected documents.
5. Booking consultation schedules.
6. Updating status with riwayat_status.
7. Generating report queries.

Controllers should focus on receiving requests, calling services, and returning responses.

## Manual Test Before Commit

Before suggesting a Git commit, AI agent must provide manual testing steps and confirm that:

1. The feature can be accessed by the correct role.
2. Unauthorized roles are blocked.
3. Validation errors appear correctly.
4. Success flow works correctly.
5. Database records are created or updated correctly.
6. No unrelated files were changed.
7. No thesis design rule was violated.

## Error Debugging Protocol

When debugging an error, AI agent must:

1. Read the exact error message first.
2. Identify the file and line related to the error.
3. Explain the most likely cause.
4. Suggest the smallest safe fix.
5. Avoid large refactors unless necessary.
6. Avoid changing database structure unless the error truly requires it.
7. Explain how to test the fix.

## Locked Documentation Rule

AI agent must not modify locked documentation files unless explicitly asked.

Locked documentation includes:

1. `AGENTS.md`
2. `CLAUDE.md`
3. `GEMINI.md`
4. `GPT.md`
5. `docs/PROJECT_CONTEXT.md`
6. `docs/FEATURE_LIST.md`
7. `docs/DATABASE_PLAN.md`
8. `docs/MODEL_RELATION_PLAN.md`

If a code change requires documentation change, AI agent must explain the required change first and ask for approval.
