# Database Aktual

Schema aktual ditentukan oleh migration committed. Perubahan baru wajib menggunakan migration non-destructive dan tidak boleh mengganti custom primary key.

## Tabel bisnis dan primary key

| Tabel | Primary key |
|---|---|
| `users` | `id_user` |
| `profil_klien` | `id_profil` |
| `kategori_perkara` | `id_kategori` |
| `pra_pendaftaran_perkara` | `id_pendaftaran` |
| `dokumen_perkara` | `id_dokumen` |
| `verifikasi_berkas` | `id_verifikasi` |
| `catatan_verifikasi` | `id_catatan` |
| `riwayat_status` | `id_riwayat` |
| `jadwal_konsultasi` | `id_jadwal` |
| `booking_konsultasi` | `id_booking` |
| `permintaan_reschedule` | `id_reschedule` |
| `privacy_consents` | `id_consent` |
| `audit_logs` | `id_audit` |

Laravel juga menggunakan `password_reset_tokens`, `cache`, `cache_locks`, `sessions`, `jobs`, dan `failed_jobs`. Project tidak menggunakan `job_batches` dan tidak memiliki tabel laporan.

## Foreign key utama

- Profil, pengajuan, booking, consent, dan audit actor mengacu pada `users.id_user` sesuai konteks.
- Pengajuan mengacu pada Klien dan kategori.
- Dokumen, verifikasi, riwayat status, dan booking mengacu pada pengajuan.
- Verifikasi mengacu pada Staf Legal; catatan mengacu pada verifikasi dan opsional pada dokumen.
- Jadwal mengacu pada Admin pembuat; booking mengacu pada jadwal, Klien, serta Admin konfirmasi bila sudah dikonfirmasi.
- Reschedule mengacu pada booking lama, Klien, jadwal/booking baru bila disetujui, dan Admin pengambil keputusan.
- Session memakai `user_id` yang mengacu pada custom `users.id_user` dan menjadi null ketika user dihapus.

## Aturan schema

- Nama tabel/kolom dan custom PK tidak boleh diubah tanpa persetujuan pemilik project.
- Role dan status disimpan sebagai VARCHAR dan divalidasi aplikasi; jangan menambah ENUM database.
- Semua model bisnis mendefinisikan `$table`, `$primaryKey`, tipe key, dan route key bila digunakan untuk binding.
- Laporan berasal dari query tabel bisnis.
- Dokumen hanya menyimpan metadata serta reference path; binary berada pada filesystem private.
- Migration yang telah committed tidak diubah atau dihapus setelah digunakan.
- Deployment hanya menjalankan pending migration dengan `php artisan migrate --force`.

## Operasi atomik

Gunakan transaksi database untuk pengajuan beserta dokumen/riwayat, verifikasi beserta catatan/status, penggantian dokumen, booking, konfirmasi/penyelesaian konsultasi, dan keputusan reschedule. File yang dibuat sebelum transaksi gagal harus dibersihkan melalui mekanisme kompensasi yang aman.

## Seeder production

`DatabaseSeeder` hanya menjalankan `AdminSeeder` dan `KategoriPerkaraSeeder`. Admin berasal dari environment, dibuat idempotent, dan password akun yang sudah ada tidak direset. Kategori bootstrap adalah Perdata, Pidana, Keluarga, dan Ketenagakerjaan.

## Larangan

Jangan menjalankan `migrate:fresh`, `migrate:refresh`, `db:wipe`, atau rollback pada staging/hosting. Migration rehearsal menggunakan database terpisah dan hanya `migrate --force`.
