# Master UI Refinement Plan

Berdasarkan hasil UI/UX Consistency Audit dan pedoman Design System, berikut adalah rencana implementasi (roadmap) perbaikan UI. 
Pekerjaan difokuskan pada standarisasi visual lintas aktor, **tanpa mengubah business logic, controller, database, atau routing**.

---

## Phase 1 — Design Foundation
**Target:** Standardisasi token desain global (Spacing, Typography, Radius, Drop-Shadow).
- **Masalah:** Arbitrary values pada typography (`text-[11px]`, `text-[13px]`, `text-xxs`), radius (`rounded-[14px]`), dan spacing padding antar tabel di Klien dan Staf Legal.
- **Aksi:** 
  1. Hapus nilai `text-xxs` dan ubah menjadi `text-xs`.
  2. Search & Replace parameter font arbitrer `text-[xx]` di dashboard dan views menjadi standard Tailwind `text-xs`, `text-sm`, `text-base`, `text-lg`.
  3. Search & Replace `rounded-[14px]` dan ukuran custom lainnya menjadi `rounded-xl`.
- **Terdampak:** Semua role (Klien, Admin, Staf Legal).
- **Dependency:** -
- **Priority:** P1 (High)
- **Risiko:** Rendah (Hanya merubah utilitas class CSS).
- **Acceptance Criteria:** Seluruh tipografi dan radius mematuhi utility class native Tailwind.

## Phase 2 — Shared Components Refactoring
**Target:** Standardisasi form, button, card, dan table component agar seragam lintas aktor.
- **Masalah:** Aktor menggunakan native tags HTML (seperti `<button>` dan `<input>`) alih-alih blade components yang telah disediakan, yang menyebabkan inkonsistensi form dan hover state. 
- **Aksi:**
  1. Integrasi input filter search menggunakan `<x-text-input>` dan `<x-input-label>`.
  2. Implementasi komponen custom `<x-select>` (atau standarisasi class select) untuk seluruh form dropdown (misal: Kategori, Status).
  3. Konversi button native menjadi `<x-primary-button>` / `<x-secondary-button>`.
  4. Standardisasi header tabel secara global dengan styling: `px-6 py-4 text-left text-xs font-bold text-gray-400 uppercase tracking-wider`.
- **Terdampak:** Semua halaman Index dan Form.
- **Dependency:** Phase 1
- **Priority:** P1 (High)
- **Risiko:** Menengah. Perlu testing agar grid search bar di mode desktop/mobile tetap presisi setelah class di-replace.
- **Acceptance Criteria:** Semua index filter menggunakan blade form components yang sama dan tinggi tabel terlihat presisi.

## Phase 3 — Admin Views Cleanup
**Target:** Konsistensi hierarki data halaman Admin.
- **Masalah:** Ada layout margin/spacing yang tidak seimbang di "Show Pengajuan", text label menggunakan fallback, dan form index search masih native.
- **Aksi:**
  1. Refactor halaman `admin.pra-pendaftaran.index` agar menggunakan shared components yang dibuat di Phase 2.
  2. Refactor halaman `admin.pra-pendaftaran.show` dan data detail lainnya (User, Jadwal) agar label tabel info menggunakan `text-xs font-bold text-gray-400 uppercase` (menggantikan `text-xxs`).
- **Terdampak:** Aktor Admin.
- **Dependency:** Phase 2
- **Priority:** P2 (Medium)
- **Risiko:** Rendah.

## Phase 4 — Staf Legal Views Cleanup
**Target:** Meratakan visual page Staf Legal agar setara dengan standard UI sistem.
- **Masalah:** Form, tabel, text, dan card memiliki margin & proporsi arbitrer yang paling banyak menyimpang dari master plan.
- **Aksi:**
  1. Perbaiki tinggi dan tipografi Card Stats Dashboard Staf Legal (ganti tipografi inline px ke `text-3xl`).
  2. Standardisasi Table List Verifikasi (Header, spacing sel tabel).
  3. Ubah format render Kolom Kategori di tabel (dari format label badge bulat menjadi teks biasa seperti di admin, agar tidak rancu dengan Status).
  4. Refactor form detail pengajuan (`staf-legal.verifikasi-berkas.show`) ke pola grid spacing yang serupa dengan Admin.
- **Terdampak:** Aktor Staf Legal.
- **Dependency:** Phase 2
- **Priority:** P2 (Medium)

## Phase 5 — Klien Views Cleanup
**Target:** Finishing touch konsistensi untuk antarmuka publik/Klien.
- **Masalah:** Beberapa tabel menggunakan padding tabel ekstra besar (`px-8 py-5`) yang boros ruang layar desktop, serta border spacing yang berbeda.
- **Aksi:**
  1. Ubah padding tabel Dokumen Pendukung dan Reschedule dari `px-8 py-5` menjadi `px-6 py-4`.
  2. Sesuaikan border alignment di `klien.pra-pendaftaran.show` agar simetris secara visual dengan role admin.
- **Terdampak:** Aktor Klien.
- **Dependency:** Phase 2
- **Priority:** P3 (Low)

## Phase 6 — Cross-Actor UI Validation
**Target:** Verifikasi komprehensif.
- **Masalah:** Memastikan tidak ada class Tailwind yang bentrok dan UI terlihat harmonis ketika berpindah-pindah sesi akun.
- **Aksi:**
  1. Buka 3 sesi pengguna (Admin, Staf Legal, Klien) secara bersamaan.
  2. Evaluasi proporsi layout dari Landing page → Dashboard → Index → Show.
  3. Validasi empty state di seluruh view jika tidak ada data yang masuk (Pastikan wrapping border tidak cacat).
  4. Tes responsiveness Mobile secara manual.
- **Dependency:** Keseluruhan fase 1 - 5 selesai.
- **Priority:** Wajib sebelum deployment UI final.
