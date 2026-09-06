# Aturan Validasi Aktual

Validasi non-trivial menggunakan Form Request. Service tetap memverifikasi invariant dan kondisi race di dalam transaksi.

## Akun dan autentikasi

- Registrasi menerima identitas akun Klien, email unik, dan password terkonfirmasi menggunakan `Password::defaults()`.
- Login, registrasi, forgot/reset password, verifikasi email, serta endpoint sensitif memiliki rate limit sesuai route.
- Respons lupa password tidak boleh membocorkan apakah email terdaftar.
- Perubahan email menghapus waktu verifikasi dan memerlukan verifikasi ulang.
- Admin hanya dapat memilih role/status yang didukung; password seluruh alur memakai kebijakan yang sama.

## Pengajuan dan dokumen

- Input perkara, kategori, kronologi, dan dokumen wajib mengikuti Form Request terkait.
- Upload menerima PDF, JPG/JPEG, atau PNG berdasarkan extension dan MIME; maksimum 5 MiB per file.
- Pengajuan awal menerima maksimum lima dokumen.
- Nama file pengguna bukan storage contract; aplikasi membuat nama/path unik.
- Setelah pengajuan dikirim, data dan upload tambahan tidak dapat diubah Klien.
- Upload pengganti hanya valid untuk catatan per dokumen yang masih `belum_diperbaiki` dan dimiliki Klien tersebut.

## Verifikasi

- `status_verifikasi` hanya `berkas_lengkap` atau `berkas_tidak_lengkap`.
- Status dokumen yang dinilai hanya `valid` atau `perlu_perbaikan`.
- Dokumen `perlu_perbaikan` wajib memiliki catatan; payload desktop/mobile memakai contract yang sama.
- Service menolak verifikasi di luar status yang dapat diverifikasi.

## Konsultasi dan reschedule

- Booking hanya untuk pengajuan milik Klien dengan status `berkas_lengkap` dan slot `tersedia`.
- Metode hanya `online` atau `offline`.
- Link konsultasi, bila digunakan, hanya URL HTTP/HTTPS.
- Konflik slot dan satu-booking-aktif divalidasi kembali dengan database transaction/locking.
- Reschedule hanya untuk booking yang memenuhi aturan dan belum memiliki permintaan pending yang bertentangan.

## Filter dan laporan

- Search dibatasi panjangnya.
- Status, kategori, metode, dan filter laporan menggunakan whitelist/enum yang tersedia.
- Rentang tanggal harus valid dan tidak boleh diinterpretasikan bebas dari input mentah.

## Kegagalan

Pesan validasi ditampilkan tanpa stack trace atau detail internal. Kegagalan multi-write harus melakukan rollback database dan kompensasi object storage yang sudah dibuat.
