# Performance & Security Improvement Execution Specification

Status: READY FOR EXECUTION
Priority: HIGH
Target Environment: STAGING
Production: OUT OF SCOPE

## Agent Execution Rule

This document is an EXECUTION SPECIFICATION, not a planning request.

The AI Agent MUST:
1. read this document completely;
2. inspect referenced project documentation and evidence;
3. execute the required changes;
4. validate the implementation;
5. deploy to staging using the existing deployment workflow;
6. perform retesting;
7. produce the requested final improvement report.

Do not stop after producing a plan unless a real blocker prevents safe execution.

PROJECT
=======

TNY Law Firm

Stack utama:
- Laravel
- PHP
- MySQL
- Azure App Service Linux
- Aiven MySQL
- Azure Blob Storage
- GitHub Actions CI/CD
- Staging environment

TARGET TASK
===========

Lakukan IMPROVEMENT PERFORMANCE DAN SECURITY terhadap aplikasi TNY Law Firm berdasarkan:

1. Test Plan yang sudah dibuat;
2. TEST_CASES.md;
3. TESTING_STATE.md;
4. FINAL_TEST_REPORT.md;
5. hasil Apache JMeter;
6. raw JTL;
7. hasil OWASP ZAP;
8. security-test-execution.log;
9. evidence testing lainnya;
10. source code aktual.

Ini BUKAN tugas untuk melakukan FINAL CORRECTION dokumentasi testing.

FINAL CORRECTION akan dilakukan SETELAH:
- improvement selesai;
- staging berhasil dideploy;
- retest selesai;
- before vs after comparison tersedia.

Tujuan task ini adalah:

TESTING RESULT
      ↓
IDENTIFY BOTTLENECK / SECURITY WEAKNESS
      ↓
IMPLEMENT IMPROVEMENT
      ↓
DEPLOY STAGING
      ↓
RETEST
      ↓
VERIFY IMPROVEMENT


==================================================
1. PRIMARY OBJECTIVES
==================================================

Lakukan dua pekerjaan:

A. PERFORMANCE IMPROVEMENT

Analisis hasil:

PF-01 Login
PF-02 Akses Pengajuan Pra-Pendaftaran
PF-03 Pengisian Formulir Perkara
PF-04 Upload Dokumen
PF-05 Monitoring Status Pengajuan
PF-06 Akses Data Pra-Pendaftaran
PF-07 Verifikasi Berkas

Gunakan hasil JMeter aktual untuk mencari scenario yang memiliki:

- response time paling tinggi;
- peningkatan response time terbesar ketika VU meningkat;
- query/database bottleneck;
- excessive database query;
- unnecessary eager/lazy loading;
- N+1 query;
- redundant request;
- expensive computation;
- synchronous I/O;
- inefficient file handling;
- inefficient database indexing;
- query tanpa index yang relevan;
- repeated database access;
- unnecessary session/database operations;
- inefficient controller/service implementation.

B. SECURITY IMPROVEMENT

Analisis:

ST-01 Authentication
ST-02 Authorization Client
ST-03 Authorization Admin
ST-04 Authorization Legal Staff
ST-05 Session Management
ST-06 Input Validation
ST-07 File Upload Security
ST-08 File Re-upload
ST-09 Security Configuration

Gunakan:
- source code;
- OWASP ZAP;
- manual security testing;
- security execution logs;
- runtime configuration;

untuk menemukan weakness yang benar-benar dapat diperbaiki.


==================================================
2. ZERO-ASSUMPTION RULE
==================================================

JANGAN melakukan optimization hanya berdasarkan teori.

Setiap perubahan harus mempunyai:

FINDING
→ EVIDENCE
→ ROOT CAUSE
→ PROPOSED FIX
→ IMPLEMENTATION
→ VERIFICATION

Jangan menulis:

"Query ini lambat"

tanpa evidence.

Jangan menulis:

"Security sudah lemah"

tanpa menunjukkan implementation/finding yang menjadi dasar.

Jika tidak ada evidence:
jangan melakukan perubahan hanya karena dianggap best practice.


==================================================
3. READ BEFORE MODIFYING
==================================================

Sebelum mengubah source code:

Baca:

- AGENTS.md
- TEST_PLAN
- TEST_CASES.md
- TESTING_STATE.md
- FINAL_TEST_REPORT.md jika tersedia
- raw JMeter JTL
- JMeter report
- security-test-execution.log
- OWASP ZAP report
- seluruh testing script

Kemudian inspect:

- routes
- controllers
- services
- models
- middleware
- Form Requests
- policies/gates
- authentication
- session configuration
- file upload implementation
- Azure Blob/document storage abstraction
- database queries
- migrations/index
- Laravel configuration
- production/staging relevant config

Jangan melakukan perubahan sebelum menyelesaikan reconnaissance.


==================================================
4. PERFORMANCE BASELINE
==================================================

Gunakan testing sebelumnya sebagai BEFORE BASELINE.

Jangan mengganti angka baseline.

Ambil langsung dari raw JTL/report.

Buat mapping:

PF ID
Current Average Response Time
Current Throughput
Current Error Rate
Current Request Count
Potential Bottleneck
Evidence

Contoh:

PF-05
Average Response Time: <actual>
20 VU Response Time: <actual>
Error Rate: <actual>
Evidence: <JTL/report>
Potential Bottleneck: <hasil source inspection>

Jangan membuat angka manual.


==================================================
5. PERFORMANCE PRIORITIZATION
==================================================

Jangan mencoba optimize seluruh aplikasi sekaligus.

Gunakan prioritas:

P1
Scenario dengan response time paling buruk / bottleneck terbesar.

P2
Scenario yang mempunyai peningkatan response time signifikan ketika load meningkat.

P3
Optimization sederhana dengan benefit tinggi dan risiko rendah.

Prioritaskan perubahan yang bersifat:

HIGH IMPACT
LOW / MEDIUM RISK


==================================================
6. PERFORMANCE ROOT CAUSE ANALYSIS
==================================================

Untuk PF-01 sampai PF-07, inspect actual execution path:

HTTP Request
   ↓
Route
   ↓
Middleware
   ↓
Controller
   ↓
Service
   ↓
Model / Query
   ↓
Storage / Database
   ↓
Response

Periksa antara lain:

DATABASE
- N+1
- missing eager loading
- unnecessary joins
- SELECT *
- query berulang
- filtering di PHP yang seharusnya di SQL
- pagination
- indexing
- ordering/filtering columns
- existence checks
- aggregate query
- transaction scope

APPLICATION
- unnecessary loops
- repeated transformations
- unnecessary object loading
- duplicated queries
- unnecessary file reads
- expensive synchronous operations

LARAVEL
- config caching
- route caching jika compatible
- view caching
- optimized autoloader
- production/staging APP_DEBUG
- appropriate cache usage

STORAGE
- Azure Blob access pattern
- unnecessary file metadata request
- repeated remote storage calls
- synchronous file operations

Jangan memasukkan Redis/queue/CDN hanya karena "best practice".

Tambahkan infrastructure baru HANYA jika evidence benar-benar menunjukkan kebutuhan.


==================================================
7. DATABASE OPTIMIZATION RULE
==================================================

Jika menemukan missing database index:

Sebelum menambah index:

verifikasi:
- query aktual;
- WHERE;
- JOIN;
- ORDER BY;
- foreign key;
- cardinality/usefulness.

Buat migration baru.

JANGAN:
- edit migration lama yang sudah deployed;
- drop database;
- migrate:fresh;
- migrate:refresh;
- db:wipe.

Migration harus aman terhadap existing staging data.


==================================================
8. SECURITY ROOT CAUSE ANALYSIS
==================================================

Periksa masing-masing security test.

ST-01 Authentication
- valid login
- invalid login
- unauthenticated resource
- authentication error handling
- credential handling
- login/session regeneration

ST-02 Authorization Client
- middleware
- policy
- ownership
- direct URL
- request-level access control

ST-03 Authorization Admin
- admin route restriction
- middleware
- policy
- privilege boundary

ST-04 Authorization Legal Staff
- middleware
- allowed resources
- forbidden resources
- direct request

ST-05 Session Management
- session fixation
- regeneration after login
- logout invalidation
- cookie security
- Secure
- HttpOnly
- SameSite where applicable

ST-06 Input Validation
- Form Request
- server-side validation
- SQL injection resilience
- XSS output escaping
- unexpected input
- boundary values

ST-07 File Upload Security
- extension
- MIME
- content validation where appropriate
- size
- filename
- path
- storage access
- authorization

ST-08 File Re-upload
- ownership
- authorization
- replacement
- old object behavior
- stale object exposure
- unauthorized replacement

ST-09 Security Configuration
- debug mode
- HTTPS
- trusted proxy
- cookies
- headers
- error exposure
- sensitive configuration
- public files
- OWASP ZAP findings


==================================================
9. SECURITY FIX RULE
==================================================

Untuk setiap security issue:

Evidence
   ↓
Attack / Failure Scenario
   ↓
Affected Code
   ↓
Root Cause
   ↓
Minimal Secure Fix
   ↓
Regression Check

Prioritaskan:

1. Authentication
2. Authorization / IDOR
3. File access
4. Input validation
5. Session
6. Security configuration
7. Informational hardening

Jangan mengubah business logic kecuali business logic tersebut memang merupakan sumber vulnerability.


==================================================
10. BUSINESS LOGIC PROTECTION
==================================================

CRITICAL:

Jangan mengubah behavior bisnis TNY Law Firm hanya untuk meningkatkan benchmark.

Contoh:

JANGAN:
- menghapus authorization agar request lebih cepat;
- melewati validation;
- melewati storage processing;
- menghapus database operation yang diperlukan;
- menggunakan fake response;
- mengurangi fungsi endpoint;
- menghapus security middleware;
- mengubah output bisnis hanya demi response time.

Performance improvement harus mempertahankan functional behavior.


==================================================
11. NO BENCHMARK GAMING
==================================================

Dilarang:

- hardcode response;
- cache hasil per-user secara salah;
- bypass database hanya saat JMeter;
- mendeteksi User-Agent JMeter;
- disable validation saat load test;
- disable security saat testing;
- menurunkan workload test;
- mengubah jumlah VU;
- mengubah test case agar hasil terlihat lebih baik.

Test tetap:

Baseline
5 VU
10 VU
20 VU


==================================================
12. MINIMAL CHANGE PRINCIPLE
==================================================

Gunakan:

MINIMUM CHANGE
MAXIMUM MEASURABLE IMPACT

Jangan melakukan large refactor kecuali evidence menunjukkan memang diperlukan.

Hindari:
- architecture rewrite;
- microservices;
- Redis jika tidak diperlukan;
- new message broker;
- Kubernetes;
- infrastructure baru yang tidak relevan.

Project skripsi harus tetap maintainable.


==================================================
13. IMPLEMENTATION PLAN
==================================================

Setelah reconnaissance, buat internal implementation plan:

PERF-IMP-01
Finding:
Evidence:
Root Cause:
Affected File:
Proposed Change:
Risk:
Expected Impact:

PERF-IMP-02
...

SEC-IMP-01
Finding:
Evidence:
Root Cause:
Affected File:
Proposed Change:
Risk:

SEC-IMP-02
...

Kemudian implementasikan perubahan yang mempunyai evidence kuat.


==================================================
14. LOCAL VALIDATION
==================================================

Setelah perubahan:

jalankan test yang relevan.

Minimal:

php artisan test

dan validation lain yang tersedia di project.

Pastikan:
- tidak ada test regression;
- application boot berhasil;
- routes valid;
- config valid;
- source code lint/syntax valid.

Jika frontend berubah:
jalankan build yang diperlukan.

Jangan lanjut deployment jika local verification gagal.


==================================================
15. GIT SAFETY
==================================================

Sebelum melakukan perubahan:

git status

Jangan overwrite unrelated changes.

Dilarang:

git reset --hard
git clean -fd tanpa verifikasi
force push
delete unrelated files

Commit hanya perubahan relevan.


==================================================
16. STAGING DEPLOYMENT
==================================================

Setelah local validation PASS:

deploy melalui existing CI/CD.

JANGAN membuat jalur deployment baru jika pipeline yang ada sudah bekerja.

Target:
Azure App Service STAGING.

Production:
OUT OF SCOPE.

Verifikasi:

Build
PASS
↓
Deploy
PASS
↓
Migration jika ada
PASS
↓
Smoke Test
PASS


==================================================
17. POST-DEPLOY VERIFICATION
==================================================

Sebelum retest:

verifikasi staging:

- homepage
- login
- database connectivity
- document storage
- Client access
- Admin access
- Legal Staff access
- relevant modified functionality

Jika deployment gagal:
jangan melakukan performance/security retest.


==================================================
18. PERFORMANCE RETEST
==================================================

Setelah deployment berhasil:

jalankan kembali PF-01 sampai PF-07 menggunakan TEST PLAN YANG SAMA.

Tidak boleh mengubah:

- endpoint;
- request;
- account;
- data model secara tidak relevan;
- methodology;
- VU stages.

Gunakan:

Baseline
5 VU
10 VU
20 VU

Collect:

Response Time
Throughput
Error Rate
Request Count

Save raw evidence baru secara terpisah.

Jangan overwrite baseline lama.


==================================================
19. BEFORE VS AFTER
==================================================

Buat hasil:

| ID | Load | Before Avg | After Avg | Difference | Improvement % | Error Before | Error After |

Improvement %:

((Before - After) / Before) × 100

Gunakan perhitungan programmatic dari raw data.

Jangan menghitung manual jika bisa diotomatisasi.

Jika hasil AFTER lebih buruk:
laporkan apa adanya.

Jangan menyembunyikan regression.


==================================================
20. SECURITY RETEST
==================================================

Setelah security fix:

jalankan kembali relevant ST-01 sampai ST-09.

Gunakan mapping resmi:

ST-01 Authentication
ST-02 Authorization Client
ST-03 Authorization Admin
ST-04 Authorization Legal Staff
ST-05 Session Management
ST-06 Input Validation
ST-07 File Upload Security
ST-08 File Re-upload
ST-09 Security Configuration

Untuk security test yang tidak terpengaruh perubahan:
minimal lakukan regression verification.

Untuk security finding yang diperbaiki:
wajib retest langsung.


==================================================
21. OWASP ZAP RETEST
==================================================

Jika ZAP sebelumnya digunakan:

jalankan kembali scan dengan scope dan methodology yang sama.

Bandingkan:

BEFORE
vs
AFTER

Tampilkan:

Alert
Risk
Before
After
Verification

Jangan menganggap alert hilang = seluruh aplikasi aman.

Manually verify relevant result.


==================================================
22. REGRESSION TEST
==================================================

Pastikan improvement tidak menyebabkan:

- broken login;
- authorization regression;
- Client memperoleh akses Admin;
- Legal Staff kehilangan akses valid;
- upload gagal;
- re-upload salah;
- session rusak;
- database inconsistency;
- storage regression;
- application error.


==================================================
23. DO NOT FINAL-CORRECT DOCUMENTATION YET
==================================================

Pada task ini:

JANGAN melakukan FINAL CORRECTION besar terhadap:
- TEST_CASES.md
- TESTING_STATE.md
- FINAL_TEST_REPORT.md

kecuali update minimal diperlukan untuk mencatat improvement/retest evidence.

Tujuannya adalah mempertahankan:

BEFORE TEST RESULTS

sebagai baseline historis.

Setelah seluruh improvement + retest selesai,
akan ada task terpisah untuk:

FINAL DOCUMENTATION CORRECTION.


==================================================
24. EVIDENCE PRESERVATION
==================================================

Jangan overwrite:

- old JTL;
- old JMeter report;
- old ZAP report;
- old security log.

Gunakan struktur seperti:

testing/
├── before/
│   ├── performance/
│   └── security/
│
└── after/
    ├── performance/
    └── security/

Jika struktur repository sudah berbeda:
ikuti struktur existing dan gunakan naming yang jelas.

Tujuannya:

BEFORE
dapat dibandingkan dengan
AFTER.


==================================================
25. OUTPUT REPORT
==================================================

Setelah selesai, berikan:

# PERFORMANCE & SECURITY IMPROVEMENT REPORT

## 1. Initial Findings

### Performance
PF-01:
...
PF-07:

### Security
ST-01:
...
ST-09:


## 2. Improvements Implemented

Untuk setiap perubahan:

ID:
Finding:
Evidence:
Root Cause:
Files Modified:
Implementation:
Risk:
Verification:


## 3. Performance Before vs After

Tampilkan tabel aktual.


## 4. Security Before vs After

Tampilkan:

Security Test
Before
Fix
After
Evidence


## 5. Regression Result


## 6. Deployment Verification

CI:
PASS / FAIL

CD:
PASS / FAIL

Staging:
PASS / FAIL


## 7. Files Modified


## 8. Remaining Issues

Gunakan:

NONE
atau daftar issue nyata.


## 9. Final Verdict

Gunakan salah satu:

IMPROVEMENT VERIFIED

atau

IMPROVEMENT PARTIALLY VERIFIED

atau

IMPROVEMENT NOT VERIFIED


==================================================
26. SUCCESS CRITERIA
==================================================

Task dianggap selesai jika:

[ ] existing testing documentation dipahami
[ ] baseline performance berasal dari raw JMeter evidence
[ ] bottleneck dianalisis dari source code aktual
[ ] hanya optimization dengan evidence yang dilakukan
[ ] security weakness dianalisis
[ ] business logic tetap terjaga
[ ] local tests PASS
[ ] staging deployment PASS
[ ] smoke test PASS
[ ] PF-01–PF-07 di-retest
[ ] ST-01–ST-09 relevant tests di-retest
[ ] before/after evidence tersimpan
[ ] tidak ada fabricated result
[ ] regression diperiksa
[ ] tidak ada credential bocor
[ ] production tidak disentuh
[ ] hasil improvement dilaporkan secara objektif


==================================================
FINAL INSTRUCTION
==================================================

Jangan berusaha membuat angka AFTER lebih bagus secara artifisial.

Tujuan pekerjaan bukan:

"membuktikan bahwa optimasi berhasil"

tetapi:

"mengimplementasikan improvement yang didukung evidence lalu mengukur dampaknya secara objektif."

Jika performance meningkat:
laporkan peningkatannya.

Jika tidak berubah signifikan:
laporkan apa adanya.

Jika memburuk:
laporkan regression dan analisis penyebabnya.

Urutan wajib:

READ TESTING EVIDENCE
        ↓
MAP FINDINGS TO SOURCE CODE
        ↓
ROOT CAUSE ANALYSIS
        ↓
IMPLEMENT MINIMAL FIX
        ↓
LOCAL VERIFY
        ↓
DEPLOY STAGING
        ↓
PERFORMANCE RETEST
        ↓
SECURITY RETEST
        ↓
BEFORE VS AFTER ANALYSIS
        ↓
REGRESSION
        ↓
IMPROVEMENT REPORT

Setelah seluruh proses tersebut selesai, STOP.

Jangan masuk ke FINAL CORRECTION dokumentasi karena itu akan menjadi task berikutnya.