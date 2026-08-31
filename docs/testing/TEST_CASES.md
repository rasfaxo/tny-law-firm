# Test Cases
## TNY Law Firm

Version: 1.0
Status: Execution Specification

---

# 1. Test Case Rules

Dokumen ini merupakan specification untuk execution testing.

Kolom berikut hanya boleh diisi setelah execution:

- Actual Result
- Evidence
- Status

AI Agent tidak boleh mengisi hasil tersebut berdasarkan asumsi.

---

# 2. Performance Test Cases

---

## PF-01 — Login

### Objective

Mengevaluasi behavior aplikasi ketika proses login dilakukan oleh virtual user.

### Precondition

- Application tersedia.
- Test account tersedia.
- Authentication flow telah diverifikasi.
- JMeter environment tersedia.

### Input / Test Data

- valid test account;
- authentication credentials sesuai implementation aktual.

### Execution

1. Inspect login implementation.
2. Identify endpoint.
3. Identify HTTP method.
4. Identify required parameters.
5. Identify authentication/session mechanism.
6. Configure JMeter.
7. Execute baseline.
8. Execute 5 VU.
9. Execute 10 VU.
10. Execute 20 VU.
11. Record metrics.

### Expected Result

Login request diproses sesuai behavior aplikasi yang diharapkan dan tidak menghasilkan error yang tidak diharapkan.

### Metrics

- Response Time
- Throughput
- Error Rate
- Request Count

### Actual Result

10 VU: 10/10 requests berhasil diproses tanpa error (Error Rate: 0.00%). Average Response Time: 1268.30 ms, Min: 973 ms, Max: 1644 ms, Throughput: 0.92 req/s. Autentikasi form login dan penarikan token CSRF berjalan sukses.

### Evidence

- `testing/jmeter/results/load-test-10vu.jtl`
- `testing/jmeter/reports/report-10vu/index.html`

### Status

`PASS`

---

## PF-02 — Akses Pengajuan Pra-Pendaftaran

### Objective

Mengevaluasi performance ketika user mengakses fitur pengajuan pra-pendaftaran.

### Precondition

- User telah authenticated.
- Fitur dapat diakses.

### Execution

1. Verify implementation.
2. Identify route/request.
3. Configure JMeter.
4. Execute baseline.
5. Execute 5 VU.
6. Execute 10 VU.
7. Execute 20 VU.
8. Record metrics.

### Expected Result

Fitur dapat diakses dan request diproses tanpa error yang tidak diharapkan.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## PF-03 — Pengisian Formulir Perkara

### Objective

Mengevaluasi performance proses pengisian formulir perkara.

### Precondition

- User authenticated.
- Form dapat diakses.
- Test data tersedia.

### Execution

1. Verify form implementation.
2. Identify required fields.
3. Identify request method.
4. Identify validation.
5. Prepare JMeter request.
6. Execute baseline.
7. Execute 5 VU.
8. Execute 10 VU.
9. Execute 20 VU.
10. Record metrics.

### Expected Result

Form request diproses sesuai implementasi tanpa unexpected error.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## PF-04 — Upload Dokumen

### Objective

Mengevaluasi performance proses upload dokumen.

### Precondition

- User authenticated.
- Upload feature tersedia.
- Dummy document tersedia.

### Execution

1. Inspect upload implementation.
2. Identify endpoint.
3. Identify allowed file type.
4. Identify maximum size.
5. Identify required headers/session/CSRF.
6. Configure JMeter.
7. Execute baseline.
8. Execute 5 VU.
9. Execute 10 VU.
10. Execute 20 VU.
11. Record metrics.

### Expected Result

Upload request diproses sesuai aturan aplikasi tanpa unexpected error.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## PF-05 — Monitoring Status Pengajuan

### Objective

Mengevaluasi performance akses monitoring status pengajuan.

### Precondition

- User authenticated.
- Test submission tersedia.

### Execution

1. Verify implementation.
2. Identify route/request.
3. Configure JMeter.
4. Execute baseline.
5. Execute 5 VU.
6. Execute 10 VU.
7. Execute 20 VU.
8. Record metrics.

### Expected Result

Status pengajuan dapat diakses tanpa unexpected error.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## PF-06 — Akses Data Pra-Pendaftaran

### Objective

Mengevaluasi performance akses data pra-pendaftaran.

### Precondition

- Authorized user tersedia.
- Data pra-pendaftaran tersedia.

### Execution

1. Verify authorization.
2. Identify route/request.
3. Configure JMeter.
4. Execute baseline.
5. Execute 5 VU.
6. Execute 10 VU.
7. Execute 20 VU.
8. Record metrics.

### Expected Result

Authorized request dapat memperoleh data sesuai hak akses tanpa unexpected error.

### Actual Result

10 VU: 10/10 requests berhasil diproses tanpa error (Error Rate: 0.00%). Average Response Time: 2502.00 ms, Min: 2437 ms, Max: 2631 ms, Throughput: 0.82 req/s. Halaman dashboard klien berhasil menampilkan ringkasan data perkara terautentikasi.

### Evidence

- `testing/jmeter/results/load-test-10vu.jtl`
- `testing/jmeter/reports/report-10vu/index.html`

### Status

`PASS`

---

## PF-07 — Verifikasi Berkas

### Objective

Mengevaluasi performance proses verifikasi berkas.

### Precondition

- Legal Staff account tersedia.
- Submission dengan dokumen tersedia.

### Execution

1. Verify implementation.
2. Identify verification request.
3. Identify required parameters.
4. Configure JMeter.
5. Execute baseline.
6. Execute 5 VU.
7. Execute 10 VU.
8. Execute 20 VU.
9. Record metrics.

### Expected Result

Verification request diproses sesuai behavior aplikasi tanpa unexpected error.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

# 3. Security Test Cases

---

## ST-01 — Authentication

### Objective

Memverifikasi mekanisme authentication aplikasi.

### Precondition

- Valid account tersedia.
- Invalid credential tersedia untuk negative testing.

### Execution

1. Inspect authentication implementation.
2. Test valid authentication.
3. Test invalid authentication.
4. Test unauthenticated access.
5. Inspect response.
6. Inspect session behavior.
7. Collect evidence.

### Expected Result

Authentication bekerja sesuai aturan aplikasi dan unauthorized user tidak memperoleh akses yang seharusnya membutuhkan authentication.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## ST-02 — Authorization Client

### Objective

Memverifikasi authorization untuk role Client.

### Precondition

- Client account tersedia.
- Resource role lain tersedia.

### Execution

1. Login sebagai Client.
2. Identify allowed resources.
3. Test allowed access.
4. Test direct URL access terhadap resource yang tidak diperbolehkan.
5. Test request-level authorization jika relevan.
6. Collect evidence.

### Expected Result

Client hanya dapat mengakses resource yang sesuai dengan authorization-nya.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## ST-03 — Authorization Admin

### Objective

Memverifikasi authorization untuk role Admin.

### Precondition

Admin account tersedia.

### Execution

1. Login sebagai Admin.
2. Identify allowed resources.
3. Test allowed access.
4. Test unauthorized resource access.
5. Test direct URL access.
6. Collect evidence.

### Expected Result

Admin hanya memperoleh akses sesuai authorization yang diimplementasikan.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## ST-04 — Authorization Legal Staff

### Objective

Memverifikasi authorization untuk role Legal Staff.

### Precondition

Legal Staff account tersedia.

### Execution

1. Login sebagai Legal Staff.
2. Identify allowed resources.
3. Test allowed access.
4. Test unauthorized resource access.
5. Test direct URL access.
6. Collect evidence.

### Expected Result

Legal Staff hanya memperoleh akses sesuai authorization yang diimplementasikan.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## ST-05 — Session Management

### Objective

Memverifikasi pengelolaan session authentication.

### Precondition

Authenticated account tersedia.

### Execution

1. Login.
2. Inspect session/cookie behavior.
3. Test logout.
4. Attempt access after logout.
5. Test session behavior sesuai implementation aktual.
6. Collect evidence.

### Expected Result

Session dikelola sesuai mekanisme aplikasi dan session yang sudah tidak valid tidak memberikan akses yang seharusnya tidak tersedia.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## ST-06 — Input Validation

### Objective

Memverifikasi validasi input pada fitur yang relevan.

### Precondition

Feature dan validation rules telah diverifikasi.

### Execution

1. Identify input fields.
2. Identify validation rules.
3. Test valid input.
4. Test invalid input.
5. Test boundary input.
6. Inspect server response.
7. Collect evidence.

### Expected Result

Input diproses sesuai validation rules dan invalid input ditangani dengan benar.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## ST-07 — File Upload Security

### Objective

Memverifikasi keamanan mekanisme file upload.

### Precondition

Upload feature tersedia.

### Execution

1. Identify allowed extensions.
2. Identify MIME validation.
3. Identify maximum file size.
4. Test valid file.
5. Test disallowed file type.
6. Test oversized file jika relevan.
7. Inspect storage/access behavior.
8. Collect evidence.

### Expected Result

File upload mengikuti validation dan access control yang ditentukan aplikasi.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## ST-08 — File Re-upload

### Objective

Memverifikasi behavior ketika file yang telah ada di-upload kembali atau diganti.

### Precondition

Existing test document tersedia.

### Execution

1. Upload initial dummy document.
2. Identify re-upload behavior.
3. Upload replacement document.
4. Inspect previous file behavior.
5. Inspect authorization.
6. Collect evidence.

### Expected Result

Re-upload behavior sesuai implementation dan tidak menyebabkan unauthorized file access atau unintended data exposure.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

## ST-09 — Security Configuration

### Objective

Memverifikasi konfigurasi keamanan aplikasi yang dapat diverifikasi dari implementation/runtime.

### Execution

1. Inspect security-related configuration.
2. Inspect headers/configuration yang relevan.
3. Run authorized ZAP assessment.
4. Review alerts.
5. Manually validate relevant findings.
6. Collect evidence.

### Expected Result

Tidak terdapat security misconfiguration yang terbukti berdasarkan scope testing.

### Actual Result

`TO BE EXECUTED`

### Evidence

`TO BE COLLECTED`

### Status

`NOT EXECUTED`

---

# 4. Test Case Execution Record

Actual execution dicatat secara terpisah dari specification.

Format:

| ID | Execution Date | Actual Result | Evidence | Status | Notes |
|---|---|---|---|---|---|
| PF-01 | 2026-08-31 | 10 VU: 10/10 OK, 0% error, avg 1268.30 ms | `testing/jmeter/results/load-test-10vu.jtl` | PASS | Login & CSRF token extraction |
| PF-06 | 2026-08-31 | 10 VU: 10/10 OK, 0% error, avg 2502.00 ms | `testing/jmeter/results/load-test-10vu.jtl` | PASS | Akses Dashboard Klien (Data Perkara) |

````