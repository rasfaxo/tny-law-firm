# Rumahweb v1.0.0 Retest Harness

Status: **SUPERSEDED / DO NOT EXECUTE**. Target retest aktif telah dikunci ke satu lingkungan Azure App Service staging. Suite ini hanya dipertahankan sebagai riwayat perencanaan dan tidak boleh dicampurkan ke konfigurasi atau evidence laporan retest Azure.

This isolated suite preserves the historical harness and evidence. It targets only `retest.tnypartners.com`, requires a written Rumahweb authorization reference before active testing, and blocks the production hostnames.

Execution order:

1. Follow `docs/testing/RUMAHWEB_V100_RETEST_EXECUTION.md`.
2. Use `templates/clone-env.template` and `templates/cpanel-cron-commands.txt.example` to provision the isolated clone without SSH.
3. Verify the v1.0.0 artifact and clone isolation.
4. Run `scripts/validate-harness.ps1`, then `scripts/preflight-retest.ps1`.
5. Run performance validation and recorded tests.
6. Summarize the 21 performance rows.
7. Run ST-01–ST-09 and complete the phpMyAdmin verification for ST-07.
8. Run public-scope ZAP and manually verify its alerts.
9. Audit evidence for secrets and finalize the SHA-256 manifest.
10. Only then create the new thesis DOCX from actual evidence.

Do not store a filled environment file in this directory. Use the blank template and keep the filled copy outside the repository.
