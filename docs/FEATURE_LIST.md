# FEATURE_LIST.md

## Purpose

Dokumen ini berisi daftar fitur dan urutan implementasi sistem.

AI agent wajib mengikuti urutan fitur pada dokumen ini agar proses coding tetap bertahap, aman, dan sesuai ruang lingkup skripsi.

Fitur tidak boleh dibuat sekaligus dalam satu perubahan besar.

Setiap fitur harus dibuat secara bertahap, diuji manual, lalu dikunci dengan commit Git yang jelas.

---

## Build Strategy

Strategi pembangunan sistem menggunakan pendekatan bertahap:

1. Setup project.
2. Rancang database dan model.
3. Implementasi authentication dan role access.
4. Implementasi fitur Klien.
5. Implementasi fitur Staf Legal.
6. Implementasi fitur Admin.
7. Implementasi laporan.
8. Testing manual.
9. Persiapan deployment.

AI agent wajib menjelaskan rencana implementasi sebelum membuat atau mengubah kode.

---

## Fase 1 - Project Setup

Status: sebelum coding fitur utama.

Tujuan fase ini adalah menyiapkan pondasi project Laravel.

Fitur/pekerjaan:

1. Membuat project Laravel.
2. Mengatur koneksi database MySQL.
3. Menginstall Laravel Breeze.
4. Mengatur Blade dan Tailwind CSS.
5. Mengatur Git repository.
6. Mengatur Laravel Storage.
7. Membuat symbolic link storage.
8. Membuat folder upload dokumen perkara.
9. Membuat dokumentasi AI agent dan dokumen perancangan project.

Output fase ini:

1. Laravel berjalan.
2. Database terkoneksi.
3. Breeze berhasil diinstall.
4. Halaman login dan register tersedia.
5. Git repository aktif.
6. Dokumentasi project tersedia.

---

## Fase 2 - Database Schema and Model Foundation

Status: fase ini hanya boleh dimulai setelah dokumentasi database dikunci.

Tujuan fase ini adalah membuat struktur database, model, relasi, dan data awal.

Fitur/pekerjaan:

1. Menyesuaikan tabel `users` dengan rancangan final.
2. Membuat migration `profil_klien`.
3. Membuat migration `kategori_perkara`.
4. Membuat migration `pra_pendaftaran_perkara`.
5. Membuat migration `dokumen_perkara`.
6. Membuat migration `verifikasi_berkas`.
7. Membuat migration `catatan_verifikasi`.
8. Membuat migration `riwayat_status`.
9. Membuat migration `jadwal_konsultasi`.
10. Membuat migration `booking_konsultasi`.
11. Membuat migration `permintaan_reschedule`.
12. Membuat model Eloquent untuk setiap tabel.
13. Menambahkan `protected $table` dan `protected $primaryKey` pada setiap model.
14. Membuat relasi model sesuai `docs/MODEL_RELATION_PLAN.md`.
15. Membuat seeder Admin.
16. Membuat seeder Staf Legal.
17. Membuat seeder kategori perkara awal.

Aturan penting:

1. Jangan membuat tabel `laporan`.
2. Jangan menggunakan database `ENUM`.
3. Jangan mengganti custom primary key menjadi default Laravel `id`.
4. Jangan mengganti foreign key seperti `id_user` menjadi `user_id`.
5. Jangan menjalankan migration sebelum file migration direview dan disetujui.
6. Jangan menggunakan `cascadeOnDelete()` secara otomatis untuk semua foreign key.

Output fase ini:

1. Semua migration tersedia.
2. Semua model tersedia.
3. Relasi model tersedia.
4. Seeder awal tersedia.
5. Struktur database sesuai rancangan skripsi.

---

## Fase 3 - Authentication and Role Access

Tujuan fase ini adalah memastikan pengguna dapat login dan diarahkan sesuai role.

Fitur/pekerjaan:

1. Menyesuaikan Laravel Breeze agar menggunakan field `nama`.
2. Menyesuaikan model `User` agar menggunakan custom primary key `id_user`.
3. Memastikan field `role`, `no_telepon`, dan `status_akun` pada tabel `users` digunakan dengan benar untuk autentikasi dan role access.
4. Menyediakan Forgot Password / Reset Password untuk pemulihan password akun melalui mekanisme Laravel Breeze.
5. Membuat middleware role.
6. Membuat redirect dashboard berdasarkan role.
7. Membuat layout dasar untuk Klien.
8. Membuat layout dasar untuk Admin.
9. Membuat layout dasar untuk Staf Legal.
10. Melindungi route berdasarkan role.

Role yang digunakan:

1. `klien`
2. `admin`
3. `staf_legal`

Output fase ini:

1. Register Klien berjalan.
2. Login berjalan.
3. Logout berjalan.
4. User diarahkan ke dashboard sesuai role.
5. Setiap role hanya dapat mengakses halaman yang sesuai.
6. Forgot Password / Reset Password tersedia untuk pemulihan password akun.

---

## Fase 4 - Modul Klien

Tujuan fase ini adalah membuat fitur utama yang digunakan oleh Klien.

### 4.1 Profil Klien

Fitur:

1. Melihat profil.
2. Mengisi profil.
3. Mengubah profil.

Data terkait:

1. `users`
2. `profil_klien`

Aturan:

1. Klien hanya dapat mengakses profil miliknya sendiri.
2. Data profil harus divalidasi.
3. Perubahan profil tidak boleh mengubah role pengguna.

---

### 4.2 Pengajuan Pra-Pendaftaran Perkara

Fitur:

1. Melihat daftar pengajuan milik sendiri.
2. Membuat pengajuan pra-pendaftaran.
3. Mengisi kategori perkara.
4. Mengisi judul perkara.
5. Mengisi kronologi perkara.
6. Mengunggah dokumen pendukung.
7. Melihat detail pengajuan.

Data terkait:

1. `pra_pendaftaran_perkara`
2. `dokumen_perkara`
3. `riwayat_status`
4. `kategori_perkara`

Aturan:

1. Status awal pengajuan adalah `menunggu_verifikasi`.
2. Status awal wajib dicatat ke `riwayat_status`.
3. Proses membuat pengajuan, menyimpan dokumen, dan mencatat riwayat status wajib menggunakan database transaction.
4. Klien tidak boleh mengubah data pengajuan setelah dikirim.
5. File dokumen harus divalidasi berdasarkan extension, MIME type, dan ukuran maksimal 5 MB.
6. File dokumen harus disimpan dengan nama unik atau random.
7. File lama tidak boleh ditimpa.

---

### 4.3 Status dan Catatan Verifikasi

Fitur:

1. Melihat status pengajuan.
2. Melihat riwayat status.
3. Melihat catatan umum.
4. Melihat catatan per dokumen.

Data terkait:

1. `pra_pendaftaran_perkara`
2. `riwayat_status`
3. `verifikasi_berkas`
4. `catatan_verifikasi`
5. `dokumen_perkara`

Aturan:

1. Klien hanya dapat melihat pengajuan miliknya sendiri.
2. Klien tidak boleh melihat data pengajuan Klien lain.
3. Catatan verifikasi hanya muncul jika sudah dibuat oleh Staf Legal.

---

### 4.4 Unggah Ulang Dokumen

Fitur:

1. Melihat dokumen yang perlu diperbaiki.
2. Mengunggah ulang dokumen.
3. Mengubah status dokumen sesuai aturan.
4. Mengubah status pengajuan menjadi `menunggu_verifikasi_ulang`.

Data terkait:

1. `dokumen_perkara`
2. `catatan_verifikasi`
3. `pra_pendaftaran_perkara`
4. `riwayat_status`

Aturan:

1. Klien hanya boleh mengunggah ulang dokumen jika ada catatan perbaikan.
2. File lama tidak boleh ditimpa.
3. File baru harus disimpan sebagai file berbeda.
4. Proses unggah ulang wajib menggunakan database transaction.
5. Perubahan status wajib dicatat pada `riwayat_status`.
6. Dokumen lama diberi status `diganti`.
7. Dokumen baru diberi status `terkirim`.
8. Catatan verifikasi terkait diperbarui menjadi `sudah_diperbaiki`.

---

### 4.5 Pemilihan Jadwal Konsultasi

Fitur:

1. Melihat jadwal konsultasi tersedia.
2. Memilih jadwal konsultasi.
3. Memilih metode konsultasi (online/offline).
4. Membuat booking konsultasi.
5. Melihat status konfirmasi detail konsultasi dari Admin.
6. Mengajukan permintaan reschedule jika jadwal atau metode perlu diubah.
7. Melihat hasil persetujuan/penolakan reschedule dari Admin.

Data terkait:

1. `jadwal_konsultasi`
2. `booking_konsultasi`
3. `permintaan_reschedule`
4. `pra_pendaftaran_perkara`
5. `riwayat_status`

Aturan:

1. Klien hanya dapat memilih jadwal jika status pengajuan adalah `berkas_lengkap`.
2. Saat booking, Klien wajib memilih `metode_konsultasi` yaitu `online` atau `offline`.
3. Klien boleh mengisi `catatan_preferensi_klien` sebagai preferensi tambahan.
4. Satu pengajuan hanya boleh memiliki satu booking konsultasi aktif.
5. Slot jadwal yang sudah terisi tidak boleh dipilih ulang.
6. Setelah booking berhasil, status pengajuan menjadi `jadwal_dipilih`.
7. Status konfirmasi detail konsultasi awal adalah `menunggu_konfirmasi`.
8. Perubahan status wajib dicatat pada `riwayat_status`.
9. Proses booking wajib menggunakan database transaction.
10. Slot jadwal yang berhasil dipilih harus diubah menjadi `terisi`.
11. Booking baru harus memiliki status `aktif`.
12. Field `tanggal_booking` wajib diisi saat booking dibuat.
13. Klien dapat mengajukan reschedule dan menunggu keputusan Admin sebelum booking lama berubah.
14. Tidak ada chat internal, video call internal, integrasi Zoom/Google Meet otomatis, notifikasi email konsultasi, atau integrasi kalender eksternal.

---

## Fase 5 - Modul Staf Legal

Tujuan fase ini adalah membuat fitur verifikasi berkas perkara.

### 5.1 Daftar Pengajuan

Fitur:

1. Melihat daftar pengajuan yang menunggu verifikasi.
2. Melihat daftar pengajuan yang menunggu verifikasi ulang.
3. Mencari atau memfilter pengajuan.

Data terkait:

1. `pra_pendaftaran_perkara`
2. `users`
3. `kategori_perkara`

Aturan:

1. Staf Legal hanya dapat mengakses halaman Staf Legal.
2. Daftar data harus menggunakan pagination.
3. Filter status dapat digunakan jika diperlukan.

---

### 5.2 Detail dan Pemeriksaan Berkas

Fitur:

1. Melihat detail pengajuan.
2. Melihat data Klien.
3. Melihat dokumen pendukung.
4. Membuka dokumen dengan otorisasi.

Data terkait:

1. `pra_pendaftaran_perkara`
2. `dokumen_perkara`
3. `profil_klien`
4. `kategori_perkara`

Aturan:

1. Dokumen tidak boleh dibuka tanpa otorisasi.
2. Staf Legal hanya boleh mengakses fitur verifikasi, bukan fitur Admin.

---

### 5.3 Verifikasi Berkas

Fitur:

1. Menandai berkas lengkap.
2. Menandai berkas tidak lengkap.
3. Menulis catatan umum.
4. Menulis catatan per dokumen.
5. Memperbarui status pengajuan.
6. Mencatat riwayat status.

Data terkait:

1. `verifikasi_berkas`
2. `catatan_verifikasi`
3. `pra_pendaftaran_perkara`
4. `dokumen_perkara`
5. `riwayat_status`

Aturan:

1. Verifikasi wajib menggunakan database transaction.
2. Jika berkas lengkap, status pengajuan menjadi `berkas_lengkap`.
3. Jika berkas tidak lengkap, status pengajuan menjadi `berkas_tidak_lengkap`.
4. Setiap perubahan status wajib dicatat di `riwayat_status`.
5. Catatan dapat bersifat umum atau per dokumen.
6. Jika ada catatan per dokumen, dokumen terkait dapat diberi status `perlu_perbaikan`.
7. Jika berkas lengkap, dokumen yang valid dapat diberi status `valid`.
8. Jika berkas tidak lengkap, dokumen yang bermasalah diberi status `perlu_perbaikan`.
9. Status verifikasi harus mengikuti `docs/STATUS_RULES.md`.

---

## Fase 6 - Modul Admin

Tujuan fase ini adalah membuat fitur pengelolaan data utama sistem.

### 6.1 Dashboard Admin

Fitur:

1. Melihat jumlah pengguna.
2. Melihat jumlah pengajuan.
3. Melihat jumlah pengajuan berdasarkan status.
4. Melihat jumlah jadwal tersedia.
5. Melihat ringkasan booking konsultasi.

Data terkait:

1. `users`
2. `pra_pendaftaran_perkara`
3. `jadwal_konsultasi`
4. `booking_konsultasi`

---

### 6.2 Kelola Pengguna

Fitur:

1. Melihat daftar pengguna.
2. Membuat akun Staf Legal.
3. Mengubah data pengguna tertentu.
4. Mengubah status akun pengguna.

Data terkait:

1. `users`

Aturan:

1. Admin dapat membuat akun Staf Legal.
2. Admin tidak boleh mengubah user tanpa validasi.
3. Role harus menggunakan slug yang valid.

---

### 6.3 Kelola Data Pra-Pendaftaran

Fitur:

1. Melihat daftar seluruh pra-pendaftaran perkara.
2. Melihat detail pra-pendaftaran perkara.
3. Melakukan pencarian data pra-pendaftaran.
4. Melakukan filter berdasarkan tanggal, status, dan kategori perkara.

Data terkait:

1. `pra_pendaftaran_perkara`
2. `users`
3. `kategori_perkara`
4. `dokumen_perkara`
5. `verifikasi_berkas`
6. `booking_konsultasi`

Aturan:

1. Admin hanya memantau dan mengelola data administratif pra-pendaftaran.
2. Admin tidak mengambil alih proses verifikasi berkas milik Staf Legal.
3. Daftar data harus menggunakan pagination.
4. Filter data harus mengikuti kebutuhan laporan dan dashboard.

---

### 6.4 Kelola Kategori Perkara

Fitur:

1. Melihat daftar kategori perkara.
2. Menambah kategori perkara.
3. Mengubah kategori perkara.
4. Menghapus kategori perkara hanya jika kategori tersebut belum pernah digunakan pada pengajuan.

Data terkait:

1. `kategori_perkara`
2. `pra_pendaftaran_perkara`

Aturan:

1. Kategori yang sudah digunakan pada pengajuan tidak boleh dihapus.
2. Perubahan kategori harus divalidasi.
3. Jika kategori sudah pernah digunakan, data kategori tetap disimpan agar riwayat pengajuan tidak rusak.
4. Fitur menonaktifkan kategori hanya boleh dibuat jika `docs/DATABASE_PLAN.md` menyediakan field khusus seperti `status_kategori` atau mekanisme soft delete.


---

### 6.5 Kelola Jadwal Konsultasi

Fitur:

1. Melihat daftar slot jadwal.
2. Membuat slot jadwal.
3. Mengubah slot jadwal.
4. Mengubah status slot jadwal.
5. Mengonfirmasi detail teknis konsultasi (mengisi link untuk online atau lokasi untuk offline).
6. Memproses (menyetujui/menolak) permintaan reschedule dari Klien.
7. Membatalkan booking lama dan membuat booking baru jika reschedule disetujui.

Data terkait:

1. `jadwal_konsultasi`
2. `booking_konsultasi`
3. `permintaan_reschedule`

Aturan:

1. Jadwal dibuat oleh Admin.
2. Slot yang sudah terisi tidak boleh dihapus sembarangan.
3. Status slot menggunakan slug sesuai `STATUS_RULES.md`.
4. Admin mengonfirmasi detail teknis konsultasi dengan mengisi manual link konsultasi online atau lokasi konsultasi offline.
5. Admin dapat menambahkan `catatan_konsultasi` sebagai instruksi final.
6. Admin dapat menyetujui atau menolak permintaan reschedule Klien sesuai alur yang dikunci.
7. Jika reschedule disetujui, booking lama dibatalkan, slot lama dibuka kembali, dan booking baru dibuat pada slot baru.
8. Tidak ada chat internal, video call internal, integrasi Zoom/Google Meet otomatis, notifikasi email konsultasi, atau integrasi kalender eksternal.

---

### 6.6 Laporan Pra-Pendaftaran

Fitur:

1. Melihat laporan pra-pendaftaran.
2. Filter laporan berdasarkan tanggal.
3. Filter laporan berdasarkan status pengajuan.
4. Filter laporan berdasarkan kategori perkara.
5. Mencetak laporan menggunakan fitur print browser.

Data terkait:

1. `pra_pendaftaran_perkara`
2. `users`
3. `kategori_perkara`
4. `verifikasi_berkas`
5. `booking_konsultasi`

Aturan:

1. Tidak membuat tabel `laporan`.
2. Laporan dibuat dari query dan rekap data.
3. Laporan ditampilkan dalam bentuk tabel.
4. Laporan dapat dicetak menggunakan fitur print browser.

---

## Fase 7 - Manual Testing

Tujuan fase ini adalah memastikan semua fitur berjalan sesuai rancangan.

Testing dilakukan berdasarkan:

```text
docs/MANUAL_TESTING_PLAN.md
```

Area testing:

1. Authentication.
2. Role access.
3. Profil Klien.
4. Pengajuan pra-pendaftaran.
5. Upload dokumen.
6. Verifikasi berkas.
7. Unggah ulang dokumen.
8. Booking jadwal.
9. Laporan.
10. Proteksi akses dokumen.

---

## Fase 8 - Deployment Preparation

Tujuan fase ini adalah menyiapkan aplikasi untuk deployment ke Cloud VPS.

Deployment mengikuti:

```text
docs/DEPLOYMENT_NOTES.md
```

Area deployment:

1. Environment production.
2. Database production.
3. Web server.
4. PHP-FPM.
5. Composer install.
6. NPM build.
7. Storage link.
8. Permission folder.
9. HTTPS jika domain tersedia.
10. Backup database dan dokumen.

---

## Feature Implementation Template

Setiap fitur harus diawali dengan rencana berikut:

```text
Nama fitur:
Role pengguna:
Tujuan fitur:
Tabel yang digunakan:
Route yang digunakan:
File yang dibuat/diubah:
Validasi:
Otorisasi:
Transaction:
Output:
Testing manual:
Risiko:
Commit message:
```

AI agent wajib menjawab menggunakan format ini sebelum mulai memberi kode untuk fitur baru.

---

## Feature Locking Rule

Setiap fitur dianggap terkunci jika:

1. Implementasi sudah selesai.
2. Testing manual sudah dijalankan.
3. Tidak ada error utama.
4. Tidak ada pelanggaran terhadap `AGENTS.md`.
5. Tidak ada perubahan di luar scope fitur.
6. Commit Git sudah dibuat.

---

## Notes for AI Agent

AI agent harus selalu mengingat:

1. Build fitur secara bertahap.
2. Jangan membuat semua modul sekaligus.
3. Jangan membuat fitur di luar ruang lingkup skripsi.
4. Jangan membuat tabel laporan.
5. Jangan mengubah nama tabel atau kolom tanpa persetujuan.
6. Jangan menjalankan command berbahaya tanpa izin.
7. Jangan mengabaikan validasi dan otorisasi.
8. Jangan mengakses dokumen perkara tanpa proteksi.
