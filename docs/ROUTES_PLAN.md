# ROUTES_PLAN.md

## Purpose

Dokumen ini berisi rancangan route untuk project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Dokumen ini wajib diikuti saat membuat route Laravel, controller, middleware role, route name, form action, link navigasi, dan authorization flow.

AI agent tidak boleh membuat route baru di luar rancangan tanpa persetujuan pemilik project.

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

Jika ada konflik akses role, ikuti:

```text
docs/SECURITY_RULES.md
```

Jika ada konflik status atau business rule, ikuti:

```text
docs/STATUS_RULES.md
docs/VALIDATION_RULES.md
```

---

## General Route Rules

Aturan umum route:

1. Semua route utama sistem wajib menggunakan middleware `auth`.
2. Semua route role-based wajib menggunakan middleware role.
3. Route Klien wajib melakukan ownership check.
4. Route dokumen wajib melakukan authorization check.
5. Route aksi yang mengubah data wajib menggunakan method `POST`, `PUT`, `PATCH`, atau `DELETE`.
6. Jangan membuat aksi sensitif menggunakan method `GET`.
7. Jangan hanya menyembunyikan menu di Blade tanpa melindungi route.
8. Jangan membuat route debug di production.
9. Jangan membuat route fitur email, payment, e-Court, export PDF, atau export Excel tanpa persetujuan.
10. Jangan membuat route password reset berbasis email jika fitur email belum disetujui.
11. Nama route harus konsisten dan mudah dipanggil dari Blade.
12. Gunakan prefix route berdasarkan role agar struktur aplikasi rapi.

---

## Route Prefix Convention

Gunakan prefix berikut:

| Role / Area            | Prefix URL    | Route Name Prefix         |
| ---------------------- | ------------- | ------------------------- |
| Public                 | `/`           | `public.` jika diperlukan |
| Authenticated Redirect | `/dashboard`  | `dashboard`               |
| Klien                  | `/klien`      | `klien.`                  |
| Staf Legal             | `/staf-legal` | `staf-legal.`             |
| Admin                  | `/admin`      | `admin.`                  |
| Dokumen Access         | `/dokumen`    | `dokumen.`                |

---

## Middleware Convention

Gunakan middleware:

```php
auth
role:klien
role:staf_legal
role:admin
```

Contoh struktur:

```php
Route::middleware(['auth', 'role:klien'])
    ->prefix('klien')
    ->name('klien.')
    ->group(function () {
        //
    });
```

Route dokumen tetap wajib melakukan authorization check di controller walaupun sudah memakai middleware `auth`.

---

# Public and Authentication Routes

## Public Routes

Route publik hanya untuk halaman yang memang boleh diakses tanpa login.

| Method | URI         | Name           | Controller | Purpose                                       |
| ------ | ----------- | -------------- | ---------- | --------------------------------------------- |
| GET    | `/`         | `home`         | optional   | Halaman awal atau redirect ke login/dashboard |
| GET    | `/login`    | `login`        | Breeze     | Menampilkan form login                        |
| POST   | `/login`    | Breeze default | Breeze     | Proses login                                  |
| GET    | `/register` | `register`     | Breeze     | Menampilkan form registrasi Klien             |
| POST   | `/register` | Breeze default | Breeze     | Proses registrasi Klien                       |

Aturan:

1. Registrasi publik hanya membuat akun role `klien`.
2. Form registrasi tidak boleh menerima `role`.
3. Form registrasi tidak boleh menerima `status_akun`.
4. Password reset berbasis email tidak dibuat pada fase awal kecuali disetujui.
5. Jika route password reset bawaan Breeze muncul, route tersebut harus direview dan boleh dinonaktifkan sampai fitur email disetujui.

---

## Authenticated Authentication Route

Route ini adalah route authentication bawaan Laravel Breeze untuk user yang sedang login.

| Method | URI       | Name     | Controller | Purpose                       |
| ------ | --------- | -------- | ---------- | ----------------------------- |
| POST   | `/logout` | `logout` | Breeze     | Logout user yang sedang login |

Aturan: 
1. `POST /logout` wajib berada di dalam middleware `auth`.
2. `POST /logout` wajib menggunakan CSRF.
3. `POST /logout` hanya relevan untuk user yang sedang login.
4. Logout harus meng-invalidasi session mengikuti mekanisme Laravel Breeze.
5. Logout tidak boleh dianggap sebagai route publik bebas.

---

## Dashboard Redirect

| Method | URI          | Name        | Controller                    | Purpose                                |
| ------ | ------------ | ----------- | ----------------------------- | -------------------------------------- |
| GET    | `/dashboard` | `dashboard` | `DashboardRedirectController` | Redirect user ke dashboard sesuai role |

Aturan:

1. Route ini wajib menggunakan middleware `auth`.
2. Jika role user `klien`, redirect ke `klien.dashboard`.
3. Jika role user `staf_legal`, redirect ke `staf-legal.dashboard`.
4. Jika role user `admin`, redirect ke `admin.dashboard`.
5. Jika role tidak valid, logout atau abort sesuai kebijakan keamanan.

---

# Klien Routes

Prefix:

```text
/klien
```

Name prefix:

```text
klien.
```

Middleware:

```text
auth
role:klien
```

---

## Klien Dashboard

| Method | URI                | Name              | Controller                        | Purpose         |
| ------ | ------------------ | ----------------- | --------------------------------- | --------------- |
| GET    | `/klien/dashboard` | `klien.dashboard` | `Klien\DashboardController@index` | Dashboard Klien |

---

## Profil Klien

| Method    | URI                  | Name                  | Controller                      | Purpose                    |
| --------- | -------------------- | --------------------- | ------------------------------- | -------------------------- |
| GET       | `/klien/profil`      | `klien.profil.show`   | `Klien\ProfilController@show`   | Melihat profil Klien       |
| GET       | `/klien/profil/edit` | `klien.profil.edit`   | `Klien\ProfilController@edit`   | Form edit profil           |
| PUT/PATCH | `/klien/profil`      | `klien.profil.update` | `Klien\ProfilController@update` | Menyimpan perubahan profil |

Aturan:

1. `id_user` profil menggunakan `auth()->id()`.
2. Klien hanya boleh mengakses profil miliknya sendiri.
3. Tidak ada input `role`, `status_akun`, atau `id_user` dari form profil.

---

## Pra-Pendaftaran Perkara Klien

| Method | URI                                  | Name                           | Controller                              | Purpose                      |
| ------ | ------------------------------------ | ------------------------------ | --------------------------------------- | ---------------------------- |
| GET    | `/klien/pra-pendaftaran`             | `klien.pra-pendaftaran.index`  | `Klien\PraPendaftaranController@index`  | Daftar pengajuan milik Klien |
| GET    | `/klien/pra-pendaftaran/create`      | `klien.pra-pendaftaran.create` | `Klien\PraPendaftaranController@create` | Form pengajuan baru          |
| POST   | `/klien/pra-pendaftaran`             | `klien.pra-pendaftaran.store`  | `Klien\PraPendaftaranController@store`  | Simpan pengajuan baru        |
| GET    | `/klien/pra-pendaftaran/{pengajuan}` | `klien.pra-pendaftaran.show`   | `Klien\PraPendaftaranController@show`   | Detail pengajuan milik Klien |

Aturan:

1. `{pengajuan}` mengacu pada `pra_pendaftaran_perkara.id_pendaftaran`.
2. Semua query detail wajib difilter dengan `id_user = auth()->id()`.
3. Klien tidak boleh mengubah isi pengajuan setelah dikirim.
4. Store pengajuan wajib menggunakan transaction.
5. Status awal pengajuan adalah `menunggu_verifikasi`.
6. Status awal wajib dicatat pada `riwayat_status`.

---

## Status dan Catatan Pengajuan Klien

| Method | URI                                         | Name                           | Controller                                  | Purpose                                         |
| ------ | ------------------------------------------- | ------------------------------ | ------------------------------------------- | ----------------------------------------------- |
| GET    | `/klien/pra-pendaftaran/{pengajuan}/status` | `klien.pra-pendaftaran.status` | `Klien\PraPendaftaranStatusController@show` | Melihat status, riwayat, dan catatan verifikasi |

Aturan:

1. Klien hanya boleh melihat status pengajuan miliknya sendiri.
2. Route ini wajib melakukan ownership check.
3. Catatan verifikasi hanya ditampilkan jika memang sudah dibuat oleh Staf Legal.

---

## Unggah Ulang Dokumen

| Method | URI                                     | Name                            | Controller                               | Purpose                     |
| ------ | --------------------------------------- | ------------------------------- | ---------------------------------------- | --------------------------- |
| GET    | `/klien/dokumen/{dokumen}/unggah-ulang` | `klien.dokumen.reupload.create` | `Klien\DokumenReuploadController@create` | Form unggah ulang dokumen   |
| POST   | `/klien/dokumen/{dokumen}/unggah-ulang` | `klien.dokumen.reupload.store`  | `Klien\DokumenReuploadController@store`  | Proses unggah ulang dokumen |

Aturan:

1. `{dokumen}` mengacu pada `dokumen_perkara.id_dokumen`.
2. Dokumen harus milik pengajuan Klien yang sedang login.
3. Re-upload hanya boleh jika dokumen berstatus `perlu_perbaikan`.
4. Re-upload hanya boleh jika ada catatan perbaikan terkait.
5. File lama tidak boleh ditimpa.
6. Dokumen lama diberi status `diganti`.
7. Dokumen baru dibuat sebagai record baru dengan status `terkirim`.
8. Status pengajuan menjadi `menunggu_verifikasi_ulang`.
9. Perubahan status wajib dicatat pada `riwayat_status`.
10. Proses wajib menggunakan transaction.

---

## Pemilihan Jadwal Konsultasi

| Method | URI                                                          | Name                   | Controller                                | Purpose                      |
| ------ | ------------------------------------------------------------ | ---------------------- | ----------------------------------------- | ---------------------------- |
| GET    | `/klien/pra-pendaftaran/{pengajuan}/jadwal`                  | `klien.jadwal.index`   | `Klien\JadwalKonsultasiController@index`  | Melihat slot jadwal tersedia |
| POST   | `/klien/pra-pendaftaran/{pengajuan}/jadwal/{jadwal}/booking` | `klien.jadwal.booking` | `Klien\BookingKonsultasiController@store` | Membuat booking konsultasi   |

Aturan:

1. `{pengajuan}` mengacu pada `pra_pendaftaran_perkara.id_pendaftaran`.
2. `{jadwal}` mengacu pada `jadwal_konsultasi.id_jadwal`.
3. Klien hanya boleh booking untuk pengajuan miliknya sendiri.
4. Booking hanya boleh jika `status_pengajuan = berkas_lengkap`.
5. Jadwal hanya boleh dipilih jika `status_slot = tersedia`.
6. Satu pengajuan hanya boleh memiliki satu booking aktif.
7. Satu slot jadwal hanya boleh memiliki satu booking aktif.
8. Booking baru berstatus `aktif`.
9. `tanggal_booking` diisi server.
10. Slot berubah menjadi `terisi`.
11. Status pengajuan berubah menjadi `jadwal_dipilih`.
12. Perubahan status wajib dicatat pada `riwayat_status`.
13. Proses wajib menggunakan transaction.

---

# Staf Legal Routes

Prefix:

```text
/staf-legal
```

Name prefix:

```text
staf-legal.
```

Middleware:

```text
auth
role:staf_legal
```

---

## Staf Legal Dashboard

| Method | URI                     | Name                   | Controller                            | Purpose              |
| ------ | ----------------------- | ---------------------- | ------------------------------------- | -------------------- |
| GET    | `/staf-legal/dashboard` | `staf-legal.dashboard` | `StafLegal\DashboardController@index` | Dashboard Staf Legal |

---

## Daftar Pengajuan untuk Verifikasi

| Method | URI                                 | Name                         | Controller                            | Purpose                              |
| ------ | ----------------------------------- | ---------------------------- | ------------------------------------- | ------------------------------------ |
| GET    | `/staf-legal/pengajuan`             | `staf-legal.pengajuan.index` | `StafLegal\PengajuanController@index` | Daftar pengajuan menunggu verifikasi |
| GET    | `/staf-legal/pengajuan/{pengajuan}` | `staf-legal.pengajuan.show`  | `StafLegal\PengajuanController@show`  | Detail pengajuan dan dokumen         |

Aturan:

1. Daftar pengajuan menggunakan pagination.
2. Filter status dapat menggunakan `menunggu_verifikasi` dan `menunggu_verifikasi_ulang`.
3. Detail pengajuan menampilkan data Klien, kategori, kronologi, dokumen, dan catatan terkait.
4. Staf Legal hanya mengakses fitur verifikasi, bukan fitur Admin.

---

## Verifikasi Berkas

| Method | URI                                            | Name                           | Controller                                    | Purpose                 |
| ------ | ---------------------------------------------- | ------------------------------ | --------------------------------------------- | ----------------------- |
| GET    | `/staf-legal/pengajuan/{pengajuan}/verifikasi` | `staf-legal.verifikasi.create` | `StafLegal\VerifikasiBerkasController@create` | Form verifikasi berkas  |
| POST   | `/staf-legal/pengajuan/{pengajuan}/verifikasi` | `staf-legal.verifikasi.store`  | `StafLegal\VerifikasiBerkasController@store`  | Simpan hasil verifikasi |

Aturan:

1. Verifikasi hanya boleh dilakukan oleh role `staf_legal`.
2. `id_user` verifikator menggunakan `auth()->id()`.
3. `tanggal_verifikasi` diisi oleh server.
4. Jika `status_verifikasi = berkas_lengkap`, status pengajuan menjadi `berkas_lengkap`.
5. Jika `status_verifikasi = berkas_tidak_lengkap`, status pengajuan menjadi `berkas_tidak_lengkap`.
6. Jika berkas tidak lengkap, wajib ada `catatan_umum` atau minimal satu `catatan_dokumen`.
7. Catatan umum utama disimpan pada `verifikasi_berkas.catatan_umum`.
8. Catatan per dokumen disimpan pada `catatan_verifikasi`.
9. Dokumen bermasalah diberi status `perlu_perbaikan`.
10. Dokumen valid dapat diberi status `valid`.
11. Perubahan status pengajuan wajib dicatat pada `riwayat_status`.
12. Proses wajib menggunakan transaction.

---

# Admin Routes

Prefix:

```text
/admin
```

Name prefix:

```text
admin.
```

Middleware:

```text
auth
role:admin
```

---

## Admin Dashboard

| Method | URI                | Name              | Controller                        | Purpose         |
| ------ | ------------------ | ----------------- | --------------------------------- | --------------- |
| GET    | `/admin/dashboard` | `admin.dashboard` | `Admin\DashboardController@index` | Dashboard Admin |

---

## Kelola Pengguna

| Method    | URI                        | Name                 | Controller                    | Purpose          |
| --------- | -------------------------- | -------------------- | ----------------------------- | ---------------- |
| GET       | `/admin/users`             | `admin.users.index`  | `Admin\UserController@index`  | Daftar pengguna  |
| GET       | `/admin/users/create`      | `admin.users.create` | `Admin\UserController@create` | Form tambah user |
| POST      | `/admin/users`             | `admin.users.store`  | `Admin\UserController@store`  | Simpan user baru |
| GET       | `/admin/users/{user}`      | `admin.users.show`   | `Admin\UserController@show`   | Detail user      |
| GET       | `/admin/users/{user}/edit` | `admin.users.edit`   | `Admin\UserController@edit`   | Form edit user   |
| PUT/PATCH | `/admin/users/{user}`      | `admin.users.update` | `Admin\UserController@update` | Update user      |

Aturan:

1. `{user}` mengacu pada `users.id_user`.
2. Admin dapat membuat akun Staf Legal.
3. Admin tidak boleh membuat role di luar `klien`, `admin`, `staf_legal`.
4. Admin tidak boleh menghapus user yang sudah memiliki data penting tanpa aturan khusus.
5. Password user tidak diubah melalui form edit umum.

---

## Kelola Kategori Perkara

| Method    | URI                                       | Name                             | Controller                                | Purpose                             |
| --------- | ----------------------------------------- | -------------------------------- | ----------------------------------------- | ----------------------------------- |
| GET       | `/admin/kategori-perkara`                 | `admin.kategori-perkara.index`   | `Admin\KategoriPerkaraController@index`   | Daftar kategori                     |
| GET       | `/admin/kategori-perkara/create`          | `admin.kategori-perkara.create`  | `Admin\KategoriPerkaraController@create`  | Form tambah kategori                |
| POST      | `/admin/kategori-perkara`                 | `admin.kategori-perkara.store`   | `Admin\KategoriPerkaraController@store`   | Simpan kategori                     |
| GET       | `/admin/kategori-perkara/{kategori}/edit` | `admin.kategori-perkara.edit`    | `Admin\KategoriPerkaraController@edit`    | Form edit kategori                  |
| PUT/PATCH | `/admin/kategori-perkara/{kategori}`      | `admin.kategori-perkara.update`  | `Admin\KategoriPerkaraController@update`  | Update kategori                     |
| DELETE    | `/admin/kategori-perkara/{kategori}`      | `admin.kategori-perkara.destroy` | `Admin\KategoriPerkaraController@destroy` | Hapus kategori jika belum digunakan |

Aturan:

1. `{kategori}` mengacu pada `kategori_perkara.id_kategori`.
2. Kategori hanya boleh dihapus jika belum pernah digunakan pada pengajuan.
3. Jika kategori sudah digunakan, hapus harus ditolak.
4. Tidak ada fitur menonaktifkan kategori karena database belum menyediakan `status_kategori` atau soft delete.

---

## Kelola Data Pra-Pendaftaran

| Method | URI                                  | Name                          | Controller                             | Purpose                              |
| ------ | ------------------------------------ | ----------------------------- | -------------------------------------- | ------------------------------------ |
| GET    | `/admin/pra-pendaftaran`             | `admin.pra-pendaftaran.index` | `Admin\PraPendaftaranController@index` | Melihat seluruh data pra-pendaftaran |
| GET    | `/admin/pra-pendaftaran/{pengajuan}` | `admin.pra-pendaftaran.show`  | `Admin\PraPendaftaranController@show`  | Detail data pra-pendaftaran          |

Aturan:

1. Admin dapat melihat seluruh data pra-pendaftaran untuk monitoring administratif.
2. Admin tidak mengambil alih proses verifikasi milik Staf Legal.
3. Daftar data harus menggunakan pagination.
4. Filter dapat menggunakan tanggal, status, kategori, dan keyword.

---

## Kelola Jadwal Konsultasi

| Method    | URI                                      | Name                             | Controller                                | Purpose                  |
| --------- | ---------------------------------------- | -------------------------------- | ----------------------------------------- | ------------------------ |
| GET       | `/admin/jadwal-konsultasi`               | `admin.jadwal-konsultasi.index`  | `Admin\JadwalKonsultasiController@index`  | Daftar jadwal konsultasi |
| GET       | `/admin/jadwal-konsultasi/create`        | `admin.jadwal-konsultasi.create` | `Admin\JadwalKonsultasiController@create` | Form tambah jadwal       |
| POST      | `/admin/jadwal-konsultasi`               | `admin.jadwal-konsultasi.store`  | `Admin\JadwalKonsultasiController@store`  | Simpan jadwal            |
| GET       | `/admin/jadwal-konsultasi/{jadwal}/edit` | `admin.jadwal-konsultasi.edit`   | `Admin\JadwalKonsultasiController@edit`   | Form edit jadwal         |
| PUT/PATCH | `/admin/jadwal-konsultasi/{jadwal}`      | `admin.jadwal-konsultasi.update` | `Admin\JadwalKonsultasiController@update` | Update jadwal            |

Aturan:

1. `{jadwal}` mengacu pada `jadwal_konsultasi.id_jadwal`.
2. Status awal jadwal baru adalah `tersedia`.
3. Status `terisi` tidak boleh dipilih saat membuat jadwal baru.
4. Status `terisi` hanya diberikan oleh sistem setelah booking berhasil.
5. Jadwal yang sudah memiliki booking aktif tidak boleh diubah sembarangan.
6. Jadwal yang sudah memiliki booking aktif tidak boleh dihapus sembarangan.
7. Tidak membuat route delete jadwal pada fase awal kecuali disetujui.

---

## Penyelesaian Konsultasi

| Method | URI                                           | Name                               | Controller                                  | Purpose                     |
| ------ | --------------------------------------------- | ---------------------------------- | ------------------------------------------- | --------------------------- |
| PATCH  | `/admin/booking-konsultasi/{booking}/selesai` | `admin.booking-konsultasi.selesai` | `Admin\BookingKonsultasiController@selesai` | Menandai konsultasi selesai |

Aturan:

1. Route ini hanya boleh diakses Admin.
2. `{booking}` mengacu pada `booking_konsultasi.id_booking`.
3. Booking hanya boleh diselesaikan jika `status_booking = aktif`.
4. Setelah booking selesai, `status_booking` menjadi `selesai`.
5. Setelah booking selesai, status pengajuan terkait menjadi `selesai`.
6. Perubahan status pengajuan wajib dicatat pada `riwayat_status`.
7. Proses wajib menggunakan database transaction.
8. Route pembatalan booking tidak dibuat kecuali fitur pembatalan disetujui.

---

## Laporan Pra-Pendaftaran

| Method | URI                                    | Name                                  | Controller                                    | Purpose                       |
| ------ | -------------------------------------- | ------------------------------------- | --------------------------------------------- | ----------------------------- |
| GET    | `/admin/laporan/pra-pendaftaran`       | `admin.laporan.pra-pendaftaran.index` | `Admin\LaporanPraPendaftaranController@index` | Tabel laporan pra-pendaftaran |
| GET    | `/admin/laporan/pra-pendaftaran/print` | `admin.laporan.pra-pendaftaran.print` | `Admin\LaporanPraPendaftaranController@print` | Tampilan print browser        |

Aturan:

1. Laporan hanya boleh diakses Admin.
2. Laporan dibuat dari query.
3. Jangan membuat tabel `laporan`.
4. Filter laporan: tanggal, status pengajuan, kategori, keyword.
5. Output laporan adalah tabel dan print browser.
6. Jangan membuat export PDF atau Excel tanpa persetujuan.

---

# Document Access Routes

Prefix:

```text
/dokumen
```

Name prefix:

```text
dokumen.
```

Middleware:

```text
auth
```

Route dokumen tidak boleh hanya mengandalkan public URL.

| Method | URI                           | Name               | Controller                         | Purpose                                |
| ------ | ----------------------------- | ------------------ | ---------------------------------- | -------------------------------------- |
| GET    | `/dokumen/{dokumen}/view`     | `dokumen.view`     | `DokumenAccessController@view`     | Melihat dokumen dengan authorization   |
| GET    | `/dokumen/{dokumen}/download` | `dokumen.download` | `DokumenAccessController@download` | Mengunduh dokumen dengan authorization |

Aturan:

1. `{dokumen}` mengacu pada `dokumen_perkara.id_dokumen`.
2. Controller wajib melakukan authorization check.
3. Klien hanya boleh membuka dokumen milik pengajuannya sendiri.
4. Staf Legal boleh membuka dokumen untuk kebutuhan verifikasi.
5. Admin boleh membuka dokumen untuk kebutuhan administratif sesuai scope.
6. Jangan menampilkan raw `file_path`.
7. Jangan membuat link langsung ke file tanpa authorization.
8. Jika menggunakan disk `public`, file tetap harus diakses melalui route/controller yang melakukan authorization check.

---

# Route Model Binding Rules

Karena project menggunakan custom primary key, route model binding harus ditangani secara eksplisit.

Jika menggunakan implicit route model binding, model harus mendefinisikan:

```php
public function getRouteKeyName()
{
    return 'nama_primary_key';
}
```

Daftar route parameter:

| Route Parameter | Model                   | Primary Key      |
| --------------- | ----------------------- | ---------------- |
| `{user}`        | `User`                  | `id_user`        |
| `{kategori}`    | `KategoriPerkara`       | `id_kategori`    |
| `{pengajuan}`   | `PraPendaftaranPerkara` | `id_pendaftaran` |
| `{dokumen}`     | `DokumenPerkara`        | `id_dokumen`     |
| `{jadwal}`      | `JadwalKonsultasi`      | `id_jadwal`      |
| `{booking}`     | `BookingKonsultasi`     | `id_booking`     |
| `{verifikasi}`  | `VerifikasiBerkas`      | `id_verifikasi`  |
| `{catatan}`     | `CatatanVerifikasi`     | `id_catatan`     |

Alternatif aman:

Gunakan query eksplisit di controller:

```php
$pengajuan = PraPendaftaranPerkara::where('id_pendaftaran', $id)
    ->firstOrFail();
```

Untuk route Klien, query eksplisit harus disertai ownership check:

```php
$pengajuan = PraPendaftaranPerkara::where('id_pendaftaran', $id)
    ->where('id_user', auth()->id())
    ->firstOrFail();
```

---

# Controller Namespace Recommendation

Gunakan struktur controller berikut:

```text
app/Http/Controllers/
├── DashboardRedirectController.php
├── DokumenAccessController.php
├── Klien/
│   ├── DashboardController.php
│   ├── ProfilController.php
│   ├── PraPendaftaranController.php
│   ├── PraPendaftaranStatusController.php
│   ├── DokumenReuploadController.php
│   ├── JadwalKonsultasiController.php
│   └── BookingKonsultasiController.php
├── StafLegal/
│   ├── DashboardController.php
│   ├── PengajuanController.php
│   └── VerifikasiBerkasController.php
└── Admin/
    ├── DashboardController.php
    ├── UserController.php
    ├── KategoriPerkaraController.php
    ├── PraPendaftaranController.php
    ├── JadwalKonsultasiController.php
    ├── BookingKonsultasiController.php
    └── LaporanPraPendaftaranController.php
```

Aturan:

1. Jangan menaruh semua logic di satu controller besar.
2. Controller boleh memanggil service class untuk proses kompleks.
3. Proses multi-tabel seperti pengajuan, verifikasi, re-upload, booking, dan penyelesaian konsultasi sebaiknya memakai service class dan transaction.

---

# Forbidden Route Actions

AI agent tidak boleh:

1. Membuat route role tanpa middleware.
2. Membuat route Klien tanpa ownership check.
3. Membuat route dokumen tanpa authorization check.
4. Membuat route delete data penting tanpa aturan.
5. Membuat route password reset email tanpa persetujuan.
6. Membuat route export PDF/Excel tanpa persetujuan.
7. Membuat route payment atau e-Court.
8. Membuat route yang menerima status penting dari request tanpa validasi.
9. Membuat route aksi sensitif menggunakan method `GET`.
10. Membuat route di luar scope `FEATURE_LIST.md`.

---

# Final Notes for AI Agent

AI agent wajib mengikuti aturan berikut:

1. Gunakan prefix role: `/klien`, `/staf-legal`, dan `/admin`.
2. Gunakan route name yang konsisten.
3. Gunakan middleware `auth` dan role.
4. Gunakan ownership check untuk Klien.
5. Gunakan authorization check untuk dokumen.
6. Jangan membuat route fitur tambahan tanpa persetujuan.
7. Cocokkan route dengan `SECURITY_RULES.md`, `VALIDATION_RULES.md`, dan `FEATURE_LIST.md`.
