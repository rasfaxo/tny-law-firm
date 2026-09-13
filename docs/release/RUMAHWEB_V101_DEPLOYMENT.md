# Panduan Pembaruan Production Rumahweb v1.0.1

Panduan ini khusus memperbarui production aktif dari `v1.0.0` ke `v1.0.1` melalui cPanel tanpa SSH. Rilis ini tidak memiliki migration baru dan tidak memerlukan reset, import, seed, atau perubahan isi database.

## 1. File yang digunakan

Gunakan empat file dari satu hasil build yang sama:

- `rumahweb-application.zip` untuk `/home/tnym6311/tny-law-firm`;
- `rumahweb-public-html.zip` untuk file publik di `/home/tnym6311/public_html`;
- `rumahweb-manifest.json` sebagai identitas build;
- `rumahweb-sha256.txt` untuk verifikasi checksum sebelum upload.

Jangan mengunggah `rumahweb-manifest.json`, checksum, atau panduan ini ke `public_html`. Jangan mengubah atau mengganti `.env` production dengan template.

## 2. Backup dan mode privat

1. Aktifkan kembali **Directory Privacy** untuk `tnypartners.com`. Pastikan akses dari jendela incognito meminta username/password cPanel sebelum melanjutkan.
2. Di File Manager, buat folder `/home/tnym6311/release-staging/v1.0.1` dan `/home/tnym6311/release-backups/v1.0.1`.
3. Compress folder `/home/tnym6311/tny-law-firm` ke `/home/tnym6311/release-backups/v1.0.1/tny-law-firm-v1.0.0.zip`.
4. Compress isi `/home/tnym6311/public_html` ke `/home/tnym6311/release-backups/v1.0.1/public-html-v1.0.0.zip`.
5. Export database production melalui phpMyAdmin ke file lokal sebagai backup pra-deployment. Jangan mengimpor kembali file ini kecuali rollback database memang diperlukan.

Jangan hapus `.well-known`, `cgi-bin`, `.user.ini`, `php.ini`, atau blok **Password Protected Directories** pada `.htaccess`.

## 3. Siapkan aplikasi baru di folder staging

1. Upload `rumahweb-application.zip` ke `/home/tnym6311/release-staging/v1.0.1`.
2. Extract ZIP tersebut di folder itu. Hasil yang benar adalah `/home/tnym6311/release-staging/v1.0.1/tny-law-firm` dan di dalamnya langsung ada `artisan`, `app`, `bootstrap`, `config`, `lang`, `public`, `resources`, `routes`, `storage`, dan `vendor`.
3. Copy file `/home/tnym6311/tny-law-firm/.env` yang sedang aktif ke `/home/tnym6311/release-staging/v1.0.1/tny-law-firm/.env`.
4. Jangan membuat `APP_KEY` baru dan jangan mengubah kredensial database, Azure, atau Resend.
5. Pastikan permission folder `storage` dan `bootstrap/cache` tetap writable oleh akun hosting.

## 4. Tukar folder aplikasi

1. Rename `/home/tnym6311/tny-law-firm` menjadi `/home/tnym6311/tny-law-firm.v1.0.0.rollback`.
2. Move `/home/tnym6311/release-staging/v1.0.1/tny-law-firm` ke `/home/tnym6311/tny-law-firm`.
3. Pastikan `/home/tnym6311/tny-law-firm/.env` ada dan ukuran file tidak nol.
4. Jangan menghapus folder rollback sampai smoke test selesai.

## 5. Perbarui file publik

1. Upload dan extract `rumahweb-public-html.zip` ke `/home/tnym6311/release-staging/v1.0.1/public-html`, bukan langsung ke `public_html`.
2. Di `/home/tnym6311/public_html`, rename folder `build` menjadi `build.v1.0.0.rollback`.
3. Move folder `build` dari staging ke `/home/tnym6311/public_html/build`.
4. Rename folder `brand` lama menjadi `brand.v1.0.0.rollback`, lalu move folder `brand` dari staging ke `public_html`.
5. Replace `favicon.ico`, `index.php`, dan `robots.txt` memakai versi dari staging.
6. Pertahankan `.htaccess` production yang sedang aktif karena file tersebut berisi aturan Laravel sekaligus blok Directory Privacy cPanel. Jangan replace `.htaccess` dengan file staging.
7. Pertahankan `.well-known`, `cgi-bin`, `.user.ini`, dan `php.ini` yang sudah ada.

## 6. Bersihkan dan bangun cache melalui Cron sementara

Gunakan path PHP 8.4 yang sama dengan deployment `v1.0.0` yang sudah berhasil. Pada menu **Cron Jobs**, buat satu job sementara setiap menit dengan command berikut dalam satu baris. Ganti `<PHP84_CLI>` hanya dengan path PHP 8.4 yang sebelumnya berhasil digunakan; jangan menebak path baru.

```text
cd /home/tnym6311/tny-law-firm && <PHP84_CLI> artisan optimize:clear && <PHP84_CLI> artisan optimize && <PHP84_CLI> artisan app:production-readiness --phase=runtime --external > /home/tnym6311/logs/deploy-v1.0.1.log 2>&1
```

Tunggu satu kali eksekusi, lalu segera hapus Cron sementara tersebut. Buka `/home/tnym6311/logs/deploy-v1.0.1.log` dan pastikan:

- `optimize:clear` dan `optimize` tidak menghasilkan error;
- seluruh pemeriksaan runtime berstatus `INFO`;
- baris terakhir menyatakan seluruh pemeriksaan production readiness fase runtime lulus.

Rilis ini tidak menjalankan `migrate`, `db:seed`, `migrate:fresh`, `db:wipe`, atau perintah reset database apa pun. Cron scheduler permanen aplikasi tetap dipertahankan.

## 7. Smoke test privat per role

Selama Directory Privacy masih aktif, lakukan pemeriksaan berikut:

1. Publik: buka `https://tnypartners.com`, pastikan HTTPS valid, CSS/font/logo tampil, dan `/up` memberi HTTP 200.
2. Klien: login, buka detail pengajuan dan detail booking; pastikan hanya ada satu tombol Request Reschedule, keputusan reschedule terakhir tampil, serta alasan penolakan terlihat jika statusnya ditolak.
3. Staf Legal: buka form verifikasi pada desktop dan mode mobile browser; uji `Berkas Tidak Lengkap` dengan minimal satu dokumen `Perlu Perbaikan` dan catatan, lalu pastikan berhasil tersimpan.
4. Klien: pastikan catatan umum yang baru dimasukkan tampil persis dan bukan fallback `Berkas diperiksa.`.
5. Admin: buka tambah jadwal dan detail permintaan reschedule; pastikan alert lama sudah hilang dan slot yang sudah dimulai tidak tersedia untuk persetujuan.
6. Pastikan `failed_jobs` tetap `0`, email keputusan masuk ke alamat uji yang disetujui, dan dokumen tetap memerlukan otorisasi.

Gunakan hanya akun dan data uji anonim. Hapus data uji secara selektif setelah pengujian selesai; jangan reset seluruh database.

## 8. Buka kembali situs atau rollback

Jika semua pemeriksaan lulus, nonaktifkan Directory Privacy dan verifikasi ulang halaman publik dari incognito. Setelah observasi awal selesai, arsipkan folder rollback di luar web root.

Jika ada kegagalan:

1. pertahankan Directory Privacy;
2. rename aplikasi v1.0.1 yang gagal menjadi `tny-law-firm.v1.0.1.failed`;
3. rename `tny-law-firm.v1.0.0.rollback` kembali menjadi `tny-law-firm`;
4. hapus atau rename `public_html/build` v1.0.1, lalu kembalikan `build.v1.0.0.rollback` menjadi `build`;
5. hapus atau rename `public_html/brand` v1.0.1, lalu kembalikan `brand.v1.0.0.rollback` menjadi `brand`;
6. pulihkan file publik lain dari `public-html-v1.0.0.zip` bila diperlukan;
7. jalankan Cron sementara `optimize:clear` dan `optimize` pada aplikasi v1.0.0;
8. buka situs hanya setelah smoke test rollback lulus.

Database tidak perlu dipulihkan untuk rollback kode ini karena v1.0.1 tidak mengubah skema maupun data deployment.
