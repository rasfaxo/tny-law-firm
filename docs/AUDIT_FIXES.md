# AUDIT_FIXES.md

## Purpose

Dokumen ini adalah changelog terpusat dari perbaikan temuan audit (`audit.md`).
Setiap item mencatat masalah, file yang diubah, ringkasan perubahan, dan cara
verifikasi manual. Dokumen ini TIDAK termasuk dalam locked docs, melainkan
pendamping agar context tetap terupdate antara hasil audit dan kode aktual.

Status:

- ✅ **Done** — sudah diimplementasi dan diverifikasi
- 🔍 **Verified** — diverifikasi bahwa masalah sudah teratasi, tidak perlu perubahan kode
- 🔄 **In Progress** — sedang dikerjakan

> Lihat `audit.md` untuk deskripsi lengkap tiap temuan (severity, dampak, rekomendasi).

---

## Ringkasan Status

| ID       | Severity | Status   | Ringkasan                                                              |
| -------- | -------- | -------- | ---------------------------------------------------------------------- |
| CRIT-01  | Critical | ✅ Done  | Storage dokumen dipindah dari disk `public` ke disk `local` (private)  |
| CRIT-02  | Critical | ✅ Done  | Kolom `remember_token` ditambahkan via migration terpisah              |
| CRIT-09  | Critical | ✅ Done  | `ProfileUpdateRequest::authorize()` ditambahkan                        |
| HIGH-02  | High     | ✅ Done  | Null-safe akses `catatan` + validasi catatan wajib saat perlu perbaikan |
| HIGH-05  | High     | ✅ Done  | Enum `app/Enums/` sebagai single source of truth status & role         |
| HIGH-08  | High     | ✅ Done  | `after_or_equal:today` di `UpdateJadwalKonsultasiRequest`              |
| HIGH-09  | High     | ✅ Done  | Password policy staf legal dinaikkan ke `Password::defaults()`         |
| HIGH-10  | High     | ✅ Done  | Bug `env()` di `AdminSeeder` diganti dengan config layer + fail-fast   |
| HIGH-17  | High     | 🔍 Verified | Eager loading sudah lengkap di semua controller jadwal/booking      |

---

## ✅ CRIT-01 — Storage Disk `public` → `local`

**Masalah:** Dokumen perkara disimpan di disk `public`, sehingga setelah
`php artisan storage:link` siapapun dapat mengakses file langsung via URL
tanpa melewati authorization check.

**File yang diubah:**

- `app/Services/DokumenPerkaraService.php`
- `app/Services/PerbaikanDokumenService.php`
- `app/Http/Controllers/Klien/DokumenPerkaraController.php`
- `app/Http/Controllers/StafLegal/VerifikasiBerkasController.php`
- `config/filesystems.php`

**Ringkasan perubahan:**

- Semua `->store("dokumen-perkara", "public")` diganti menjadi disk `"local"`.
- Rollback delete pada catch block juga memakai disk `"local"`.
- File diserve melalui controller (`Storage::disk("local")->download(...)`)
  dengan authorization + existence check (403/404), bukan URL publik.
- `config/filesystems.php`: disk `local` diarahkan ke `storage_path('app/private')`
  dengan opsi `'serve' => true`. Default disk = `local`.

**Verifikasi manual:**

1. Pastikan tidak ada symlink yang mengekspos `storage/app/private` ke `public/`.
2. Buka dokumen via route klien/staf legal yang sah → file terunduh.
3. Akses langsung `storage/app/private/dokumen-perkara/{file}` via browser →
   harus gagal (bukan URL publik).

**Penyesuaian test (saat sesi HIGH-08/09/10):**

Implementasi CRIT-01 sempat meninggalkan 2 test yang masih mengasumsikan disk
`"public"`, sehingga gagal setelah storage dipindah ke `"local"`. Test-test
berikut diupdate agar konsisten dengan CRIT-01 (semua `Storage::fake`/`assertExists`
dialihkan ke disk `"local"`):

- `tests/Feature/Klien/DokumenPerkaraTest.php` — 4 method
- `tests/Feature/Klien/PerbaikanDokumenTest.php` — 2 method

Setelah penyesuaian ini, test suite penuh hijau (66 passed, 219 assertions).

---

## ✅ CRIT-02 — Kolom `remember_token`

**Masalah:** Migration `users` awal tidak membuat kolom `remember_token`,
sedangkan model `User` meng-extend `Authenticatable`. Login dengan "remember me"
crash di MySQL dengan `Column not found: 1054 Unknown column 'remember_token'`.

**File yang dibuat/diubah:**

- `database/migrations/2026_07_08_000001_add_remember_token_to_users_table.php` (baru)
- `app/Models/User.php`

**Ringkasan perubahan:**

- Migration terpisah menambah `$table->rememberToken()` setelah kolom
  `status_akun`, lengkap dengan `down()` untuk rollback.
- Model `User::$hidden` sudah mencakup `remember_token` (bersama `password`).

**Verifikasi manual:**

1. Jalankan `php artisan migrate` → kolom `remember_token` muncul di tabel `users`.
2. Login sebagai klien/admin/staf legal dengan checkbox "Remember Me" aktif →
   tidak ada error, sesi tetap valid setelah browser ditutup.

---

## ✅ CRIT-09 — `ProfileUpdateRequest::authorize()`

**Masalah:** Class tidak mendefinisikan `authorize()`. Base `FormRequest`
default return `false`, sehingga setiap request update profil ditolak 403
sebelum `rules()` dievaluasi — fitur update profil sepenuhnya broken.

**File yang diubah:**

- `app/Http/Requests/ProfileUpdateRequest.php`

**Ringkasan perubahan:**

```php
public function authorize(): bool
{
    return $this->user() !== null;
}
```

Rule unique email juga sudah menyesuaikan primary key kustom `id_user`.

**Verifikasi manual:**

1. Login sebagai klien → buka halaman profil → ubah nama/telepon → simpan.
2. Status harus 200/302 (bukan 403) dan data tersimpan.

---

## ✅ HIGH-02 — Null-safe akses `catatan` + validasi wajib

**Masalah:** Akses `$documentData["catatan"]` tanpa null-check dapat
menyebabkan PHP fatal error bila form submit mengirim `status_dokumen =
perlu_perbaikan` tanpa key `catatan`. Kolom `isi_catatan` juga `NOT NULL`.

**File yang diubah:**

- `app/Services/VerifikasiBerkasService.php`
- `app/Http/Requests/StafLegal/StoreVerifikasiBerkasRequest.php`

**Ringkasan perubahan:**

- Service memakai `$documentData["catatan"] ?? ""` sebagai safety net
  (defensive layering), disertai komentar penjelasan.
- Form Request menambah `max:2000` pada `dokumen.*.catatan` dan menerapkan
  logika "catatan wajib saat perlu_perbaikan" via method `after()` yang
  memberi pesan error spesifik per-field (setara `required_if` namun lebih
  informatif).
- Validasi tambahan: ID dokumen valid, dokumen milik pengajuan, semua dokumen
  aktif terverifikasi, dan saat `berkas_lengkap` semua status harus `valid`.

**Verifikasi manual:**

1. Sebagai staf legal, tandai satu dokumen `perlu_perbaikan` tanpa mengisi
   catatan → submit → muncul error "Catatan perbaikan wajib diisi...".
2. Isi catatan → submit berhasil, tidak ada fatal error.

---

## ✅ HIGH-05 — Enum Single Source of Truth

**Masalah:** Status values, role slugs, dan metode konsultasi tersebar sebagai
raw string literal di 8+ file tanpa single source of truth di kode.

**File yang dibuat:**

- `app/Enums/RoleUser.php`
- `app/Enums/StatusPengajuan.php`
- `app/Enums/StatusDokumen.php`
- `app/Enums/StatusBooking.php`
- `app/Enums/StatusSlot.php`
- `app/Enums/StatusReschedule.php`
- `app/Enums/StatusKonfirmasi.php`

**Ringkasan perubahan:**

- 7 PHP 8.1 backed string enum dibuat sebagai kerangka single source of truth.
- Setiap enum menyediakan method `label()` (display) dan `values()` (untuk
  dropdown/rule in), serta helper khusus domain (mis.
  `StatusPengajuan::verifiableStatuses()`, `StatusSlot::bookableStatuses()`).
- **Catatan:** Integrasi masih parsial. `StatusPengajuan` dan beberapa enum
  lain sudah dipakai di `VerifikasiBerkasService` dan `LaporanController`.
  Migrasi magic string di tempat lain (service/model yang masih hardcode
  string status) adalah pekerjaan cleanup post-MVP.

**Verifikasi manual:**

1. `php artisan tinker` → `App\Enums\StatusPengajuan::BerkasLengkap->value`
   → `'berkas_lengkap'`.
2. `App\Enums\StatusPengajuan::verifiableStatuses()` → array status yang
   boleh diverifikasi.

---

## ✅ HIGH-08 — `after_or_equal:today` di Update Jadwal Konsultasi

**Masalah:** `StoreJadwalKonsultasiRequest` mewajibkan
`after_or_equal:today` pada `tanggal`, tetapi `UpdateJadwalKonsultasiRequest`
tidak. Admin bisa update jadwal ke tanggal masa lalu, yang lalu muncul di
form booking klien.

**File yang diubah:**

- `app/Http/Requests/Admin/UpdateJadwalKonsultasiRequest.php`

**Ringkasan perubahan:**

```php
// sebelum
"tanggal" => ["required", "date"],
// sesudah
"tanggal" => ["required", "date", "after_or_equal:today"],
```

Sekarang konsisten dengan `StoreJadwalKonsultasiRequest`.

**Dampak pada sisi klien:** Tidak ada perubahan Blade. Filter
`Klien/BookingKonsultasiController::create()` hanya menampilkan slot dengan
`status_slot = tersedia`, sehingga validasi ini mencegah admin membuat/mengubah
slot masa lalu agar tidak tampil di form booking klien.

**Verifikasi manual:**

1. Login admin → edit jadwal → input tanggal kemarin → simpan.
2. Harus muncul validation error "The tanggal must be a date after or equal to today."

---

## ✅ HIGH-09 — Password Policy Staf Legal

**Masalah:** Password staf_legal hanya divalidasi `min:8`, padahal registrasi
klien memakai `Password::defaults()`. Akun staf_legal lebih privileged
namun password-nya lebih lemah — inkonsisten dan tidak aman.

**File yang diubah:**

- `app/Http/Requests/Admin/StoreStafLegalRequest.php`
- `app/Http/Requests/Admin/UpdateStafLegalPasswordRequest.php`

**Ringkasan perubahan:**

- Kedua file menambah import `use Illuminate\Validation\Rules\Password;`.
- Rule password diganti dari `["required", "confirmed", "min:8"]` menjadi
  `["required", "confirmed", Password::defaults()]`.
- Konsisten dengan `RegisteredUserController`, `NewPasswordController`, dan
  `PasswordController` di sisi klien/auth yang semuanya sudah pakai
  `Password::defaults()`.
- `Password::defaults()` di project ini adalah default Laravel (min 12
  karakter, kombinasi huruf besar/kecil/angka/simbol) — tidak ada
  kustomisasi di `AppServiceProvider`, sehingga semua tempat konsisten.

**Verifikasi manual:**

1. Login admin → buat akun staf legal dengan password `12345678` → harus
   gagal dengan error kompleksitas.
2. Ulangi dengan password `Test@123456` → harus berhasil.

---

## ✅ HIGH-10 — Bug `env()` di AdminSeeder

**Masalah:** Seeder memakai `env('ADMIN_DEFAULT_PASSWORD', 'password')`
langsung. Setelah `php artisan config:cache` di production, `env()` return
`null` untuk key custom, sehingga password admin selalu jatuh ke literal
string `'password'` — trivially guessable.

**File yang diubah/dibuat:**

- `config/app.php` — tambah array key `admin.default_password`
- `database/seeders/AdminSeeder.php` — baca via `config()` + fail-fast
- `.env.example` — tambah placeholder `ADMIN_DEFAULT_PASSWORD=`

**Ringkasan perubahan:**

```php
// config/app.php
'admin' => [
    'default_password' => env('ADMIN_DEFAULT_PASSWORD'),
],
```

```php
// database/seeders/AdminSeeder.php
private function resolveDefaultPassword(): string
{
    $password = config('app.admin.default_password');

    if ($password !== null && $password !== '') {
        return $password;
    }

    if (app()->environment('production')) {
        throw new RuntimeException(
            'ADMIN_DEFAULT_PASSWORD belum didefinisikan. '
            . 'Tambahkan key ADMIN_DEFAULT_PASSWORD pada file .env '
            . 'sebelum menjalankan seeder di production.'
        );
    }

    return 'password';
}
```

**Pendekatan:** Config key + fail-fast.

- Pembacaan password lewat `config()` agar tetap berfungsi setelah
  `config:cache` (best practice Laravel).
- Di production: lempar exception jika `ADMIN_DEFAULT_PASSWORD` kosong,
  mencegah diam-diam jatuh ke password lemah.
- Di local/testing: fallback `'password'` dipertahankan untuk kenyamanan dev.

**Verifikasi manual:**

1. Local: kosongkan `ADMIN_DEFAULT_PASSWORD`, `APP_ENV=local` → jalankan
   `php artisan db:seed --class=AdminSeeder` → berhasil, password = `password`.
2. Production: set `APP_ENV=production`, hapus/kosongkan env → jalankan
   `php artisan config:cache` lalu seeder → harus throw RuntimeException.
3. Production: set `ADMIN_DEFAULT_PASSWORD=StrongPass123!` → `config:cache`
   → seeder → akun admin pakai password dari env.

---

## 🔍 HIGH-17 — Eager Loading N+1 (Terverifikasi Sudah Benar)

**Masalah (awal):** Blade views mengakses relasi dalam loop
(`$jadwal->admin`, `$booking->praPendaftaranPerkara`, `$booking->klien`).
Jika controller tidak menyertakan eager loading, terjadi N+1 query.

**Hasil verifikasi:** Setelah audit ulang, **semua controller sudah menyertakan
eager loading yang lengkap**. Tidak ada perubahan kode yang diperlukan.

**Controller & relasi yang sudah di-eager-load:**

| Controller | Method | Eager loading |
| ---------- | ------ | ------------- |
| `Admin/JadwalKonsultasiController` | `index()` | `->with("admin")` |
| `Admin/JadwalKonsultasiController` | `show()` | `->load("admin")` |
| `Admin/BookingKonsultasiController` | `index()` | `->with(["jadwalKonsultasi", "klien", "praPendaftaranPerkara.kategori"])` |
| `Admin/BookingKonsultasiController` | `show()` | `->load(["adminKonfirmasi", "jadwalKonsultasi", "klien", "permintaanReschedule", "praPendaftaranPerkara.kategori"])` |
| `Klien/BookingKonsultasiController` | `index()` | `->with(["jadwalKonsultasi", "praPendaftaranPerkara.kategori"])` |
| `Klien/BookingKonsultasiController` | `show()` | `->load(["jadwalKonsultasi", "praPendaftaranPerkara.kategori", "permintaanReschedule"])` |

Semua relasi yang diakses di Blade loop sudah ter-cover. Catatan: saat form
booking klien (`create()`) melakukan pagination jadwal, tidak ada akses
relasi tambahan di Blade yang menyebabkan N+1.

**Verifikasi manual (opsional, untuk konfirmasi):**

1. Install Laravel Debugbar (`barryvdh/laravel-debugbar` — dev only).
2. Buka `admin.jadwal-konsultasi.index` dan `admin.booking-konsultasi.index`
   dengan data ≥ 10 baris.
3. Debugbar "Queries" tab → total query harus ~2-3 (1 utama + 1 per relasi
   eager-loaded), TIDAK ada lonjakan query sebanding jumlah baris.

---

## Catatan Pemeliharaan

- Item HIGH-14 (magic strings) hanya teratasi parsial lewat HIGH-05.
  Migrasi penuh magic string → Enum di seluruh service/model adalah
  pekerjaan cleanup post-MVP.
- Temuan audit lain (CRIT-03 sampai CRIT-08, HIGH-01, HIGH-03, HIGH-04,
  HIGH-06, HIGH-07, HIGH-11 sampai HIGH-22) belum dikerjakan dan tetap
  tercatat di `audit.md` sebagai backlog.
