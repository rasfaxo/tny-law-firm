# Model dan Relasi Aktual

Dokumen ini merangkum relasi Eloquent yang telah diimplementasikan. Definisi method pada `app/Models` tetap menjadi bukti utama.

## Relasi

- `User` memiliki satu profil Klien, banyak pengajuan, verifikasi sebagai Staf Legal, jadwal sebagai Admin, booking sebagai Klien, riwayat status, dan privacy consent.
- `ProfilKlien` belongs-to `User`.
- `KategoriPerkara` memiliki banyak `PraPendaftaranPerkara`.
- `PraPendaftaranPerkara` belongs-to Klien dan kategori; memiliki dokumen, dokumen aktif, riwayat dokumen, verifikasi, verifikasi terakhir, riwayat status, booking, booking aktif, dan booking terakhir.
- `DokumenPerkara` belongs-to pengajuan dan memiliki catatan verifikasi.
- `VerifikasiBerkas` belongs-to pengajuan dan Staf Legal serta memiliki catatan verifikasi.
- `CatatanVerifikasi` belongs-to verifikasi dan opsional belongs-to dokumen.
- `RiwayatStatus` belongs-to pengajuan dan user pelaku.
- `JadwalKonsultasi` belongs-to Admin serta memiliki booking dan booking aktif.
- `BookingKonsultasi` belongs-to pengajuan, jadwal, Klien, dan opsional Admin konfirmasi; memiliki permintaan reschedule.
- `PermintaanReschedule` belongs-to booking lama dan Klien; dapat mengacu pada Admin keputusan, jadwal baru, dan booking baru.
- `PrivacyConsent` belongs-to user.
- `AuditLog` dapat mengacu pada actor; resource disimpan sebagai tipe/ID agar log tetap generik dan append-only.

## Aturan query dan binding

- Semua resource Klien harus dibatasi berdasarkan ownership, bukan sekadar `findOrFail` dari ID URL.
- Policy wajib digunakan untuk pengajuan, dokumen, booking, dan reschedule; middleware role tetap menjadi lapisan awal.
- Relasi list harus memakai eager loading dan pagination ketika relevan.
- Query dokumen aktif mengecualikan status `diganti`; file lama tetap dapat disimpan sebagai riwayat tetapi tidak diperlakukan sebagai dokumen aktif.
- Route binding bisnis menggunakan custom primary key melalui `getRouteKeyName()`.

## Aturan persistence

- Assignment massal hanya untuk field yang tercantum pada `$fillable`; password dan token tidak boleh masuk audit metadata.
- Timestamp disimpan melalui Eloquent/migration dan ditampilkan sebagai WIB oleh presentation layer.
- Perubahan status pengajuan harus disertai `riwayat_status` dalam transaksi yang sama.
- Satu pengajuan hanya boleh memiliki satu booking aktif dan konflik slot harus diperiksa di dalam transaksi dengan lock.
- Penggantian dokumen membuat record/object baru dan menandai record lama `diganti`.

## Source of truth

Urutan verifikasi perubahan: migration, model, policy/service, Form Request, route, automated test, lalu dokumen ini. Bila terjadi perbedaan, jangan menebak atau mengubah schema tanpa persetujuan.
