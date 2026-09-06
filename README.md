# Sistem Pra-Pendaftaran Perkara TNY & PARTNERS

Aplikasi Laravel untuk registrasi Klien, pra-pendaftaran perkara, verifikasi dokumen oleh Staf Legal, konsultasi, reschedule, administrasi pengguna, dan laporan.

## Stack

- PHP 8.3+; runtime staging dan target hosting menggunakan PHP 8.4.
- Laravel 13, Blade, Tailwind CSS, Alpine CSP, dan Vite.
- MySQL/MariaDB dengan database session, cache, dan queue.
- Resend untuk email transaksional.
- Azure Blob private untuk dokumen perkara.

## Menjalankan secara lokal

Prasyarat: PHP, Composer, Node.js 20+, NPM, dan database yang didukung Laravel.

```bash
composer install
npm ci
```

Salin `.env.example` menjadi `.env`, isi konfigurasi lokal tanpa memasukkan secret ke Git, lalu jalankan:

```bash
php artisan key:generate
php artisan migrate
npm run build
php artisan serve
```

Untuk development terpadu tersedia `composer dev`. Queue production diproses oleh scheduler; definisinya berada di `routes/console.php`.

## Validasi

```bash
composer validate --strict
composer audit --locked
npm audit
npm run brand:optimize
npm run build
php artisan test
composer check-platform-reqs --no-dev
```

Jangan gunakan `migrate:fresh`, `migrate:refresh`, `db:wipe`, atau rollback terhadap database staging/hosting.

## Konfigurasi penting

Semua konfigurasi deployment berasal dari environment, termasuk URL, timezone `Asia/Jakarta`, locale Indonesia, database, session, queue, Resend, Azure Blob, trusted host/proxy, identitas firma, dan versi kebijakan privasi.

Jalankan pemeriksaan tanpa menampilkan secret:

```bash
php artisan app:production-readiness --phase=bootstrap
```

Runtime gate hanya dijalankan setelah kebijakan privasi disahkan dan password bootstrap Admin dihapus.

## Release

- CI memvalidasi build dan automated test.
- Azure App Service masih dipertahankan sebagai staging transisi.
- Workflow **Build Rumahweb Manual Artifact** menghasilkan paket aplikasi dan `public_html` terpisah untuk cPanel tanpa SSH.
- Hanya isi paket public yang ditempatkan di `public_html`; aplikasi berada di luar web root.
- Situs Rumahweb tetap dilindungi Directory Privacy sampai seluruh release gate lulus.

Panduan operasional terdapat di `docs/release/RUMAHWEB_RELEASE_GATE.md`. Aturan kontribusi dan batas keselamatan project terdapat di `AGENTS.md`.

## Struktur utama

- `app/`: controller, request, middleware, policy, model, notification, dan service.
- `database/`: migration serta seeder bootstrap production.
- `resources/`: Blade, CSS, JavaScript, dan sumber branding.
- `routes/`: route web, autentikasi, dan scheduler.
- `tests/`: automated test.
- `testing/`: evidence skripsi yang tidak dikirim ke hosting.
- `docs/`: dokumentasi canonical dan release gate.

Dokumen perkara selalu private dan hanya diakses melalui controller yang terotorisasi.
