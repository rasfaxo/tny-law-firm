Saya kini memiliki seluruh data dari audit menyeluruh 15 fase. Berikut laporan final.

---

# EXECUTIVE SUMMARY

```
Overall Score: 69 / 100

Production Ready: TIDAK

Critical Issues : 9
High Issues     : 22
Medium Issues   : 26
Low Issues      : 18
```

---

# DETAIL TEMUAN

---

## ⚫ CRITICAL — Harus diperbaiki sebelum apapun

---

**[CRIT-01]**
**Severity:** Critical
**Kategori:** Security — Storage
**Lokasi:** `app/Services/DokumenPerkaraService.php` line 24, `app/Services/PerbaikanDokumenService.php` line 27, `app/Http/Controllers/Klien/DokumenPerkaraController.php` line 76

**Masalah:**
Semua dokumen perkara disimpan di disk `"public"`:
```php
$file->store("dokumen-perkara", "public");
```
Setelah `php artisan storage:link` dijalankan (wajib untuk deployment), setiap file langsung dapat diakses melalui:
```
https://example.com/storage/dokumen-perkara/{nama_file}.pdf
```
Ini **melewati seluruh authorization check** yang ada di `DokumenPerkaraController::show()` dan `VerifikasiBerkasController::showDokumen()`. Seluruh controller sudah benar secara logika, tetapi tidak relevan karena file bisa diakses langsung tanpa melewati PHP.

**Dampak:** Siapapun yang menebak atau mendapatkan nama file (yang tersimpan di database) dapat mengunduh dokumen hukum rahasia milik klien lain tanpa autentikasi. Ini melanggar `SECURITY_RULES.md` aturan 11–12.

**Rekomendasi:** Ganti disk ke `"local"` (private):
```php
$file->store("dokumen-perkara", "local");
```
Serve melalui controller dengan:
```php
return Storage::disk("local")->download($path, $namaFile);
```

---

**[CRIT-02]**
**Severity:** Critical
**Kategori:** Bug — Authentication
**Lokasi:** `database/migrations/0001_01_01_000000_create_users_table.php`

**Masalah:** Migration `users` tidak membuat kolom `remember_token`. Model `User` meng-extend `Authenticatable` yang mencoba menulis ke kolom ini saat "remember me" aktif. Menyebabkan:
```
SQLSTATE[42S22]: Column not found: 1054 Unknown column 'remember_token'
```

**Dampak:** Login dengan "remember me" akan crash di production MySQL. Breeze menyertakan fitur ini secara default.

**Rekomendasi:** Tambahkan `$table->rememberToken();` ke migration. Jika "remember me" sengaja dinonaktifkan, hapus checkbox dari Blade login dan dokumentasikan.

---

**[CRIT-03]**
**Severity:** Critical
**Kategori:** Race Condition — Service
**Lokasi:** `app/Services/JadwalKonsultasiService.php` lines 13–28

**Masalah:** `create()` mengecek overlap lalu menulis tanpa transaction atau lock:
```php
public function create(array $data, int $adminId): JadwalKonsultasi
{
    $this->ensureNoOverlap(...); // ← cek tanpa lock
    return JadwalKonsultasi::create([...]); // ← tulis tanpa transaction
}
```
Dua admin yang mengirim request bersamaan bisa lolos cek overlap sebelum salah satu commit, menghasilkan dua slot yang bertabrakan secara permanen. Ini adalah TOCTOU race condition klasik.

**Dampak:** Jadwal konsultasi bisa overlap, menyebabkan dua klien dibooking ke waktu yang sama.

**Rekomendasi:** Bungkus dalam `DB::transaction()` dengan `lockForUpdate()` pada query cek overlap, dan tambahkan composite unique index `(tanggal, waktu_mulai, waktu_selesai)` sebagai safety net di database.

---

**[CRIT-04]**
**Severity:** Critical
**Kategori:** Security — File Exposure
**Lokasi:** `app/Models/DokumenPerkara.php`

**Masalah:** `file_path` tidak ada dalam `$hidden`. Jika model di-serialize ke JSON (response API, `->toArray()`, debug output), path storage internal langsung terekspos. Ini melanggar aturan eksplisit di `AGENTS.md` dan `SECURITY_RULES.md`.

**Dampak:** Eksposur struktur direktori internal dan kemungkinan path traversal.

**Rekomendasi:** Tambahkan `protected $hidden = ['file_path'];`. Expose hanya melalui signed URL atau authorized route.

---

**[CRIT-05]**
**Severity:** Critical
**Kategori:** Performance — Memory
**Lokasi:** `app/Http/Controllers/Admin/LaporanController.php` — semua 5 method

**Masalah:** Setiap method laporan mengakhiri query dengan `->get()` tanpa limit:
```php
->latest("tanggal_pengajuan")
->get(); // ← fetch seluruh dataset ke memory PHP
```
Dengan eager loading relasi (`kategori`, `klien`, `jadwalKonsultasi`), setiap row memiliki object graph yang besar.

**Dampak:** Memory exhaustion (`Allowed memory size exhausted`) pada data real jangka panjang. Satu request admin bisa crash PHP-FPM worker.

**Rekomendasi:** Tambahkan `->limit(500)` dengan warning banner saat limit tercapai, atau implementasikan pagination. Tambahkan index pada kolom tanggal yang difilter.

---

**[CRIT-06]**
**Severity:** Critical
**Kategori:** Testing — Infrastructure
**Lokasi:** `phpunit.xml` lines 26–27

**Masalah:**
```xml
<env name="DB_CONNECTION" value="sqlite"/>
<env name="DB_DATABASE" value=":memory:"/>
```
Production menggunakan MySQL, tests menggunakan SQLite in-memory. Akibatnya:
1. `lockForUpdate()` adalah **no-op di SQLite** — seluruh race condition guard tidak pernah benar-benar ditest
2. Foreign key constraints tidak aktif secara default di SQLite — data invalid dari factory lolos
3. MySQL strict mode menolak data yang SQLite terima diam-diam
4. `whereDate()` berperilaku berbeda antara MySQL dan SQLite

**Dampak:** False confidence. Semua test pass di CI tetapi behavior production tidak ter-cover.

**Rekomendasi:** Gunakan MySQL dedicated test database. Minimal ubah `phpunit.xml` ke `DB_CONNECTION=mysql` dengan skema test terpisah.

---

**[CRIT-07]**
**Severity:** Critical
**Kategori:** Testing — Coverage
**Lokasi:** Missing test files

**Masalah:** Modul-modul berikut tidak memiliki test sama sekali:

| Test File yang Tidak Ada | Fitur |
|---|---|
| `AdminStafLegalTest.php` | CRUD staf legal, deaktivasi akun |
| `AdminKategoriPerkaraTest.php` | CRUD kategori, delete protection |
| `ProfilKlienTest.php` | Profil klien, ownership |
| `VerifikasiUlangTest.php` | Flow `menunggu_verifikasi_ulang` |
| `AdminPraPendaftaranTest.php` | Admin view semua pengajuan |

**Dampak:** Business-critical flows tidak memiliki regression protection. Perubahan kecil pada controller atau service bisa merusak fitur ini tanpa diketahui.

---

**[CRIT-08]**
**Severity:** Critical
**Kategori:** Testing — Security Coverage
**Lokasi:** `app/Http/Controllers/StafLegal/VerifikasiBerkasController.php` lines 83–104

**Masalah:** Method `showDokumen()` adalah endpoint paling sensitif dalam aplikasi (akses dokumen perkara), namun tidak ada satu test pun yang memverifikasi:
- Klien tidak bisa akses dokumen klien lain melalui route ini
- Admin tidak bisa akses via route staf_legal ini
- Dokumen dengan pengajuan status `selesai` return 403
- File tidak ada return 404

**Dampak:** Authorization logic yang paling kritis tidak ter-cover oleh automated test.

---

**[CRIT-09]**
**Severity:** Critical
**Kategori:** Bug — Form Request
**Lokasi:** `app/Http/Requests/ProfileUpdateRequest.php`

**Masalah:** Class tidak mendefinisikan method `authorize()`. Base class `FormRequest::authorize()` default return `false`. Artinya **setiap request update profil akan ditolak dengan HTTP 403** sebelum `rules()` dievaluasi.

**Dampak:** Fitur update profil sepenuhnya broken — tidak ada user yang bisa update profil mereka.

**Rekomendasi:**
```php
public function authorize(): bool
{
    return $this->user() !== null;
}
```

---

## 🔴 HIGH — Sebaiknya diperbaiki sebelum frontend

---

**[HIGH-01]**
**Severity:** High
**Kategori:** Race Condition — Service
**Lokasi:** `app/Services/PermintaanRescheduleService.php`

**Masalah:** `hasPendingRequest` tidak menggunakan `lockForUpdate()`. Dua request bersamaan dari klien yang sama bisa lolos cek dan membuat dua permintaan reschedule aktif untuk booking yang sama.
```php
$hasPendingRequest = PermintaanReschedule::query()
    ->where("id_booking", $booking->id_booking)
    ->where("status_reschedule", "menunggu_persetujuan")
    ->exists(); // ← plain SELECT, bukan current read
```

**Rekomendasi:** Tambahkan `->lockForUpdate()` pada query `hasPendingRequest`.

---

**[HIGH-02]**
**Severity:** High
**Kategori:** Bug — Service
**Lokasi:** `app/Services/VerifikasiBerkasService.php` ~line 101

**Masalah:** Akses array key `$documentData["catatan"]` tanpa null-check. Jika form submission mengirim document dengan `status_dokumen = "perlu_perbaikan"` tapi tanpa key `catatan`, terjadi PHP fatal error (`Undefined array key "catatan"`). Column `isi_catatan` di database juga `NOT NULL`, sehingga ada dua independent failure modes.

**Dampak:** PHP fatal error pada proses verifikasi berkas tidak lengkap.

**Rekomendasi:** Gunakan `$documentData["catatan"] ?? ''` dan pastikan `StoreVerifikasiBerkasRequest` mewajibkan `dokumen.*.catatan` ketika `dokumen.*.status_dokumen === "perlu_perbaikan"` (gunakan `required_if` rule).

---

**[HIGH-03]**
**Severity:** High
**Kategori:** Security — PII
**Lokasi:** `app/Models/ProfilKlien.php`

**Masalah:** `no_identitas` (NIK) tidak ada dalam `$hidden`. Jika model di-serialize, nomor identitas nasional klien terekspos. Ini data PII yang sangat sensitif dalam konteks law firm.

**Rekomendasi:** Tambahkan `protected $hidden = ['no_identitas'];`.

---

**[HIGH-04]**
**Severity:** High
**Kategori:** Security — Mass Assignment
**Lokasi:** `app/Models/ProfilKlien.php` line 21

**Masalah:** `id_user` ada di `$fillable`. Memungkinkan ownership takeover jika ada controller yang melakukan `fill($request->all())`.

**Rekomendasi:** Hapus `id_user` dari `$fillable`, set secara eksplisit: `$profil->id_user = $userId;`.

---

**[HIGH-05]**
**Severity:** High
**Kategori:** Database — Race Condition
**Lokasi:** `database/migrations/2026_06_17_000008_create_jadwal_konsultasi_table.php`

**Masalah:** Tidak ada composite unique constraint pada `(tanggal, waktu_mulai, waktu_selesai)`. Overlap protection di `JadwalKonsultasiService` adalah application-level saja tanpa database safety net.

**Rekomendasi:** Tambahkan `$table->unique(['tanggal', 'waktu_mulai', 'waktu_selesai']);`.

---

**[HIGH-06]**
**Severity:** High
**Kategori:** Database — Audit Trail
**Lokasi:** `database/migrations/2026_06_25_000002_create_permintaan_reschedule_table.php` lines 31–37

**Masalah:** FK `id_booking` dan `id_user` di `permintaan_reschedule` menggunakan `cascadeOnDelete()`. Menghapus booking atau user akan menghapus seluruh riwayat permintaan reschedule — audit trail hilang selamanya. Semua FK lain di project menggunakan RESTRICT (default) yang justru mencegah penghapusan tidak disengaja.

**Dampak:** Jika ada cleanup script atau future admin feature yang menghapus booking, histori reschedule hilang tanpa bisa dipulihkan.

**Rekomendasi:** Review dengan `docs/DATABASE_PLAN.md`. Jika tidak ada justifikasi eksplisit untuk cascade, ganti ke `restrictOnDelete()` atau setidakkan `nullOnDelete()`.

---

**[HIGH-07]**
**Severity:** High
**Kategori:** Security — Authentication
**Lokasi:** `routes/auth.php`, `app/Http/Controllers/Auth/NewPasswordController.php`

**Masalah:** Breeze default `NewPasswordController::store()` memanggil `Auth::login($user)` setelah reset password berhasil, sebelum melewati `EnsureAccountIsActive` middleware. Akun yang di-nonaktifkan bisa mendapatkan sesi valid sementara melalui alur reset password.

**Rekomendasi:** Override `store()` untuk memeriksa `status_akun` sebelum `Auth::login()`, atau tambahkan check di `EnsureAccountIsActive` untuk langsung redirect ke login dengan pesan yang sesuai.

---

**[HIGH-08]**
**Severity:** High
**Kategori:** Validation — Form Request
**Lokasi:** `app/Http/Requests/Admin/UpdateJadwalKonsultasiRequest.php` line 22

**Masalah:** `StoreJadwalKonsultasiRequest` mewajibkan `after_or_equal:today` pada `tanggal`. `UpdateJadwalKonsultasiRequest` tidak. Admin bisa update jadwal ke tanggal masa lalu.

**Dampak:** Slot jadwal yang sudah lewat bisa muncul di booking form klien, membingungkan pengguna.

**Rekomendasi:** Tambahkan `"after_or_equal:today"` ke `UpdateJadwalKonsultasiRequest::rules()['tanggal']`.

---

**[HIGH-09]**
**Severity:** High
**Kategori:** Security — Password Policy
**Lokasi:** `app/Http/Requests/Admin/StoreStafLegalRequest.php` line 24, `UpdateStafLegalPasswordRequest.php` line 20

**Masalah:** Password staf_legal hanya divalidasi dengan `"min:8"`. Registrasi klien menggunakan `Rules\Password::defaults()`. Akun staf_legal lebih privileged dan harusnya menggunakan policy yang lebih ketat.

**Rekomendasi:** Ganti `"min:8"` dengan `Rules\Password::defaults()` di kedua request.

---

**[HIGH-10]**
**Severity:** High
**Kategori:** Security — Seeder
**Lokasi:** `database/seeders/AdminSeeder.php`

**Masalah:**
```php
'password' => Hash::make(env('ADMIN_DEFAULT_PASSWORD', 'password')),
```
Setelah `php artisan config:cache` dijalankan di production, `env()` mengembalikan `null` untuk semua custom key. Akibatnya password admin default menjadi literal string `"password"` di setiap production deployment.

**Dampak:** Admin password production menjadi trivially guessable tanpa developer menyadarinya.

**Rekomendasi:** Gunakan `config()` bukan `env()` langsung. Atau lempar exception jika password tidak tersedia.

---

**[HIGH-11]**
**Severity:** High
**Kategori:** Testing — Factory
**Lokasi:** `database/factories/BookingKonsultasiFactory.php` lines 21–26

**Masalah:** `id_pendaftaran` dan `id_user` menunjuk ke User yang berbeda. Factory membuat dua independent users. Setiap test yang menggunakan `BookingKonsultasi::factory()->create()` langsung menghasilkan data invalid dimana booking user ≠ pengajuan owner.

---

**[HIGH-12]**
**Severity:** High
**Kategori:** Testing — Factory
**Lokasi:** `database/factories/CatatanVerifikasiFactory.php`

**Masalah:** `id_verifikasi` dan `id_dokumen` di-generate oleh sub-factory yang berbeda, menunjuk ke `pra_pendaftaran_perkara` yang berbeda. Data relasional yang tidak konsisten ini lolos di SQLite tapi akan gagal di MySQL jika FK constraints diperiksa.

---

**[HIGH-13]**
**Severity:** High
**Kategori:** Documentation
**Lokasi:** `README.md`

**Masalah:** README masih berisi boilerplate default Laravel. Tidak ada setup instruction, deskripsi project, cara konfigurasi `.env`, role yang ada, atau cara menjalankan test.

**Dampak:** Developer baru atau penguji akademik tidak mendapatkan informasi apapun tentang project ini dari README.

---

**[HIGH-14]**
**Severity:** High
**Kategori:** Clean Code — Magic Strings
**Lokasi:** 8+ file (`DashboardController`, `LaporanController`, `VerifikasiBerkasService`, `PermintaanRescheduleService`, Blade views, test files)

**Masalah:** Status values, role slugs, dan metode konsultasi tersebar sebagai raw string literal di seluruh codebase tanpa single source of truth di code. Contoh: `"menunggu_verifikasi"` muncul di minimal 5 file yang berbeda.

**Dampak:** Typo atau perubahan nama status mengharuskan developer mencari secara manual di seluruh codebase.

**Rekomendasi:** Buat PHP 8.1 Backed Enums: `StatusPengajuan`, `StatusDokumen`, `StatusBooking`, `StatusSlot`, `StatusReschedule`, `RoleUser`.

---

**[HIGH-15]**
**Severity:** High
**Kategori:** Clean Code — SRP
**Lokasi:** `app/Http/Controllers/DashboardController.php`

**Masalah:** Satu controller menangani tiga dashboard berbeda (`klien()`, `admin()`, `stafLegal()`), redirect logic, dan session management. Method `admin()` 67 baris, `klien()` 65 baris.

**Rekomendasi:** Split menjadi `KlienDashboardController`, `AdminDashboardController`, `StafLegalDashboardController`.

---

**[HIGH-16]**
**Severity:** High
**Kategori:** Clean Code — DRY
**Lokasi:** `app/Http/Controllers/Admin/LaporanController.php`

**Masalah:** Validation block `tanggal_mulai`/`tanggal_selesai` di-copy-paste verbatim di 5 method berbeda. Status array di `LaporanController` menduplikasi `STATUS_RULES.md`.

---

**[HIGH-17]**
**Severity:** High
**Kategori:** Performance — N+1
**Lokasi:** `resources/views/admin/jadwal-konsultasi/index.blade.php` line 72, `resources/views/admin/booking-konsultasi/index.blade.php` lines 35–36

**Masalah:** Blade views mengakses relasi (`$jadwal->admin`, `$booking->praPendaftaranPerkara`, `$booking->klien`) dalam loop. Jika controller tidak menyertakan eager loading yang tepat, menghasilkan N+1 queries (50 jadwal = 51 queries).

**Rekomendasi:** Verifikasi kedua controller menyertakan `->with([...])` yang lengkap. Tambahkan Laravel Debugbar selama development untuk mendeteksi N+1.

---

**[HIGH-18]**
**Severity:** High
**Kategori:** Performance — Dashboard
**Lokasi:** `app/Http/Controllers/DashboardController.php` lines 99–130

**Masalah:** Admin dashboard menembak 12 query COUNT terpisah per page load, plus 3 collection queries = **15 total queries per request**. Pola yang sama di klien (10 queries) dan staf legal (9 queries).

**Rekomendasi:** Konsolidasikan COUNT dengan `groupBy('status_pengajuan')` atau `selectRaw('SUM(CASE WHEN...)')`. Tambahkan caching 60 detik.

---

**[HIGH-19]**
**Severity:** High
**Kategori:** Testing — Service Unit Tests
**Lokasi:** Semua `app/Services/*.php`

**Masalah:** Semua service hanya ditest melalui HTTP feature tests. Logic bisnis paling kritis (`lockForUpdate`, state transitions, exception guards) tidak ter-cover oleh unit test yang isolated.

**Dampak:** Jika route atau middleware berubah, semua service tests bisa break meski service-nya benar.

---

**[HIGH-20]**
**Severity:** High
**Kategori:** Business Rule — Controller
**Lokasi:** `app/Http/Controllers/Admin/KategoriPerkaraController.php`, `app/Http/Controllers/Admin/StafLegalController.php`

**Masalah:** Kedua controller melakukan direct DB manipulation (`create()`, `update()`) tanpa service layer. Melanggar aturan di `AGENTS.md` (Coding Rule #10: service class untuk logika bisnis penting).

**Rekomendasi:** Buat `KategoriPerkaraService` dan `StafLegalService`. Pindahkan business logic ke sana.

---

**[HIGH-21]**
**Severity:** High
**Kategori:** Business Flow
**Lokasi:** `app/Services/PraPendaftaranPerkaraService.php`, `app/Services/DokumenPerkaraService.php`

**Masalah:** `AGENTS.md` mewajibkan satu transaction untuk membuat pra-pendaftaran + dokumen + riwayat_status. Implementasi saat ini menggunakan dua transaction terpisah. Jika pengajuan berhasil dibuat (TX1 commit) tapi upload dokumen gagal (TX2 rollback), tersisa record pengajuan tanpa dokumen dengan tidak ada cleanup mechanism.

**Rekomendasi:** Bungkus kedua service call dalam satu outer `DB::transaction()` di controller, atau buat combined service method.

---

**[HIGH-22]**
**Severity:** High
**Kategori:** Security — Dead Code
**Lokasi:** `app/Http/Controllers/ProfileController.php` lines 41–57

**Masalah:** Method `destroy()` dapat menghapus akun user manapun yang sedang login tanpa role check. Tidak ada route yang terdaftar saat ini (dead code), tetapi jika route ditambahkan secara tidak sengaja di masa depan, admin atau staf_legal bisa self-delete. Scope fitur tidak mencakup penghapusan akun.

**Rekomendasi:** Hapus method `destroy()` dari `ProfileController` karena bukan bagian dari scope project.

---

## 🟡 MEDIUM — Boleh diperbaiki setelah frontend, sebelum production

---

**[MED-01]**
**Severity:** Medium
**Kategori:** Security — Mass Assignment
**Lokasi:** `app/Models/PraPendaftaranPerkara.php` lines 25–27

`status_pengajuan` dan `tanggal_pengajuan` ada di `$fillable`. Sesuai `AGENTS.md`: *"Jangan menerima `status_pengajuan` dari form Klien."* Model tidak melindungi dari kesalahan controller.

---

**[MED-02]**
**Severity:** Medium
**Kategori:** Security — Mass Assignment
**Lokasi:** `app/Models/BookingKonsultasi.php` lines 20–34

Field khusus Admin (`link_konsultasi`, `lokasi_konsultasi`, `dikonfirmasi_pada`, `id_admin_konfirmasi`, `status_konfirmasi_konsultasi`) ada di `$fillable` yang memungkinkan mass assignment bug di route Klien.

---

**[MED-03]**
**Severity:** Medium
**Kategori:** Validation
**Lokasi:** `app/Http/Requests/StafLegal/StoreVerifikasiBerkasRequest.php`

Tidak ada `max:` constraint pada `catatan_umum` dan `dokumen.*.catatan`. Staf legal bisa submit teks tidak terbatas. Tidak ada `required_if` yang mewajibkan `catatan` ketika `status_dokumen = "perlu_perbaikan"`, yang menjadi akar dari bug di SV-02/HIGH-02.

---

**[MED-04]**
**Severity:** Medium
**Kategori:** Validation
**Lokasi:** `app/Http/Requests/Klien/StorePraPendaftaranPerkaraRequest.php`

`"kronologi"` tidak memiliki `max:`. Klien bisa submit megabytes teks.

---

**[MED-05]**
**Severity:** Medium
**Kategori:** Validation — Missing Field
**Lokasi:** `app/Http/Requests/ProfileUpdateRequest.php`

Field `no_telepon` tidak ada dalam rules. Klien tidak punya jalur self-service untuk mengupdate nomor telepon mereka.

---

**[MED-06]**
**Severity:** Medium
**Kategori:** Data Integrity — Database
**Lokasi:** `database/migrations/2026_06_17_000002_create_kategori_perkara_table.php`

Tidak ada `UNIQUE` constraint pada `nama_kategori`. Seeder menggunakan `firstOrCreate()` yang mengimplikasikan uniqueness, tetapi tidak ada protection di level DB.

---

**[MED-07]**
**Severity:** Medium
**Kategori:** Database — Index
**Lokasi:** `database/migrations/2026_06_25_000002_create_permintaan_reschedule_table.php`

FK columns (`id_booking`, `id_user`, `id_jadwal_baru`, `id_booking_baru`) dan `status_reschedule`, `tanggal_pengajuan` tidak memiliki explicit index. `status_reschedule` diquery sangat sering tapi akan full table scan.

---

**[MED-08]**
**Severity:** Medium
**Kategori:** Database — Composite Index
**Lokasi:** Multiple migrations

Query patterns yang sering muncul tanpa composite index:
- `WHERE id_pendaftaran = X AND status_booking = 'aktif'` (booking_konsultasi)
- `WHERE id_pendaftaran = X AND status_dokumen IN (...)` (dokumen_perkara)
- `WHERE id_booking = X AND status_reschedule = 'menunggu_persetujuan'` (permintaan_reschedule)

---

**[MED-09]**
**Severity:** Medium
**Kategori:** Race Condition — Fragile Guard
**Lokasi:** `app/Services/BookingKonsultasiService.php`

`$hasActiveBooking` query tidak menggunakan `lockForUpdate()`. Protection bergantung secara implisit pada perubahan `status_pengajuan` — coupling yang tidak obvious dan bisa break jika ada refactor.

---

**[MED-10]**
**Severity:** Medium
**Kategori:** Business Rule
**Lokasi:** `app/Services/SelesaikanKonsultasiService.php`

`jadwal_konsultasi.status_slot` tetap `"terisi"` selamanya setelah konsultasi selesai. Slot tidak bisa dibersihkan, diarsipkan, atau digunakan kembali. `ensureNotTerisi()` di service mencegah modifikasi apapun.

---

**[MED-11]**
**Severity:** Medium
**Kategori:** Business Rule — Bug Logic
**Lokasi:** `app/Services/PermintaanRescheduleService.php` lines 109–112

Penggunaan `?:` (Elvis) bukan `??` (null coalescing) untuk fallback `metode_konsultasi`. Empty string `""` dari database akan trigger fallback diam-diam.

---

**[MED-12]**
**Severity:** Medium
**Kategori:** Business Rule — Coupling
**Lokasi:** `app/Services/VerifikasiBerkasService.php` line 66

`status_verifikasi` digunakan langsung sebagai `status_pengajuan`. Jika vocabulary kedua status pernah diverge, ini silently menulis status invalid.

---

**[MED-13]**
**Severity:** Medium
**Kategori:** Security — Route
**Lokasi:** `app/Http/Controllers/Admin/StafLegalController.php`, `routes/web.php`

Route `{user}` binding bisa me-resolve akun apapun termasuk Admin. Hanya dilindungi oleh manual `ensureStafLegal()` check. Jika check terlewat di future code, Admin bisa jadi target.

---

**[MED-14]**
**Severity:** Medium
**Kategori:** Security — Middleware
**Lokasi:** `app/Http/Middleware/EnsureUserHasRole.php` line 18

`abort_if(! $user || ...)` mengembalikan HTTP 403 untuk unauthenticated user, bukan redirect ke login.

---

**[MED-15]**
**Severity:** Medium
**Kategori:** Business Flow
**Lokasi:** `app/Services/PraPendaftaranPerkaraService.php`

Tidak ada limit jumlah `pra_pendaftaran_perkara` aktif per klien. Klien bisa flood antrian verifikasi staf legal.

---

**[MED-16]**
**Severity:** Medium
**Kategori:** Error Handling — Controller
**Lokasi:** `app/Http/Controllers/Admin/BookingKonsultasiController.php` method `confirm()`

Method `confirm()` tidak menangkap `ValidationException` dari service (misalnya booking tidak aktif). `selesai()` sudah menangkap dengan try-catch. Inkonsistensi ini menyebabkan error ditampilkan sebagai field validation error daripada flash message.

---

**[MED-17]**
**Severity:** Medium
**Kategori:** Route — Missing
**Lokasi:** `routes/web.php` (admin group)

Tidak ada route untuk Admin mengakses/download dokumen perkara. Admin harus menggunakan akses database langsung untuk menginspeksi dokumen jika diperlukan.

---

**[MED-18]**
**Severity:** Medium
**Kategori:** Testing — Assertion Quality
**Lokasi:** Multiple test files

Banyak test hanya mengassert HTTP status tanpa verifikasi state database atau konten response. Contoh: `DashboardTest` hanya cek `assertOk()`, `LaporanTest` hanya cek `assertOk()`.

---

**[MED-19]**
**Severity:** Medium
**Kategori:** Testing — Role Isolation
**Lokasi:** `tests/Feature/Auth/RoleAccessTest.php`

Test tidak memverifikasi: Staf Legal mengakses `admin.laporan.index`, Admin submit ke `staf-legal.verifikasi-berkas.store`, Guest mengakses `admin.dashboard` atau `staf-legal.dashboard`.

---

**[MED-20]**
**Severity:** Medium
**Kategori:** Testing — Edge Cases
**Lokasi:** Multiple test files

Missing failure path tests: duplicate email registration, pengajuan tanpa dokumen di verifikasi, booking dengan jadwal yang sudah `terisi`, password reset token expired, reschedule approve dengan jadwal sama seperti lama.

---

**[MED-21]**
**Severity:** Medium
**Kategori:** Blade — UX Bug
**Lokasi:** `resources/views/admin/permintaan-reschedule/show.blade.php` lines 195, 212

Dua form di halaman yang sama menggunakan `name="catatan_admin"`. Saat validasi gagal, `old('catatan_admin')` mengisi **kedua** textarea dengan value yang sama — admin yang mengetik catatan tolak akan melihat draft-nya juga muncul di form setujui.

---

**[MED-22]**
**Severity:** Medium
**Kategori:** Clean Code — Controller
**Lokasi:** `app/Http/Controllers/Admin/LaporanController.php`

5 method menggunakan `$request->validate([...])` inline alih-alih Form Request — melanggar aturan konsistensi dan `AGENTS.md` Coding Rule #7.

---

**[MED-23]**
**Severity:** Medium
**Kategori:** Clean Code — Fragility
**Lokasi:** `app/Services/JadwalKonsultasiService.php` lines 64–66

```php
substr((string) $jadwalKonsultasi->waktu_mulai, 0, 5)
```
Hardcode asumsi format `HH:MM:SS`. Jika cast TIME ke Carbon ditambahkan di masa depan, `substr(..., 0, 5)` akan return `"2000-"` dan overlap protection diam-diam rusak.

---

**[MED-24]**
**Severity:** Medium
**Kategori:** Documentation
**Lokasi:** `docs/VALIDATION_RULES.md` vs implementasi aktual

Laporan menggunakan inline validation bukan Form Request, melanggar aturan yang didokumentasikan.

---

**[MED-25]**
**Severity:** Medium
**Kategori:** Documentation
**Lokasi:** `docs/DEPLOYMENT_NOTES.md`

Tidak ada panduan `memory_limit` PHP untuk laporan yang mengambil seluruh dataset ke memory. Deployment default `128M` bisa crash.

---

**[MED-26]**
**Severity:** Medium
**Kategori:** Business Rule — Locking Inconsistency
**Lokasi:** `app/Services/PerbaikanDokumenService.php`

`verifikasi_berkas` di-load tanpa `lockForUpdate()` dalam transaction yang sama dimana entitas lain dilocked. Inkonsistensi ini bisa menjadi bug jika future code menambah write ke `verifikasi` dalam method yang sama.

---

## 🟢 LOW — Dapat didefer, cleanup post-MVP

---

**[LOW-01]** Database — `status_akun` tidak memiliki `DEFAULT` value di migration. Setiap code path yang membuat User tanpa set eksplisit akan error.

**[LOW-02]** Model — `JadwalKonsultasi.waktu_mulai`/`waktu_selesai` tidak di-cast. Konsistensi dengan kolom `tanggal` yang di-cast ke `date`.

**[LOW-03]** Model — `PraPendaftaranPerkara.dokumenAktif()` menggunakan chained scope pada relation — fragile dengan IDE static analysis.

**[LOW-04]** Model — `BookingKonsultasi` tidak memiliki relationship helper `permintaanReschedulePending()`. Logic duplikat di dua service.

**[LOW-05]** Model — `User.$hidden` belum mencantumkan `remember_token` (perlu ditambahkan bersamaan dengan fix CRIT-02).

**[LOW-06]** Validation — Semua 18 Form Request tidak mendefinisikan `messages()` dan `attributes()`. Error messages menampilkan snake_case field names seperti `"id_kategori"` dan `"dokumen.5.status_dokumen"`.

**[LOW-07]** Validation — `StoreBookingKonsultasiRequest` dan `ApprovePermintaanRescheduleRequest` tidak memeriksa `status_slot = 'tersedia'` — service menangani ini, tapi UX lebih baik jika ditangkap di validasi.

**[LOW-08]** Security — `link_konsultasi` di anchor `href` tidak divalidasi protokolnya secara eksplisit (hanya `url` rule). Tambahkan `starts_with:https://,http://`.

**[LOW-09]** Blade — Flash message dirender secara tidak konsisten antar views. Tidak ada shared `<x-flash-message />` component.

**[LOW-10]** Blade — `status-badge.blade.php` menggunakan mechanical `str_replace`/`ucfirst` bukan lookup table. `"berkas_tidak_lengkap"` menjadi "Berkas tidak lengkap" (casing tidak polished).

**[LOW-11]** Blade — `klien/pra-pendaftaran/index.blade.php` menggunakan raw `str_replace` untuk display status alih-alih component `<x-status-badge>`. Inkonsistensi visual antar role.

**[LOW-12]** Route — Sub-action route naming tidak konsisten: beberapa Indonesia (`konfirmasi`, `setujui`, `tolak`), beberapa English noun (`status`, `password`).

**[LOW-13]** Route — Tidak ada route `profile.destroy` yang terdaftar (method ada tapi orphan). Perlu dibuang atau didokumentasikan.

**[LOW-14]** Testing — `tests/Unit/ExampleTest.php` berisi `assertTrue(true)` — dead test tanpa nilai.

**[LOW-15]** Testing — `JadwalKonsultasiFactory` hardcode waktu `09:00–10:00` untuk semua slot. Beberapa factory call dalam satu test menghasilkan duplicate constraint conflict.

**[LOW-16]** Testing — `TestCase.php` kosong tanpa custom helpers atau shared setup.

**[LOW-17]** Clean Code — `DashboardController::stafLegal()` menerima `Request $request` hanya untuk mengekstrak `id_user`. Gunakan `Auth::id()` langsung.

**[LOW-18]** Documentation — `FEATURE_LIST.md` vs `AGENTS.md` saling bertentangan tentang penggunaan email (forgot password butuh email, tapi AGENTS.md bilang "tidak menggunakan email pada fase awal"). Perlu diklarifikasi bahwa password reset adalah satu-satunya penggunaan email yang diizinkan.

---

# POSITIVE FINDINGS

Berikut yang sudah diimplementasikan dengan baik dan menjadi fondasi yang kuat:

**✅ Service Layer Architecture**
Service layer ada dan konsisten untuk semua proses bisnis utama. `VerifikasiBerkasService`, `PermintaanRescheduleService`, `BookingKonsultasiService`, dan `SelesaikanKonsultasiService` semuanya menggunakan `DB::transaction()` dengan `lockForUpdate()`. Ini adalah pola yang benar dan menunjukkan pemahaman yang baik tentang concurrency control.

**✅ Eager Loading di Dashboard dan Controller**
`DashboardController` menggunakan `->with([...])` dengan benar untuk semua collection queries. `PraPendaftaranPerkaraController`, `VerifikasiBerkasController`, dan controller lainnya juga menggunakan eager loading. Ini menunjukkan kesadaran N+1 yang baik.

**✅ CSRF Protection Konsisten**
Semua form menggunakan `@csrf`. Semua PATCH/PUT/DELETE menggunakan `@method()`. Tidak ditemukan form yang rentan CSRF.

**✅ XSS Protection**
Tidak ditemukan penggunaan `{!! !!}` untuk output user-generated content. Semua output menggunakan `{{ }}` dengan HTML entity escaping.

**✅ Role-Based Navigation Gating**
`navigation.blade.php` menggunakan `isKlien()`, `isAdmin()`, `isStafLegal()` helper methods dengan benar. Menu tidak bocor antar role.

**✅ Status Flow Enforcement**
Business flow kritis sudah dilindungi di dua lapisan (controller + service):
- Klien tidak bisa booking tanpa `berkas_lengkap` ✓
- Staf legal tidak bisa verifikasi `berkas_lengkap` atau `selesai` ✓
- Admin tidak bisa menyelesaikan booking dengan pending reschedule ✓
- Admin tidak bisa konfirmasi booking yang sudah `selesai` ✓

**✅ Comprehensive Manual Testing Plan**
`docs/MANUAL_TESTING_PLAN.md` sangat komprehensif (17 area testing, ratusan skenario). Ini adalah aset berharga untuk QA.

**✅ Custom Primary Key Implementation**
Semua model mendefinisikan `$table`, `$primaryKey`, `$incrementing`, `$keyType`, dan `getRouteKeyName()` secara eksplisit. Tidak ada yang mengandalkan default Laravel yang bertentangan dengan rancangan database.

**✅ Ownership Check Konsisten**
Semua route Klien memiliki ownership check yang konsisten: `abort_unless($pengajuan->id_user === $request->user()->id_user, 403)`. Tidak ditemukan IDOR yang obvious.

**✅ Reusable Scope di Model**
`DokumenPerkara::scopeAktif()` dan `scopeDiganti()`, serta relationship `PraPendaftaranPerkara::dokumenAktif()` dan `riwayatDokumen()` menunjukkan penggunaan scope yang tepat untuk query yang berulang.

**✅ Transaction Atomicity di Service Kritis**
`PermintaanRescheduleService::approve()` melakukan locking pada 5 entitas berbeda (permintaan, booking lama, pengajuan, jadwal lama, jadwal baru) sebelum melakukan perubahan. Ini adalah implementation yang benar untuk operasi multi-tabel yang kompleks.

**✅ Test Coverage untuk Core Happy Paths**
Test ada untuk: auth flow, booking konsultasi, verifikasi berkas, perbaikan dokumen, pra-pendaftaran, jadwal konsultasi, konfirmasi konsultasi, penyelesaian konsultasi, reschedule. Assertions mengecek database state, bukan hanya HTTP status.

**✅ Form Request Authorization Hardening**
Setelah audit Fase 13, semua Form Request kini memiliki `authorize()` yang memeriksa role — bukan hanya `return true`.

---

# ARCHITECTURE REVIEW

**Score: 72 / 100**

**Kekuatan:**
- Controller → Service → Model layering diterapkan dengan konsisten untuk semua fitur utama
- Service layer memiliki single responsibility yang jelas untuk setiap domain bisnis
- Transaction + locking strategy sudah benar dan kompleks (multi-table lock ordering)
- Route grouping per role dengan middleware yang tepat

**Kelemahan:**
- `DashboardController` melanggar SRP (satu class, tiga responsibility berbeda)
- `LaporanController` adalah fat controller dengan 300+ baris dan validasi inline
- `KategoriPerkaraController` dan `StafLegalController` bypass service layer
- Tidak ada Enum sebagai single source of truth untuk status values
- Database tidak memiliki safety net untuk application-level business rules (missing unique constraints, missing composite indexes)

---

# SECURITY REVIEW

**Score: 71 / 100**

**Critical Gap:**
- **Document storage di public disk** adalah temuan paling serius. Seluruh authorization logic di controller tidak efektif karena file bisa diakses langsung. Ini harus diperbaiki **sebelum production** tanpa pengecualian.

**Foundation yang Solid:**
- Ownership check hadir di semua route Klien
- Role middleware diterapkan di semua group
- CSRF protection konsisten
- `lockForUpdate()` digunakan untuk operasi sensitif
- Password di-hash, tidak pernah di-log

**Gap Lainnya:**
- Password reset bypass `EnsureAccountIsActive` (meski window-nya sempit)
- Password policy staf_legal lebih lemah dari klien padahal lebih privileged
- `AdminSeeder` menggunakan `env()` langsung yang break setelah `config:cache`
- Tidak ada rate limiting pada endpoint sensitif (selain login yang sudah ada via Breeze)

---

# CLEAN CODE REVIEW

**Score: 63 / 100**

**Kekuatan:**
- Penamaan variabel dan method dalam Bahasa Indonesia konsisten dengan domain bisnis
- Service methods memiliki nama yang deskriptif (`ensureKlienCanRequest`, `ensureBookingIsActive`)
- Code navigation mudah karena folder structure jelas

**Kelemahan:**
- Magic strings proliferasi — status values muncul sebagai literal di 8+ file tanpa Enum
- `LaporanController` melanggar DRY dengan 5x duplikasi validation block
- Tidak ada return type declaration pada beberapa private methods
- Status-to-color mapping duplikat di minimal 3 Blade views berbeda
- `DashboardController` 232 baris dengan 5 method berbeda responsibility
- Triple Elvis operator fallback yang membingungkan di `PermintaanRescheduleService`

---

# TEST REVIEW

**Score: 52 / 100**

**Kekuatan:**
- Feature tests ada untuk semua happy path utama
- Tests menggunakan `assertDatabaseHas()` untuk verifikasi state
- `CreatesTestingData` concern membantu test setup yang konsisten
- Test coverage untuk: booking, verifikasi, reschedule, selesai konsultasi, laporan, jadwal

**Kelemahan Kritis:**
- **SQLite vs MySQL gap** membuat semua `lockForUpdate()` tests tidak meaningful
- **5 modul** tidak memiliki satu test pun
- **Endpoint paling sensitif** (document download dengan auth) tidak memiliki test
- Factory data inconsistency (`BookingKonsultasiFactory`, `CatatanVerifikasiFactory`) menghasilkan data tidak valid
- Tidak ada unit tests untuk service layer secara isolated
- Tidak ada concurrent/race condition tests

---

# MAINTAINABILITY SCORE

**Score: 62 / 100**

Codebase ini maintainable untuk scope saat ini dengan developer yang sudah familiar. Namun ada beberapa "traps" yang akan menyebabkan masalah saat project berkembang:

1. Magic strings di 8+ file — setiap penambahan status memerlukan update manual di banyak tempat
2. SQLite test gap — developer tidak akan sadar jika race condition guard-nya rusak
3. 5 modul tanpa test — perubahan di `StafLegalController` atau `KategoriPerkaraController` tidak akan terdeteksi regresinya
4. Factory inconsistency — test baru yang menggunakan factory langsung akan menghasilkan data invalid

---

# FINAL VERDICT

```
════════════════════════════════════════════════════════════

READY WITH MINOR IMPROVEMENT

════════════════════════════════════════════════════════════
```

Backend ini **memiliki fondasi arsitektur yang baik** dan layak dijadikan dasar pengembangan frontend — dengan syarat 5 item berikut diselesaikan terlebih dahulu:

### Wajib Diselesaikan Sebelum Frontend Development

| # | ID | Item | Alasan |
|---|---|---|---|
| 1 | CRIT-01 | Pindahkan storage dokumen ke disk `local` (private) | Security breach langsung — dokumen accessible tanpa auth |
| 2 | CRIT-09 | Tambahkan `authorize()` ke `ProfileUpdateRequest` | Fitur profile update sepenuhnya broken (HTTP 403 selalu) |
| 3 | CRIT-02 | Tambahkan `remember_token` ke migration users | Login crash di production MySQL |
| 4 | HIGH-02 | Fix `$documentData["catatan"]` null access + `required_if` validation | PHP fatal error pada flow verifikasi berkas tidak lengkap |
| 5 | HIGH-05 | Tambahkan `Enum` atau minimal status constants | Frontend akan mengkonsumsi status values — perlu satu source of truth |

### Sangat Disarankan Sebelum Frontend

- **HIGH-17** — Verifikasi dan perbaiki eager loading pada jadwal dan booking index views (N+1)
- **HIGH-13** — Tulis README yang proper (akademik akan membaca ini)
- **CRIT-07** — Buat test untuk 5 modul yang tidak ter-cover sama sekali
- **CRIT-06** — Switch test environment ke MySQL (atau dokumentasikan batasan SQLite secara eksplisit)
- **HIGH-08** — Tambahkan `after_or_equal:today` ke `UpdateJadwalKonsultasiRequest`
- **HIGH-09** — Upgrade password policy staf_legal ke `Password::defaults()`
- **HIGH-10** — Fix `env()` di `AdminSeeder`

### Dapat Didefer ke Post-MVP

Semua temuan LOW, dan sebagian besar MEDIUM yang bersifat clean code / refactoring (Enum, DashboardController split, LaporanController Form Requests, magic strings) dapat diselesaikan dalam sprint cleanup setelah frontend selesai.

---

> **Ringkasan untuk Pemilik Project:** Backend ini **siap untuk dikembangkan frontendnya** setelah 5 item critical di atas diperbaiki. Arsitektur service + transaction + locking sudah benar dan merupakan fondasi yang solid. Yang perlu ditangani sekarang adalah security gap pada document storage, 1 broken form request, dan beberapa isu stability. Sisanya adalah quality improvements yang bisa dikerjakan secara iteratif.