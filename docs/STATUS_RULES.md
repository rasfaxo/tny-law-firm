# STATUS_RULES.md

## Purpose

Dokumen ini berisi aturan role, status, dan transisi status untuk project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Dokumen ini wajib diikuti saat membuat migration, model, validation, controller, service, middleware, UI label, filter laporan, dan logic perubahan status.

AI agent tidak boleh membuat status baru, role baru, atau transisi status baru tanpa persetujuan pemilik project.

---

## Source of Truth

Dokumen ini harus konsisten dengan:

1. `AGENTS.md`
2. `docs/PROJECT_CONTEXT.md`
3. `docs/DATABASE_PLAN.md`
4. `docs/MODEL_RELATION_PLAN.md`
5. `docs/VALIDATION_RULES.md`
6. `docs/SECURITY_RULES.md`
7. `docs/FEATURE_LIST.md`
8. `docs/ROUTES_PLAN.md`

Jika ada konflik struktur tabel, ikuti:

```text
docs/DATABASE_PLAN.md
```

Jika ada konflik proses validasi, ikuti:

```text
docs/VALIDATION_RULES.md
```

---

## General Status Rules

Aturan umum:

1. Semua role dan status disimpan sebagai slug lowercase.
2. Jangan menyimpan label UI ke database.
3. Jangan menggunakan database `ENUM`.
4. Semua status menggunakan `VARCHAR`.
5. Label status untuk tampilan dibuat di layer aplikasi.
6. Jangan membuat status baru tanpa persetujuan.
7. Jangan membuat transisi status baru tanpa persetujuan.
8. Semua perubahan `status_pengajuan` wajib dicatat pada `riwayat_status`.
9. Field waktu perubahan status menggunakan `created_at` pada `riwayat_status`.
10. Jangan menambahkan kolom `tanggal_status`.

---

# Role Rules

## User Role

Kolom:

```text
users.role
```

Role yang valid:

| Slug         | Label UI   | Keterangan                                                   |
| ------------ | ---------- | ------------------------------------------------------------ |
| `klien`      | Klien      | Pengguna yang mengajukan pra-pendaftaran perkara             |
| `admin`      | Admin      | Pengelola sistem, data master, jadwal, pengguna, dan laporan |
| `staf_legal` | Staf Legal | Petugas yang melakukan verifikasi berkas perkara             |

Aturan:

1. Registrasi publik hanya boleh membuat role `klien`.
2. Admin dapat membuat akun `staf_legal`.
3. Role `admin` awal dapat dibuat melalui seeder.
4. Jangan membuat role baru di luar daftar valid.
5. Jangan menerima role dari form registrasi publik.
6. Role digunakan untuk middleware dan pembatasan akses fitur.

---

## Account Status

Kolom:

```text
users.status_akun
```

Status akun yang valid:

| Slug       | Label UI | Keterangan                                        |
| ---------- | -------- | ------------------------------------------------- |
| `aktif`    | Aktif    | Akun dapat login dan mengakses sistem sesuai role |
| `nonaktif` | Nonaktif | Akun tidak dapat login atau mengakses sistem      |

Aturan:

1. Registrasi publik menghasilkan `status_akun = aktif`.
2. Akun yang dibuat Admin dapat diberi status `aktif` atau `nonaktif`.
3. Akun `nonaktif` tidak boleh login.
4. Jangan membuat status akun baru tanpa persetujuan.

---

# Pra-Pendaftaran Status Rules

## Status Pengajuan

Kolom:

```text
pra_pendaftaran_perkara.status_pengajuan
```

Status pengajuan yang valid:

| Slug                        | Label UI                  | Keterangan                                                         |
| --------------------------- | ------------------------- | ------------------------------------------------------------------ |
| `menunggu_verifikasi`       | Menunggu Verifikasi       | Pengajuan baru dikirim dan menunggu pemeriksaan Staf Legal         |
| `berkas_tidak_lengkap`      | Berkas Tidak Lengkap      | Berkas dinyatakan belum lengkap dan perlu perbaikan                |
| `menunggu_verifikasi_ulang` | Menunggu Verifikasi Ulang | Klien sudah mengunggah ulang dokumen dan menunggu verifikasi ulang |
| `berkas_lengkap`            | Berkas Lengkap            | Berkas sudah valid dan Klien dapat memilih jadwal konsultasi       |
| `jadwal_dipilih`            | Jadwal Dipilih            | Klien sudah memilih jadwal konsultasi                              |
| `selesai`                   | Selesai                   | Konsultasi atau proses pra-pendaftaran telah diselesaikan          |

---

## Status Pengajuan Transition Flow

Transisi status yang valid:

```text
menunggu_verifikasi
    -> berkas_lengkap
    -> jadwal_dipilih
    -> selesai
```

```text
menunggu_verifikasi
    -> berkas_tidak_lengkap
    -> menunggu_verifikasi_ulang
    -> berkas_lengkap
    -> jadwal_dipilih
    -> selesai
```

Transisi detail:

| Dari                        | Ke                          | Dipicu Oleh    | Keterangan                                    |
| --------------------------- | --------------------------- | -------------- | --------------------------------------------- |
| -                           | `menunggu_verifikasi`       | Klien / Sistem | Pengajuan baru dibuat                         |
| `menunggu_verifikasi`       | `berkas_lengkap`            | Staf Legal     | Berkas dinyatakan lengkap                     |
| `menunggu_verifikasi`       | `berkas_tidak_lengkap`      | Staf Legal     | Berkas dinyatakan tidak lengkap               |
| `berkas_tidak_lengkap`      | `menunggu_verifikasi_ulang` | Klien / Sistem | Klien melakukan unggah ulang dokumen          |
| `menunggu_verifikasi_ulang` | `berkas_lengkap`            | Staf Legal     | Berkas hasil unggah ulang dinyatakan lengkap  |
| `menunggu_verifikasi_ulang` | `berkas_tidak_lengkap`      | Staf Legal     | Berkas hasil unggah ulang masih tidak lengkap |
| `berkas_lengkap`            | `jadwal_dipilih`            | Klien / Sistem | Klien memilih jadwal konsultasi               |
| `jadwal_dipilih`            | `selesai`                   | Admin / Sistem | Admin menandai konsultasi selesai             |

Aturan:

1. Pengajuan baru selalu dimulai dari `menunggu_verifikasi`.
2. Klien hanya boleh memilih jadwal jika status pengajuan `berkas_lengkap`.
3. Setelah Klien memilih jadwal, status pengajuan menjadi `jadwal_dipilih`.
4. Setelah Admin menandai konsultasi selesai, status pengajuan menjadi `selesai`.
5. Semua perubahan status pengajuan wajib masuk ke `riwayat_status`.
6. Jangan mengubah status pengajuan langsung dari request Klien.
7. Status pengajuan ditentukan server berdasarkan proses bisnis.
8. Jangan melewati alur status tanpa alasan yang disetujui.

---

# Dokumen Perkara Status Rules

## Status Dokumen

Kolom:

```text
dokumen_perkara.status_dokumen
```

Status dokumen yang valid:

| Slug              | Label UI        | Keterangan                                      |
| ----------------- | --------------- | ----------------------------------------------- |
| `terkirim`        | Terkirim        | Dokumen baru diunggah oleh Klien                |
| `valid`           | Valid           | Dokumen sudah diverifikasi dan dinyatakan valid |
| `perlu_perbaikan` | Perlu Perbaikan | Dokumen perlu diperbaiki atau diunggah ulang    |
| `diganti`         | Diganti         | Dokumen lama sudah diganti oleh dokumen baru    |

---

## Status Dokumen Transition Flow

Transisi valid:

| Dari              | Ke                | Dipicu Oleh    | Keterangan                                   |
| ----------------- | ----------------- | -------------- | -------------------------------------------- |
| -                 | `terkirim`        | Klien / Sistem | Dokumen pertama kali diunggah                |
| `terkirim`        | `valid`           | Staf Legal     | Dokumen dinyatakan benar                     |
| `terkirim`        | `perlu_perbaikan` | Staf Legal     | Dokumen bermasalah dan perlu diperbaiki      |
| `perlu_perbaikan` | `diganti`         | Klien / Sistem | Klien mengunggah ulang dokumen pengganti     |
| -                 | `terkirim`        | Klien / Sistem | Record dokumen baru dibuat saat unggah ulang |

Aturan:

1. Dokumen baru selalu berstatus `terkirim`.
2. Dokumen yang valid diberi status `valid`.
3. Dokumen yang bermasalah diberi status `perlu_perbaikan`.
4. Saat Klien re-upload, dokumen lama menjadi `diganti`.
5. Saat Klien re-upload, dokumen baru dibuat sebagai record baru dengan status `terkirim`.
6. File lama tidak boleh ditimpa.
7. Jangan membuat status dokumen baru tanpa persetujuan.

---

# Verifikasi Berkas Status Rules

## Status Verifikasi

Kolom:

```text
verifikasi_berkas.status_verifikasi
```

Status verifikasi yang valid:

| Slug                   | Label UI             | Keterangan                                       |
| ---------------------- | -------------------- | ------------------------------------------------ |
| `berkas_lengkap`       | Berkas Lengkap       | Hasil verifikasi menyatakan berkas lengkap       |
| `berkas_tidak_lengkap` | Berkas Tidak Lengkap | Hasil verifikasi menyatakan berkas belum lengkap |

Aturan:

1. Verifikasi hanya dilakukan oleh role `staf_legal`.
2. `id_user` pada `verifikasi_berkas` adalah Staf Legal yang melakukan verifikasi.
3. `tanggal_verifikasi` diisi oleh server.
4. Jika `status_verifikasi = berkas_lengkap`, maka `status_pengajuan` menjadi `berkas_lengkap`.
5. Jika `status_verifikasi = berkas_tidak_lengkap`, maka `status_pengajuan` menjadi `berkas_tidak_lengkap`.
6. Jika berkas tidak lengkap, wajib ada `catatan_umum` atau minimal satu catatan per dokumen.
7. Catatan umum utama disimpan pada `verifikasi_berkas.catatan_umum`.
8. Catatan per dokumen disimpan pada `catatan_verifikasi`.
9. Proses verifikasi wajib menggunakan database transaction.

---

# Catatan Verifikasi Status Rules

## Status Perbaikan

Kolom:

```text
catatan_verifikasi.status_perbaikan
```

Status perbaikan yang valid:

| Slug               | Label UI         | Keterangan                                                    |
| ------------------ | ---------------- | ------------------------------------------------------------- |
| `belum_diperbaiki` | Belum Diperbaiki | Catatan perbaikan belum ditindaklanjuti Klien                 |
| `sudah_diperbaiki` | Sudah Diperbaiki | Klien sudah mengunggah ulang dokumen terkait catatan tersebut |

Aturan:

1. Catatan baru untuk dokumen bermasalah memiliki status `belum_diperbaiki`.
2. Saat Klien mengunggah ulang dokumen terkait, status catatan berubah menjadi `sudah_diperbaiki`.
3. `id_dokumen` pada `catatan_verifikasi` boleh nullable untuk catatan tambahan umum jika diperlukan.
4. Catatan umum utama tetap disimpan pada `verifikasi_berkas.catatan_umum`.
5. Jangan menggandakan catatan umum yang sama pada `catatan_verifikasi`.
6. Jangan membuat status perbaikan baru tanpa persetujuan.

---

# Jadwal Konsultasi Status Rules

## Status Slot Jadwal

Kolom:

```text
jadwal_konsultasi.status_slot
```

Status slot yang valid:

| Slug          | Label UI    | Keterangan                                      |
| ------------- | ----------- | ----------------------------------------------- |
| `tersedia`    | Tersedia    | Jadwal dapat dipilih Klien                      |
| `terisi`      | Terisi      | Jadwal sudah dipilih dan memiliki booking aktif |
| `tidak_aktif` | Tidak Aktif | Jadwal tidak dapat dipilih                      |

Aturan:

1. Jadwal baru default berstatus `tersedia`.
2. Admin boleh membuat jadwal dengan status `tersedia` atau `tidak_aktif`.
3. Admin tidak boleh memilih status `terisi` saat membuat jadwal baru.
4. Status `terisi` hanya diberikan oleh sistem setelah booking berhasil.
5. Jadwal dengan status `terisi` tidak boleh dipilih lagi oleh Klien.
6. Jadwal dengan status `tidak_aktif` tidak boleh dipilih oleh Klien.
7. Jadwal yang sudah memiliki booking aktif tidak boleh diubah sembarangan.
8. Jangan membuat status slot baru tanpa persetujuan.

---

# Booking Konsultasi Status Rules

## Status Booking

Kolom:

```text
booking_konsultasi.status_booking
```

Status booking yang valid:

| Slug         | Label UI   | Keterangan                                                          |
| ------------ | ---------- | ------------------------------------------------------------------- |
| `aktif`      | Aktif      | Booking konsultasi aktif dan sedang menunggu pelaksanaan konsultasi |
| `dibatalkan` | Dibatalkan | Booking dibatalkan jika fitur pembatalan disetujui                  |
| `selesai`    | Selesai    | Konsultasi sudah selesai                                            |

---

## Status Booking Transition Flow

Transisi valid:

| Dari    | Ke           | Dipicu Oleh           | Keterangan                                  |
| ------- | ------------ | --------------------- | ------------------------------------------- |
| -       | `aktif`      | Klien / Sistem        | Booking dibuat setelah Klien memilih jadwal |
| `aktif` | `selesai`    | Admin / Sistem        | Admin menandai konsultasi selesai           |
| `aktif` | `dibatalkan` | Sistem / Role terkait | Hanya jika fitur pembatalan disetujui       |

Aturan:

1. Booking baru selalu berstatus `aktif`.
2. Booking hanya boleh dibuat jika `status_pengajuan = berkas_lengkap`.
3. Booking hanya boleh dibuat pada jadwal dengan `status_slot = tersedia`.
4. Satu pengajuan hanya boleh memiliki satu booking aktif.
5. Satu slot jadwal hanya boleh memiliki satu booking aktif.
6. Setelah booking berhasil, status jadwal menjadi `terisi`.
7. Setelah booking berhasil, status pengajuan menjadi `jadwal_dipilih`.
8. Setelah konsultasi selesai, status booking menjadi `selesai`.
9. Setelah konsultasi selesai, status pengajuan terkait menjadi `selesai`.
10. Perubahan status pengajuan wajib dicatat pada `riwayat_status`.
11. Fitur pembatalan booking tidak dibuat pada fase awal kecuali disetujui.
12. Jika booking `selesai`, slot tidak otomatis menjadi `tersedia` kembali kecuali ada aturan baru yang disetujui.

---

# Riwayat Status Rules

## Riwayat Status

Tabel:

```text
riwayat_status
```

Kolom status:

```text
riwayat_status.status
```

Aturan:

1. Setiap perubahan `pra_pendaftaran_perkara.status_pengajuan` wajib membuat record `riwayat_status`.
2. `id_pendaftaran` berisi pengajuan yang statusnya berubah.
3. `id_user` berisi user yang melakukan atau memicu perubahan status.
4. `status` berisi status pengajuan terbaru.
5. `keterangan` boleh diisi deskripsi perubahan status.
6. Waktu perubahan memakai `created_at`.
7. Jangan menambahkan kolom `tanggal_status`.
8. Jangan membuat riwayat status untuk status dokumen, status slot, atau status booking kecuali perubahan tersebut juga mengubah `status_pengajuan`.

Contoh keterangan:

| Status                      | Contoh Keterangan                                    |
| --------------------------- | ---------------------------------------------------- |
| `menunggu_verifikasi`       | Pengajuan pra-pendaftaran berhasil dikirim           |
| `berkas_tidak_lengkap`      | Berkas perlu diperbaiki berdasarkan hasil verifikasi |
| `menunggu_verifikasi_ulang` | Klien telah mengunggah ulang dokumen                 |
| `berkas_lengkap`            | Berkas dinyatakan lengkap oleh Staf Legal            |
| `jadwal_dipilih`            | Klien telah memilih jadwal konsultasi                |
| `selesai`                   | Konsultasi telah diselesaikan oleh Admin             |

---

# Status Label UI

Status disimpan di database sebagai slug. Label untuk UI dibuat di aplikasi.

Contoh mapping label:

```php
[
    'menunggu_verifikasi' => 'Menunggu Verifikasi',
    'berkas_tidak_lengkap' => 'Berkas Tidak Lengkap',
    'menunggu_verifikasi_ulang' => 'Menunggu Verifikasi Ulang',
    'berkas_lengkap' => 'Berkas Lengkap',
    'jadwal_dipilih' => 'Jadwal Dipilih',
    'selesai' => 'Selesai',
]
```

Aturan:

1. Database menyimpan slug.
2. UI menampilkan label.
3. Jangan menyimpan label seperti `Menunggu Verifikasi` ke database.
4. Jangan mencampur huruf besar/kecil pada status database.

---

# Validation Rules Reference

Gunakan validasi server-side untuk memastikan status valid.

Contoh validasi role:

```php
'role' => ['required', Rule::in(['klien', 'admin', 'staf_legal'])]
```

Contoh validasi status akun:

```php
'status_akun' => ['required', Rule::in(['aktif', 'nonaktif'])]
```

Contoh validasi status pengajuan:

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

Catatan:

1. Jangan menerima status penting dari request publik.
2. Status penting harus ditentukan oleh server berdasarkan proses bisnis.
3. Input status dari Admin atau Staf Legal tetap harus divalidasi.
4. Transisi status harus dicek, bukan hanya mengecek apakah status masuk daftar valid.

---

# Transaction Rules

Gunakan database transaction pada proses yang mengubah lebih dari satu tabel.

Wajib transaction untuk:

1. Membuat pra-pendaftaran, dokumen, dan riwayat status.
2. Verifikasi berkas, update status pengajuan, update status dokumen, catatan verifikasi, dan riwayat status.
3. Unggah ulang dokumen, update dokumen lama, dokumen baru, update catatan, update status pengajuan, dan riwayat status.
4. Booking konsultasi, update status jadwal, update status pengajuan, dan riwayat status.
5. Penyelesaian konsultasi, update booking, update status pengajuan, dan riwayat status.

---

# Forbidden Status Practices

AI agent tidak boleh:

1. Membuat role baru tanpa persetujuan.
2. Membuat status baru tanpa persetujuan.
3. Membuat transisi status baru tanpa persetujuan.
4. Menggunakan database `ENUM`.
5. Menyimpan label UI ke database.
6. Mengubah status pengajuan tanpa mencatat `riwayat_status`.
7. Menerima `status_pengajuan` dari form Klien.
8. Menerima `status_booking` dari form Klien.
9. Membuat status slot `terisi` dari form create jadwal Admin.
10. Mengubah status jadwal menjadi `tersedia` jika masih ada booking aktif.
11. Menghapus atau menimpa dokumen lama saat re-upload.
12. Membuat fitur pembatalan booking tanpa persetujuan.
13. Membuat status `selesai` tanpa aturan transisi yang jelas.
14. Membuat field `tanggal_status`.
15. Membuat field `uploaded_at`.

---

# Final Notes for AI Agent

AI agent wajib mengikuti aturan berikut:

1. Gunakan slug lowercase untuk semua role dan status.
2. Gunakan label hanya untuk tampilan UI.
3. Jangan membuat status baru.
4. Jangan membuat role baru.
5. Jangan membuat transisi baru.
6. Validasi semua status menggunakan daftar valid.
7. Perubahan `status_pengajuan` wajib dicatat di `riwayat_status`.
8. Proses multi-tabel wajib menggunakan transaction.
9. Cocokkan implementasi dengan `DATABASE_PLAN.md`, `MODEL_RELATION_PLAN.md`, dan `VALIDATION_RULES.md`.
