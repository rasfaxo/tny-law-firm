# LAPORAN VALIDASI PENGUJIAN PERFORMA DAN KEAMANAN (RETEST BERSIH)
**Sistem Informasi Pra-Pendaftaran Perkara Berbasis Web pada TNY Law Firm**

- **Run ID / Timestamp:** `20260903-204646`
- **Tanggal Pengujian:** 3 September 2026
- **Lingkungan Uji:** Azure App Service Linux (Staging: `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net/`)
- **Status Validasi Keseluruhan:** **PERFORMANCE RETEST VALID; SECURITY RETEST PARTIALLY VALID; VALIDATION REPORT VALID WITH LIMITATIONS.**
- **Status Integritas Evidence:** READ-ONLY & UNTOUCHED (38 file evidence historis + 4 file harness uji diverifikasi via `EVIDENCE_INTEGRITY_MANIFEST.sha256`)

---

## 1. Ringkasan Eksekutif dan Status Validasi

Laporan ini menyajikan hasil validasi pengujian ulang (*retest*) performa dan keamanan pada aplikasi staging TNY Law Firm. Berdasarkan audit bukti artefak dan verifikasi faktual, status akhir laporan ditetapkan sebagai:

> **PERFORMANCE RETEST VALID; SECURITY RETEST PARTIALLY VALID; VALIDATION REPORT VALID WITH LIMITATIONS.**

### Rincian Evaluasi Status
1. **Pengujian Performa (JMeter): VALID**
   - Seluruh 10 file JTL baru memuat total **1.477 sampel** dengan **0 sampel gagal (Error Rate 0,00%)**.
   - Metrik Average, P95 *nearest-rank*, minimum, maksimum, dan throughput end-to-end terverifikasi cocok dengan data mentah JTL.
   - **Evaluasi Target Respon Skripsi ($\le$ 3.000 ms):** Hanya **3 dari 21 variasi beban** (14,3%) yang memenuhi target waktu respon skripsi (PF-02 5 VU: 1.057 ms; PF-06 5 VU: 2.063 ms; dan PF-06 10 VU: 2.659 ms). Sebanyak 18 variasi lainnya melebihi ambang batas 3.000 ms.

2. **Pengujian Keamanan (ST-01 s/d ST-09): PARTIALLY VALID**
   - Kasus uji ST-01 s/d ST-06, ST-08, dan ST-09 terverifikasi **PASS**.
   - Kasus uji **ST-07 berstatus PARTIALLY VALID (PARTIAL_PASS)**: Penolakan file executable `.exe` (HTTP 302), pesan validasi tipe berkas pada form redirect (*"The file field must be a file of type: pdf, jpg, jpeg, png"*), serta keutuhan metadata perkara (`1 -> 1`) terbukti secara faktual pada `Case 307` (`menunggu_verifikasi`). Namun, ketiadaan penyimpanan fisik blob di storage backend berstatus **NOT VERIFIED** karena keterbatasan probe HTTP eksternal yang tidak memiliki akses log telemetri backend Azure Storage.
   - Riwayat eksekusi pengujian keamanan dicatat secara transparan: file `security-test-trial-history.log` mendokumentasikan 4 percobaan awal saat pengembangan harness, sedangkan `security-test-execution.log` memuat eksekusi bersih tunggal pengujian final.

3. **Pemindaian Keamanan Otomatis OWASP ZAP: VALID WITH LIMITATIONS**
   - **“ZAP tidak melaporkan alert High pada active scan cakupan publik yang dijalankan.”**
   - Alert yang terdeteksi: **High 0, Medium 2, Low 2, Informational 4**.
   - Batasan teknis: Aturan pemindaian *DOM XSS* dilewati karena ketiadaan browser binary Firefox headless pada lingkungan CLI Windows, serta terdapat catatan koneksi timeout pada beberapa probe pasif yang terekam pada `zap-scan-warnings-errors.log`.

---

## 2. Metodologi dan Penyelarasan Sumber Data

### 2.1 Konfigurasi Beban JMeter
Pengujian performa menggunakan Apache JMeter 5.6.3 dalam mode CLI non-GUI (`-n -t ... -l ...`) dengan parameter beban:
- **General Flow (`tny-law-firm-load-test.jmx`):** 5 VU (ramp-up 5s), 10 VU (ramp-up 10s), 20 VU (ramp-up 20s), Loop 1.
- **Klien Flow (`tny-law-firm-klien-flow.jmx`):** 5 VU (ramp-up 5s), 10 VU (ramp-up 10s), 20 VU (ramp-up 10s), Loop 1.
- **Legal Flow (`tny-law-firm-legal-flow.jmx`):** 5 VU (ramp-up 5s), 10 VU (ramp-up 10s), 20 VU (ramp-up 10s), Loop 1.

### 2.2 Penyelarasan Sumber Data Skenario (Pemilihan Sumber PF-01)
Untuk memastikan konsistensi metodologis antara rencana pengujian, tabel utama, dan file `metrics-summary.json`:
- **`PF-01` (POST Login Klien):** Secara kanonikal bersumber dari JTL General Flow (`load-test-general-{5,10,20}vu.jtl`) dengan label `POST Login Klien` (sub-sampler: `POST Login Klien-0`). Pemilihan ini konsisten dengan baseline historis skenario login terisolasi pada `TEST_CASES.md` (`load-test-{5,10,20}vu.jtl`).
  *(Catatan komparasi konteks: Pada JTL Klien Flow `load-test-klien-*.jtl`, terdapat pula sampler `Step 2 - POST Login Klien` sebagai bagian dari rangkaian rantai pengajuan perkara yang mencatat 5 VU: P95 4.576 ms, durasi 6,62s, throughput 0,75 req/s; 10 VU: P95 15.410 ms; 20 VU: P95 18.427 ms. Namun sumber kanonikal tunggal yang diadopsi untuk PF-01 adalah General Flow).*
- **`PF-02` (GET Form Pra-Pendaftaran):** JTL `load-test-klien-{5,10,20}vu.jtl`, Label: `PF-02 - GET Form Pra-Pendaftaran`.
- **`PF-03` (POST Formulir Perkara):** JTL `load-test-klien-{5,10,20}vu.jtl`, Label: `PF-03 - POST Formulir Perkara` (sub-sampler: `PF-03 - POST Formulir Perkara-0`).
- **`PF-04` (POST Upload Dokumen Perkara):** JTL `load-test-klien-{5,10,20}vu.jtl`, Label: `PF-04 - POST Upload Dokumen` (sub-sampler: `PF-04 - POST Upload Dokumen-0`).
- **`PF-05` (GET Monitoring Detail Kasus):** JTL `load-test-klien-{5,10,20}vu.jtl`, Label: `PF-05 - GET Monitoring Status`.
- **`PF-06` (GET Klien Dashboard):** JTL `load-test-general-{5,10,20}vu.jtl`, Label: `GET Klien Dashboard`.
- **`PF-07` (POST Submit Verifikasi Berkas):** JTL `load-test-legal-{5,10,20}vu.jtl`, Label: `PF-07 - POST Submit Verifikasi Berkas` (sub-sampler: `PF-07 - POST Submit Verifikasi Berkas-0`).

### 2.3 Rumus Perhitungan Metrik Raw JTL
Seluruh metrik dihitung langsung dari data mentah JTL:
- **Average:** $\frac{1}{N}\sum_{i=1}^N \text{elapsed}_i$
- **Percentile 95 (P95):** Metode *nearest-rank* (1-indexed) pada array terurut menaik, indeks $k = \lceil 0{,}95 \times N \rceil$.
- **Error Rate:** $\frac{\text{Jumlah Request Gagal}}{N} \times 100\%$
- **Throughput End-to-End:** 
  $$\text{Throughput} = \frac{N}{\frac{\max(\text{timeStamp}_i + \text{elapsed}_i) - \min(\text{timeStamp}_i)}{1000}}$$
  *Catatan: Rumus ini memperhitungkan durasi penuh hingga respons sampel terakhir selesai diterima.*

---

## 3. Hasil Pengujian Performa (Data Utama Subbab 4.6)

Tabel berikut menyajikan data performa hasil retest bersih yang telah diselaraskan dengan throughput end-to-end terkoreksi:

| ID | Skenario Pengujian | Beban (VU) | Ramp-up (s) | Sampel ($N$) | Rata-rata (ms) | P95 Total (ms) | P95 POST Awal Sebelum Redirect (ms) | Min (ms) | Max (ms) | Error Rate (%) | Durasi Run (s) | Throughput End-to-End (req/s) | Status Eksekusi | Evaluasi Target Skripsi ($\le$ 3000 ms) |
|:---|:---|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---:|:---|
| **PF-01** | POST Login Klien | 5 | 5 | 5 | 4.617,4 | 4.738 | 1.945 | 4.190 | 4.738 | 0,00% | 6,14 | **0,81** | **PASS** | MELEBIHI |
| **PF-01** | POST Login Klien | 10 | 10 | 10 | 4.390,6 | 5.175 | 2.077 | 3.864 | 5.175 | 0,00% | 13,17 | **0,76** | **PASS** | MELEBIHI |
| **PF-01** | POST Login Klien | 20 | 20 | 20 | 5.463,0 | 7.105 | 2.165 | 3.902 | 7.706 | 0,00% | 25,46 | **0,79** | **PASS** | MELEBIHI |
| **PF-02** | GET Form Pra-Pendaftaran | 5 | 5 | 5 | 1.006,0 | **1.057** | - | 945 | 1.057 | 0,00% | 3,54 | **1,41** | **PASS** | **MEMENUHI** |
| **PF-02** | GET Form Pra-Pendaftaran | 10 | 10 | 10 | 2.591,4 | 6.049 | - | 1.001 | 6.049 | 0,00% | 23,42 | **0,43** | **PASS** | MELEBIHI |
| **PF-02** | GET Form Pra-Pendaftaran | 20 | 10 | 20 | 5.021,2 | 6.807 | - | 1.971 | 6.888 | 0,00% | 31,98 | **0,63** | **PASS** | MELEBIHI |
| **PF-03** | POST Formulir Perkara | 5 | 5 | 5 | 4.537,8 | 4.930 | 2.356 | 4.200 | 4.930 | 0,00% | 6,68 | **0,75** | **PASS** | MELEBIHI |
| **PF-03** | POST Formulir Perkara | 10 | 10 | 10 | 8.813,7 | 12.050 | 7.349 | 6.249 | 12.050 | 0,00% | 28,66 | **0,35** | **PASS** | MELEBIHI |
| **PF-03** | POST Formulir Perkara | 20 | 10 | 20 | 14.262,0 | 16.110 | 8.155 | 10.468 | 16.479 | 0,00% | 46,12 | **0,43** | **PASS** | MELEBIHI |
| **PF-04** | POST Upload Dokumen Perkara | 5 | 5 | 5 | 3.943,6 | 4.024 | 1.421 | 3.850 | 4.024 | 0,00% | 5,67 | **0,88** | **PASS** | MELEBIHI |
| **PF-04** | POST Upload Dokumen Perkara | 10 | 10 | 10 | 7.530,5 | 10.960 | 7.199 | 4.182 | 10.960 | 0,00% | 26,19 | **0,38** | **PASS** | MELEBIHI |
| **PF-04** | POST Upload Dokumen Perkara | 20 | 10 | 20 | 14.036,2 | 16.462 | 8.196 | 10.189 | 16.537 | 0,00% | 46,38 | **0,43** | **PASS** | MELEBIHI |
| **PF-05** | GET Monitoring Detail Kasus | 5 | 5 | 5 | 2.698,8 | 3.207 | - | 2.501 | 3.207 | 0,00% | 4,85 | **1,03** | **PASS** | MELEBIHI |
| **PF-05** | GET Monitoring Detail Kasus | 10 | 10 | 10 | 3.645,4 | 4.887 | - | 2.376 | 4.887 | 0,00% | 17,60 | **0,57** | **PASS** | MELEBIHI |
| **PF-05** | GET Monitoring Detail Kasus | 20 | 10 | 20 | 6.531,8 | 8.163 | - | 2.390 | 9.352 | 0,00% | 34,13 | **0,59** | **PASS** | MELEBIHI |
| **PF-06** | GET Klien Dashboard | 5 | 5 | 5 | 1.980,0 | **2.063** | - | 1.915 | 2.063 | 0,00% | 3,50 | **1,43** | **PASS** | **MEMENUHI** |
| **PF-06** | GET Klien Dashboard | 10 | 10 | 10 | 2.181,8 | **2.659** | - | 1.932 | 2.659 | 0,00% | 10,88 | **0,92** | **PASS** | **MEMENUHI** |
| **PF-06** | GET Klien Dashboard | 20 | 20 | 20 | 2.515,3 | 3.129 | - | 1.974 | 3.186 | 0,00% | 23,09 | **0,87** | **PASS** | MELEBIHI |
| **PF-07** | POST Submit Verifikasi Berkas | 5 | 5 | 5 | 4.191,0 | 4.379 | 2.605 | 4.037 | 4.379 | 0,00% | 4,74 | **1,06** | **PASS** | MELEBIHI |
| **PF-07** | POST Submit Verifikasi Berkas | 10 | 10 | 10 | 5.739,0 | 6.833 | 3.984 | 3.984 | 6.833 | 0,00% | 18,04 | **0,55** | **PASS** | MELEBIHI |
| **PF-07** | POST Submit Verifikasi Berkas | 20 | 10 | 20 | 15.816,9 | 21.253 | 14.690 | 5.678 | 22.210 | 0,00% | 44,91 | **0,45** | **PASS** | MELEBIHI |

### 3.1 Pembahasan Hasil dan Batasan Bukti Telemetri
1. **Status Ketercapaian Target Skripsi:**
   Dari 21 kombinasi skenario dan beban yang diuji, hanya 3 variasi (14,3%) yang memenuhi target waktu respon skripsi $\le$ 3.000 ms. Sebanyak 18 variasi lainnya mencatatkan waktu respon di atas 3.000 ms.
2. **P95 Total vs Latensi POST Awal Sebelum Redirect:**
   Sub-sampler `-0` membuktikan bahwa sebagian besar durasi eksekusi request POST berada pada proses pengalihan (*redirect GET*) menuju halaman tujuan. Sebagai contoh pada `PF-01` 5 VU, POST awal hanya membutuhkan 1.945 ms dari total waktu respon 4.738 ms.
3. **Status Verifikasi Penyebab Latensi (NOT VERIFIED):**
   Mengingat pengujian ini dilakukan dari sisi klien eksternal tanpa pengumpulan telemetri performa Azure App Service, log slow-query database, trace `EXPLAIN`, maupun profil utilisasi resource hosting pada saat retest, klaim spesifik mengenai akar penyebab tingginya latensi pada transaksi *write* **berstatus NOT VERIFIED** dan tidak dapat dinyatakan sebagai fakta terbukti pada artefak.

---

## 4. Hasil Pengujian Keamanan Fungsional (ST-01 s/d ST-09)

Pengujian probe keamanan dijalankan menggunakan script harness `testing/security/run_all_security_tests.ps1` (SHA-256: `48C8B9C5EEE337CAE989D11942800270FEBE973C84A4F07C54860E10DF0B4F1E`):

| ID | Kasus Pengujian Keamanan | Status | Ringkasan Bukti Faktual |
|:---|:---|:---:|:---|
| **ST-01** | SQL Injection pada Form Login | **PASS** | Payload `' OR 1=1 --` ditolak dengan aman (HTTP 302 redirect kembali ke `/login`, tanpa error SQL). |
| **ST-02** | Cross-Site Scripting (Reflected XSS) | **PASS** | Payload `<script>window.__xss_probe=1</script>` direfleksikan secara aman dalam bentuk HTML-escaped (`&lt;script&gt;`) oleh template Blade, mencegah eksekusi skrip di browser. |
| **ST-03** | Proteksi CSRF Token | **PASS** | Form submit POST tanpa token atau dengan token acak ditolak dengan **HTTP 419 Page Expired**. |
| **ST-04** | Session Cookie Flags & Rate Limiting | **PASS** | Cookie `laravel-session` memiliki flag `Secure`, `HttpOnly`, dan `SameSite=Lax`. Upaya login berulang memicu lockout dengan pesan teks faktual: *"Too many login attempts"*. |
| **ST-05** | Role-Based Access Control (RBAC 4 Aktor) | **PASS** | Akses rute `/admin/dashboard` terisolasi: Anonim (HTTP 302), Klien (HTTP 403), Staf Legal (HTTP 403), dan Admin (HTTP 200). Akses Klien ke `/staf-legal/verifikasi-berkas` ditolak (HTTP 403). |
| **ST-06** | Pencegahan Insecure Direct Object References (IDOR) | **PASS** | Akses perkara milik akun lain (`client002`) oleh `client001` (`/klien/pra-pendaftaran/2`) diblokir Policy dengan **HTTP 403 Forbidden**; akses oleh pemilik sah berhasil (**HTTP 200 OK**). |
| **ST-07** | Keamanan Validasi Unggah Berkas (.exe) | **PARTIAL_PASS** | Unggah file `invalid-executable.exe` ditolak (HTTP 302). Respon form redirect menampilkan pesan validasi spesifik: *"The file field must be a file of type: pdf, jpg, jpeg, png"*. Metadata dokumen perkara tidak bertambah (`1 -> 1`). Namun, **ketiadaan penyimpanan fisik blob di sisi Azure Storage berstatus NOT VERIFIED** karena ketiadaan telemetri audit log storage backend. |
| **ST-08** | Otorisasi Akses Unduhan Dokumen Perkara | **PASS** | Unduhan berkas privat (ID 297) diblokir bagi anonim (HTTP 302) dan penyerang (HTTP 403); hanya pemilik sah yang berhasil streaming berkas (HTTP 200). |
| **ST-09** | Pemblokiran Direct Storage & 8 Security Headers | **PASS** | Akses langsung direktori storage `/storage/dokumen-perkara/valid-document.pdf` diblokir Nginx (HTTP 403). Seluruh 8 security header hadir lengkap: `Content-Security-Policy`, `Strict-Transport-Security`, `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy`, `Permissions-Policy`, `Cross-Origin-Opener-Policy`, dan `Cross-Origin-Resource-Policy`. Header `X-Powered-By` nihil, versi numerik Server disembunyikan (`nginx`), dan font eksternal Google Fonts nihil. |

### 4.1 Transparansi Riwayat Percobaan Eksekusi Security
Sesuai prinsip integritas bukti, riwayat percobaan pengujian keamanan tidak disembunyikan:
- **File `security-test-trial-history.log` (6.510 bytes):** Mendokumentasikan 4 eksekusi awal saat pengembangan probe, di mana percobaan 1–3 sempat mencatat kegagalan pada ST-04 (karena regex multi-cookie), ST-05 (kredensial admin seeder), dan ST-07 (pengujian awal pada perkara yang telah berstatus `berkas_lengkap`).
- **File `security-test-execution.log` (1.825 bytes):** Mendokumentasikan eksekusi bersih tunggal (*single clean run*) dari script final terhadap target perkara aktif (`Case 307`) berstatus `menunggu_verifikasi`.

---

## 5. Hasil Pemindaian Keamanan Otomatis OWASP ZAP

Pemindaian keamanan dinamis (*DAST*) dijalankan menggunakan OWASP ZAP v2.17.0 terhadap URL target publik: `https://tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net/`.

### 5.1 Kesimpulan dan Batasan Pemindaian
> **“ZAP tidak melaporkan alert High pada active scan cakupan publik yang dijalankan.”**

### 5.2 Rincian Temuan Alert Pemindaian

| Tingkat Risiko (*Risk Level*) | Jumlah Alert | Rincian Temuan Alert Scanner & Rekomendasi Hardening |
|:---|:---:|:---|
| **High** | **0** | ZAP tidak melaporkan alert High pada active scan cakupan publik yang dijalankan. |
| **Medium** | **2** | 1. CSP: `script-src unsafe-inline` (Rekomendasi hardening: pertimbangkan evaluasi implementasi nonce atau SHA-hash untuk script inline).<br>2. CSP: `style-src unsafe-inline` (Rekomendasi hardening: evaluasi pembatasan sumber style). |
| **Low** | **2** | 1. *Big Redirect Detected*: Respons pengalihan HTTP 302 memuat badan respons HTML navigasi.<br>2. *Cookie No HttpOnly Flag*: Cookie `XSRF-TOKEN` diterbitkan tanpa atribut `HttpOnly` (cookie session `laravel-session` memiliki flag `HttpOnly`). |
| **Informational** | **4** | 1. Authentication Request Identified (`/login`).<br>2. Re-examine Cache-control Directives.<br>3. Session Management Response Identified.<br>4. User Agent Fuzzer. |
| **False Positives** | **0** | - |

### 5.3 Keterbatasan Teknis Scan
1. **DOM XSS Scanner Dilewati:** Scanner `DomXssScanRule` dilewati oleh ZAP karena ketiadaan driver/binary Firefox headless pada lingkungan eksekusi CLI Windows.
2. **Koneksi Jaringan & Port 8080:** Terdapat catatan koneksi timeout pada beberapa probe pasif yang terekam pada `zap-scan-warnings-errors.log`.

---

## 6. Inventaris Artefak Bukti Retest dan Manifest Integritas

Tabel berikut menyajikan seluruh file bukti artefak pengujian pada direktori `testing/retest/20260903-204646/` dengan ukuran byte dan hash SHA-256 aktual:

| Nama File Artefak | Ukuran (Bytes) | SHA-256 Checksum Aktual |
|:---|:---:|:---|
| `EVIDENCE_INTEGRITY_MANIFEST.sha256` | 6.451 | `6BF7CE0D7F5D607B28B54731D7A9D33A40CB39FC6DD5627CFC979850C8E5746C` |
| `performance/load-test-general-5vu.jtl` | 7.179 | `1A97A8FA527AF4DBC1156ADAF32D749224EBF4875FEA59CCC72A8D00CC9C79C0` |
| `performance/load-test-general-10vu.jtl` | 14.175 | `551326971DAF567993E2495CB17BD66E1D5C7038B5A66BAE6B44B028CAFA6E15` |
| `performance/load-test-general-20vu.jtl` | 28.325 | `1D44263B96648F1E84CCFAB709A39B532B5971B6F9ACD5435E4F050A69AAE299` |
| `performance/load-test-klien-5vu.jtl` | 14.248 | `D64DD7744ED3BA739E3825037EFEA1ED46159E891009354336B5D7BBB375DB87` |
| `performance/load-test-klien-10vu.jtl` | 28.477 | `EDB83EEC48C61A502265DCDD7FBF370D956FAD5952493E6B04979BF2D3A3CE09` |
| `performance/load-test-klien-20vu.jtl` | 57.208 | `08C5488790349CB0ECBD6CFAD16D2E18C40C7AACB4D8420810F8CD233EF25ED0` |
| `performance/load-test-legal-5vu.jtl` | 25.235 | `70AEA38286B65C692B93B98C7B72E7BF4096AC55B38730FFF20397C76CC7742E` |
| `performance/load-test-legal-10vu.jtl` | 50.755 | `B044EAC579CB5BAD2A68B315DFE643B53FC3AD974B171D8411A6BBFC9DB0CE62` |
| `performance/load-test-legal-20vu.jtl` | 101.937 | `0B2FF1DD782F05F20EABB5E062F653C7F3E66250E9CE9C21CC2201EBFCA779EE` |
| `performance/metrics-summary.json` | 8.415 | `0BF6854AFF1887E9A8A653B3C65B1D589AE425429DD35F7E36024BB83DBCA397` |
| `performance/validation-baseline.jtl` | 1.569 | `A3AE7B03B77D8FBF0D3ADEAA6E370E7E905C667F45E919748A70D566E509C125` |
| `security/security-test-execution.log` | 1.825 | `0C248F62E100C22B2FF0C915FB0AC67AE942D106D3DB9722C38491555AC94222` |
| `security/security-test-results.json` | 2.053 | `16BB97C86E45D9E4A118725AD94341A82DC3C8BDC933A81A9B8B93E0D0CAD992` |
| `security/security-test-trial-history.log` | 6.510 | `1D564A545C753A1F2AD8EF5074B3B26DFF7CB8547339E0B0199FAB24ABDC5733` |
| `security/zap-active-scan-report.html` | 84.123 | `DDDFF75842693EB6ED12878BB95EA18CA060B186A5AB2387271630023D1D649F` |
| `security/zap-scan-warnings-errors.log` | 15.157 | `EFF44FC538B8A6B56F46B82DF0FDDAC978655D7FF658478907259DCC0B3D04CB` |

### 6.1 Manifest File Test Harness Terbekukan
File skenario dan skrip uji yang digunakan dalam pengujian ini dibekukan dengan hash SHA-256 sebagai berikut:
- `testing/jmeter/tny-law-firm-load-test.jmx`: `5B92BB01988A33A677A6B5183946EE6EF339CE7491398B00D8AFE7E5EB12C2F2`
- `testing/jmeter/tny-law-firm-klien-flow.jmx`: `DA35D2F30D3A384CF7257089EFA6D6EBB6DF51ED35EC4CD5FCD360551DB6F42A`
- `testing/jmeter/tny-law-firm-legal-flow.jmx`: `5E37C783398B9700D334750C8AC596E121EF3CABF5D5264E1430D7BFB82AE60A`
- `testing/security/run_all_security_tests.ps1`: `48C8B9C5EEE337CAE989D11942800270FEBE973C84A4F07C54860E10DF0B4F1E`

---

## 7. Status Integrasi Laporan Skripsi

Sesuai arahan evaluasi dan aturan proyek:
1. Status akhir laporan validasi: **PERFORMANCE RETEST VALID; SECURITY RETEST PARTIALLY VALID; VALIDATION REPORT VALID WITH LIMITATIONS.**
2. Laporan ini merupakan dasar rujukan teknis yang siap dipakai untuk penyusunan Subbab 4.6.
3. Dokumen resmi skripsi (`docs/testing/FINAL_TEST_REPORT.md`, `docs/testing/TESTING_STATE.md`, maupun draf Bab 4 Subbab 4.6) **tidak diubah**.
4. Seluruh artefak tetap berada dalam direktori pengujian lokal staging tanpa adanya perintah `git commit` maupun `git push`.
