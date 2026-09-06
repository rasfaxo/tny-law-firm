# Daftar Fitur Aktual

Dokumen ini adalah inventory ringkas berdasarkan route dan implementasi saat ini. Detail endpoint harus diverifikasi dengan `php artisan route:list`.

## Publik dan autentikasi

- Landing page dan kebijakan privasi.
- Registrasi Klien dengan rate limit.
- Login/logout session, lupa password, reset password, dan konfirmasi password.
- Verifikasi email dan kirim ulang verifikasi.
- Penolakan akun nonaktif serta pencabutan session ketika akun dinonaktifkan atau password direset Admin.

## Klien

- Persetujuan versi kebijakan privasi aktif.
- Pengelolaan profil; perubahan email mewajibkan verifikasi ulang.
- Daftar, buat, dan detail pra-pendaftaran milik sendiri.
- Upload dokumen awal bersama pengajuan serta download terotorisasi.
- Monitoring status, riwayat, hasil verifikasi, dan catatan per dokumen.
- Upload pengganti hanya untuk catatan yang belum diperbaiki; dokumen lama dipertahankan.
- Pemilihan jadwal untuk pengajuan `berkas_lengkap`.
- Detail booking, pengajuan reschedule, dan monitoring keputusan.

## Staf Legal

- Dashboard, antrean, riwayat, dan detail verifikasi.
- Download dokumen melalui controller terotorisasi.
- Verifikasi lengkap/tidak lengkap, catatan umum, status per dokumen, dan catatan perbaikan.
- Verifikasi hanya pada `menunggu_verifikasi` atau `menunggu_verifikasi_ulang`.

## Admin

- Dashboard statistik.
- Daftar/detail/edit/status/reset password Klien.
- CRUD dan status/reset password akun Staf Legal.
- CRUD kategori perkara.
- Daftar/detail pra-pendaftaran dan download dokumen terotorisasi.
- CRUD/status slot konsultasi.
- Daftar/detail booking, konfirmasi teknis online/offline, dan penyelesaian konsultasi.
- Daftar/detail serta persetujuan/penolakan reschedule.
- Laporan index, pra-pendaftaran, verifikasi, booking, reschedule, dan pengajuan selesai; seluruhnya mendukung tampilan cetak browser.

## Infrastruktur aplikasi

- Database session, cache, queue, dan failed jobs.
- Email Resend: verifikasi, reset password, hasil verifikasi, perubahan/konfirmasi konsultasi, keputusan reschedule, dan konsultasi selesai.
- Audit log append-only untuk aksi kritis tanpa isi dokumen, kronologi, NIK, catatan hukum, token, password, atau secret.
- Azure Blob private melalui abstraction filesystem.
- Strict CSP, HSTS pada HTTPS, security headers, trusted hosts, dan trusted proxies eksplisit.
- Halaman error bermerek 403, 404, 419, 429, 500, dan 503.
- Readiness command fase bootstrap/runtime dan opsi external/storage round-trip.
- Scheduler memproses queue database serta pruning failed jobs/reset token.

## Tidak tersedia

- e-Court, pembayaran, tanda tangan digital, live chat, video conference internal, auto-delete retensi, dan UI audit log.
- Upload dokumen tambahan setelah pengajuan dikirim.
- Endpoint publik untuk dokumen, consent, audit log, atau queue.
