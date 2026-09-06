# Konteks Project

## Identitas dan tujuan

Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm membantu Klien mengajukan perkara dan dokumen awal, Staf Legal memverifikasi berkas, serta Admin mengelola pengguna, kategori, konsultasi, reschedule, dan laporan.

Dokumen ini merangkum implementasi saat ini. Route, migration, model, Form Request, policy, service, dan konfigurasi tetap menjadi bukti teknis utama.

## Aktor

- `klien`: registrasi, verifikasi email, persetujuan privasi, profil, pengajuan, dokumen, monitoring, perbaikan, booking, dan reschedule.
- `staf_legal`: daftar/detail pengajuan, akses dokumen terotorisasi, verifikasi, dan catatan umum/per dokumen.
- `admin`: pengguna, Staf Legal, kategori, data pengajuan, jadwal, booking, keputusan reschedule, status konsultasi, dashboard, dan laporan cetak.

Role dan status disimpan sebagai slug lowercase, bukan ENUM database.

## Alur bisnis

1. Klien registrasi, memverifikasi email, lalu menyetujui versi kebijakan privasi aktif.
2. Klien membuat pengajuan beserta maksimal lima dokumen awal.
3. Pengajuan masuk `menunggu_verifikasi` dan perubahan status dicatat.
4. Staf Legal memutuskan `berkas_lengkap` atau `berkas_tidak_lengkap` dan dapat membuat catatan per dokumen.
5. Dokumen yang perlu diperbaiki diganti menggunakan object baru; file lama tidak ditimpa.
6. Setelah perbaikan, pengajuan masuk `menunggu_verifikasi_ulang`.
7. Pengajuan `berkas_lengkap` dapat memilih satu booking aktif.
8. Admin mengonfirmasi detail teknis, memproses reschedule, dan menyelesaikan konsultasi.
9. Admin mencetak laporan dari query data; tidak ada tabel laporan.

## Stack dan lingkungan

- Laravel 13, PHP 8.3+; PHP 8.4 digunakan pada CI, Azure staging, dan target Rumahweb.
- Blade, Tailwind CSS, Alpine CSP, dan Vite/Node.js 20.
- MySQL/MariaDB; session, cache, dan queue menggunakan database pada deployment.
- Resend untuk email transaksional queued after-commit.
- Laravel Filesystem dengan Azure Blob private untuk dokumen deployment.
- Timezone `Asia/Jakarta`, locale `id`, dan tampilan waktu WIB.

Azure App Service tetap menjadi staging transisi. Target rehearsal adalah shared hosting Rumahweb tanpa SSH menggunakan dua artifact ZIP dan cron cPanel. Situs tidak boleh dibuka sebelum release gate dan kebijakan privasi final lulus.

## Batasan

Tidak ada integrasi e-Court, pembayaran online, tanda tangan digital, chat realtime, video conference internal, atau manajemen perkara setelah konsultasi. Dokumen tidak disimpan sebagai blob database dan tidak boleh diakses melalui URL publik.

## Referensi aktif

- `AGENTS.md`: aturan kerja dan keselamatan.
- `DATABASE_PLAN.md` dan `MODEL_RELATION_PLAN.md`: schema serta relasi.
- `STATUS_RULES.md`: vocabulary dan transisi status.
- `VALIDATION_RULES.md` dan `SECURITY_RULES.md`: boundary input dan keamanan.
- `FEATURE_LIST.md`: inventory fitur aktual.
- `release/RUMAHWEB_RELEASE_GATE.md`: prosedur rehearsal hosting.
