# Master UI/UX Design Consistency Audit

## Ringkasan Audit
Berdasarkan audit yang dilakukan terhadap views Admin, Staf Legal, dan Klien:
- **Jumlah halaman yang diaudit:** 9 halaman utama (3 Dashboard, 3 Daftar Pengajuan, 3 Detail Pengajuan) beserta komponen layout dan shared components.
- **Jumlah inconsistency yang ditemukan:** 6 area masalah (Typography, Form/Filter, Buttons, Spacing/Padding, Empty States, Label/Badge).
- **Prioritas Inconsistency:**
  - P0 (Critical): 0
  - P1 (High): 3
  - P2 (Medium): 3
  - P3 (Low): 0

## Detail Temuan

| Priority | Aktor | Halaman | Area/Component | Masalah | Jenis | Dampak | Rekomendasi |
| -------- | ----- | ------- | -------------- | ------- | ----- | ------ | ----------- |
| P1 | Staf Legal | Dashboard, Index | Typography | Menggunakan arbitrary values seperti `text-[11px]`, `text-[13px]`, `text-[30px]`, `h-[168px]` yang keluar dari standar scale Tailwind (`text-xs`, `text-sm`, `text-3xl`, `h-[160px]`). | Unintentional | UI terlihat tidak proporsional dan tidak seragam bila dibandingkan role Admin & Klien. | Refactor menggunakan standar Tailwind classes (e.g. `text-xs`, `text-sm`, `text-3xl`). |
| P1 | Admin | Show Pengajuan | Typography | Menggunakan class `text-xxs` untuk label data yang mana tidak didefinisikan secara global di `tailwind.config.js`. | Unintentional | Fallback visual menyebabkan label tidak memiliki ukuran font yang distandarisasi dan cenderung lebih besar dari harapan. | Ganti penggunaan `text-xxs` menjadi `text-xs`. |
| P1 | Klien, Admin, Staf Legal | Index Pengajuan | Form & Filter | Implementasi field filter berbeda. Klien menggunakan `x-text-input`, Admin menggunakan native `<input>`, dan Staf Legal menggunakan `<x-text-input>`. Admin tidak memakai `<x-input-label>`. | Unintentional | Styling form (focus ring, border radius, padding) tidak konsisten lintas aktor. | Standardisasi menggunakan `<x-input-label>`, `<x-text-input>`, dan custom `<x-select>` untuk seluruh form filter. |
| P2 | Admin | Index Pengajuan | Button | Menggunakan native `<button>` untuk submit filter tanpa membungkusnya dalam komponen `<x-primary-button>`. | Unintentional | Visual tinggi button search dan styling hover shadow berbeda dengan styling Klien/Staf Legal. | Bungkus dengan komponen `<x-primary-button>`. |
| P2 | Klien, Admin, Staf Legal | Index Pengajuan | Table | Padding table berbeda (`px-6 py-4` vs `px-8 py-5` vs `px-5 py-4`). Warna teks header juga berbeda (`text-gray-400` vs `text-gray-500`). | Unintentional | Transisi visual berpindah role tampak kurang seamless (ada pergeseran tata letak tabel). | Standardisasi class header table (`px-6 py-4 text-xs font-bold text-gray-400 uppercase tracking-wider`). |
| P2 | Staf Legal | Index Pengajuan | Badge/Label | Label kolom "Kategori" di-render menggunakan tag inline-flex rounded-full custom, sedangkan Admin/Klien merender kategori sebagai plain text. | Unintentional | Tampilan mencolok tanpa hierarki yang jelas, mendistraksi fungsi `x-status-badge`. | Gunakan format text biasa, atau jika diperlukan bentuk tag, standarisasi sebagai komponen `<x-tag>`. |
| P2 | Staf Legal | Index Pengajuan | Empty State | Komponen `<x-empty-state>` dibungkus berbeda (tanpa border, bg-transparent) dibanding default Klien/Admin. | Unintentional | UI container tampak patah jika data tabel kosong. | Samakan styling container `<x-empty-state>` agar seragam. |

## Cross-Actor Audit (Admin ↔ Staf Legal ↔ Klien)
- **Typography:** Admin dan Klien cenderung konsisten pada scale Tailwind (xs, sm, base, lg, xl, 2xl), Staf Legal banyak hardcode pixel (`[13px]`, `[11px]`).
- **Button:** Klien lebih mengadopsi `<x-primary-button>`, sedangkan Admin kadang pakai styling manual (meski warna HEX sama).
- **Table:** Struktur grid untuk detail dan padding sel tabel saling tidak cocok antar ketiga role.
- **Card:** Penggunaan `p-6` vs `p-4` vs `p-5` dan radius (ada yang `rounded-xl`, `rounded-[14px]`, dan `rounded-2xl`).

## Rekomendasi Component Reusability
1. **Shared Component:** `<x-card>`, `<x-primary-button>`, `<x-secondary-button>`, `<x-text-input>`, `<x-input-label>`, `<x-status-badge>`, `<x-empty-state>`. Harus digunakan *strictly* tanpa in-line styling yang merusak struktur aslinya.
2. **Variant Component:** `Top Header/Navbar` dan `Sidebar` memiliki struktur desain sama tetapi list action item disesuaikan dengan role. Dashboard Cards dapat menggunakan variant color gradient khusus role.
3. **Role-specific Component:** Form verifikasi Staf Legal dan Form Konfirmasi Admin.
