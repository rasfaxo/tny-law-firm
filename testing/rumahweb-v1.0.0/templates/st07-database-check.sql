-- Run in phpMyAdmin against tnym6311_tnyretest only.
-- Replace <ST07_MARKER> with marker from security/st07-probe-evidence.json.
-- Both results must be 0 after the rejected upload.
SELECT COUNT(*) AS st07_case_count
FROM pra_pendaftaran_perkara
WHERE judul_perkara = '<ST07_MARKER>';

SELECT COUNT(*) AS st07_document_count
FROM dokumen_perkara AS d
INNER JOIN pra_pendaftaran_perkara AS p
    ON p.id_pendaftaran = d.id_pendaftaran
WHERE p.judul_perkara = '<ST07_MARKER>';
