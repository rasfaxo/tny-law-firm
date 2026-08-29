# Test Plan
## TNY Law Firm

Version: 1.0
Status: Approved Testing Scope

---

# 1. Testing Objective

Testing dilakukan untuk mengevaluasi implementasi sistem TNY Law Firm berdasarkan aspek:

1. Performance;
2. Security.

Testing tidak bertujuan membuat hasil pengujian sebelum execution dilakukan.

Actual result hanya diperoleh melalui execution aktual.

---

# 2. Testing Scope

## 2.1 Performance Testing

Tool:

**Apache JMeter**

Test Case:

| ID | Test Scenario |
|---|---|
| PF-01 | Login |
| PF-02 | Akses pengajuan pra-pendaftaran |
| PF-03 | Pengisian formulir perkara |
| PF-04 | Upload dokumen |
| PF-05 | Monitoring status pengajuan |
| PF-06 | Akses data pra-pendaftaran |
| PF-07 | Verifikasi berkas |

Load:

```text
Baseline
5 Virtual Users
10 Virtual Users
20 Virtual Users
````

Metrics:

* Response Time;
* Throughput;
* Error Rate;
* Request Count.

---

# 3. Performance Methodology

Metode utama:

**Progressive Load Testing**

Urutan:

```text
Baseline
↓
5 VU
↓
10 VU
↓
20 VU
```

Setiap tahap digunakan untuk melihat behavior aplikasi ketika jumlah virtual user meningkat.

Actual metrics harus berasal dari Apache JMeter.

Tidak diperbolehkan membuat angka hasil secara manual.

---

# 4. Security Testing

Security Testing menggunakan:

* OWASP WSTG;
* OWASP ZAP;
* Manual Testing;
* Browser;
* DevTools.

Test Case:

| ID    | Test Scenario             |
| ----- | ------------------------- |
| ST-01 | Authentication            |
| ST-02 | Authorization Client      |
| ST-03 | Authorization Admin       |
| ST-04 | Authorization Legal Staff |
| ST-05 | Session Management        |
| ST-06 | Input Validation          |
| ST-07 | File Upload Security      |
| ST-08 | File Re-upload            |
| ST-09 | Security Configuration    |

---

# 5. Security Methodology

Security testing dilakukan melalui kombinasi:

### Manual Testing

Digunakan untuk:

* authentication;
* authorization;
* session;
* input validation;
* file behavior;
* access control.

### Automated Assistance

OWASP ZAP digunakan untuk membantu:

* passive scanning;
* vulnerability discovery;
* request inspection;
* security alert detection.

Alert ZAP harus diverifikasi secara manual.

---

# 6. Test Environment

Environment harus diverifikasi sebelum execution.

Dokumentasikan:

| Item            | Value | Status     |
| --------------- | ----- | ---------- |
| Application URL | TBD   | UNVERIFIED |
| Environment     | TBD   | UNVERIFIED |
| Browser         | TBD   | UNVERIFIED |
| Server          | TBD   | UNVERIFIED |
| Database        | TBD   | UNVERIFIED |
| JMeter          | TBD   | UNVERIFIED |
| OWASP ZAP       | TBD   | UNVERIFIED |

Nilai `TBD` tidak boleh dianggap sebagai hasil final.

---

# 7. Test Data

Test data menggunakan:

* Client account;
* Admin account;
* Legal Staff account;
* dummy case;
* dummy documents.

Data harus:

* reproducible;
* isolated;
* traceable;
* non-production.

---

# 8. Testing Workflow

```text
Project Reconnaissance
↓
Implementation Mapping
↓
Environment Preparation
↓
Performance Testing
↓
Security Testing
↓
Evidence Collection
↓
Result Analysis
↓
Bug / Finding Analysis
↓
Retest
↓
Regression
↓
Final Report
```

---

# 9. Scope Change Policy

Test Plan tidak boleh diubah hanya karena implementasi tidak sesuai Test Case.

Jika ditemukan discrepancy:

```text
Identify
↓
Verify
↓
Document
↓
Assess Impact
↓
Propose Adjustment
↓
Human Approval
```

---

# 10. Completion Criteria

Testing dianggap selesai apabila:

* semua applicable Test Case telah dieksekusi;
* semua status terdokumentasi;
* evidence tersedia;
* failure dianalisis;
* security finding divalidasi;
* regression relevan selesai;
* final report dibuat.

````