# Runbook Deployment Production Final v1.0.0 — Rumahweb

Dokumen ini adalah runbook operasional deployment production, bukan materi utama skripsi. Prosedur hanya boleh dimulai dari tag Git immutable `v1.0.0` setelah seluruh gate source dan artifact lulus.

## 1. Batas keamanan dan target

- Target kanonis: `https://tnypartners.com`; `www.tnypartners.com` mengarah ke apex.
- Aplikasi Laravel berada pada `<APP_ROOT>` di luar `public_html`; hanya paket public boleh diekstrak ke `<PUBLIC_ROOT>`.
- Database, kredensial, backup, log privat, dan object Azure dari rehearsal **tidak boleh** dipromosikan atau diimpor ke production.
- Container Azure tetap private. Dokumen hanya boleh diakses melalui route aplikasi yang terotorisasi.
- Directory Privacy dipertahankan sepanjang instalasi dan seluruh gate internal. Jangan menghapus directive autentikasi dari `.htaccess` sebelum go-live disetujui.
- Jangan menyimpan secret pada Git, artifact, chat, log publik, atau `public_html`.

## 2. Source dan artifact rilis

1. Pastikan source berada pada tag `v1.0.0` dan tag tersebut menunjuk ke commit yang telah lulus seluruh test.
2. Jalankan workflow GitHub Actions **Build Rumahweb Manual Artifact** dengan ref `v1.0.0`.
3. Unduh satu artifact dan verifikasi `rumahweb-sha256.txt` secara lokal sebelum upload.
4. Simpan `rumahweb-manifest.json` dan checksum SHA-256 bersama catatan rilis privat. Manifest harus menyebut commit yang sama dengan tag.

Artifact yang dipakai:

- `rumahweb-application.zip`: diekstrak di luar `public_html` sehingga menghasilkan folder aplikasi.
- `rumahweb-public-html.zip`: diekstrak hanya ke `<PUBLIC_ROOT>`.
- `rumahweb-manifest.json` dan `rumahweb-sha256.txt`: bukti build, bukan file yang diekstrak sebagai aplikasi.

Jangan memakai artifact yang dibangun dari branch lain atau membangun ulang secara manual setelah checksum diverifikasi.

## 3. Provisioning production sebelum cutover

### Database MariaDB

1. Buat satu database MariaDB production baru dan satu user aplikasi baru melalui cPanel.
2. Berikan user hanya hak yang diperlukan pada database production tersebut.
3. Jangan mengimpor database rehearsal dan jangan memakai user/database rehearsal.
4. Siapkan backup database harian dengan retensi 30 hari di lokasi di luar web root, serta prosedur backup tambahan tepat sebelum setiap deployment.
5. Uji restore backup hanya ke database uji yang berbeda. Jangan memakai `migrate:fresh`, `migrate:refresh`, `migrate:rollback`, `db:wipe`, atau import rehearsal sebagai langkah deployment.

### Azure Blob Storage

1. Buat kredensial production baru dengan hak minimum hanya pada container yang dipakai aplikasi; jangan mendaur ulang kredensial rehearsal.
2. Pastikan container private dan set konfigurasi Azure berikut pada `.env`:

```dotenv
DOCUMENT_DISK=azure
AZURE_STORAGE_PREFIX="production/tnypartners"
AZURE_READINESS_PREFIX="release-gate/production/tnypartners"
```

3. Verifikasi dua prefix tersebut tidak sama dan tidak saling menjadi parent/child. Dokumen pengguna selalu memakai `production/tnypartners`; readiness hanya boleh membuat object di `release-gate/production/tnypartners`.
4. Di Azure Portal, verifikasi blob soft delete 14 hari, container soft delete 14 hari, versioning aktif, serta lifecycle yang menyimpan previous version 90 hari.

### Resend

1. Buat API key production baru dengan izin sending-only dan simpan hanya pada `.env` production.
2. Gunakan domain yang sudah verified, From `no-reply@tnypartners.com`, serta Reply-To alamat firma yang dikuasai.
3. Nonaktifkan open dan click tracking. Tambahkan hanya record DNS yang diberikan Resend; jangan menimpa SPF/DMARC yang ada tanpa pemeriksaan.

## 4. File system dan environment

1. Aktifkan Directory Privacy pada domain.
2. Backup snapshot `<APP_ROOT>` dan `<PUBLIC_ROOT>` aktif sebelum mengubah apa pun. Pertahankan file cPanel serta directive autentikasi selama gate privat.
3. Unggah artifact terverifikasi. Ekstrak `rumahweb-application.zip` ke lokasi di luar web root dan `rumahweb-public-html.zip` hanya ke `<PUBLIC_ROOT>`.
4. Pastikan `<PUBLIC_ROOT>/index.php` tetap menunjuk ke `<APP_ROOT>` yang baru.
5. Atur PHP 8.4 untuk web dan CLI. Target minimum: `upload_max_filesize=8M`, `post_max_size=32M`, `memory_limit=256M`, `max_execution_time=120`, `max_input_time=120`, `display_errors=Off`, serta `log_errors=On`.
6. Salin daftar key dari `.deploy/rumahweb/env.template`, lalu buat `.env` langsung di `<APP_ROOT>` (bukan di `<PUBLIC_ROOT>`). Gunakan nilai production non-rahasia berikut sebagai minimum:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://tnypartners.com
APP_TIMEZONE=Asia/Jakarta
APP_LOCALE=id
SESSION_DRIVER=database
QUEUE_CONNECTION=database
PRIVACY_POLICY_READY=true
PRIVACY_POLICY_VERSION=v1.0
PRIVACY_POLICY_EFFECTIVE_DATE=2026-09-08
```

7. Isi koneksi database production, kredensial Azure production, API key Resend production, serta kontak firma yang benar langsung pada `.env`. Jangan mengirim nilainya melalui chat atau menyimpannya dalam source.

## 5. Cutover privat

Jangan menebak lokasi PHP CLI. Tentukan binary PHP 8.4 yang tersedia di cPanel, lalu gunakan placeholder `<PHP84_CLI>`, `<APP_ROOT>`, dan `<PRIVATE_LOG_DIR>` pada perintah berikut. Simpan output pada log privat.

```text
<PHP84_CLI> <APP_ROOT>/artisan key:generate --force
<PHP84_CLI> <APP_ROOT>/artisan app:production-readiness --phase=bootstrap --external --storage-roundtrip
<PHP84_CLI> <APP_ROOT>/artisan migrate --force
<PHP84_CLI> <APP_ROOT>/artisan db:seed --class=DatabaseSeeder --force
<PHP84_CLI> <APP_ROOT>/artisan optimize
```

`--storage-roundtrip` hanya membuat, membaca, dan menghapus satu object acak pada disk readiness. Perintah tidak boleh berhasil jika readiness prefix bertumpang tindih dengan prefix dokumen.

Login sebagai Admin bootstrap melalui akses yang masih diproteksi Directory Privacy. Setelah login pertama berhasil, hapus `ADMIN_DEFAULT_PASSWORD` dari `.env`, kemudian jalankan:

```text
<PHP84_CLI> <APP_ROOT>/artisan optimize
<PHP84_CLI> <APP_ROOT>/artisan app:production-readiness --phase=runtime --external
```

Pastikan runtime gate lulus setelah privacy v1.0 aktif dan Admin bootstrap tersedia.

## 6. Scheduler, queue, dan health

Pasang Cron permanen setiap menit (dengan output ke file privat dan rotasi yang sesuai fasilitas cPanel):

```text
* * * * * <PHP84_CLI> <APP_ROOT>/artisan schedule:run >> <PRIVATE_LOG_DIR>/scheduler.log 2>&1
```

Verifikasi scheduler menjalankan pemroses queue database aplikasi, lalu cek bahwa `failed_jobs` bernilai nol. Jangan menghapus cron permanen sebagai bagian pembersihan cron uji.

Sebelum go-live, periksa `/up`, log aplikasi/scheduler, koneksi database, pengiriman email uji yang disetujui, serta akses blob readonly dan readiness round-trip.

## 7. Go-live dan observasi tujuh hari

Lepaskan Directory Privacy hanya setelah semua gate sebelumnya lulus. Setelah sertifikat valid, aktifkan Force HTTPS, gunakan apex sebagai URL kanonis, dan arahkan `www` ke apex.

Dari jaringan eksternal/incognito, verifikasi:

- `/up`, halaman publik, registrasi, login, dan halaman error;
- HTTPS, HSTS, serta CSP;
- dokumen Azure tidak dapat dibuka tanpa autentikasi dan otorisasi;
- queue sehat dan `failed_jobs` tetap nol.

Selama tujuh hari pertama, periksa setiap hari log aplikasi dan scheduler, `failed_jobs`, status Resend, kapasitas cPanel, serta hasil backup harian.

## 8. Rollback

Jika gate atau smoke test gagal, aktifkan kembali Directory Privacy, pulihkan `<APP_ROOT>` dan `<PUBLIC_ROOT>` dari snapshot pra-cutover, lalu pulihkan **hanya database production** dari backup production terbaru. Jangan mengambil database atau object rehearsal sebagai sumber rollback. Cleanup/arsip rehearsal diputuskan terpisah setelah release stabil.
