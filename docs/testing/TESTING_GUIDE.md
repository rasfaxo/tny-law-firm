* **Performance Testing + Security Testing** sebagai metodologi final.
* Performance: **Apache JMeter**, PF-01–PF-07.
* Security: **OWASP WSTG + OWASP ZAP + Manual Testing + Browser/DevTools**, ST-01–ST-09.
* Test Plan dan Test Case menjadi **execution specification**.
* AI Agent wajib melakukan **implementation mapping sebelum testing**.
* Tidak boleh mengarang endpoint, hasil, evidence, vulnerability, maupun metrics.
* Actual Result hanya boleh berasal dari execution aktual.
* AI Agent boleh melakukan debugging, tetapi tidak boleh mengubah test agar PASS.
* Retest dan regression wajib dilakukan bila ada perbaikan yang relevan.

---

# TNY LAW FIRM — AI AGENT TESTING GUIDE

**Document Type:** AI Agent Testing Operating Manual
**Target Environment:** Antigravity IDE
**Application:** TNY Law Firm
**Testing Methodology:** Performance Testing + Security Testing
**Performance Tool:** Apache JMeter
**Security Tools:** OWASP WSTG, OWASP ZAP, Browser/DevTools, Manual Testing
**Vulnerability Assessment:** CVSS v4.0
**Execution Principle:** Evidence-Based, Zero-Assumption, Reproducible

---

# 1. PURPOSE

Dokumen ini merupakan **operating manual bagi AI Agent** untuk melakukan software testing terhadap project TNY Law Firm menggunakan Antigravity IDE.

Dokumen ini **bukan hasil pengujian**.

Dokumen ini juga **bukan instruksi untuk mengarang atau mengisi hasil pengujian sebelum testing dilakukan**.

Fungsi utama dokumen ini adalah memberikan prosedur operasional yang harus diikuti AI Agent mulai dari:

```text
Project Reconnaissance
        ↓
Understanding Test Plan
        ↓
Understanding Test Case
        ↓
Implementation Mapping
        ↓
Environment Preparation
        ↓
Test Data Preparation
        ↓
Performance Testing
        ↓
Security Testing
        ↓
Evidence Collection
        ↓
Result Analysis
        ↓
Failure / Finding Investigation
        ↓
Fix if Authorized
        ↓
Retest
        ↓
Regression Testing
        ↓
Final Test Report
```

AI Agent harus menggunakan dokumen ini sebagai **SOP execution**.

---

# 2. PRIMARY OBJECTIVE

AI Agent bertugas melakukan testing terhadap sistem TNY Law Firm berdasarkan:

1. Skripsi final sebagai konteks sistem dan fitur.
2. Test Plan sebagai dasar strategi dan scope testing.
3. Test Case sebagai execution specification.
4. Source code sebagai sumber fakta implementasi.
5. Runtime application sebagai sumber fakta perilaku aktual.
6. Tool output sebagai evidence hasil testing.

Tujuan akhir adalah menghasilkan:

* test execution record;
* evidence;
* performance result;
* security result;
* bug/finding record;
* retest result;
* regression result;
* final test report.

---

# 3. AI AGENT ROLE

AI Agent bertindak sebagai:

> **Senior QA Automation Engineer + Performance Testing Engineer + Security Tester**

AI Agent bertanggung jawab untuk:

* memahami struktur project;
* memahami arsitektur aplikasi;
* memahami feature dan actor;
* memahami Test Plan;
* memahami Test Case;
* memeriksa implementasi aktual;
* membuat implementation mapping;
* mempersiapkan environment;
* menyiapkan test data;
* menjalankan performance testing;
* menjalankan security testing;
* mengumpulkan evidence;
* menganalisis hasil;
* mengidentifikasi failure;
* melakukan root cause analysis;
* melakukan debugging bila diizinkan;
* melakukan retest;
* melakukan regression testing;
* menyusun final testing report.

AI Agent **tidak bertindak sebagai pengganti keputusan manusia** pada perubahan scope, requirement, security architecture, atau operasi berisiko.

---

# 4. CORE PRINCIPLES

AI Agent wajib mengikuti prinsip berikut.

## 4.1 Zero-Assumption

Jangan menganggap sesuatu benar hanya karena:

* nama file mengindikasikan demikian;
* framework biasanya menggunakan pola tertentu;
* Test Case menyebut endpoint tertentu;
* dokumentasi menyebut fitur tertentu;
* AI mengetahui pola umum Laravel.

Semua informasi yang berhubungan dengan implementasi harus diverifikasi.

---

## 4.2 Evidence-Based

Setiap hasil testing harus memiliki dasar observasi.

Gunakan:

```text
Expected Result
+
Actual Observation
+
Evidence
=
Validated Test Result
```

Jangan menggunakan:

```text
Expected Result
+
AI Assumption
=
PASS
```

---

## 4.3 No Fabricated Result

AI Agent dilarang mengarang:

* PASS;
* FAIL;
* response time;
* throughput;
* error rate;
* request count;
* vulnerability;
* CVSS score;
* screenshot;
* log;
* request/response;
* test evidence.

Jika testing belum dilakukan, statusnya:

> NOT EXECUTED

Jika testing tidak dapat dilakukan:

> BLOCKED

Jika implementasi belum dapat diverifikasi:

> UNVERIFIED

---

# 5. SOURCE OF TRUTH HIERARCHY

Gunakan prioritas sumber berikut:

```text
1. Approved Test Plan
2. Approved Test Case
3. Final Thesis / System Requirements
4. Actual Source Code
5. Actual Runtime Behavior
6. Testing Tool Output
7. AI Inference
```

Namun terdapat aturan penting:

> **AI inference tidak boleh menggantikan fakta yang belum diverifikasi.**

Jika dua sumber berbeda:

1. identifikasi discrepancy;
2. jangan memilih salah satu berdasarkan asumsi;
3. verifikasi source code/runtime;
4. dokumentasikan discrepancy;
5. minta human approval apabila discrepancy mengubah scope atau requirement.

---

# 6. TESTING METHODOLOGY

Metodologi pengujian penelitian TNY Law Firm adalah:

## Performance Testing

Menggunakan:

> **Apache JMeter**

Scope:

* PF-01 — Login
* PF-02 — Akses pengajuan pra-pendaftaran
* PF-03 — Pengisian formulir perkara
* PF-04 — Upload dokumen
* PF-05 — Monitoring status pengajuan
* PF-06 — Akses data pra-pendaftaran
* PF-07 — Verifikasi berkas

Load progression:

```text
Baseline
   ↓
5 Virtual Users
   ↓
10 Virtual Users
   ↓
20 Virtual Users
```

Metrics:

* Response Time
* Throughput
* Error Rate
* Request Count

---

## Security Testing

Menggunakan:

* OWASP WSTG;
* OWASP ZAP;
* Manual Testing;
* Browser/DevTools bila diperlukan;
* CVSS v4.0 untuk vulnerability yang telah diverifikasi.

Scope:

* ST-01 — Authentication
* ST-02 — Authorization Klien
* ST-03 — Authorization Admin
* ST-04 — Authorization Staf Legal
* ST-05 — Session Management
* ST-06 — Input Validation
* ST-07 — File Upload Security
* ST-08 — File Re-upload
* ST-09 — Security Configuration

**Black Box Testing bukan metode execution testing penelitian ini.**

Jika istilah Black Box muncul dalam penelitian terdahulu atau landasan teori, jangan mengubahnya menjadi metode pengujian TNY Law Firm.

---

# 7. TESTING ARCHITECTURE

Selalu ikuti lifecycle berikut:

```text
TEST PLAN
    ↓
TEST CASE
    ↓
PROJECT ANALYSIS
    ↓
IMPLEMENTATION MAPPING
    ↓
ENVIRONMENT VERIFICATION
    ↓
TEST DATA PREPARATION
    ↓
TEST EXECUTION
    ↓
EVIDENCE COLLECTION
    ↓
RESULT ANALYSIS
    ↓
FAILURE / FINDING
    ↓
ROOT CAUSE ANALYSIS
    ↓
FIX IF AUTHORIZED
    ↓
RETEST
    ↓
REGRESSION
    ↓
FINAL REPORT
```

**Jangan langsung menjalankan Test Case sebelum implementation mapping dilakukan.**

---

# 8. PHASE 0 — INITIALIZATION

Sebelum melakukan testing:

1. Baca dokumen Testing Guide ini.
2. Temukan Test Plan.
3. Temukan seluruh Test Case.
4. Temukan dokumentasi project.
5. Periksa struktur repository.
6. Tentukan environment testing.
7. Pastikan testing authorization tersedia.

Buat execution workspace khusus untuk artefak testing.

Contoh struktur:

```text
testing/
├── test-plan/
├── test-cases/
├── implementation-mapping/
├── performance/
│   ├── jmeter/
│   ├── raw-results/
│   ├── reports/
│   └── evidence/
├── security/
│   ├── zap/
│   ├── evidence/
│   └── findings/
├── defects/
├── retest/
├── regression/
└── final-report/
```

Jangan membuat folder tersebut jika project memiliki struktur testing yang telah ditentukan. Adaptasikan berdasarkan repository aktual.

---

# 9. PHASE 1 — PROJECT RECONNAISSANCE

Sebelum melakukan execution, pahami project.

Periksa jika tersedia:

```text
README
package manager
framework
programming language
source code
routes
controllers
models
middleware
authentication
authorization
validation
database
API
frontend
existing tests
environment configuration
deployment configuration
file upload implementation
```

Jika menggunakan Laravel, periksa keberadaan:

```text
routes/
app/Http/Controllers/
app/Http/Middleware/
app/Models/
resources/
database/
config/
tests/
composer.json
.env.example
```

**Jangan menganggap semua path tersebut tersedia.**

Pertama periksa filesystem/repository.

---

# 10. PHASE 2 — SYSTEM UNDERSTANDING

Identifikasi sistem berdasarkan implementasi aktual.

Actor utama:

```text
Klien
Admin
Staf Legal
```

Feature Set yang harus diverifikasi:

```text
FS-01 Authentication
FS-02 Pra-Pendaftaran Perkara
FS-03 Konsultasi
FS-04 Monitoring Status
FS-05 Verifikasi Berkas
FS-06 Administrasi
```

AI Agent harus memverifikasi implementasi setiap Feature Set.

Jangan menganggap Feature Set tersedia hanya karena disebut dalam dokumentasi.

---

# 11. PHASE 3 — IMPLEMENTATION MAPPING

Setiap Test Case harus memiliki implementation mapping.

Gunakan format:

| Test Case | Feature | Route | HTTP Method | Controller | Middleware | Authentication | Authorization | Validation | Implementation Status |
| --------- | ------- | ----- | ----------- | ---------- | ---------- | -------------- | ------------- | ---------- | --------------------- |

Contoh:

| PF-01 | Login | UNVERIFIED | UNVERIFIED | UNVERIFIED | UNVERIFIED | UNVERIFIED | UNVERIFIED | UNVERIFIED | UNVERIFIED |

Jangan mengisi:

```text
/login
POST
LoginController
auth
```

hanya karena pola tersebut umum digunakan.

Cari fakta dari source code.

---

# 12. IMPLEMENTATION MAPPING RULE

Untuk setiap Test Case, AI Agent harus mencari:

1. Feature.
2. Route.
3. HTTP method.
4. Controller.
5. Middleware.
6. Authentication mechanism.
7. Authorization mechanism.
8. Validation.
9. Request parameters.
10. Session behavior.
11. Database interaction.
12. File handling jika relevan.
13. Actual executable flow.

Jika informasi tidak ditemukan:

> UNVERIFIED

Jika feature tidak ditemukan:

> IMPLEMENTATION DISCREPANCY

Jika Test Case tidak dapat diterapkan:

> BLOCKED / NOT APPLICABLE — WITH JUSTIFICATION

---

# 13. PHASE 4 — ENVIRONMENT VERIFICATION

Sebelum execution, verifikasi:

* application URL;
* environment;
* browser;
* application server;
* database;
* test account;
* test data;
* JMeter;
* ZAP;
* required dependencies.

Catat status:

```text
VERIFIED
UNVERIFIED
BLOCKED
```

Jangan menulis environment value berdasarkan asumsi.

---

# 14. TEST ACCOUNT MANAGEMENT

Gunakan account khusus testing.

Minimal, jika tersedia:

```text
Client Test Account
Admin Test Account
Legal Staff Test Account
```

Verifikasi role dari implementation/database.

Jangan menggunakan:

* akun client sebenarnya;
* dokumen client sebenarnya;
* data perkara sebenarnya;
* credential production.

Credential atau secret tidak boleh dimasukkan ke final report.

---

# 15. TEST DATA MANAGEMENT

Test data harus:

* reproducible;
* isolated;
* traceable;
* disposable;
* non-sensitive.

Contoh:

```text
Dummy Client
Dummy Case
Dummy Document
Dummy Consultation Schedule
Dummy Verification Note
```

Gunakan dummy document untuk upload testing.

---

# 16. UNIVERSAL TEST EXECUTION PROTOCOL

Untuk setiap Test Case:

```text
1. Read Test Case
2. Identify Objective
3. Read Preconditions
4. Read Expected Result
5. Inspect Implementation
6. Prepare Test Data
7. Execute Test
8. Observe Actual Behavior
9. Capture Evidence
10. Compare Actual vs Expected
11. Determine Status
12. Document Result
```

Status yang diperbolehkan:

```text
PASS
FAIL
BLOCKED
NOT EXECUTED
NOT APPLICABLE
```

---

# 17. EXPECTED RESULT VS ACTUAL RESULT

## Expected Result

Harus berasal dari:

> Test Case yang disetujui.

## Actual Result

Harus berasal dari:

> hasil observasi saat execution aktual.

Contoh:

```text
Expected:
User dengan credential valid dapat login.

Actual:
Saat execution aktual, sistem menampilkan dashboard Klien
setelah credential valid dikirim.

Evidence:
SEC-ST01-001
```

Jangan mengisi Actual Result sebelum execution.

---

# 18. PERFORMANCE TESTING WORKFLOW

Tool:

> Apache JMeter

Execution:

```text
Implementation Mapping
        ↓
Understand User Flow
        ↓
Identify Endpoint
        ↓
Understand Authentication
        ↓
Understand Session
        ↓
Understand CSRF
        ↓
Build JMeter Test Plan
        ↓
Baseline
        ↓
5 VU
        ↓
10 VU
        ↓
20 VU
        ↓
Collect Metrics
        ↓
Analyze
```

---

# 19. PERFORMANCE TEST CASES

## PF-01 — Login

Tujuan:

Mengevaluasi performa proses login.

Sebelum membuat JMeter request:

* verifikasi endpoint;
* verifikasi HTTP method;
* verifikasi credential flow;
* verifikasi session/cookie;
* verifikasi CSRF jika ada.

---

## PF-02 — Akses Pengajuan Pra-Pendaftaran

Verifikasi flow aktual menuju fitur pra-pendaftaran.

Jangan mengasumsikan endpoint.

---

## PF-03 — Pengisian Formulir Perkara

Identifikasi:

* form;
* request;
* required field;
* validation;
* authentication;
* session;
* submission endpoint.

Gunakan test data dummy.

---

## PF-04 — Upload Dokumen

Sebelum load test:

* verifikasi endpoint upload;
* file format;
* file size;
* multipart request;
* authentication;
* CSRF;
* storage behavior.

Gunakan dummy document.

---

## PF-05 — Monitoring Status Pengajuan

Verifikasi flow aktual Klien ketika melihat status pengajuan.

---

## PF-06 — Akses Data Pra-Pendaftaran

Verifikasi flow aktual Admin dalam mengakses data pra-pendaftaran.

---

## PF-07 — Verifikasi Berkas

Verifikasi flow aktual Staf Legal dalam melakukan verifikasi.

---

# 20. JMeter TEST PLAN

Jika implementation mapping telah selesai, AI Agent dapat membuat `.jmx`.

Komponen yang dapat digunakan sesuai kebutuhan:

```text
Test Plan
Thread Group
HTTP Request Defaults
HTTP Cookie Manager
HTTP Header Manager
CSV Data Set Config
HTTP Request
Assertions
Listeners
```

Jangan menambahkan komponen hanya karena template.

Gunakan hanya yang diperlukan oleh flow aktual.

---

# 21. JMeter AUTHENTICATION

Sebelum load testing authenticated endpoint, verifikasi:

* login sequence;
* cookie;
* session;
* token;
* CSRF;
* redirect;
* authorization.

Jika authentication state tidak dapat direplikasi dengan benar:

> BLOCKED

Jangan memalsukan authentication token.

---

# 22. PERFORMANCE LOAD PROFILE

Gunakan:

```text
Baseline
5 VU
10 VU
20 VU
```

Jika baseline diperlukan sebagai kondisi pembanding, lakukan baseline terlebih dahulu.

Jangan mengubah:

```text
5 → 10 → 20
```

menjadi angka lain tanpa alasan dan approval.

---

# 23. PERFORMANCE METRICS

Catat:

### Response Time

Waktu respons request.

### Throughput

Jumlah request yang dapat diproses dalam periode tertentu.

### Error Rate

Persentase request yang menghasilkan error.

### Request Count

Jumlah request yang dieksekusi.

Semua angka harus berasal dari JMeter.

AI Agent **dilarang menghitung atau mengarang angka yang tidak didukung raw result**.

---

# 24. JMeter EVIDENCE

Simpan:

```text
.jmx
raw result
CSV
HTML report
logs
screenshots
```

Naming:

```text
PERF-PF01-001
PERF-PF02-001
PERF-PF03-001
...
PERF-PF07-001
```

Jika satu execution menghasilkan beberapa artefak, gunakan suffix konsisten:

```text
PERF-PF01-001-jmx
PERF-PF01-001-result
PERF-PF01-001-report
PERF-PF01-001-evidence
```

---

# 25. PERFORMANCE ANALYSIS

Jangan hanya menyatakan:

> "Performance bagus."

Analisis harus berdasarkan data aktual.

Contoh struktur:

```text
Test Case:
PF-01

Load:
10 VU

Response Time:
[actual result]

Throughput:
[actual result]

Error Rate:
[actual result]

Observation:
[actual observation]

Conclusion:
[analysis based on evidence]
```

Jika threshold tidak ditetapkan dalam Test Plan:

> Jangan membuat threshold sendiri lalu mengklaim PASS.

Gunakan:

> Observed Result

dan jelaskan analisis berdasarkan baseline/perbandingan yang tersedia.

---

# 26. SECURITY TESTING WORKFLOW

Gunakan:

```text
OWASP WSTG
    +
OWASP ZAP
    +
Manual Testing
    +
Browser/DevTools
```

Workflow:

```text
Configure ZAP
      ↓
Configure Browser
      ↓
Explore Application
      ↓
Passive Scan
      ↓
Review Alerts
      ↓
Manual Verification
      ↓
Active Scan if Authorized
      ↓
Validate Findings
      ↓
CVSS v4.0
      ↓
Security Report
```

---

# 27. ZAP RULE

> **ZAP alert bukan otomatis vulnerability.**

Jika ZAP menghasilkan alert:

1. baca alert;
2. identifikasi affected endpoint;
3. reproduksi;
4. periksa request/response;
5. periksa source code bila relevan;
6. tentukan apakah behavior benar-benar security issue;
7. dokumentasikan evidence;
8. baru klasifikasikan finding.

Jika alert tidak dapat diverifikasi:

> UNVERIFIED / POTENTIAL FINDING

Jangan langsung memasukkannya sebagai vulnerability confirmed.

---

# 28. SECURITY TEST CASES

## ST-01 — Authentication

Verifikasi:

* login;
* invalid credential;
* authentication boundary;
* unauthorized access;
* logout/session behavior bila relevan.

Gunakan account testing.

---

## ST-02 — Authorization Klien

Verifikasi bahwa Klien tidak dapat mengakses resource yang hanya diperuntukkan bagi Admin atau Staf Legal.

Uji:

* UI access;
* direct URL;
* request-level access jika relevan.

---

## ST-03 — Authorization Admin

Verifikasi pembatasan akses Admin terhadap resource yang bukan kewenangannya.

---

## ST-04 — Authorization Staf Legal

Verifikasi pembatasan akses Staf Legal terhadap resource yang bukan kewenangannya.

---

## ST-05 — Session Management

Periksa:

* session lifecycle;
* logout;
* session invalidation;
* unauthorized session reuse;
* cookie/session behavior bila relevan.

---

## ST-06 — Input Validation

Periksa input berdasarkan implementation dan Test Case.

Jangan melakukan destructive payload secara sembarangan.

Gunakan input testing yang aman dan authorized.

---

## ST-07 — File Upload Security

Verifikasi:

* allowed extension;
* MIME handling;
* size restriction;
* filename handling;
* storage;
* access control.

Gunakan dummy file.

---

## ST-08 — File Re-upload

Verifikasi:

* valid replacement;
* invalid format;
* oversized file;
* association dengan pengajuan;
* verification state;
* access control.

Gunakan dummy data.

---

## ST-09 — Security Configuration

Periksa konfigurasi security yang relevan dengan aplikasi aktual.

Jangan mengklaim konfigurasi tertentu ada/tidak ada sebelum diverifikasi.

---

# 29. AUTHORIZATION MATRIX

Buat matrix berdasarkan implementation aktual.

Format:

| Resource / Action | Klien    | Admin    | Staf Legal |
| ----------------- | -------- | -------- | ---------- |
| Resource A        | VERIFIED | VERIFIED | VERIFIED   |
| Resource B        | VERIFIED | VERIFIED | VERIFIED   |

Jangan mengisi permission berdasarkan asumsi.

Sumber:

```text
Route
+
Middleware
+
Controller authorization
+
Policy/Gate
+
Runtime behavior
```

---

# 30. HIDDEN UI ≠ AUTHORIZATION

Jangan menyimpulkan:

> "Menu tidak terlihat, berarti user tidak memiliki akses."

Authorization harus diuji pada server-side behavior.

Jika memungkinkan, lakukan:

```text
UI Access
+
Direct URL
+
Request-level Access
```

---

# 31. FILE UPLOAD TESTING

Sebelum testing file upload, AI Agent wajib mencari:

```text
Allowed Extension
MIME Validation
Maximum File Size
Storage Location
Filename Handling
Access Control
Replacement Logic
Re-upload Logic
```

Sumber informasi harus berasal dari:

* source code;
* configuration;
* runtime;
* Test Case.

---

# 32. SECURITY EVIDENCE

Simpan:

```text
screenshots
ZAP report
request/response
browser evidence
logs
source-code evidence where appropriate
```

Naming:

```text
SEC-ST01-001
SEC-ST02-001
SEC-ST03-001
...
SEC-ST09-001
```

---

# 33. SECURITY FINDING WORKFLOW

Jika menemukan potential vulnerability:

```text
Potential Finding
       ↓
Collect Evidence
       ↓
Reproduce
       ↓
Validate
       ↓
Classify
       ↓
CVSS v4.0
       ↓
Recommendation
```

Jangan memberikan CVSS score sebelum vulnerability terverifikasi.

Jika tidak cukup informasi:

> CVSS: NOT DETERMINED

---

# 34. CVSS RULE

CVSS hanya digunakan untuk vulnerability yang benar-benar telah diverifikasi.

Dokumentasikan:

* vulnerability;
* affected component;
* attack vector;
* relevant CVSS metrics;
* resulting score;
* severity;
* evidence;
* remediation recommendation.

Jangan membuat score berdasarkan perkiraan semata.

---

# 35. FAILURE HANDLING

Jika Test Case FAIL:

```text
FAIL
 ↓
Reproduce
 ↓
Collect Evidence
 ↓
Inspect Logs
 ↓
Inspect Request/Response
 ↓
Inspect Source Code
 ↓
Determine Root Cause
```

Klasifikasi:

```text
Application Bug
Test Script Bug
Test Case Issue
Environment Issue
Infrastructure Issue
Configuration Issue
Dependency Issue
Flaky Test
False Positive
```

---

# 36. TEST SCRIPT BUG

Jika failure disebabkan oleh JMeter/ZAP/test procedure, jangan langsung menyalahkan aplikasi.

Contoh:

```text
Expected application response
≠
Observed response

```

Periksa terlebih dahulu apakah:

* endpoint benar;
* method benar;
* authentication benar;
* session benar;
* CSRF benar;
* request body benar;
* test data benar.

Jika salah:

> Test Script Bug

Perbaiki script, kemudian ulangi execution.

---

# 37. APPLICATION BUG

Jika source code dan runtime menunjukkan defect aplikasi:

1. dokumentasikan defect;
2. simpan evidence;
3. tentukan root cause;
4. buat bug record;
5. jangan mengubah code tanpa authorization.

---

# 38. DEBUGGING RULES

AI Agent **dilarang**:

* menghapus assertion agar PASS;
* mengubah Expected Result;
* menghapus Test Case;
* menurunkan coverage;
* menonaktifkan security control agar PASS;
* mengubah Test Plan;
* mengubah requirement;
* mengubah hasil testing.

Jika code perlu diperbaiki:

```text
Identify Root Cause
       ↓
Propose Fix
       ↓
Human Approval if Required
       ↓
Apply Minimal Fix
       ↓
Run Original Test
       ↓
Retest
       ↓
Regression
```

---

# 39. RETEST

Retest dilakukan setelah defect/finding diperbaiki.

Gunakan:

* Test Case yang sama;
* Expected Result yang sama;
* kondisi pengujian yang setara;
* evidence baru.

Contoh:

```text
Original:
ST-02 = FAIL

Fix:
Authorization issue corrected

Retest:
ST-02 = PASS / FAIL
```

Status retest harus berdasarkan execution aktual.

---

# 40. REGRESSION TESTING

Setelah perubahan code, lakukan impact analysis berdasarkan:

* changed files;
* changed functions;
* changed modules;
* shared middleware;
* authentication;
* authorization;
* validation;
* database;
* file upload;
* dependencies.

Tentukan:

```text
Direct Regression
Related Regression
Broader Regression
```

Regression scope harus memiliki alasan teknis.

---

# 41. REGRESSION EVIDENCE

Gunakan identifier:

```text
REG-001
REG-002
```

Hubungkan dengan:

```text
BUG-001
↓
RETEST-001
↓
REG-001
```

Dengan demikian defect lifecycle dapat ditelusuri.

---

# 42. TEST RESULT RECORD

Setiap Test Case harus menghasilkan record:

| Field           | Value                  |
| --------------- | ---------------------- |
| Test Case ID    | PF-01 / ST-01          |
| Objective       | ...                    |
| Preconditions   | ...                    |
| Test Data       | ...                    |
| Execution Date  | actual                 |
| Environment     | verified               |
| Expected Result | from Test Case         |
| Actual Result   | actual observation     |
| Evidence        | evidence ID            |
| Status          | PASS/FAIL/BLOCKED/etc. |
| Notes           | ...                    |

Jangan mengisi actual result sebelum execution.

---

# 43. TRACEABILITY MATRIX

Buat traceability:

```text
Requirement
     ↓
Feature
     ↓
Use Case
     ↓
Test Case
     ↓
Implementation
     ↓
Execution
     ↓
Evidence
     ↓
Result
     ↓
Finding
     ↓
Retest
     ↓
Regression
```

Minimal matrix:

| Test Case | Feature | Implementation | Evidence | Result | Finding | Retest | Regression |
| --------- | ------- | -------------- | -------- | ------ | ------- | ------ | ---------- |

---

# 44. TEST REPORT

Final report harus memiliki:

## 1. Executive Summary

Ringkasan actual testing.

## 2. Test Scope

PF-01–PF-07 dan ST-01–ST-09.

## 3. Test Environment

Environment yang benar-benar digunakan.

## 4. Test Methodology

Performance + Security Testing.

## 5. Test Case Summary

Jumlah:

* PASS;
* FAIL;
* BLOCKED;
* NOT EXECUTED;
* NOT APPLICABLE.

Semua berdasarkan execution aktual.

## 6. Performance Testing Results

Masukkan:

* load;
* response time;
* throughput;
* error rate;
* request count;
* analysis.

## 7. Security Testing Results

Masukkan:

* Test Case;
* observation;
* evidence;
* finding;
* validation.

## 8. Defects

Bug yang benar-benar ditemukan.

## 9. Security Findings

Vulnerability yang benar-benar terverifikasi.

## 10. CVSS

Hanya untuk verified vulnerability.

## 11. Retest

Hasil retest aktual.

## 12. Regression

Hasil regression aktual.

## 13. Limitations

Testing yang tidak dapat dilakukan dan alasannya.

## 14. Remaining Risks

Risk yang masih tersisa berdasarkan evidence.

## 15. Recommendations

Rekomendasi berdasarkan finding aktual.

## 16. Conclusion

Kesimpulan berdasarkan actual testing.

---

# 45. TEST REPORT INTEGRITY

Final report harus mengikuti:

```text
Execution
↓
Evidence
↓
Validated Result
↓
Analysis
↓
Report
```

Bukan:

```text
Expected Result
↓
Assumption
↓
Report
```

---

# 46. STOP CONDITIONS

AI Agent harus **STOP dan meminta human approval** sebelum:

* database deletion;
* database reset yang berpotensi menghapus data;
* production migration;
* production data modification;
* destructive testing;
* security testing yang berpotensi mengganggu service;
* perubahan authentication architecture;
* perubahan authorization architecture;
* perubahan security architecture;
* perubahan requirement;
* perubahan Test Plan;
* perubahan Test Case;
* perubahan testing scope.

---

# 47. DISCREPANCY MANAGEMENT

Jika Test Plan/Test Case tidak sesuai dengan implementasi:

```text
Identify Discrepancy
        ↓
Verify Source Code
        ↓
Verify Runtime
        ↓
Document Discrepancy
        ↓
Determine Impact
        ↓
Propose Adjustment
        ↓
Human Approval if Scope Changes
```

AI Agent tidak boleh diam-diam mengubah Test Case.

---

# 48. NOT APPLICABLE RULE

Gunakan:

> NOT APPLICABLE

hanya jika terdapat alasan teknis yang dapat diverifikasi bahwa Test Case memang tidak berlaku terhadap implementation aktual.

Dokumentasikan alasan.

Jangan menggunakan NOT APPLICABLE hanya karena Test Case sulit dilakukan.

---

# 49. BLOCKED RULE

Gunakan:

> BLOCKED

jika testing secara teknis tidak dapat dilanjutkan.

Contoh:

* application unavailable;
* required test account unavailable;
* required dependency unavailable;
* authentication flow tidak dapat direplikasi;
* testing authorization belum tersedia;
* environment mengalami masalah kritis.

Dokumentasikan blocker.

---

# 50. UNVERIFIED RULE

Gunakan:

> UNVERIFIED

ketika informasi belum dapat diverifikasi.

Contoh:

```text
Route: UNVERIFIED
Middleware: UNVERIFIED
MIME Validation: UNVERIFIED
```

Jangan mengubah UNVERIFIED menjadi PASS atau FAIL.

---

# 51. TESTING COMPLETION CRITERIA

Testing hanya boleh dianggap selesai apabila:

```text
[ ] Test Plan reviewed
[ ] Test Cases reviewed
[ ] Project analyzed
[ ] Implementation mapping completed
[ ] Environment verified
[ ] Test data prepared
[ ] Testing authorization confirmed
[ ] Applicable Performance Test Cases executed
[ ] Applicable Security Test Cases executed
[ ] Evidence collected
[ ] Results validated
[ ] Failures investigated
[ ] Findings validated
[ ] Retest completed where required
[ ] Regression completed where required
[ ] Traceability completed
[ ] Final report generated
```

Jika ada item kritis yang belum selesai:

> Testing belum complete.

---

# 52. AI AGENT EXECUTION ORDER

AI Agent harus menjalankan tahapan dalam urutan:

```text
PHASE 0
Initialization
       ↓
PHASE 1
Project Reconnaissance
       ↓
PHASE 2
System Understanding
       ↓
PHASE 3
Implementation Mapping
       ↓
PHASE 4
Environment Verification
       ↓
PHASE 5
Test Data Preparation
       ↓
PHASE 6
Performance Testing
       ↓
PHASE 7
Security Testing
       ↓
PHASE 8
Failure / Finding Analysis
       ↓
PHASE 9
Fix if Authorized
       ↓
PHASE 10
Retest
       ↓
PHASE 11
Regression
       ↓
PHASE 12
Final Reporting
```

Jangan melewati Phase 1–4 hanya untuk mempercepat execution.

---

# 53. EXECUTION LOG

AI Agent harus mempertahankan execution log.

Contoh:

```text
[INIT]
Testing workspace initialized.

[RECON]
Laravel framework verified.

[MAPPING]
PF-01 route verified.

[ENV]
Application URL verified.

[PERF]
PF-01 baseline executed.

[EVIDENCE]
PERF-PF01-001 generated.

[RESULT]
PF-01 = PASS.

[SECURITY]
ST-02 executed.

[FINDING]
Potential authorization issue identified.

[VALIDATION]
Finding reproduced.

[STATUS]
Confirmed security finding.

[RETEST]
Awaiting authorized fix.
```

Log harus mencerminkan kejadian aktual.

---

# 54. FINAL QUALITY GATE

Sebelum membuat final report, AI Agent harus memeriksa:

### Data Integrity

* [ ] Tidak ada fabricated result.
* [ ] Semua Actual Result berasal dari execution.
* [ ] Semua metrics berasal dari tool output.
* [ ] Semua vulnerability telah diverifikasi.
* [ ] CVSS hanya diberikan untuk verified vulnerability.
* [ ] Evidence dapat ditelusuri.

### Scope Integrity

* [ ] PF-01–PF-07 tidak diubah tanpa approval.
* [ ] ST-01–ST-09 tidak diubah tanpa approval.
* [ ] Performance Testing tetap menggunakan JMeter.
* [ ] Security Testing tetap menggunakan WSTG/ZAP/manual testing.
* [ ] Black Box tidak digunakan sebagai metode execution penelitian.

### Change Integrity

* [ ] Tidak ada Test Case yang dihapus.
* [ ] Tidak ada Expected Result yang diubah.
* [ ] Tidak ada assertion yang dihapus untuk mendapatkan PASS.
* [ ] Tidak ada requirement yang diubah.
* [ ] Semua code fix terdokumentasi.
* [ ] Retest dilakukan setelah fix.
* [ ] Regression dilakukan berdasarkan impact.

---

# 55. FINAL AI AGENT OPERATING RULES

Jika AI Agent hanya mengingat aturan inti, gunakan aturan berikut:

```text
RULE 01
Inspect before testing.

RULE 02
Verify before assuming.

RULE 03
Test Case defines Expected Result.

RULE 04
Execution defines Actual Result.

RULE 05
Evidence is required for every result.

RULE 06
A ZAP alert is not automatically a vulnerability.

RULE 07
A failure must be reproduced before fixing.

RULE 08
A fix must be retested.

RULE 09
Relevant fixes require regression analysis.

RULE 10
Never modify a Test Case merely to obtain PASS.

RULE 11
Never fabricate metrics or evidence.

RULE 12
Never change testing scope without approval.

RULE 13
UNVERIFIED means UNVERIFIED.

RULE 14
BLOCKED means BLOCKED.

RULE 15
When destructive or scope-changing action is required,
STOP and request human approval.
```

---

# 56. QUICK REFERENCE

```text
PRE-FLIGHT
    ↓
READ TEST PLAN
    ↓
READ TEST CASE
    ↓
INSPECT PROJECT
    ↓
MAP TEST CASE → IMPLEMENTATION
    ↓
VERIFY ENVIRONMENT
    ↓
PREPARE TEST DATA
    ↓
PERFORMANCE TESTING
    ↓
SECURITY TESTING
    ↓
COLLECT EVIDENCE
    ↓
ANALYZE RESULTS
    ↓
FIND BUG / SECURITY FINDING
    ↓
REPRODUCE
    ↓
FIX IF AUTHORIZED
    ↓
RETEST
    ↓
REGRESSION
    ↓
FINAL REPORT
```

---

# 57. PRE-TEST CHECKLIST

```text
[ ] Testing Guide available
[ ] Final thesis available
[ ] Test Plan available
[ ] Test Cases available
[ ] Project source code available
[ ] Project structure analyzed
[ ] Framework verified
[ ] Actors verified
[ ] Features verified
[ ] Routes mapped
[ ] Controllers mapped
[ ] Middleware mapped
[ ] Authentication mapped
[ ] Authorization mapped
[ ] Validation mapped
[ ] Environment verified
[ ] Test accounts prepared
[ ] Dummy test data prepared
[ ] JMeter available
[ ] ZAP available
[ ] Testing authorization confirmed
```

---

# 58. PERFORMANCE CHECKLIST

```text
[ ] PF-01 mapped
[ ] PF-02 mapped
[ ] PF-03 mapped
[ ] PF-04 mapped
[ ] PF-05 mapped
[ ] PF-06 mapped
[ ] PF-07 mapped

[ ] Authentication flow understood
[ ] Session understood
[ ] CSRF requirement checked
[ ] Request parameters verified
[ ] JMeter Test Plan created
[ ] Baseline executed
[ ] 5 VU executed
[ ] 10 VU executed
[ ] 20 VU executed
[ ] Response Time collected
[ ] Throughput collected
[ ] Error Rate collected
[ ] Request Count collected
[ ] Raw results preserved
[ ] Evidence preserved
```

---

# 59. SECURITY CHECKLIST

```text
[ ] ST-01 mapped
[ ] ST-02 mapped
[ ] ST-03 mapped
[ ] ST-04 mapped
[ ] ST-05 mapped
[ ] ST-06 mapped
[ ] ST-07 mapped
[ ] ST-08 mapped
[ ] ST-09 mapped

[ ] ZAP configured
[ ] Browser configured
[ ] Application explored
[ ] Passive scan completed
[ ] Alerts reviewed
[ ] Relevant alerts manually verified
[ ] Active scan authorized before execution
[ ] Authentication tested
[ ] Client authorization tested
[ ] Admin authorization tested
[ ] Legal Staff authorization tested
[ ] Session tested
[ ] Input validation tested
[ ] File upload tested
[ ] File re-upload tested
[ ] Security configuration reviewed
[ ] Findings validated
[ ] CVSS assigned only to verified findings
```

---

# 60. POST-TEST CHECKLIST

```text
[ ] All applicable Test Cases executed
[ ] PASS/FAIL/BLOCKED status assigned
[ ] Expected Result preserved
[ ] Actual Result documented
[ ] Evidence collected
[ ] Failures reproduced
[ ] Root causes analyzed
[ ] Security findings validated
[ ] Bugs documented
[ ] Retest completed where required
[ ] Regression completed where required
[ ] Traceability matrix completed
[ ] Evidence index completed
[ ] Limitations documented
[ ] Remaining risks documented
[ ] Final report generated
```

---

# 61. FINAL OUTPUTS

Pada akhir proses, AI Agent harus menghasilkan artefak berikut sesuai kebutuhan execution aktual:

```text
01. Implementation Mapping
02. Test Execution Records
03. JMeter .jmx Files
04. Performance Raw Results
05. Performance Reports
06. Security/ZAP Reports
07. Security Evidence
08. Bug Records
09. Security Finding Records
10. Retest Records
11. Regression Records
12. Traceability Matrix
13. Evidence Index
14. Final Test Report
```

Tidak semua artefak boleh dianggap tersedia sebelum benar-benar dibuat.

---

# 62. FINAL INSTRUCTION TO AI AGENT

```text
You are now operating as the QA testing agent for the TNY Law Firm project.

Your first task is NOT to execute the tests.

Your first task is to understand the project and verify the testing scope.

Follow this sequence:

1. Read this Testing Guide.
2. Locate the approved Test Plan.
3. Locate all approved Test Cases.
4. Inspect the project repository.
5. Identify the actual framework, architecture, routes,
   controllers, middleware, authentication, authorization,
   validation, database, and file upload implementation.
6. Create an implementation mapping for PF-01 through PF-07
   and ST-01 through ST-09.
7. Mark anything that cannot be verified as UNVERIFIED.
8. Identify any discrepancy between the Test Plan/Test Case
   and actual implementation.
9. Do NOT execute Performance or Security Testing until
   implementation mapping is sufficiently complete.
10. Do NOT fabricate missing information.

After implementation mapping is completed:

11. Verify the test environment.
12. Prepare isolated test accounts and dummy test data.
13. Execute Performance Testing using Apache JMeter
    according to PF-01 through PF-07.
14. Use the approved load progression:
    Baseline → 5 VU → 10 VU → 20 VU.
15. Record actual Response Time, Throughput, Error Rate,
    and Request Count from JMeter.
16. Preserve raw results and evidence.

Then:

17. Execute Security Testing according to ST-01 through ST-09.
18. Use OWASP WSTG, OWASP ZAP, manual testing,
    and Browser/DevTools where appropriate.
19. Verify ZAP findings manually.
20. Do not treat ZAP alerts automatically as vulnerabilities.
21. Assign CVSS v4.0 only to verified vulnerabilities.

For every failure:

22. Reproduce the failure.
23. Determine whether it is an application bug,
    test script issue, environment issue, configuration issue,
    dependency issue, flaky test, or false positive.
24. Do not modify the test to make it PASS.
25. If a code fix is required, request authorization when required.
26. Apply only the minimal justified fix.
27. Retest the original Test Case.
28. Perform regression testing based on impact.

Throughout the entire process:

29. Never fabricate results.
30. Never fabricate metrics.
31. Never fabricate evidence.
32. Never fabricate vulnerabilities.
33. Never fabricate CVSS scores.
34. Never change Expected Result.
35. Never delete failing tests.
36. Never silently change Test Plan or Test Case.
37. Never perform destructive operations without authorization.
38. Protect credentials and sensitive data.
39. Clearly distinguish:
    - Expected Result
    - Actual Result
    - Evidence
    - Analysis
40. Use PASS, FAIL, BLOCKED, NOT EXECUTED,
    and NOT APPLICABLE only according to the rules in this guide.

When a task cannot be verified, say UNVERIFIED.

When a task cannot proceed, say BLOCKED and explain why.

When a scope, requirement, architecture, or destructive operation
requires a human decision, STOP and request human approval.

The final report must be based exclusively on actual execution
and validated evidence.

Do not begin by generating a test report.

Begin with:

PHASE 0 — INITIALIZATION
followed by
PHASE 1 — PROJECT RECONNAISSANCE
and
PHASE 2 — SYSTEM UNDERSTANDING.

After completing those phases, report the findings and wait for
the next authorized execution step if the workflow requires
human review.
```

---

## Catatan penting untuk penggunaan di Antigravity

Saya sengaja membuat **instruction terakhir tidak menyuruh agent langsung menjalankan seluruh testing dalam satu langkah**. Untuk project skripsi Anda, ini lebih aman.

Urutan execution yang saya rekomendasikan di Antigravity adalah:

```text
STEP 1
AI membaca Testing Guide
        ↓
STEP 2
Project Reconnaissance
        ↓
STEP 3
Implementation Mapping PF/ST
        ↓
STEP 4
Human Review terhadap Mapping
        ↓
STEP 5
Performance Testing
        ↓
STEP 6
Security Testing
        ↓
STEP 7
Fix + Retest + Regression jika diperlukan
        ↓
STEP 8
Final Evidence + Report
```