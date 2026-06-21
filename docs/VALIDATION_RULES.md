# VALIDATION_RULES.md

## Purpose

Dokumen ini berisi aturan validasi input untuk project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Dokumen ini wajib diikuti saat membuat form, Form Request, controller, service, file upload, filter laporan, dan proses perubahan status.

AI agent tidak boleh menerima input bebas dari user tanpa validasi server-side.

---

## Source of Truth

Dokumen ini harus konsisten dengan:

1. `AGENTS.md`
2. `docs/PROJECT_CONTEXT.md`
3. `docs/DATABASE_PLAN.md`
4. `docs/MODEL_RELATION_PLAN.md`
5. `docs/STATUS_RULES.md`
6. `docs/SECURITY_RULES.md`
7. `docs/FEATURE_LIST.md`

Jika ada konflik nilai status atau struktur database, ikuti:

```text
docs/DATABASE_PLAN.md
docs/STATUS_RULES.md
```

---

## General Validation Rules

Aturan umum validasi:

1. Semua input dari user wajib divalidasi di server-side.
2. Validasi frontend boleh ditambahkan, tetapi tidak menggantikan validasi backend.
3. Gunakan Form Request untuk input yang kompleks.
4. Jangan mempercayai hidden input untuk role, status, user ID, atau file path.
5. Role penting harus ditentukan oleh server.
6. Status penting harus ditentukan oleh server berdasarkan proses bisnis.
7. `id_user` untuk aksi Klien harus menggunakan `auth()->id()`, bukan dari request.
8. File path tidak boleh berasal langsung dari input user.
9. Semua ID dari URL harus dicek keberadaan data dan otorisasinya.
10. Input teks harus dibatasi panjangnya sesuai database.
11. Semua nilai status harus mengikuti `docs/STATUS_RULES.md`.
12. Semua proses multi-tabel harus mengikuti aturan transaction pada `DATABASE_PLAN.md`.

---

## Recommended Laravel Validation Style

Gunakan Form Request jika input berasal dari form utama.

Contoh:

```php
public function rules(): array
{
    return [
        'judul_perkara' => ['required', 'string', 'max:150'],
        'kronologi' => ['required', 'string'],
    ];
}
```

Gunakan validasi eksplisit di controller hanya untuk validasi sederhana atau filter.

Jangan membuat validasi status seperti ini:

```php
'status_pengajuan' => ['required', 'string']
```

Gunakan daftar status yang sah:

```php
'status_pengajuan' => [
    'required',
    Rule::in([
        'menunggu_verifikasi',
        'berkas_tidak_lengkap',
        'menunggu_verifikasi_ulang',
        'berkas_lengkap',
        'jadwal_dipilih',
        'selesai',
    ]),
]
```

---

# Authentication Validation

## Register Klien

Form registrasi publik hanya untuk Klien.

Field:

| Field        | Validation                                   |
| ------------ | -------------------------------------------- |
| `nama`       | required, string, max:100                    |
| `email`      | required, email, max:100, unique:users,email |
| `password`   | required, confirmed, min:8                   |
| `no_telepon` | nullable, string, max:20                     |

Aturan:

1. Role registrasi publik selalu `klien`.
2. `role` tidak boleh diterima dari form registrasi publik.
3. `status_akun` default adalah `aktif`.
4. Password harus disimpan dalam bentuk hash.
5. Jangan menggunakan field `name`; gunakan `nama`.

Contoh nilai server-side:

```php
'role' => 'klien',
'status_akun' => 'aktif',
```

---

## Login

Field:

| Field      | Validation       |
| ---------- | ---------------- |
| `email`    | required, email  |
| `password` | required, string |

Aturan:

1. Login menggunakan `email` dan `password`.
2. Akun dengan `status_akun = nonaktif` tidak boleh masuk sistem.
3. Setelah login, user diarahkan berdasarkan `role`.
4. Role harus salah satu dari `klien`, `admin`, atau `staf_legal`.

---

## Forgot Password

Field:

| Field   | Validation                      |
| ------- | ------------------------------- |
| `email` | required, email, exists:users,email |

Aturan:

1. Email harus terdaftar pada akun yang valid.
2. Proses forgot password mengikuti Laravel Breeze.
3. Email hanya digunakan untuk reset password.

---

## Reset Password

Field:

| Field                   | Validation                        |
| ----------------------- | --------------------------------- |
| `token`                 | required, string                  |
| `email`                 | required, email, exists:users,email |
| `password`              | required, confirmed, min:8        |
| `password_confirmation` | required_with:password             |

Aturan:

1. Token harus valid dan belum expired sesuai mekanisme Laravel Breeze.
2. Password baru wajib di-hash sebelum disimpan.
3. Reset password hanya untuk pemulihan password akun.

---

# User and Account Validation

## Admin Membuat Akun Staf Legal

Field:

| Field         | Validation                                   |
| ------------- | -------------------------------------------- |
| `nama`        | required, string, max:100                    |
| `email`       | required, email, max:100, unique:users,email |
| `password`    | required, confirmed, min:8                   |
| `no_telepon`  | nullable, string, max:20                     |
| `status_akun` | required, in:aktif,nonaktif                  |

Aturan:

1. Hanya Admin yang boleh membuat akun Staf Legal.
2. Role akun yang dibuat melalui fitur ini adalah `staf_legal`.
3. Jangan menerima role bebas dari request.
4. Jika role perlu dipilih oleh Admin, validasi harus menggunakan daftar role yang sah.
5. Admin awal dapat dibuat melalui seeder.

---

## Admin Mengubah User

Field yang dapat diubah:

| Field         | Validation                                                         |
| ------------- | ------------------------------------------------------------------ |
| `nama`        | required, string, max:100                                          |
| `email`       | required, email, max:100, unique:users,email kecuali user saat ini |
| `no_telepon`  | nullable, string, max:20                                           |
| `status_akun` | required, in:aktif,nonaktif                                        |

Aturan:

1. Email harus tetap unique.
2. Jangan mengubah password tanpa form khusus.
3. Jangan mengubah role tanpa aturan khusus.
4. Admin tidak boleh membuat role di luar `klien`, `admin`, `staf_legal`.
5. User yang sudah memiliki data penting tidak boleh dihapus sembarangan.

---

# Profil Klien Validation

## Create or Update Profil Klien

Field:

| Field           | Validation                |
| --------------- | ------------------------- |
| `alamat`        | nullable, string          |
| `jenis_kelamin` | nullable, string, max:20  |
| `pekerjaan`     | nullable, string, max:100 |
| `no_identitas`  | nullable, string, max:50  |

Aturan:

1. Klien hanya boleh membuat atau mengubah profil miliknya sendiri.
2. `id_user` tidak boleh berasal dari request.
3. `id_user` harus menggunakan `auth()->id()`.
4. Satu Klien hanya boleh memiliki satu profil.
5. Perubahan profil tidak boleh mengubah role, email, password, atau status akun.

---

# Kategori Perkara Validation

## Create Kategori Perkara

Field:

| Field           | Validation                |
| --------------- | ------------------------- |
| `nama_kategori` | required, string, max:100 |
| `deskripsi`     | nullable, string          |

Aturan:

1. Hanya Admin yang boleh membuat kategori perkara.
2. `nama_kategori` tidak boleh kosong.
3. Tidak ada field `status_kategori`.
4. Tidak ada field `is_active`.
5. Jangan menambahkan soft delete tanpa persetujuan.

---

## Update Kategori Perkara

Field:

| Field           | Validation                |
| --------------- | ------------------------- |
| `nama_kategori` | required, string, max:100 |
| `deskripsi`     | nullable, string          |

Aturan:

1. Hanya Admin yang boleh mengubah kategori perkara.
2. Kategori yang sudah digunakan tetap boleh diperbaiki namanya jika diperlukan, tetapi harus hati-hati agar tidak merusak konteks laporan.
3. Jangan menambahkan field baru untuk menonaktifkan kategori tanpa revisi database.

---

## Delete Kategori Perkara

Aturan validasi sebelum delete:

1. Kategori hanya boleh dihapus jika belum pernah digunakan pada `pra_pendaftaran_perkara`.
2. Jika kategori sudah digunakan, proses delete harus ditolak.
3. Jangan menghapus kategori yang sudah memiliki relasi pengajuan.
4. Jangan menggunakan soft delete kecuali database direvisi dan disetujui.

---

# Pra-Pendaftaran Perkara Validation

## Create Pra-Pendaftaran Perkara

Field:

| Field                     | Validation                                       |
| ------------------------- | ------------------------------------------------ |
| `id_kategori`             | required, exists:kategori_perkara,id_kategori    |
| `judul_perkara`           | required, string, max:150                        |
| `kronologi`               | required, string                                 |
| `dokumen`                 | required, array                                  |
| `dokumen.*.nama_dokumen`  | required, string, max:150                        |
| `dokumen.*.jenis_dokumen` | required, string, max:50                         |
| `dokumen.*.file`          | required, file, max:5120, mimes:pdf,jpg,jpeg,png |

Aturan:

1. `id_user` harus menggunakan `auth()->id()`.
2. `status_pengajuan` tidak boleh berasal dari request.
3. Status awal pengajuan selalu `menunggu_verifikasi`.
4. `tanggal_pengajuan` diisi oleh server.
5. Status awal wajib dicatat pada `riwayat_status`.
6. Proses create pengajuan, upload dokumen, dan create riwayat status wajib menggunakan database transaction.
7. Klien tidak boleh mengubah isi pengajuan setelah dikirim.
8. File dokumen harus divalidasi extension, MIME type, dan ukuran.
9. File dokumen disimpan menggunakan nama unik atau random.
10. File lama tidak boleh ditimpa.

---

## File Upload Validation

Format dokumen yang diperbolehkan:

| Format | Extension |
| ------ | --------- |
| PDF    | `.pdf`    |
| JPG    | `.jpg`    |
| JPEG   | `.jpeg`   |
| PNG    | `.png`    |

Ukuran maksimal:

```text
5 MB per file
```

Laravel validation:

```php
'file' => [
    'required',
    'file',
    'max:5120',
    'mimes:pdf,jpg,jpeg,png',
]
```


Jika diperlukan, tambahkan validasi MIME type eksplisit:

```php
'mimetypes:application/pdf,image/jpeg,image/png'
```


Validasi file tetap harus disertai pemeriksaan otorisasi akses dokumen.

Extension file, MIME type, ukuran file, dan kepemilikan data harus diperiksa sebelum file disimpan.

Aturan:

1. Maksimal file adalah 5 MB per file.
2. File harus disimpan di `storage/app/public/dokumen-perkara`.
3. Nama file harus random atau unik.
4. Jangan menggunakan nama asli file sebagai nama final.
5. Nama asli file boleh disimpan sebagai metadata hanya jika diperlukan, tetapi tidak menjadi nama file utama.
6. Jangan menerima `file_path` dari request.
7. Jangan menyimpan file langsung di database.
8. Jangan menimpa file lama saat re-upload.
9. Akses file harus dilindungi oleh authorization.

---

# Status dan Riwayat Validation

## Status Pengajuan

Status valid:

```text
menunggu_verifikasi
berkas_tidak_lengkap
menunggu_verifikasi_ulang
berkas_lengkap
jadwal_dipilih
selesai
```

Aturan:

1. Klien tidak boleh mengirim `status_pengajuan` melalui form.
2. Status ditentukan oleh server berdasarkan proses bisnis.
3. Setiap perubahan `status_pengajuan` wajib dicatat pada `riwayat_status`.
4. Status pada `riwayat_status.status` harus sama dengan status baru pada `pra_pendaftaran_perkara.status_pengajuan`.

---

## Riwayat Status

Field:

| Field        | Validation                                 |
| ------------ | ------------------------------------------ |
| `status`     | required, in daftar status pengajuan valid |
| `keterangan` | nullable, string                           |

Aturan:

1. `id_pendaftaran` berasal dari data pengajuan yang sedang diproses.
2. `id_user` berasal dari user yang melakukan aksi.
3. Tidak ada field `tanggal_status`.
4. Waktu perubahan status menggunakan `created_at`.
5. Jangan membuat riwayat status tanpa perubahan status yang sah.

---

# Verifikasi Berkas Validation

## Create Verifikasi Berkas

Field:

| Field                           | Validation                                                       |
| ------------------------------- | ---------------------------------------------------------------- |
| `status_verifikasi`             | required, in:berkas_lengkap,berkas_tidak_lengkap                 |
| `catatan_umum`                  | nullable, string                                                 |
| `catatan_dokumen`               | nullable, array                                                  |
| `catatan_dokumen.*.id_dokumen`  | required_with:catatan_dokumen, exists:dokumen_perkara,id_dokumen |
| `catatan_dokumen.*.isi_catatan` | required_with:catatan_dokumen, string                            |
| `dokumen_status`                | nullable, array                                                  |
| `dokumen_status.*`              | nullable, in:valid,perlu_perbaikan                               |


Catatan format `dokumen_status`:

`dokumen_status` sebaiknya menggunakan format key-value dengan `id_dokumen` sebagai key dan status sebagai value.

Contoh: 
```php 
'dokumen_status' => [ 
    12 => 'valid',
    13 => 'perlu_perbaikan',
]
```

Setiap key pada dokumen_status wajib divalidasi sebagai id_dokumen yang termasuk dalam pengajuan yang sedang diverifikasi.

Status dokumen tidak boleh diterapkan ke dokumen milik pengajuan lain.

Aturan:

1. Hanya Staf Legal yang boleh melakukan verifikasi.
2. `id_user` verifikator harus menggunakan `auth()->id()`.
3. `id_pendaftaran` berasal dari route atau query yang sudah dicek.
4. `tanggal_verifikasi` diisi oleh server.
5. Jika `status_verifikasi = berkas_lengkap`, maka `status_pengajuan` menjadi `berkas_lengkap`.
6. Jika `status_verifikasi = berkas_tidak_lengkap`, maka `status_pengajuan` menjadi `berkas_tidak_lengkap`.
7. Jika dokumen valid, `status_dokumen` dapat menjadi `valid`.
8. Jika dokumen bermasalah, `status_dokumen` menjadi `perlu_perbaikan`.
9. Catatan umum utama disimpan di `verifikasi_berkas.catatan_umum`.
10. Catatan per dokumen disimpan di `catatan_verifikasi`.
11. Jangan menduplikasi catatan umum yang sama ke `catatan_verifikasi`.
12. Verifikasi wajib menggunakan database transaction.
13. Perubahan status pengajuan wajib dicatat pada `riwayat_status`.
14. Jika `status_verifikasi = berkas_tidak_lengkap`, maka wajib ada `catatan_umum` atau minimal satu `catatan_dokumen`.
15. Jika ada dokumen yang diberi status `perlu_perbaikan`, maka dokumen tersebut wajib memiliki catatan perbaikan.
16. Setiap `id_dokumen` pada catatan atau status dokumen wajib dipastikan milik `id_pendaftaran` yang sedang diverifikasi.

---

## Catatan Verifikasi Validation

Field:

| Field              | Validation                                     |
| ------------------ | ---------------------------------------------- |
| `id_dokumen`       | nullable, exists:dokumen_perkara,id_dokumen    |
| `isi_catatan`      | required, string                               |
| `status_perbaikan` | required, in:belum_diperbaiki,sudah_diperbaiki |

Aturan:

1. Catatan per dokumen harus mengacu pada dokumen milik pengajuan yang sedang diverifikasi.
2. Jangan menerima `id_dokumen` yang berasal dari pengajuan lain.
3. Status awal catatan perbaikan adalah `belum_diperbaiki`.
4. `sudah_diperbaiki` hanya digunakan setelah Klien mengunggah ulang dokumen terkait.
5. Catatan umum utama lebih baik disimpan pada `verifikasi_berkas.catatan_umum`.
6. `catatan_verifikasi` dengan `id_dokumen = null` hanya digunakan sebagai catatan umum tambahan jika diperlukan.

---

# Unggah Ulang Dokumen Validation

## Re-upload Dokumen

Field:

| Field        | Validation                                       |
| ------------ | ------------------------------------------------ |
| `id_dokumen` | required, exists:dokumen_perkara,id_dokumen      |
| `file`       | required, file, max:5120, mimes:pdf,jpg,jpeg,png |

Aturan:

1. Klien hanya boleh re-upload dokumen milik pengajuannya sendiri.
2. Re-upload hanya boleh dilakukan jika dokumen berstatus `perlu_perbaikan`.
3. Re-upload hanya boleh dilakukan jika ada catatan perbaikan terkait.
4. File lama tidak boleh ditimpa.
5. Dokumen lama diberi status `diganti`.
6. Dokumen baru dibuat sebagai record baru dengan status `terkirim`.
7. Catatan verifikasi terkait diperbarui menjadi `sudah_diperbaiki`.
8. Status pengajuan berubah menjadi `menunggu_verifikasi_ulang`.
9. Perubahan status wajib dicatat pada `riwayat_status`.
10. Proses re-upload wajib menggunakan database transaction.
11. Jangan menerima `file_path` dari request.
12. Jangan mengubah dokumen milik Klien lain.

---

# Jadwal Konsultasi Validation

## Create Jadwal Konsultasi

Field:

| Field           | Validation                                   |
| --------------- | -------------------------------------------- |
| `tanggal`       | required, date                               |
| `waktu_mulai`   | required, date_format:H:i                    |
| `waktu_selesai` | required, date_format:H:i, after:waktu_mulai |
| `status_slot`   | nullable, in:tersedia,tidak_aktif               |

Aturan:

1. Hanya Admin yang boleh membuat jadwal konsultasi.
2. `id_user` pembuat jadwal harus menggunakan `auth()->id()`.
3. Status awal jadwal baru sebaiknya `tersedia`.
4. Jangan menerima `id_user` dari request.
5. `waktu_selesai` harus lebih besar dari `waktu_mulai`.
6. Slot yang bentrok pada tanggal dan jam yang sama harus dicegah jika aturan ini diterapkan.
7. Status slot harus mengikuti `docs/STATUS_RULES.md`.
8. Status `terisi` tidak boleh dipilih saat membuat jadwal baru.
9. Status `terisi` hanya boleh diberikan oleh sistem setelah booking berhasil.

---

## Update Jadwal Konsultasi

Field:

| Field           | Validation                                   |
| --------------- | -------------------------------------------- |
| `tanggal`       | required, date                               |
| `waktu_mulai`   | required, date_format:H:i                    |
| `waktu_selesai` | required, date_format:H:i, after:waktu_mulai |
| `status_slot`   | required, in:tersedia,terisi,tidak_aktif     |

Aturan:

1. Hanya Admin yang boleh mengubah jadwal.
2. Jadwal yang sudah `terisi` tidak boleh diubah sembarangan.
3. Jadwal yang sudah memiliki booking aktif tidak boleh dihapus sembarangan.
4. Jangan mengubah slot menjadi `tersedia` jika sudah memiliki booking aktif tanpa aturan khusus yang disetujui.
5. Perubahan status slot harus mengikuti `docs/STATUS_RULES.md`.

---

# Booking Konsultasi Validation

## Create Booking Konsultasi

Field:

| Field            | Validation                                              |
| ---------------- | ------------------------------------------------------- |
| `id_pendaftaran` | required, exists:pra_pendaftaran_perkara,id_pendaftaran |
| `id_jadwal`      | required, exists:jadwal_konsultasi,id_jadwal            |

Aturan:

1. Hanya Klien yang boleh membuat booking untuk pengajuan miliknya sendiri.
2. `id_user` booking harus menggunakan `auth()->id()`.
3. Booking hanya boleh dibuat jika `status_pengajuan = berkas_lengkap`.
4. Jadwal hanya boleh dipilih jika `status_slot = tersedia`.
5. Satu pengajuan hanya boleh memiliki satu booking aktif.
6. Satu slot jadwal hanya boleh memiliki satu booking aktif.
7. Booking baru memiliki status `aktif`.
8. `tanggal_booking` diisi oleh server.
9. Setelah booking berhasil, `status_slot` menjadi `terisi`.
10. Setelah booking berhasil, `status_pengajuan` menjadi `jadwal_dipilih`.
11. Perubahan status pengajuan wajib dicatat pada `riwayat_status`.
12. Proses booking wajib menggunakan database transaction.
13. Jangan menerima `status_booking` dari request.
14. Jangan menerima `tanggal_booking` dari request.

---

## Update Status Booking Konsultasi Aturan:

1. Update status booking hanya boleh dilakukan jika fitur tersebut sudah disetujui. 
2. Status booking hanya boleh berubah dari `aktif` ke `selesai` atau `dibatalkan`. 
3. Status `dibatalkan` tidak boleh dibuat otomatis jika fitur pembatalan belum masuk scope implementasi. 
4. Jika booking menjadi `selesai`, maka status pengajuan dapat berubah menjadi `selesai` dan wajib dicatat pada `riwayat_status`. 
5. Perubahan status booking tidak boleh menerima status bebas dari request. 
6. Perubahan status booking harus mengikuti `docs/STATUS_RULES.md`. 
7. Jika perubahan status booking memengaruhi status pengajuan atau status slot jadwal, proses wajib menggunakan database transaction.

---

# Laporan Validation

## Filter Laporan Pra-Pendaftaran

Field:

| Field              | Validation                                    |
| ------------------ | --------------------------------------------- |
| `tanggal_mulai`    | nullable, date                                |
| `tanggal_selesai`  | nullable, date, after_or_equal:tanggal_mulai  |
| `status_pengajuan` | nullable, in daftar status pengajuan valid    |
| `id_kategori`      | nullable, exists:kategori_perkara,id_kategori |
| `keyword`          | nullable, string, max:100                     |

Aturan:

1. Hanya Admin yang boleh mengakses laporan.
2. Laporan dibuat dari query, bukan tabel `laporan`.
3. Jangan membuat tabel `laporan`.
4. Filter tanggal harus divalidasi.
5. Filter status harus menggunakan daftar status valid.
6. Filter kategori harus mengacu ke `kategori_perkara.id_kategori`.
7. Output laporan berupa tabel dan print browser.

---

# Search and Pagination Validation

Aturan:

1. Parameter `keyword` maksimal 100 karakter.
2. Parameter `page` harus mengikuti pagination Laravel.
3. Parameter filter status harus divalidasi.
4. Parameter filter kategori harus divalidasi.
5. Jangan memasukkan input search langsung ke raw SQL.
6. Gunakan query builder atau Eloquent dengan binding parameter.

---

# Forbidden Validation Practices

AI agent tidak boleh:

1. Menerima `role` bebas dari form publik.
2. Menerima `status_pengajuan` bebas dari form Klien.
3. Menerima `id_user` dari form Klien untuk kepemilikan data.
4. Menerima `file_path` dari request.
5. Menggunakan file original name sebagai nama file final.
6. Melewati validasi MIME dan ukuran file.
7. Mengubah status berdasarkan hidden input tanpa validasi server-side.
8. Membuat status baru tanpa mengikuti `STATUS_RULES.md`.
9. Membuat field validasi untuk kolom yang tidak ada di `DATABASE_PLAN.md`.
10. Menjalankan logic multi-tabel tanpa transaction.

---

# Final Notes for AI Agent

AI agent wajib mengikuti aturan berikut:

1. Gunakan Form Request untuk form utama.
2. Gunakan server-side validation untuk semua input.
3. Gunakan daftar status dari `STATUS_RULES.md`.
4. Gunakan struktur kolom dari `DATABASE_PLAN.md`.
5. Jangan menerima role, status penting, file path, atau ID kepemilikan dari request tanpa kontrol server.
6. Validasi file upload secara ketat.
7. Pastikan validasi selalu disertai authorization pada fitur sensitif.
8. Jangan membuat validasi untuk fitur di luar ruang lingkup skripsi.
