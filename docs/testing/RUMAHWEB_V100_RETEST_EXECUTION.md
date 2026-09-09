# Eksekusi Retest v1.0.0 pada Clone Privat Rumahweb

> **SUPERSEDED / TIDAK DIGUNAKAN.** Retest dan laporan akhir telah dikunci ke satu lingkungan Azure App Service staging. Dokumen ini dipertahankan hanya sebagai riwayat perencanaan dan bukan sumber konfigurasi, evidence, atau hasil skripsi.

Status: **PREPARED — belum dieksekusi**. Angka performa dan PASS/FAIL hanya boleh ditulis setelah evidence aktual tersedia.

## 1. Batas dan larangan

- Target aktif satu-satunya adalah `https://retest.tnypartners.com` dengan Directory Privacy dan SSL valid.
- `tnypartners.com` dan `www.tnypartners.com` adalah production dan tidak boleh menerima JMeter, payload ST, spider, atau ZAP Active Scan.
- Database clone harus baru (`tnym6311_tnyretest`) dan tidak boleh diisi salinan data production.
- Container Azure harus non-production (`documents-retest-v100`) dan private.
- Jangan menjalankan `migrate:fresh`, `migrate:refresh`, `migrate:rollback`, atau `db:wipe`.
- Evidence lama tidak boleh ditimpa. Semua output baru masuk ke `testing/retest/<timestamp>-rumahweb-v100/`.

## 2. Gate izin Rumahweb

1. Isi [template permohonan izin](../../testing/rumahweb-v1.0.0/templates/rumahweb-authorization-request.md).
2. Minta Rumahweb menyetujui hostname, IP penguji, jadwal, batas 20 VU, dan ZAP Active Scan publik secara tertulis.
3. Simpan email/tiket yang sudah direduksi dari data sensitif sebagai evidence `authorization/`.
4. Masukkan hanya nomor referensi ke `RETEST_PROVIDER_AUTHORIZATION`.
5. Jika ditolak, jangan mengalihkan pengujian ke production. Rumahweb hanya menerima smoke/passive check; load dan Active Scan tetap di Azure staging.

## 3. Provisioning clone terisolasi

1. Buat subdomain `retest.tnypartners.com` dengan document root khusus `/home/tnym6311/retest-v100/tny-law-firm/public`.
2. Aktifkan SSL dan Directory Privacy sebelum aplikasi diunggah.
3. Buat parent clone `/home/tnym6311/retest-v100`; folder aplikasi hasil ekstraksi akan menjadi `/home/tnym6311/retest-v100/tny-law-firm`, sedangkan hanya subfolder `public` yang menjadi document root.
4. Buat database dan user baru `tnym6311_tnyretest`; berikan hak hanya pada database tersebut.
5. Buat container Azure non-production `documents-retest-v100`, lalu gunakan:

```dotenv
DOCUMENT_DISK=azure
AZURE_STORAGE_CONTAINER=documents-retest-v100
AZURE_STORAGE_PREFIX="retest/v1.0.0/tnypartners"
AZURE_READINESS_PREFIX="release-gate/retest/v1.0.0/tnypartners"
```

6. SAS dibatasi pada container retest dan memiliki create/read/write/delete/list. Jangan menyimpan SAS di source, chat, screenshot, atau evidence.
7. Unduh artifact workflow Rumahweb dari `v1.0.0`.
8. Dari root repository, tentukan satu direktori run yang akan dipakai sampai akhir, lalu jalankan verifikasi sebelum upload:

```powershell
$runDir = Join-Path (Resolve-Path .) ("testing\retest\{0}-rumahweb-v100" -f (Get-Date -Format 'yyyyMMdd-HHmmss'))
.\testing\rumahweb-v1.0.0\scripts\verify-release-artifact.ps1 `
  -ArtifactDirectory '<folder-artifact>' `
  -EvidenceDirectory (Join-Path $runDir 'release')
```

Manifest harus menunjuk commit `3b0ae82...` dan kedua ZIP harus cocok dengan `rumahweb-sha256.txt`.

9. Ekstrak `rumahweb-application.zip` di `/home/tnym6311/retest-v100`, sehingga terbentuk `tny-law-firm`. Gunakan `tny-law-firm/public` dari ZIP aplikasi sebagai document root; `public/index.php` relatifnya tetap valid tanpa diubah. `rumahweb-public-html.zip` tetap diverifikasi dan disimpan sebagai bukti build, tetapi jangan diekstrak ke clone karena `index.php` paket itu menunjuk ke sibling production `/home/tnym6311/tny-law-firm`. Jika cPanel tidak mengizinkan document root tersebut, hentikan provisioning dan minta Rumahweb mengaturnya—jangan mengubah production dan jangan diam-diam memodifikasi artifact.
10. Salin `testing/rumahweb-v1.0.0/templates/clone-env.template` menjadi `<APP_ROOT>/.env`, lalu isi secret langsung melalui cPanel. Gunakan `APP_URL=https://retest.tnypartners.com`, database clone, Azure retest, `APP_DEBUG=false`, session database, dan queue database. Pada project Resend yang sudah ada, tambahkan dan verifikasi sending domain `retest.tnypartners.com`, gunakan API key khusus retest, lalu isi `MAIL_FROM_ADDRESS=no-reply@retest.tnypartners.com`. Readiness mewajibkan domain pengirim sama persis dengan host `APP_URL`; alamat `no-reply@tnypartners.com` tidak memenuhi gate clone.
11. Karena hosting tidak menyediakan SSH, gunakan perintah dalam `testing/rumahweb-v1.0.0/templates/cpanel-cron-commands.txt.example`. Buat hanya satu one-time cPanel Cron Job pada satu waktu, tunggu satu eksekusi, periksa log privat, lalu hapus sebelum membuat langkah berikutnya. Jangan lanjut setelah kegagalan dan jangan menyimpan secret di command cron.
12. Verifikasi readiness, migration, seed bootstrap, cache, `/`, `/up`, dan `/login` menghasilkan respons yang benar. Jangan menganggap HTTP 200 cukup: periksa marker halaman dan log readiness.

## 4. Data fixture anonim

Siapkan fixture melalui UI clone dan catat hanya ID non-rahasia:

1. Buka `/register`, buat Klien A dan Klien B dengan identitas anonim, lalu selesaikan verifikasi email dan persetujuan kebijakan privasi pada keduanya.
2. Login Admin clone, buat satu akun Staf Legal anonim dan satu kategori perkara khusus retest.
3. Login Klien B, buat satu pengajuan dengan satu PDF valid. Catat `id_pendaftaran` dari URL detail sebagai `SECURITY_OTHER_CASE_ID`.
4. Login Klien A, buat satu pengajuan dengan satu PDF valid. Login Staf Legal, verifikasi sebagai `berkas_tidak_lengkap`, tandai dokumen `perlu_perbaikan`, dan isi catatan. Dari URL/record clone catat `SECURITY_REPAIR_CASE_ID`, `SECURITY_REPAIR_OLD_DOCUMENT_ID`, dan `SECURITY_REPAIR_NOTE_ID`. Jangan melakukan unggah ulang sebelum ST-08.
5. Dari record dokumen clone, salin hanya nilai `file_path` non-rahasia ke `SECURITY_PRIVATE_DOCUMENT_PATH`; jangan salin URL SAS.
6. Siapkan PDF valid kedua untuk ST-08 dan file `.exe` inert untuk ST-07 pada mesin penguji.

Inventaris minimum:

- satu Admin;
- satu Staf Legal;
- dua Klien dengan email terverifikasi dan persetujuan kebijakan privasi aktif;
- satu kategori perkara;
- satu perkara milik Klien B untuk ST-06;
- satu dokumen milik Klien A untuk ST-08;
- satu perkara Klien A berstatus `berkas_tidak_lengkap`, satu dokumen lama `perlu_perbaikan`, dan satu catatan `belum_diperbaiki` khusus ST-08;
- satu file PDF valid kecil dan satu file `.exe` inert khusus validasi tipe.

Jangan gunakan nama, email, dokumen, atau isi perkara production. Password disimpan hanya pada file environment lokal yang tidak di-commit.

## 5. Validasi harness dan environment lokal

1. Salin `testing/rumahweb-v1.0.0/templates/retest-environment.ps1.example` ke lokasi di luar repository.
2. Isi nilai lokal, lalu muat di PowerShell dengan `. '<path-private>\retest-environment.ps1'`.
3. Jalankan validasi statis:

```powershell
.\testing\rumahweb-v1.0.0\scripts\validate-harness.ps1
```

4. Pastikan browser dapat membuka clone melalui Directory Privacy dan sertifikat cocok dengan `retest.tnypartners.com`.
5. Setelah artifact verification evidence berada pada `$runDir/release`, jalankan preflight. Skrip ini menolak production, memastikan izin tertulis sudah direferensikan, memvalidasi commit artifact, DNS, TLS, Directory Privacy, marker `/`, `/up`, `/login`, serta lokasi JMeter dan ZAP:

```powershell
.\testing\rumahweb-v1.0.0\scripts\preflight-retest.ps1 -RunDirectory $runDir
```

Jangan menjalankan performance/security/ZAP bila preflight belum menghasilkan `preflight.json` berstatus `PASS`.

## 6. Performance retest PF-01–PF-07

Jalankan hanya pada jadwal yang disetujui:

```powershell
.\testing\rumahweb-v1.0.0\scripts\run-performance.ps1 -RunDirectory $runDir
```

Runner melakukan validation run 1 VU (tidak masuk metrik), kemudian 12 recorded run: empat flow pada 5, 10, dan 20 VU. Ramp-up general adalah 5/5, 10/10, 20/20; flow klien, perbaikan, dan legal adalah 5/5, 10/10, 20/10. Loop selalu 1. Jeda 65 detik antar-run memisahkan jendela throttle; jeda tidak masuk JTL atau metrik.

Middleware aplikasi membatasi beberapa endpoint berdasarkan IP (misalnya login dan submit awal). Harness tidak menonaktifkan atau melewati kontrol tersebut. Jika beban 20 VU menghasilkan HTTP 429/error, hasil itu dicatat apa adanya sebagai perilaku runtime, bukan dihapus dari metrik.

PF-04 menggunakan alur v1.0.0 yang sah: buat perkara dan dokumen awal, Staf Legal memberi catatan `perlu_perbaikan`, Klien membuka form **Unggah Ulang Dokumen**, lalu mengirim dokumen pengganti. Endpoint upload tambahan lama tidak digunakan.

Selama tiap recorded run, ambil screenshot cPanel Metrics untuk CPU, I/O, Entry Processes, memory, waktu mulai/selesai, dan lokasi penguji. Jangan mengurangi VU atau request ketika hasil buruk.

Setelah selesai:

```powershell
.\testing\rumahweb-v1.0.0\scripts\summarize-performance.ps1 -RunDirectory $runDir
```

Ringkasan menghasilkan tepat 21 baris dan memisahkan `execution_status` dari `p95_target_status`. P95 dihitung dengan nearest-rank; target tetap 3.000 ms.

## 7. Security retest ST-01–ST-09

```powershell
.\testing\rumahweb-v1.0.0\security\run-security-tests.ps1 -OutputRoot $runDir
```

ID ST tetap mengikuti `TEST_CASES.md`: autentikasi; otorisasi Klien, Admin, dan Staf Legal; session; validasi input; keamanan upload; unggah ulang; lalu konfigurasi keamanan. ST-07 mengirim `.exe` melalui submit awal `/klien/pra-pendaftaran`, memeriksa error validasi, jumlah link perkara/dokumen hasil query marker, dan jumlah blob pada prefix Azure sebelum-sesudah. Setelah runner selesai, ambil marker dari `security/st07-probe-evidence.json`, jalankan `templates/st07-database-check.sql` melalui phpMyAdmin pada `tnym6311_tnyretest`, dan simpan screenshot dua hasil bernilai 0. ST-08 benar-benar mengunggah file pengganti pada fixture perbaikan dan memastikan dokumen lama tetap tersedia. SAS tidak pernah ditulis ke log. ST-09 menyimpan header yang aman; jika `X-Powered-By` ada, hasilnya FAIL sesuai bukti.

Hasil otomatis tetap harus ditinjau manual. Jika pemeriksaan database via phpMyAdmin menunjukkan perubahan yang tidak terwakili UI, catat sebagai temuan dan jangan mengubah hasil agar tampak PASS.

## 8. OWASP ZAP

Setelah izin Active Scan tersedia, jalankan berurutan: spider pada scope publik, tunggu passive scan selesai, lalu Active Scan hanya pada scope publik. Kecualikan `/klien/*`, `/admin/*`, `/staf-legal/*`, `/logout`, endpoint email/password, dan semua operasi mutasi terautentikasi. Simpan Automation Framework plan yang sudah disanitasi, JSON/HTML report, log, versi ZAP, waktu, dan target.

Set `ZAP_PATH` ke `zap.bat`, lalu jalankan:

```powershell
.\testing\rumahweb-v1.0.0\zap\run-zap-public.ps1 -RunDirectory $runDir
.\testing\rumahweb-v1.0.0\zap\summarize-zap.ps1 -RunDirectory $runDir
```

Alert ZAP adalah indikasi awal. Setiap alert yang akan disebut kerentanan harus direproduksi manual dan dipetakan ke source/config aktual. Jangan menjalankan authenticated destructive scan.

## 9. Finalisasi evidence dan DOCX

Struktur minimum run:

```text
testing/retest/<timestamp>-rumahweb-v100/
  authorization/
  release/
  preflight.json
  performance/validation/
  performance/recorded/
  performance/metrics/
  security/
  zap/
  screenshots/
  environment.json
```

Periksa seluruh file agar tidak memuat cookie, CSRF token, password, SAS, API key, atau Authorization header. Jalankan audit otomatis, lalu tinjau manual:

```powershell
.\testing\rumahweb-v1.0.0\scripts\audit-evidence-secrets.ps1 -RunDirectory $runDir
```

Setelah audit lulus dan tidak ada lagi file yang ditambahkan:

```powershell
.\testing\rumahweb-v1.0.0\scripts\finalize-evidence.ps1 -RunDirectory $runDir
```

Baru setelah evidence final, buat salinan `12220093_NAUFAL_FARRAZ_BAB (RETEST RUMAHWEB).docx`. Dokumen asli tidak diubah. Ganti lingkungan, tabel PF, ST, ZAP, ringkasan, dan gambar dengan hasil aktual; jelaskan penyesuaian PF-04 dan bahwa pengujian berlangsung pada clone terisolasi. Render DOCX dan periksa seluruh halaman, caption, tabel, page break, daftar gambar/tabel, serta daftar isi.
