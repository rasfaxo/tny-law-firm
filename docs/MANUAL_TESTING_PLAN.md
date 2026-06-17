# MANUAL_TESTING_PLAN.md

## Purpose

Dokumen ini berisi rencana pengujian manual untuk project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Dokumen ini digunakan untuk memastikan setiap fitur berjalan sesuai rancangan, role access, validasi, keamanan, alur status, dan kebutuhan skripsi.

AI agent wajib menggunakan dokumen ini sebagai acuan saat memberikan langkah testing setelah implementasi fitur.

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

Jika ada konflik aturan status, role, validasi, atau route, ikuti dokumen sumber yang lebih spesifik.

---

## General Manual Testing Rules

Aturan umum testing:

1. Testing dilakukan per fitur, bukan semua fitur sekaligus.
2. Setiap fitur harus diuji dari sisi alur normal dan alur gagal.
3. Setiap fitur role-based harus diuji dengan role yang benar dan role yang salah.
4. Setiap fitur Klien harus diuji ownership check-nya.
5. Setiap fitur dokumen harus diuji authorization check-nya.
6. Setiap proses multi-tabel harus diuji hasil perubahan datanya.
7. Setiap perubahan `status_pengajuan` harus menghasilkan record pada `riwayat_status`.
8. Testing dilakukan setelah migration, seeder, route, controller, request validation, model, dan view tersedia.
9. Jika terjadi error, perbaiki dengan perubahan terkecil yang aman.
10. Setelah fitur lolos testing manual, fitur boleh dikunci dengan commit Git.

---

## Test Environment

Environment testing lokal:

| Item                | Value                                |
| ------------------- | ------------------------------------ |
| Framework           | Laravel                              |
| Database            | MySQL                                |
| Frontend            | Blade + Tailwind CSS                 |
| Authentication      | Laravel Breeze                       |
| Local Server        | `php artisan serve`                  |
| Frontend Dev Server | `npm run dev`                        |
| Storage             | Laravel Storage                      |
| Upload Folder       | `storage/app/public/dokumen-perkara` |

Command umum:

```bash
php artisan serve
npm run dev
```

Command pendukung testing:

```bash
php artisan storage:link
php artisan route:list
php artisan migrate:status
```

Jika menggunakan build frontend:

```bash
npm run build
```

Catatan:

```text
php artisan migrate hanya dijalankan setelah file migration direview dan disetujui.
Jangan menggunakan php artisan migrate:fresh sebagai prosedur testing default.
```

---

## Test Accounts

Gunakan minimal tiga akun untuk testing:

| Role       | Email Contoh            | Keterangan                       |
| ---------- | ----------------------- | -------------------------------- |
| Admin      | `admin@example.com`     | Dibuat melalui seeder            |
| Staf Legal | `staflegal@example.com` | Dibuat oleh Admin atau seeder    |
| Klien      | `klien@example.com`     | Dibuat melalui registrasi publik |

Aturan:

1. Admin awal dapat dibuat melalui seeder.
2. Staf Legal dapat dibuat oleh Admin atau seeder sesuai fase implementasi.
3. Klien dibuat melalui form registrasi publik.
4. Pastikan setiap akun memiliki `status_akun = aktif`.
5. Akun dengan `status_akun = nonaktif` harus gagal mengakses sistem.

---

# Testing Area 1 - Authentication

## 1.1 Register Klien

Tujuan:

Memastikan registrasi publik hanya membuat akun Klien.

Langkah testing:

1. Buka halaman register.
2. Isi nama, email, password, konfirmasi password, dan nomor telepon jika tersedia.
3. Submit form.
4. Cek user berhasil dibuat.
5. Cek user diarahkan ke dashboard Klien atau halaman sesuai konfigurasi.
6. Cek database tabel `users`.

Expected result:

1. Akun berhasil dibuat.
2. Field `role` otomatis bernilai `klien`.
3. Field `status_akun` otomatis bernilai `aktif`.
4. Password tersimpan dalam bentuk hash.
5. Form tidak menerima input role dari user.

Negative test:

1. Email kosong harus gagal.
2. Email tidak valid harus gagal.
3. Email duplikat harus gagal.
4. Password kurang dari minimum harus gagal.
5. Konfirmasi password tidak sama harus gagal.
6. User tidak bisa mengatur role melalui form publik.

---

## 1.2 Login

Tujuan:

Memastikan user dapat login sesuai akun dan diarahkan berdasarkan role.

Langkah testing:

1. Login sebagai Klien.
2. Pastikan diarahkan ke dashboard Klien.
3. Logout.
4. Login sebagai Staf Legal.
5. Pastikan diarahkan ke dashboard Staf Legal.
6. Logout.
7. Login sebagai Admin.
8. Pastikan diarahkan ke dashboard Admin.

Expected result:

1. Klien masuk ke `klien.dashboard`.
2. Staf Legal masuk ke `staf-legal.dashboard`.
3. Admin masuk ke `admin.dashboard`.
4. Role tidak valid tidak diberi akses.

Negative test:

1. Email salah harus gagal.
2. Password salah harus gagal.
3. Akun `nonaktif` tidak boleh login.
4. User tidak boleh mengakses dashboard role lain melalui URL langsung.

---

## 1.3 Logout

Tujuan:

Memastikan logout berjalan aman.

Langkah testing:

1. Login sebagai salah satu user.
2. Klik logout.
3. Pastikan session keluar.
4. Coba akses dashboard sebelumnya melalui URL.

Expected result:

1. User berhasil logout.
2. Session di-invalidasi.
3. Dashboard tidak bisa diakses tanpa login.
4. User diarahkan ke login.

---

# Testing Area 2 - Role Access

## 2.1 Akses Route Berdasarkan Role

Tujuan:

Memastikan route role-based terlindungi middleware.

Langkah testing:

1. Login sebagai Klien.
2. Coba akses URL Admin.
3. Coba akses URL Staf Legal.
4. Login sebagai Staf Legal.
5. Coba akses URL Klien.
6. Coba akses URL Admin.
7. Login sebagai Admin.
8. Coba akses URL Klien.
9. Coba akses URL Staf Legal.

Expected result:

1. Klien hanya dapat mengakses route Klien.
2. Staf Legal hanya dapat mengakses route Staf Legal.
3. Admin hanya dapat mengakses route Admin.
4. Akses tidak sah ditolak dengan redirect atau error sesuai kebijakan aplikasi.
5. Menu hidden bukan satu-satunya proteksi.

---

# Testing Area 3 - Kelola Pengguna Admin

## 3.1 Admin Membuat Akun Staf Legal

Tujuan:

Memastikan Admin dapat membuat akun Staf Legal sesuai aturan role dan validasi user.

Langkah testing:

1. Login sebagai Admin.
2. Buka halaman kelola pengguna.
3. Klik tambah user.
4. Isi nama, email, password, konfirmasi password, nomor telepon jika tersedia, dan status akun.
5. Simpan data.
6. Cek database tabel `users`.

Expected result:

1. Akun Staf Legal berhasil dibuat.
2. Field `role` bernilai `staf_legal`.
3. Field `status_akun` sesuai input yang valid.
4. Email bersifat unique.
5. Password tersimpan dalam bentuk hash.
6. Akun Staf Legal dapat login jika `status_akun = aktif`.

Negative test:

1. Role selain Admin tidak boleh mengakses kelola pengguna.
2. Email kosong harus gagal.
3. Email tidak valid harus gagal.
4. Email duplikat harus gagal.
5. Password kurang dari minimum harus gagal.
6. Konfirmasi password tidak sama harus gagal.
7. Admin tidak boleh membuat role di luar `klien`, `admin`, dan `staf_legal`.
8. Form publik tidak boleh digunakan untuk membuat akun Staf Legal.

---

## 3.2 Admin Mengubah Data User

Tujuan:

Memastikan Admin dapat mengubah data user tanpa merusak aturan akun.

Langkah testing:

1. Login sebagai Admin.
2. Buka daftar pengguna.
3. Pilih salah satu user.
4. Edit nama, email, nomor telepon, atau status akun.
5. Simpan perubahan.
6. Cek database tabel `users`.

Expected result:

1. Data user berhasil diperbarui.
2. Email tetap unique.
3. Role tidak berubah sembarangan.
4. Password tidak berubah melalui form edit umum.
5. Data tervalidasi sesuai `VALIDATION_RULES.md`.

Negative test:

1. Role selain Admin tidak boleh mengubah data user.
2. Email duplikat harus gagal.
3. Status akun selain `aktif` atau `nonaktif` harus gagal.
4. Password tidak boleh berubah jika tidak melalui form khusus.
5. Admin tidak boleh membuat role baru di luar daftar role valid.

---

## 3.3 Admin Menonaktifkan Akun

Tujuan:

Memastikan akun yang dinonaktifkan tidak dapat mengakses sistem.

Langkah testing:

1. Login sebagai Admin.
2. Buka halaman kelola pengguna.
3. Pilih user aktif.
4. Ubah `status_akun` menjadi `nonaktif`.
5. Logout.
6. Coba login menggunakan akun yang dinonaktifkan.

Expected result:

1. Status akun berubah menjadi `nonaktif`.
2. Akun `nonaktif` tidak dapat login.
3. Jika user sedang memiliki session aktif, akses berikutnya harus ditolak sesuai mekanisme sistem.
4. Hanya Admin yang dapat mengubah status akun.

Negative test:

1. Role selain Admin tidak boleh menonaktifkan akun.
2. Status akun selain `aktif` atau `nonaktif` harus ditolak.
3. Akun nonaktif tidak boleh masuk ke dashboard role apa pun.

---

# Testing Area 4 - Profil Klien

## 4.1 Melihat dan Mengubah Profil

Tujuan:

Memastikan Klien dapat mengelola profil miliknya sendiri.

Langkah testing:

1. Login sebagai Klien.
2. Buka halaman profil.
3. Isi atau ubah alamat, jenis kelamin, pekerjaan, dan nomor identitas.
4. Submit form.
5. Cek data tersimpan pada `profil_klien`.

Expected result:

1. Profil berhasil disimpan.
2. `id_user` menggunakan ID user login.
3. Satu Klien hanya memiliki satu profil.
4. Perubahan profil tidak mengubah role, email, password, atau status akun.

Negative test:

1. Klien tidak boleh mengubah profil Klien lain.
2. Input terlalu panjang harus gagal.
3. `id_user` dari request tidak boleh dipercaya.

---

# Testing Area 5 - Kategori Perkara

## 5.1 Admin Membuat Kategori

Tujuan:

Memastikan Admin dapat membuat kategori perkara.

Langkah testing:

1. Login sebagai Admin.
2. Buka halaman kategori perkara.
3. Tambah kategori baru.
4. Isi nama kategori dan deskripsi.
5. Submit form.

Expected result:

1. Kategori berhasil dibuat.
2. Data masuk ke tabel `kategori_perkara`.
3. Tidak ada field `status_kategori`.
4. Tidak ada field `is_active`.

Negative test:

1. Nama kategori kosong harus gagal.
2. Role selain Admin tidak boleh mengakses fitur kategori.

---

## 5.2 Admin Mengubah Kategori

Tujuan:

Memastikan Admin dapat mengubah kategori perkara.

Langkah testing:

1. Login sebagai Admin.
2. Buka daftar kategori.
3. Edit salah satu kategori.
4. Simpan perubahan.

Expected result:

1. Data kategori berubah.
2. Perubahan tervalidasi.
3. Tidak ada kolom tambahan di luar database plan.

---

## 5.3 Admin Menghapus Kategori

Tujuan:

Memastikan kategori hanya dapat dihapus jika belum digunakan.

Langkah testing:

1. Buat kategori baru yang belum digunakan.
2. Hapus kategori tersebut.
3. Pastikan berhasil.
4. Coba hapus kategori yang sudah digunakan oleh pengajuan.

Expected result:

1. Kategori yang belum digunakan boleh dihapus.
2. Kategori yang sudah digunakan tidak boleh dihapus.
3. Riwayat pengajuan tidak rusak.

---

# Testing Area 6 - Kelola Data Pra-Pendaftaran Admin

## 6.1 Admin Melihat Seluruh Data Pra-Pendaftaran

Tujuan:

Memastikan Admin dapat melihat seluruh data pra-pendaftaran untuk kebutuhan monitoring administratif.

Langkah testing:

1. Login sebagai Admin.
2. Buka halaman `/admin/pra-pendaftaran`.
3. Lihat daftar seluruh pra-pendaftaran perkara.
4. Pastikan data dari beberapa Klien dapat tampil.
5. Cek pagination jika data banyak.

Expected result:

1. Admin dapat melihat seluruh data pra-pendaftaran.
2. Data tampil dengan informasi Klien, kategori, tanggal pengajuan, dan status pengajuan.
3. Daftar data menggunakan pagination.
4. Admin tidak masuk ke proses verifikasi milik Staf Legal dari halaman ini.
5. Role selain Admin tidak boleh mengakses halaman ini.

Negative test:

1. Klien tidak boleh mengakses halaman ini.
2. Staf Legal tidak boleh mengakses halaman ini.
3. Akses langsung melalui URL oleh role selain Admin harus ditolak.

---

## 6.2 Admin Melihat Detail Pra-Pendaftaran

Tujuan:

Memastikan Admin dapat membuka detail pra-pendaftaran untuk monitoring administratif.

Langkah testing:

1. Login sebagai Admin.
2. Buka halaman daftar pra-pendaftaran.
3. Pilih salah satu data pengajuan.
4. Buka halaman detail.
5. Lihat data Klien, kategori, kronologi, dokumen, status, verifikasi, dan booking jika ada.

Expected result:

1. Detail pra-pendaftaran tampil.
2. Admin dapat melihat data administratif yang diperlukan.
3. Admin tidak mengambil alih proses verifikasi Staf Legal.
4. Dokumen tetap dibuka melalui route/controller dengan authorization.
5. Raw `file_path` tidak ditampilkan.

Negative test:

1. Role selain Admin tidak boleh membuka detail melalui URL langsung.
2. Admin tidak boleh mengubah status pengajuan sembarangan dari halaman detail.
3. Admin tidak boleh membuat verifikasi berkas melalui fitur Admin.

---

## 6.3 Admin Filter Data Pra-Pendaftaran

Tujuan:

Memastikan filter monitoring pra-pendaftaran berjalan sesuai validasi.

Langkah testing:

1. Login sebagai Admin.
2. Buka halaman `/admin/pra-pendaftaran`.
3. Filter berdasarkan tanggal.
4. Filter berdasarkan status pengajuan.
5. Filter berdasarkan kategori perkara.
6. Filter berdasarkan keyword.
7. Cek hasil filter.

Expected result:

1. Filter tanggal berjalan.
2. Filter status berjalan menggunakan status valid dari `STATUS_RULES.md`.
3. Filter kategori mengacu ke `kategori_perkara.id_kategori`.
4. Filter keyword berjalan.
5. Pagination tetap berjalan setelah filter.
6. Query tidak menggunakan raw SQL tanpa binding.

Negative test:

1. Status tidak valid harus ditolak.
2. Kategori tidak valid harus ditolak.
3. Keyword terlalu panjang harus gagal.
4. Role selain Admin tidak boleh mengakses filter data pra-pendaftaran.

---

# Testing Area 7 - Pra-Pendaftaran Perkara Klien

## 7.1 Klien Membuat Pengajuan

Tujuan:

Memastikan Klien dapat membuat pengajuan pra-pendaftaran perkara.

Langkah testing:

1. Login sebagai Klien.
2. Buka halaman pengajuan baru.
3. Pilih kategori perkara.
4. Isi judul perkara.
5. Isi kronologi.
6. Upload dokumen pendukung.
7. Submit form.
8. Cek database.

Expected result:

1. Data masuk ke `pra_pendaftaran_perkara`.
2. `id_user` menggunakan user login.
3. `status_pengajuan` otomatis `menunggu_verifikasi`.
4. `tanggal_pengajuan` diisi oleh server.
5. Dokumen masuk ke `dokumen_perkara`.
6. `status_dokumen` awal `terkirim`.
7. Record awal masuk ke `riwayat_status`.
8. Proses create menggunakan transaction.

Negative test:

1. Tanpa kategori harus gagal.
2. Judul kosong harus gagal.
3. Kronologi kosong harus gagal.
4. Tanpa dokumen harus gagal.
5. File lebih dari 5 MB harus gagal.
6. Format selain PDF, JPG, JPEG, PNG harus gagal.
7. `status_pengajuan` dari request tidak boleh diterima.
8. `id_user` dari request tidak boleh diterima.

---

## 7.2 Klien Melihat Daftar Pengajuan

Tujuan:

Memastikan Klien hanya melihat pengajuan miliknya sendiri.

Langkah testing:

1. Login sebagai Klien A.
2. Buat pengajuan.
3. Login sebagai Klien B.
4. Buat pengajuan.
5. Login kembali sebagai Klien A.
6. Buka daftar pengajuan.

Expected result:

1. Klien A hanya melihat pengajuan miliknya.
2. Klien A tidak melihat pengajuan Klien B.
3. Pagination berjalan jika data banyak.

---

## 7.3 Klien Melihat Detail Pengajuan

Tujuan:

Memastikan detail pengajuan dilindungi ownership check.

Langkah testing:

1. Login sebagai Klien A.
2. Buka detail pengajuan milik Klien A.
3. Coba akses URL detail pengajuan milik Klien B.

Expected result:

1. Detail milik sendiri dapat dibuka.
2. Detail milik Klien lain ditolak.
3. Dokumen milik pengajuan tampil sesuai otorisasi.

---

# Testing Area 8 - Akses Dokumen

## 8.1 Melihat Dokumen Dengan Authorization

Tujuan:

Memastikan dokumen tidak bisa diakses sembarangan.

Langkah testing:

1. Login sebagai Klien pemilik dokumen.
2. Buka dokumen melalui route/controller dokumen.
3. Login sebagai Klien lain.
4. Coba akses dokumen yang sama.
5. Login sebagai Staf Legal.
6. Coba akses dokumen untuk verifikasi.
7. Login sebagai Admin.
8. Coba akses dokumen untuk kebutuhan administratif.

Expected result:

1. Klien pemilik dapat membuka dokumen miliknya.
2. Klien lain ditolak.
3. Staf Legal dapat membuka dokumen untuk kebutuhan verifikasi.
4. Admin dapat membuka dokumen sesuai scope.
5. Raw `file_path` tidak ditampilkan.
6. Link langsung tanpa authorization tidak digunakan.

---

# Testing Area 9 - Verifikasi Berkas

## 9.1 Staf Legal Melihat Daftar Pengajuan

Tujuan:

Memastikan Staf Legal dapat melihat pengajuan yang perlu diverifikasi.

Langkah testing:

1. Login sebagai Staf Legal.
2. Buka daftar pengajuan.
3. Pastikan pengajuan dengan status `menunggu_verifikasi` tampil.
4. Pastikan pengajuan dengan status `menunggu_verifikasi_ulang` tampil jika ada.

Expected result:

1. Daftar pengajuan tampil.
2. Data menggunakan pagination.
3. Data Klien, kategori, dan tanggal dapat terlihat sesuai kebutuhan.
4. Staf Legal tidak masuk ke fitur Admin.

---

## 9.2 Verifikasi Berkas Lengkap

Tujuan:

Memastikan Staf Legal dapat menandai berkas lengkap.

Langkah testing:

1. Login sebagai Staf Legal.
2. Buka detail pengajuan dengan status `menunggu_verifikasi`.
3. Buka form verifikasi.
4. Pilih status verifikasi `berkas_lengkap`.
5. Tandai dokumen valid jika fitur tersedia.
6. Submit form.

Expected result:

1. Data masuk ke `verifikasi_berkas`.
2. `id_user` verifikator menggunakan ID Staf Legal.
3. `tanggal_verifikasi` diisi server.
4. `status_verifikasi` bernilai `berkas_lengkap`.
5. `status_pengajuan` berubah menjadi `berkas_lengkap`.
6. Dokumen valid dapat berubah menjadi `valid`.
7. Record baru masuk ke `riwayat_status`.
8. Proses menggunakan transaction.

---

## 9.3 Verifikasi Berkas Tidak Lengkap

Tujuan:

Memastikan Staf Legal wajib memberikan catatan jika berkas tidak lengkap.

Langkah testing:

1. Login sebagai Staf Legal.
2. Buka detail pengajuan.
3. Buka form verifikasi.
4. Pilih status verifikasi `berkas_tidak_lengkap`.
5. Isi catatan umum atau catatan per dokumen.
6. Tandai dokumen bermasalah sebagai `perlu_perbaikan`.
7. Submit form.

Expected result:

1. `status_verifikasi` bernilai `berkas_tidak_lengkap`.
2. `status_pengajuan` berubah menjadi `berkas_tidak_lengkap`.
3. Catatan umum masuk ke `verifikasi_berkas.catatan_umum`.
4. Catatan per dokumen masuk ke `catatan_verifikasi`.
5. Dokumen bermasalah menjadi `perlu_perbaikan`.
6. `status_perbaikan` awal `belum_diperbaiki`.
7. Record baru masuk ke `riwayat_status`.
8. Proses menggunakan transaction.

Negative test:

1. Status tidak lengkap tanpa catatan harus gagal.
2. Dokumen `perlu_perbaikan` tanpa catatan harus gagal.
3. `id_dokumen` dari pengajuan lain harus ditolak.
4. Role selain Staf Legal tidak boleh melakukan verifikasi.

---

# Testing Area 10 - Status dan Catatan Klien

## 10.1 Klien Melihat Status dan Catatan

Tujuan:

Memastikan Klien dapat melihat status, riwayat, dan catatan pengajuan miliknya.

Langkah testing:

1. Login sebagai Klien.
2. Buka detail pengajuan.
3. Buka halaman status.
4. Lihat status pengajuan.
5. Lihat riwayat status.
6. Lihat catatan verifikasi jika ada.

Expected result:

1. Status pengajuan tampil sesuai database.
2. Riwayat status tampil berurutan.
3. Catatan umum tampil jika ada.
4. Catatan per dokumen tampil jika ada.
5. Klien hanya melihat data miliknya.

---

# Testing Area 11 - Unggah Ulang Dokumen

## 11.1 Re-upload Dokumen Bermasalah

Tujuan:

Memastikan Klien dapat mengunggah ulang dokumen yang perlu diperbaiki.

Langkah testing:

1. Login sebagai Klien.
2. Buka pengajuan dengan status `berkas_tidak_lengkap`.
3. Lihat dokumen yang statusnya `perlu_perbaikan`.
4. Buka form unggah ulang.
5. Upload file pengganti.
6. Submit form.
7. Cek database.

Expected result:

1. Re-upload berhasil.
2. File lama tidak ditimpa.
3. Dokumen lama berubah menjadi `diganti`.
4. Record dokumen baru dibuat.
5. Dokumen baru berstatus `terkirim`.
6. Catatan terkait berubah menjadi `sudah_diperbaiki`.
7. Status pengajuan berubah menjadi `menunggu_verifikasi_ulang`.
8. Record baru masuk ke `riwayat_status`.
9. Proses menggunakan transaction.

Negative test:

1. Re-upload dokumen milik Klien lain harus ditolak.
2. Re-upload dokumen yang tidak berstatus `perlu_perbaikan` harus ditolak.
3. Re-upload tanpa catatan perbaikan harus ditolak.
4. File lebih dari 5 MB harus gagal.
5. Format file tidak valid harus gagal.
6. `file_path` dari request tidak boleh diterima.

---

# Testing Area 12 - Jadwal Konsultasi

## 12.1 Admin Membuat Jadwal

Tujuan:

Memastikan Admin dapat membuat slot jadwal konsultasi.

Langkah testing:

1. Login sebagai Admin.
2. Buka halaman jadwal konsultasi.
3. Tambah jadwal baru.
4. Isi tanggal, waktu mulai, waktu selesai.
5. Submit form.

Expected result:

1. Jadwal berhasil dibuat.
2. `id_user` pembuat jadwal adalah ID Admin.
3. Status awal slot adalah `tersedia`.
4. Status `terisi` tidak bisa dipilih saat membuat jadwal.
5. `waktu_selesai` harus lebih besar dari `waktu_mulai`.

Negative test:

1. Tanggal kosong harus gagal.
2. Waktu mulai kosong harus gagal.
3. Waktu selesai lebih kecil dari waktu mulai harus gagal.
4. Role selain Admin tidak boleh membuat jadwal.

---

## 12.2 Admin Mengubah Jadwal

Tujuan:

Memastikan Admin dapat mengubah slot jadwal sesuai aturan.

Langkah testing:

1. Login sebagai Admin.
2. Buka daftar jadwal.
3. Edit jadwal yang belum memiliki booking.
4. Simpan perubahan.

Expected result:

1. Jadwal berhasil diubah.
2. Jadwal yang sudah memiliki booking aktif tidak boleh diubah sembarangan.
3. Status slot mengikuti `STATUS_RULES.md`.

---

# Testing Area 13 - Booking Konsultasi

## 13.1 Klien Melihat Jadwal Tersedia

Tujuan:

Memastikan Klien hanya bisa melihat jadwal tersedia setelah berkas lengkap.

Langkah testing:

1. Login sebagai Klien dengan pengajuan `berkas_lengkap`.
2. Buka halaman pilih jadwal.
3. Lihat daftar jadwal tersedia.

Expected result:

1. Jadwal dengan status `tersedia` tampil.
2. Jadwal `terisi` tidak bisa dipilih.
3. Jadwal `tidak_aktif` tidak bisa dipilih.

Negative test:

1. Pengajuan belum `berkas_lengkap` tidak boleh masuk ke pemilihan jadwal.
2. Klien tidak boleh memilih jadwal untuk pengajuan milik orang lain.

---

## 13.2 Klien Membuat Booking

Tujuan:

Memastikan Klien dapat memilih jadwal konsultasi.

Langkah testing:

1. Login sebagai Klien.
2. Buka pengajuan dengan status `berkas_lengkap`.
3. Pilih jadwal yang `tersedia`.
4. Submit booking.
5. Cek database.

Expected result:

1. Booking masuk ke `booking_konsultasi`.
2. `id_user` booking adalah ID Klien.
3. `status_booking` awal `aktif`.
4. `tanggal_booking` diisi server.
5. Jadwal berubah menjadi `terisi`.
6. Status pengajuan berubah menjadi `jadwal_dipilih`.
7. Record baru masuk ke `riwayat_status`.
8. Proses menggunakan transaction.

Negative test:

1. Satu pengajuan tidak boleh memiliki dua booking aktif.
2. Satu jadwal tidak boleh memiliki dua booking aktif.
3. Jadwal `terisi` tidak boleh dipilih.
4. Jadwal `tidak_aktif` tidak boleh dipilih.
5. Booking untuk pengajuan milik Klien lain harus ditolak.
6. `status_booking` dari request tidak boleh diterima.
7. `tanggal_booking` dari request tidak boleh diterima.

---

# Testing Area 14 - Penyelesaian Konsultasi

## 14.1 Admin Menandai Konsultasi Selesai

Tujuan:

Memastikan Admin dapat menandai konsultasi selesai.

Langkah testing:

1. Login sebagai Admin.
2. Buka data booking aktif.
3. Pilih aksi selesai.
4. Konfirmasi aksi.
5. Cek database.

Expected result:

1. Booking dengan status `aktif` berubah menjadi `selesai`.
2. Status pengajuan terkait berubah menjadi `selesai`.
3. Record baru masuk ke `riwayat_status`.
4. Proses menggunakan transaction.

Negative test:

1. Booking yang sudah `selesai` tidak boleh diselesaikan ulang.
2. Booking yang `dibatalkan` tidak boleh diselesaikan. Pengujian ini hanya dilakukan jika status booking `dibatalkan` tersedia melalui fitur yang sudah disetujui atau data testing.
3. Role selain Admin tidak boleh menyelesaikan konsultasi.
4. Perubahan status pengajuan tanpa riwayat status harus dianggap gagal.

---

# Testing Area 15 - Laporan Pra-Pendaftaran

## 15.1 Admin Melihat Laporan

Tujuan:

Memastikan Admin dapat melihat laporan pra-pendaftaran dari query.

Langkah testing:

1. Login sebagai Admin.
2. Buka halaman laporan pra-pendaftaran.
3. Lihat tabel laporan.
4. Coba filter berdasarkan tanggal.
5. Coba filter berdasarkan status.
6. Coba filter berdasarkan kategori.
7. Coba filter berdasarkan keyword.

Expected result:

1. Laporan tampil dalam bentuk tabel.
2. Data berasal dari query, bukan tabel `laporan`.
3. Filter tanggal bekerja.
4. Filter status bekerja.
5. Filter kategori bekerja.
6. Filter keyword bekerja.
7. Pagination berjalan jika data banyak.

Negative test:

1. Role selain Admin tidak boleh mengakses laporan.
2. Filter status tidak valid harus ditolak.
3. Filter kategori tidak valid harus ditolak.
4. Keyword terlalu panjang harus gagal.
5. Jangan menampilkan raw `file_path`.

---

## 15.2 Print Browser

Tujuan:

Memastikan laporan dapat dicetak melalui fitur print browser.

Langkah testing:

1. Login sebagai Admin.
2. Buka halaman laporan.
3. Terapkan filter jika diperlukan.
4. Klik tombol print.
5. Pastikan tampilan print muncul.

Expected result:

1. Tampilan print browser terbuka.
2. Data yang dicetak sesuai filter.
3. Tidak ada export PDF atau Excel tanpa persetujuan.
4. Tidak ada tabel `laporan`.

---

# Testing Area 16 - Security Regression

## 16.1 URL Direct Access Test

Tujuan:

Memastikan user tidak bisa melewati menu dengan akses URL langsung.

Langkah testing:

1. Login sebagai Klien.
2. Akses URL Admin secara langsung.
3. Akses URL Staf Legal secara langsung.
4. Login sebagai Staf Legal.
5. Akses URL Admin secara langsung.
6. Login sebagai Admin.
7. Akses URL Klien secara langsung.

Expected result:

1. Semua akses lintas role ditolak.
2. Middleware role berjalan.
3. Menu hidden bukan satu-satunya proteksi.

---

## 16.2 Ownership Bypass Test

Tujuan:

Memastikan Klien tidak bisa mengakses data Klien lain.

Langkah testing:

1. Buat dua akun Klien.
2. Klien A membuat pengajuan.
3. Klien B membuat pengajuan.
4. Login sebagai Klien A.
5. Ubah ID pengajuan pada URL menjadi ID milik Klien B.

Expected result:

1. Akses ditolak.
2. Data Klien B tidak tampil.
3. Query menggunakan `id_user = auth()->id()`.

---

## 16.3 File Path Exposure Test

Tujuan:

Memastikan path file tidak bocor ke user.

Langkah testing:

1. Login sebagai Klien.
2. Buka halaman dokumen.
3. Inspect link dokumen.
4. Pastikan link mengarah ke route/controller dokumen.
5. Pastikan raw `file_path` tidak ditampilkan.

Expected result:

1. Tidak ada raw `file_path` di UI.
2. Dokumen dibuka melalui controller dengan authorization.
3. Klien lain tidak bisa membuka dokumen tersebut.

---

# Testing Area 17 - Database Integrity

## 17.1 Cek Tabel Utama

Tujuan:

Memastikan database tidak keluar dari rancangan skripsi.

Cek tabel domain:

1. `users`
2. `profil_klien`
3. `kategori_perkara`
4. `pra_pendaftaran_perkara`
5. `dokumen_perkara`
6. `verifikasi_berkas`
7. `catatan_verifikasi`
8. `riwayat_status`
9. `jadwal_konsultasi`
10. `booking_konsultasi`

Expected result:

1. Sepuluh tabel domain tersedia.
2. Tidak ada tabel `laporan`.
3. Tidak ada kolom yang tidak disetujui.
4. Tabel `migrations` boleh ada sebagai metadata internal Laravel.
5. Tabel auxiliary Laravel lain seperti `sessions`, `cache`, `jobs`, atau `password_reset_tokens` hanya boleh ada jika sudah disetujui.

---

## 17.2 Cek Status dan Role

Tujuan:

Memastikan role dan status tersimpan sebagai slug lowercase.

Expected role:

1. `klien`
2. `admin`
3. `staf_legal`

Expected status pengajuan:

1. `menunggu_verifikasi`
2. `berkas_tidak_lengkap`
3. `menunggu_verifikasi_ulang`
4. `berkas_lengkap`
5. `jadwal_dipilih`
6. `selesai`

Expected result:

1. Tidak ada label UI tersimpan di database.
2. Tidak ada database `ENUM`.
3. Tidak ada status baru tanpa persetujuan.

---

# Feature Completion Checklist

Sebuah fitur dianggap lolos manual testing jika:

1. Route dapat diakses oleh role yang benar.
2. Route ditolak untuk role yang salah.
3. Input valid berhasil diproses.
4. Input tidak valid ditolak.
5. Validasi server-side berjalan.
6. Ownership check berjalan.
7. Authorization check berjalan untuk dokumen.
8. Perubahan status sesuai `STATUS_RULES.md`.
9. Perubahan status pengajuan tercatat di `riwayat_status`.
10. Proses multi-tabel menggunakan transaction.
11. Tampilan memiliki flash message.
12. Tampilan memiliki empty state jika data kosong.
13. Daftar data menggunakan pagination jika diperlukan.
14. Tidak ada fitur di luar scope skripsi.
15. Tidak ada tabel atau kolom baru tanpa persetujuan.
16. Tidak ada error utama setelah testing.

---

# Bug Report Template

Gunakan format berikut saat menemukan bug:

```text
Nama fitur:
Role yang digunakan:
URL/route:
Langkah yang dilakukan:
Expected result:
Actual result:
Pesan error:
Screenshot/log jika ada:
Dugaan penyebab:
Prioritas:
```

---

# Final Notes for AI Agent

AI agent wajib:

1. Memberikan langkah testing manual setelah membuat fitur.
2. Menguji alur berhasil dan alur gagal.
3. Menguji role access.
4. Menguji ownership check untuk Klien.
5. Menguji authorization dokumen.
6. Menguji validasi input.
7. Menguji perubahan status dan `riwayat_status`.
8. Tidak menyatakan fitur selesai sebelum testing manual dijelaskan.
