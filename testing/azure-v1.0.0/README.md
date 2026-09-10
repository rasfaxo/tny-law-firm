# Azure App Service v1.0.0 Retest Harness

Status: **PREPARED / NOT EXECUTED**.

This isolated suite preserves the historical harness and evidence. It targets only `tny-law-firm-staging-afb3fqbdfvbteea3.indonesiacentral-01.azurewebsites.net`, records the project owner's authorization, and blocks the production hostnames.

Execution order:

1. Follow `docs/testing/AZURE_V100_RETEST_EXECUTION.md`.
2. Apply `templates/azure-app-settings.template` to the isolated staging App Service.
3. Follow `FIXTURE_SETUP.md`: deploy the temporary seeder helper once, capture the fixture IDs, and remove its three App Settings.
4. Deploy tag v1.0.0 again and record that final GitHub Actions run with `scripts/verify-azure-deployment.ps1`.
5. Run `scripts/validate-harness.ps1`, then rerun `scripts/preflight-retest.ps1` against the final deployment.
6. Verify that all four seeded accounts can log in and that every fixture ID is correct.
7. Run performance validation and recorded tests.
8. Summarize the 21 performance rows.
9. Run ST-01–ST-09 and complete the database verification for ST-07.
10. Run public-scope ZAP and manually verify its alerts.
11. Audit evidence for secrets and finalize the SHA-256 manifest.
12. Only then create the new thesis DOCX from actual evidence.

Do not store a filled environment file in this directory. Use the blank template and keep the filled copy outside the repository.
