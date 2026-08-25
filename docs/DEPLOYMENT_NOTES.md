# DEPLOYMENT_NOTES.md

## Purpose

Dokumen ini berisi catatan deployment untuk project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Dokumen ini digunakan sebagai panduan saat aplikasi Laravel dipindahkan dari environment lokal ke Cloud VPS.

AI agent wajib mengikuti dokumen ini saat membantu proses deployment, konfigurasi server, environment production, storage, database, permission, build frontend, dan backup.

---

## Source of Truth

Dokumen ini harus konsisten dengan:

1. `AGENTS.md`
2. `docs/PROJECT_CONTEXT.md`
3. `docs/DATABASE_PLAN.md`
4. `docs/MODEL_RELATION_PLAN.md`
5. `docs/STATUS_RULES.md`
6. `docs/VALIDATION_RULES.md`
7. `docs/SECURITY_RULES.md`
8. `docs/FEATURE_LIST.md`
9. `docs/ROUTES_PLAN.md`
10. `docs/MANUAL_TESTING_PLAN.md`

Jika ada konflik aturan database, ikuti:

```text
docs/DATABASE_PLAN.md
```

Jika ada konflik aturan keamanan, ikuti:

```text
docs/SECURITY_RULES.md
```

---

## Deployment Target

Target deployment:

| Item             | Value                 |
| ---------------- | --------------------- |
| Server           | Cloud VPS             |
| Operating System | Linux / Ubuntu Server |
| Web Server       | Apache atau Nginx     |
| Runtime          | PHP 8.x / PHP-FPM     |
| Database         | MySQL                 |
| Backend          | Laravel               |
| Frontend         | Blade + Tailwind CSS  |
| Authentication   | Laravel Breeze        |
| File Storage     | Laravel Storage       |
| Version Control  | Git / GitHub          |

---

## Production Deployment Principles

Aturan umum deployment:

1. Deployment dilakukan setelah fitur utama lolos manual testing.
2. File `.env` production tidak boleh masuk Git.
3. `APP_DEBUG` production wajib `false`.
4. Database production harus dipisahkan dari database lokal.
5. Jangan menjalankan command destruktif di production.
6. Jangan menjalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`, atau command sejenis di production.
7. `php artisan migrate` hanya boleh dijalankan setelah migration direview dan disetujui.
8. Backup database dan dokumen wajib dilakukan sebelum perubahan besar.
9. Permission folder harus diatur agar Laravel dapat menulis ke `storage` dan `bootstrap/cache`.
10. Dokumen perkara tidak boleh diakses tanpa authorization.

---

## Required Server Components

Komponen yang dibutuhkan pada server:

1. PHP 8.x.
2. PHP extension yang dibutuhkan Laravel.
3. Composer.
4. Node.js dan npm untuk build frontend.
5. MySQL server atau akses ke MySQL external.
6. Git.
7. Apache atau Nginx.
8. PHP-FPM jika menggunakan Nginx atau konfigurasi FPM.
9. SSL certificate jika domain sudah tersedia.

PHP extension umum yang diperlukan:

```text
bcmath
ctype
curl
dom
fileinfo
json
mbstring
openssl
pdo
pdo_mysql
tokenizer
xml
zip
```

---

## Production Environment File

File `.env` production harus dibuat langsung di server dan tidak boleh di-commit ke Git.

Contoh konfigurasi utama:

```env
APP_NAME="TNY Law Firm"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://domain-production.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=tny_law_firm
DB_USERNAME=production_user
DB_PASSWORD=production_password

FILESYSTEM_DISK=public

MAIL_MAILER=smtp
MAIL_HOST=smtp.example.com
MAIL_PORT=587
MAIL_USERNAME=your-email@example.com
MAIL_PASSWORD=your-email-password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=no-reply@example.com
MAIL_FROM_NAME="TNY Law Firm"

LOG_CHANNEL=stack
LOG_LEVEL=error
```

Aturan:

1. `APP_ENV` wajib `production`.
2. `APP_DEBUG` wajib `false`.
3. `APP_URL` harus sesuai domain production.
4. `APP_KEY` dibuat menggunakan `php artisan key:generate`.
5. Credential database tidak boleh hardcode di source code.
6. Jangan membagikan file `.env`.
7. Konfigurasi `MAIL_*` hanya digunakan untuk forgot password / reset password yang sudah disetujui.
8. Jangan menambahkan konfigurasi email lain untuk notifikasi konsultasi, reminder jadwal, atau broadcast status tanpa persetujuan.

---

## Repository Deployment Flow

Alur deployment dari repository:

```bash
git clone <repository-url> tny-law-firm
cd tny-law-firm
```

Jika repository sudah ada di server:

```bash
git pull origin main
```

Aturan:

1. Branch production harus jelas.
2. Jangan deploy dari branch eksperimen.
3. Jangan menggunakan `git reset --hard` tanpa persetujuan eksplisit.
4. Jangan menggunakan `git clean -fd` tanpa persetujuan eksplisit.
5. Pastikan perubahan sudah di-commit sebelum deployment.

---

## Composer Install

Install dependency Laravel:

```bash
composer install --no-dev --optimize-autoloader
```

Aturan:

1. Gunakan `--no-dev` untuk production.
2. Jangan menjalankan `composer update` di production tanpa persetujuan.
3. Jika dependency berubah, review dulu perubahan `composer.json` dan `composer.lock`.

---

## Frontend Build

Install dependency frontend jika diperlukan:

```bash
npm install
```

Build asset production:

```bash
npm run build
```

Aturan:

1. Production menggunakan hasil build, bukan `npm run dev`.
2. Jangan menjalankan `npm audit fix --force` tanpa persetujuan.
3. Jika package frontend berubah, review dulu `package.json` dan `package-lock.json`.

---

## Konsultasi Online/Offline Deployment Notes

Aturan:

1. Sistem tidak memiliki integrasi Zoom, Google Meet, atau video meeting otomatis.
2. Link konsultasi online diisi manual oleh Admin melalui aplikasi.
3. Sistem tidak menyediakan chat internal, video call internal, integrasi kalender eksternal, pembayaran, atau integrasi e-Court.
4. Tidak ada konfigurasi deployment tambahan untuk fitur konsultasi selain konfigurasi aplikasi Laravel standar.
5. Tidak ada konfigurasi email tambahan selain forgot password / reset password yang sudah disetujui.

---

## Application Key

Generate application key:

```bash
php artisan key:generate
```

Aturan:

1. Jalankan hanya setelah `.env` production tersedia.
2. Jangan mengganti `APP_KEY` production sembarangan setelah aplikasi berjalan.
3. Mengubah `APP_KEY` dapat memengaruhi data terenkripsi dan session.

---

## Database Production Setup

Database production menggunakan MySQL.

Nama database yang direkomendasikan:

```text
tny_law_firm
```

Aturan:

1. Database production harus dibuat sebelum migration.
2. User database production sebaiknya memiliki privilege terbatas sesuai kebutuhan aplikasi.
3. Jangan menggunakan user root database untuk aplikasi production jika memungkinkan.
4. Jangan membuat tabel di luar rancangan tanpa persetujuan.
5. Jangan membuat tabel `laporan`.
6. Jangan menggunakan database `ENUM`.
7. Tabel `migrations` boleh ada sebagai metadata internal Laravel.
8. Tabel auxiliary Laravel seperti `sessions`, `cache`, `jobs`, dan `failed_jobs` tetap perlu direview jika digunakan; `password_reset_tokens` boleh digunakan untuk forgot password dan tidak dihitung sebagai tabel domain.

---

## Migration Rules in Production

Migration production hanya boleh dijalankan setelah file migration direview dan disetujui.

Command yang diperbolehkan setelah review:

```bash
php artisan migrate
```

Cek status migration:

```bash
php artisan migrate:status
```

Dilarang tanpa persetujuan eksplisit:

```bash
php artisan migrate:fresh
php artisan migrate:refresh
php artisan migrate:rollback
php artisan db:wipe
```

Aturan:

1. Backup database sebelum migration penting.
2. Review migration sebelum dijalankan.
3. Pastikan migration mengikuti `docs/DATABASE_PLAN.md`.
4. Jangan membuat kolom atau tabel baru tanpa persetujuan.
5. Jangan menjalankan migration destruktif di production.
6. Jika environment production meminta konfirmasi, opsi --force hanya boleh digunakan setelah migration direview dan disetujui.

---

## Seeder Rules in Production

Seeder boleh digunakan untuk data awal tertentu, seperti akun Admin awal dan kategori perkara awal.

Aturan:

1. Seeder Admin awal boleh dijalankan jika belum ada Admin.
2. Seeder kategori perkara awal boleh dijalankan jika data belum tersedia.
3. Seeder tidak boleh menghapus data production.
4. Seeder tidak boleh membuat data dummy testing di production.
5. Password akun awal harus segera diganti setelah login pertama.

Contoh command setelah review:

```bash
php artisan db:seed --class=AdminSeeder
php artisan db:seed --class=KategoriPerkaraSeeder
```

---

## Storage Setup

Project menggunakan Laravel Storage.

Path dokumen perkara:

```text
storage/app/public/dokumen-perkara
```

Buat symbolic link storage:

```bash
php artisan storage:link
```

Pastikan folder upload tersedia:

```bash
mkdir -p storage/app/public/dokumen-perkara
```

Aturan:

1. Dokumen perkara disimpan menggunakan Laravel Storage.
2. Database hanya menyimpan metadata dan `file_path`.
3. File dokumen tidak disimpan langsung di database.
4. File lama tidak boleh ditimpa saat re-upload.
5. Nama file final harus random atau unik.
6. Jangan menampilkan raw `file_path` kepada user.
7. Dokumen harus diakses melalui route/controller dengan authorization check.
8. Jika menggunakan disk `public`, jangan menampilkan URL langsung tanpa kontrol akses.
9. Untuk keamanan lebih ketat, private storage dapat dipertimbangkan setelah disetujui.

---

## Document Storage Access Protection

Karena dokumen perkara disimpan pada:

```text
storage/app/public/dokumen-perkara
```

dan deployment menggunakan:

```bash
php artisan storage:link
```

maka akses langsung ke folder dokumen harus dibatasi pada production.

Aturan:

1. Dokumen perkara tetap harus diakses melalui route/controller yang melakukan authorization check.
2. Aplikasi tidak boleh menampilkan direct URL ke `/storage/dokumen-perkara/...`.
3. Jika `php artisan storage:link` digunakan, web server harus dikonfigurasi agar akses langsung ke folder `/storage/dokumen-perkara` ditolak, atau dokumen dipindahkan ke private storage setelah disetujui.
4. File dokumen hanya boleh diberikan kepada user setelah role dan ownership/authorization check berhasil.
5. Jika private storage digunakan, perubahan konfigurasi storage harus direview dan disetujui karena memengaruhi konfigurasi storage dan deployment.
6. AI agent tidak boleh mengubah strategi storage dari `public` ke `private` tanpa persetujuan pemilik project.

---

## Folder Permission

Laravel membutuhkan permission tulis pada folder berikut:

```text
storage
bootstrap/cache
```

Contoh command umum:

```bash
chmod -R 775 storage bootstrap/cache
```

Jika perlu menyesuaikan owner:

```bash
chown -R www-data:www-data storage bootstrap/cache
```

Aturan:

1. Permission harus cukup untuk web server menulis cache, log, session, dan file upload.
2. Jangan memberi permission terlalu longgar seperti `777` kecuali benar-benar terpaksa dan disetujui.
3. Pastikan folder upload dokumen dapat ditulis oleh aplikasi.
4. Pastikan file sensitif seperti `.env` tidak bisa diakses publik.

---

## Cache and Optimization

Command optimization production:

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Clear cache jika ada perubahan konfigurasi:

```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

Aturan:

1. Setelah mengubah `.env`, jalankan ulang cache config jika menggunakan `config:cache`.
2. Setelah mengubah route, jalankan ulang route cache jika menggunakan `route:cache`.
3. Jangan menjalankan cache command sebelum konfigurasi production benar.

---

## Web Server Configuration

Document root web server harus mengarah ke folder:

```text
public
```

Contoh target:

```text
/var/www/tny-law-firm/public
```

Aturan:

1. Jangan arahkan web server ke root project Laravel.
2. Folder selain `public` tidak boleh langsung bisa diakses dari browser.
3. File `.env` tidak boleh bisa diakses publik.
4. File source code tidak boleh bisa diakses publik.
5. Route Laravel harus berjalan melalui `index.php` di folder `public`.

---

## HTTPS and Domain

Jika domain tersedia, production sebaiknya menggunakan HTTPS.

Aturan:

1. Gunakan HTTPS untuk login, upload dokumen, dan akses data perkara.
2. `APP_URL` harus menggunakan domain production.
3. Jika menggunakan HTTPS, pastikan konfigurasi reverse proxy atau web server benar.
4. Jangan mengirim credential melalui HTTP pada production.

---

## Queue, Jobs, Cache, and Session Tables

Pada fase awal, sistem tidak membutuhkan queue, job, atau email otomatis lain selain forgot password.

Aturan:

1. Jangan membuat tabel `jobs` atau `failed_jobs` tanpa persetujuan.
2. Tabel `password_reset_tokens` boleh digunakan untuk forgot password melalui Laravel Breeze.
3. Jangan membuat tabel `sessions` berbasis database tanpa persetujuan.
4. Jangan membuat tabel `cache` berbasis database tanpa persetujuan.
5. Jika fitur tersebut diperlukan kemudian, harus direview dan disetujui terlebih dahulu.
6. Tabel `migrations` tetap diperbolehkan sebagai metadata internal Laravel.

---

## Password Reset and Email

Email di production hanya digunakan untuk forgot password / reset password.

Aturan:

1. Forgot password diperbolehkan karena sudah disetujui dan harus mengikuti Laravel Breeze.
2. SMTP production wajib dikonfigurasi agar link reset benar-benar dapat terkirim.
3. Credential email harus disimpan di `.env` dan tidak boleh di-commit ke Git.
4. Jika SMTP belum siap, forgot password tidak bisa digunakan secara nyata di production.
5. Email tidak boleh dipakai untuk notifikasi perkara, reminder jadwal, broadcast status, atau email lain di luar reset password.

---

## Report Deployment Rules

Laporan pra-pendaftaran dibuat dari query dan tampilan tabel.

Aturan:

1. Jangan membuat tabel `laporan`.
2. Jangan membuat export PDF tanpa persetujuan.
3. Jangan membuat export Excel tanpa persetujuan.
4. Print browser boleh digunakan.
5. Filter laporan harus tetap divalidasi.
6. Laporan hanya boleh diakses Admin.

---

## Security Checklist Before Go Live

Checklist keamanan sebelum go live:

1. `APP_ENV=production`.
2. `APP_DEBUG=false`.
3. `.env` tidak masuk Git.
4. Web server mengarah ke folder `public`.
5. Route role sudah menggunakan middleware.
6. Ownership check Klien berjalan.
7. Authorization dokumen berjalan.
8. Raw `file_path` tidak ditampilkan.
9. Upload file dibatasi PDF, JPG, JPEG, PNG.
10. Ukuran upload maksimal 5 MB per file.
11. Dokumen disimpan dengan nama random atau unik.
12. Password tersimpan dalam bentuk hash.
13. Akun `nonaktif` tidak bisa login.
14. Tidak ada route debug.
15. Tidak ada fitur email/payment/e-Court yang belum disetujui.
16. Tidak ada tabel `laporan`.
17. Tidak ada database `ENUM`.

---

## Manual Testing Before Go Live

Sebelum go live, jalankan testing berdasarkan:

```text
docs/MANUAL_TESTING_PLAN.md
```

Minimal area yang wajib lolos:

1. Register Klien.
2. Login dan logout.
3. Role access.
4. Kelola pengguna Admin.
5. Profil Klien.
6. Pra-pendaftaran perkara.
7. Upload dokumen.
8. Akses dokumen dengan authorization.
9. Verifikasi berkas.
10. Status dan catatan Klien.
11. Unggah ulang dokumen.
12. Jadwal konsultasi.
13. Booking konsultasi.
14. Penyelesaian konsultasi.
15. Laporan pra-pendaftaran.
16. Database integrity.

---

## Backup Rules

Backup wajib dilakukan sebelum perubahan besar.

Data yang perlu dibackup:

1. Database MySQL.
2. Folder dokumen perkara.
3. File `.env` production secara aman.
4. Konfigurasi web server jika diperlukan.

Contoh backup database:

```bash
mysqldump -u production_user -p tny_law_firm > backup_tny_law_firm.sql
```

Contoh backup dokumen:

```bash
tar -czf backup_dokumen_perkara.tar.gz storage/app/public/dokumen-perkara
```

Aturan:

1. Jangan menyimpan backup di folder public web.
2. Jangan commit backup ke Git.
3. Simpan backup di lokasi aman.
4. Backup harus diuji dapat dipulihkan jika memungkinkan.

---

## Deployment Update Flow

Alur update aplikasi production:

1. Backup database dan dokumen jika update besar.
2. Pull code terbaru dari branch production.
3. Install dependency jika berubah.
4. Build asset frontend jika berubah.
5. Review migration jika ada.
6. Jalankan migration hanya setelah disetujui.
7. Jalankan cache/optimization.
8. Cek permission storage.
9. Jalankan smoke test.
10. Cek log error.

Contoh command umum:

```bash
git pull origin main
composer install --no-dev --optimize-autoloader
npm install
npm run build
php artisan migrate:status
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Catatan:

```text
php artisan migrate hanya dijalankan jika migration sudah direview dan disetujui.
```

---

## Smoke Test After Deployment

Setelah deployment, lakukan smoke test:

1. Buka halaman utama.
2. Login sebagai Admin.
3. Login sebagai Staf Legal.
4. Login sebagai Klien.
5. Cek dashboard setiap role.
6. Cek halaman daftar pengajuan.
7. Cek halaman upload dokumen.
8. Cek akses dokumen melalui controller.
9. Cek halaman laporan Admin.
10. Cek log Laravel.

Cek log:

```bash
tail -n 100 storage/logs/laravel.log
```

Expected result:

1. Tidak ada error fatal.
2. Login berjalan.
3. Route role berjalan.
4. Upload folder dapat ditulis.
5. Dokumen tidak bisa diakses tanpa authorization.
6. Laporan dapat tampil.

---

## Rollback Notes

Rollback harus dilakukan hati-hati.

Aturan:

1. Jangan rollback database sembarangan.
2. Jangan menjalankan `migrate:rollback` di production tanpa persetujuan.
3. Jika rollback code diperlukan, pastikan kompatibel dengan struktur database saat ini.
4. Jika perubahan database sudah terjadi, rencana rollback harus dibuat secara khusus.
5. Backup harus tersedia sebelum rollback besar.

Command berisiko dan dilarang tanpa persetujuan:

```bash
php artisan migrate:rollback
php artisan migrate:refresh
php artisan migrate:fresh
php artisan db:wipe
git reset --hard
git clean -fd
```

---

## Production Troubleshooting Notes

Jika terjadi error production:

1. Jangan aktifkan `APP_DEBUG=true` untuk publik.
2. Cek log Laravel.
3. Cek permission folder `storage` dan `bootstrap/cache`.
4. Cek `.env`.
5. Cek koneksi database.
6. Cek hasil `php artisan route:list`.
7. Cek hasil `php artisan migrate:status`.
8. Perbaiki dengan perubahan terkecil yang aman.

Command yang dapat membantu:

```bash
php artisan route:list
php artisan migrate:status
php artisan config:clear
php artisan cache:clear
tail -n 100 storage/logs/laravel.log
```

---

## Forbidden Deployment Actions

AI agent tidak boleh menyarankan atau menjalankan tanpa persetujuan:

1. `php artisan migrate:fresh`.
2. `php artisan migrate:refresh`.
3. `php artisan migrate:rollback`.
4. `php artisan db:wipe`.
5. `git reset --hard`.
6. `git clean -fd`.
7. `git push --force`.
8. `composer update`.
9. `npm audit fix --force`.
10. Menghapus folder upload dokumen.
11. Menghapus database production.
12. Mengubah struktur database tanpa review.
13. Mengaktifkan fitur email/payment/e-Court tanpa persetujuan.
14. Membuka akses dokumen tanpa authorization.
15. Mengubah `APP_DEBUG=true` pada production publik.

---

## Final Notes for AI Agent

AI agent wajib mengikuti aturan berikut:

1. Deployment harus aman dan bertahap.
2. Jangan menjalankan command destruktif tanpa persetujuan eksplisit.
3. Jangan membuat tabel atau kolom baru tanpa persetujuan.
4. Jangan menjalankan migration sebelum direview.
5. Jangan menghapus data production.
6. Jangan menghapus dokumen perkara.
7. Jangan membagikan file `.env`.
8. Pastikan dokumen perkara tetap dilindungi authorization.
9. Pastikan `APP_DEBUG=false` pada production.
10. Selalu cocokkan deployment dengan `SECURITY_RULES.md`, `DATABASE_PLAN.md`, dan `MANUAL_TESTING_PLAN.md`.

---

## Fase 13 Final Deployment Checklist

Checklist ini digunakan untuk persiapan demo dan deployment final setelah audit Fase 13.

Kebutuhan server:

1. PHP 8.x dan extension Laravel yang dibutuhkan.
2. Composer.
3. Node.js dan npm.
4. MySQL.
5. Apache/Nginx dengan document root mengarah ke folder `public`.
6. Permission tulis untuk `storage` dan `bootstrap/cache`.

Contoh command deployment awal setelah source code tersedia dan `.env` sudah disiapkan:

```bash
composer install --no-dev --optimize-autoloader
npm install
npm run build
cp .env.example .env
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Catatan penting:

1. `cp .env.example .env` hanya contoh untuk environment baru; isi `.env` production harus disesuaikan manual dan tidak boleh di-commit.
2. `php artisan migrate --force` hanya boleh dijalankan setelah seluruh migration direview dan disetujui.
3. `php artisan db:seed --force` hanya boleh dipakai untuk seed data awal yang memang disetujui, bukan data dummy production.
4. Jangan menjalankan `php artisan migrate:fresh`, `php artisan migrate:refresh`, `php artisan migrate:rollback`, atau `php artisan db:wipe` di production.
5. Jalankan `php artisan storage:link` agar storage public tersedia, tetapi dokumen perkara tetap harus diakses melalui controller dengan authorization.
6. Jika route/config/view masih sering berubah di local, jangan menjalankan cache production di local; command cache ditujukan untuk deployment yang sudah stabil.
7. Sistem tidak memiliki integrasi Zoom/Google Meet otomatis, chat internal, payment, integrasi kalender, atau e-Court.
8. Tidak ada queue atau cron wajib untuk fitur utama saat ini selain kebutuhan framework/default environment jika nanti dipilih secara sadar.

Akun demo lokal/presentasi yang dapat disiapkan:

| Role       | Email                  | Password demo |
| ---------- | ---------------------- | ------------- |
| Admin      | admin@example.com      | password      |
| Staf Legal | staflegal@example.com  | password      |
| Klien      | klien@example.com      | password      |

Aturan akun demo:

1. Akun demo hanya untuk local/demo, bukan password production.
2. Password production harus diganti dan tidak boleh ditulis di dokumentasi publik.
3. Pastikan akun demo memiliki role sesuai slug final: `admin`, `staf_legal`, dan `klien`.
4. Pastikan akun demo berstatus `aktif` sebelum digunakan untuk presentasi.
