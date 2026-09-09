# Azure App Service v1.0.0 Retest Harness

Status: **PREPARED / NOT EXECUTED**.

This isolated suite preserves the historical harness and evidence. It targets only `tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net`, records the project owner's authorization, and blocks the production hostnames.

Execution order:

1. Follow `docs/testing/AZURE_V100_RETEST_EXECUTION.md`.
2. Apply `templates/azure-app-settings.template` to the isolated staging App Service.
3. Deploy v1.0.0 and record the GitHub Actions run with `scripts/verify-azure-deployment.ps1`.
4. Run `scripts/validate-harness.ps1`, then `scripts/preflight-retest.ps1`.
5. Run performance validation and recorded tests.
6. Summarize the 21 performance rows.
7. Run ST-01–ST-09 and complete the database verification for ST-07.
8. Run public-scope ZAP and manually verify its alerts.
9. Audit evidence for secrets and finalize the SHA-256 manifest.
10. Only then create the new thesis DOCX from actual evidence.

Do not store a filled environment file in this directory. Use the blank template and keep the filled copy outside the repository.
