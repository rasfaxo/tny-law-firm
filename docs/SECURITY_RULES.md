# Aturan Keamanan Aktual

## Autentikasi dan akses

- Session authentication menggunakan custom `users.id_user`.
- Middleware `auth`, akun aktif, role, verifikasi email, dan consent diterapkan sesuai kelompok route.
- Fitur Klien memerlukan `verified` dan persetujuan versi kebijakan aktif.
- Policy melindungi pengajuan, dokumen, booking, dan reschedule dari direct access lintas Klien/role.
- Penonaktifan akun atau reset password oleh Admin mencabut session database user terkait.

## Dokumen dan data pribadi

- Dokumen berada pada disk private melalui `Storage::disk(config('filesystems.document_disk'))`.
- Download hanya melalui controller yang mengotorisasi actor dan resource.
- Container Azure tidak boleh public; database hanya menyimpan metadata dan reference path.
- Jangan log password, token, secret, NIK, kronologi, isi dokumen, nama file, atau isi catatan hukum.
- Email hanya membawa informasi minimum dan tautan HTTPS ke aplikasi.

## Request dan platform

- Semua mutasi menggunakan CSRF protection dan endpoint sensitif memakai rate limit.
- Trusted host berasal dari daftar environment; trusted proxy `*` dilarang dan nilai `none` berarti tanpa proxy tepercaya.
- Production menggunakan HTTPS, secure/HttpOnly/SameSite cookie, `APP_DEBUG=false`, dan error page bermerek.
- CSP strict tidak mengizinkan inline script/style; asset berasal dari Vite/lokal.
- Security header meliputi CSP, HSTS pada HTTPS, frame denial, no-sniff, referrer policy, permissions policy, COOP, dan CORP.
- External link menggunakan `rel="noopener noreferrer"` bila membuka context baru.

## Email, queue, dan audit

- Resend API key hanya berada di environment; pengirim dan Reply-To berasal dari konfigurasi.
- Notification diproses queue database setelah transaction commit dengan retry/timeout terbatas.
- Failed job dicatat secara terstruktur tanpa isi email.
- Audit log append-only menyimpan actor/role snapshot, aksi, resource, metadata aman, dan waktu; tidak mempunyai route publik atau UI Admin.

## Deployment dan secret

- `.env`, credential, database lokal, log, testing evidence, dependency development, dan private key tidak boleh masuk artifact.
- Secret tidak dicetak ke chat, CI log, dokumentasi, atau commit.
- Readiness command tidak boleh menampilkan nilai secret.
- Migration production harus non-destructive dan deployment hanya memakai `migrate --force`.
- Directory Privacy Rumahweb tetap aktif sampai runtime gate, backup/restore, Resend, Azure Blob, dan pengujian eksternal selesai.

## Pengujian keamanan

Verifikasi minimal mencakup direct access ownership, lintas-role, CSRF, rate limit, upload MIME/ukuran, CSP/header, trusted host/proxy, secure cookie, generic password-reset response, dan tidak adanya data sensitif pada audit/email/log. Scan hanya boleh menargetkan environment yang diotorisasi; hasil scanner harus diverifikasi terhadap kode.
