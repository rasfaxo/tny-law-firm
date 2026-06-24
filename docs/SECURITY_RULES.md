# SECURITY_RULES.md

## Purpose

Dokumen ini berisi aturan keamanan untuk project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Dokumen ini wajib diikuti saat membuat route, middleware, controller, service, file upload, akses dokumen, dashboard role, dan fitur laporan.

AI agent tidak boleh membuat fitur yang melewati otorisasi, ownership check, atau proteksi dokumen perkara.

---

## Source of Truth

Dokumen ini harus konsisten dengan:

1. `AGENTS.md`
2. `docs/PROJECT_CONTEXT.md`
3. `docs/DATABASE_PLAN.md`
4. `docs/MODEL_RELATION_PLAN.md`
5. `docs/STATUS_RULES.md`
6. `docs/VALIDATION_RULES.md`
7. `docs/FEATURE_LIST.md`

Jika ada konflik struktur database, ikuti:

```text
docs/DATABASE_PLAN.md
```

Jika ada konflik status atau role, ikuti:

```text
docs/STATUS_RULES.md
```

---

## General Security Rules

Aturan umum keamanan:

1. Semua halaman utama sistem wajib berada di balik authentication.
2. Semua route role-based wajib dilindungi middleware role.
3. Semua data Klien wajib dilindungi ownership check.
4. Semua dokumen perkara wajib dilindungi authorization check.
5. Jangan mempercayai `id_user`, `role`, `status`, atau `file_path` dari request.
6. Jangan menampilkan data Klien lain kepada Klien.
7. Jangan memberikan akses Admin ke proses verifikasi milik Staf Legal tanpa aturan khusus.
8. Jangan memberikan akses Staf Legal ke fitur pengelolaan Admin.
9. Jangan menggunakan input user langsung pada raw SQL.
10. Jangan menyimpan password dalam bentuk plain text.
11. Jangan menampilkan pesan error teknis sensitif kepada user.
12. Jangan membuat fitur di luar ruang lingkup skripsi tanpa persetujuan.

---

# Authentication Security

## Login Security

Aturan:

1. Login menggunakan `email` dan `password`.
2. Password wajib disimpan dalam bentuk hash.
3. Akun dengan `status_akun = nonaktif` tidak boleh login.
4. Setelah login, user diarahkan berdasarkan `role`.
5. Role valid hanya:

   * `klien`
   * `admin`
   * `staf_legal`
6. Jika role tidak valid, user tidak boleh diberi akses dashboard.
7. Logout harus menghapus session login dengan benar.
8. Laravel Breeze login throttling tidak boleh dihapus.
9. Session harus diregenerasi setelah login mengikuti mekanisme Laravel.
10. Session harus di-invalidasi saat logout mengikuti mekanisme Laravel.

---

## Register Security

Aturan:

1. Registrasi publik hanya untuk role `klien`.
2. Form registrasi publik tidak boleh menerima input `role`.
3. Form registrasi publik tidak boleh menerima input `status_akun`.
4. `role` registrasi publik ditentukan server sebagai `klien`.
5. `status_akun` registrasi publik ditentukan server sebagai `aktif`.
6. Password wajib divalidasi dan di-hash.
7. Email wajib unique.

---

## Forgot Password / Reset Password

Aturan:

1. Forgot password diperbolehkan karena sudah disetujui.
2. Implementasi wajib mengikuti mekanisme bawaan Laravel Breeze.
3. Fitur ini hanya untuk reset password akun.
4. Token reset password disimpan melalui tabel `password_reset_tokens`.
5. Password baru wajib divalidasi, di-hash, dan disimpan dalam bentuk hash.
6. Email hanya digunakan untuk pengiriman link reset password.

---

# Role-Based Access Control

## Role Access Matrix

| Fitur                                              | Klien | Staf Legal | Admin |
| -------------------------------------------------- | ----: | ---------: | ----: |
| Dashboard Klien                                    |   Yes |         No |    No |
| Dashboard Staf Legal                               |    No |        Yes |    No |
| Dashboard Admin                                    |    No |         No |   Yes |
| Profil Klien milik sendiri                         |   Yes |         No |    No |
| Pengajuan pra-pendaftaran milik sendiri            |   Yes |         No |    No |
| Melihat semua pengajuan untuk verifikasi           |    No |        Yes |    No |
| Verifikasi berkas                                  |    No |        Yes |    No |
| Melihat seluruh data pra-pendaftaran administratif |    No |         No |   Yes |
| Kelola pengguna                                    |    No |         No |   Yes |
| Kelola kategori perkara                            |    No |         No |   Yes |
| Kelola jadwal konsultasi                           |    No |         No |   Yes |
| Memilih jadwal konsultasi                          |   Yes |         No |    No |
| Melihat booking konsultasi milik sendiri           |   Yes |         No |    No |
| Melihat permintaan reschedule milik sendiri        |   Yes |         No |    No |
| Mengonfirmasi detail konsultasi                    |    No |         No |   Yes |
| Menyetujui / menolak reschedule                    |    No |         No |   Yes |
| Laporan pra-pendaftaran                            |    No |         No |   Yes |

---

## Middleware Rules

Gunakan middleware untuk membatasi akses berdasarkan role.

Contoh struktur route:

```php
Route::middleware(['auth', 'role:klien'])->group(function () {
    // route Klien
});

Route::middleware(['auth', 'role:staf_legal'])->group(function () {
    // route Staf Legal
});

Route::middleware(['auth', 'role:admin'])->group(function () {
    // route Admin
});
```

Aturan:

1. Route Klien hanya boleh diakses role `klien`.
2. Route Staf Legal hanya boleh diakses role `staf_legal`.
3. Route Admin hanya boleh diakses role `admin`.
4. Jangan hanya menyembunyikan menu di Blade tanpa melindungi route.
5. Middleware wajib memblokir akses langsung melalui URL.
6. Jika role tidak sesuai, redirect atau abort dengan status yang sesuai.

---

# Ownership Rules

## Data Klien

Aturan:

1. Klien hanya boleh melihat data miliknya sendiri.
2. Klien hanya boleh mengubah profil miliknya sendiri.
3. Klien hanya boleh melihat pengajuan miliknya sendiri.
4. Klien hanya boleh mengunggah ulang dokumen untuk pengajuan miliknya sendiri.
5. Klien hanya boleh membuat booking untuk pengajuan miliknya sendiri.
6. Klien hanya boleh melihat booking konsultasi miliknya sendiri.
7. Klien hanya boleh melihat dan mengajukan reschedule untuk booking miliknya sendiri.
8. Klien tidak boleh mengakses data Klien lain walaupun mengetahui ID dari URL.

Contoh ownership check:

```php
$pengajuan = PraPendaftaranPerkara::where('id_pendaftaran', $id)
    ->where('id_user', auth()->id())
    ->firstOrFail();
```

Aturan penting:

1. Jangan mengambil data Klien hanya dengan `findOrFail($id)` pada halaman Klien.
2. Query halaman Klien wajib difilter dengan `id_user = auth()->id()`.
3. `id_user` tidak boleh berasal dari request.
4. `id_user` harus berasal dari user yang sedang login.

---

## Data Admin

Aturan:

1. Admin dapat melihat data administratif seluruh pengajuan.
2. Admin dapat melihat laporan pra-pendaftaran.
3. Admin dapat mengelola pengguna, kategori perkara, dan jadwal konsultasi.
4. Admin dapat mengonfirmasi detail konsultasi untuk booking yang valid.
5. Admin dapat menyetujui atau menolak permintaan reschedule sesuai alur sistem.
6. Admin tidak mengambil alih proses verifikasi berkas milik Staf Legal.
7. Admin tidak boleh mengubah status pengajuan sembarangan di luar alur sistem.
8. Admin tidak boleh menghapus data penting jika sudah memiliki relasi.

---

## Data Staf Legal

Aturan:

1. Staf Legal dapat melihat pengajuan yang perlu diverifikasi.
2. Staf Legal dapat melihat dokumen untuk kebutuhan verifikasi.
3. Staf Legal dapat membuat verifikasi berkas.
4. Staf Legal dapat memberi catatan umum dan catatan per dokumen.
5. Staf Legal tidak boleh mengakses fitur kelola pengguna Admin.
6. Staf Legal tidak boleh mengakses fitur laporan Admin.
7. Staf Legal tidak boleh mengubah jadwal konsultasi.
8. Staf Legal tidak boleh mengonfirmasi detail konsultasi.
9. Staf Legal tidak boleh memproses permintaan reschedule.

---

# Consultation and Reschedule Access Security

## Booking Konsultasi dan Reschedule

Aturan:

1. Klien hanya boleh melihat booking konsultasi miliknya sendiri.
2. Klien hanya boleh melihat permintaan reschedule miliknya sendiri.
3. Admin boleh melihat data booking dan reschedule untuk kebutuhan administratif.
4. Staf Legal tidak boleh mengakses detail link/lokasi konsultasi kecuali ada kebutuhan yang disetujui secara khusus.
5. Link konsultasi online hanya boleh ditampilkan kepada Klien pemilik booking dan Admin.
6. Lokasi konsultasi offline hanya boleh ditampilkan kepada Klien pemilik booking dan Admin.
7. Data konfirmasi konsultasi tidak boleh dibuka lintas ownership.
8. Permintaan reschedule harus selalu dicek terhadap kepemilikan booking dan role pemroses.

---

# Document Access Security

Dokumen perkara adalah data sensitif dan harus dilindungi.

## Storage Rules

Aturan:

1. Dokumen disimpan pada:

```text
storage/app/public/dokumen-perkara
```

2. Database hanya menyimpan metadata dan `file_path`.
3. Jangan menyimpan file dokumen langsung di database.
4. Jangan menerima `file_path` dari request.
5. Jangan menggunakan nama asli file sebagai nama file final.
6. Nama file final harus random atau unik.
7. File lama tidak boleh ditimpa saat re-upload.
8. Directory listing tidak boleh diaktifkan.

---

## Document Viewing and Download Rules

Aturan:

1. Dokumen tidak boleh dibuka tanpa authorization.
2. Jangan menampilkan raw `file_path` kepada user.
3. Jangan membuat link dokumen yang dapat diakses bebas tanpa pengecekan role dan ownership.
4. Akses dokumen sebaiknya melalui controller khusus yang melakukan authorization check.
5. Klien hanya boleh membuka dokumen milik pengajuannya sendiri.
6. Staf Legal boleh membuka dokumen untuk pengajuan yang sedang diverifikasi.
7. Admin boleh membuka dokumen hanya untuk kebutuhan administratif yang sesuai scope sistem.
8. Jika user tidak berhak membuka dokumen, sistem harus menolak akses.

Contoh pendekatan aman:

```php
$document = DokumenPerkara::with('praPendaftaranPerkara')
    ->where('id_dokumen', $id)
    ->firstOrFail();

if (auth()->user()->role === 'klien') {
    abort_unless(
        $document->praPendaftaranPerkara->id_user === auth()->id(),
        403
    );
}
```

Catatan:

1. Walaupun dokumen disimpan pada disk `public`, aplikasi tidak boleh sembarangan menampilkan URL langsung tanpa kontrol akses. Jika diperlukan keamanan yang lebih ketat pada deployment, pertimbangkan penyajian file melalui route/controller dengan authorization.
2. Jika tetap menggunakan disk `public`, aplikasi tidak boleh menampilkan URL langsung ke file dokumen. File harus diakses melalui route/controller yang melakukan authorization check terlebih dahulu.
3. Untuk keamanan yang lebih ketat pada deployment, dokumen perkara sebaiknya dipindahkan ke private storage dan disajikan melalui controller setelah authorization check. Perubahan ini hanya boleh dilakukan jika disetujui pemilik project karena dapat memengaruhi konfigurasi storage dan deployment.


---

# File Upload Security

Aturan upload:

1. Format yang diperbolehkan hanya PDF, JPG, JPEG, dan PNG.
2. Ukuran maksimal adalah 5 MB per file.
3. Validasi wajib memeriksa extension dan MIME type.
4. File harus disimpan menggunakan nama random atau unik.
5. Nama asli file tidak boleh dijadikan nama file final.
6. File path tidak boleh berasal dari request.
7. Jangan menimpa file lama saat unggah ulang.
8. Re-upload harus membuat record dokumen baru.
9. Dokumen lama diberi status `diganti`.
10. Dokumen baru diberi status `terkirim`.

Validasi dasar:

```php
'file' => [
    'required',
    'file',
    'max:5120',
    'mimes:pdf,jpg,jpeg,png',
]
```

Validasi tambahan jika diperlukan:

```php
'mimetypes:application/pdf,image/jpeg,image/png'
```

---

# Route Security

Aturan:

1. Semua route dashboard wajib memakai middleware `auth`.
2. Semua route role wajib memakai middleware role.
3. Route Klien wajib melakukan ownership check.
4. Route dokumen wajib melakukan authorization check.
5. Route Admin tidak boleh bisa diakses Staf Legal.
6. Route Staf Legal tidak boleh bisa diakses Admin kecuali memang disetujui.
7. Route publik hanya boleh untuk login, register, dan halaman yang memang publik.
8. Route aksi `POST`, `PUT`, `PATCH`, dan `DELETE` wajib dilindungi CSRF.
9. Jangan membuat route debug di production.
10. Jangan menaruh aksi sensitif pada route `GET`.

---

# Controller and Service Security

Aturan:

1. Controller tidak boleh mempercayai input request secara mentah.
2. Gunakan Form Request untuk validasi input utama.
3. Gunakan service class untuk logic multi-tabel jika proses kompleks.
4. Gunakan database transaction untuk proses multi-tabel.
5. Jangan menyimpan semua business logic kompleks di controller.
6. Jangan mengubah status penting berdasarkan hidden input.
7. Jangan menerima `id_user` dari request untuk menentukan pemilik data.
8. Jangan menerima `role` dari request publik.
9. Jangan menerima `status_pengajuan` dari form Klien.
10. Jangan menerima `tanggal_booking` dari request.
11. Jangan menerima `status_konfirmasi_konsultasi`, `dikonfirmasi_pada`, atau `id_admin_konfirmasi` dari request Klien.
12. Jangan menerima `status_reschedule`, `id_jadwal_baru`, `id_booking_baru`, atau `tanggal_keputusan` dari request Klien.
13. Field konfirmasi detail konsultasi hanya boleh diproses pada route dan role Admin yang sah.

---

# Database Security

Aturan:

1. Gunakan Eloquent atau query builder dengan parameter binding.
2. Jangan menggunakan raw SQL dengan input user tanpa binding.
3. Jangan membuat tabel baru tanpa persetujuan.
4. Jangan membuat kolom baru tanpa persetujuan.
5. Jangan menggunakan database `ENUM`.
6. Jangan membuat tabel `laporan`.
7. Jangan menjalankan migration sebelum direview dan disetujui.
8. Jangan menggunakan `cascadeOnDelete()` secara otomatis untuk semua foreign key.
9. Jangan menghapus data penting secara otomatis.

---

# Mass Assignment Security

Aturan:

1. Setiap model wajib mengatur `$fillable`.
2. Jangan menggunakan `$guarded = []` tanpa alasan dan persetujuan.
3. Jangan memasukkan field sensitif ke `$fillable` jika tidak perlu.
4. Field seperti `role`, `status_akun`, `status_pengajuan`, `status_booking`, `status_konfirmasi_konsultasi`, `dikonfirmasi_pada`, `id_admin_konfirmasi`, `status_reschedule`, `id_jadwal_baru`, `id_booking_baru`, dan `file_path` hanya boleh diisi oleh server sesuai proses bisnis atau oleh Admin pada alur yang sah.
5. Jangan menggunakan `$request->all()` langsung untuk create/update data sensitif.

Hindari:

```php
User::create($request->all());
```

Gunakan data yang sudah divalidasi dan dikontrol:

```php
User::create([
    'nama' => $validated['nama'],
    'email' => $validated['email'],
    'password' => Hash::make($validated['password']),
    'role' => 'klien',
    'status_akun' => 'aktif',
]);
```

---

# Status Security

Aturan:

1. Status pengajuan tidak boleh dikirim bebas oleh Klien.
2. Status pengajuan ditentukan oleh server berdasarkan proses bisnis.
3. Setiap perubahan `status_pengajuan` wajib dicatat pada `riwayat_status`.
4. Status dokumen ditentukan oleh proses upload, verifikasi, atau re-upload.
5. Status slot `terisi` hanya boleh diberikan oleh sistem setelah booking berhasil.
6. Status booking `aktif` dibuat oleh sistem saat booking berhasil.
7. Status konfirmasi konsultasi ditentukan oleh Admin dan server sesuai proses bisnis.
8. Status reschedule ditentukan oleh server berdasarkan keputusan Admin.
9. Status booking `dibatalkan` digunakan pada alur reschedule yang disetujui dan tidak boleh dipakai bebas di luar alur yang sah.
10. Semua perubahan status multi-tabel wajib menggunakan transaction.

---

# CSRF and Form Security

Aturan:

1. Semua form yang mengubah data wajib menggunakan CSRF token.
2. Method spoofing Laravel boleh digunakan untuk `PUT`, `PATCH`, dan `DELETE`.
3. Jangan membuat form aksi penting tanpa validasi server-side.
4. Jangan mengandalkan input hidden untuk role, status, atau ownership.
5. Semua error validasi harus ditampilkan secara aman.

---

# Session and Remember Me Security

Aturan:

1. Session login menggunakan mekanisme Laravel.
2. Jika menggunakan session berbasis database, tabel `sessions` harus direview dan disetujui karena bukan bagian dari 11 tabel domain skripsi.
3. Jika fitur remember me membutuhkan `remember_token`, penambahan kolom harus disetujui terlebih dahulu.
4. Forgot password diperbolehkan; gunakan `password_reset_tokens` sesuai mekanisme Laravel Breeze.

---

# Report Security

Aturan:

1. Laporan hanya boleh diakses Admin.
2. Laporan dibuat dari query, bukan tabel `laporan`.
3. Filter laporan wajib divalidasi.
4. Jangan memasukkan keyword search langsung ke raw SQL.
5. Laporan tidak boleh menampilkan file path dokumen mentah.
6. Print browser hanya mencetak data yang memang boleh dilihat Admin.
7. Jangan membuat export tambahan seperti PDF atau Excel tanpa persetujuan.

---

# Error Handling Security

Aturan:

1. Jangan menampilkan stack trace kepada user.
2. Jangan menampilkan detail koneksi database kepada user.
3. Jangan menampilkan path server internal kepada user.
4. Pesan error untuk user harus jelas tetapi tidak membocorkan detail teknis sensitif.
5. Log error boleh digunakan untuk debugging developer.
6. Mode debug production harus dimatikan.

Production environment:

```env
APP_DEBUG=false
```

---

# Environment Security

Aturan:

1. File `.env` tidak boleh masuk Git.
2. Jangan hardcode password database di source code.
3. Jangan hardcode credential server di source code.
4. Jangan menaruh secret key di Blade atau JavaScript.
5. Jangan membagikan file `.env`.
6. Gunakan konfigurasi environment sesuai deployment.

---

# Forbidden Security Practices

AI agent tidak boleh:

1. Membuat halaman role tanpa middleware.
2. Mengandalkan menu hidden sebagai satu-satunya proteksi.
3. Mengambil data Klien tanpa ownership check.
4. Membuka dokumen perkara tanpa authorization.
5. Menampilkan raw `file_path`.
6. Menggunakan nama asli file sebagai nama final upload.
7. Menerima `id_user` dari request untuk ownership.
8. Menerima `role` dari form publik.
9. Menerima `status_pengajuan` dari form Klien.
10. Menerima `tanggal_booking` dari form booking.
11. Menggunakan `$request->all()` untuk data sensitif.
12. Membuat fitur email lain seperti notifikasi perkara, reminder jadwal, atau broadcast status tanpa persetujuan.
13. Membuat export PDF/Excel tanpa persetujuan.
14. Membuat tabel atau kolom baru tanpa persetujuan.
15. Menjalankan migration sebelum direview.

---

# Final Notes for AI Agent

AI agent wajib mengikuti aturan berikut:

1. Lindungi semua route berdasarkan authentication dan role.
2. Lindungi semua data Klien berdasarkan ownership.
3. Lindungi semua dokumen perkara berdasarkan authorization.
4. Validasi semua input server-side.
5. Jangan mempercayai role, status, user ID, atau file path dari request.
6. Gunakan transaction untuk proses multi-tabel.
7. Jangan membuat fitur keamanan atau tabel tambahan tanpa persetujuan.
8. Selalu cocokkan implementasi dengan `DATABASE_PLAN.md`, `STATUS_RULES.md`, dan `VALIDATION_RULES.md`.
