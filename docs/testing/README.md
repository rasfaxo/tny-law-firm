# Indeks Evidence Pengujian

Direktori `testing/` menyimpan bukti pengujian skripsi dan tidak termasuk artifact hosting.

- `testing/jmeter/*.jmx`: skenario performance test.
- `testing/jmeter/results/`: raw JTL baseline (BEFORE).
- `testing/jmeter/reports/`: laporan HTML yang diturunkan dari raw JTL.
- `testing/evidence/security/`: evidence keamanan baseline (BEFORE).
- `testing/after/`: raw evidence setelah improvement (AFTER).
- `testing/security/` dan `testing/zap/`: script atau konfigurasi security test.
- `testing/test-documents/`: fixture valid dan tidak valid untuk validasi upload.

Evidence yang sudah ada bersifat immutable: jangan ditimpa, dihapus, atau diregenerasi menggunakan path yang sama. Retest baru harus menggunakan direktori dan nama baru. Klaim hasil hanya boleh berasal dari eksekusi aktual.

Dokumen pengujian aktif:

- `TEST_PLAN.md`: ruang lingkup dan metode.
- `TEST_CASES.md`: test case serta referensi evidence.
- `TESTING_STATE.md`: status pengujian yang tercatat.
- `FINAL_TEST_REPORT.md`: ringkasan hasil BEFORE/AFTER.
- `RUMAHWEB_V100_RETEST_EXECUTION.md`: rancangan lama yang **tidak digunakan** setelah target retest dikunci ke Azure; tidak menjadi sumber konfigurasi atau hasil laporan.
- `AZURE_V100_RETEST_EXECUTION.md`: runbook aktif retest v1.0.0 satu lingkungan pada Azure App Service staging; menjadi satu-satunya sumber konfigurasi dan hasil retest baru ketika staging aktif.
