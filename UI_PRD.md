# UI/UX Product Requirements Document (PRD)
## Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm

## 1. Document Purpose

Dokumen ini menjadi Product Requirements Document (PRD) utama untuk perancangan UI/UX **Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm**.

PRD ini digunakan sebagai acuan kerja untuk membuat desain antarmuka di dengan format **native editable design**. Seluruh elemen desain harus dapat diedit langsung, termasuk frame, text, card, button, input, table, badge, modal, sidebar, icon, dan komponen UI lainnya.

Dokumen ini digunakan oleh:

1. Product Manager.
2. UX Designer.
3. UI Designer.
4. Frontend Developer.
5. Figma App di ChatGPT.
6. Figma AI Pro.
7. Tim implementasi Laravel.
8. Tim akademik skripsi.

PRD ini berfungsi sebagai **single source of truth** untuk perancangan UI/UX sistem.

---

## 2. Product Overview

### 2.1 Nama Sistem

**Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm**

### 2.2 Latar Belakang

TNY Law Firm membutuhkan sistem berbasis web untuk membantu proses pra-pendaftaran perkara secara lebih terstruktur. Sistem ini memungkinkan Klien melakukan pengajuan awal perkara, mengunggah dokumen pendukung, memantau status verifikasi, memilih jadwal konsultasi, menentukan metode konsultasi, dan mengajukan reschedule apabila berhalangan hadir.

Sistem juga membantu Staf Legal memverifikasi kelengkapan dokumen dan membantu Admin mengelola data pengguna, kategori perkara, jadwal konsultasi, booking konsultasi, permintaan reschedule, serta laporan pra-pendaftaran.

### 2.3 Tujuan Sistem

Tujuan sistem:

1. Memudahkan Klien melakukan pra-pendaftaran perkara secara online.
2. Memudahkan Klien mengunggah dokumen pendukung secara terstruktur.
3. Memudahkan Klien memantau status pengajuan dan catatan verifikasi.
4. Memudahkan Staf Legal memverifikasi kelengkapan berkas.
5. Memudahkan Klien memilih jadwal dan metode konsultasi.
6. Memungkinkan Admin mengonfirmasi detail teknis konsultasi.
7. Memungkinkan Klien mengajukan reschedule tanpa langsung mengubah jadwal lama.
8. Memungkinkan Admin menyetujui atau menolak permintaan reschedule.
9. Memudahkan Admin memantau data pra-pendaftaran dan konsultasi.
10. Menghasilkan rancangan UI/UX yang profesional, formal, dan sesuai kebutuhan firma hukum.

### 2.4 Target Pengguna

Target pengguna sistem:

1. Klien.
2. Staf Legal.
3. Admin.

---

## 3. Product Scope

### 3.1 Fitur Wajib

Fitur wajib yang harus didukung UI:

1. Landing page publik.
2. Login.
3. Register Klien.
4. Dashboard Klien.
5. Profil Klien.
6. Pengajuan pra-pendaftaran perkara.
7. Upload dokumen perkara.
8. Detail pengajuan Klien.
9. Status dan catatan verifikasi.
10. Unggah ulang dokumen.
11. Pilih jadwal konsultasi.
12. Pilih metode konsultasi online/offline.
13. Catatan preferensi konsultasi dari Klien.
14. Detail booking konsultasi Klien.
15. Informasi link konsultasi online atau lokasi konsultasi offline.
16. Ajukan reschedule konsultasi.
17. Detail permintaan reschedule Klien.
18. Dashboard Staf Legal.
19. Daftar pengajuan untuk verifikasi.
20. Detail pengajuan Staf Legal.
21. Form verifikasi berkas.
22. Dashboard Admin.
23. Kelola pengguna.
24. Kelola kategori perkara.
25. Kelola data pra-pendaftaran.
26. Kelola jadwal konsultasi.
27. Daftar booking konsultasi.
28. Detail booking konsultasi Admin.
29. Form konfirmasi detail konsultasi.
30. Daftar permintaan reschedule.
31. Detail permintaan reschedule.
32. Form setujui reschedule.
33. Form tolak reschedule.
34. Tandai konsultasi selesai.
35. Laporan pra-pendaftaran.
36. Print browser laporan.
37. Akses dokumen melalui mekanisme yang aman.
38. Empty state, loading state, error state, dan success state.
39. Badge status.
40. Modal konfirmasi.
41. Table filter dan pagination.

### 3.2 Fitur Opsional

Fitur berikut bersifat opsional dan hanya dibuat jika disetujui:

1. Dark mode.
2. Export PDF.
3. Export Excel.
4. Email notification.
5. Integrasi kalender eksternal.
6. Integrasi video meeting otomatis.
7. Landing page marketing yang lebih lengkap.

### 3.3 Fitur yang Tidak Dibuat

Sistem tidak menyediakan:

1. Chat internal.
2. Video call internal.
3. Integrasi Zoom otomatis.
4. Integrasi Google Meet otomatis.
5. Notifikasi email baru di luar kebutuhan reset password.
6. Integrasi kalender eksternal.
7. Pembayaran.
8. e-Court integration.
9. Digital signature.
10. Rating atau testimoni konsultasi.
11. Manajemen perkara lanjutan setelah konsultasi.
12. Export PDF/Excel tanpa persetujuan.

Link konsultasi online diisi manual oleh Admin.

---

## 4. User Roles

## 4.1 Klien

### Deskripsi

Klien adalah pengguna eksternal yang melakukan pra-pendaftaran perkara melalui sistem.

### Tujuan Penggunaan

Klien menggunakan sistem untuk mengajukan pra-pendaftaran perkara, mengunggah dokumen, memantau status, memilih jadwal konsultasi, melihat detail teknis konsultasi, dan mengajukan reschedule apabila diperlukan.

### Hak Akses UI

Klien dapat:

1. Register.
2. Login.
3. Mengelola profil sendiri.
4. Membuat pengajuan pra-pendaftaran perkara.
5. Mengunggah dokumen.
6. Melihat daftar pengajuan miliknya.
7. Melihat detail pengajuan miliknya.
8. Melihat status dan catatan verifikasi.
9. Mengunggah ulang dokumen yang perlu perbaikan.
10. Memilih jadwal konsultasi jika berkas lengkap.
11. Memilih metode konsultasi online/offline.
12. Menulis catatan preferensi konsultasi.
13. Melihat detail booking konsultasi.
14. Melihat link/lokasi konsultasi setelah dikonfirmasi Admin.
15. Mengajukan reschedule.
16. Melihat status permintaan reschedule.
17. Logout.

---

## 4.2 Staf Legal

### Deskripsi

Staf Legal adalah pengguna internal yang bertugas memverifikasi berkas pengajuan Klien.

### Tujuan Penggunaan

Staf Legal menggunakan sistem untuk memeriksa pengajuan, melihat dokumen, memberikan status verifikasi, dan menulis catatan perbaikan.

### Hak Akses UI

Staf Legal dapat:

1. Login.
2. Melihat dashboard Staf Legal.
3. Melihat daftar pengajuan yang perlu diverifikasi.
4. Melihat detail pengajuan.
5. Melihat dokumen.
6. Mengisi form verifikasi berkas.
7. Memberikan catatan umum.
8. Memberikan catatan per dokumen.
9. Logout.

---

## 4.3 Admin

### Deskripsi

Admin adalah pengguna internal yang mengelola operasional sistem.

### Tujuan Penggunaan

Admin menggunakan sistem untuk mengelola pengguna, kategori perkara, jadwal konsultasi, booking konsultasi, permintaan reschedule, penyelesaian konsultasi, dan laporan pra-pendaftaran.

### Hak Akses UI

Admin dapat:

1. Login.
2. Melihat dashboard Admin.
3. Mengelola pengguna.
4. Membuat akun Staf Legal.
5. Mengelola kategori perkara.
6. Melihat data pra-pendaftaran.
7. Mengelola jadwal konsultasi.
8. Melihat daftar booking konsultasi.
9. Mengonfirmasi detail konsultasi.
10. Mengisi link konsultasi online.
11. Mengisi lokasi konsultasi offline.
12. Menulis catatan konsultasi.
13. Melihat permintaan reschedule.
14. Menyetujui reschedule.
15. Menolak reschedule.
16. Menandai konsultasi selesai jika syarat terpenuhi.
17. Melihat laporan pra-pendaftaran.
18. Print browser laporan.
19. Logout.

---

## 5. Information Architecture

### 5.1 Public Area

```text
/
├── Landing Page
├── Login
└── Register
```

### 5.2 Klien Area

```text
/klien
├── Dashboard
├── Profil Saya
│   ├── Lihat Profil
│   └── Edit Profil
├── Pengajuan
│   ├── Daftar Pengajuan
│   ├── Buat Pengajuan
│   ├── Detail Pengajuan
│   ├── Status dan Catatan
│   └── Unggah Ulang Dokumen
├── Jadwal Konsultasi
│   ├── Pilih Jadwal Konsultasi
│   ├── Konfirmasi Booking
│   └── Detail Booking Konsultasi
├── Reschedule
│   ├── Ajukan Reschedule
│   └── Detail Permintaan Reschedule
└── Logout
```

### 5.3 Staf Legal Area

```text
/staf-legal
├── Dashboard
├── Pengajuan Verifikasi
│   ├── Daftar Pengajuan
│   ├── Detail Pengajuan
│   └── Form Verifikasi Berkas
└── Logout
```

### 5.4 Admin Area

```text
/admin
├── Dashboard
├── Staf Legal / Pengguna
│   ├── Daftar Pengguna
│   ├── Tambah User
│   ├── Detail User
│   └── Edit User
├── Kategori Perkara
│   ├── Daftar Kategori
│   ├── Tambah Kategori
│   └── Edit Kategori
├── Data Pra-Pendaftaran
│   ├── Daftar Pengajuan
│   └── Detail Pengajuan
├── Jadwal Konsultasi
│   ├── Daftar Jadwal
│   ├── Tambah Jadwal
│   └── Edit Jadwal
├── Booking Konsultasi
│   ├── Daftar Booking
│   ├── Detail Booking
│   └── Form Konfirmasi Detail Konsultasi
├── Permintaan Reschedule
│   ├── Daftar Permintaan
│   ├── Detail Permintaan
│   ├── Form Setujui Reschedule
│   └── Form Tolak Reschedule
├── Laporan
│   ├── Laporan Pra-Pendaftaran
│   └── Print Browser Layout
└── Logout
```

---

## 6. Core User Flow

## 6.1 Login Flow

1. User membuka halaman Login.
2. User mengisi email dan password.
3. Sistem memvalidasi input.
4. Sistem mengecek status akun.
5. User diarahkan ke dashboard sesuai role.
6. Jika gagal, UI menampilkan error.

## 6.2 Register Klien Flow

1. User membuka halaman Register.
2. User mengisi data registrasi.
3. UI tidak menampilkan input role atau status akun.
4. Sistem membuat akun Klien aktif.
5. User diarahkan sesuai alur autentikasi.

## 6.3 Pengajuan Pra-Pendaftaran Flow

1. Klien membuka halaman Buat Pengajuan.
2. Klien memilih kategori perkara.
3. Klien mengisi judul dan kronologi.
4. Klien mengunggah dokumen pendukung.
5. Pengajuan dikirim dengan status awal `menunggu_verifikasi`.
6. Klien dapat memantau status pengajuan.

## 6.4 Verifikasi Berkas Flow

1. Staf Legal membuka daftar pengajuan.
2. Staf Legal membuka detail pengajuan.
3. Staf Legal memeriksa dokumen.
4. Staf Legal menentukan status berkas lengkap atau tidak lengkap.
5. Staf Legal mengisi catatan jika berkas tidak lengkap.
6. Klien melihat hasil verifikasi dan catatan.

## 6.5 Booking Konsultasi Flow

1. Klien dapat memilih jadwal jika status pengajuan `berkas_lengkap`.
2. Klien memilih slot jadwal tersedia.
3. Klien memilih metode konsultasi:
   - online,
   - offline.
4. Klien mengisi catatan preferensi jika diperlukan.
5. Klien mengonfirmasi booking.
6. Booking dibuat dengan status `aktif`.
7. Detail konsultasi berstatus `menunggu_konfirmasi`.
8. Admin mengonfirmasi detail teknis konsultasi.
9. Klien dapat melihat link atau lokasi konsultasi setelah dikonfirmasi.

## 6.6 Konfirmasi Detail Konsultasi Admin Flow

1. Admin membuka daftar booking konsultasi.
2. Admin membuka detail booking.
3. Admin melihat metode konsultasi dan catatan preferensi Klien.
4. Jika metode online, Admin mengisi link konsultasi.
5. Jika metode offline, Admin mengisi lokasi konsultasi.
6. Admin dapat menulis catatan konsultasi.
7. Status konfirmasi menjadi `terkonfirmasi`.

## 6.7 Reschedule Flow

1. Klien membuka detail booking.
2. Klien mengajukan reschedule jika booking masih aktif dan tidak ada reschedule pending.
3. Klien mengisi alasan reschedule.
4. Klien memilih preferensi jadwal baru.
5. Klien memilih preferensi metode baru.
6. Permintaan reschedule berstatus `menunggu_persetujuan`.
7. Jadwal lama tetap berlaku sampai Admin menyetujui perubahan.
8. Admin menyetujui atau menolak permintaan.
9. Jika disetujui, booking lama dibatalkan dan booking baru dibuat.
10. Jika ditolak, booking lama tetap aktif dan jadwal lama tetap berlaku.

## 6.8 Selesaikan Konsultasi Flow

Admin hanya dapat menandai konsultasi selesai jika:

1. booking aktif,
2. pengajuan berstatus `jadwal_dipilih`,
3. detail konsultasi sudah `terkonfirmasi`,
4. tidak ada permintaan reschedule yang masih `menunggu_persetujuan`.

---

## 7. Screen Inventory

## 7.1 Public & Auth

1. Landing Page - Desktop.
2. Login Page - Desktop.
3. Register Page - Desktop.

## 7.2 Klien

1. Dashboard Klien - Desktop.
2. Profil Klien - Desktop.
3. Edit Profil Klien - Desktop.
4. Daftar Pengajuan Klien - Desktop.
5. Buat Pengajuan - Desktop.
6. Detail Pengajuan Klien - Desktop.
7. Status dan Catatan - Desktop.
8. Unggah Ulang Dokumen - Desktop.
9. Pilih Jadwal Konsultasi - Desktop.
10. Detail Booking Konsultasi - Desktop.
11. Ajukan Reschedule - Desktop.
12. Detail Permintaan Reschedule Klien - Desktop.

## 7.3 Staf Legal

1. Dashboard Staf Legal - Desktop.
2. Daftar Pengajuan Verifikasi - Desktop.
3. Detail Pengajuan Staf Legal - Desktop.
4. Form Verifikasi Berkas - Desktop.

## 7.4 Admin

1. Dashboard Admin - Desktop.
2. Daftar Pengguna - Desktop.
3. Tambah User - Desktop.
4. Detail User - Desktop.
5. Edit User - Desktop.
6. Daftar Kategori Perkara - Desktop.
7. Tambah Kategori - Desktop.
8. Edit Kategori - Desktop.
9. Daftar Data Pra-Pendaftaran - Desktop.
10. Detail Data Pra-Pendaftaran - Desktop.
11. Daftar Jadwal Konsultasi - Desktop.
12. Tambah Jadwal Konsultasi - Desktop.
13. Edit Jadwal Konsultasi - Desktop.
14. Daftar Booking Konsultasi - Desktop.
15. Detail Booking Konsultasi Admin - Desktop.
16. Form Konfirmasi Detail Konsultasi - Desktop.
17. Daftar Permintaan Reschedule - Desktop.
18. Detail Permintaan Reschedule - Desktop.
19. Form Setujui Reschedule - Desktop.
20. Form Tolak Reschedule - Desktop.
21. Laporan Pra-Pendaftaran - Desktop.
22. Print Browser Layout - Desktop.

## 7.5 Document Access

1. View Dokumen.
2. Download Dokumen action representation.
3. Access denied state.
4. File not found state.
5. Loading document state.

---

## 8. Figma Design Requirements

### 8.1 Output Format

Seluruh desain harus dibuat sebagai **native editable Figma design**.

Tidak boleh berupa:

1. Screenshot.
2. PNG.
3. JPG.
4. Flat image.
5. Mockup tunggal yang tidak bisa diedit.
6. Komponen yang digabung menjadi satu image.

Semua elemen harus dapat diedit langsung.

### 8.2 Figma Page Structure

Gunakan struktur page berikut:

```text
00 - Cover
01 - Design System
02 - Public & Auth
03 - Klien
04 - Staf Legal
05 - Admin
06 - Consultation & Reschedule
07 - Prototype Flow
08 - Notes & Handoff
```

### 8.3 Design Direction

Gaya visual:

1. Modern corporate.
2. Legal professional.
3. Clean.
4. Formal.
5. Web app oriented.
6. Banyak whitespace.
7. Tidak seperti slide presentasi.
8. Tidak playful.
9. Tidak marketplace-like.
10. Tidak terlalu dekoratif.

### 8.4 Desktop-First

Tahap awal desain fokus pada desktop.

Tablet dan mobile dibuat setelah desktop disetujui.

---

## 9. Design System

### 9.1 Color Palette

| Token | Hex | Usage |
|---|---|---|
| Primary Navy | `#1E3A5F` | Sidebar, primary button, active menu |
| Accent Blue | `#2563EB` | CTA, focus, highlight |
| Success Green | `#16A34A` | Success, valid, terkonfirmasi |
| Warning Amber | `#F59E0B` | Pending, waiting |
| Error Red | `#DC2626` | Error, rejected, invalid |
| Background | `#F8FAFC` | App background |
| Surface | `#FFFFFF` | Card, modal, table |
| Border | `#E2E8F0` | Divider, input border |
| Text Primary | `#0F172A` | Main text |
| Text Secondary | `#475569` | Supporting text |

### 9.2 Typography

Gunakan font modern yang mudah dibaca:

1. Inter.
2. Plus Jakarta Sans.
3. System font.

Hierarchy:

1. H1: 32px / bold.
2. H2: 24px / semibold.
3. H3: 20px / semibold.
4. Body: 14–16px.
5. Caption: 12px.

### 9.3 Component Standards

Komponen wajib:

1. Button.
2. Input.
3. Select.
4. Textarea.
5. File upload.
6. Table.
7. Card.
8. Badge.
9. Alert.
10. Modal.
11. Sidebar.
12. Header.
13. Timeline.
14. Pagination.
15. Empty state.
16. Loading state.
17. Error state.
18. Success state.

---

## 10. Status Badge Requirements

### 10.1 Status Pengajuan

| Slug | Label |
|---|---|
| `menunggu_verifikasi` | Menunggu Verifikasi |
| `berkas_tidak_lengkap` | Berkas Tidak Lengkap |
| `menunggu_verifikasi_ulang` | Menunggu Verifikasi Ulang |
| `berkas_lengkap` | Berkas Lengkap |
| `jadwal_dipilih` | Jadwal Dipilih |
| `selesai` | Selesai |

### 10.2 Status Dokumen

| Slug | Label |
|---|---|
| `terkirim` | Terkirim |
| `valid` | Valid |
| `perlu_perbaikan` | Perlu Perbaikan |
| `diganti` | Diganti |

### 10.3 Metode Konsultasi

| Slug | Label |
|---|---|
| `online` | Online |
| `offline` | Offline |

### 10.4 Status Konfirmasi Konsultasi

| Slug | Label |
|---|---|
| `menunggu_konfirmasi` | Menunggu Konfirmasi |
| `terkonfirmasi` | Terkonfirmasi |

### 10.5 Status Reschedule

| Slug | Label |
|---|---|
| `menunggu_persetujuan` | Menunggu Persetujuan |
| `disetujui` | Disetujui |
| `ditolak` | Ditolak |

### 10.6 Status Booking

| Slug | Label |
|---|---|
| `aktif` | Aktif |
| `dibatalkan` | Dibatalkan |
| `selesai` | Selesai |

---

## 11. Key Page Requirements

## 11.1 Landing Page

Landing page harus berisi:

1. Header publik.
2. Logo TNY Law Firm atau placeholder.
3. CTA Login.
4. CTA Register.
5. Hero section.
6. Section layanan pra-pendaftaran.
7. Section alur singkat.
8. CTA penutup.
9. Footer.

Landing page tidak perlu menampilkan section manfaat sistem atau role pengguna.

Headline:

```text
Portal Pra-Pendaftaran Perkara TNY Law Firm
```

Subheadline:

```text
Ajukan pra-pendaftaran perkara, unggah dokumen pendukung, pantau status verifikasi, dan pilih jadwal konsultasi melalui satu sistem yang terstruktur.
```

### 11.2 Login Page

Login wajib memiliki:

1. Logo atau placeholder.
2. Email input.
3. Password input.
4. Link visual Lupa Password.
5. Button Login.
6. Link Register.
7. Error/loading state.

### 11.3 Register Page

Register wajib memiliki:

1. Nama.
2. Email.
3. Nomor telepon.
4. Password.
5. Confirm password.
6. Button Register.
7. Link Login.
8. Error/loading state.

Register tidak boleh menampilkan input role atau status akun.

### 11.4 Pilih Jadwal Konsultasi

Wajib memiliki:

1. Daftar jadwal tersedia.
2. Filter tanggal.
3. Pilihan metode online/offline.
4. Catatan preferensi Klien.
5. Modal konfirmasi booking.
6. Informasi bahwa link/lokasi dikonfirmasi Admin.

Copy modal:

```text
Pastikan jadwal dan metode konsultasi sudah sesuai. Detail teknis konsultasi seperti link online atau lokasi offline akan dikonfirmasi oleh Admin.
```

### 11.5 Detail Booking Klien

Wajib menampilkan:

1. Status booking.
2. Metode konsultasi.
3. Status konfirmasi konsultasi.
4. Tanggal dan waktu.
5. Link konsultasi jika online.
6. Lokasi konsultasi jika offline.
7. Catatan Admin.
8. Catatan preferensi Klien.
9. Tombol Ajukan Reschedule jika memenuhi syarat.
10. Informasi reschedule pending jika ada.

Copy menunggu konfirmasi:

```text
Informasi teknis konsultasi sedang menunggu konfirmasi Admin. Admin akan melengkapi link atau lokasi konsultasi sebelum jadwal berlangsung.
```

Copy reschedule:

```text
Jika Anda berhalangan hadir, Anda dapat mengajukan reschedule. Jadwal lama tetap berlaku sampai Admin menyetujui perubahan.
```

### 11.6 Ajukan Reschedule

Wajib memiliki:

1. Informasi booking lama.
2. Alasan reschedule.
3. Preferensi jadwal baru.
4. Preferensi metode baru.
5. Button Ajukan Reschedule.
6. Informasi bahwa jadwal lama tetap berlaku.

Copy:

```text
Selama permintaan reschedule masih menunggu persetujuan Admin, jadwal lama tetap berlaku.
```

### 11.7 Admin Detail Booking

Wajib menampilkan:

1. Data Klien.
2. Data pengajuan.
3. Data jadwal.
4. Metode konsultasi.
5. Catatan preferensi Klien.
6. Status konfirmasi konsultasi.
7. Link konsultasi jika online.
8. Lokasi konsultasi jika offline.
9. Catatan Admin.
10. Tombol konfirmasi detail konsultasi.
11. Tombol tandai konsultasi selesai jika memenuhi syarat.
12. Alert alasan jika belum bisa selesai.

### 11.8 Admin Reschedule

Daftar dan detail reschedule wajib mendukung:

1. Status reschedule.
2. Data Klien.
3. Data pengajuan.
4. Booking lama.
5. Alasan Klien.
6. Preferensi jadwal baru.
7. Preferensi metode baru.
8. Aksi Setujui.
9. Aksi Tolak.

Form setujui:

1. Pilih jadwal baru tersedia.
2. Metode konsultasi baru.
3. Catatan Admin opsional.

Form tolak:

1. Catatan Admin wajib.

---

## 12. Accessibility Requirements

Desain harus memperhatikan:

1. Kontras teks memadai.
2. Label form jelas.
3. Error message dekat dengan field.
4. Button memiliki teks jelas.
5. Icon tidak berdiri sendiri tanpa label.
6. Focus state harus dirancang.
7. Modal memiliki close action.
8. Warna status tidak menjadi satu-satunya penanda.

---

## 13. Figma AI Pro Usage Guidance

Figma AI Pro dapat digunakan untuk:

1. Merapikan spacing.
2. Merapikan visual hierarchy.
3. Membuat komponen reusable.
4. Menamai layer secara konsisten.
5. Membuat auto layout.
6. Mengatur responsive behavior.
7. Membantu membuat variasi state.

Figma AI Pro tidak boleh digunakan untuk:

1. Menambah fitur bisnis baru.
2. Mengubah role.
3. Mengubah status.
4. Mengubah alur sistem.
5. Menambahkan integrasi di luar scope.

---

## 14. Design Phase Plan

### Fase 0 — Figma Setup

1. Buat struktur page Figma.
2. Buat cover.
3. Buat catatan scope dan batasan.

### Fase 1 — Design System

1. Color tokens.
2. Typography.
3. Component library.
4. Badge status.
5. Table, card, form, modal.

### Fase 2 — Public & Auth

1. Landing Page.
2. Login.
3. Register.

### Fase 3 — App Shell

1. Sidebar Klien.
2. Sidebar Staf Legal.
3. Sidebar Admin.
4. Header internal.

### Fase 4 — UI Klien Core

1. Dashboard.
2. Profil.
3. Pengajuan.
4. Dokumen.
5. Status dan catatan.

### Fase 5 — Consultation & Reschedule Klien

1. Pilih jadwal.
2. Detail booking.
3. Ajukan reschedule.
4. Detail reschedule.

### Fase 6 — UI Staf Legal

1. Dashboard.
2. Daftar pengajuan.
3. Detail pengajuan.
4. Form verifikasi.

### Fase 7 — UI Admin Core

1. Dashboard.
2. Pengguna.
3. Kategori.
4. Data pra-pendaftaran.
5. Jadwal.
6. Laporan.

### Fase 8 — Admin Consultation & Reschedule

1. Booking konsultasi.
2. Konfirmasi detail konsultasi.
3. Permintaan reschedule.
4. Setujui/tolak reschedule.
5. Selesaikan konsultasi.

### Fase 9 — Prototype Flow

1. Hubungkan alur utama Klien.
2. Hubungkan alur verifikasi Staf Legal.
3. Hubungkan alur konsultasi dan reschedule Admin.

### Fase 10 — Responsive Design

1. Tablet.
2. Mobile.
3. Prioritas halaman utama.

### Fase 11 — Final Review and Handoff

1. Review kelengkapan halaman.
2. Review konsistensi status.
3. Review role navigation.
4. Review component naming.
5. Review readiness untuk implementasi Laravel.

---

## 15. Acceptance Criteria

Desain dianggap selesai jika:

1. Semua halaman wajib tersedia.
2. Semua frame desktop tersedia.
3. Semua elemen native editable.
4. Semua role memiliki navigasi sesuai hak akses.
5. Semua status badge tersedia.
6. Tidak ada fitur di luar scope.
7. Tidak ada raw file path.
8. Tidak ada role baru.
9. Tidak ada status baru di luar rancangan.
10. Desain terlihat modern corporate dan legal professional.
11. Landing page tidak terlihat seperti slide presentasi.
12. App internal terlihat seperti web app profesional.
13. Alur konsultasi online/offline jelas.
14. Alur reschedule jelas.
15. Admin flow untuk konfirmasi dan penyelesaian konsultasi jelas.
16. Desain siap dipakai untuk skripsi, presentasi, dan implementasi Laravel.