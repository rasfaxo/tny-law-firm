# Project Context

Judul:
Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm

Tech Stack:
- Laravel
- MySQL
- Blade + Tailwind CSS
- Laravel Breeze
- Laravel Storage
- Cloud VPS

Aktor:
- klien
- admin
- staf_legal

Fitur Utama:
- Registrasi dan login
- Pengajuan pra-pendaftaran perkara
- Upload dokumen pendukung
- Verifikasi berkas
- Catatan verifikasi
- Unggah ulang dokumen
- Pemilihan jadwal konsultasi
- Laporan pra-pendaftaran

Keputusan Arsitektur:
- Tidak membuat tabel laporan
- Role dan status menggunakan VARCHAR
- Status disimpan sebagai slug lowercase
- File dokumen disimpan di Laravel Storage
- Database hanya menyimpan metadata dan file_path
- Tidak menggunakan email terlebih dahulu
