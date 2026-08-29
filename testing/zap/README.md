# Panduan Security Scanning dengan OWASP ZAP (Staging)

Dokumen ini menjelaskan langkah-langkah manual GUI untuk mengonfigurasi dan menjalankan *Dynamic Application Security Testing* (DAST) menggunakan OWASP ZAP pada aplikasi TNY Law Firm di environment Staging.

## Persyaratan
- OWASP ZAP v2.17.0 (atau yang lebih baru)
- Akses ke URL Staging
- Browser lokal

---

## MANUAL OWASP ZAP GUI CONFIGURATION (Baseline & Passive Scan)

Target awal adalah melakukan *spidering* (crawling) dan pemindaian pasif (*Passive Scan*) pada halaman publik.
Langkah-langkah berikut merupakan aksi yang HARUS Anda lakukan secara manual:

**STEP 1: Buka ZAP**
Buka aplikasi OWASP ZAP versi 2.17.0 di komputer Anda.

**STEP 2: Konfigurasi Target**
- Pada tab **Quick Start**, klik tombol besar **Automated Scan**.
- Di bagian **URL to attack**, isi dengan target staging Anda (contoh: `https://tny-law-firm-staging.azurewebsites.net`).
- Di bagian **Use traditional spider**, biarkan tercentang.
- Klik **Attack**.

**STEP 3: Tinjau Hasil Pasif (Baseline)**
- ZAP akan otomatis mengakses URL dan melakukan *Spidering* (mencari tautan seperti `/`, `/login`, `/register`).
- Buka tab **Alerts** di panel bawah ZAP.
- Analisis kerentanan yang terdeteksi (seperti *Missing Anti-clickjacking Header*, *Cookie No HttpOnly Flag*, dll). Karena ini hanya *Passive Scan*, ZAP tidak melakukan penyerangan berbahaya ke sistem.

**STEP 4: Ekspor Laporan (Report)**
- Setelah *Spidering* selesai (progress 100%), klik menu utama: `Report` -> `Generate Report...`.
- Pilih template yang diinginkan (misal: *Traditional HTML Report*).
- Simpan file PDF/HTML tersebut ke direktori project Anda: `testing/evidence/security/zap-baseline-report.html`.

---

## MANUAL AUTHENTICATED ZAP TESTING (Advanced)

Jika Anda ingin ZAP melakukan scan ke halaman berbayar/terautentikasi (seperti Dashboard Klien `/klien/*`, Dashboard Admin `/admin/*`, atau Staf Legal `/staf-legal/*`), Anda perlu memasukkan kredensial ke dalam sistem ZAP tanpa menuliskannya di file repositori ini.

Langkah Manual Autentikasi:
**STEP 1: Setup ZAP Local Proxy**
- ZAP secara default berjalan sebagai proxy di `localhost:8080`.
- Buka browser Firefox / Chrome Anda, atur network settings agar menggunakan HTTP Proxy `localhost` dengan port `8080`. (Atau cukup gunakan fitur "Manual Explore" di tab Quick Start ZAP, yang akan membuka browser bawaan ZAP secara otomatis).

**STEP 2: Login Manual via Browser ZAP**
- Buka URL Staging `/login` di browser yang di-proxy oleh ZAP.
- Masukkan email Klien (`client001@tny.test`) dan password (`Password123!`).
- Klik "Log In".
- Arahkan ke `/klien/dashboard` dan `/klien/pra-pendaftaran`.

**STEP 3: Tetapkan Konteks Autentikasi di ZAP**
- Kembali ke GUI ZAP.
- Pada panel **Sites** di sebelah kiri, temukan *request* POST ke `/login`.
- Klik kanan *request* POST tersebut, pilih **Flag as Context** -> **Default Context** -> **Form-based Auth Login Request**.
- Konfirmasi bahwa kolom *username* memetakan form `email` dan *password* memetakan form `password`.
- Tambahkan Session Cookie ZAP jika diperlukan.

**STEP 4: Jalankan Active Scan (Hanya jika disetujui)**
> **PERINGATAN:** Active Scan dapat mengirimkan payload berbahaya seperti SQL Injection dan XSS ke aplikasi Anda, yang dapat merusak data di Staging atau meninggalkan ratusan data *spam*.
- Jika Anda benar-benar yakin, klik kanan URL pada tab *Sites*, pilih **Attack** -> **Active Scan**.
- Jangan pernah jalankan Active Scan terhadap URL Production!
