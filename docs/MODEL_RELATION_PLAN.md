# MODEL_RELATION_PLAN.md

## Purpose

Dokumen ini berisi rancangan model Eloquent dan relasi antar model untuk project:

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm.

Dokumen ini wajib diikuti saat membuat model, relationship, route model binding, query Eloquent, eager loading, dan controller logic.

AI agent tidak boleh membuat model, relasi, foreign key, atau field baru di luar rancangan tanpa persetujuan pemilik project.

---

## Source of Truth

Dokumen ini harus konsisten dengan:

1. `AGENTS.md`
2. `docs/PROJECT_CONTEXT.md`
3. `docs/DATABASE_PLAN.md`
4. `docs/STATUS_RULES.md`
5. `docs/VALIDATION_RULES.md`
6. `docs/SECURITY_RULES.md`
7. `docs/FEATURE_LIST.md`
8. `docs/ROUTES_PLAN.md`

Jika ada konflik struktur tabel, primary key, foreign key, atau nama kolom, ikuti:

```text
docs/DATABASE_PLAN.md
```

Jika ada konflik status dan role, ikuti:

```text
docs/STATUS_RULES.md
```

---

## General Model Rules

Aturan umum model:

1. Setiap model wajib menggunakan nama tabel eksplisit melalui `$table`.
2. Setiap model wajib menggunakan primary key eksplisit melalui `$primaryKey`.
3. Jangan mengandalkan primary key default Laravel `id`.
4. Jangan mengubah foreign key menjadi konvensi Laravel default seperti `user_id`.
5. Gunakan foreign key sesuai database plan, misalnya `id_user`, `id_pendaftaran`, `id_kategori`, dan seterusnya.
6. Setiap model wajib memiliki `$fillable`.
7. Jangan menggunakan `$guarded = []` tanpa alasan dan persetujuan.
8. Relasi harus memakai foreign key dan owner key eksplisit.
9. Model `User` tetap harus extend `Authenticatable`.
10. Status dan role tetap disimpan sebagai `VARCHAR`, bukan database `ENUM`.
11. Label status untuk UI dibuat di layer aplikasi, bukan disimpan di database.
12. Relationship tidak menggantikan authorization check.
13. Route model binding dengan custom primary key harus ditangani secara eksplisit.

---

## Model List

Model yang digunakan:

1. `User`
2. `ProfilKlien`
3. `KategoriPerkara`
4. `PraPendaftaranPerkara`
5. `DokumenPerkara`
6. `VerifikasiBerkas`
7. `CatatanVerifikasi`
8. `RiwayatStatus`
9. `JadwalKonsultasi`
10. `BookingKonsultasi`

---

# 1. User Model

Path:

```text
app/Models/User.php
```

Model `User` digunakan untuk seluruh aktor:

1. Klien
2. Admin
3. Staf Legal

Tabel:

```text
users
```

Primary key:

```text
id_user
```

Model rule:

```php
protected $table = 'users';

protected $primaryKey = 'id_user';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'nama',
    'email',
    'password',
    'role',
    'no_telepon',
    'status_akun',
];
```

Hidden:

```php
protected $hidden = [
    'password',
];
```

Casts:

```php
protected $casts = [
    'password' => 'hashed',
];
```

Catatan:

1. Gunakan `nama`, bukan `name`.
2. Gunakan `id_user`, bukan `id`.
3. `role` hanya boleh berisi `klien`, `admin`, atau `staf_legal`.
4. `status_akun` hanya boleh berisi `aktif` atau `nonaktif`.
5. Jika Breeze masih memakai field `name`, sesuaikan menjadi `nama`.
6. Jika fitur remember me membutuhkan `remember_token`, penambahan kolom harus direview dan disetujui terlebih dahulu.

Relasi:

```php
public function profilKlien()
{
    return $this->hasOne(ProfilKlien::class, 'id_user', 'id_user');
}

public function praPendaftaranPerkara()
{
    return $this->hasMany(PraPendaftaranPerkara::class, 'id_user', 'id_user');
}

public function verifikasiBerkas()
{
    return $this->hasMany(VerifikasiBerkas::class, 'id_user', 'id_user');
}

public function jadwalKonsultasi()
{
    return $this->hasMany(JadwalKonsultasi::class, 'id_user', 'id_user');
}

public function bookingKonsultasi()
{
    return $this->hasMany(BookingKonsultasi::class, 'id_user', 'id_user');
}

public function riwayatStatus()
{
    return $this->hasMany(RiwayatStatus::class, 'id_user', 'id_user');
}
```

Helper role opsional:

```php
public function isKlien(): bool
{
    return $this->role === 'klien';
}

public function isAdmin(): bool
{
    return $this->role === 'admin';
}

public function isStafLegal(): bool
{
    return $this->role === 'staf_legal';
}
```

---

# 2. ProfilKlien Model

Path:

```text
app/Models/ProfilKlien.php
```

Tabel:

```text
profil_klien
```

Primary key:

```text
id_profil
```

Model rule:

```php
protected $table = 'profil_klien';

protected $primaryKey = 'id_profil';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'id_user',
    'alamat',
    'jenis_kelamin',
    'pekerjaan',
    'no_identitas',
];
```

Relasi:

```php
public function user()
{
    return $this->belongsTo(User::class, 'id_user', 'id_user');
}
```

Aturan:

1. Satu Klien hanya memiliki satu profil.
2. `id_user` pada `profil_klien` harus unique.
3. `id_user` tidak boleh berasal dari request publik.
4. Saat update profil, gunakan `auth()->id()`.

---

# 3. KategoriPerkara Model

Path:

```text
app/Models/KategoriPerkara.php
```

Tabel:

```text
kategori_perkara
```

Primary key:

```text
id_kategori
```

Model rule:

```php
protected $table = 'kategori_perkara';

protected $primaryKey = 'id_kategori';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'nama_kategori',
    'deskripsi',
];
```

Relasi:

```php
public function praPendaftaranPerkara()
{
    return $this->hasMany(PraPendaftaranPerkara::class, 'id_kategori', 'id_kategori');
}
```

Aturan:

1. Jangan menambahkan `status_kategori`.
2. Jangan menambahkan `is_active`.
3. Jangan menambahkan soft delete.
4. Kategori hanya boleh dihapus jika belum digunakan pada pengajuan.

---

# 4. PraPendaftaranPerkara Model

Path:

```text
app/Models/PraPendaftaranPerkara.php
```

Tabel:

```text
pra_pendaftaran_perkara
```

Primary key:

```text
id_pendaftaran
```

Model rule:

```php
protected $table = 'pra_pendaftaran_perkara';

protected $primaryKey = 'id_pendaftaran';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'id_user',
    'id_kategori',
    'judul_perkara',
    'kronologi',
    'status_pengajuan',
    'tanggal_pengajuan',
];
```

Casts:

```php
protected $casts = [
    'tanggal_pengajuan' => 'datetime',
];
```

Relasi:

```php
public function klien()
{
    return $this->belongsTo(User::class, 'id_user', 'id_user');
}

public function kategori()
{
    return $this->belongsTo(KategoriPerkara::class, 'id_kategori', 'id_kategori');
}

public function dokumenPerkara()
{
    return $this->hasMany(DokumenPerkara::class, 'id_pendaftaran', 'id_pendaftaran');
}

public function verifikasiBerkas()
{
    return $this->hasMany(VerifikasiBerkas::class, 'id_pendaftaran', 'id_pendaftaran');
}

public function riwayatStatus()
{
    return $this->hasMany(RiwayatStatus::class, 'id_pendaftaran', 'id_pendaftaran');
}

public function bookingKonsultasi()
{
    return $this->hasMany(BookingKonsultasi::class, 'id_pendaftaran', 'id_pendaftaran');
}

public function bookingAktif()
{
    return $this->hasOne(BookingKonsultasi::class, 'id_pendaftaran', 'id_pendaftaran')
        ->where('status_booking', 'aktif');
}
```

Relasi opsional untuk verifikasi terakhir:

```php
public function verifikasiTerakhir()
{
    return $this->hasOne(VerifikasiBerkas::class, 'id_pendaftaran', 'id_pendaftaran')
        ->latestOfMany('tanggal_verifikasi');
}
```

Aturan:

1. `id_user` adalah Klien pemilik pengajuan.
2. Status awal pengajuan adalah `menunggu_verifikasi`.
3. Setiap perubahan `status_pengajuan` wajib dicatat pada `riwayat_status`.
4. Klien hanya boleh mengakses pengajuan miliknya sendiri.
5. Klien tidak boleh mengubah isi pengajuan setelah dikirim.
6. Booking hanya boleh dilakukan jika `status_pengajuan = berkas_lengkap`.

---

# 5. DokumenPerkara Model

Path:

```text
app/Models/DokumenPerkara.php
```

Tabel:

```text
dokumen_perkara
```

Primary key:

```text
id_dokumen
```

Model rule:

```php
protected $table = 'dokumen_perkara';

protected $primaryKey = 'id_dokumen';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'id_pendaftaran',
    'nama_dokumen',
    'jenis_dokumen',
    'file_path',
    'status_dokumen',
];
```

Relasi:

```php
public function praPendaftaranPerkara()
{
    return $this->belongsTo(PraPendaftaranPerkara::class, 'id_pendaftaran', 'id_pendaftaran');
}

public function catatanVerifikasi()
{
    return $this->hasMany(CatatanVerifikasi::class, 'id_dokumen', 'id_dokumen');
}
```

Aturan:

1. File dokumen disimpan di Laravel Storage.
2. Database hanya menyimpan metadata dan `file_path`.
3. Jangan menyimpan file langsung di database.
4. Jangan menerima `file_path` dari request.
5. Jangan menimpa file lama saat re-upload.
6. Jangan menambahkan `uploaded_at`; gunakan timestamps.
7. Dokumen wajib dilindungi authorization check.

---

# 6. VerifikasiBerkas Model

Path:

```text
app/Models/VerifikasiBerkas.php
```

Tabel:

```text
verifikasi_berkas
```

Primary key:

```text
id_verifikasi
```

Model rule:

```php
protected $table = 'verifikasi_berkas';

protected $primaryKey = 'id_verifikasi';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'id_pendaftaran',
    'id_user',
    'status_verifikasi',
    'tanggal_verifikasi',
    'catatan_umum',
];
```

Casts:

```php
protected $casts = [
    'tanggal_verifikasi' => 'datetime',
];
```

Relasi:

```php
public function praPendaftaranPerkara()
{
    return $this->belongsTo(PraPendaftaranPerkara::class, 'id_pendaftaran', 'id_pendaftaran');
}

public function stafLegal()
{
    return $this->belongsTo(User::class, 'id_user', 'id_user');
}

public function catatanVerifikasi()
{
    return $this->hasMany(CatatanVerifikasi::class, 'id_verifikasi', 'id_verifikasi');
}
```

Aturan:

1. `id_user` adalah Staf Legal yang melakukan verifikasi.
2. `catatan_umum` digunakan untuk catatan umum utama.
3. Catatan per dokumen disimpan pada `catatan_verifikasi`.
4. Status verifikasi hanya `berkas_lengkap` atau `berkas_tidak_lengkap`.
5. Proses verifikasi wajib menggunakan transaction.

---

# 7. CatatanVerifikasi Model

Path:

```text
app/Models/CatatanVerifikasi.php
```

Tabel:

```text
catatan_verifikasi
```

Primary key:

```text
id_catatan
```

Model rule:

```php
protected $table = 'catatan_verifikasi';

protected $primaryKey = 'id_catatan';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'id_verifikasi',
    'id_dokumen',
    'isi_catatan',
    'status_perbaikan',
];
```

Relasi:

```php
public function verifikasiBerkas()
{
    return $this->belongsTo(VerifikasiBerkas::class, 'id_verifikasi', 'id_verifikasi');
}

public function dokumenPerkara()
{
    return $this->belongsTo(DokumenPerkara::class, 'id_dokumen', 'id_dokumen');
}
```

Aturan:

1. `id_dokumen` nullable untuk catatan tambahan umum jika diperlukan.
2. Catatan umum utama tetap disimpan di `verifikasi_berkas.catatan_umum`.
3. Jangan menggandakan catatan umum yang sama ke `catatan_verifikasi`.
4. `status_perbaikan` hanya `belum_diperbaiki` atau `sudah_diperbaiki`.
5. Saat Klien re-upload dokumen, catatan terkait berubah menjadi `sudah_diperbaiki`.

---

# 8. RiwayatStatus Model

Path:

```text
app/Models/RiwayatStatus.php
```

Tabel:

```text
riwayat_status
```

Primary key:

```text
id_riwayat
```

Model rule:

```php
protected $table = 'riwayat_status';

protected $primaryKey = 'id_riwayat';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'id_pendaftaran',
    'id_user',
    'status',
    'keterangan',
];
```

Relasi:

```php
public function praPendaftaranPerkara()
{
    return $this->belongsTo(PraPendaftaranPerkara::class, 'id_pendaftaran', 'id_pendaftaran');
}

public function user()
{
    return $this->belongsTo(User::class, 'id_user', 'id_user');
}
```

Aturan:

1. Setiap perubahan `status_pengajuan` wajib membuat record `riwayat_status`.
2. Jangan menambahkan `tanggal_status`.
3. Waktu perubahan status memakai `created_at`.
4. `id_user` adalah user yang melakukan atau memicu perubahan status.
5. Status disimpan sebagai slug lowercase sesuai `STATUS_RULES.md`.

---

# 9. JadwalKonsultasi Model

Path:

```text
app/Models/JadwalKonsultasi.php
```

Tabel:

```text
jadwal_konsultasi
```

Primary key:

```text
id_jadwal
```

Model rule:

```php
protected $table = 'jadwal_konsultasi';

protected $primaryKey = 'id_jadwal';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'id_user',
    'tanggal',
    'waktu_mulai',
    'waktu_selesai',
    'status_slot',
];
```

Casts:

```php
protected $casts = [
    'tanggal' => 'date',
];
```

Relasi:

```php
public function admin()
{
    return $this->belongsTo(User::class, 'id_user', 'id_user');
}

public function bookingKonsultasi()
{
    return $this->hasMany(BookingKonsultasi::class, 'id_jadwal', 'id_jadwal');
}

public function bookingAktif()
{
    return $this->hasOne(BookingKonsultasi::class, 'id_jadwal', 'id_jadwal')
        ->where('status_booking', 'aktif');
}
```

Aturan:

1. `id_user` adalah Admin pembuat jadwal.
2. Status awal jadwal baru adalah `tersedia`.
3. Status `terisi` hanya diberikan oleh sistem setelah booking berhasil.
4. Status slot hanya `tersedia`, `terisi`, atau `tidak_aktif`.
5. Jadwal yang memiliki booking aktif tidak boleh diubah sembarangan.

---

# 10. BookingKonsultasi Model

Path:

```text
app/Models/BookingKonsultasi.php
```

Tabel:

```text
booking_konsultasi
```

Primary key:

```text
id_booking
```

Model rule:

```php
protected $table = 'booking_konsultasi';

protected $primaryKey = 'id_booking';

public $incrementing = true;

protected $keyType = 'int';
```

Fillable:

```php
protected $fillable = [
    'id_pendaftaran',
    'id_jadwal',
    'id_user',
    'status_booking',
    'tanggal_booking',
];
```

Casts:

```php
protected $casts = [
    'tanggal_booking' => 'datetime',
];
```

Relasi:

```php
public function praPendaftaranPerkara()
{
    return $this->belongsTo(PraPendaftaranPerkara::class, 'id_pendaftaran', 'id_pendaftaran');
}

public function jadwalKonsultasi()
{
    return $this->belongsTo(JadwalKonsultasi::class, 'id_jadwal', 'id_jadwal');
}

public function klien()
{
    return $this->belongsTo(User::class, 'id_user', 'id_user');
}
```

Aturan:

1. `id_user` adalah Klien yang melakukan booking.
2. Booking hanya boleh dibuat jika pengajuan berstatus `berkas_lengkap`.
3. Booking baru berstatus `aktif`.
4. `tanggal_booking` diisi oleh server.
5. Satu pengajuan hanya boleh memiliki satu booking aktif.
6. Satu jadwal hanya boleh memiliki satu booking aktif.
7. Setelah booking berhasil, status slot menjadi `terisi`.
8. Setelah booking berhasil, status pengajuan menjadi `jadwal_dipilih`.
9. Setelah konsultasi selesai, status booking menjadi `selesai`.
10. Setelah konsultasi selesai, status pengajuan menjadi `selesai`.
11. Semua proses booking dan penyelesaian konsultasi wajib menggunakan transaction.

---

# Route Model Binding Rules

Karena project menggunakan custom primary key, route model binding harus ditangani secara eksplisit.

Jika menggunakan implicit route model binding, setiap model dapat mendefinisikan `getRouteKeyName()`.

Daftar route parameter dan primary key:

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

Contoh:

```php
public function getRouteKeyName()
{
    return 'id_pendaftaran';
}
```

Alternatif aman:

Gunakan query eksplisit di controller.

```php
$pengajuan = PraPendaftaranPerkara::where('id_pendaftaran', $id)
    ->firstOrFail();
```

Untuk route Klien, query wajib disertai ownership check.

```php
$pengajuan = PraPendaftaranPerkara::where('id_pendaftaran', $id)
    ->where('id_user', auth()->id())
    ->firstOrFail();
```

---

# Eager Loading Recommendations

Gunakan eager loading untuk menghindari query berulang.

Contoh detail pengajuan Klien:

```php
$pengajuan = PraPendaftaranPerkara::with([
        'kategori',
        'dokumenPerkara',
        'riwayatStatus.user',
        'verifikasiBerkas.catatanVerifikasi.dokumenPerkara',
        'bookingKonsultasi.jadwalKonsultasi',
    ])
    ->where('id_pendaftaran', $id)
    ->where('id_user', auth()->id())
    ->firstOrFail();
```

Contoh detail pengajuan Staf Legal:

```php
$pengajuan = PraPendaftaranPerkara::with([
        'klien',
        'kategori',
        'dokumenPerkara',
        'verifikasiBerkas.catatanVerifikasi',
        'riwayatStatus.user',
    ])
    ->where('id_pendaftaran', $id)
    ->firstOrFail();
```

Contoh laporan Admin:

```php
$query = PraPendaftaranPerkara::with([
    'klien',
    'kategori',
    'bookingKonsultasi.jadwalKonsultasi',
]);
```

---

# Ownership and Authorization Notes

Relationship tidak otomatis menjamin keamanan.

Aturan:

1. Klien hanya boleh mengakses data miliknya sendiri.
2. Query Klien wajib difilter dengan `id_user = auth()->id()`.
3. Dokumen perkara wajib dicek ownership/authorization sebelum dibuka.
4. Staf Legal hanya menggunakan data untuk kebutuhan verifikasi.
5. Admin hanya menggunakan data untuk kebutuhan administratif sesuai scope.
6. Jangan menggunakan `findOrFail($id)` saja pada route Klien.
7. Jangan menerima `id_user` dari request untuk menentukan pemilik data.

---

# Transaction Usage Notes

Gunakan database transaction untuk proses multi-tabel berikut:

1. Membuat pra-pendaftaran + dokumen + riwayat status.
2. Verifikasi berkas + catatan + update status pengajuan + update status dokumen + riwayat status.
3. Unggah ulang dokumen + update dokumen lama + dokumen baru + update catatan + update status pengajuan + riwayat status.
4. Booking konsultasi + update slot jadwal + update status pengajuan + riwayat status.
5. Penyelesaian konsultasi + update booking + update status pengajuan + riwayat status.

Contoh pola:

```php
DB::transaction(function () {
    // proses multi-tabel
});
```

---

# Forbidden Model Practices

AI agent tidak boleh:

1. Menggunakan default primary key `id` untuk tabel domain.
2. Mengubah `id_user` menjadi `user_id`.
3. Mengubah `id_pendaftaran` menjadi `pra_pendaftaran_perkara_id`.
4. Mengubah `id_kategori` menjadi `kategori_perkara_id`.
5. Membuat tabel atau kolom baru tanpa persetujuan.
6. Menambahkan `status_kategori`.
7. Menambahkan `is_active`.
8. Menambahkan `uploaded_at`.
9. Menambahkan `tanggal_status`.
10. Menggunakan database `ENUM`.
11. Menggunakan `$request->all()` untuk mass assignment data sensitif.
12. Menggunakan `$guarded = []` tanpa review.
13. Menghapus data hukum penting secara otomatis.
14. Mengandalkan relationship tanpa authorization check.
15. Membuat relasi berdasarkan tebakan di luar database plan.

---

# Final Notes for AI Agent

AI agent wajib mengikuti aturan berikut:

1. Baca `DATABASE_PLAN.md` sebelum membuat model.
2. Gunakan custom primary key di semua model domain.
3. Gunakan foreign key eksplisit pada semua relationship.
4. Jangan mengandalkan konvensi default Laravel untuk foreign key.
5. Jangan membuat field, relasi, model, atau tabel tambahan tanpa persetujuan.
6. Pastikan model sesuai dengan `STATUS_RULES.md`, `VALIDATION_RULES.md`, dan `SECURITY_RULES.md`.
7. Pastikan proses multi-tabel memakai transaction.
8. Pastikan route Klien tetap memakai ownership check.
9. Pastikan akses dokumen tetap memakai authorization check.
