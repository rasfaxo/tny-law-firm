# Eksekusi Retest v1.0.0 pada Azure App Service Staging

Status: **PREPARED / BLOCKED — Azure App Service merespons HTTP 403 Site Disabled pada 9 September 2026**. Tidak ada hasil performa atau keamanan baru sampai staging diaktifkan dan preflight lulus.

## 1. Lingkungan tunggal

- Semua PF-01–PF-07, ST-01–ST-09, dan OWASP ZAP menargetkan hanya `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net`.
- `tnypartners.com`, `www.tnypartners.com`, dan Rumahweb tidak menjadi target serta tidak masuk tabel konfigurasi hasil retest.
- Lingkungan terdiri dari Azure App Service Linux, PHP 8.4, MySQL staging, dan Azure Blob non-production.
- Database, container, akun, dan dokumen production tidak boleh digunakan atau disalin.
- Evidence lama bersifat immutable. Output baru memakai `testing/retest/<timestamp>-azure-v100/`.

## 2. Aktifkan dan isolasi Azure staging

1. Di Azure Portal, buka App Service staging yang sesuai dengan hostname di atas dan ubah status menjadi **Running**.
2. Catat nama App Service plan, SKU, region, jumlah instance, OS, dan PHP runtime melalui screenshot Azure Portal untuk tabel konfigurasi.
3. Buat database MySQL kosong khusus retest dan user dengan hak hanya pada database tersebut. Jangan menjalankan `migrate:fresh`, `migrate:refresh`, `migrate:rollback`, atau `db:wipe`.
4. Pada Azure Storage non-production, buat container private `documents-retest-v100` dan SAS container dengan izin create/read/write/delete/list.
5. Terapkan key dari `testing/azure-v1.0.0/templates/azure-app-settings.template` melalui App Service Configuration. Isi secret hanya di Azure Portal.
6. Gunakan prefix dokumen `retest/v1.0.0/tnypartners` dan readiness `release-gate/retest/v1.0.0/tnypartners`.
7. Gunakan alamat Resend pada domain milik proyek yang telah terverifikasi. Karena host bawaan `azurewebsites.net` bukan domain milik proyek, pemeriksaan production-readiness tentang kesamaan domain pengirim tidak dijadikan gate staging; pengiriman email diverifikasi ke inbox uji yang dikuasai.

## 3. Deploy sumber persis v1.0.0

1. Jalankan workflow `CD Pipeline - Deploy to Azure App Service` dengan ref/tag `v1.0.0`.
2. Workflow harus menunjuk commit `3b0ae8205b6e636df275f008162b25c6c8ec5bd1`; build, test, deploy, dan smoke test harus berhasil.
3. Simpan URL workflow run dan commit tanpa menyimpan publish profile atau secret.
4. Buat satu direktori evidence dan catat deployment:

```powershell
$runDir = Join-Path (Resolve-Path .) ("testing\retest\{0}-azure-v100" -f (Get-Date -Format 'yyyyMMdd-HHmmss'))
$env:AZURE_DEPLOYMENT_COMMIT = '3b0ae8205b6e636df275f008162b25c6c8ec5bd1'
$env:AZURE_WORKFLOW_RUN_URL = '<URL GitHub Actions run>'
.\testing\azure-v1.0.0\scripts\verify-azure-deployment.ps1 -RunDirectory $runDir
```

## 4. Fixture anonim dan konfigurasi lokal

1. Buat satu Admin, satu Staf Legal, dua Klien terverifikasi dengan persetujuan privasi, dan satu kategori retest.
2. Klien B membuat satu perkara untuk ownership ST-06.
3. Klien A membuat perkara lain; Staf Legal menetapkan dokumennya `perlu_perbaikan` dan membuat catatan yang belum diperbaiki untuk ST-08.
4. Catat hanya ID kategori, perkara, dokumen, catatan, dan `file_path`. Jangan menyalin SAS, cookie, CSRF token, atau password ke evidence.
5. Siapkan PDF valid kecil, PDF pengganti, dan file `.exe` inert.
6. Salin `testing/azure-v1.0.0/templates/retest-environment.ps1.example` ke luar repository, isi nilainya, lalu dot-source file tersebut.
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

## 8. Evidence dan DOCX

```powershell
.\testing\azure-v1.0.0\scripts\audit-evidence-secrets.ps1 -RunDirectory $runDir
.\testing\azure-v1.0.0\scripts\finalize-evidence.ps1 -RunDirectory $runDir
```

Evidence minimum mencakup deployment verification, preflight, 12 JTL, 21 baris metrik, laporan JMeter, ST-01–ST-09, verifikasi ST-07, laporan ZAP, screenshot Azure Metrics, dan manifest SHA-256. Setelah manifest dibuat, evidence tidak boleh diubah.

Baru setelah evidence aktual lengkap, buat `12220093_NAUFAL_FARRAZ_BAB (RETEST AZURE V1.0.0).docx`. Seluruh tabel konfigurasi dan hasil hanya menyebut Azure App Service staging. DOCX asli tidak diubah; hasil baru wajib dirender dan diperiksa per halaman sebelum diserahkan.
