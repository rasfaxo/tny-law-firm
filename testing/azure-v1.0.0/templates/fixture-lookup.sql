-- Read-only lookup for fixtures created by RetestFixtureSeeder.
-- Run only on the isolated Azure retest database.

SELECT id_user, nama, email, role, status_akun, email_verified_at
FROM users
WHERE email IN (
    'client-a.retest.v100@example.test',
    'client-b.retest.v100@example.test',
    'legal.retest.v100@example.test',
    'admin.retest.v100@example.test'
)
ORDER BY role, id_user;

SELECT id_kategori, nama_kategori
FROM kategori_perkara
WHERE nama_kategori = 'Retest Azure v1.0.0';

SELECT
    p.id_pendaftaran,
    u.email,
    p.judul_perkara,
    p.status_pengajuan,
    p.created_at
FROM pra_pendaftaran_perkara AS p
INNER JOIN users AS u ON u.id_user = p.id_user
WHERE p.judul_perkara IN (
    'Retest Ownership Azure v1.0.0',
    'Retest Perbaikan Azure v1.0.0'
)
ORDER BY p.id_pendaftaran;

SELECT
    d.id_dokumen,
    d.id_pendaftaran,
    d.file_path,
    d.status_dokumen,
    c.id_catatan,
    c.status_perbaikan
FROM dokumen_perkara AS d
LEFT JOIN catatan_verifikasi AS c ON c.id_dokumen = d.id_dokumen
WHERE d.file_path IN (
    'fixtures/v1.0.0/ownership-document.pdf',
    'fixtures/v1.0.0/repair-original-document.pdf'
)
ORDER BY d.id_dokumen;

-- Copy this final row directly into the matching local runner variables.
SELECT
    (
        SELECT id_kategori
        FROM kategori_perkara
        WHERE nama_kategori = 'Retest Azure v1.0.0'
        ORDER BY id_kategori
        LIMIT 1
    ) AS RETEST_CATEGORY_ID,
    (
        SELECT id_pendaftaran
        FROM pra_pendaftaran_perkara
        WHERE judul_perkara = 'Retest Ownership Azure v1.0.0'
        ORDER BY id_pendaftaran
        LIMIT 1
    ) AS SECURITY_OTHER_CASE_ID,
    (
        SELECT id_pendaftaran
        FROM pra_pendaftaran_perkara
        WHERE judul_perkara = 'Retest Perbaikan Azure v1.0.0'
        ORDER BY id_pendaftaran
        LIMIT 1
    ) AS SECURITY_REPAIR_CASE_ID,
    (
        SELECT id_dokumen
        FROM dokumen_perkara
        WHERE file_path = 'fixtures/v1.0.0/repair-original-document.pdf'
        ORDER BY id_dokumen
        LIMIT 1
    ) AS SECURITY_REPAIR_OLD_DOCUMENT_ID,
    (
        SELECT c.id_catatan
        FROM catatan_verifikasi AS c
        INNER JOIN dokumen_perkara AS d ON d.id_dokumen = c.id_dokumen
        WHERE d.file_path = 'fixtures/v1.0.0/repair-original-document.pdf'
          AND c.status_perbaikan = 'belum_diperbaiki'
        ORDER BY c.id_catatan
        LIMIT 1
    ) AS SECURITY_REPAIR_NOTE_ID;
