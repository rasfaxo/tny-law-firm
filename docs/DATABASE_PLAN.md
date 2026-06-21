# DATABASE_PLAN.md

## Purpose

Dokumen ini berisi rancangan database final untuk project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Dokumen ini menjadi acuan utama sebelum membuat migration, model, foreign key, relasi Eloquent, seeder, dan query laporan.

AI agent wajib mengikuti dokumen ini dan tidak boleh membuat struktur database berdasarkan asumsi sendiri.

---

## Database Name

Nama database lokal:

```text
tny_law_firm
```

---

## General Database Rules

Aturan umum database:

1. Database menggunakan MySQL.
2. Semua tabel menggunakan `created_at` dan `updated_at`.
3. Primary key menggunakan nama custom sesuai rancangan skripsi.
4. Jangan menggunakan primary key default Laravel `id` jika tabel memiliki primary key custom.
5. Jangan menggunakan database `ENUM`.
6. Role dan status disimpan menggunakan `VARCHAR`.
7. Validasi nilai role dan status dilakukan pada level aplikasi Laravel.
8. File dokumen tidak disimpan langsung di database.
9. Database hanya menyimpan metadata dokumen dan `file_path`.
10. Tidak membuat tabel `laporan`.
11. Laporan dibuat dari query dan rekap data.
12. Jangan menambahkan kolom baru tanpa persetujuan pemilik project.
13. Jangan mengubah nama tabel atau kolom tanpa persetujuan pemilik project.
14. Jangan menerapkan `cascadeOnDelete()` secara otomatis ke semua foreign key.
15. Penggunaan `cascadeOnDelete()`, `restrictOnDelete()`, atau `nullOnDelete()` harus disesuaikan dengan konteks relasi dan direview sebelum migration dijalankan.

---

## Laravel Migration Rules

Saat membuat migration, gunakan primary key custom secara eksplisit.

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
    ->on('users');
```

Jangan gunakan shorthand jika menghasilkan nama kolom default Laravel yang tidak sesuai rancangan.

Hindari:

```php
$table->foreignId('user_id')->constrained();
```

Gunakan:

```php
$table->unsignedBigInteger('id_user');

$table->foreign('id_user')
    ->references('id_user')
    ->on('users');
```

Migration hanya boleh dijalankan menggunakan:

```bash
php artisan migrate
```

setelah file migration direview dan disetujui.

---

## Laravel Breeze Compatibility Rule

Project menggunakan Laravel Breeze untuk authentication.

Tabel `users` tetap mengikuti rancangan skripsi dengan primary key `id_user`.

Model `User` wajib menggunakan:

```php
protected $table = 'users';
protected $primaryKey = 'id_user';
public $incrementing = true;
protected $keyType = 'int';
```

Laravel Breeze login tetap menggunakan:

```text
email
password
```

Jika Breeze scaffolding menggunakan field `name`, field tersebut harus disesuaikan menjadi:

```text
nama
```

Catatan penting:

Kolom `remember_token` tidak termasuk dalam rancangan database skripsi final. Jika fitur remember me bawaan Breeze tetap ingin digunakan, penambahan `remember_token` harus disetujui terlebih dahulu. Jika tidak disetujui, fitur remember me perlu disesuaikan agar tidak bergantung pada kolom tersebut.

---

## Laravel Default Auxiliary Tables Rule

- Laravel internal auxiliary tables such as `migrations` remain allowed for framework metadata.
- `password_reset_tokens` is allowed as an auxiliary Laravel table only for forgot password / reset password via Laravel Breeze.
- `password_reset_tokens` is not part of the 10 core domain tables, is not counted as a domain table, and does not enter the main ERD/LRS.
- Auxiliary tables such as `sessions`, `cache`, `jobs`, and `failed_jobs` remain outside the locked thesis schema unless separately reviewed and approved.
- The locked thesis domain database schema tetap berisi sepuluh tabel inti yang tercantum pada dokumen ini.
- Larangan tabel `laporan` tetap berlaku.

---

## Locked Tables

Gunakan hanya tabel berikut:

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

Jangan membuat tabel:

```text
laporan
```

---

## Table and Primary Key Summary

| Table                     | Primary Key      |
| ------------------------- | ---------------- |
| `users`                   | `id_user`        |
| `profil_klien`            | `id_profil`      |
| `kategori_perkara`        | `id_kategori`    |
| `pra_pendaftaran_perkara` | `id_pendaftaran` |
| `dokumen_perkara`         | `id_dokumen`     |
| `verifikasi_berkas`       | `id_verifikasi`  |
| `catatan_verifikasi`      | `id_catatan`     |
| `riwayat_status`          | `id_riwayat`     |
| `jadwal_konsultasi`       | `id_jadwal`      |
| `booking_konsultasi`      | `id_booking`     |

---

# Table Design

## 1. `users`

Fungsi:

Menyimpan data akun pengguna sistem, yaitu Klien, Admin, dan Staf Legal.

| Field         | Type            | Key    | Nullable | Description                               |
| ------------- | --------------- | ------ | -------- | ----------------------------------------- |
| `id_user`     | BIGINT UNSIGNED | PK     | No       | ID pengguna                               |
| `nama`        | VARCHAR(100)    |        | No       | Nama pengguna                             |
| `email`       | VARCHAR(100)    | UNIQUE | No       | Email untuk login                         |
| `password`    | VARCHAR(255)    |        | No       | Password hash                             |
| `role`        | VARCHAR(30)     | INDEX  | No       | Role user: `klien`, `admin`, `staf_legal` |
| `no_telepon`  | VARCHAR(20)     |        | Yes      | Nomor telepon pengguna                    |
| `status_akun` | VARCHAR(30)     | INDEX  | No       | Status akun, contoh: `aktif`, `nonaktif`  |
| `created_at`  | TIMESTAMP       |        | Yes      | Timestamp Laravel                         |
| `updated_at`  | TIMESTAMP       |        | Yes      | Timestamp Laravel                         |

Aturan:

1. `email` harus unique.
2. `role` tidak boleh menggunakan ENUM.
3. `status_akun` tidak boleh menggunakan ENUM.
4. Role default untuk registrasi publik adalah `klien`.
5. Admin dapat membuat akun `staf_legal`.
6. Jangan mengganti `nama` menjadi `name`.
7. Jangan mengganti `id_user` menjadi `id`.
8. Status akun default untuk registrasi publik adalah `aktif`.
9. Akun Admin dan Staf Legal dibuat atau dikelola melalui proses yang disetujui oleh Admin.


---

## 2. `profil_klien`

Fungsi:

Menyimpan data profil tambahan milik Klien.

| Field           | Type            | Key        | Nullable | Description               |
| --------------- | --------------- | ---------- | -------- | ------------------------- |
| `id_profil`     | BIGINT UNSIGNED | PK         | No       | ID profil                 |
| `id_user`       | BIGINT UNSIGNED | FK, UNIQUE | No       | Relasi ke `users.id_user` |
| `alamat`        | TEXT            |            | Yes      | Alamat Klien              |
| `jenis_kelamin` | VARCHAR(20)     |            | Yes      | Jenis kelamin             |
| `pekerjaan`     | VARCHAR(100)    |            | Yes      | Pekerjaan                 |
| `no_identitas`  | VARCHAR(50)     |            | Yes      | Nomor identitas           |
| `created_at`    | TIMESTAMP       |            | Yes      | Timestamp Laravel         |
| `updated_at`    | TIMESTAMP       |            | Yes      | Timestamp Laravel         |

Relasi:

```text
profil_klien.id_user → users.id_user
```

Aturan:

1. Satu Klien hanya memiliki satu profil.
2. `id_user` pada `profil_klien` harus unique.
3. Profil hanya dapat diakses oleh pemiliknya dan role yang diizinkan.
4. Jangan mengganti `id_user` menjadi `user_id`.

---

## 3. `kategori_perkara`

Fungsi:

Menyimpan data kategori perkara.

| Field           | Type            | Key | Nullable | Description           |
| --------------- | --------------- | --- | -------- | --------------------- |
| `id_kategori`   | BIGINT UNSIGNED | PK  | No       | ID kategori           |
| `nama_kategori` | VARCHAR(100)    |     | No       | Nama kategori perkara |
| `deskripsi`     | TEXT            |     | Yes      | Deskripsi kategori    |
| `created_at`    | TIMESTAMP       |     | Yes      | Timestamp Laravel     |
| `updated_at`    | TIMESTAMP       |     | Yes      | Timestamp Laravel     |

Aturan:

1. Tidak ada field `status_kategori`.
2. Tidak ada field `is_active`.
3. Tidak ada soft delete kecuali disetujui kemudian.
4. Kategori yang sudah digunakan pada pengajuan tidak boleh dihapus.
5. Jika perlu fitur menonaktifkan kategori, database harus direvisi terlebih dahulu dengan persetujuan.

---

## 4. `pra_pendaftaran_perkara`

Fungsi:

Menyimpan data pengajuan pra-pendaftaran perkara dari Klien.

| Field               | Type            | Key       | Nullable | Description           |
| ------------------- | --------------- | --------- | -------- | --------------------- |
| `id_pendaftaran`    | BIGINT UNSIGNED | PK        | No       | ID pra-pendaftaran    |
| `id_user`           | BIGINT UNSIGNED | FK, INDEX | No       | Klien yang mengajukan |
| `id_kategori`       | BIGINT UNSIGNED | FK, INDEX | No       | Kategori perkara      |
| `judul_perkara`     | VARCHAR(150)    |           | No       | Judul perkara         |
| `kronologi`         | TEXT            |           | No       | Kronologi perkara     |
| `status_pengajuan`  | VARCHAR(50)     | INDEX     | No       | Status pengajuan      |
| `tanggal_pengajuan` | DATETIME        | INDEX     | No       | Tanggal pengajuan     |
| `created_at`        | TIMESTAMP       |           | Yes      | Timestamp Laravel     |
| `updated_at`        | TIMESTAMP       |           | Yes      | Timestamp Laravel     |

Relasi:

```text
pra_pendaftaran_perkara.id_user → users.id_user
pra_pendaftaran_perkara.id_kategori → kategori_perkara.id_kategori
```

Aturan:

1. `id_user` adalah Klien yang membuat pengajuan.
2. Status awal pengajuan adalah `menunggu_verifikasi`.
3. Status awal wajib dicatat pada `riwayat_status`.
4. Klien tidak boleh mengubah data pengajuan setelah dikirim.
5. Perubahan status harus mengikuti `docs/STATUS_RULES.md`.
6. Jangan mengganti `id_pendaftaran` menjadi `pra_pendaftaran_perkara_id`.

---

## 5. `dokumen_perkara`

Fungsi:

Menyimpan metadata dokumen pendukung perkara.

| Field            | Type            | Key       | Nullable | Description               |
| ---------------- | --------------- | --------- | -------- | ------------------------- |
| `id_dokumen`     | BIGINT UNSIGNED | PK        | No       | ID dokumen                |
| `id_pendaftaran` | BIGINT UNSIGNED | FK, INDEX | No       | Relasi ke pra-pendaftaran |
| `nama_dokumen`   | VARCHAR(150)    |           | No       | Nama dokumen              |
| `jenis_dokumen`  | VARCHAR(50)     |           | No       | Jenis dokumen             |
| `file_path`      | VARCHAR(255)    |           | No       | Lokasi file pada storage  |
| `status_dokumen` | VARCHAR(50)     | INDEX     | No       | Status dokumen            |
| `created_at`     | TIMESTAMP       |           | Yes      | Timestamp upload pertama  |
| `updated_at`     | TIMESTAMP       |           | Yes      | Timestamp update data     |

Relasi:

```text
dokumen_perkara.id_pendaftaran → pra_pendaftaran_perkara.id_pendaftaran
```

Aturan:

1. File fisik disimpan di Laravel Storage.
2. Database hanya menyimpan metadata dan `file_path`.
3. Tidak menggunakan kolom `uploaded_at`.
4. Waktu upload menggunakan `created_at`.
5. File lama tidak boleh ditimpa saat unggah ulang.
6. Dokumen lama diberi status `diganti` saat diganti oleh dokumen baru.
7. Dokumen baru diberi status `terkirim`.
8. Jangan mengganti `id_dokumen` menjadi `dokumen_perkara_id`.

---

## 6. `verifikasi_berkas`

Fungsi:

Menyimpan data hasil verifikasi berkas oleh Staf Legal.

| Field                | Type            | Key       | Nullable | Description                          |
| -------------------- | --------------- | --------- | -------- | ------------------------------------ |
| `id_verifikasi`      | BIGINT UNSIGNED | PK        | No       | ID verifikasi                        |
| `id_pendaftaran`     | BIGINT UNSIGNED | FK, INDEX | No       | Relasi ke pengajuan                  |
| `id_user`            | BIGINT UNSIGNED | FK, INDEX | No       | Staf Legal yang melakukan verifikasi |
| `status_verifikasi`  | VARCHAR(50)     | INDEX     | No       | Status hasil verifikasi              |
| `tanggal_verifikasi` | DATETIME        | INDEX     | No       | Tanggal verifikasi                   |
| `catatan_umum`       | TEXT            |           | Yes      | Catatan umum verifikasi              |
| `created_at`         | TIMESTAMP       |           | Yes      | Timestamp Laravel                    |
| `updated_at`         | TIMESTAMP       |           | Yes      | Timestamp Laravel                    |

Relasi:

```text
verifikasi_berkas.id_pendaftaran → pra_pendaftaran_perkara.id_pendaftaran
verifikasi_berkas.id_user → users.id_user
```

Aturan:

1. `id_user` adalah Staf Legal yang melakukan verifikasi.
2. Status verifikasi mengikuti `docs/STATUS_RULES.md`.
3. Catatan umum boleh kosong.
4. Catatan per dokumen disimpan pada `catatan_verifikasi`.
5. Jangan mengganti `id_verifikasi` menjadi `verifikasi_berkas_id`.

---

## 7. `catatan_verifikasi`

Fungsi:

Menyimpan catatan verifikasi umum atau catatan per dokumen.

| Field              | Type            | Key       | Nullable | Description                 |
| ------------------ | --------------- | --------- | -------- | --------------------------- |
| `id_catatan`       | BIGINT UNSIGNED | PK        | No       | ID catatan                  |
| `id_verifikasi`    | BIGINT UNSIGNED | FK, INDEX | No       | Relasi ke verifikasi        |
| `id_dokumen`       | BIGINT UNSIGNED | FK, INDEX | Yes      | Relasi ke dokumen, nullable |
| `isi_catatan`      | TEXT            |           | No       | Isi catatan                 |
| `status_perbaikan` | VARCHAR(50)     | INDEX     | No       | Status perbaikan            |
| `created_at`       | TIMESTAMP       |           | Yes      | Timestamp Laravel           |
| `updated_at`       | TIMESTAMP       |           | Yes      | Timestamp Laravel           |

Relasi:

```text
catatan_verifikasi.id_verifikasi → verifikasi_berkas.id_verifikasi
catatan_verifikasi.id_dokumen → dokumen_perkara.id_dokumen
```

Aturan:

1. `id_dokumen` nullable.
2. Catatan umum utama disimpan pada kolom `verifikasi_berkas.catatan_umum`.
3. Tabel `catatan_verifikasi` terutama digunakan untuk catatan per dokumen.
4. Jika `id_dokumen` bernilai null, catatan tersebut boleh dianggap sebagai catatan umum tambahan.
5. AI agent tidak boleh menduplikasi isi `verifikasi_berkas.catatan_umum` yang sama ke tabel `catatan_verifikasi`.
6. Jika `id_dokumen` terisi, catatan bersifat per dokumen.
7. Status perbaikan mengikuti `docs/STATUS_RULES.md`.
8. Saat Klien mengunggah ulang dokumen terkait, status perbaikan dapat berubah menjadi `sudah_diperbaiki`.
9. Jangan mengganti `id_catatan` menjadi `catatan_verifikasi_id`.

---

## 8. `riwayat_status`

Fungsi:

Menyimpan riwayat perubahan status pengajuan perkara.

| Field            | Type            | Key       | Nullable | Description                   |
| ---------------- | --------------- | --------- | -------- | ----------------------------- |
| `id_riwayat`     | BIGINT UNSIGNED | PK        | No       | ID riwayat                    |
| `id_pendaftaran` | BIGINT UNSIGNED | FK, INDEX | No       | Relasi ke pengajuan           |
| `id_user`        | BIGINT UNSIGNED | FK, INDEX | No       | Pengguna yang mengubah status |
| `status`         | VARCHAR(50)     | INDEX     | No       | Status baru                   |
| `keterangan`     | TEXT            |           | Yes      | Keterangan perubahan status   |
| `created_at`     | TIMESTAMP       |           | Yes      | Waktu perubahan status        |
| `updated_at`     | TIMESTAMP       |           | Yes      | Timestamp Laravel             |

Relasi:

```text
riwayat_status.id_pendaftaran → pra_pendaftaran_perkara.id_pendaftaran
riwayat_status.id_user → users.id_user
```

Aturan:

1. Tidak menggunakan kolom `tanggal_status`.
2. Waktu perubahan status menggunakan `created_at`.
3. Setiap perubahan `status_pengajuan` wajib dicatat di tabel ini.
4. `id_user` adalah pengguna yang menyebabkan perubahan status.
5. Jangan mengganti `id_riwayat` menjadi `riwayat_status_id`.

---

## 9. `jadwal_konsultasi`

Fungsi:

Menyimpan slot jadwal konsultasi yang dibuat oleh Admin.

| Field           | Type            | Key       | Nullable | Description          |
| --------------- | --------------- | --------- | -------- | -------------------- |
| `id_jadwal`     | BIGINT UNSIGNED | PK        | No       | ID jadwal            |
| `id_user`       | BIGINT UNSIGNED | FK, INDEX | No       | Admin pembuat jadwal |
| `tanggal`       | DATE            | INDEX     | No       | Tanggal konsultasi   |
| `waktu_mulai`   | TIME            |           | No       | Waktu mulai          |
| `waktu_selesai` | TIME            |           | No       | Waktu selesai        |
| `status_slot`   | VARCHAR(30)     | INDEX     | No       | Status slot jadwal   |
| `created_at`    | TIMESTAMP       |           | Yes      | Timestamp Laravel    |
| `updated_at`    | TIMESTAMP       |           | Yes      | Timestamp Laravel    |

Relasi:

```text
jadwal_konsultasi.id_user → users.id_user
```

Aturan:

1. `id_user` adalah Admin pembuat jadwal.
2. Status slot mengikuti `docs/STATUS_RULES.md`.
3. Slot yang berhasil dipilih Klien berubah menjadi `terisi`.
4. Slot yang sudah `terisi` tidak boleh dipilih ulang.
5. Jangan mengganti `id_jadwal` menjadi `jadwal_konsultasi_id`.

---

## 10. `booking_konsultasi`

Fungsi:

Menyimpan data booking konsultasi Klien.

| Field             | Type            | Key       | Nullable | Description                  |
| ----------------- | --------------- | --------- | -------- | ---------------------------- |
| `id_booking`      | BIGINT UNSIGNED | PK        | No       | ID booking                   |
| `id_pendaftaran`  | BIGINT UNSIGNED | FK, INDEX | No       | Relasi ke pengajuan          |
| `id_jadwal`       | BIGINT UNSIGNED | FK, INDEX | No       | Relasi ke jadwal             |
| `id_user`         | BIGINT UNSIGNED | FK, INDEX | No       | Klien yang melakukan booking |
| `status_booking`  | VARCHAR(30)     | INDEX     | No       | Status booking               |
| `tanggal_booking` | DATETIME        | INDEX     | No       | Tanggal booking              |
| `created_at`      | TIMESTAMP       |           | Yes      | Timestamp Laravel            |
| `updated_at`      | TIMESTAMP       |           | Yes      | Timestamp Laravel            |

Relasi:

```text
booking_konsultasi.id_pendaftaran → pra_pendaftaran_perkara.id_pendaftaran
booking_konsultasi.id_jadwal → jadwal_konsultasi.id_jadwal
booking_konsultasi.id_user → users.id_user
```

Aturan:

1. `id_user` adalah Klien yang melakukan booking.
2. Booking baru memiliki status awal `aktif`.
3. `tanggal_booking` wajib diisi saat booking dibuat.
4. Satu pengajuan hanya boleh memiliki satu booking aktif.
5. Satu slot jadwal yang sudah `terisi` tidak boleh dipilih ulang.
6. Setelah booking berhasil, status pengajuan menjadi `jadwal_dipilih`.
7. Perubahan status wajib dicatat pada `riwayat_status`.
8. Jangan mengganti `id_booking` menjadi `booking_konsultasi_id`.

Catatan constraint:

Karena MySQL tidak mendukung partial unique index sederhana untuk “satu booking aktif”, aturan satu booking aktif lebih aman diterapkan pada service layer menggunakan transaction dan pengecekan status. Unique constraint tambahan hanya boleh dibuat setelah direview.

---

# Foreign Key Summary

## Foreign Keys Used

Gunakan foreign key berikut sesuai kebutuhan:

1. `id_user`
2. `id_kategori`
3. `id_pendaftaran`
4. `id_dokumen`
5. `id_verifikasi`
6. `id_jadwal`

Jangan mengganti foreign key menjadi format default Laravel seperti:

1. `user_id`
2. `category_id`
3. `pra_pendaftaran_perkara_id`
4. `dokumen_perkara_id`
5. `verifikasi_berkas_id`
6. `jadwal_konsultasi_id`
7. `booking_konsultasi_id`

---

## Relationship Summary

Relasi utama:

1. `users` has one `profil_klien`.
2. `users` has many `pra_pendaftaran_perkara` as Klien.
3. `users` has many `verifikasi_berkas` as Staf Legal.
4. `users` has many `jadwal_konsultasi` as Admin.
5. `users` has many `booking_konsultasi` as Klien.
6. `users` has many `riwayat_status` as pengubah status.
7. `kategori_perkara` has many `pra_pendaftaran_perkara`.
8. `pra_pendaftaran_perkara` has many `dokumen_perkara`.
9. `pra_pendaftaran_perkara` has many `verifikasi_berkas`.
10. `pra_pendaftaran_perkara` has many `riwayat_status`.
11. `pra_pendaftaran_perkara` has one active `booking_konsultasi`.
12. `verifikasi_berkas` has many `catatan_verifikasi`.
13. `dokumen_perkara` has many `catatan_verifikasi`.
14. `jadwal_konsultasi` has zero or one active `booking_konsultasi`.

Detail implementasi relasi Eloquent ditulis pada:

```text
docs/MODEL_RELATION_PLAN.md
```

---

# Index and Constraint Recommendations

Gunakan index pada kolom yang sering digunakan untuk filter dan relasi:

1. `users.email` unique.
2. `users.role`.
3. `users.status_akun`.
4. `profil_klien.id_user` unique.
5. `pra_pendaftaran_perkara.id_user`.
6. `pra_pendaftaran_perkara.id_kategori`.
7. `pra_pendaftaran_perkara.status_pengajuan`.
8. `pra_pendaftaran_perkara.tanggal_pengajuan`.
9. `dokumen_perkara.id_pendaftaran`.
10. `dokumen_perkara.status_dokumen`.
11. `verifikasi_berkas.id_pendaftaran`.
12. `verifikasi_berkas.id_user`.
13. `verifikasi_berkas.status_verifikasi`.
14. `catatan_verifikasi.id_verifikasi`.
15. `catatan_verifikasi.id_dokumen`.
16. `catatan_verifikasi.status_perbaikan`.
17. `riwayat_status.id_pendaftaran`.
18. `riwayat_status.id_user`.
19. `riwayat_status.status`.
20. `jadwal_konsultasi.id_user`.
21. `jadwal_konsultasi.tanggal`.
22. `jadwal_konsultasi.status_slot`.
23. `booking_konsultasi.id_pendaftaran`.
24. `booking_konsultasi.id_jadwal`.
25. `booking_konsultasi.id_user`.
26. `booking_konsultasi.status_booking`.
27. `booking_konsultasi.tanggal_booking`.

Jangan menambahkan unique constraint untuk aturan bisnis yang masih harus fleksibel tanpa persetujuan.

---

# Status and Role Reference

Nilai role dan status tidak didefinisikan sebagai ENUM pada database.

Semua nilai status dan role mengacu pada:

```text
docs/STATUS_RULES.md
```

Database hanya menyimpan slug.

UI menampilkan label manusia.

---

# Transaction Requirements

Gunakan database transaction untuk proses berikut:

1. Membuat pra-pendaftaran perkara beserta dokumen dan riwayat status.
2. Verifikasi berkas oleh Staf Legal.
3. Membuat catatan verifikasi umum atau per dokumen.
4. Mengunggah ulang dokumen.
5. Booking jadwal konsultasi.
6. Mengubah status pengajuan dan mencatat riwayat status.

Jika salah satu proses gagal, seluruh perubahan harus rollback.

---

# Report Rules

Laporan pra-pendaftaran perkara tidak menggunakan tabel khusus.

Laporan dibuat dari query data:

1. `pra_pendaftaran_perkara`
2. `users`
3. `kategori_perkara`
4. `verifikasi_berkas`
5. `booking_konsultasi`

Filter laporan:

1. Tanggal.
2. Status pengajuan.
3. Kategori perkara.

Output laporan:

1. Tampilan tabel.
2. Print browser.

Jangan membuat tabel:

```text
laporan
```

---

# Data Deletion Rules

Karena data berkaitan dengan proses hukum, penghapusan data harus hati-hati.

Aturan:

1. Jangan menghapus data pra-pendaftaran secara otomatis.
2. Jangan menghapus dokumen perkara tanpa izin.
3. Jangan menghapus kategori yang sudah digunakan.
4. Jangan menghapus user yang sudah memiliki data pengajuan, verifikasi, jadwal, booking, atau riwayat status.
5. Jika data perlu dinonaktifkan, gunakan mekanisme yang sudah tersedia pada rancangan database.
6. Jika mekanisme belum tersedia, jangan menambah kolom baru tanpa persetujuan.

---

# Final Notes for AI Agent

AI agent wajib mengikuti aturan berikut:

1. Jangan membuat tabel baru tanpa persetujuan.
2. Jangan membuat kolom baru tanpa persetujuan.
3. Jangan mengubah nama tabel.
4. Jangan mengubah nama kolom.
5. Jangan menggunakan `id` default Laravel sebagai primary key.
6. Jangan menggunakan foreign key default Laravel seperti `user_id`.
7. Jangan menggunakan database ENUM.
8. Jangan membuat tabel laporan.
9. Jangan menjalankan migration sebelum direview.
10. Jangan menerapkan `cascadeOnDelete()` secara otomatis.
11. Selalu cocokkan database dengan `AGENTS.md`, `PROJECT_CONTEXT.md`, `MODEL_RELATION_PLAN.md`, dan `STATUS_RULES.md`.
