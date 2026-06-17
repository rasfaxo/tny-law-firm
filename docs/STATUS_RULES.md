`MODEL_RELATION_PLAN.md` kita lanjutkan sebagai **dikunci**, dengan catatan tetap mengikuti `DATABASE_PLAN.md` jika ada konflik.

Commit dulu:

```bash id="wrp55b"
git add docs/MODEL_RELATION_PLAN.md
git commit -m "docs: add model relation plan"
```

Sekarang lanjut ke file berikutnya: **`docs/STATUS_RULES.md`**.

Buat file:

```bash id="yclvfp"
touch docs/STATUS_RULES.md
```

Lalu isi dengan draft berikut.

# STATUS_RULES.md

## Purpose

Dokumen ini berisi aturan role, status, label status, nilai status yang valid, dan transisi status untuk project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Dokumen ini wajib diikuti saat membuat validation rule, controller, service, seeder, middleware, query filter, badge status, dan tampilan UI.

AI agent tidak boleh membuat role atau status baru tanpa persetujuan pemilik project.

---

## General Rules

Aturan umum:

1. Semua role dan status disimpan di database dalam bentuk slug lowercase.
2. Jangan menggunakan database `ENUM`.
3. Role dan status menggunakan tipe `VARCHAR`.
4. Validasi nilai role dan status dilakukan pada level aplikasi Laravel.
5. Label status untuk tampilan UI dibuat di level aplikasi.
6. Jangan menyimpan label UI seperti `Menunggu Verifikasi` langsung ke database.
7. Jangan membuat status baru tanpa persetujuan.
8. Setiap perubahan `status_pengajuan` wajib dicatat pada tabel `riwayat_status`.
9. Transisi status harus mengikuti aturan pada dokumen ini.
10. Jika ada konflik dengan `DATABASE_PLAN.md`, ikuti `DATABASE_PLAN.md`.

---

# Role Rules

## Role User

Role disimpan pada:

```text
users.role
```

Nilai role yang valid:

| Slug         | Label UI   | Deskripsi                                        |
| ------------ | ---------- | ------------------------------------------------ |
| `klien`      | Klien      | Pengguna yang mengajukan pra-pendaftaran perkara |
| `admin`      | Admin      | Pengguna yang mengelola data utama sistem        |
| `staf_legal` | Staf Legal | Pengguna yang memverifikasi berkas perkara       |

Aturan:

1. Registrasi publik hanya boleh membuat akun dengan role `klien`.
2. Akun Admin awal dapat dibuat melalui seeder.
3. Akun Staf Legal dibuat atau dikelola oleh Admin.
4. User tidak boleh mengubah role miliknya sendiri melalui form publik.
5. Role harus digunakan untuk middleware dan pembatasan akses halaman.

---

# Status Akun

Status akun disimpan pada:

```text
users.status_akun
```

Nilai status akun yang valid:

| Slug       | Label UI | Deskripsi                              |
| ---------- | -------- | -------------------------------------- |
| `aktif`    | Aktif    | Akun dapat digunakan untuk login       |
| `nonaktif` | Nonaktif | Akun tidak dapat digunakan untuk login |

Aturan:

1. Status default registrasi publik adalah `aktif`.
2. Akun dengan status `nonaktif` tidak boleh mengakses sistem.
3. Perubahan status akun hanya boleh dilakukan oleh Admin.
4. Jangan membuat status akun baru tanpa persetujuan.

---

# Status Pengajuan

Status pengajuan disimpan pada:

```text
pra_pendaftaran_perkara.status_pengajuan
```

Nilai status pengajuan yang valid:

| Slug                        | Label UI                  | Deskripsi                                                           |
| --------------------------- | ------------------------- | ------------------------------------------------------------------- |
| `menunggu_verifikasi`       | Menunggu Verifikasi       | Pengajuan baru dikirim dan menunggu pemeriksaan Staf Legal          |
| `berkas_tidak_lengkap`      | Berkas Tidak Lengkap      | Berkas sudah diperiksa tetapi masih ada kekurangan                  |
| `menunggu_verifikasi_ulang` | Menunggu Verifikasi Ulang | Klien sudah mengunggah ulang dokumen dan menunggu pemeriksaan ulang |
| `berkas_lengkap`            | Berkas Lengkap            | Berkas sudah dinyatakan lengkap oleh Staf Legal                     |
| `jadwal_dipilih`            | Jadwal Dipilih            | Klien sudah memilih jadwal konsultasi                               |
| `selesai`                   | Selesai                   | Proses pra-pendaftaran dan konsultasi dinyatakan selesai            |

Aturan:

1. Status awal pengajuan baru adalah `menunggu_verifikasi`.
2. Status awal wajib dicatat pada `riwayat_status`.
3. Setiap perubahan status wajib dicatat pada `riwayat_status`.
4. Klien tidak boleh mengubah status pengajuan secara langsung.
5. Staf Legal dapat mengubah status melalui proses verifikasi.
6. Sistem dapat mengubah status saat Klien mengunggah ulang dokumen atau memilih jadwal konsultasi.
7. Admin dapat melihat status pengajuan untuk kebutuhan monitoring dan laporan.
8. Status disimpan sebagai slug, bukan label UI.

---

## Transisi Status Pengajuan

Transisi status yang valid:

| Dari Status                 | Ke Status                   | Pemicu                                        | Aktor          |
| --------------------------- | --------------------------- | --------------------------------------------- | -------------- |
| -                           | `menunggu_verifikasi`       | Klien membuat pengajuan baru                  | Klien / Sistem |
| `menunggu_verifikasi`       | `berkas_lengkap`            | Berkas dinyatakan lengkap                     | Staf Legal     |
| `menunggu_verifikasi`       | `berkas_tidak_lengkap`      | Berkas dinyatakan tidak lengkap               | Staf Legal     |
| `berkas_tidak_lengkap`      | `menunggu_verifikasi_ulang` | Klien mengunggah ulang dokumen                | Klien / Sistem |
| `menunggu_verifikasi_ulang` | `berkas_lengkap`            | Berkas hasil unggah ulang dinyatakan lengkap  | Staf Legal     |
| `menunggu_verifikasi_ulang` | `berkas_tidak_lengkap`      | Berkas hasil unggah ulang masih belum lengkap | Staf Legal     |
| `berkas_lengkap`            | `jadwal_dipilih`            | Klien memilih jadwal konsultasi               | Klien / Sistem |
| `jadwal_dipilih`            | `selesai`                   | Proses konsultasi diselesaikan                | Admin / Sistem |

Transisi yang tidak boleh dilakukan:

1. `menunggu_verifikasi` langsung ke `jadwal_dipilih`.
2. `berkas_tidak_lengkap` langsung ke `berkas_lengkap` tanpa proses verifikasi ulang.
3. `berkas_tidak_lengkap` langsung ke `jadwal_dipilih`.
4. `menunggu_verifikasi_ulang` langsung ke `jadwal_dipilih`.
5. `berkas_lengkap` kembali ke `menunggu_verifikasi` tanpa alasan dan persetujuan.
6. `selesai` kembali ke status sebelumnya tanpa persetujuan.

---

# Status Dokumen

Status dokumen disimpan pada:

```text
dokumen_perkara.status_dokumen
```

Nilai status dokumen yang valid:

| Slug              | Label UI        | Deskripsi                                          |
| ----------------- | --------------- | -------------------------------------------------- |
| `terkirim`        | Terkirim        | Dokumen berhasil diunggah dan menunggu pemeriksaan |
| `valid`           | Valid           | Dokumen sudah diperiksa dan dinyatakan valid       |
| `perlu_perbaikan` | Perlu Perbaikan | Dokumen bermasalah dan perlu diunggah ulang        |
| `diganti`         | Diganti         | Dokumen lama sudah diganti dengan dokumen baru     |

Aturan:

1. Status awal dokumen baru adalah `terkirim`.
2. Jika dokumen dinyatakan benar, status dapat menjadi `valid`.
3. Jika dokumen bermasalah, status dapat menjadi `perlu_perbaikan`.
4. Jika Klien mengunggah ulang dokumen, dokumen lama diberi status `diganti`.
5. Dokumen baru hasil unggah ulang diberi status `terkirim`.
6. File lama tidak boleh ditimpa.
7. Status dokumen tidak boleh menggunakan label UI.

---

## Transisi Status Dokumen

Transisi status dokumen yang valid:

| Dari Status       | Ke Status         | Pemicu                              | Aktor          |
| ----------------- | ----------------- | ----------------------------------- | -------------- |
| -                 | `terkirim`        | Dokumen pertama kali diunggah       | Klien / Sistem |
| `terkirim`        | `valid`           | Dokumen dinyatakan valid            | Staf Legal     |
| `terkirim`        | `perlu_perbaikan` | Dokumen bermasalah                  | Staf Legal     |
| `perlu_perbaikan` | `diganti`         | Klien mengunggah dokumen pengganti  | Klien / Sistem |
| -                 | `terkirim`        | Dokumen pengganti berhasil diunggah | Klien / Sistem |
| `terkirim`        | `perlu_perbaikan` | Dokumen pengganti masih bermasalah  | Staf Legal     |
| `terkirim`        | `valid`           | Dokumen pengganti dinyatakan valid  | Staf Legal     |

Aturan tambahan:

1. Dokumen dengan status `diganti` tidak boleh digunakan sebagai dokumen aktif.
2. Dokumen dengan status `valid` tidak perlu diunggah ulang.
3. Dokumen dengan status `perlu_perbaikan` menjadi dasar Klien untuk unggah ulang.

---

# Status Verifikasi Berkas

Status verifikasi disimpan pada:

```text
verifikasi_berkas.status_verifikasi
```

Nilai status verifikasi yang valid:

| Slug                   | Label UI             | Deskripsi                                        |
| ---------------------- | -------------------- | ------------------------------------------------ |
| `berkas_lengkap`       | Berkas Lengkap       | Hasil verifikasi menyatakan berkas lengkap       |
| `berkas_tidak_lengkap` | Berkas Tidak Lengkap | Hasil verifikasi menyatakan berkas belum lengkap |

Aturan:

1. Status verifikasi hanya dibuat melalui proses verifikasi oleh Staf Legal.
2. Jika status verifikasi `berkas_lengkap`, maka `status_pengajuan` menjadi `berkas_lengkap`.
3. Jika status verifikasi `berkas_tidak_lengkap`, maka `status_pengajuan` menjadi `berkas_tidak_lengkap`.
4. Verifikasi wajib mencatat `tanggal_verifikasi`.
5. Catatan umum utama disimpan pada `verifikasi_berkas.catatan_umum`.
6. Catatan per dokumen disimpan pada `catatan_verifikasi`.
7. Jangan menduplikasi catatan umum yang sama ke dua tempat.

---

# Status Perbaikan Catatan

Status perbaikan disimpan pada:

```text
catatan_verifikasi.status_perbaikan
```

Nilai status perbaikan yang valid:

| Slug               | Label UI         | Deskripsi                                            |
| ------------------ | ---------------- | ---------------------------------------------------- |
| `belum_diperbaiki` | Belum Diperbaiki | Catatan belum ditindaklanjuti oleh Klien             |
| `sudah_diperbaiki` | Sudah Diperbaiki | Klien sudah mengunggah ulang dokumen terkait catatan |

Aturan:

1. Jika catatan dibuat karena dokumen bermasalah, status awal adalah `belum_diperbaiki`.
2. Saat Klien mengunggah ulang dokumen terkait, status berubah menjadi `sudah_diperbaiki`.
3. Catatan umum tambahan tanpa dokumen juga dapat memiliki status perbaikan, tetapi tidak boleh menduplikasi `catatan_umum`.
4. Perubahan status perbaikan harus dilakukan dalam proses unggah ulang dokumen jika relevan.

---

# Status Slot Jadwal

Status slot jadwal disimpan pada:

```text
jadwal_konsultasi.status_slot
```

Nilai status slot yang valid:

| Slug          | Label UI    | Deskripsi                                     |
| ------------- | ----------- | --------------------------------------------- |
| `tersedia`    | Tersedia    | Slot dapat dipilih oleh Klien                 |
| `terisi`      | Terisi      | Slot sudah dipilih dan memiliki booking aktif |
| `tidak_aktif` | Tidak Aktif | Slot tidak tersedia untuk dipilih             |

Aturan:

1. Status awal slot jadwal baru adalah `tersedia`.
2. Slot dengan status `tersedia` dapat dipilih oleh Klien yang pengajuannya `berkas_lengkap`.
3. Slot yang berhasil dipilih berubah menjadi `terisi`.
4. Slot `terisi` tidak boleh dipilih ulang.
5. Slot `tidak_aktif` tidak boleh dipilih.
6. Perubahan status slot dilakukan oleh Admin atau sistem sesuai proses booking.

---

# Status Booking Konsultasi

Status booking disimpan pada:

```text
booking_konsultasi.status_booking
```

Nilai status booking yang valid:

| Slug         | Label UI   | Deskripsi                        |
| ------------ | ---------- | -------------------------------- |
| `aktif`      | Aktif      | Booking konsultasi masih aktif   |
| `dibatalkan` | Dibatalkan | Booking dibatalkan               |
| `selesai`    | Selesai    | Booking konsultasi sudah selesai |

Aturan:

1. Status awal booking baru adalah `aktif`.
2. `tanggal_booking` wajib diisi saat booking dibuat.
3. Satu pengajuan hanya boleh memiliki satu booking aktif.
4. Satu slot jadwal hanya boleh memiliki satu booking aktif.
5. Booking hanya dapat dibuat jika status pengajuan adalah `berkas_lengkap`.
6. Setelah booking berhasil, status pengajuan menjadi `jadwal_dipilih`.
7. Setelah booking berhasil, status slot jadwal menjadi `terisi`.
8. Perubahan status pengajuan wajib dicatat pada `riwayat_status`.

---

## Transisi Status Booking Konsultasi

Transisi status booking yang valid:

| Dari Status | Ke Status    | Pemicu                                                   | Aktor          |
| ----------- | ------------ | -------------------------------------------------------- | -------------- |
| -           | `aktif`      | Klien berhasil memilih jadwal konsultasi                 | Klien / Sistem |
| `aktif`     | `selesai`    | Konsultasi telah selesai dilakukan                       | Admin / Sistem |
| `aktif`     | `dibatalkan` | Booking dibatalkan sesuai persetujuan atau aturan sistem | Admin / Sistem |

Aturan:

1. Booking baru selalu dibuat dengan status `aktif`.
2. Status `selesai` digunakan jika konsultasi sudah selesai.
3. Status `dibatalkan` hanya boleh digunakan jika fitur pembatalan disetujui atau diimplementasikan.
4. Jika booking menjadi `selesai`, status pengajuan dapat berubah menjadi `selesai` dan wajib dicatat pada `riwayat_status`.
5. Slot jadwal yang sudah pernah digunakan untuk booking tidak boleh digunakan ulang tanpa aturan khusus yang disetujui.
6. Jika fitur pembatalan belum masuk fase implementasi, AI agent tidak boleh membuat fitur pembatalan booking secara otomatis.

---

# Riwayat Status Rules

Riwayat status disimpan pada:

```text
riwayat_status
```

Kolom status disimpan pada:

```text
riwayat_status.status
```

Aturan:

1. Setiap perubahan `pra_pendaftaran_perkara.status_pengajuan` wajib membuat record baru pada `riwayat_status`.
2. Tidak menggunakan kolom `tanggal_status`.
3. Waktu perubahan status menggunakan `created_at`.
4. `id_user` adalah user yang menyebabkan perubahan status.
5. Jika perubahan dilakukan otomatis oleh sistem karena aksi user, `id_user` tetap menggunakan user yang melakukan aksi.
6. `keterangan` boleh diisi untuk menjelaskan perubahan status.
7. Status pada `riwayat_status.status` harus sama dengan status baru pada `pra_pendaftaran_perkara.status_pengajuan`.

Contoh penggunaan:

| Aksi                                       | Status Baru                 | id_user       |
| ------------------------------------------ | --------------------------- | ------------- |
| Klien membuat pengajuan                    | `menunggu_verifikasi`       | ID Klien      |
| Staf Legal menyatakan berkas lengkap       | `berkas_lengkap`            | ID Staf Legal |
| Staf Legal menyatakan berkas tidak lengkap | `berkas_tidak_lengkap`      | ID Staf Legal |
| Klien mengunggah ulang dokumen             | `menunggu_verifikasi_ulang` | ID Klien      |
| Klien memilih jadwal konsultasi            | `jadwal_dipilih`            | ID Klien      |
| Admin menyelesaikan proses                 | `selesai`                   | ID Admin      |

---

# UI Label Mapping

AI agent boleh membuat helper, enum-like class, constant class, atau config file untuk mapping label UI, selama tidak menggunakan database ENUM.

Contoh mapping:

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

1. Mapping label hanya untuk tampilan UI.
2. Database tetap menyimpan slug.
3. Jangan menyimpan label UI ke database.
4. Jangan membuat status baru hanya untuk kebutuhan warna badge.
5. Warna badge UI tidak boleh menjadi nilai database.

---

# Validation Rules

Setiap input status harus divalidasi menggunakan daftar slug yang sah.

Contoh Laravel validation:

```php
'in:menunggu_verifikasi,berkas_tidak_lengkap,menunggu_verifikasi_ulang,berkas_lengkap,jadwal_dipilih,selesai'
```

Jika menggunakan constant class, validasi boleh menggunakan daftar dari constant tersebut.

Aturan:

1. Jangan menerima status bebas dari request tanpa validasi.
2. Jangan mempercayai status dari hidden input.
3. Status penting harus ditentukan oleh server berdasarkan proses bisnis.
4. Role penting harus ditentukan oleh server berdasarkan otorisasi.

---

# Transaction Rules for Status Changes

Perubahan status harus menggunakan database transaction jika melibatkan lebih dari satu tabel.

Wajib transaction untuk:

1. Membuat pengajuan baru:

   * `pra_pendaftaran_perkara`
   * `dokumen_perkara`
   * `riwayat_status`

2. Verifikasi berkas:

   * `verifikasi_berkas`
   * `catatan_verifikasi`
   * `dokumen_perkara`
   * `pra_pendaftaran_perkara`
   * `riwayat_status`

3. Unggah ulang dokumen:

   * `dokumen_perkara`
   * `catatan_verifikasi`
   * `pra_pendaftaran_perkara`
   * `riwayat_status`

4. Booking konsultasi:

   * `booking_konsultasi`
   * `jadwal_konsultasi`
   * `pra_pendaftaran_perkara`
   * `riwayat_status`

Jika salah satu perubahan gagal, semua perubahan harus rollback.

---

# Forbidden Status Actions

AI agent tidak boleh:

1. Membuat status baru tanpa persetujuan.
2. Menggunakan database `ENUM`.
3. Menyimpan label UI ke database.
4. Mengubah status pengajuan tanpa mencatat `riwayat_status`.
5. Mengubah status berdasarkan input user tanpa validasi server-side.
6. Mengizinkan Klien mengubah status pengajuan secara langsung.
7. Mengizinkan booking jika status pengajuan belum `berkas_lengkap`.
8. Mengizinkan slot `terisi` dipilih ulang.
9. Mengizinkan dokumen lama ditimpa saat unggah ulang.
10. Menghapus riwayat status untuk menyembunyikan perubahan.

---

# Final Notes for AI Agent

AI agent wajib mengikuti aturan berikut:

1. Gunakan slug lowercase untuk database.
2. Gunakan label manusia hanya untuk UI.
3. Validasi semua status pada server-side.
4. Catat semua perubahan status pengajuan ke `riwayat_status`.
5. Gunakan transaction untuk proses multi-tabel.
6. Jangan membuat status, role, atau transisi baru tanpa persetujuan.
7. Selalu cocokkan implementasi status dengan `DATABASE_PLAN.md`, `MODEL_RELATION_PLAN.md`, `VALIDATION_RULES.md`, dan `SECURITY_RULES.md`.

Setelah kamu validasi, baru kita revisi dan kunci `STATUS_RULES.md`.
