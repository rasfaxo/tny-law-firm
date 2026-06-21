# PROJECT_CONTEXT.md

## Judul Project

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

## Deskripsi Project

Project ini adalah aplikasi web berbasis Laravel yang digunakan untuk membantu proses pra-pendaftaran perkara pada TNY Law Firm.

Sistem ini memungkinkan Klien melakukan pra-pendaftaran perkara secara online, mengunggah dokumen pendukung, memantau status pengajuan, melihat catatan verifikasi, mengunggah ulang dokumen jika terdapat catatan perbaikan, dan memilih jadwal konsultasi apabila berkas sudah memenuhi syarat.

Sistem juga membantu Staf Legal dalam melakukan verifikasi berkas perkara dan membantu Admin dalam mengelola data pengguna, kategori perkara, jadwal konsultasi, serta laporan pra-pendaftaran.

## Tujuan Sistem

Tujuan utama sistem ini adalah:

1. Mempermudah Klien dalam melakukan pra-pendaftaran perkara secara online.
2. Membantu TNY Law Firm mengelola data pra-pendaftaran perkara secara lebih terstruktur.
3. Membantu Staf Legal melakukan pemeriksaan dan verifikasi berkas perkara.
4. Memudahkan Klien memantau status pengajuan perkara.
5. Menyediakan fasilitas unggah ulang dokumen apabila berkas belum memenuhi syarat.
6. Memudahkan Klien memilih jadwal konsultasi setelah berkas dinyatakan lengkap.
7. Membantu Admin melihat dan mencetak laporan pra-pendaftaran perkara berdasarkan data yang tersimpan di sistem.

## Ruang Lingkup Sistem

Sistem mencakup:

1. Registrasi dan login pengguna.
2. Pengelolaan profil Klien.
3. Pengajuan pra-pendaftaran perkara.
4. Upload dokumen pendukung perkara.
5. Verifikasi berkas oleh Staf Legal.
6. Pemberian catatan verifikasi umum atau per dokumen.
7. Pemantauan status pengajuan oleh Klien.
8. Unggah ulang dokumen oleh Klien jika ada catatan perbaikan.
9. Pengelolaan kategori perkara oleh Admin.
10. Pengelolaan akun pengguna oleh Admin.
11. Pengelolaan slot jadwal konsultasi oleh Admin.
12. Pemilihan jadwal konsultasi oleh Klien.
13. Melihat dan mencetak laporan pra-pendaftaran melalui tampilan tabel dan fitur print browser.

## Batasan Sistem

Sistem tidak mencakup:

1. Integrasi dengan sistem e-Court.
2. Pembayaran online.
3. Fitur email otomatis pada fase awal.
4. Tanda tangan digital.
5. Chat realtime.
6. Video conference.
7. Sistem manajemen perkara lengkap setelah konsultasi.
8. Tabel khusus laporan.
9. Penyimpanan file dokumen langsung di database.

Laporan dibuat dari query dan rekap data, bukan dari tabel laporan.

## Aktor Sistem

### 1. Klien

Klien adalah pengguna yang melakukan pra-pendaftaran perkara melalui sistem.

Klien dapat:

1. Registrasi akun.
2. Login.
3. Mengelola profil.
4. Mengajukan pra-pendaftaran perkara.
5. Mengunggah dokumen pendukung.
6. Melihat status pengajuan.
7. Melihat catatan verifikasi.
8. Mengunggah ulang dokumen jika terdapat catatan perbaikan.
9. Memilih jadwal konsultasi jika status berkas sudah lengkap.

### 2. Admin

Admin adalah pengguna yang mengelola data utama sistem.

Admin dapat:

1. Login.
2. Melihat dashboard statistik.
3. Mengelola data pengguna.
4. Membuat akun Staf Legal.
5. Mengelola kategori perkara.
6. Melihat data pra-pendaftaran perkara.
7. Mengelola slot jadwal konsultasi.
8. Melihat dan mencetak laporan pra-pendaftaran melalui tampilan tabel dan fitur print browser.

### 3. Staf Legal

Staf Legal adalah pengguna yang bertugas memeriksa dan memverifikasi berkas perkara dari Klien.

Staf Legal dapat:

1. Login.
2. Melihat daftar pengajuan pra-pendaftaran.
3. Melihat detail pengajuan perkara.
4. Melihat dokumen pendukung.
5. Memberikan status verifikasi.
6. Memberikan catatan umum.
7. Memberikan catatan per dokumen.
8. Memperbarui status pengajuan berdasarkan hasil verifikasi.

## Alur Umum Sistem

Alur umum sistem adalah:

1. Klien melakukan registrasi akun.
2. Klien login ke sistem.
3. Klien mengisi atau melengkapi profil.
4. Klien mengajukan pra-pendaftaran perkara.
5. Klien mengunggah dokumen pendukung.
6. Sistem menyimpan pengajuan dengan status awal `menunggu_verifikasi`.
7. Sistem mencatat status awal pengajuan ke tabel `riwayat_status`.
8. Staf Legal memeriksa detail pengajuan dan dokumen.
9. Staf Legal menentukan apakah berkas lengkap atau tidak lengkap.
10. Jika berkas tidak lengkap, Staf Legal memberikan catatan verifikasi.
11. Klien melihat catatan dan mengunggah ulang dokumen.
12. Staf Legal melakukan verifikasi ulang.
13. Jika berkas lengkap, Klien dapat memilih jadwal konsultasi.
14. Satu pengajuan perkara hanya boleh memiliki satu booking konsultasi aktif.
15. Admin dapat melihat dan mencetak laporan pra-pendaftaran melalui tampilan tabel dan fitur print browser.

## Tech Stack Final

Project menggunakan stack berikut:

* Backend: Laravel
* Frontend: Blade + Tailwind CSS
* Authentication: Laravel Breeze
* Database: MySQL
* File Storage: Laravel Storage
* Version Control: Git dan GitHub
* Editor: Zed
* Deployment Target: Cloud VPS
* Web Server: Apache atau Nginx
* Runtime: PHP 8.x / PHP-FPM

## Keputusan Final Project

Keputusan final yang wajib diikuti:

1. Project menggunakan Laravel.
2. Authentication menggunakan Laravel Breeze.
3. Frontend menggunakan Blade dan Tailwind CSS.
4. Database menggunakan MySQL.
5. Role disimpan menggunakan slug lowercase.
6. Status disimpan menggunakan slug lowercase.
7. Label status untuk tampilan dibuat di level aplikasi.
8. File dokumen disimpan menggunakan Laravel Storage.
9. Database hanya menyimpan metadata file dan `file_path`.
10. Laporan dibuat dari query dan rekap data.
11. Tidak membuat tabel `laporan`.
12. Tidak menggunakan database ENUM.
13. Tidak menggunakan fitur email pada fase awal.
14. Tidak menggunakan integrasi e-Court.
15. Tidak menggunakan fitur pembayaran.
16. Format dokumen yang diperbolehkan adalah PDF, JPG, JPEG, dan PNG.
17. Ukuran maksimal dokumen adalah 5 MB per file.
18. Lokasi penyimpanan dokumen adalah `storage/app/public/dokumen-perkara`.
19. Dokumen lama tidak boleh ditimpa saat Klien mengunggah ulang dokumen.
20. Satu pengajuan perkara hanya boleh memiliki satu booking konsultasi aktif.
21. Klien hanya dapat memilih jadwal konsultasi jika status pengajuan adalah `berkas_lengkap`.
22. Status awal pengajuan baru adalah `menunggu_verifikasi`.
23. Setiap perubahan status pengajuan harus dicatat pada `riwayat_status`.

## Role Final

Role yang digunakan:

| Slug       | Label      |
| ---------- | ---------- |
| klien      | Klien      |
| admin      | Admin      |
| staf_legal | Staf Legal |

Role disimpan di tabel `users` pada kolom `role`.

## Aturan Utama Status

Status disimpan sebagai slug lowercase di database.

Label status ditampilkan pada UI dalam bentuk teks yang mudah dibaca.

Status awal pengajuan baru adalah `menunggu_verifikasi`.

Setiap perubahan status pengajuan wajib dicatat pada tabel `riwayat_status`.

Detail status dan aturan transisi status ditulis pada:

```text
docs/STATUS_RULES.md
```

## Aturan Utama Database

Rancangan database harus mengikuti:

```text
docs/DATABASE_PLAN.md
```

Rancangan relasi model Laravel harus mengikuti:

```text
docs/MODEL_RELATION_PLAN.md
```

AI agent tidak boleh membuat struktur database baru berdasarkan asumsi sendiri.

## Aturan Utama Validasi

Aturan validasi input harus mengikuti:

```text
docs/VALIDATION_RULES.md
```

Validasi wajib diterapkan untuk:

1. Registrasi.
2. Login.
3. Profil Klien.
4. Pra-pendaftaran perkara.
5. Upload dokumen.
6. Verifikasi berkas.
7. Catatan verifikasi.
8. Jadwal konsultasi.
9. Booking jadwal.
10. Filter laporan.

## Aturan Utama Keamanan

Aturan keamanan harus mengikuti:

```text
docs/SECURITY_RULES.md
```

Keamanan utama yang wajib diperhatikan:

1. Role-based access control.
2. Ownership check untuk data Klien.
3. Proteksi akses dokumen perkara.
4. Validasi file upload.
5. Pencegahan akses lintas role.
6. Pencegahan perubahan data tanpa otorisasi.

## Output Utama Sistem

Output utama sistem meliputi:

1. Data pengguna.
2. Data profil Klien.
3. Data kategori perkara.
4. Data pra-pendaftaran perkara.
5. Data dokumen perkara.
6. Data verifikasi berkas.
7. Data catatan verifikasi.
8. Data riwayat status.
9. Data jadwal konsultasi.
10. Data booking konsultasi.
11. Laporan pra-pendaftaran perkara.

## Laporan

Laporan pra-pendaftaran perkara dibuat berdasarkan data yang tersimpan di sistem.

Laporan dapat difilter berdasarkan:

1. Tanggal.
2. Status pengajuan.
3. Kategori perkara.

Laporan ditampilkan dalam bentuk tabel dan dapat dicetak menggunakan fitur print browser.

Tidak ada tabel khusus `laporan`.

## Catatan untuk AI Agent

AI agent wajib memahami bahwa project ini adalah implementasi skripsi, sehingga:

1. Fitur harus sesuai ruang lingkup.
2. Struktur database harus sesuai rancangan final.
3. Nama tabel dan kolom tidak boleh diubah tanpa persetujuan.
4. Fitur tambahan di luar skripsi tidak boleh dibuat tanpa persetujuan.
5. Implementasi harus bertahap dan mudah diuji.
6. Setiap perubahan harus menjaga konsistensi dengan diagram dan dokumen rancangan.
