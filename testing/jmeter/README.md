# Panduan Load Testing dengan Apache JMeter (Staging)

Dokumen ini berisi panduan konfigurasi dan eksekusi load testing untuk aplikasi TNY Law Firm pada environment Staging.
Struktur pengujian dirancang *Zero Assumption* dan tidak menyimpan kredensial asli di dalam file konfigurasi repositori.

## Persyaratan
- Apache JMeter v5.6.3 (atau yang lebih baru)
- Java Runtime Environment (JRE) 11+
- URL Staging (didapatkan dari Azure App Service / GitHub Secrets)

---

## 1. Eksekusi JMeter via CLI (Non-GUI Mode) - Direkomendasikan
Untuk pengujian otomatis, sangat disarankan menggunakan mode CLI agar beban resource lebih ringan.

Jalankan perintah berikut di terminal pada *root folder project*:

```bash
jmeter -n \
  -t testing/jmeter/tny-law-firm-load-test.jmx \
  -l testing/jmeter/results/load-test-results.jtl \
  -JSTAGING_URL=tny-law-firm-staging.azurewebsites.net
```
*(Ganti `tny-law-firm-staging.azurewebsites.net` dengan hostname staging yang sebenarnya)*

Setelah selesai, Anda dapat membuat report HTML:
```bash
jmeter -g testing/jmeter/results/load-test-results.jtl -o testing/jmeter/reports/
```
Buka `testing/jmeter/reports/index.html` di browser Anda.

---

## 2. MANUAL JMeter GUI CONFIGURATION (Langkah Interaktif)

Jika Anda ingin melihat secara visual atau memodifikasi parameter test (misal mengubah user yang login), lakukan langkah-langkah berikut:

**STEP 1: Buka JMeter**
Jalankan JMeter dari komputer lokal Anda (`jmeter.bat` di Windows atau `./jmeter` di Mac/Linux).

**STEP 2: Buka File JMX**
- Klik `File` -> `Open`
- Arahkan ke folder project: `testing/jmeter/tny-law-firm-load-test.jmx`

**STEP 3: Konfigurasi Parameter (User Defined Variables)**
- Di sebelah kiri (Test Plan tree), klik **User Defined Variables**.
- Pada tabel, ubah `Value` dari variabel `STAGING_URL`.
  *Nilai defaultnya adalah `${__P(STAGING_URL, localhost)}` yang membaca argumen CLI.*
  *Untuk testing GUI, ubah nilainya menjadi URL Staging Anda secara spesifik.* (Misal: `tny-law-firm-staging.azurewebsites.net`)

**STEP 4: Konfigurasi Thread Group (Beban User)**
- Klik **Baseline Staging Users** di Test Plan tree.
- Konfigurasi Default yang tersedia:
  - **Number of Threads (users):** 10 (jumlah user bersamaan)
  - **Ramp-up period (seconds):** 10 (waktu yang dibutuhkan untuk memasukkan semua user)
  - **Loop Count:** 1 (jumlah pengulangan skenario per user)
- Anda dapat mengubah nilai ini sesuai kebutuhan *load test* yang ingin dilakukan.

**STEP 5: Eksekusi Test**
- Klik tombol **Start** (Ikon panah hijau / Play) di toolbar atas.
- Untuk melihat hasil secara *real-time*, klik **View Results Tree** atau **Summary Report** di dalam Thread Group.

---

## Tentang Skenario
Test plan bawaan ini (`tny-law-firm-load-test.jmx`) berisi skenario dasar berikut:
1. Akses halaman awal `/`
2. Akses halaman login `/login` dan ekstrak token keamanan CSRF Laravel.
3. Kirim form POST ke `/login` (menggunakan akun dummy Klien `client001@tny.test` dan `Password123!`).
4. Akses halaman Dashboard Klien `/klien/dashboard`.

*(Harap gunakan panduan ini HANYA untuk environment Staging, JANGAN arahkan ke Production)*
