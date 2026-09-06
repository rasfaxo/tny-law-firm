# Role dan Status Aktual

Nilai berikut berasal dari enum aplikasi serta service bisnis. Database menyimpannya sebagai VARCHAR lowercase.

## Role

| Slug | Label |
|---|---|
| `klien` | Klien |
| `admin` | Admin |
| `staf_legal` | Staf Legal |

Status akun adalah `aktif` atau `nonaktif`. Akun nonaktif tidak boleh melanjutkan session autentikasi.

## Pengajuan

`menunggu_verifikasi`, `berkas_tidak_lengkap`, `menunggu_verifikasi_ulang`, `berkas_lengkap`, `jadwal_dipilih`, dan `selesai`.

Transisi utama:

- Pengajuan baru → `menunggu_verifikasi`.
- Verifikasi hanya menerima `menunggu_verifikasi` atau `menunggu_verifikasi_ulang`.
- Hasil Staf Legal → `berkas_tidak_lengkap` atau `berkas_lengkap`.
- Penggantian dokumen terakhir yang diminta → `menunggu_verifikasi_ulang`.
- Booking aktif untuk berkas lengkap → `jadwal_dipilih`.
- Konsultasi diselesaikan Admin → `selesai`.

Setiap perubahan status pengajuan harus dicatat pada `riwayat_status` dalam transaksi yang sama.

## Dokumen dan catatan

Status dokumen: `terkirim`, `valid`, `perlu_perbaikan`, dan `diganti`.

- Dokumen baru berstatus `terkirim`.
- Verifikasi lengkap membuat dokumen aktif `valid`.
- Dokumen bermasalah menjadi `perlu_perbaikan` dan memiliki catatan `belum_diperbaiki`.
- Upload pengganti membuat object/record baru, dokumen lama menjadi `diganti`, dan catatan menjadi `sudah_diperbaiki`.

## Jadwal dan booking

Status slot: `tersedia`, `terisi`, dan `tidak_aktif`.

Status booking: `aktif`, `dibatalkan`, dan `selesai`.

Status konfirmasi teknis: `menunggu_konfirmasi` dan `terkonfirmasi`. Metode konsultasi hanya `online` atau `offline`; URL online harus HTTP/HTTPS.

Satu pengajuan hanya boleh memiliki satu booking aktif. Slot yang dipilih berubah menjadi `terisi`; penyelesaian mengubah booking dan pengajuan menjadi `selesai`.

## Reschedule

Status: `menunggu_persetujuan`, `disetujui`, dan `ditolak`.

- Permintaan baru tidak langsung mengubah booking lama.
- Penolakan mempertahankan booking dan slot lama.
- Persetujuan membatalkan booking lama, membebaskan slot lama, membuat booking baru pada slot baru, dan mencatat Admin keputusan.

## Perubahan vocabulary

Status atau role baru memerlukan persetujuan pemilik project, penambahan/penyesuaian enum, Form Request, service, UI label, filter laporan, migration bila diperlukan, serta automated test.
