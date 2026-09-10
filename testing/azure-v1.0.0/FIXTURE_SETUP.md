# Fixture Retest Azure v1.0.0

Fixture dibuat otomatis oleh `Database\Seeders\RetestFixtureSeeder`; tidak perlu membuat akun, consent, kategori, perkara ownership, atau perkara perbaikan melalui UI. Seeder ini adalah helper sementara dan bukan bagian dari artifact final v1.0.0.

## A. Isi fixture

Seeder membuat data anonim berikut:

| Fixture | Nilai tetap |
|---|---|
| Admin | `admin.retest.v100@example.test` |
| Staf Legal | `legal.retest.v100@example.test` |
| Klien A | `client-a.retest.v100@example.test` |
| Klien B | `client-b.retest.v100@example.test` |
| Kategori | `Retest Azure v1.0.0` |
| Perkara ownership Klien B | `Retest Ownership Azure v1.0.0` |
| Perkara perbaikan Klien A | `Retest Perbaikan Azure v1.0.0` |
| Blob ownership | `fixtures/v1.0.0/ownership-document.pdf` |
| Blob perbaikan awal | `fixtures/v1.0.0/repair-original-document.pdf` |

Kedua Klien dibuat aktif, `email_verified_at` terisi, profil anonim tersedia, dan consent untuk `PRIVACY_POLICY_VERSION` aktif sudah tercatat. Perkara perbaikan berstatus `berkas_tidak_lengkap`, dokumen berstatus `perlu_perbaikan`, serta mempunyai catatan `belum_diperbaiki`.

Pengiriman/verifikasi email bukan bagian PF-01–PF-07 atau ST-01–ST-09. Karena verifikasi fixture dilakukan langsung oleh seeder, fungsi email harus tetap dicatat `NOT EXECUTED` jika Resend/queue staging memang tidak diuji.

## B. Gate wajib sebelum deployment helper

Pastikan App Service masih menunjuk lingkungan retest tunggal dan mempunyai konfigurasi berikut:

```text
APP_URL=https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net
DB_CONNECTION=mysql
DB_DATABASE=<nama database terisolasi yang mengandung kata retest>
DOCUMENT_DISK=azure
AZURE_STORAGE_CONTAINER=documents
AZURE_STORAGE_PREFIX=retest/v1.0.0/tnypartners
AZURE_STORAGE_CONNECTION_STRING=BlobEndpoint=https://tnylawfirmstorage.blob.core.windows.net/;SharedAccessSignature=<SAS container staging; jangan simpan di repository>
PRIVACY_POLICY_READY=true
PRIVACY_POLICY_VERSION=v1.0
```

Tambahkan tiga App Settings sementara:

```text
RETEST_FIXTURES_ENABLED=true
RETEST_FIXTURE_CONFIRMATION=SEED-AZURE-V100-RETEST-ONLY
RETEST_FIXTURE_PASSWORD=<buat password uji acak minimal 16 karakter>
```

`RETEST_FIXTURE_PASSWORD` adalah satu password bersama untuk keempat akun anonim. Simpan nilainya hanya pada password manager dan file environment lokal di luar repository. Seeder tidak mencetak password tersebut.

Seeder menolak berjalan apabila hostname aplikasi, hostname Blob Storage staging `tnylawfirmstorage`, database retest, container, prefix, disk dokumen, consent, confirmation, izin SAS, atau panjang password tidak sesuai. Akun production `tnylawfirmdocs` dilarang untuk retest. Seluruh metadata database dibuat dalam satu transaction; blob yang baru dibuat dikompensasi jika transaction gagal.

## C. Deployment dua tahap

1. Deploy branch helper yang berisi `RetestFixtureSeeder` melalui workflow `CD Pipeline - Deploy to Azure App Service`.
2. Startup menjalankan `DatabaseSeeder`, yang memanggil fixture seeder hanya karena `RETEST_FIXTURES_ENABLED=true`.
3. Periksa log startup. Baris `Retest fixture Azure v1.0.0 berhasil dibuat.` harus muncul bersama lima ID non-rahasia:
   - `RETEST_CATEGORY_ID`
   - `SECURITY_OTHER_CASE_ID`
   - `SECURITY_REPAIR_CASE_ID`
   - `SECURITY_REPAIR_OLD_DOCUMENT_ID`
   - `SECURITY_REPAIR_NOTE_ID`
4. Jika ID tidak terlihat pada log, gunakan query read-only `templates/fixture-lookup.sql`.
5. Hapus ketiga App Settings `RETEST_FIXTURES_*` segera setelah seed berhasil.
6. Deploy ulang tag `v1.0.0` yang menunjuk commit `3b0ae8205b6e636df275f008162b25c6c8ec5bd1`.
7. Pastikan workflow final berhasil. Simpan URL workflow final yang baru; run lama tidak lagi menjadi bukti deployment aktif setelah helper pernah dipasang.
8. Jalankan ulang deployment verification dan preflight terhadap deployment final tersebut sebelum validation run 1 VU.

Langkah 6 wajib: pengujian akhir harus menggunakan source persis v1.0.0, sementara data fixture dan blob yang sudah dibuat tetap berada pada database/container retest.

## D. Ambil ID fixture

Jika ID tidak terlihat di startup log, jalankan query read-only `templates/fixture-lookup.sql` pada database retest. Query sudah memakai email dan judul tetap sehingga tidak ada placeholder yang perlu diganti.

`SECURITY_PRIVATE_DOCUMENT_PATH` sudah tetap:

```text
fixtures/v1.0.0/ownership-document.pdf
```

## E. Isi environment lokal runner

1. Salin `templates/retest-environment.ps1.example` ke lokasi di luar repository.
2. Isi satu variabel lokal `$fixturePassword` dengan password yang sama seperti App Setting sementara tadi.
3. Isi lima ID hasil seeder.
4. Isi URL container dan SAS read/list sementara untuk pemeriksaan langsung ST-07/ST-09.
5. Dot-source file tersebut sebelum menjalankan preflight/runner.

Jangan menyimpan password, SAS, cookie, CSRF token, API key, atau connection string pada evidence atau repository. Jangan menjalankan recorded run sebelum login seluruh role, ID fixture, blob, dan validation run 1 VU telah diverifikasi.
