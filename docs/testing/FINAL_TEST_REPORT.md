# Laporan Akhir Pengujian Sistem (Final Test Report)
## Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm

**Dokumen:** Laporan Akhir Pengujian Sistem (*Final Test Report*)  
**Versi:** 1.1 (Final Corrected)  
**Tanggal Penyusunan:** 31 Agustus 2026 (Updated 2 September 2026)  
**Aplikasi Sasaran:** TNY Law Firm (*Staging Environment*)  
**URL Aplikasi:** `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net`  
**Metodologi Pengujian:** *Performance Testing* (Apache JMeter) & *Security Testing* (OWASP WSTG, OWASP ZAP, *Manual/Probe Verification*)  
**Status Pengujian:** **100% COMPLETED (16/16 TEST CASES EXECUTED)**

---

# 1. Ringkasan Eksekutif (Executive Summary)

Pengujian sistem pada aplikasi **Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm** telah selesai dilaksanakan secara menyeluruh pada lingkungan *Staging*. Pengujian ini bertujuan untuk memvalidasi kinerja sistem di bawah beban concurrent users (*Performance Testing*) serta mengevaluasi mekanisme keamanan aplikasi (*Security Testing*) sesuai dengan rancangan skripsi.

Berdasarkan seluruh tahapan pengujian yang telah dieksekusi:
- **Total Test Cases:** 16 Kasus Uji (7 Skenario Kinerja + 9 Skenario Keamanan).
- **Hasil Eksekusi:** **All 16 test cases within the defined Performance and Security testing scope received PASS status.**
- **Error Rate Kinerja:** **0.00%** di seluruh tingkatan beban pengujian (*5 VU, 10 VU, dan 20 VU*) pada eksekusi log yang dilaporkan.
- **Temuan Kerentanan Kritis:** **OWASP ZAP assessment did not identify High/Critical alerts within the tested scope.**

Dengan hasil ini, pengujian sistem untuk aplikasi **Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm** dinyatakan ready to be used as evidence for thesis evaluation.

---

# 2. Lingkungan & Konfigurasi Pengujian (Test Environment)

Pengujian dilakukan pada lingkungan yang terisolasi dan mencerminkan arsitektur target *staging*:

| Komponen / Parameter | Spesifikasi / Konfigurasi Aktual | Status |
| :--- | :--- | :---: |
| **Application Target** | `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net` | VERIFIED |
| **Environment Type** | Staging Cloud Server (Azure App Service Linux - Standard B1) | VERIFIED |
| **Web & App Server** | Nginx Reverse Proxy + PHP 8.2-FPM (Linux Container) | VERIFIED |
| **Database Server** | MySQL | VERIFIED |
| **Storage Subsystem** | Azure Blob Storage through the configured Laravel document disk | VERIFIED |
| **Performance Tool** | Apache JMeter versi 5.6.3 (*Distributed Thread Groups, CSV Dataset, Assertion Listeners*) | VERIFIED |
| **Security Testing Tool** | OWASP ZAP (Zed Attack Proxy) versi 2.17.0 (*Active & Passive Scanners*) & PowerShell Security Test Harness | VERIFIED |
| **Browser Testing Client**| Chromium-based Browser (Google Chrome / Microsoft Edge) & Headless HTTP Client | VERIFIED |

### Akun Pengujian yang Digunakan (Isolated Test Accounts)
- **Role Klien:** `client001@tny.test` (User ID Terisolasi)
- **Role Staf Legal:** `legal1.testing@tny.test` (User ID Terisolasi)
- **Role Admin:** `admin.testing@tny.test` (User ID Terisolasi)

---

# 3. Metodologi Pengujian (Testing Methodology)

### 3.1 Performance Testing (Pengujian Kinerja)
Pengujian kinerja dilakukan menggunakan **Apache JMeter** dengan skenario *Progressive Load Testing*. Baseline execution with one virtual user was used to validate request flow, session, authentication, and CSRF handling before progressive load execution. Comparative performance analysis uses 5, 10, and 20 virtual users:
1. **Progressive Load 5 VU (5 Concurrent Virtual Users)**: Menguji beban kerja normal harian.
2. **Progressive Load 10 VU (10 Concurrent Virtual Users)**: Menguji beban kerja menengah saat jam sibuk.
3. **Progressive Load 20 VU (20 Concurrent Virtual Users)**: Menguji kapasitas puncak pengajuan perkara dan verifikasi berkas secara simultan.

**Metrik yang Diukur:**
- *Average Response Time (ms)*
- *Minimum & Maximum Response Time (ms)*
- *Throughput / Request per Second (RPS)*
- *Error Rate (%)*

### 3.2 Security Testing (Pengujian Keamanan)
Pengujian keamanan dilakukan dengan mengacu pada standar **OWASP Web Security Testing Guide (WSTG)** dan **DAST Automated Scanning (OWASP ZAP)** yang mencakup:
- *Authentication & Session Management* (Pencegahan bypass SQLi, manajemen cookie `HttpOnly`/`SameSite`, proteksi CSRF, dan Rate Limiting).
- *Role-Based Access Control (RBAC) & Authorization* (Isolasi hak akses Klien, Admin, dan Staf Legal).
- *Input Validation & Data Sanitization* (Pencegahan Cross-Site Scripting / XSS dan Insecure Direct Object References / IDOR).
- *File Upload & Storage Security* (Validasi MIME type/ekstensi, random hashing filename).

---

# 4. Hasil Pelaksanaan Pengujian Kinerja Awal (Performance Testing BEFORE)

Pengujian kinerja mencakup seluruh modul operasional utama (PF-01 s/d PF-07):

### 4.1 Rekapitulasi Kasus Uji Kinerja

| ID | Skenario Pengujian | Target Modul / Endpoint | Hasil Pengujian Aktual | Status |
| :--- | :--- | :--- | :--- | :---: |
| **PF-01** | Login & Autentikasi | `POST /login` | Request diproses sesuai expected behavior; parsing CSRF dan pembuatan session berhasil. | **PASS** |
| **PF-02** | Akses Form Pra-Pendaftaran | `GET /klien/pra-pendaftaran/create` | Halaman form beserta data kategori perkara dirender. | **PASS** |
| **PF-03** | Pengisian & Submit Formulir | `POST /klien/pra-pendaftaran` | Transaksi database (`pra_pendaftaran_perkara`, `riwayat_status`) selesai tanpa kegagalan fungsional. | **PASS** |
| **PF-04** | Upload Dokumen Pendukung | `POST /klien/pra-pendaftaran (multipart)` | Unggah file dummy PDF/JPG diproses. | **PASS** |
| **PF-05** | Monitoring Status Pengajuan | `GET /klien/pengajuan/{id}` | Riwayat status perkara dan catatan verifikasi dimuat. | **PASS** |
| **PF-06** | Akses Data Pra-Pendaftaran | `GET /klien/dashboard` | Query relasi data perkara pengguna diproses. | **PASS** |
| **PF-07** | Verifikasi Berkas Staf Legal | `POST /staf-legal/verifikasi-berkas/{id}` | Pembaruan status perkara, pencatatan verifikasi, dan riwayat status diproses. | **PASS** |

### 4.2 Tabel Analisis Metrik Kinerja Awal (Progressive Load 5 VU, 10 VU, 20 VU)

| Test Case | Virtual Users | Total Requests | Error Rate (%) | Average Response Time (ms) | Min (ms) | Max (ms) | Throughput (req/sec) |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: | :---: |
| **PF-01 (Login)** | 5 VU | 5 | 0.00% | 1,264 ms | 1,180 ms | 1,350 ms | 1.82 /s |
| | 10 VU | 10 | 0.00% | 1,385 ms | 1,190 ms | 1,590 ms | 2.65 /s |
| | 20 VU | 20 | 0.00% | 1,892 ms | 1,210 ms | 2,840 ms | 3.48 /s |
| **PF-02 (Akses Form)** | 5 VU | 5 | 0.00% | 1,852 ms | 1,620 ms | 2,110 ms | 1.45 /s |
| | 10 VU | 10 | 0.00% | 2,340 ms | 1,750 ms | 3,120 ms | 1.88 /s |
| | 20 VU | 20 | 0.00% | 4,095 ms | 2,100 ms | 5,420 ms | 2.15 /s |
| **PF-03 (Submit Form)** | 5 VU | 5 | 0.00% | 3,421 ms | 3,100 ms | 3,850 ms | 0.95 /s |
| | 10 VU | 10 | 0.00% | 3,745 ms | 3,210 ms | 4,320 ms | 1.25 /s |
| | 20 VU | 20 | 0.00% | 5,112 ms | 3,450 ms | 6,890 ms | 1.62 /s |
| **PF-04 (Upload Berkas)**| 5 VU | 5 | 0.00% | 1,520 ms | 1,380 ms | 1,750 ms | 1.65 /s |
| | 10 VU | 10 | 0.00% | 2,680 ms | 1,850 ms | 3,420 ms | 1.85 /s |
| | 20 VU | 20 | 0.00% | 4,705 ms | 2,210 ms | 6,120 ms | 2.05 /s |
| **PF-05 (Monitoring)** | 5 VU | 5 | 0.00% | 2,504 ms | 2,120 ms | 2,890 ms | 1.15 /s |
| | 10 VU | 10 | 0.00% | 3,150 ms | 2,450 ms | 4,120 ms | 1.55 /s |
| | 20 VU | 20 | 0.00% | 5,410 ms | 2,890 ms | 7,150 ms | 1.82 /s |
| **PF-06 (Dashboard)** | 5 VU | 5 | 0.00% | 2,501 ms | 2,210 ms | 2,820 ms | 1.20 /s |
| | 10 VU | 10 | 0.00% | 2,890 ms | 2,340 ms | 3,560 ms | 1.60 /s |
| | 20 VU | 20 | 0.00% | 3,682 ms | 2,650 ms | 4,920 ms | 2.10 /s |
| **PF-07 (Verifikasi)** | 5 VU | 5 | 0.00% | 2,298 ms | 2,050 ms | 2,610 ms | 1.28 /s |
| | 10 VU | 10 | 0.00% | 3,550 ms | 2,410 ms | 4,720 ms | 1.52 /s |
| | 20 VU | 20 | 0.00% | 5,780 ms | 2,950 ms | 7,890 ms | 1.75 /s |

---

# 5. Hasil Pelaksanaan Pengujian Keamanan Awal (Security Testing BEFORE)

Pengujian keamanan mencakup 9 kasus uji spesifik (ST-01 s/d ST-09) dan pemindaian otomatis OWASP ZAP:

### 5.1 Rekapitulasi Kasus Uji Keamanan

| ID | Fokus Keamanan | Metode & Pengujian | Hasil Pengujian Aktual | Status |
| :--- | :--- | :--- | :--- | :---: |
| **ST-01** | **Autentikasi & SQLi Prevention** | Injeksi payload SQL (`' OR '1'='1' --`) pada form autentikasi | Ditolak oleh Eloquent PDO Parameter Binding; tidak terjadi bypass otentikasi. | **PASS** |
| **ST-02** | **Otorisasi Role Klien** | Percobaan akses direct URL Klien ke route Admin & Staf Legal | Klien mengakses data miliknya; direct access ke area Admin (`/admin/users`) dan Staf Legal diblokir `HTTP 403`. | **PASS** |
| **ST-03** | **Otorisasi Role Admin** | Proteksi rute administratif dan verifikasi akses unauthenticated | Request unauthenticated dialihkan ke halaman login `HTTP 302`. | **PASS** |
| **ST-04** | **Otorisasi Role Staf Legal**| Hak akses verifikasi perkara dan isolasi manajemen user | Staf Legal mengakses modul verifikasi; direct access ke modul Admin diblokir `HTTP 403`. | **PASS** |
| **ST-05** | **Session & CSRF Management** | Uji cookie flags, request tanpa token CSRF, dan brute force | Request tanpa CSRF token menghasilkan `HTTP 419 Page Expired`. Rate Limiter aktif. | **PASS** |
| **ST-06** | **Input Validation, XSS & IDOR** | Injeksi script XSS (`<script>alert(1)</script>`) & IDOR probe | Payload XSS di-escape otomatis oleh Blade. Akses IDOR lintas user diblokir Policy (`HTTP 403/404`). | **PASS** |
| **ST-07** | **File Upload Security** | Upload ekstensi berbahaya (`shell.php`, `.exe`, MIME bypass) | File non-whitelisted ditolak validator. File valid disimpan dengan random filename. | **PASS** |
| **ST-08** | **Document Re-upload Integrity** | Uji unggah ulang pada berbagai status perkara | Unggah ulang berkas hanya diizinkan pada status `perlu_perbaikan`. File lama dipertahankan terpisah. | **PASS** |
| **ST-09** | **Security Config & DAST Scan** | *Directory browsing check* & pemindaian otomatis OWASP ZAP | OWASP ZAP assessment did not identify High/Critical alerts within the tested scope. | **PASS** |

### 5.2 Rangkuman Pemindaian DAST OWASP ZAP Awal
- **Target URL:** `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net`
- **Tingkat Kerentanan Tinggi (High):** **0**
- **Tingkat Kerentanan Sedang (Medium):** **0**
- **Tingkat Kerentanan Rendah (Low):** **0**
- **Informational / Passive Review Alerts:** **39**

---

# 6. Analisis Temuan Awal (Phase 6 Analysis)

Berdasarkan data eksekusi riil pada tahap pengujian awal:
- **Application Bugs:** No defect causing failure was identified within the 16 defined test cases.
- **Security Vulnerabilities:** None identified within tested scope.
- **Severity & CVSS:** N/A

---

# 7. Laporan Peningkatan & Pengujian Ulang (Improvement & Retest Report)

## 7.1 Peningkatan yang Diimplementasikan (Improvements Implemented)

ID: **PERF-IMP-01**
Finding: Application Startup Timeout & Logging Insufficiency.
Evidence: GitHub Actions CI/CD pipeline smoke tests were failing with `HTTP 404` due to aggressive timeouts.
Root Cause: Azure App Service Linux container requires more than 20 seconds to fully initialize and bind Nginx to the PHP-FPM socket on cold starts.
Files Modified: 
- `.github/workflows/cd.yml`
- `startup.sh`
Classification: **Staging startup/reliability improvement**
Implementation: 
- Increased wait time to 40 seconds.
- Added extensive diagnostic curl checks and retry limits.
- Included `cache:clear` dan `config:cache` in the startup script to optimize runtime execution environment.
Security Improvement: No direct security remediation was required based on the confirmed findings.

## 7.2 Retest (Pengujian Ulang - Phase 7)
Status: **COMPLETED**
Retest dilakukan setelah implementation/deployment improvement dengan methodology dan test case yang sama untuk memperoleh AFTER result. Semua 16 test cases dieksekusi ulang di environment Staging.

## 7.3 Regression Verification (Phase 8)
Status: **COMPLETED**
No functional regression was identified within the tested flows. However, performance regression was observed in several scenarios. All HTTP responses for tested paths returned expected HTTP codes with 0.00% error rate during concurrent load testing.
- Login and Authentication: Intact.
- File Upload/Storage: Intact.
- Verification workflows: Intact.

---

# 8. Analisis Kinerja & Keamanan Pasca-Perbaikan (Before vs After Analysis)

## 8.1 Performance Before vs After

*Comparative performance analysis comparing Initial (BEFORE) baseline against Retest (AFTER) raw JTL executions. Error rate remained 0.00% for reported JMeter executions.*

| ID | Load | Before Avg (ms) | After Avg (ms) | Difference (ms) | Error Before | Error After |
| :--- | :---: | :---: | :---: | :---: | :---: | :---: |
| **PF-01** | 5 VU | 1264 | 4484 | +3220 | 0.00% | 0.00% |
| | 10 VU | 1385 | 5774 | +4389 | 0.00% | 0.00% |
| | 20 VU | 1892 | 10564 | +8672 | 0.00% | 0.00% |
| **PF-02** | 5 VU | 1852 | 1065 | -787 | 0.00% | 0.00% |
| | 10 VU | 2340 | 2080 | -260 | 0.00% | 0.00% |
| | 20 VU | 4095 | 3689 | -406 | 0.00% | 0.00% |
| **PF-03** | 5 VU | 3421 | 5193 | +1772 | 0.00% | 0.00% |
| | 10 VU | 3745 | 7312 | +3567 | 0.00% | 0.00% |
| | 20 VU | 5112 | 10182 | +5070 | 0.00% | 0.00% |
| **PF-04** | 5 VU | 1520 | 3975 | +2455 | 0.00% | 0.00% |
| | 10 VU | 2680 | 6307 | +3627 | 0.00% | 0.00% |
| | 20 VU | 4705 | 10066 | +5361 | 0.00% | 0.00% |
| **PF-05** | 5 VU | 2504 | 2645 | +141 | 0.00% | 0.00% |
| | 10 VU | 3150 | 3297 | +147 | 0.00% | 0.00% |
| | 20 VU | 5410 | 5208 | -202 | 0.00% | 0.00% |
| **PF-06** | 5 VU | 2501 | 2463 | -38 | 0.00% | 0.00% |
| | 10 VU | 2890 | 2027 | -863 | 0.00% | 0.00% |
| | 20 VU | 3682 | 2214 | -1468 | 0.00% | 0.00% |
| **PF-07** | 5 VU | 2298 | 4660 | +2362 | 0.00% | 0.00% |
| | 10 VU | 3550 | 5747 | +2197 | 0.00% | 0.00% |
| | 20 VU | 5780 | 9301 | +3521 | 0.00% | 0.00% |

## 8.2 Security Before vs After

ST-01–ST-09 remained PASS within tested scope. Security Retest Regression verification completed successfully without introducing any new vulnerabilities. OWASP ZAP assessment did not identify High/Critical alerts.

---

# 9. Remaining Issues / Limitations

- Performance variability/regression remains observable on PF-01, PF-03, PF-04, and PF-07.
- The available JMeter evidence is sufficient to establish the observed regression, but not sufficient to conclusively attribute the root cause to network latency or cold start.
- Network latency or Azure cold start effects may act as a possible contributing factor, but the root cause remains unconfirmed without deeper server-side instrumentation.

---

# 10. Indeks Bukti Pengujian (Evidence Index)

1. **Bukti Pengujian Kinerja (JMeter)**:
   - JMX Test Plan: [`testing/jmeter/tny-law-firm-load-test.jmx`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/jmeter/tny-law-firm-load-test.jmx)
   - Raw JTL Results (BEFORE): `testing/jmeter/results/load-test-{5,10,20}vu.jtl`
   - Raw JTL Results (AFTER): `testing/after/performance/load-test-{5,10,20}vu-after.jtl`
2. **Bukti Pengujian Keamanan (Security)**:
   - Execution Logs: [`testing/evidence/security/security-test-execution.log`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/evidence/security/security-test-execution.log) (BEFORE) dan `testing/after/security/security-test-execution.log` (AFTER).
   - Laporan DAST OWASP ZAP: [`testing/evidence/security/zap-baseline-report.html`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/evidence/security/zap-baseline-report.html) (BEFORE) dan `testing/after/security/zap-baseline-report.html` (AFTER).
3. **Dokumentasi Spesifikasi**:
   - Master Test Plan: [`docs/testing/TEST_PLAN.md`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/docs/testing/TEST_PLAN.md)
   - Test Cases Matrix: [`docs/testing/TEST_CASES.md`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/docs/testing/TEST_CASES.md)
   - Testing State Tracker: [`docs/testing/TESTING_STATE.md`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/docs/testing/TESTING_STATE.md)

---

# 11. Kesimpulan Akhir (Final Conclusion)

1. All 16 test cases within the defined Performance and Security testing scope were executed successfully.
2. Initial performance and security testing was completed establishing a functional baseline.
3. Post-improvement deployments (startup reliability optimizations) and complete Retesting procedures were fully executed.
4. Functional behavior remained intact within all verified flows. Error rates remained 0.00% for all reported JMeter executions.
5. Security tests remained PASS within the tested scope. OWASP ZAP and manual probes did not identify any confirmed High/Critical security finding.
6. The performance AFTER produced mixed results. Some scenarios improved (e.g., PF-02 and PF-06), while other scenarios experienced performance regression (e.g., PF-01, PF-03, PF-04, PF-07).
7. Due to the observed performance regression alongside functional stability, the implemented improvements are classified as **IMPROVEMENT PARTIALLY VERIFIED**.

Status Akhir: **READY TO BE USED AS EVIDENCE FOR THESIS EVALUATION**.
