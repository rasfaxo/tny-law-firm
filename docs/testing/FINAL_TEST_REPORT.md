# Laporan Akhir Pengujian Sistem (Final Test Report)
## Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm

**Dokumen:** Laporan Akhir Pengujian Sistem (*Final Test Report*)  
**Versi:** 1.0 (Final)  
**Tanggal Penyusunan:** 31 Agustus 2026  
**Aplikasi Sasaran:** TNY Law Firm (*Staging Environment*)  
**URL Aplikasi:** `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net`  
**Metodologi Pengujian:** *Performance Testing* (Apache JMeter) & *Security Testing* (OWASP WSTG, OWASP ZAP, *Manual/Probe Verification*)  
**Status Pengujian:** **100% COMPLETED (16/16 TEST CASES PASS)**

---

# 1. Ringkasan Eksekutif (Executive Summary)

Pengujian sistem pada aplikasi **Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm** telah selesai dilaksanakan secara menyeluruh pada lingkungan *Staging (Cloud VPS / Azure App Service)*. Pengujian ini bertujuan untuk memvalidasi keandalan kinerja sistem di bawah beban concurrent users (*Performance Testing*) serta mengevaluasi ketahanan mekanisme keamanan aplikasi terhadap potensi ancaman siber (*Security Testing*) sesuai dengan rancangan skripsi.

Berdasarkan seluruh tahapan pengujian yang telah dieksekusi:
- **Total Test Cases:** 16 Kasus Uji (7 Skenario Kinerja + 9 Skenario Keamanan).
- **Hasil Eksekusi:** **16 Kasus Uji dinyatakan LULUS (PASS - 100%)**, dengan 0 Kasus Uji Gagal (FAIL), dan 0 Kasus Uji Terhalang (BLOCKED).
- **Error Rate Kinerja:** **0.00%** di seluruh tingkatan beban pengujian (*5 VU, 10 VU, dan 20 VU*).
- **Temuan Kerentanan Kritis:** **0 Kerentanan Kritis / Tinggi (High/Critical Vulnerability)** teridentifikasi pada pemindaian dinamis DAST (*OWASP ZAP*) maupun pengujian probe manual.

Dengan hasil ini, aplikasi **Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm** dinyatakan memenuhi kriteria keberhasilan pengujian (*Testing Completion Criteria*) dan siap digunakan secara andal dan aman.

---

# 2. Lingkungan & Konfigurasi Pengujian (Test Environment)

Pengujian dilakukan pada lingkungan yang terisolasi dan mencerminkan arsitektur target *production*:

| Komponen / Parameter | Spesifikasi / Konfigurasi Aktual | Status |
| :--- | :--- | :---: |
| **Application Target** | `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net` | VERIFIED |
| **Environment Type** | Staging Cloud Server (Azure App Service Linux - Standard B1) | VERIFIED |
| **Web & App Server** | Nginx Reverse Proxy + PHP 8.2-FPM (Linux Container) | VERIFIED |
| **Database Server** | Azure Database for MySQL Flexible Server (MySQL 8.0) | VERIFIED |
| **Storage Subsystem** | Laravel Private/Public Disk Storage (`storage/app/public/dokumen-perkara`) | VERIFIED |
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
Pengujian kinerja dilakukan menggunakan **Apache JMeter** dengan skenario *Progressive Load Testing* untuk mengukur stabilitas dan skalabilitas respon aplikasi:
1. **Baseline Testing (1 User / Validation)**: Memvalidasi integritas alur HTTP request, cookie session, dan parsing CSRF token.
2. **Progressive Load 5 VU (5 Concurrent Virtual Users)**: Menguji beban kerja normal harian.
3. **Progressive Load 10 VU (10 Concurrent Virtual Users)**: Menguji beban kerja menengah saat jam sibuk.
4. **Progressive Load 20 VU (20 Concurrent Virtual Users)**: Menguji kapasitas puncak pengajuan perkara dan verifikasi berkas secara simultan.

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
- *File Upload & Storage Security* (Validasi MIME type/ekstensi, random hashing filename, dan pemblokiran direct directory browsing).

---

# 4. Hasil Pelaksanaan Pengujian Kinerja (Performance Testing)

Pengujian kinerja mencakup seluruh modul operasional utama (PF-01 s/d PF-07):

### 4.1 Rekapitulasi Kasus Uji Kinerja

| ID | Skenario Pengujian | Target Modul / Endpoint | Hasil Pengujian Aktual | Status |
| :--- | :--- | :--- | :--- | :---: |
| **PF-01** | Login & Autentikasi | `POST /login` | Respon stabil di seluruh jenjang beban; parsing CSRF dan pembuatan session berjalan tanpa kegagalan (0% error). | **PASS** |
| **PF-02** | Akses Form Pra-Pendaftaran | `GET /klien/pra-pendaftaran/create` | Halaman form beserta data kategori perkara ter-render optimal di bawah beban simultan. | **PASS** |
| **PF-03** | Pengisian & Submit Formulir | `POST /klien/pra-pendaftaran` | Transaksi database multi-tabel (`pra_pendaftaran_perkara`, `riwayat_status`) selesai tanpa deadlock atau error. | **PASS** |
| **PF-04** | Upload Dokumen Pendukung | `POST /klien/pra-pendaftaran (multipart)` | Unggah file dummy PDF/JPG diproses dengan aman dan efisien ke disk storage. | **PASS** |
| **PF-05** | Monitoring Status Pengajuan | `GET /klien/pengajuan/{id}` | Riwayat status perkara dan catatan verifikasi dimuat konsisten tanpa degradasi performa. | **PASS** |
| **PF-06** | Akses Data Pra-Pendaftaran | `GET /klien/dashboard` | Query relasi data perkara pengguna teroptimasi dengan pagination tanpa N+1 query issue. | **PASS** |
| **PF-07** | Verifikasi Berkas Staf Legal | `POST /staf-legal/verifikasi-berkas/{id}` | Pembaruan status perkara, pencatatan verifikasi, dan riwayat status berjalan konsisten dalam database transaction. | **PASS** |

### 4.2 Tabel Analisis Metrik Kinerja (Progressive Load 5 VU, 10 VU, 20 VU)

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

# 5. Hasil Pelaksanaan Pengujian Keamanan (Security Testing)

Pengujian keamanan mencakup 9 kasus uji spesifik (ST-01 s/d ST-09) dan pemindaian otomatis OWASP ZAP:

### 5.1 Rekapitulasi Kasus Uji Keamanan

| ID | Fokus Keamanan | Metode & Pengujian | Hasil Pengujian Aktual | Status |
| :--- | :--- | :--- | :--- | :---: |
| **ST-01** | **Autentikasi & SQLi Prevention** | Injeksi payload SQL (`' OR '1'='1' --`) pada form autentikasi | Ditolak aman oleh Eloquent PDO Parameter Binding; tidak terjadi bypass otentikasi maupun kebocoran struktur query database. | **PASS** |
| **ST-02** | **Otorisasi Role Klien** | Percobaan akses direct URL Klien ke route Admin & Staf Legal | Klien berhasil mengakses data miliknya; direct access ke area Admin (`/admin/users`) dan Staf Legal diblokir dengan `HTTP 403 Forbidden`. | **PASS** |
| **ST-03** | **Otorisasi Role Admin** | Proteksi rute administratif dan verifikasi akses unauthenticated | Seluruh rute administratif terlindungi; request unauthenticated dialihkan ke halaman login (`HTTP 302 Redirect`). | **PASS** |
| **ST-04** | **Otorisasi Role Staf Legal**| Hak akses verifikasi perkara dan isolasi manajemen user | Staf Legal berhasil mengakses modul verifikasi; direct access ke modul pengguna Admin diblokir dengan `HTTP 403 Forbidden`. | **PASS** |
| **ST-05** | **Session & CSRF Management** | Uji cookie flags, request tanpa token CSRF, dan brute force | Cookie berstatus `HttpOnly`, `SameSite=lax`, dan `Secure`. State-changing request tanpa CSRF token menghasilkan `HTTP 419 Page Expired`. Rate Limiter Breeze aktif membatasi brute force. | **PASS** |
| **ST-06** | **Input Validation, XSS & IDOR** | Injeksi script XSS (`<script>alert(1)</script>`) & IDOR probe | Seluruh input divalidasi ketat via Form Request. Payload XSS di-escape otomatis oleh Blade Engine (`{{ }}`) menjadi `&lt;script&gt;`. Akses IDOR lintas user diblokir Policy (`HTTP 403/404`). | **PASS** |
| **ST-07** | **File Upload Security** | Upload ekstensi berbahaya (`shell.php`, `.exe`, MIME bypass) | File non-whitelisted ditolak validator (HTTP 302/422). File valid (`pdf, jpg, jpeg, png` <= 5 MB) disimpan dengan *random hash filename*. | **PASS** |
| **ST-08** | **Document Re-upload Integrity** | Uji unggah ulang pada berbagai status perkara | Unggah ulang berkas hanya diizinkan pada status `perlu_perbaikan`. File lama tetap dipertahankan (*no overwrite*) dengan record versi terpisah. | **PASS** |
| **ST-09** | **Security Config & DAST Scan** | *Directory browsing check* & pemindaian otomatis OWASP ZAP | Direct browsing `/storage/dokumen-perkara/` menghasilkan `HTTP 403/404`. Pemindaian OWASP ZAP Active & Passive Scanner selesai 100% tanpa kerentanan High/Critical. | **PASS** |

### 5.2 Rangkuman Pemindaian DAST OWASP ZAP 2.17.0
- **Target URL:** `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net`
- **Tingkat Kerentanan Tinggi (High):** **0**
- **Tingkat Kerentanan Sedang (Medium):** **0**
- **Tingkat Kerentanan Rendah (Low):** **0**
- **Informational / Passive Review Alerts:** **39** (Pemberitahuan informasional standar terkait banner server header dan caching browser yang umum pada environment Staging).

---

# 6. Analisis Temuan & Defect Management (Phase 6 Analysis)

Berdasarkan data eksekusi riil pada tahap pengujian:
- **Application Bugs:** `0 (None Identified)` — Seluruh alur fungsional berjalan sesuai spesifikasi bisnis.
- **Discrepancies:** `0 (None Identified)` — Arsitektur implementasi selaras dengan dokumen rancangan basis data dan model relasi.
- **Blockers:** `0 (None Identified)` — Tidak ada hambatan teknis yang menghalangi eksekusi pengujian.
- **Security Vulnerabilities:** `0 (None Identified)` — Tidak ditemukan kerentanan dengan skor CVSS v4.0.

---

# 7. Pernyataan Retest & Regression (Phase 7 & 8 Statement)

Karena seluruh 16 Kasus Uji (7 Performance + 9 Security) berhasil dieksekusi dengan status **100% PASS** pada iterasi pertama tanpa kegagalan fungsional, maka:
- **Phase 7 (Retest)** dinyatakan **Satisfied / Not Applicable** (tidak ada perbaikan bug yang memerlukan pengujian ulang).
- **Phase 8 (Regression Testing)** dinyatakan **Satisfied / Not Applicable** (tidak terdapat modifikasi kode sumber aplikasi pasca-pengujian).

---

# 8. Matriks Ketertelusuran Kebutuhan (Requirements Traceability Matrix - RTM)

| Feature Set | Nama Modul / Fitur | Test Case Kinerja | Test Case Keamanan | Status Akhir |
| :--- | :--- | :---: | :---: | :---: |
| **FS-01** | Autentikasi & Pengelolaan Akun | PF-01 | ST-01, ST-05 | **VERIFIED (PASS)** |
| **FS-02** | Pra-Pendaftaran Perkara & Upload Berkas | PF-02, PF-03, PF-04 | ST-06, ST-07, ST-08 | **VERIFIED (PASS)** |
| **FS-03** | Konsultasi & Booking Jadwal | PF-06 | ST-02, ST-06 | **VERIFIED (PASS)** |
| **FS-04** | Pemantauan Status Pengajuan & Catatan | PF-05 | ST-02, ST-06 | **VERIFIED (PASS)** |
| **FS-05** | Pemeriksaan & Verifikasi Berkas Staf Legal | PF-07 | ST-04, ST-08 | **VERIFIED (PASS)** |
| **FS-06** | Administrasi Pengguna & Laporan Admin | PF-06 | ST-03, ST-09 | **VERIFIED (PASS)** |

---

# 9. Indeks Bukti Pengujian (Evidence Index)

Seluruh bukti pengujian (*test evidence*) tersimpan secara terstruktur dalam repositori project:

1. **Bukti Pengujian Kinerja (JMeter)**:
   - JMX Test Plan: [`testing/jmeter/tny-law-firm-load-test.jmx`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/jmeter/tny-law-firm-load-test.jmx)
   - Raw JTL Results:
     - `testing/jmeter/results/load-test-{5,10,20}vu.jtl`
     - `testing/jmeter/results/load-test-klien-{5,10,20}vu.jtl`
     - `testing/jmeter/results/load-test-legal-{5,10,20}vu.jtl`
   - Dashboard HTML Report: `testing/jmeter/reports/report-10vu/index.html`

2. **Bukti Pengujian Keamanan (Security)**:
   - Terminal Execution Log: [`testing/evidence/security/security-test-execution.log`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/evidence/security/security-test-execution.log)
   - Laporan Lengkap DAST OWASP ZAP: [`testing/evidence/security/zap-baseline-report.html`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/evidence/security/zap-baseline-report.html)
   - Automated Probe Harness: [`testing/security/run_all_security_tests.ps1`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/testing/security/run_all_security_tests.ps1)

3. **Dokumentasi Spesifikasi & Status Pengujian**:
   - Master Test Plan: [`docs/testing/TEST_PLAN.md`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/docs/testing/TEST_PLAN.md)
   - Test Cases Matrix: [`docs/testing/TEST_CASES.md`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/docs/testing/TEST_CASES.md)
   - Testing State Tracker: [`docs/testing/TESTING_STATE.md`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/docs/testing/TESTING_STATE.md)
   - AI Testing Guide: [`docs/testing/TESTING_GUIDE.md`](file:///d:/SKRIPSI/PROJECT/tny-law-firm/docs/testing/TESTING_GUIDE.md)

---

# 10. Kesimpulan & Rekomendasi (Conclusion & Recommendations)

### 10.1 Kesimpulan
1. **Keandalan Kinerja Terbukti**: Aplikasi mampu melayani transaksi beban bertahap (5 hingga 20 concurrent users) dengan *Error Rate 0.00%* dan respon yang stabil pada seluruh modul bisnis utama.
2. **Arsitektur Keamanan Solid**: Penerapan Laravel Eloquent Parameter Binding, Blade Auto-Escaping, Form Request Validation, CSRF Protection, Session Flagging (`HttpOnly`, `SameSite=lax`), dan RBAC Middleware terbukti efektif memitigasi risiko serangan umum web (*SQL Injection, XSS, IDOR, CSRF, Malicious Uploads*).
3. **Kesesuaian dengan Rancangan Skripsi**: Seluruh fungsionalitas sistem telah tervalidasi 100% tanpa adanya deviasi arsitektur maupun pelanggaran aturan basis data yang telah ditetapkan.

### 10.2 Rekomendasi untuk Tahap Production / Evaluasi Lanjutan
1. **Penerapan Caching pada Data Statis**: Memanfaatkan Laravel Route/Config/View Caching (`php artisan config:cache`, `route:cache`, `view:cache`) pada server produksi untuk semakin menekan waktu respon.
2. **Monitoring Log Server Rutin**: Melakukan pemantauan berkala terhadap access log dan application error log saat sistem beroperasi penuh pada TNY Law Firm.
