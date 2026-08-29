# Master Design System - TNY Law Firm

Dokumen ini adalah source of truth untuk komponen UI pada aplikasi Pra-Pendaftaran Perkara TNY Law Firm. Tujuan dari Design System ini adalah memastikan konsistensi visual di seluruh portal Admin, Staf Legal, dan Klien.

## 1. Design Token Foundation

### 1.1 Typography (Font: Inter)
Dilarang menggunakan nilai spesifik (seperti `text-[11px]`, `text-[13px]`, atau `text-xxs`). Gunakan standar scale Tailwind:
- **H1 (Display):** `text-2xl font-extrabold`
- **H2 (Title):** `text-xl font-bold`
- **H3 (Subtitle):** `text-lg font-bold`
- **Body Large:** `text-base`
- **Body:** `text-sm font-medium` (Gunakan `font-semibold` untuk text yang butuh penekanan).
- **Caption:** `text-xs font-bold text-gray-400 uppercase tracking-wider` (Utamanya digunakan untuk Table Header dan Label Data).

### 1.2 Color Palette
Sesuai dari `tailwind.config.js` project:
- **Primary:** Navy Dark (`#0F1E3A`) dan Navy Primary (`#1E3A5F`). Digunakan untuk text utama, heading, dan gradient background Klien.
- **Accent:** Blue (`#2563EB`). Digunakan untuk CTA buttons, focus ring, link, dan highlight.
- **Success:** Green (`#16A34A`).
- **Warning:** Amber (`#F59E0B`).
- **Error:** Red (`#DC2626`).
- **Backgrounds:** Light (`#F8FAFC`). Digunakan untuk body app dan background table/card alternate.
- **Surface:** White (`#FFFFFF`). Digunakan untuk Card dan Modal.
- **Border:** Slate-200 (`#E2E8F0`).

### 1.3 Border Radius (Corners)
Dilarang menggunakan nilai spesifik seperti `rounded-[14px]`. Gunakan standar scale Tailwind:
- **Card & Container:** `rounded-xl` (12px) atau `rounded-2xl` (16px) untuk banner utama.
- **Buttons, Inputs, Select:** `rounded-xl`.
- **Badge/Status:** `rounded-full`.

### 1.4 Spacing & Padding
- **Card Internal Padding:** Default menggunakan `p-6`. Hindari kombinasi seperti `px-8 py-5` pada tabel untuk mencegah layout yang lompat-lompat.
- **Table Cell Padding:** Standardisasi `px-6 py-4`.

## 2. Component Categorization

### 2.1 Shared Components (Wajib digunakan bersama)
- **`<x-card>`**: Pembungkus konten dengan border dan drop-shadow yang sangat halus. Base class: `bg-white border border-[#E2E8F0] p-6 rounded-xl shadow-sm`.
- **`<x-primary-button>` & `<x-secondary-button>`**: CTA interaktif utama. Pastikan selalu dipanggil melalui komponen ini dan bukan tag `<button>` native yang di-style manual.
- **Form Elements (`<x-text-input>`, `<x-input-label>`, `<x-select>`)**: Semua input field wajib memiliki tinggi `h-11`, focus outline `focus:ring focus:ring-accent-blue/20`, dan border `border-[#E2E8F0]`. 
- **`<x-status-badge>`**: Badge eksklusif untuk status (misal: "Menunggu Verifikasi", "Berkas Lengkap", dsb).
- **`<x-empty-state>`**: Komponen ilustrasi visual dan pesan ketika daftar tabel atau data detail tidak tersedia.
- **`<x-alert-banner>`**: Banner alert sukses/error di bagian atas halaman (Flash message).

### 2.2 Variant Components
- **Top Navigation (`x-topbar`)**: Struktur sama, namun menu breadcrumb akan bervariasi bergantung aktor.
- **Sidebar (`x-navigation`)**: Hierarki item menu bervariasi berdasarkan aktor, namun menggunakan styling active indicator emas (`#d4af37`) yang sama.
- **Data Table Layout**: Pola header (Kiri) dan Aksi (Kanan) sama di seluruh tabel. Kolom informasi beradaptasi sesuai role (misal: Admin menampilkan Klien, Klien tidak perlu).

### 2.3 Role-specific Components
*(Visual khusus yang dipertahankan sebagai Intentional Difference untuk membedakan konteks lingkungan kerja.)*
- **Dashboard Jumbotron Banner:** 
  - Admin: Menggunakan Gradient Blue-to-Purple (menandakan scope manajemen yang luas).
  - Staf Legal: Menggunakan Gradient Amber-to-Orange (menandakan lingkungan verifikasi & kewaspadaan operasional).
  - Klien: Menggunakan Gradient Navy (Corporate brand).
- **Specific Forms:**
  - Form verifikasi Staf Legal.
  - Form konfirmasi detail konsultasi Admin.
