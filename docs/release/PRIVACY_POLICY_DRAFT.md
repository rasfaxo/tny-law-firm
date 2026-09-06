# USULAN—WAJIB DISETUJUI FIRMA

Dokumen ini adalah bahan review kebijakan privasi TNY & PARTNERS. Dokumen belum berlaku, bukan pendapat hukum, dan tidak boleh dipakai untuk mengaktifkan consent sebelum disahkan oleh pihak firma.

## Data dan tujuan pemrosesan

Aplikasi memproses data akun, profil Klien, informasi pra-pendaftaran perkara, dokumen pendukung, hasil verifikasi, jadwal, dan informasi konsultasi untuk menyediakan layanan pra-pendaftaran perkara.

Dokumen perkara dirancang sebagai resource privat yang hanya diakses melalui akun berwenang. Isi dokumen, nomor identitas, kronologi, password, token, dan isi catatan hukum tidak boleh dimasukkan ke metadata audit aplikasi.

## Matriks retensi yang diusulkan

| Kelompok data | Usulan retensi |
|---|---:|
| Akun, profil, dan bukti persetujuan | Masa layanan + 5 tahun |
| Pengajuan, dokumen, verifikasi, dan riwayat | 5 tahun setelah proses ditutup |
| Booking dan reschedule | 5 tahun setelah konsultasi selesai |
| Audit log aplikasi | 2 tahun |
| Log aplikasi | 14 hari |
| Failed jobs | 7 hari |
| Backup operasional | Target rolling 30 hari, menunggu kemampuan provider |
| Soft-deleted blob/container | 14 hari |
| Versi Blob sebelumnya | 90 hari |

Matriks ini tidak mengaktifkan penghapusan otomatis. Soft delete dan versioning merupakan kontrol pemulihan operasional, bukan pengganti keputusan retensi data bisnis.

## Permintaan subjek data

Klien direncanakan dapat meminta akses, koreksi, atau penghapusan melalui kanal resmi firma. Firma harus menentukan prosedur verifikasi identitas pemohon, penanggung jawab, waktu respons, pengecualian retensi, dan bukti penyelesaian sebelum naskah berlaku.

## Hal yang wajib diputuskan firma

- dasar pemrosesan untuk setiap kelompok data;
- validitas setiap masa retensi dan titik awal perhitungannya;
- mekanisme permintaan akses, koreksi, dan penghapusan;
- pihak penerima/pemroses data termasuk Rumahweb, Resend, dan Azure;
- prosedur insiden dan pemberitahuan;
- versi kebijakan serta tanggal berlaku;
- redaksi final yang akan ditampilkan kepada Klien.

Referensi awal: [Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi](https://jdih.komdigi.go.id/produk_hukum/view/id/832/t/undangundang%2Bnomor%2B27%2Btahun%2B2022).

## Status persetujuan

- Penelaah firma: belum ditentukan.
- Versi final: belum ditentukan.
- Tanggal berlaku: belum ditentukan.
- Status: **belum disahkan**.
