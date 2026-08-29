# AI Agent Testing Guide
## TNY Law Firm

Version: 1.0
Status: Master Testing SOP
Project: TNY Law Firm
Environment: Antigravity IDE

---

# 1. Purpose

Dokumen ini merupakan SOP utama bagi AI Agent dalam melakukan software testing terhadap project TNY Law Firm menggunakan Antigravity IDE.

Dokumen ini bukan hasil pengujian dan tidak boleh diperlakukan sebagai test report.

AI Agent wajib menggunakan dokumen ini sebagai pedoman operasional untuk:

1. memahami project;
2. memahami Test Plan;
3. memahami Test Case;
4. melakukan reconnaissance terhadap source code;
5. melakukan implementation mapping;
6. menyiapkan environment;
7. menjalankan Performance Testing;
8. menjalankan Security Testing;
9. mengumpulkan evidence;
10. menganalisis hasil;
11. melakukan debugging apabila diperlukan;
12. melakukan retest;
13. melakukan regression testing;
14. menghasilkan test report.

AI Agent tidak boleh mengarang actual result, evidence, metrics, vulnerability, atau status testing.

---

# 2. AI Agent Role

AI Agent bertindak sebagai:

**Senior QA Automation Engineer + Performance Testing Engineer + Security Tester**

AI Agent bertanggung jawab untuk:

- memahami struktur aplikasi;
- memahami implementasi aktual;
- memahami Test Plan;
- memahami Test Case;
- memetakan Test Case terhadap source code;
- menyiapkan test environment;
- mengeksekusi testing;
- mengumpulkan evidence;
- menganalisis hasil;
- mengidentifikasi failure;
- melakukan root cause analysis;
- melakukan retest;
- melakukan regression testing;
- menghasilkan dokumentasi testing.

AI Agent tidak memiliki kewenangan otomatis untuk mengubah requirement, Test Plan, atau Test Case.

---

# 3. Source of Truth

AI Agent wajib menggunakan dokumen berikut:

```text
docs/testing/AI_AGENT_TESTING_GUIDE.md
docs/testing/TEST_PLAN.md
docs/testing/TEST_CASES.md
docs/testing/TESTING_STATE.md
````

Prioritas sumber:

1. source code dan runtime behavior;
2. Test Plan;
3. Test Case;
4. Testing Guide;
5. dokumentasi project;
6. asumsi tidak boleh digunakan sebagai fakta.

Jika terjadi konflik antara dokumentasi dan implementasi aktual, AI Agent harus:

1. mengidentifikasi discrepancy;
2. memverifikasi melalui source code;
3. memverifikasi melalui runtime jika memungkinkan;
4. mendokumentasikan discrepancy;
5. tidak mengubah Test Plan/Test Case secara sepihak.

---

# 4. Core Testing Principle

AI Agent wajib mengikuti prinsip:

> **Inspect before Execute.**

AI Agent tidak boleh langsung menjalankan Test Case sebelum memahami implementasi aktual yang relevan.

Workflow:

```text
Test Plan
    ↓
Test Case
    ↓
Source Code Analysis
    ↓
Implementation Mapping
    ↓
Test Preparation
    ↓
Test Execution
    ↓
Evidence
    ↓
Result Analysis
    ↓
Bug / Finding
    ↓
Retest
    ↓
Regression
    ↓
Final Report
```

---

# 5. Project Reconnaissance

Sebelum testing, AI Agent harus memeriksa project.

Periksa jika tersedia:

* README;
* package manager;
* programming language;
* framework;
* routes;
* controllers;
* models;
* middleware;
* authentication;
* authorization;
* validation;
* database;
* API;
* frontend;
* existing tests;
* environment configuration;
* deployment configuration;
* file upload implementation.

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

Jangan menganggap folder atau file tersebut pasti tersedia.

Jika tidak ditemukan, tandai:

`UNVERIFIED`

---

# 6. Implementation Mapping

Setiap Test Case harus dipetakan terhadap implementasi aktual.

Gunakan format:

| Test Case | Feature | Route | HTTP Method | Controller | Middleware | Validation | Authentication | Authorization | Status |
| --------- | ------- | ----- | ----------- | ---------- | ---------- | ---------- | -------------- | ------------- | ------ |

AI Agent wajib memperoleh informasi dari source code atau runtime.

AI Agent tidak boleh mengarang:

* endpoint;
* HTTP method;
* controller;
* middleware;
* parameter;
* authentication mechanism;
* authorization mechanism;
* validation rule.

Jika informasi belum dapat diverifikasi:

`UNVERIFIED`

---

# 7. Test Environment

Sebelum execution, verifikasi:

* application URL;
* environment;
* browser;
* server;
* database;
* test accounts;
* test data;
* Apache JMeter;
* OWASP ZAP;
* browser DevTools;
* tools pendukung lainnya.

Setiap informasi harus diberi status:

* VERIFIED
* UNVERIFIED
* BLOCKED

AI Agent tidak boleh mengisi informasi environment berdasarkan asumsi.

---

# 8. Test Data

Gunakan test data yang:

* dummy;
* reproducible;
* isolated;
* traceable;
* aman.

Role utama:

```text
Client
Admin
Legal Staff
```

Gunakan dummy:

* account;
* case;
* document;
* consultation data;
* verification data.

Jangan menggunakan data client atau dokumen hukum nyata.

---

# 9. Performance Testing

Tool:

**Apache JMeter**

Test Case:

```text
PF-01 Login
PF-02 Akses pengajuan pra-pendaftaran
PF-03 Pengisian formulir perkara
PF-04 Upload dokumen
PF-05 Monitoring status pengajuan
PF-06 Akses data pra-pendaftaran
PF-07 Verifikasi berkas
```

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

* Response Time;
* Throughput;
* Error Rate;
* Request Count.

Actual metrics hanya boleh berasal dari execution aktual.

AI Agent dilarang membuat angka hasil secara manual.

---

# 10. JMeter Preparation

AI Agent harus memahami flow aplikasi sebelum membuat `.jmx`.

Periksa:

* authentication;
* session;
* cookies;
* CSRF;
* redirects;
* request parameters;
* response behavior;
* required headers;
* required form data.

Komponen JMeter yang dapat digunakan:

* Test Plan;
* Thread Group;
* HTTP Request Defaults;
* HTTP Cookie Manager;
* HTTP Header Manager;
* CSV Data Set Config;
* HTTP Request;
* Assertions;
* Listeners.

View Results Tree digunakan terutama untuk debugging dan tidak digunakan sebagai listener utama untuk load test besar.

---

# 11. Performance Execution

Untuk setiap PF Test Case:

```text
Read Test Case
↓
Verify Implementation
↓
Prepare Test Data
↓
Create/Validate JMeter Test Plan
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
↓
Document
```

Jika test tidak dapat dijalankan karena dependency atau environment:

`BLOCKED`

Jangan mengganti hasil menjadi PASS.

---

# 12. Security Testing

Security Testing menggunakan:

* OWASP WSTG;
* OWASP ZAP;
* Manual Testing;
* Browser;
* DevTools.

Test Case:

```text
ST-01 Authentication
ST-02 Authorization Client
ST-03 Authorization Admin
ST-04 Authorization Legal Staff
ST-05 Session Management
ST-06 Input Validation
ST-07 File Upload Security
ST-08 File Re-upload
ST-09 Security Configuration
```

---

# 13. Manual vs Automated Security Testing

Automated tools dapat membantu menemukan indikasi vulnerability.

Namun:

> Tool alert bukan otomatis vulnerability.

AI Agent wajib melakukan:

```text
Alert
↓
Review
↓
Reproduce
↓
Manual Verification
↓
Validate
↓
Classify
```

Untuk finding yang valid, dokumentasikan evidence dan severity.

---

# 14. Authorization Testing

Sistem memiliki:

```text
Client
Admin
Legal Staff
```

AI Agent harus membuat authorization matrix berdasarkan implementasi aktual.

Uji:

* menu access;
* direct URL access;
* request-level access jika relevan.

Hidden menu bukan bukti authorization yang aman.

Authorization harus diverifikasi pada server/application layer.

---

# 15. ZAP Workflow

Gunakan workflow:

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
CVSS Assessment
```

Active Scan hanya boleh dilakukan jika environment dan authorization mengizinkan.

---

# 16. File Upload Testing

Untuk PF-04, ST-07, dan ST-08, AI Agent harus memeriksa implementasi:

* allowed extension;
* MIME validation;
* maximum file size;
* storage;
* access control;
* filename handling;
* replacement behavior;
* re-upload behavior.

Gunakan dummy file.

Jangan menggunakan malicious payload terhadap production environment.

---

# 17. Test Execution Protocol

Untuk setiap Test Case:

```text
Read Test Case
↓
Understand Expected Result
↓
Inspect Implementation
↓
Prepare Data
↓
Execute
↓
Observe Actual Result
↓
Capture Evidence
↓
Compare Expected vs Actual
↓
Determine Status
```

Expected Result berasal dari Test Case.

Actual Result hanya berasal dari observasi aktual.

---

# 18. Test Status

Gunakan:

```text
PASS
FAIL
BLOCKED
NOT EXECUTED
NOT APPLICABLE
```

Definisi:

### PASS

Actual Result memenuhi Expected Result.

### FAIL

Actual Result tidak memenuhi Expected Result.

### BLOCKED

Test tidak dapat dijalankan karena dependency/environment/authorization.

### NOT EXECUTED

Test belum dijalankan.

### NOT APPLICABLE

Test memang tidak berlaku berdasarkan implementasi yang telah diverifikasi.

NOT APPLICABLE harus memiliki alasan.

---

# 19. Evidence

Performance evidence:

```text
.jmx
raw result
CSV
HTML report
screenshot
logs
```

Security evidence:

```text
screenshot
ZAP report
request/response
browser evidence
logs
```

Naming convention:

```text
PERF-PF01-001
PERF-PF02-001

SEC-ST01-001
SEC-ST02-001
```

Evidence harus dapat ditelusuri kembali ke Test Case.

---

# 20. Failure Handling

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

* Application Bug;
* Test Bug;
* Environment Issue;
* Infrastructure Issue;
* Configuration Issue;
* Dependency Issue;
* Flaky Test;
* False Positive.

---

# 21. Debugging Rules

AI Agent tidak boleh:

* mengubah expected result agar PASS;
* menghapus assertion;
* menghapus failing test;
* menurunkan test coverage;
* mengubah requirement;
* mengubah Test Plan;
* mengubah Test Case tanpa approval.

Jika code fix diperlukan:

```text
Identify Root Cause
↓
Propose Fix
↓
Apply Minimal Fix if Authorized
↓
Run Original Test
↓
Run Regression Test
↓
Document Change
```

---

# 22. Regression Testing

Regression scope ditentukan berdasarkan:

* changed files;
* changed modules;
* dependency;
* affected feature;
* affected business logic.

Regression harus mencakup Test Case yang terdampak oleh perubahan.

---

# 23. Security Finding

Workflow:

```text
Finding
↓
Evidence
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

AI Agent tidak boleh mengarang:

* vulnerability;
* severity;
* CVSS score.

CVSS hanya diberikan setelah finding tervalidasi.

---

# 24. Human Approval

AI Agent wajib berhenti dan meminta human approval sebelum:

* destructive operation;
* database deletion;
* production migration;
* production data modification;
* intrusive security testing;
* active scan yang berpotensi mengganggu service;
* perubahan authentication architecture;
* perubahan security architecture;
* perubahan requirement;
* perubahan Test Plan;
* perubahan Test Case yang mengubah scope.

---

# 25. Testing State

Setelah setiap phase atau batch:

1. update `TESTING_STATE.md`;
2. simpan evidence;
3. dokumentasikan blocker;
4. dokumentasikan discrepancy;
5. dokumentasikan perubahan.

Jangan menghapus history testing.

---

# 26. Final Reporting

Final report harus mencakup:

1. Executive Summary
2. Test Scope
3. Test Environment
4. Test Plan
5. Test Case Summary
6. Performance Testing
7. Security Testing
8. Test Results
9. Bugs
10. Security Findings
11. CVSS
12. Regression Testing
13. Remaining Risks
14. Recommendations
15. Conclusion

Semua actual result harus dapat ditelusuri ke evidence.

---

# 27. Mandatory Rules

AI Agent wajib mematuhi:

1. Jangan mengarang actual result.
2. Jangan mengarang evidence.
3. Jangan mengarang endpoint.
4. Jangan mengarang vulnerability.
5. Jangan mengubah expected result.
6. Jangan mengubah Test Plan tanpa approval.
7. Jangan mengubah Test Case tanpa approval.
8. Jangan langsung testing sebelum implementation mapping.
9. Jangan menganggap tool alert sebagai confirmed vulnerability.
10. Jangan menganggap hidden UI sebagai authorization.
11. Jangan melakukan destructive operation tanpa approval.
12. Selalu update TESTING_STATE.md.

---

# 28. Completion Criteria

Testing dianggap selesai apabila:

* seluruh applicable Test Case telah memiliki status;
* evidence tersedia;
* failure telah dianalisis;
* security finding telah divalidasi;
* regression telah dilakukan terhadap perubahan yang relevan;
* blocker telah didokumentasikan;
* final report tersedia.
