# Release Gate Rumahweb Small Tanpa SSH

Status dokumen: **prosedur rehearsal pra-rilis**. Dokumen ini tidak memberi izin untuk membuka aplikasi ke publik.

## Batas keamanan

- `tnypartners.com` harus dilindungi dengan **Directory Privacy** selama rehearsal.
- Aplikasi Laravel ditempatkan pada folder `tny-law-firm` di luar `public_html`.
- Hanya isi paket `rumahweb-public-html.zip` yang diekstrak ke `public_html`.
- Jangan unggah `.env`, backup database, credential, log privat, atau dokumen perkara ke `public_html`.
- Jangan menyalin secret ke issue, workflow, laporan, atau percakapan.
- Pipeline Azure staging tetap terpisah dan tidak diubah oleh prosedur ini.

## 1. Artifact

Jalankan workflow GitHub Actions **Build Rumahweb Manual Artifact**. Unduh artifact dan verifikasi checksum dalam `rumahweb-sha256.txt` sebelum mengunggah:

- `rumahweb-application.zip`: diekstrak di luar `public_html` sehingga menghasilkan folder `tny-law-firm`.
- `rumahweb-public-html.zip`: diekstrak ke `public_html`.
- `rumahweb-manifest.json`: commit dan waktu build tanpa secret.

Sebelum ekstraksi paket public, backup isi `public_html`. Pertahankan `php.ini`, direktori yang dikelola cPanel, serta directive Directory Privacy. Bila cPanel menambahkan directive autentikasi ke `.htaccess`, gabungkan bagian rewrite Laravel tanpa menghapus directive tersebut.

## 2. PHP dan SSL

Target Select PHP Version:

| Opsi | Nilai target |
|---|---:|
| PHP | 8.4 |
| `upload_max_filesize` | `8M` |
| `post_max_size` | `32M` |
| `memory_limit` | `256M` |
| `max_execution_time` | `120` |
| `max_input_time` | `120` |
| `display_errors` | Off |
| `log_errors` | On |

Pastikan SSL mencakup apex dan `www`. Aktifkan Force HTTPS hanya setelah sertifikat valid. Konfigurasi awal proxy adalah `TRUSTED_PROXIES=none`; ubah hanya bila hasil pemeriksaan request membuktikan adanya reverse proxy dan Rumahweb memberikan alamat proxy eksplisit.

## 3. Database dan environment

1. Buat database MariaDB serta user khusus aplikasi melalui cPanel.
2. Berikan user tersebut hak hanya pada database aplikasi.
3. Gunakan `.deploy/rumahweb/env.template` sebagai daftar key, lalu buat `.env` langsung di folder aplikasi di luar web root dan batasi permission semaksimal yang didukung cPanel.
4. Gunakan `APP_URL=https://tnypartners.com`, database session/queue, Resend, serta Azure Blob private.
5. Gunakan prefix Azure terisolasi yang memuat `release-gate`, misalnya `release-gate/rumahweb/rehearsal`.
6. Isi `ADMIN_DEFAULT_PASSWORD` hanya untuk bootstrap pertama. Hapus setelah Admin berhasil dibuat.
7. Tetapkan `PRIVACY_POLICY_READY=false` sampai firma menyetujui naskah, versi, dan tanggal berlaku.

## 4. Cron sementara

Jangan menebak binary PHP. Buat Cron sementara yang menulis versi PHP dan daftar extension ke log privat di folder aplikasi. Gunakan binary hanya jika menghasilkan PHP 8.4 dan extension yang diperlukan.

Dengan placeholder `<PHP84_CLI>` dan `<APP_ROOT>` yang diisi langsung di cPanel, jalankan satu per satu dan periksa exit/output sebelum melanjutkan:

```text
<PHP84_CLI> <APP_ROOT>/artisan key:generate --force
<PHP84_CLI> <APP_ROOT>/artisan app:production-readiness --phase=bootstrap
<PHP84_CLI> <APP_ROOT>/artisan migrate --force
<PHP84_CLI> <APP_ROOT>/artisan db:seed --class=DatabaseSeeder --force
<PHP84_CLI> <APP_ROOT>/artisan optimize
```

Setelah seed, hapus `ADMIN_DEFAULT_PASSWORD`, jalankan ulang `artisan optimize`, dan jangan menjalankan runtime gate sebelum kebijakan privasi disahkan.

Hapus seluruh Cron sementara setelah hasilnya diperiksa. Cron permanen menjalankan scheduler setiap menit:

```text
<PHP84_CLI> <APP_ROOT>/artisan schedule:run
```

Alihkan output Cron permanen sesuai fasilitas cPanel agar tidak menghasilkan email/log tak terbatas. Kegagalan queue tetap diperiksa melalui `failed_jobs` dan log aplikasi.

## 5. Resend

- From: `no-reply@tnypartners.com`.
- Reply-To: `tny.partnerhukum@gmail.com`.
- Gunakan API key sending-only.
- Tambahkan hanya record DNS yang ditampilkan oleh Resend.
- Jangan membuat dua record SPF untuk hostname yang sama. Record SPF Rumahweb yang sudah ada tidak boleh ditimpa tanpa analisis.
- Pertahankan satu DMARC `p=none` selama observasi awal.
- Nonaktifkan open dan click tracking.

Jalankan `artisan app:production-readiness --phase=bootstrap --external` setelah DNS berstatus verified. Kirim email uji hanya lewat alur aplikasi kepada penerima yang disetujui.

## 6. Azure Blob

Sebelum mengubah Storage Account, tampilkan konfigurasi lama dan perubahan yang diajukan untuk konfirmasi. Target:

- container private;
- credential dengan hak minimum pada container;
- blob soft delete 14 hari;
- container soft delete 14 hari;
- versioning aktif;
- lifecycle previous versions 90 hari.

Storage round-trip hanya boleh dijalankan dengan prefix yang memuat `release-gate`:

```text
<PHP84_CLI> <APP_ROOT>/artisan app:production-readiness --phase=bootstrap --external --storage-roundtrip
```

Perintah membuat, membaca, lalu menghapus object acak miliknya sendiri. Pemulihan soft-deleted object tetap diverifikasi melalui Azure Portal.

## 7. Backup, pemeriksaan, dan penutupan gate

- Export database melalui phpMyAdmin, lalu buktikan restore ke database uji yang berbeda.
- Verifikasi backup File Manager dapat dipulihkan.
- Uji upload satu file 5 MB dan lima file dengan total mendekati 25 MB.
- Uji database session, scheduler, queue, semua email, otorisasi dokumen, `/up`, HTTPS/HSTS/CSP, error page, mobile, desktop, dan print A4.
- Catat CPU, RAM, entry process, I/O, inode, serta error limit setelah workload representatif.
- Jalankan runtime gate hanya setelah privacy disahkan:

```text
<PHP84_CLI> <APP_ROOT>/artisan app:production-readiness --phase=runtime --external
```

Directory Privacy tidak boleh dibuka bila runtime gate atau restore drill belum lulus.
