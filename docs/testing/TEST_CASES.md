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

- **5 VU:** 5/5 requests berhasil (Error: 0.00%). Avg Response Time: 1300.60 ms, Min: 1055 ms, Max: 1528 ms, Throughput: 1.69 req/s.
- **10 VU:** 10/10 requests berhasil (Error: 0.00%). Avg Response Time: 1268.30 ms, Min: 973 ms, Max: 1644 ms, Throughput: 0.92 req/s.
- **20 VU:** 20/20 requests berhasil (Error: 0.00%). Avg Response Time: 1893.05 ms, Min: 1056 ms, Max: 3491 ms, Throughput: 0.84 req/s.

Autentikasi form login dan penarikan token CSRF berjalan stabil dan sukses di semua tingkatan beban.

### Evidence

- `testing/jmeter/results/load-test-5vu.jtl`, `testing/jmeter/reports/report-5vu/index.html`
- `testing/jmeter/results/load-test-10vu.jtl`, `testing/jmeter/reports/report-10vu/index.html`
- `testing/jmeter/results/load-test-20vu.jtl`, `testing/jmeter/reports/report-20vu/index.html`

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

- **5 VU:** 5/5 requests berhasil (Error: 0.00%). Avg Response Time: 1848.40 ms, Min: 1021 ms, Max: 2307 ms, Throughput: 1.92 req/s.
- **10 VU:** 10/10 requests berhasil (Error: 0.00%). Avg Response Time: 1960.80 ms, Min: 1019 ms, Max: 3367 ms, Throughput: 0.62 req/s.
- **20 VU:** 20/20 requests berhasil (Error: 0.00%). Avg Response Time: 4094.75 ms, Min: 1002 ms, Max: 6935 ms, Throughput: 0.50 req/s.

Halaman formulir pra-pendaftaran perkara dan data kategori perkara berhasil dimuat tanpa error.

### Evidence

- `testing/jmeter/results/load-test-klien-5vu.jtl`, `testing/jmeter/reports/report-klien-5vu/index.html`
- `testing/jmeter/results/load-test-klien-10vu.jtl`, `testing/jmeter/reports/report-klien-10vu/index.html`
- `testing/jmeter/results/load-test-klien-20vu.jtl`, `testing/jmeter/reports/report-klien-20vu/index.html`

### Status

`PASS`

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

- **5 VU:** 5/5 submissions berhasil (Error: 0.00%). Avg Response Time: 4008.40 ms (POST) / 6984.40 ms (Total with redirect), Throughput: 1.22 req/s.
- **10 VU:** 10/10 submissions berhasil (Error: 0.00%). Avg Response Time: 3417.20 ms (POST) / 7628.20 ms (Total with redirect), Throughput: 0.54 req/s.
- **20 VU:** 20/20 submissions berhasil (Error: 0.00%). Avg Response Time: 5113.45 ms (POST) / 10953.35 ms (Total with redirect), Throughput: 0.45 req/s.

Data perkara baru beserta dokumen awal berhasil tersimpan ke database dan file storage secara konsisten.

### Evidence

- `testing/jmeter/results/load-test-klien-5vu.jtl`, `testing/jmeter/reports/report-klien-5vu/index.html`
- `testing/jmeter/results/load-test-klien-10vu.jtl`, `testing/jmeter/reports/report-klien-10vu/index.html`
- `testing/jmeter/results/load-test-klien-20vu.jtl`, `testing/jmeter/reports/report-klien-20vu/index.html`

### Status

`PASS`

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

- **5 VU:** 5/5 uploads berhasil (Error: 0.00%). Avg Response Time: 1515.00 ms (POST) / 4043.40 ms (Total with redirect), Throughput: 2.48 req/s.
- **10 VU:** 10/10 uploads berhasil (Error: 0.00%). Avg Response Time: 2804.90 ms (POST) / 6720.90 ms (Total with redirect), Throughput: 0.55 req/s.
- **20 VU:** 20/20 uploads berhasil (Error: 0.00%). Avg Response Time: 4701.40 ms (POST) / 10390.40 ms (Total with redirect), Throughput: 0.42 req/s.

Unggah dokumen tambahan multipart berhasil divalidasi dan disimpan ke storage tanpa kegagalan transfer.

### Evidence

- `testing/jmeter/results/load-test-klien-5vu.jtl`, `testing/jmeter/reports/report-klien-5vu/index.html`
- `testing/jmeter/results/load-test-klien-10vu.jtl`, `testing/jmeter/reports/report-klien-10vu/index.html`
- `testing/jmeter/results/load-test-klien-20vu.jtl`, `testing/jmeter/reports/report-klien-20vu/index.html`

### Status

`PASS`

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

- **5 VU:** 5/5 requests berhasil (Error: 0.00%). Avg Response Time: 2498.00 ms, Min: 2472 ms, Max: 2517 ms, Throughput: 1.71 req/s.
- **10 VU:** 10/10 requests berhasil (Error: 0.00%). Avg Response Time: 3399.70 ms, Min: 2453 ms, Max: 4440 ms, Throughput: 0.65 req/s.
- **20 VU:** 20/20 requests berhasil (Error: 0.00%). Avg Response Time: 5408.55 ms, Min: 2458 ms, Max: 7856 ms, Throughput: 0.44 req/s.

Detail pra-pendaftaran, dokumen aktif, dan timeline riwayat status berhasil ditampilkan secara akurat.

### Evidence

- `testing/jmeter/results/load-test-klien-5vu.jtl`, `testing/jmeter/reports/report-klien-5vu/index.html`
- `testing/jmeter/results/load-test-klien-10vu.jtl`, `testing/jmeter/reports/report-klien-10vu/index.html`
- `testing/jmeter/results/load-test-klien-20vu.jtl`, `testing/jmeter/reports/report-klien-20vu/index.html`

### Status

`PASS`

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

- **5 VU:** 5/5 requests berhasil (Error: 0.00%). Avg Response Time: 2662.20 ms, Min: 2627 ms, Max: 2712 ms, Throughput: 1.19 req/s.
- **10 VU:** 10/10 requests berhasil (Error: 0.00%). Avg Response Time: 2502.00 ms, Min: 2437 ms, Max: 2631 ms, Throughput: 0.82 req/s.
- **20 VU:** 20/20 requests berhasil (Error: 0.00%). Avg Response Time: 3683.15 ms, Min: 2680 ms, Max: 4541 ms, Throughput: 0.77 req/s.

Halaman dashboard klien berhasil merender data pra-pendaftaran perkara secara konsisten tanpa kegagalan koneksi DB.

### Evidence

- `testing/jmeter/results/load-test-5vu.jtl`, `testing/jmeter/reports/report-5vu/index.html`
- `testing/jmeter/results/load-test-10vu.jtl`, `testing/jmeter/reports/report-10vu/index.html`
- `testing/jmeter/results/load-test-20vu.jtl`, `testing/jmeter/reports/report-20vu/index.html`

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

- **5 VU:** 5/5 verifikasi berhasil (Error: 0.00%). Avg Response Time: 2292.40 ms (POST) / 3964.40 ms (Total with redirect), Throughput: 1.41 req/s.
- **10 VU:** 10/10 verifikasi berhasil (Error: 0.00%). Avg Response Time: 3477.90 ms (POST) / 6209.80 ms (Total with redirect), Throughput: 0.51 req/s.
- **20 VU:** 20/20 verifikasi berhasil (Error: 0.00%). Avg Response Time: 5778.80 ms (POST) / 10691.10 ms (Total with redirect), Throughput: 0.43 req/s.

Pemeriksaan berkas perkara, validasi status dokumen, perubahan status pendaftaran menjadi `berkas_lengkap`, dan pencatatan log riwayat status tersimpan secara konsisten dan aman via database transaction.

### Evidence

- `testing/jmeter/results/load-test-legal-5vu.jtl`, `testing/jmeter/reports/report-legal-5vu/index.html`
- `testing/jmeter/results/load-test-legal-10vu.jtl`, `testing/jmeter/reports/report-legal-10vu/index.html`
- `testing/jmeter/results/load-test-legal-20vu.jtl`, `testing/jmeter/reports/report-legal-20vu/index.html`

### Status

`PASS`

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
| PF-01 | 2026-08-31 | Progressive Load (5 VU, 10 VU, 20 VU): 100% OK, 0% error, avg 1.26s–1.89s | `testing/jmeter/results/load-test-{5,10,20}vu.jtl` | PASS | Login & CSRF token extraction |
| PF-02 | 2026-08-31 | Progressive Load (5 VU, 10 VU, 20 VU): 100% OK, 0% error, avg 1.85s–4.09s | `testing/jmeter/results/load-test-klien-{5,10,20}vu.jtl` | PASS | Akses Form Pra-Pendaftaran |
| PF-03 | 2026-08-31 | Progressive Load (5 VU, 10 VU, 20 VU): 100% OK, 0% error, avg 3.42s–5.11s | `testing/jmeter/results/load-test-klien-{5,10,20}vu.jtl` | PASS | Pengisian & Submit Formulir Perkara |
| PF-04 | 2026-08-31 | Progressive Load (5 VU, 10 VU, 20 VU): 100% OK, 0% error, avg 1.52s–4.70s | `testing/jmeter/results/load-test-klien-{5,10,20}vu.jtl` | PASS | Upload Dokumen Pendukung |
| PF-05 | 2026-08-31 | Progressive Load (5 VU, 10 VU, 20 VU): 100% OK, 0% error, avg 2.50s–5.41s | `testing/jmeter/results/load-test-klien-{5,10,20}vu.jtl` | PASS | Monitoring Status & Timeline Pengajuan |
| PF-06 | 2026-08-31 | Progressive Load (5 VU, 10 VU, 20 VU): 100% OK, 0% error, avg 2.50s–3.68s | `testing/jmeter/results/load-test-{5,10,20}vu.jtl` | PASS | Akses Dashboard Klien (Data Perkara) |
| PF-07 | 2026-08-31 | Progressive Load (5 VU, 10 VU, 20 VU): 100% OK, 0% error, avg 2.29s–5.78s | `testing/jmeter/results/load-test-legal-{5,10,20}vu.jtl` | PASS | Verifikasi Berkas Perkara Staf Legal |

````