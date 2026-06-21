# PATCH FINAL — UI_PRD.md

## Final UI Branding, Landing Page, and Prototype Direction

## 1. Final UI Decision Summary

Berdasarkan keputusan terbaru, UI sistem menggunakan ketentuan final berikut:

| Item                        | Keputusan                                 |
| --------------------------- | ----------------------------------------- |
| Logo resmi TNY Law Firm     | Sudah ada                                 |
| Warna brand resmi           | Belum ditentukan                          |
| Style UI                    | Modern Corporate                          |
| Landing page publik         | Wajib dibuat                              |
| Output tahap awal           | Desain statis di MCP Paper                |
| Implementasi Blade/Tailwind | Dilakukan setelah desain statis disetujui |

Keputusan ini menjadi acuan final untuk pembuatan desain UI dan prototype sistem.

---

# 2. Branding Requirements

## 2.1 Logo

TNY Law Firm sudah memiliki logo resmi.

Logo resmi wajib digunakan pada:

1. Landing page.
2. Login page.
3. Register Klien page.
4. Sidebar aplikasi.
5. Header aplikasi.
6. Print layout laporan jika diperlukan.

Jika file logo belum tersedia saat proses desain dimulai, gunakan placeholder sementara berupa teks:

```text
TNY Law Firm
```

Setelah file logo tersedia, placeholder wajib diganti dengan logo resmi.

## 2.2 Brand Color

Warna brand resmi TNY Law Firm belum ditentukan.

Untuk tahap desain awal, gunakan color palette sementara yang memiliki karakter modern corporate dan sesuai dengan konteks firma hukum.

Color palette sementara:

| Token          | Nama Warna     | Hex       | Penggunaan                                       |
| -------------- | -------------- | --------- | ------------------------------------------------ |
| Primary        | Corporate Navy | `#1E3A5F` | Sidebar, primary button, active menu, link utama |
| Secondary      | Slate Gray     | `#64748B` | Teks sekunder, border, icon muted                |
| Accent         | Modern Blue    | `#2563EB` | CTA ringan, highlight, focus ring                |
| Success        | Green          | `#16A34A` | Berkas lengkap, dokumen valid, success alert     |
| Warning        | Amber          | `#F59E0B` | Menunggu verifikasi, peringatan                  |
| Error          | Red            | `#DC2626` | Error, berkas tidak lengkap, perlu perbaikan     |
| Background     | Soft Gray      | `#F8FAFC` | Background halaman                               |
| Surface        | White          | `#FFFFFF` | Card, table, modal                               |
| Text Primary   | Dark Slate     | `#0F172A` | Teks utama                                       |
| Text Secondary | Slate          | `#475569` | Teks pendukung                                   |

Aturan penggunaan warna:

1. Warna ini bersifat sementara sampai warna brand resmi ditentukan.
2. Jangan menggunakan warna acak pada setiap halaman.
3. Semua status badge harus mengikuti warna status yang konsisten.
4. Jika warna brand resmi tersedia di kemudian hari, hanya design token yang boleh disesuaikan tanpa mengubah struktur UI.
5. UI harus tetap memiliki kontras yang baik dan mudah dibaca.

---

# 3. Visual Style Direction

## 3.1 UI Style

Gaya UI final yang digunakan adalah:

```text
Modern Corporate
```

## 3.2 Karakter Visual

UI harus memiliki karakter:

1. Profesional.
2. Modern.
3. Bersih.
4. Formal tetapi tidak kaku.
5. Cocok untuk firma hukum.
6. Mudah dibaca.
7. Fokus pada data, status, dokumen, dan alur kerja.
8. Tidak terlalu dekoratif.
9. Tidak terlalu banyak animasi.
10. Tidak menggunakan gaya playful, casual, marketplace, atau social app.

## 3.3 Design Mood

Desain harus memberi kesan:

1. Terpercaya.
2. Rapi.
3. Aman.
4. Terstruktur.
5. Profesional.
6. Sesuai kebutuhan layanan hukum.
7. Sesuai kebutuhan akademik skripsi.

---

# 4. Landing Page Requirement

## 4.1 Status Landing Page

Landing page publik wajib dibuat.

Landing page berada pada route:

```text
/
```

Landing page menjadi halaman awal sistem sebelum user login.

## 4.2 Tujuan Landing Page

Landing page bertujuan untuk:

1. Memberikan kesan profesional terhadap TNY Law Firm.
2. Menjelaskan fungsi sistem pra-pendaftaran perkara.
3. Mengarahkan Klien baru untuk melakukan registrasi.
4. Mengarahkan user yang sudah memiliki akun untuk login.
5. Menjelaskan alur singkat layanan secara visual.
6. Menjadi halaman publik yang mendukung kebutuhan presentasi sistem dalam skripsi.

## 4.3 Struktur Landing Page

Landing page minimal memiliki bagian berikut:

1. Public header.
2. Logo TNY Law Firm.
3. Navigation menu.
4. Hero section.
5. Section alur layanan.
6. Section manfaat sistem.
7. Section role pengguna.
8. Call to action.
9. Footer.

## 4.4 Public Header

Header publik harus menampilkan:

1. Logo TNY Law Firm.
2. Nama sistem atau nama firma.
3. Menu navigasi:

   * Beranda
   * Alur Layanan
   * Tentang Sistem
   * Login
   * Daftar
4. Button utama untuk daftar.
5. Button atau link untuk login.

## 4.5 Hero Section

Hero section harus menampilkan judul utama:

```text
Pra-Pendaftaran Perkara Lebih Mudah dan Terstruktur
```

Deskripsi hero:

```text
Sistem ini membantu Klien melakukan pra-pendaftaran perkara, mengunggah dokumen pendukung, memantau status verifikasi, dan memilih jadwal konsultasi secara online.
```

CTA utama:

```text
Daftar sebagai Klien
```

CTA sekunder:

```text
Masuk ke Sistem
```

## 4.6 Alur Layanan pada Landing Page

Landing page harus menampilkan alur layanan singkat:

1. Registrasi akun.
2. Lengkapi profil.
3. Ajukan pra-pendaftaran perkara.
4. Unggah dokumen pendukung.
5. Tunggu verifikasi Staf Legal.
6. Unggah ulang dokumen jika diperlukan.
7. Pilih jadwal konsultasi jika berkas lengkap.
8. Konsultasi selesai.

## 4.7 Manfaat Sistem

Section manfaat sistem dapat menampilkan poin berikut:

1. Pengajuan perkara lebih terstruktur.
2. Dokumen pendukung tersimpan lebih rapi.
3. Klien dapat memantau status pengajuan.
4. Staf Legal dapat memverifikasi berkas secara sistematis.
5. Admin dapat memantau data pra-pendaftaran.
6. Jadwal konsultasi dapat dipilih setelah berkas lengkap.

## 4.8 Role Pengguna pada Landing Page

Tampilkan ringkasan role:

1. Klien: mengajukan pra-pendaftaran dan memantau status.
2. Staf Legal: memverifikasi dokumen dan memberikan catatan.
3. Admin: mengelola data pengguna, kategori, jadwal, dan laporan.

## 4.9 Batasan Landing Page

Landing page tidak boleh menampilkan atau menjanjikan fitur berikut:

1. e-Court.
2. Payment.
3. Digital signature.
4. Video conference.
5. Chat online.
6. Email notification.
7. Export PDF.
8. Export Excel.
9. Manajemen perkara setelah konsultasi.
10. Fitur lain di luar scope skripsi.

---

# 5. Prototype Output Decision

## 5.1 Output Tahap Awal

Tahap awal UI dibuat sebagai:

```text
Desain statis di MCP Paper
```

Bukan langsung implementasi Blade/Tailwind.

## 5.2 Tujuan Prototype Statis

Prototype statis digunakan untuk:

1. Menyusun struktur visual sistem.
2. Memvalidasi layout setiap role.
3. Memastikan navigasi sesuai rancangan.
4. Memastikan seluruh halaman wajib tersedia.
5. Menjadi acuan sebelum implementasi Laravel Blade.
6. Mempermudah penyusunan dokumentasi skripsi.
7. Mempermudah presentasi desain antarmuka.
8. Menghindari perubahan besar saat implementasi kode.

## 5.3 Batasan Prototype Statis

Pada tahap desain statis:

1. Tidak perlu membuat logic backend.
2. Tidak perlu membuat database.
3. Tidak perlu membuat controller.
4. Tidak perlu membuat migration.
5. Tidak perlu membuat Blade final.
6. Tidak perlu membuat route Laravel.
7. Tidak perlu membuat validasi server-side.
8. Tidak perlu membuat interaksi kompleks.
9. Fokus pada layout, komponen, navigasi, state, dan alur visual.

## 5.4 Tahap Setelah Prototype Disetujui

Setelah desain statis di MCP Paper disetujui, implementasi dapat dilanjutkan ke:

```text
Laravel Blade + Tailwind CSS
```

Implementasi wajib mengikuti:

1. `AGENTS.md`
2. `docs/UI_PRD.md`
3. `docs/PROJECT_CONTEXT.md`
4. `docs/FEATURE_LIST.md`
5. `docs/ROUTES_PLAN.md`
6. `docs/STATUS_RULES.md`
7. `docs/VALIDATION_RULES.md`
8. `docs/SECURITY_RULES.md`
9. `docs/MANUAL_TESTING_PLAN.md`

---

# 6. MCP Paper Output Requirements

## 6.1 Screen yang Wajib Dibuat di MCP Paper

MCP Paper harus menghasilkan desain statis untuk halaman berikut:

### Public and Authentication

1. Landing page.
2. Login page.
3. Register Klien page.

### Shared Layout

1. Guest layout.
2. Authenticated app layout.
3. Sidebar role-based.
4. Header authenticated.
5. Footer.
6. Flash message.
7. Empty state.
8. Error state.
9. Loading state.

### Klien

1. Dashboard Klien.
2. Profil Klien.
3. Edit Profil Klien.
4. Daftar pengajuan Klien.
5. Buat pengajuan pra-pendaftaran.
6. Detail pengajuan Klien.
7. Status dan catatan pengajuan.
8. Timeline riwayat status.
9. Form unggah ulang dokumen.
10. Pilih jadwal konsultasi.
11. Konfirmasi booking.
12. Detail booking konsultasi.

### Staf Legal

1. Dashboard Staf Legal.
2. Daftar pengajuan untuk verifikasi.
3. Detail pengajuan.
4. Form verifikasi berkas.

### Admin

1. Dashboard Admin.
2. Daftar pengguna.
3. Tambah user.
4. Detail user.
5. Edit user.
6. Daftar kategori perkara.
7. Tambah kategori.
8. Edit kategori.
9. Daftar data pra-pendaftaran.
10. Detail data pra-pendaftaran.
11. Daftar jadwal konsultasi.
12. Tambah jadwal konsultasi.
13. Edit jadwal konsultasi.
14. Penyelesaian konsultasi.
15. Laporan pra-pendaftaran.
16. Print browser layout.

### Document Access

1. View dokumen.
2. Download dokumen state atau action representation.

## 6.2 Komponen UI yang Wajib Ditampilkan

Prototype harus menampilkan komponen berikut:

1. Button.
2. Input.
3. Select.
4. Textarea.
5. File upload.
6. Table.
7. Card.
8. Badge status.
9. Alert.
10. Modal konfirmasi.
11. Timeline.
12. Pagination.
13. Filter form.
14. Empty state.
15. Loading state.
16. Error state.
17. Success message.
18. Sidebar.
19. Header.
20. Footer.

---

# 7. Updated Page Priority

## Priority 1 — Public and Authentication UI

1. Landing page.
2. Login page.
3. Register Klien page.
4. Guest layout.
5. Basic branding with official logo or temporary placeholder.

## Priority 2 — App Shell

1. Authenticated layout.
2. Sidebar role-based.
3. Header.
4. Footer.
5. Flash message.
6. Status badge component.
7. Empty state component.
8. Modal confirmation component.

## Priority 3 — Klien Core Flow

1. Dashboard Klien.
2. Profil Klien.
3. Daftar pengajuan.
4. Buat pengajuan.
5. Detail pengajuan.
6. Status dan catatan.
7. Unggah ulang dokumen.
8. Pilih jadwal konsultasi.
9. Konfirmasi booking.
10. Detail booking.

## Priority 4 — Staf Legal Core Flow

1. Dashboard Staf Legal.
2. Daftar pengajuan.
3. Detail pengajuan.
4. Form verifikasi berkas.

## Priority 5 — Admin Core Flow

1. Dashboard Admin.
2. Kelola pengguna.
3. Kelola kategori perkara.
4. Kelola data pra-pendaftaran.
5. Kelola jadwal konsultasi.
6. Penyelesaian konsultasi.
7. Laporan pra-pendaftaran.
8. Print browser layout.

---

# 8. UI State Requirements

Setiap halaman utama harus memiliki state berikut:

## 8.1 Default State

Tampilan normal ketika data tersedia.

## 8.2 Empty State

Tampilan ketika belum ada data.

Contoh:

```text
Belum ada pengajuan pra-pendaftaran.
```

## 8.3 Loading State

Tampilan saat data sedang dimuat atau form sedang diproses.

Contoh:

```text
Memuat data...
```

atau button disabled:

```text
Memproses...
```

## 8.4 Error State

Tampilan ketika terjadi error atau validasi gagal.

Contoh:

```text
Data gagal dimuat.
```

```text
Field ini wajib diisi.
```

## 8.5 Success State

Tampilan ketika aksi berhasil.

Contoh:

```text
Data berhasil disimpan.
```

```text
Pengajuan berhasil dikirim.
```

---

# 9. Status Badge Rules for UI

Status tidak boleh ditulis bebas. UI harus menggunakan mapping status berikut.

## 9.1 Status Pengajuan

| Slug                        | Label UI                  | Badge   |
| --------------------------- | ------------------------- | ------- |
| `menunggu_verifikasi`       | Menunggu Verifikasi       | Warning |
| `berkas_tidak_lengkap`      | Berkas Tidak Lengkap      | Error   |
| `menunggu_verifikasi_ulang` | Menunggu Verifikasi Ulang | Warning |
| `berkas_lengkap`            | Berkas Lengkap            | Success |
| `jadwal_dipilih`            | Jadwal Dipilih            | Primary |
| `selesai`                   | Selesai                   | Neutral |

## 9.2 Status Dokumen

| Slug              | Label UI        | Badge   |
| ----------------- | --------------- | ------- |
| `terkirim`        | Terkirim        | Warning |
| `valid`           | Valid           | Success |
| `perlu_perbaikan` | Perlu Perbaikan | Error   |
| `diganti`         | Diganti         | Neutral |

## 9.3 Status Slot Jadwal

| Slug          | Label UI    | Badge   |
| ------------- | ----------- | ------- |
| `tersedia`    | Tersedia    | Success |
| `terisi`      | Terisi      | Primary |
| `tidak_aktif` | Tidak Aktif | Neutral |

## 9.4 Status Booking

| Slug         | Label UI   | Badge   |
| ------------ | ---------- | ------- |
| `aktif`      | Aktif      | Primary |
| `dibatalkan` | Dibatalkan | Error   |
| `selesai`    | Selesai    | Success |

---

# 10. Responsive Prototype Requirements

## 10.1 Desktop

Desktop layout harus menampilkan:

1. Sidebar penuh.
2. Header penuh.
3. Content area luas.
4. Table dalam format penuh.
5. Dashboard card 3–4 kolom.
6. Form dengan lebar nyaman.

## 10.2 Tablet

Tablet layout harus menampilkan:

1. Sidebar collapsible.
2. Card 2 kolom.
3. Table horizontal scroll jika diperlukan.
4. Form full width dalam content area.

## 10.3 Mobile

Mobile layout harus menampilkan:

1. Sidebar sebagai drawer.
2. Header dengan hamburger menu.
3. Card 1 kolom.
4. Table dapat berubah menjadi card list atau horizontal scroll.
5. Button utama full width.
6. Modal responsive.
7. Form 1 kolom.

---

# 11. Accessibility Requirements for Prototype

Prototype harus memperhatikan:

1. Kontras teks memadai.
2. Label form terlihat jelas.
3. Error message dekat dengan field.
4. Button memiliki teks jelas.
5. Icon tidak berdiri sendiri tanpa label.
6. Focus state harus dirancang.
7. Modal memiliki close action.
8. Warna status tidak menjadi satu-satunya penanda; tetap gunakan label teks.

---

# 12. Final Instruction for Codex and MCP Paper

Codex harus menggunakan MCP Paper terlebih dahulu untuk membuat desain statis.

Instruksi utama:

1. Gunakan logo resmi TNY Law Firm jika file tersedia.
2. Jika logo belum tersedia, gunakan placeholder teks “TNY Law Firm”.
3. Gunakan gaya modern corporate.
4. Gunakan warna sementara corporate navy sampai warna brand resmi ditentukan.
5. Buat landing page publik.
6. Buat desain statis terlebih dahulu, bukan implementasi Blade/Tailwind langsung.
7. Jangan membuat logic backend.
8. Jangan membuat database, migration, controller, atau route pada tahap prototype statis.
9. Jangan membuat fitur di luar scope sistem.
10. Jangan membuat role baru.
11. Jangan membuat status baru.
12. Jangan membuat route baru.
13. Jangan membuat halaman tambahan tanpa persetujuan.
14. Pastikan seluruh screen inventory tercakup dalam prototype.
15. Pastikan desain mendukung alur Klien, Staf Legal, dan Admin.
16. Pastikan desain mendukung kebutuhan blackbox testing skripsi.
17. Setelah desain statis disetujui, baru lanjut ke implementasi Laravel Blade + Tailwind CSS.

---
