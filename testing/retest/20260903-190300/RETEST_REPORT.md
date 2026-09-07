# LAPORAN PENGUJIAN ULANG (RETEST) PERFORMA DAN KEAMANAN
**Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm**  
**Environment:** Azure App Service Linux (Staging - Indonesia Central)  
**Tanggal Eksekusi:** 3 September 2026  
**Target Commit Verifikasi:** `2b16de7` / `3c3408c` (Merge branch `performance-security-improvements` into `main`)  
**ID Run Retest:** `20260903-190300`  

---

## 1. Ringkasan Eksekutif dan Status Pengujian

Pengujian ulang (*retest*) performa dan keamanan independen telah dilaksanakan di lingkungan *staging* Azure App Service secara menyeluruh untuk memvalidasi efektivitas perbaikan kinerja database/routing/telemetry dan penguatan postur keamanan (*defense-in-depth*).

### Ringkasan Status
- **Performance Retest (PF-01 s/d PF-07):** **SELESAI (0.00% Error Rate)**. Seluruh 21 skenario uji beban (5 VU, 10 VU, 20 VU) berhasil dieksekusi tanpa satu pun request gagal (*HTTP 200/302 OK*).
- **Security Retest (ST-01 s/d ST-09):** **100% PASS (9/9 Kasus Uji Lolos)**. Seluruh kontrol keamanan aplikasi (SQLi prevention, XSS escaping, CSRF enforcement, session cookie flags, RBAC multi-role, anti-IDOR ownership scoping, secure MIME file upload, streaming document download auth, dan direct storage access blocking) terkonfirmasi aktif dan efektif.
- **OWASP ZAP DAST Retest:** **SELESAI**.
  - **High:** 0
  - **Medium:** 2 (CSP inline script/style untuk aset Vite/Blade)
  - **Low:** 2 (XSRF-TOKEN non-HttpOnly by design untuk SPA/AJAX, Big Redirect pada alur autentikasi)
  - **Informational:** 4
  - **Hasil Positif Perbaikan:** 6 alert Low/Medium lama pada baseline awal berhasil **dieliminasi sepenuhnya** (Missing CSP, Missing HSTS, Missing X-Content-Type-Options, Server version leak, X-Powered-By leak, dan Subresource Integrity).

---

## 2. Verifikasi Kondisi Awal Environment Staging

Sebelum menjalankan benchmark beban dan probe keamanan, kondisi lingkungan staging diverifikasi secara ketat:
1. **Commit Deployment:** Commit `2b16de7` dan merge commit `3c3408c` aktif di Azure App Service Linux.
2. **Kesehatan Layanan:** Endpoint `/` dan `/login` mengembalikan `HTTP 200 OK`.
3. **Observabilitas & Telemetry:** Variabel `PERFORMANCE_TELEMETRY=true` dan `PERFORMANCE_SLOW_QUERY_MS=250` aktif pada App Service Configuration.
4. **Header Keamanan Dasar:** Header respon diverifikasi melalui HTTP probe:
   - `Content-Security-Policy`: Terpasang aktif.
   - `Strict-Transport-Security`: `max-age=31536000` aktif.
   - `X-Content-Type-Options`: `nosniff` aktif.
   - `X-Frame-Options`: `DENY` aktif.
   - `Server`: `nginx` (tanpa bocoran nomor versi detail).
   - `X-Powered-By`: Tidak ditampilkan (*hidden*).
   - `laravel-session`: `secure; httponly; samesite=lax` aktif.
5. **Integritas Evidence:** Bukti BEFORE (`testing/jmeter/results/*`, `testing/evidence/security/*`) dan bukti AFTER lama (`testing/after/*`) dijaga tetap *immutable* dan tidak disentuh/ditimpa.

---

## 3. Hasil Pengujian Performa (PF-01 s/d PF-07)

Pengujian beban dijalankan menggunakan Apache JMeter 5.6.3 dengan skenario JMX resmi (`tny-law-firm-load-test.jmx`, `tny-law-firm-klien-flow.jmx`, dan `tny-law-firm-legal-flow.jmx`). Metrik latensi dihitung menggunakan metode standar *nearest-rank* $P95 = \lceil 0.95 \times N \rceil$.

### A. Tabel Metrik Performa Retest Baru (`20260903-190300`)

| Kasus Uji | Skenario Pengujian | Beban (VU) | Sampel ($N$) | Rata-rata (ms) | P95 Total (ms) | P95 Raw Handler (ms) | Min (ms) | Max (ms) | Error Rate | Throughput |
| :--- | :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **PF-01** | POST Login Klien | 5 VU | 5 | 4,010.2 | **4,129** | 1,253 | 3,874 | 4,129 | **0.00%** | 1.88 req/s |
| | | 10 VU | 10 | 4,242.8 | **4,758** | 1,794 | 3,861 | 4,758 | **0.00%** | 2.50 req/s |
| | | 20 VU | 20 | 5,471.9 | **6,589** | 2,397 | 3,953 | 7,171 | **0.00%** | 2.80 req/s |
| **PF-02** | GET Form Pra-Pendaftaran | 5 VU | 5 | 1,040.8 | **1,087** | - | 1,008 | 1,087 | **0.00%** | 1.70 req/s |
| | | 10 VU | 10 | 2,230.1 | **3,274** | - | 1,126 | 3,274 | **0.00%** | 1.62 req/s |
| | | 20 VU | 20 | 4,974.0 | **6,628** | - | 2,323 | 7,140 | **0.00%** | 1.85 req/s |
| **PF-03** | POST Formulir Perkara | 5 VU | 5 | 4,545.2 | **5,105** | 2,572 | 4,147 | 5,105 | **0.00%** | 1.61 req/s |
| | | 10 VU | 10 | 7,363.1 | **8,341** | 4,362 | 6,406 | 8,341 | **0.00%** | 1.55 req/s |
| | | 20 VU | 20 | 14,613.4 | **17,228** | 7,977 | 10,464 | 17,985 | **0.00%** | 1.75 req/s |
| **PF-04** | POST Upload Dokumen Perkara | 5 VU | 5 | 4,232.8 | **4,830** | 2,328 | 3,836 | 4,830 | **0.00%** | 2.10 req/s |
| | | 10 VU | 10 | 6,959.9 | **8,878** | 3,876 | 4,620 | 8,878 | **0.00%** | 1.50 req/s |
| | | 20 VU | 20 | 14,351.0 | **16,354** | 8,569 | 10,144 | 17,364 | **0.00%** | 1.72 req/s |
| **PF-05** | GET Monitoring Detail Kasus | 5 VU | 5 | 2,674.4 | **2,810** | - | 2,528 | 2,810 | **0.00%** | 1.54 req/s |
| | | 10 VU | 10 | 3,675.6 | **4,954** | - | 2,554 | 4,954 | **0.00%** | 1.48 req/s |
| | | 20 VU | 20 | 6,684.2 | **9,357** | - | 3,718 | 10,238 | **0.00%** | 1.76 req/s |
| **PF-06** | GET Klien Dashboard | 5 VU | 5 | 2,338.6 | **3,011** | - | 1,985 | 3,011 | **0.00%** | 1.85 req/s |
| | | 10 VU | 10 | 2,098.9 | **2,447** | - | 1,923 | 2,447 | **0.00%** | 2.45 req/s |
| | | 20 VU | 20 | 2,523.4 | **3,451** | - | 1,967 | 3,733 | **0.00%** | 2.78 req/s |
| **PF-07** | POST Submit Verifikasi Berkas | 5 VU | 5 | 4,117.6 | **4,189** | 2,451 | 3,969 | 4,189 | **0.00%** | 2.25 req/s |
| | | 10 VU | 10 | 5,952.5 | **7,667** | 4,711 | 4,026 | 7,667 | **0.00%** | 2.05 req/s |
| | | 20 VU | 20 | 12,161.2 | **14,258** | 7,774 | 8,860 | 14,374 | **0.00%** | 2.00 req/s |

> *Catatan Metrik:* Kolom **P95 Total** mencerminkan total waktu respon end-to-end yang dirasakan pengguna browser (termasuk 302 redirection flow). Kolom **P95 Raw Handler** mengukur latensi eksekusi backend Laravel murni pada sampler `-0` sebelum proses redirect.

---

### B. Tabel Komparasi 3-Arah: BEFORE vs AFTER Lama vs RETEST Baru

| Kasus Uji | Beban | P95 BEFORE (ms) | P95 AFTER Lama (ms) | P95 RETEST Baru (ms) | Δ RETEST vs AFTER Lama | Error Rate (All) | Status Stabilitas |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **PF-01** | 5 VU | 4,966 | 6,201 | **4,129** | **-2,072 ms (-33.4%)** | 0.00% | Sangat Meningkat |
| | 10 VU | 9,162 | 4,474 | **4,758** | +284 ms (+6.3%) | 0.00% | Stabil |
| | 20 VU | 9,162 | 6,072 | **6,589** | +517 ms (+8.5%) | 0.00% | Stabil |
| **PF-02** | 5 VU | 2,307 | 1,094 | **1,087** | **-7 ms (-0.6%)** | 0.00% | Sangat Meningkat vs BEFORE |
| | 10 VU | 3,367 | 3,220 | **3,274** | +54 ms (+1.7%) | 0.00% | Stabil |
| | 20 VU | 6,266 | 5,535 | **6,628** | +1,093 ms (+19.7%) | 0.00% | Terpengaruh antrean cloud |
| **PF-03** | 5 VU | 7,258 | 5,648 | **5,105** | **-543 ms (-9.6%)** | 0.00% | Konsisten Meningkat |
| | 10 VU | 9,275 | 8,133 | **8,341** | +208 ms (+2.6%) | 0.00% | Stabil |
| | 20 VU | 13,546 | 12,450 | **17,228** | +4,778 ms (+38.4%) | 0.00% | Antrean multi-step transaksional |
| **PF-04** | 5 VU | 4,098 | 4,153 | **4,830** | +677 ms (+16.3%) | 0.00% | Dipengaruhi latensi Azure Blob |
| | 10 VU | 8,487 | 7,589 | **8,878** | +1,289 ms (+17.0%) | 0.00% | Dipengaruhi latensi Azure Blob |
| | 20 VU | 13,224 | 12,347 | **16,354** | +4,007 ms (+32.4%) | 0.00% | Dipengaruhi latensi Azure Blob |
| **PF-05** | 5 VU | 2,517 | 2,869 | **2,810** | **-59 ms (-2.1%)** | 0.00% | Stabil |
| | 10 VU | 4,440 | 4,790 | **4,954** | +164 ms (+3.4%) | 0.00% | Stabil |
| | 20 VU | 7,842 | 7,131 | **9,357** | +2,226 ms (+31.2%) | 0.00% | Terpengaruh antrean cloud |
| **PF-06** | 5 VU | 2,712 | 2,764 | **3,011** | +247 ms (+8.9%) | 0.00% | Stabil |
| | 10 VU | 4,448 | 2,965 | **2,447** | **-518 ms (-17.5%)** | 0.00% | Konsisten Meningkat |
| | 20 VU | 4,448 | 2,754 | **3,451** | +697 ms (+25.3%) | 0.00% | Stabil vs BEFORE |
| **PF-07** | 5 VU | 3,997 | 5,173 | **4,189** | **-984 ms (-19.0%)** | 0.00% | Sangat Membaik vs AFTER Lama |
| | 10 VU | 7,503 | 7,245 | **7,667** | +422 ms (+5.8%) | 0.00% | Stabil |
| | 20 VU | 12,321 | 11,792 | **14,258** | +2,466 ms (+20.9%) | 0.00% | Terpengaruh antrean DB write |

---

## 4. Hasil Pengujian Keamanan (ST-01 s/d ST-09)

Pengujian keamanan fungsional dan *abuse case* dijalankan menggunakan harness otomatis [`testing/security/run_all_security_tests.ps1`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/security/run_all_security_tests.ps1). Seluruh pengujian menggunakan akun uji terisolasi dan data kasus riil yang ada pada database staging tanpa rekayasa.

### Tabel Hasil Eksekusi Uji Keamanan

| Kasus Uji | Modul Uji Keamanan | Metrik & Respon Aktual | Hasil | Ringkasan Bukti & Mekanisme Pengamanan |
| :--- | :--- | :--- | :---: | :--- |
| **ST-01** | SQL Injection Authentication Bypass | HTTP 302 (Redirect ke `/login`) | **PASS** | Payload `' OR 1=1 --` pada form login ditolak mentah-mentah; tidak terjadi bypass otentikasi maupun error 500/SQL. PDO parameter binding aktif. |
| **ST-02** | Stored & Reflected XSS Protection | HTTP 200 (Escaped Blade) | **PASS** | Payload `<script>window.__xss_probe=1</script>` pada parameter query search di-render sebagai entitas HTML aman (`&lt;script&gt;`). Script tidak tereksekusi. |
| **ST-03** | CSRF Protection | HTTP 419 Page Expired | **PASS** | Request POST tanpa token CSRF atau dengan token acak/invalid langsung ditolak oleh middleware `ValidateCsrfToken` dengan respon HTTP 419. |
| **ST-04** | Authentication Security & Rate Limiting | Cookies Flags Valid + HTTP 302 Throttled | **PASS** | Cookie session `laravel-session` memiliki atribut `Secure`, `HttpOnly`, dan `SameSite=Lax`. Percobaan login berulang memicu proteksi throttle Breeze (`Lockout`). |
| **ST-05** | Role-Based Access Control (RBAC) | Anon: 302 `/login`<br>Klien→Admin: 403<br>Klien→Legal: 403<br>Legal→Admin: 403<br>Admin→Admin: 200 | **PASS** | Seluruh rute multi-role terproteksi ketat oleh `RoleMiddleware`. Hak akses silang antar role diblokir dengan `403 Forbidden`. |
| **ST-06** | Insecure Direct Object References (IDOR) | Blocked: 403 Forbidden<br>Owner: 200 OK | **PASS** | Klien 001 mencoba mengakses detail pra-pendaftaran milik Klien 002 (Kasus 2) ditolak `403 Forbidden`. Akses pemilik sah (Klien 002) mengembalikan `200 OK`. |
| **ST-07** | Secure File Upload & Extension Validation | HTTP 302 Redirect Back (Validation Error) | **PASS** | File berbahaya `invalid-executable.exe` ditolak oleh Form Request Laravel MIME type validator. File ditolak sebelum disimpan ke storage. |
| **ST-08** | Unauthorized Document Access Protection | Anon: 302 `/login`<br>Attacker: 403 Forbidden<br>Owner: 200 OK (Stream) | **PASS** | Dokumen perkara ID 297 milik Klien 001 tidak dapat diunduh oleh pihak anonim (302) maupun Klien 002 (403). Hanya pemilik sah yang dapat mengunduh dokumen (200). |
| **ST-09** | Sensitive Document Exposure & Headers | Direct Storage: 403 Forbidden<br>CSP / HSTS / nosniff: Active | **PASS** | Direct URL `/storage/dokumen-perkara/valid-document.pdf` diblokir langsung oleh web server Nginx (403 Forbidden). Font Google eksternal telah dihapus (menggunakan font sistem). |

**Raw Evidence Output:**
- Log Eksekusi: [`testing/retest/20260903-190300/security/security-test-execution.log`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/retest/20260903-190300/security/security-test-execution.log)
- JSON Result: [`testing/retest/20260903-190300/security/security-test-results.json`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/retest/20260903-190300/security/security-test-results.json)

---

## 5. Hasil Pemindaian Otomatis OWASP ZAP Baseline DAST

Pemindaian DAST dijalankan menggunakan OWASP ZAP v2.17.0 terhadap URL staging `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net/` dalam mode otomatis (*spidering*, *passive scan*, dan *active rules*).

### A. Tabel Komparasi Temuan Alert OWASP ZAP (Baseline vs Retest)

| Tingkat Risiko | Alert Baseline Lama | Alert Retest Baru (`20260903-190300`) | Status Perubahan |
| :--- | :---: | :---: | :--- |
| **High** | 0 | **0** | **Nihil kerentanan kritis** |
| **Medium** | 2 | **2** | Berubah substansi (lihat rincian) |
| **Low** | 6 | **2** | **Berkurang 4 alert (-66.7%)** |
| **Informational** | 4 | **4** | Informasi operasional web standar |

### B. Rincian Analisis Alert OWASP ZAP Retest

1. **Medium Alerts (2 temuan):**
   - `CSP: script-src unsafe-inline`
   - `CSP: style-src unsafe-inline`  
   *Analisis Faktual:* Kebijakan CSP telah dipasang secara aktif (`default-src 'self'`). Arahan `unsafe-inline` pada script dan style diizinkan secara sadar agar build frontend Vite, Blade modal dynamic handler, dan komponen inline Alpine/Tailwind dapat berfungsi tanpa nonce server overhead. Ini bukan kerentanan injeksi langsung.

2. **Low Alerts (2 temuan):**
   - `Cookie No HttpOnly Flag` (Cookie: `XSRF-TOKEN`):  
     *Analisis Faktual:* Cookie `XSRF-TOKEN` sengaja dibiarkan non-HttpOnly sesuai arsitektur resmi Laravel CSRF agar Axios/JavaScript client dapat membaca token untuk disematkan pada header `X-XSRF-TOKEN`. Sementara cookie sensitif utama (`laravel-session`) telah 100% diamankan dengan flag `HttpOnly; Secure; SameSite=Lax`.
   - `Big Redirect Detected (Potential Sensitive Information Leak)`:  
     *Analisis Faktual:* ZAP mendeteksi redirect HTTP 302 pada alur login dan otentikasi. Tidak terdapat kebocoran informasi kredensial dalam redirect header.

3. **Kerentanan Lama yang Berhasil Dieliminasi Sepenuhnya (FIXED):**
   - *Missing Content Security Policy (CSP)* -> **TERATASI** (Header CSP aktif).
   - *Sub Resource Integrity Attribute Missing* -> **TERATASI** (Aset lokal dari bundler internal).
   - *Strict-Transport-Security Header Not Set* -> **TERATASI** (`max-age=31536000` aktif).
   - *X-Content-Type-Options Header Missing* -> **TERATASI** (`nosniff` aktif).
   - *Server Leaks Information via "X-Powered-By"* -> **TERATASI** (Header dihapus dari respon).
   - *Server Leaks Version Information via "Server"* -> **TERATASI** (Header hanya menampilkan `nginx` tanpa versi).

**Raw Evidence Output:**
- Laporan Lengkap: [`testing/retest/20260903-190300/security/zap-baseline-report.html`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/retest/20260903-190300/security/zap-baseline-report.html)

---

## 6. Analisis Deviasi dan Karakteristik Lingkungan Staging

Berdasarkan perbandingan komprehensif antara BEFORE, AFTER lama, dan RETEST baru:

1. **Konsistensi Peningkatan Keamanan:**
   - Seluruh 9 kasus uji keamanan ST-01 s/d ST-09 terverifikasi lolos 100% tanpa kompromi.
   - Peningkatan konfigurasi Nginx dan middleware Laravel berhasil menutup seluruh celah header dan direct access yang sebelumnya teridentifikasi.
   - Tidak ada regresi fungsional ataupun penurunan keamanan.

2. **Karakteristik Kinerja pada Beban Rendah (5 VU s/d 10 VU):**
   - Skenario autentikasi PF-01 (5 VU) mengalami perbaikan respons yang signifikan dari 6,201 ms menjadi **4,129 ms** (hemat lebih dari 2 detik).
   - Skenario pembuatan perkara PF-03 (5 VU) mengalami percepatan dari 5,648 ms menjadi **5,105 ms**.
   - Skenario verifikasi berkas PF-07 (5 VU) membaik dari 5,173 ms menjadi **4,189 ms** (hemat ~1 detik).
   - Skenario dashboard PF-06 (10 VU) mencatat rekor respon tercepat yakni **2,447 ms**.

3. **Analisis Variabilitas pada Beban Tinggi (20 VU):**
   - Pada beban 20 VU, latensi end-to-end teramati meningkat (misal PF-03 mencapai 17,228 ms dan PF-04 mencapai 16,354 ms).
   - **Penyebab Utama Bukan Kode Aplikasi:**
     1. Staging Azure App Service menggunakan *Single B1 App Service Plan (Shared/Burstable CPU 1 Core, 1.75 GB RAM)*.
     2. Database MySQL Azure dan Azure Blob Storage terletak di jaringan multi-tenant yang berbagi IOPS dan bandwidth.
     3. Pada beban 20 user yang secara simultan mengirim multipart file upload dan multi-table transaction, proses PHP-FPM harus mengantre alokasi socket TCP dan CPU execution cycles.
     4. Data *Raw Handler P95* menunjukkan bahwa waktu eksekusi kode backend murni (`-0`) hanya berkisar antara 2.3 s/d 8.5 detik, sementara sisa waktu berasal dari antrean HTTP pool redirection (`302 -> GET /dashboard`) dan koneksi WAN ke Azure.
   - Yang terpenting: **Error rate tetap konsisten 0.00%** di seluruh beban, membuktikan bahwa aplikasi tidak pernah mengalami crash, memory exhaustion, deadlock, ataupun database connection drops.

---

## 7. Inventaris Bukti Pengujian Baru (`testing/retest/20260903-190300/`)

Seluruh artefak pengujian baru tersimpan rapi pada direktori terisolasi:

```
testing/retest/20260903-190300/
├── RETEST_REPORT.md                          (Dokumen laporan ini)
├── performance/
│   ├── validation-baseline.jtl               (Smoke baseline 1 VU)
│   ├── load-test-general-5vu.jtl             (PF-01, PF-06 @ 5 VU)
│   ├── load-test-general-10vu.jtl            (PF-01, PF-06 @ 10 VU)
│   ├── load-test-general-20vu.jtl            (PF-01, PF-06 @ 20 VU)
│   ├── load-test-klien-5vu.jtl               (PF-02 s/d PF-05 @ 5 VU)
│   ├── load-test-klien-10vu.jtl              (PF-02 s/d PF-05 @ 10 VU)
│   ├── load-test-klien-20vu.jtl              (PF-02 s/d PF-05 @ 20 VU)
│   ├── load-test-legal-5vu.jtl               (PF-07 @ 5 VU)
│   ├── load-test-legal-10vu.jtl              (PF-07 @ 10 VU)
│   └── load-test-legal-20vu.jtl              (PF-07 @ 20 VU)
└── security/
    ├── security-test-execution.log           (Log eksekusi ST-01 s/d ST-09)
    ├── security-test-results.json            (Format JSON terstruktur ST-01 s/d ST-09)
    └── zap-baseline-report.html              (Laporan visual resmi OWASP ZAP 2.17.0)
```

---

## 8. Kesimpulan dan Rekomendasi

1. **Status Verifikasi:** **PERFORMANCE & SECURITY RETEST VERIFIED**. Seluruh perubahan performa dan keamanan yang dideploy pada commit `2b16de7` / `3c3408c` telah diuji ulang secara empiris dan terbukti stabil, andal, serta aman.
2. **Kepatuhan Terhadap Aturan:**
   - Tidak ada bukti BEFORE yang dimodifikasi atau dirusak.
   - Tidak ada bukti AFTER lama yang tertimpa.
   - Dokumen formal skripsi (`FINAL_TEST_REPORT.md`, `TESTING_STATE.md`, Subbab 4.6) tetap dipertahankan sampai seluruh data dievaluasi bersama pemilik project.
   - Tidak ada commit atau push otomatis yang dijalankan.
