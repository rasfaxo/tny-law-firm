# Eksekusi Retest v1.0.0 pada Azure App Service Staging

Status: **FIXTURE SEEDER READY / DEPLOYMENT HELPER PENDING**. Workflow run `34373256128` dan job `102539747241` berhasil pada 9 September 2026 menggunakan tag `v1.0.0` dan commit `3b0ae8205b6e636df275f008162b25c6c8ec5bd1`. Preflight awal dari Jakarta memperoleh HTTP 200 pada `/`, `/up`, dan `/login`, memverifikasi DNS target, serta memastikan executable JMeter/ZAP tersedia. Karena helper seeder akan dipasang sementara, deployment verification dan preflight wajib diulang setelah tag v1.0.0 dideploy kembali. Hasil performa dan keamanan belum dibuat.

## 1. Lingkungan tunggal

- Semua PF-01–PF-07, ST-01–ST-09, dan OWASP ZAP menargetkan hanya `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net`.
- Penyimpanan dokumen hanya menggunakan akun staging `tnylawfirmstorage` dengan endpoint `https://tnylawfirmstorage.blob.core.windows.net/`; akun production `tnylawfirmdocs` tidak boleh digunakan.
- `tnypartners.com`, `www.tnypartners.com`, dan Rumahweb tidak menjadi target serta tidak masuk tabel konfigurasi hasil retest.
- App Service menggunakan tier Free F1 sehingga custom domain tidak didukung. Hostname bawaan Azure menjadi satu-satunya target retest dan sudah dilindungi HTTPS bawaan Azure.
- Lingkungan terdiri dari Azure App Service Linux, PHP 8.4, MySQL staging, dan Azure Blob non-production.
- Database, container, akun, dan dokumen production tidak boleh digunakan atau disalin.
- Evidence lama bersifat immutable. Output baru memakai `testing/retest/<timestamp>-azure-v100/`.

## 2. Aktifkan dan isolasi Azure staging

1. Pastikan App Service staging yang sesuai dengan hostname di atas berstatus **Running**.
2. Catat nama App Service plan, SKU, region, jumlah instance, OS, dan PHP runtime melalui screenshot Azure Portal untuk tabel konfigurasi.
3. Buat database MySQL kosong khusus retest dan user dengan hak hanya pada database tersebut. Jangan menjalankan `migrate:fresh`, `migrate:refresh`, `migrate:rollback`, atau `db:wipe`.
4. Gunakan container private yang sudah ada, `documents`. Data retest wajib terisolasi pada prefix `retest/v1.0.0/tnypartners`; operasi pengujian tidak boleh menyentuh path di luar prefix tersebut. Koneksi aplikasi harus mempunyai izin create/read/write/delete/list.
5. Terapkan key dari `testing/azure-v1.0.0/templates/azure-app-settings.template` melalui App Service Configuration. Isi secret hanya di Azure Portal.
6. Gunakan prefix dokumen `retest/v1.0.0/tnypartners` dan readiness `release-gate/retest/v1.0.0/tnypartners`.
7. Gunakan alamat Resend pada domain milik proyek yang telah terverifikasi. Karena host bawaan `azurewebsites.net` bukan domain milik proyek, pemeriksaan production-readiness tentang kesamaan domain pengirim tidak dijadikan gate staging; pengiriman email diverifikasi ke inbox uji yang dikuasai.

## 3. Buat fixture lalu kembalikan source persis v1.0.0

1. Ikuti `testing/azure-v1.0.0/FIXTURE_SETUP.md`: aktifkan tiga App Settings `RETEST_FIXTURES_*`, deploy branch helper, dan pastikan seeder menghasilkan fixture beserta lima ID.
2. Hapus ketiga App Settings helper segera setelah seeding selesai.
3. Deploy kembali tag `v1.0.0` yang menunjuk commit `3b0ae8205b6e636df275f008162b25c6c8ec5bd1`.
4. Workflow final harus menyelesaikan build, deploy, dan smoke test. Gunakan URL run final baru sebagai bukti deployment aktif; run `34373256128` hanya menjadi bukti kondisi sebelum helper.
5. Buat satu direktori evidence dan catat deployment final:

```powershell
$runDir = Join-Path (Resolve-Path .) ("testing\retest\{0}-azure-v100" -f (Get-Date -Format 'yyyyMMdd-HHmmss'))
$env:AZURE_DEPLOYMENT_COMMIT = '3b0ae8205b6e636df275f008162b25c6c8ec5bd1'
$env:AZURE_WORKFLOW_RUN_URL = '<URL GitHub Actions run final setelah helper>'
.\testing\azure-v1.0.0\scripts\verify-azure-deployment.ps1 -RunDirectory $runDir
```

## 4. Fixture anonim dan konfigurasi lokal

Ikuti checklist lengkap `testing/azure-v1.0.0/FIXTURE_SETUP.md`. Ringkasannya:

1. `RetestFixtureSeeder` membuat satu Admin, satu Staf Legal, dua Klien terverifikasi beserta consent, kategori, perkara ownership, perkara perbaikan, metadata dokumen, dan dua blob anonim.
2. Seeder memakai satu password dari `RETEST_FIXTURE_PASSWORD`; nilainya tidak berada di source atau log.
3. Salin lima ID non-rahasia dari output seeder. Jika output tidak tersedia, gunakan query read-only `templates/fixture-lookup.sql`.
4. Siapkan PDF valid kecil, PDF pengganti, dan file `.exe` inert untuk eksekusi runner.
5. Salin `testing/azure-v1.0.0/templates/retest-environment.ps1.example` ke luar repository, isi satu password fixture, lima ID, serta SAS read/list sementara, lalu dot-source file tersebut.
6. Verifikasi login keempat akun dan state fixture setelah deployment final v1.0.0.
7. Gunakan `RETEST_OWNER_AUTHORIZATION=project-owner-approved-20260909` sebagai catatan otorisasi pemilik.

## 5. Preflight wajib

```powershell
.\testing\azure-v1.0.0\scripts\validate-harness.ps1
.\testing\azure-v1.0.0\scripts\preflight-retest.ps1 -RunDirectory $runDir
```

Preflight memeriksa target tunggal, evidence deployment v1.0.0, DNS, TLS, respons dan marker `/`, `/up`, `/login`, serta lokasi JMeter/ZAP. HTTP 403 Site Disabled, kesalahan marker, atau commit berbeda menghentikan eksekusi.

## 6. Performance PF-01–PF-07

```powershell
.\testing\azure-v1.0.0\scripts\run-performance.ps1 -RunDirectory $runDir
.\testing\azure-v1.0.0\scripts\summarize-performance.ps1 -RunDirectory $runDir
```

Runner menjalankan validation 1 VU per flow, lalu 12 recorded run 5/10/20 VU dengan loop 1. General memakai ramp 5/5, 10/10, 20/20 detik; klien, perbaikan, dan legal memakai 5/5, 10/10, 20/10 detik. Jeda 65 detik memisahkan throttle window. PF-04 memakai endpoint unggah ulang v1.0.0. Semua HTTP 429/error tetap masuk evidence. Ringkasan harus berisi tepat 21 baris dan memisahkan status eksekusi dari target P95 3.000 ms.

Selama setiap recorded run, simpan screenshot Azure Metrics untuk CPU Time, Average Response Time, Requests, HTTP 4xx/5xx, Memory Working Set, dan Data In/Out pada rentang waktu yang sama.

## 7. Security dan ZAP

```powershell
.\testing\azure-v1.0.0\security\run-security-tests.ps1 -OutputRoot $runDir
.\testing\azure-v1.0.0\zap\run-zap-public.ps1 -RunDirectory $runDir
.\testing\azure-v1.0.0\zap\summarize-zap.ps1 -RunDirectory $runDir
```

- ST-01–ST-09 mengikuti spesifikasi terkunci.
- ST-07 membandingkan UI/database dan daftar blob sebelum-sesudah `.exe`; query manual memakai `templates/st07-database-check.sql` pada database staging.
- ST-08 memastikan dokumen lama tetap ada setelah unggah ulang.
- ST-09 mencatat header apa adanya; `X-Powered-By` menghasilkan temuan/FAIL.
- ZAP hanya mencakup halaman publik, tanpa authenticated destructive scan.
- Alert otomatis diverifikasi manual sebelum dinyatakan sebagai kerentanan.
- Pengiriman/verifikasi email tidak termasuk PF-01–PF-07 maupun ST-01–ST-09. Jika Resend/queue worker staging tidak tersedia, fitur tersebut dicatat `NOT EXECUTED` dan tidak diberi status PASS/FAIL berdasarkan asumsi.

## 8. Evidence dan DOCX

```powershell
.\testing\azure-v1.0.0\scripts\audit-evidence-secrets.ps1 -RunDirectory $runDir
.\testing\azure-v1.0.0\scripts\finalize-evidence.ps1 -RunDirectory $runDir
```

Evidence minimum mencakup deployment verification, preflight, 12 JTL, 21 baris metrik, laporan JMeter, ST-01–ST-09, verifikasi ST-07, laporan ZAP, screenshot Azure Metrics, dan manifest SHA-256. Setelah manifest dibuat, evidence tidak boleh diubah.

Baru setelah evidence aktual lengkap, buat `12220093_NAUFAL_FARRAZ_BAB (RETEST AZURE V1.0.0).docx`. Seluruh tabel konfigurasi dan hasil hanya menyebut Azure App Service staging. DOCX asli tidak diubah; hasil baru wajib dirender dan diperiksa per halaman sebelum diserahkan.
