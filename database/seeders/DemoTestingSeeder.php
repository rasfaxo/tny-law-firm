<?php

namespace Database\Seeders;

use App\Enums\RoleUser;
use App\Enums\StatusBooking;
use App\Enums\StatusDokumen;
use App\Enums\StatusKonfirmasi;
use App\Enums\StatusPengajuan;
use App\Enums\StatusReschedule;
use App\Enums\StatusSlot;
use App\Models\BookingKonsultasi;
use App\Models\CatatanVerifikasi;
use App\Models\DokumenPerkara;
use App\Models\JadwalKonsultasi;
use App\Models\KategoriPerkara;
use App\Models\PermintaanReschedule;
use App\Models\PraPendaftaranPerkara;
use App\Models\ProfilKlien;
use App\Models\RiwayatStatus;
use App\Models\User;
use App\Models\VerifikasiBerkas;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use RuntimeException;

class DemoTestingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('DemoTestingSeeder tidak boleh dijalankan di environment production.');
        }

        $this->command?->info('Memulai seeding dataset demo komprehensif TNY & PARTNERS...');

        // 1. Prepare Physical Sample Files in Storage
        $sampleFiles = $this->prepareStorageSampleFiles();

        // 2. Master Kategori Perkara
        $categories = $this->seedKategoriPerkara();

        // 3. Users (Admin, Staf Legal, Klien) & Profiles
        $users = $this->seedUsersAndProfiles();

        // 4. Jadwal Konsultasi Slots by Admin
        $jadwalSlots = $this->seedJadwalKonsultasi($users['admin']);

        // 5. Pra-Pendaftaran Perkara, Dokumen, Verifikasi, Booking, Reschedule, & Riwayat Status
        $this->seedCompleteCaseWorkflows($categories, $users, $sampleFiles, $jadwalSlots);

        $this->command?->info('Seeding dataset demo komprehensif TNY & PARTNERS berhasil diselesaikan!');
    }

    /**
     * Prepare sample dummy PDF and JPG files in Laravel Storage.
     */
    private function prepareStorageSampleFiles(): array
    {
        $storageDir = storage_path('app/public/dokumen-perkara');
        if (!File::exists($storageDir)) {
            File::makeDirectory($storageDir, 0755, true);
        }

        // Minimal valid PDF byte sequence
        $minimalPdf = "%PDF-1.4\n1 0 obj<</Type/Catalog/Pages 2 0 R>>endobj\n2 0 obj<</Type/Pages/Kids[3 0 R]/Count 1>>endobj\n3 0 obj<</Type/Page/MediaBox[0 0 595 842]/Parent 2 0 R/Resources<<>>>>endobj\nxref\n0 4\n0000000000 65535 f \n0000000009 00000 n \n0000000052 00000 n \n0000000101 00000 n \ntrailer<</Size 4/Root 1 0 R>>\nstartxref\n178\n%%EOF";

        // Minimal valid 1x1 JPG
        $minimalJpg = base64_decode('/9j/4AAQSkZJRgABAQEASABIAAD/2wBDAP//////////////////////////////////////////////////////////////////////////////////////wgALCAABAAEBAREA/8QAFBABAAAAAAAAAAAAAAAAAAAAAP/aAAgBAQABPxA=');

        $files = [
            'identitas_ktp.pdf' => $minimalPdf,
            'kartu_keluarga.pdf' => $minimalPdf,
            'surat_kuasa_khusus.pdf' => $minimalPdf,
            'dokumen_perjanjian_kontrak.pdf' => $minimalPdf,
            'bukti_transfer_pembayaran.pdf' => $minimalPdf,
            'sertifikat_hak_milik.pdf' => $minimalPdf,
            'surat_somasi_peringatan.pdf' => $minimalPdf,
            'lampiran_bukti_pendukung.pdf' => $minimalPdf,
            'foto_bukti_lapangan.jpg' => $minimalJpg,
        ];

        $relativePaths = [];
        foreach ($files as $name => $content) {
            $fullPath = $storageDir . DIRECTORY_SEPARATOR . $name;
            if (!File::exists($fullPath)) {
                File::put($fullPath, $content);
            }
            $relativePaths[$name] = 'dokumen-perkara/' . $name;
        }

        return $relativePaths;
    }

    /**
     * Seed Categories.
     */
    private function seedKategoriPerkara(): array
    {
        $categoriesData = [
            [
                'nama_kategori' => 'Perdata',
                'deskripsi' => 'Perkara hukum perdata, sengketa bisnis, wanprestasi, perbuatan melawan hukum, dan perjanjian kerja sama komersial.',
            ],
            [
                'nama_kategori' => 'Pidana',
                'deskripsi' => 'Perkara hukum pidana umum dan khusus, serta pendampingan hukum terkait proses penyidikan, penuntutan, dan persidangan.',
            ],
            [
                'nama_kategori' => 'Keluarga',
                'deskripsi' => 'Perkara hukum perkawinan, perceraian, pembagian harta bersama/gono-gini, hak asuh anak, dan waris.',
            ],
            [
                'nama_kategori' => 'Ketenagakerjaan',
                'deskripsi' => 'Perkara hubungan industrial, perselisihan pemutusan hubungan kerja (PHK), hak pesangon, dan sengketa ketenagakerjaan.',
            ],
        ];

        $categories = [];
        foreach ($categoriesData as $data) {
            $cat = KategoriPerkara::firstOrCreate(
                ['nama_kategori' => $data['nama_kategori']],
                ['deskripsi' => $data['deskripsi']]
            );
            $categories[$data['nama_kategori']] = $cat;
        }

        return $categories;
    }

    /**
     * Seed Admin, Staf Legal, and Klien Users with Profiles.
     */
    private function seedUsersAndProfiles(): array
    {
        $passwordHash = Hash::make('Password123!');

        // 1. Admin Users
        $adminTesting = User::updateOrCreate(
            ['email' => 'admin.testing@tny.test'],
            [
                'nama' => 'Admin Testing TNY',
                'password' => $passwordHash,
                'role' => RoleUser::Admin->value,
                'no_telepon' => '081234567890',
                'status_akun' => 'aktif',
            ]
        );

        $adminDefault = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            [
                'nama' => 'Admin TNY Law Firm',
                'password' => $passwordHash,
                'role' => RoleUser::Admin->value,
                'no_telepon' => '081299998888',
                'status_akun' => 'aktif',
            ]
        );

        // 2. Staf Legal Users
        $legalUsersData = [
            [
                'email' => 'legal1.testing@tny.test',
                'nama' => 'Bima Arya Pratama, S.H.',
                'no_telepon' => '081311223344',
            ],
            [
                'email' => 'legal2.testing@tny.test',
                'nama' => 'Siti Rahmadani, S.H., M.H.',
                'no_telepon' => '081322334455',
            ],
            [
                'email' => 'legal3.testing@tny.test',
                'nama' => 'Dedi Kurniawan, S.H.',
                'no_telepon' => '081333445566',
            ],
        ];

        $legalUsers = [];
        foreach ($legalUsersData as $data) {
            $legal = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'nama' => $data['nama'],
                    'password' => $passwordHash,
                    'role' => RoleUser::StafLegal->value,
                    'no_telepon' => $data['no_telepon'],
                    'status_akun' => 'aktif',
                ]
            );
            $legalUsers[] = $legal;
        }

        // 3. Client Users (20 Klien)
        $clientProfilesData = [
            [
                'email' => 'client001@tny.test',
                'nama' => 'Ahmad Fauzi Rahman',
                'telp' => '081211110001',
                'alamat' => 'Jl. Tebet Barat Dalam Raya No. 45, Jakarta Selatan',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Direktur PT Cahaya Abadi Logistik',
                'nik' => '3174011203850001',
            ],
            [
                'email' => 'client002@tny.test',
                'nama' => 'Dewi Anggraini Permata',
                'telp' => '081211110002',
                'alamat' => 'Jl. Radio Dalam Raya Blok A No. 12, Kebayoran Baru, Jakarta Selatan',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Wiraswasta / Pemilik Butik',
                'nik' => '3174025408900002',
            ],
            [
                'email' => 'client003@tny.test',
                'nama' => 'Hendra Wijaya Kusumah',
                'telp' => '081211110003',
                'alamat' => 'Komplek Permata Hijau Blok D-8, Jakarta Selatan',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Komisaris PT Nusantara Propertindo',
                'nik' => '3174032105780003',
            ],
            [
                'email' => 'client004@tny.test',
                'nama' => 'Rina Marlina Siregar, S.E.',
                'telp' => '081211110004',
                'alamat' => 'Jl. Kemang Raya No. 28, Mampang Prapatan, Jakarta Selatan',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Konsultan Keuangan',
                'nik' => '3174046511870004',
            ],
            [
                'email' => 'client005@tny.test',
                'nama' => 'Ir. Muhammad Ridwan Hidayat',
                'telp' => '081211110005',
                'alamat' => 'Jl. Margonda Raya No. 102, Pondok Cina, Beji, Depok',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Kontraktor Sipil & Arsitek',
                'nik' => '3276011409820005',
            ],
            [
                'email' => 'client006@tny.test',
                'nama' => 'drg. Melani Kartika Putri',
                'telp' => '081211110006',
                'alamat' => 'Jl. Bintaro Utama Sektor 9 Blok HB No. 15, Tangerang Selatan',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Dokter Gigi Spesialis',
                'nik' => '3674026307890006',
            ],
            [
                'email' => 'client007@tny.test',
                'nama' => 'Bambang Sudarsono, S.H.',
                'telp' => '081211110007',
                'alamat' => 'Jl. Boulevard Raya Blok QJ No. 9, Kelapa Gading, Jakarta Utara',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'General Manager HRD',
                'nik' => '3172010912750007',
            ],
            [
                'email' => 'client008@tny.test',
                'nama' => 'Siti Nurhaliza Wulandari',
                'telp' => '081211110008',
                'alamat' => 'Jl. Gandaria Tengah II No. 17, Kramat Pela, Kebayoran Baru, Jakarta Selatan',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Pegawai Negeri Sipil (PNS)',
                'nik' => '3174025001920008',
            ],
            [
                'email' => 'client009@tny.test',
                'nama' => 'Eko Prasetyo Utomo',
                'telp' => '081211110009',
                'alamat' => 'Jl. Jenderal Sudirman Kav. 52-53, Senayan, Jakarta Selatan',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Direktur Operasional PT Sinar Samudra',
                'nik' => '3174031506840009',
            ],
            [
                'email' => 'client010@tny.test',
                'nama' => 'Farida Hanum Lubis',
                'telp' => '081211110010',
                'alamat' => 'Jl. Pejaten Barat No. 88, Pasar Minggu, Jakarta Selatan',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Dosen / Akademisi',
                'nik' => '3174054402800010',
            ],
            [
                'email' => 'client011@tny.test',
                'nama' => 'Gunawan Tri Atmojo',
                'telp' => '081211110011',
                'alamat' => 'Jl. Puri Indah Raya Blok A No. 3, Kembangan, Jakarta Barat',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Pengusaha Distribusi Farmasi',
                'nik' => '3173012210810011',
            ],
            [
                'email' => 'client012@tny.test',
                'nama' => 'Ratna Sari Dewi, M.Sc.',
                'telp' => '081211110012',
                'alamat' => 'Jl. KH. Ahmad Dahlan No. 19, Sukasari, Tangerang',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Peneliti Lembaga Riset',
                'nik' => '3671025504860012',
            ],
            [
                'email' => 'client013@tny.test',
                'nama' => 'Raden Mas Haryo Danu Kusumo',
                'telp' => '081211110013',
                'alamat' => 'Jl. Cik Ditiro No. 40, Menteng, Jakarta Pusat',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Pengembang Properti Residensial',
                'nik' => '3171011804790013',
            ],
            [
                'email' => 'client014@tny.test',
                'nama' => 'Novita Christine Simanjuntak',
                'telp' => '081211110014',
                'alamat' => 'Jl. Fatmawati Raya No. 64, Cilandak Barat, Jakarta Selatan',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Manajer Pemasaran Global',
                'nik' => '3174066011930014',
            ],
            [
                'email' => 'client015@tny.test',
                'nama' => 'Kurnia Ramadhan Saputra',
                'telp' => '081211110015',
                'alamat' => 'Jl. Pajajaran No. 78, Baranangsiang, Bogor Timur',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Chief Technology Officer (CTO)',
                'nik' => '3271012508910015',
            ],
            [
                'email' => 'client016@tny.test',
                'nama' => 'Yuliana Tanuwidjaja',
                'telp' => '081211110016',
                'alamat' => 'Komplek Pantai Indah Kapuk Blok B No. 21, Penjaringan, Jakarta Utara',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Eksportir Hasil Perikanan',
                'nik' => '3172024803880016',
            ],
            [
                'email' => 'client017@tny.test',
                'nama' => 'Zainal Abidin Syahputra, S.T.',
                'telp' => '081211110017',
                'alamat' => 'Jl. Dago Asri No. 14, Coblong, Bandung',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Konsultan Energi Terbarukan',
                'nik' => '3273010705830017',
            ],
            [
                'email' => 'client018@tny.test',
                'nama' => 'Grace Natalia Manurung',
                'telp' => '081211110018',
                'alamat' => 'Jl. Rawamangun Muka Raya No. 11, Pulo Gadung, Jakarta Timur',
                'jk' => 'Perempuan',
                'pekerjaan' => 'Notaris Pengganti',
                'nik' => '3175016212850018',
            ],
            [
                'email' => 'client019@tny.test',
                'nama' => 'Faisal Basri Al-Habsyi',
                'telp' => '081211110019',
                'alamat' => 'Jl. Otista Raya No. 90, Jatinegara, Jakarta Timur',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Pedagang Grosir Otomotif',
                'nik' => '3175021008820019',
            ],
            // Edge Case Client: Long Name
            [
                'email' => 'client020@tny.test',
                'nama' => 'Dr. H. Raden Muhammad Cokroadiningrat Suryohadiprojo, S.E., M.M.',
                'telp' => '081211110020',
                'alamat' => 'Kawasan Perumahan Elit Bukit Golf Mediterania Blok Victoria Crown No. 99, Jakarta Utara',
                'jk' => 'Laki-laki',
                'pekerjaan' => 'Senior Vice President Holding Corporate Banking',
                'nik' => '3172010101700020',
            ],
        ];

        $clientUsers = [];
        foreach ($clientProfilesData as $data) {
            $client = User::updateOrCreate(
                ['email' => $data['email']],
                [
                    'nama' => $data['nama'],
                    'password' => $passwordHash,
                    'role' => RoleUser::Klien->value,
                    'no_telepon' => $data['telp'],
                    'status_akun' => 'aktif',
                ]
            );

            ProfilKlien::updateOrCreate(
                ['id_user' => $client->id_user],
                [
                    'alamat' => $data['alamat'],
                    'jenis_kelamin' => $data['jk'],
                    'pekerjaan' => $data['pekerjaan'],
                    'no_identitas' => $data['nik'],
                ]
            );

            $clientUsers[] = $client;
        }

        return [
            'admin' => $adminTesting,
            'adminDefault' => $adminDefault,
            'legal' => $legalUsers,
            'clients' => $clientUsers,
        ];
    }

    /**
     * Seed Jadwal Konsultasi Slots.
     */
    private function seedJadwalKonsultasi(User $admin): array
    {
        $baseDates = [
            '2026-07-06', '2026-07-08', '2026-07-10', '2026-07-13', '2026-07-15',
            '2026-07-20', '2026-07-22', '2026-07-27', '2026-08-03', '2026-08-05',
            '2026-08-10', '2026-08-12', '2026-08-17', '2026-08-19', '2026-08-24',
            '2026-08-26', '2026-09-02', '2026-09-07', '2026-09-09', '2026-09-14',
        ];

        $timeSlots = [
            ['mulai' => '09:00:00', 'selesai' => '10:30:00'],
            ['mulai' => '10:45:00', 'selesai' => '12:15:00'],
            ['mulai' => '13:30:00', 'selesai' => '15:00:00'],
            ['mulai' => '15:15:00', 'selesai' => '16:45:00'],
        ];

        $slots = [];
        $index = 0;
        foreach ($baseDates as $d) {
            foreach ($timeSlots as $t) {
                $statusSlot = ($index % 5 === 0) ? StatusSlot::Tersedia->value : StatusSlot::Terisi->value;
                if ($index % 17 === 0) {
                    $statusSlot = StatusSlot::TidakAktif->value;
                }

                $jadwal = JadwalKonsultasi::updateOrCreate(
                    [
                        'id_user' => $admin->id_user,
                        'tanggal' => $d,
                        'waktu_mulai' => $t['mulai'],
                        'waktu_selesai' => $t['selesai'],
                    ],
                    [
                        'status_slot' => $statusSlot,
                    ]
                );

                $slots[] = $jadwal;
                $index++;
            }
        }

        return $slots;
    }

    /**
     * Seed All 40 Cases with Complete Business Lifecycle.
     */
    private function seedCompleteCaseWorkflows(array $categories, array $users, array $sampleFiles, array $jadwalSlots): void
    {
        $clients = $users['clients'];
        $legalList = $users['legal'];
        $admin = $users['admin'];

        // Case Templates (40 Realistic Cases in Indonesian Legal Context)
        $casesData = [
            // --- GRUP 1: STATUS SELESAI (Perkara 1 s/d 8) ---
            [
                'client_idx' => 0,
                'kategori' => 'Perdata',
                'judul' => 'Gugatan Wanprestasi Perjanjian Kerjasama Pengadaan Alat Berat Proyek Tambang Batubara',
                'kronologi' => 'Klien telah menandatangani kontrak penyewaan 10 unit excavator senilai Rp 3.500.000.000 dengan pihak Tergugat. Namun sejak bulan Maret 2026 pihak Tergugat tidak melakukan pembayaran sewa selama 4 bulan berturut-turut dan menolak mengembalikan unit alat berat.',
                'status' => StatusPengajuan::Selesai->value,
                'tgl_pengajuan' => '2026-07-01 09:15:00',
                'tgl_verif' => '2026-07-02 11:30:00',
                'tgl_booking' => '2026-07-03 14:00:00',
                'tgl_selesai' => '2026-07-08 16:00:00',
                'legal_idx' => 0,
                'jadwal_idx' => 1,
                'booking_status' => StatusBooking::Selesai->value,
                'metode' => 'offline',
                'reschedule' => false,
            ],
            [
                'client_idx' => 1,
                'kategori' => 'Keluarga',
                'judul' => 'Permohonan Gugatan Perceraian dan Penetapan Hak Asuh Anak Serta Nafkah Mut\'ah',
                'kronologi' => 'Pernikahan yang dibina selama 9 tahun telah mengalami perselisihan terus menerus akibat pihak Tergugat meninggalkan tempat tinggal bersama sejak akhir tahun 2025 dan tidak memberikan nafkah lahir maupun batin kepada Klien dan 2 orang anak.',
                'status' => StatusPengajuan::Selesai->value,
                'tgl_pengajuan' => '2026-07-02 10:00:00',
                'tgl_verif' => '2026-07-03 13:45:00',
                'tgl_booking' => '2026-07-04 15:30:00',
                'tgl_selesai' => '2026-07-10 12:00:00',
                'legal_idx' => 1,
                'jadwal_idx' => 2,
                'booking_status' => StatusBooking::Selesai->value,
                'metode' => 'online',
                'reschedule' => false,
            ],
            [
                'client_idx' => 2,
                'kategori' => 'Perdata',
                'judul' => 'Sengketa Kepemilikan Hak Milik Atas Tanah Waris Sertifikat Hak Milik No. 452/Gandaria',
                'kronologi' => 'Tanah warisan seluas 1.200 m2 atas nama almarhum orang tua Klien diklaim secara sepihak oleh pihak ketiga menggunakan girik palsu dan telah didirikan pagar pembatas tanpa izin ahli waris yang sah.',
                'status' => StatusPengajuan::Selesai->value,
                'tgl_pengajuan' => '2026-07-03 11:20:00',
                'tgl_verif' => '2026-07-04 10:00:00',
                'tgl_booking' => '2026-07-05 09:40:00',
                'tgl_selesai' => '2026-07-13 15:30:00',
                'legal_idx' => 2,
                'jadwal_idx' => 3,
                'booking_status' => StatusBooking::Selesai->value,
                'metode' => 'offline',
                'reschedule' => false,
            ],
            [
                'client_idx' => 3,
                'kategori' => 'Ketenagakerjaan',
                'judul' => 'Perselisihan Pemutusan Hubungan Kerja (PHK) Sepihak dan Tuntutan Pembayaran Uang Pesangon',
                'kronologi' => 'Klien telah bekerja selama 7 tahun sebagai Manajer Operasional pada PT Bahari Global, namun mengalami PHK sepihak dengan alasan efisiensi tanpa melalui proses bipartit dan menolak membayarkan hak pesangon sesuai UU Ketenagakerjaan.',
                'status' => StatusPengajuan::Selesai->value,
                'tgl_pengajuan' => '2026-07-05 14:00:00',
                'tgl_verif' => '2026-07-06 16:10:00',
                'tgl_booking' => '2026-07-07 11:00:00',
                'tgl_selesai' => '2026-07-15 17:00:00',
                'legal_idx' => 0,
                'jadwal_idx' => 4,
                'booking_status' => StatusBooking::Selesai->value,
                'metode' => 'offline',
                'reschedule' => false,
            ],
            [
                'client_idx' => 4,
                'kategori' => 'Pidana',
                'judul' => 'Laporan Tindak Pidana Penipuan dan Penggelapan Dana Investasi Saham Properti Rp 1,8 Miliar',
                'kronologi' => 'Terlapor menawarkan program investasi pembangunan perumahan dengan janji imbal hasil 15% per tahun. Setelah dana disetorkan sebesar Rp 1,8 Miliar, proyek fiktif dan Terlapor menghilang serta memblokir komunikasi.',
                'status' => StatusPengajuan::Selesai->value,
                'tgl_pengajuan' => '2026-07-08 09:30:00',
                'tgl_verif' => '2026-07-09 10:45:00',
                'tgl_booking' => '2026-07-10 13:20:00',
                'tgl_selesai' => '2026-07-20 16:30:00',
                'legal_idx' => 1,
                'jadwal_idx' => 5,
                'booking_status' => StatusBooking::Selesai->value,
                'metode' => 'offline',
                'reschedule' => false,
            ],
            [
                'client_idx' => 5,
                'kategori' => 'Perdata',
                'judul' => 'Gugatan Perbuatan Melawan Hukum (PMH) Penyerobotan Batas Lahan dan Perusakan Pagar',
                'kronologi' => 'Tergugat melakukan perluasan bangunan gudang hingga melampaui batas patok BPN sah milik Klien seluas 180 m2 dan merobohkan pagar beton pembatas tanpa persetujuan.',
                'status' => StatusPengajuan::Selesai->value,
                'tgl_pengajuan' => '2026-07-10 13:10:00',
                'tgl_verif' => '2026-07-11 15:00:00',
                'tgl_booking' => '2026-07-12 10:30:00',
                'tgl_selesai' => '2026-07-22 14:00:00',
                'legal_idx' => 2,
                'jadwal_idx' => 6,
                'booking_status' => StatusBooking::Selesai->value,
                'metode' => 'online',
                'reschedule' => false,
            ],
            [
                'client_idx' => 6,
                'kategori' => 'Keluarga',
                'judul' => 'Sengketa Pembagian Harta Bersama (Gono-Gini) Berupa 3 Unit Ruko dan Aset Deposito',
                'kronologi' => 'Pasca perceraian yang telah berkekuatan hukum tetap, pihak Mantan Suami menguasai seluruh aset yang diperoleh selama masa perkawinan dan menolak melakukan pembagian secara adil.',
                'status' => StatusPengajuan::Selesai->value,
                'tgl_pengajuan' => '2026-07-12 14:45:00',
                'tgl_verif' => '2026-07-13 11:15:00',
                'tgl_booking' => '2026-07-14 09:15:00',
                'tgl_selesai' => '2026-07-27 16:00:00',
                'legal_idx' => 0,
                'jadwal_idx' => 7,
                'booking_status' => StatusBooking::Selesai->value,
                'metode' => 'offline',
                'reschedule' => false,
            ],
            [
                'client_idx' => 7,
                'kategori' => 'Ketenagakerjaan',
                'judul' => 'Tuntutan Pembayaran Upah Lembur dan Hak Jamsostek yang Tidak Disetorkan Perusahaan',
                'kronologi' => 'Perusahaan telah memotong iuran BPJS Ketenagakerjaan dari gaji Klien selama 3 tahun namun tidak disetorkan ke BPJS, serta menolak membayar kewajiban uang lembur yang telah diverifikasi.',
                'status' => StatusPengajuan::Selesai->value,
                'tgl_pengajuan' => '2026-07-15 10:30:00',
                'tgl_verif' => '2026-07-16 14:00:00',
                'tgl_booking' => '2026-07-17 11:45:00',
                'tgl_selesai' => '2026-08-03 15:00:00',
                'legal_idx' => 1,
                'jadwal_idx' => 8,
                'booking_status' => StatusBooking::Selesai->value,
                'metode' => 'offline',
                'reschedule' => false,
            ],

            // --- GRUP 2: STATUS JADWAL DIPILIH / BOOKING KONSULTASI AKTIF (Perkara 9 s/d 16) ---
            [
                'client_idx' => 8,
                'kategori' => 'Perdata',
                'judul' => 'Gugatan Pembatalan Akta Jual Beli Tanah Karena Cacat Hukum dan Penipuan Kuasa Jual',
                'kronologi' => 'AJB ditandatangani di hadapan PPAT menggunakan surat kuasa palsu yang tidak pernah diberikan oleh Klien sebagai pemilik sah sertifikat tanah.',
                'status' => StatusPengajuan::JadwalDipilih->value,
                'tgl_pengajuan' => '2026-07-20 09:00:00',
                'tgl_verif' => '2026-07-21 10:30:00',
                'tgl_booking' => '2026-07-22 14:00:00',
                'legal_idx' => 2,
                'jadwal_idx' => 9,
                'booking_status' => StatusBooking::Aktif->value,
                'metode' => 'offline',
                'reschedule' => false,
            ],
            [
                'client_idx' => 9,
                'kategori' => 'Pidana',
                'judul' => 'Pendampingan Hukum Korban Tindak Pidana Penganiayaan dan Pengeroyokan Pasal 170 KUHP',
                'kronologi' => 'Klien menjadi korban pengeroyokan oleh oknum sekelompok orang di area parkir komersial yang mengakibatkan luka berat dan rawat inap intensif.',
                'status' => StatusPengajuan::JadwalDipilih->value,
                'tgl_pengajuan' => '2026-07-22 11:30:00',
                'tgl_verif' => '2026-07-23 13:00:00',
                'tgl_booking' => '2026-07-24 15:45:00',
                'legal_idx' => 0,
                'jadwal_idx' => 10,
                'booking_status' => StatusBooking::Aktif->value,
                'metode' => 'offline',
                'reschedule' => false,
            ],
            [
                'client_idx' => 10,
                'kategori' => 'Perdata',
                'judul' => 'Sengketa Wanprestasi Perjanjian Kerjasama Franchise Restoran Cepat Saji',
                'kronologi' => 'Pemberi waralaba (Franchisor) tidak mengirimkan pasokan bahan baku sesuai standar SOP dan melanggar kesepakatan zonasi eksklusif wilayah.',
                'status' => StatusPengajuan::JadwalDipilih->value,
                'tgl_pengajuan' => '2026-07-25 14:15:00',
                'tgl_verif' => '2026-07-26 15:30:00',
                'tgl_booking' => '2026-07-27 10:00:00',
                'legal_idx' => 1,
                'jadwal_idx' => 11,
                'booking_status' => StatusBooking::Aktif->value,
                'metode' => 'online',
                'reschedule' => true, // Reschedule Case: Disetujui
                'reschedule_status' => StatusReschedule::Disetujui->value,
                'reschedule_alasan' => 'Ada jadwal audit mendadak dari kantor pusat pada jam yang sama, mohon digeser ke jadwal hari berikutnya.',
                'reschedule_jadwal_baru_idx' => 12,
            ],
            [
                'client_idx' => 11,
                'kategori' => 'Keluarga',
                'judul' => 'Permohonan Penetapan Wali dan Pengampuan Terhadap Ahli Waris Bawah Umur',
                'kronologi' => 'Kedua orang tua anak telah meninggal dunia, diperlukan penetapan pengadilan agar paman kandung dapat bertindak sah mengurus hak pendidikan dan aset anak.',
                'status' => StatusPengajuan::JadwalDipilih->value,
                'tgl_pengajuan' => '2026-07-28 09:45:00',
                'tgl_verif' => '2026-07-29 11:20:00',
                'tgl_booking' => '2026-07-30 14:30:00',
                'legal_idx' => 2,
                'jadwal_idx' => 13,
                'booking_status' => StatusBooking::Aktif->value,
                'metode' => 'offline',
                'reschedule' => true, // Reschedule Case: Disetujui
                'reschedule_status' => StatusReschedule::Disetujui->value,
                'reschedule_alasan' => 'Klien harus menghadiri sidang luar kota pada tanggal tersebut.',
                'reschedule_jadwal_baru_idx' => 14,
            ],
            [
                'client_idx' => 12,
                'kategori' => 'Ketenagakerjaan',
                'judul' => 'Gugatan Perselisihan Hak Mutasi Kerja Sepihak Antar Pulau Tanpa Tunjangan Penugasan',
                'kronologi' => 'Pekerja dimutasi sepihak dari kantor Jakarta ke cabang pedalaman Kalimantan tanpa fasilitas tempat tinggal dan tunjangan yang dijanjikan dalam PKB.',
                'status' => StatusPengajuan::JadwalDipilih->value,
                'tgl_pengajuan' => '2026-08-01 10:15:00',
                'tgl_verif' => '2026-08-02 13:40:00',
                'tgl_booking' => '2026-08-03 16:00:00',
                'legal_idx' => 0,
                'jadwal_idx' => 15,
                'booking_status' => StatusBooking::Aktif->value,
                'metode' => 'offline',
                'reschedule' => true, // Reschedule Case: Menunggu Persetujuan
                'reschedule_status' => StatusReschedule::MenungguPersetujuan->value,
                'reschedule_alasan' => 'Penerbangan tertunda karena cuaca buruk, mohon dijadwalkan sesi daring di hari Rabu.',
                'reschedule_preferensi' => 'Hari Rabu, jam 14:00 - 15:30 WIB (Metode Online)',
            ],
            [
                'client_idx' => 13,
                'kategori' => 'Perdata',
                'judul' => 'Sengketa Keterlambatan Serah Terima Unit Rumah Mewah oleh Pengembang Properti',
                'kronologi' => 'Developer menjanjikan serah terima kunci pada Desember 2025, namun hingga Agustus 2026 bangunan belum selesai 50% dan developer menolak klausul penalti.',
                'status' => StatusPengajuan::JadwalDipilih->value,
                'tgl_pengajuan' => '2026-08-03 13:30:00',
                'tgl_verif' => '2026-08-04 14:50:00',
                'tgl_booking' => '2026-08-05 10:15:00',
                'legal_idx' => 1,
                'jadwal_idx' => 16,
                'booking_status' => StatusBooking::Aktif->value,
                'metode' => 'offline',
                'reschedule' => true, // Reschedule Case: Menunggu Persetujuan
                'reschedule_status' => StatusReschedule::MenungguPersetujuan->value,
                'reschedule_alasan' => 'Ada bentrok jadwal pemeriksaan medis rumah sakit.',
                'reschedule_preferensi' => 'Hari Kamis pagi atau Jumat pagi',
            ],
            [
                'client_idx' => 14,
                'kategori' => 'Pidana',
                'judul' => 'Pendampingan Pelaporan Dugaan Pencemaran Nama Baik dan Fitnah Melalui Media Sosial UU ITE',
                'kronologi' => 'Akun anonim menyebarkan konten manipulasi foto dan dokumen palsu yang menuduh Klien melakukan korupsi tender pengadaan.',
                'status' => StatusPengajuan::JadwalDipilih->value,
                'tgl_pengajuan' => '2026-08-05 15:00:00',
                'tgl_verif' => '2026-08-06 16:30:00',
                'tgl_booking' => '2026-08-07 11:20:00',
                'legal_idx' => 2,
                'jadwal_idx' => 17,
                'booking_status' => StatusBooking::Aktif->value,
                'metode' => 'online',
                'reschedule' => true, // Reschedule Case: Ditolak
                'reschedule_status' => StatusReschedule::Ditolak->value,
                'reschedule_alasan' => 'Minta diundur jam konsultasi ke pukul 21:00 malam.',
                'reschedule_catatan_admin' => 'Permintaan ditolak karena kantor hukum kami tidak melayani sesi konsultasi di luar jam kerja operasional (maksimal pukul 17:00 WIB).',
            ],
            [
                'client_idx' => 15,
                'kategori' => 'Keluarga',
                'judul' => 'Permohonan Itsbat Nikah Terhadap Pernikahan Siri Sah Agama Demi Penerbitan Akta Kelahiran',
                'kronologi' => 'Pernikahan dilaksanakan tahun 2018 secara sah menurut syariat Islam namun belum tercatat di KUA, sehingga anak kesulitan mendapatkan akta lahir dan paspor.',
                'status' => StatusPengajuan::JadwalDipilih->value,
                'tgl_pengajuan' => '2026-08-07 09:10:00',
                'tgl_verif' => '2026-08-08 10:20:00',
                'tgl_booking' => '2026-08-09 13:45:00',
                'legal_idx' => 0,
                'jadwal_idx' => 18,
                'booking_status' => StatusBooking::Aktif->value,
                'metode' => 'offline',
                'reschedule' => true, // Reschedule Case: Ditolak
                'reschedule_status' => StatusReschedule::Ditolak->value,
                'reschedule_alasan' => 'Ingin diganti hari Minggu.',
                'reschedule_catatan_admin' => 'Sesi konsultasi pada akhir pekan libur operasional kantor.',
            ],

            // --- GRUP 3: STATUS BERKAS LENGKAP (Perkara 17 s/d 24) ---
            [
                'client_idx' => 16,
                'kategori' => 'Perdata',
                'judul' => 'Gugatan Wanprestasi Pembayaran Suplai Bahan Bangunan Proyek Konstruksi Rp 850 Juta',
                'kronologi' => 'Barang semen dan besi beton telah diterima utuh dengan Berita Acara Serah Terima, namun pembayaran giro mundur tertolak oleh bank.',
                'status' => StatusPengajuan::BerkasLengkap->value,
                'tgl_pengajuan' => '2026-08-08 11:00:00',
                'tgl_verif' => '2026-08-09 14:15:00',
                'legal_idx' => 1,
                'catatan_verif' => 'Seluruh berkas invoice, BAST, dan surat penolakan kliring bank telah lengkap dan valid.',
            ],
            [
                'client_idx' => 17,
                'kategori' => 'Ketenagakerjaan',
                'judul' => 'Sengketa Pemotongan Bonus Kinerja Tahunan Tanpa Dasar Peraturan Perusahaan',
                'kronologi' => 'Perusahaan secara sepihak memotong bonus tahunan 50 karyawan dengan dalih kerugian cabang lain yang tidak ada hubungannya dengan target divisi Klien.',
                'status' => StatusPengajuan::BerkasLengkap->value,
                'tgl_pengajuan' => '2026-08-09 13:40:00',
                'tgl_verif' => '2026-08-10 15:30:00',
                'legal_idx' => 2,
                'catatan_verif' => 'Perjanjian Kerja Bersama dan slip gaji telah memenuhi syarat pemeriksaan.',
            ],
            [
                'client_idx' => 18,
                'kategori' => 'Pidana',
                'judul' => 'Pendampingan Saksi Kasus Dugaan Pelanggaran Rahasia Dagang dan Pembajakan Database Klien',
                'kronologi' => 'Mantan karyawan Klien mendirikan perusahaan pesaing dengan mencuri data sensitif pelanggan dan formula produk.',
                'status' => StatusPengajuan::BerkasLengkap->value,
                'tgl_pengajuan' => '2026-08-10 09:20:00',
                'tgl_verif' => '2026-08-11 11:00:00',
                'legal_idx' => 0,
                'catatan_verif' => 'Bukti forensik digital log server dan surat perjanjian kerahasiaan (NDA) lengkap.',
            ],
            [
                'client_idx' => 19,
                'kategori' => 'Keluarga',
                'judul' => 'Permohonan Pengangkatan Anak (Adopsi Legal) Melalui Pengadilan Negeri',
                'kronologi' => 'Pasangan suami istri yang telah merawat anak sejak lahir bermaksud meresmikan status pengangkatan anak secara hukum formal negara.',
                'status' => StatusPengajuan::BerkasLengkap->value,
                'tgl_pengajuan' => '2026-08-11 10:45:00',
                'tgl_verif' => '2026-08-12 14:00:00',
                'legal_idx' => 1,
                'catatan_verif' => 'Surat rekomendasi Dinas Sosial dan akta penyerahan dari orang tua kandung lengkap.',
            ],
            // Edge Case: Long Title
            [
                'client_idx' => 19,
                'kategori' => 'Perdata',
                'judul' => 'Gugatan Perbuatan Melawan Hukum Terhadap Tindakan Pembongkaran Fasilitas Bangunan Tanpa Izin di Kawasan Industri Terpadu Pulo Gadung dan Tuntutan Ganti Kerugian Materiil serta Immaterial Sebesar Dua Puluh Miliar Rupiah',
                'kronologi' => 'Pihak pengelola kawasan industri melakukan pembongkaran paksa akses jalan masuk dan gardu listrik pabrik Klien tanpa adanya putusan pengadilan yang sah sehingga kegiatan produksi terhenti total selama 2 pekan.',
                'status' => StatusPengajuan::BerkasLengkap->value,
                'tgl_pengajuan' => '2026-08-12 14:10:00',
                'tgl_verif' => '2026-08-13 16:20:00',
                'legal_idx' => 2,
                'catatan_verif' => 'Dokumen IMB, HGB, dan foto dokumentasi kerusakan lengkap terlampir.',
            ],
            [
                'client_idx' => 0,
                'kategori' => 'Ketenagakerjaan',
                'judul' => 'Perselisihan Hak Kompensasi Pekerja Kontrak (PKWT) yang Diakhiri Sebelum Waktu Berakhir',
                'kronologi' => 'Kontrak 2 tahun diputus sepihak pada bulan ke-8 tanpa membayar sisa upah masa kontrak yang belum dijalani.',
                'status' => StatusPengajuan::BerkasLengkap->value,
                'tgl_pengajuan' => '2026-08-13 09:30:00',
                'tgl_verif' => '2026-08-14 10:40:00',
                'legal_idx' => 0,
                'catatan_verif' => 'Salinan PKWT dan bukti transfer gaji terakhir valid.',
            ],
            [
                'client_idx' => 1,
                'kategori' => 'Perdata',
                'judul' => 'Sengketa Hak Cipta Desain Motif Tekstil Tradisional Melawan Produsen Pakaian Ritel',
                'kronologi' => 'Karya cipta motif yang telah terdaftar di Ditjen KI diproduksi massal oleh merek busana tanpa lisensi resmi.',
                'status' => StatusPengajuan::BerkasLengkap->value,
                'tgl_pengajuan' => '2026-08-14 11:15:00',
                'tgl_verif' => '2026-08-15 13:30:00',
                'legal_idx' => 1,
                'catatan_verif' => 'Sertifikat Pendaftaran Hak Cipta Kementerian Hukum & HAM terverifikasi sah.',
            ],
            [
                'client_idx' => 2,
                'kategori' => 'Pidana',
                'judul' => 'Laporan Tindak Pidana Penggelapan Jabatan oleh Direktur Keuangan Senilai Rp 2,4 Miliar',
                'kronologi' => 'Audit internal independen menemukan aliran dana kas perusahaan ditransfer ke rekening pribadi keluarga Terlapor.',
                'status' => StatusPengajuan::BerkasLengkap->value,
                'tgl_pengajuan' => '2026-08-15 15:00:00',
                'tgl_verif' => '2026-08-16 09:15:00',
                'legal_idx' => 2,
                'catatan_verif' => 'Laporan Hasil Audit Forensik Akuntan Publik lengkap terlampir.',
            ],

            // --- GRUP 4: STATUS MENUNGGU VERIFIKASI ULANG (Perkara 25 s/d 30) ---
            [
                'client_idx' => 3,
                'kategori' => 'Perdata',
                'judul' => 'Gugatan Pelanggaran Perjanjian Sewa Beli Mesin Pabrik Pengolahan Padi',
                'kronologi' => 'Pihak debitur mengalihkan kepemilikan mesin sewa beli kepada pihak lain tanpa persetujuan tertulis dari Klien.',
                'status' => StatusPengajuan::MenungguVerifikasiUlang->value,
                'tgl_pengajuan' => '2026-08-10 10:00:00',
                'tgl_verif' => '2026-08-11 11:30:00',
                'legal_idx' => 0,
                'catatan_perbaikan' => 'Scan akta sewa beli sebelumnya terpotong pada halaman tandatangan. Klien telah mengunggah ulang dokumen revisi.',
            ],
            [
                'client_idx' => 4,
                'kategori' => 'Keluarga',
                'judul' => 'Permohonan Perubahan Akta Kelahiran Terkait Kesalahan Nama Orang Tua di Catatan Sipil',
                'kronologi' => 'Terdapat perbedaan ejaan nama ayah kandung antara Buku Nikah dan Akta Kelahiran yang menghambat pengurusan visa studi luar negeri.',
                'status' => StatusPengajuan::MenungguVerifikasiUlang->value,
                'tgl_pengajuan' => '2026-08-11 13:00:00',
                'tgl_verif' => '2026-08-12 14:20:00',
                'legal_idx' => 1,
                'catatan_perbaikan' => 'Surat pengantar kelurahan dan KTP orang tua telah diunggah ulang dengan jelas.',
            ],
            [
                'client_idx' => 5,
                'kategori' => 'Ketenagakerjaan',
                'judul' => 'Perselisihan Hak Pesangon Akibat Perusahaan Pailit yang Dinyatakan Pengadilan Niaga',
                'kronologi' => 'Kurator menolak mendaftarkan tagihan pesangon pekerja sebagai kreditur preferen yang diutamakan.',
                'status' => StatusPengajuan::MenungguVerifikasiUlang->value,
                'tgl_pengajuan' => '2026-08-12 09:30:00',
                'tgl_verif' => '2026-08-13 10:50:00',
                'legal_idx' => 2,
                'catatan_perbaikan' => 'Klien telah mengunggah salinan putusan kepailitan Pengadilan Niaga yang berstempel legalisir.',
            ],
            [
                'client_idx' => 6,
                'kategori' => 'Pidana',
                'judul' => 'Laporan Kasus Dugaan Pemalsuan Surat Tanah Girik C No. 201 oleh Oknum Mafia Tanah',
                'kronologi' => 'Klien menemukan warkah tanah lama milik kakek dipalsukan untuk mengajukan sertifikat hak milik baru.',
                'status' => StatusPengajuan::MenungguVerifikasiUlang->value,
                'tgl_pengajuan' => '2026-08-13 11:45:00',
                'tgl_verif' => '2026-08-14 13:10:00',
                'legal_idx' => 0,
                'catatan_perbaikan' => 'Buku C Desa dan surat keterangan riwayat tanah dari kelurahan telah diperbarui.',
            ],
            [
                'client_idx' => 7,
                'kategori' => 'Perdata',
                'judul' => 'Sengketa Wanprestasi Pembayaran Royalti Lisensi Merk Dagang Minuman Kekinian',
                'kronologi' => 'Mitra bisnis membuka 15 gerai baru namun tidak pernah menyetorkan bagi hasil royalti bulanan.',
                'status' => StatusPengajuan::MenungguVerifikasiUlang->value,
                'tgl_pengajuan' => '2026-08-14 14:00:00',
                'tgl_verif' => '2026-08-15 15:30:00',
                'legal_idx' => 1,
                'catatan_perbaikan' => 'Bukti mutasi rekening koran 6 bulan terakhir telah dilengkapi oleh Klien.',
            ],
            [
                'client_idx' => 8,
                'kategori' => 'Keluarga',
                'judul' => 'Gugatan Pembatalan Hibah Rumah Tinggal Akibat Pelanggaran Syarat Moralitas',
                'kronologi' => 'Penerima hibah menelantarkan pemberi hibah yang sudah lanjut usia bertentangan dengan klausul akta hibah.',
                'status' => StatusPengajuan::MenungguVerifikasiUlang->value,
                'tgl_pengajuan' => '2026-08-15 10:20:00',
                'tgl_verif' => '2026-08-16 11:45:00',
                'legal_idx' => 2,
                'catatan_perbaikan' => 'Salinan akta notaris hibah dan surat somasi keluarga telah diunggah ulang.',
            ],

            // --- GRUP 5: STATUS BERKAS TIDAK LENGKAP (Perkara 31 s/d 35) ---
            [
                'client_idx' => 9,
                'kategori' => 'Perdata',
                'judul' => 'Gugatan Ganti Rugi Kerusakan Bangunan Akibat Proyek Penggalian Pondasi Basement Tetangga',
                'kronologi' => 'Dinding rumah Klien mengalami retak struktur parah akibat aktivitas pemancangan paku bumi proyek apartemen sebelah.',
                'status' => StatusPengajuan::BerkasTidakLengkap->value,
                'tgl_pengajuan' => '2026-08-14 09:00:00',
                'tgl_verif' => '2026-08-15 10:30:00',
                'legal_idx' => 0,
                // Edge case: Long Verification Notes
                'catatan_verif' => 'Dokumen hasil penilaian kerugian dari Kantor Jasa Penilai Publik (KJPP) belum dilampirkan, foto dokumentasi belum memuat tanggal pengambilan gambar resmi, serta surat bukti kepemilikan bangunan (IMB/PBG) masih berupa salinan fotokopi yang tidak dilegalisir. Mohon Klien segera mengunggah dokumen asli berformat PDF resolusi tinggi.',
            ],
            [
                'client_idx' => 10,
                'kategori' => 'Ketenagakerjaan',
                'judul' => 'Perselisihan Penolakan Pemberian Cuti Hamil dan Melahirkan oleh Manajemen Rumah Sakit Swasta',
                'kronologi' => 'Pekerja perawat dipaksa mengundurkan diri saat mengajukan hak cuti melahirkan 3 bulan sesuai ketentuan undang-undang.',
                'status' => StatusPengajuan::BerkasTidakLengkap->value,
                'tgl_pengajuan' => '2026-08-14 11:30:00',
                'tgl_verif' => '2026-08-15 14:00:00',
                'legal_idx' => 1,
                'catatan_verif' => 'Surat keterangan dokter kandungan dan bukti surat penolakan cuti dari HRD belum diunggah.',
            ],
            [
                'client_idx' => 11,
                'kategori' => 'Pidana',
                'judul' => 'Pendampingan Laporan Dugaan Pengrusakan Barang Bersama-sama Pasal 406 KUHP',
                'kronologi' => 'Pagar perkebunan kopi dan pos jaga milik Klien dirusak sekelompok massa tak dikenal.',
                'status' => StatusPengajuan::BerkasTidakLengkap->value,
                'tgl_pengajuan' => '2026-08-15 08:45:00',
                'tgl_verif' => '2026-08-16 10:15:00',
                'legal_idx' => 2,
                'catatan_verif' => 'Surat Tanda Penerimaan Laporan (STPL) dari kepolisian belum dilampirkan.',
            ],
            [
                'client_idx' => 12,
                'kategori' => 'Keluarga',
                'judul' => 'Permohonan Penetapan Asal Usul Anak Luar Kawin Hasil Perkawinan Adat',
                'kronologi' => 'Keluarga bermaksud mengesahkan pengakuan anak secara hukum negara pasca orang tua menikah resmi di KUA.',
                'status' => StatusPengajuan::BerkasTidakLengkap->value,
                'tgl_pengajuan' => '2026-08-15 13:15:00',
                'tgl_verif' => '2026-08-16 14:40:00',
                'legal_idx' => 0,
                'catatan_verif' => 'Hasil tes DNA laboratorium forensik belum dilampirkan.',
            ],
            [
                'client_idx' => 13,
                'kategori' => 'Perdata',
                'judul' => 'Sengketa Wanprestasi Pelaksanaan Tender Proyek Pengaspalan Jalan Lingkungan',
                'kronologi' => 'Pemenang tender subkontraktor kabur setelah menerima uang muka 30% tanpa melakukan pekerjaan fisik.',
                'status' => StatusPengajuan::BerkasTidakLengkap->value,
                'tgl_pengajuan' => '2026-08-16 09:20:00',
                'tgl_verif' => '2026-08-16 15:00:00',
                'legal_idx' => 1,
                'catatan_verif' => 'Kwitansi asli tanda terima uang muka dan jaminan pelaksanaan bank garansi belum diunggah.',
            ],

            // --- GRUP 6: STATUS MENUNGGU VERIFIKASI (Perkara 36 s/d 40) ---
            [
                'client_idx' => 14,
                'kategori' => 'Perdata',
                'judul' => 'Gugatan Wanprestasi Pengembalian Uang Muka Pembelian Kapal Tongkang Pasir',
                'kronologi' => 'Penjual membatalkan sepihak transaksi penjualan kapal tongkang namun tidak mengembalikan deposit Rp 500 juta.',
                'status' => StatusPengajuan::MenungguVerifikasi->value,
                'tgl_pengajuan' => '2026-08-16 10:00:00',
            ],
            [
                'client_idx' => 15,
                'kategori' => 'Pidana',
                'judul' => 'Pendampingan Hukum Korban Penipuan Investasi Kripto Skema Ponzi Berkedok Robot Trading',
                'kronologi' => 'Aplikasi robot trading tiba-tiba mengunci seluruh saldo penarikan nasabah dengan total kerugian member mencapai Rp 12 Miliar.',
                'status' => StatusPengajuan::MenungguVerifikasi->value,
                'tgl_pengajuan' => '2026-08-16 11:15:00',
            ],
            [
                'client_idx' => 16,
                'kategori' => 'Keluarga',
                'judul' => 'Gugatan Nafkah Anak Pasca Perceraian Akibat Ayah Menolak Membayar Biaya Sekolah',
                'kronologi' => 'Tergugat mengingkari isi putusan pengadilan yang mewajibkan pembayaran nafkah anak sebesar Rp 15 juta per bulan.',
                'status' => StatusPengajuan::MenungguVerifikasi->value,
                'tgl_pengajuan' => '2026-08-16 13:30:00',
            ],
            [
                'client_idx' => 17,
                'kategori' => 'Ketenagakerjaan',
                'judul' => 'Tuntutan Pembayaran Uang Pesangon Akibat Merger Dua Perusahaan Teknologi',
                'kronologi' => 'Manajemen baru melakukan rasionalisasi karyawan tanpa memberikan opsi pesangon 2x PMTK sesuai kesepakatan serikat pekerja.',
                'status' => StatusPengajuan::MenungguVerifikasi->value,
                'tgl_pengajuan' => '2026-08-16 14:45:00',
            ],
            [
                'client_idx' => 18,
                'kategori' => 'Perdata',
                'judul' => 'Sengketa Batas Hak Guna Bangunan (HGB) Kawasan Pergudangan Modern Marunda',
                'kronologi' => 'Pengembang pergudangan sebelah membangun saluran pembuangan limbah melintasi batas HGB milik Klien.',
                'status' => StatusPengajuan::MenungguVerifikasi->value,
                'tgl_pengajuan' => '2026-08-16 16:00:00',
            ],
        ];

        foreach ($casesData as $idx => $cData) {
            $clientUser = $clients[$cData['client_idx']];
            $kategoriObj = $categories[$cData['kategori']];

            // 1. Create PraPendaftaranPerkara
            $pengajuan = PraPendaftaranPerkara::updateOrCreate(
                [
                    'id_user' => $clientUser->id_user,
                    'judul_perkara' => $cData['judul'],
                ],
                [
                    'id_kategori' => $kategoriObj->id_kategori,
                    'kronologi' => $cData['kronologi'],
                    'status_pengajuan' => $cData['status'],
                    'tanggal_pengajuan' => $cData['tgl_pengajuan'],
                    'created_at' => $cData['tgl_pengajuan'],
                    'updated_at' => $cData['tgl_selesai'] ?? $cData['tgl_verif'] ?? $cData['tgl_pengajuan'],
                ]
            );

            // 2. Initial Riwayat Status: Menunggu Verifikasi
            RiwayatStatus::updateOrCreate(
                [
                    'id_pendaftaran' => $pengajuan->id_pendaftaran,
                    'status' => StatusPengajuan::MenungguVerifikasi->value,
                ],
                [
                    'id_user' => $clientUser->id_user,
                    'keterangan' => 'Permohonan pra-pendaftaran perkara berhasil diajukan oleh Klien.',
                    'created_at' => $cData['tgl_pengajuan'],
                    'updated_at' => $cData['tgl_pengajuan'],
                ]
            );

            // 3. Attach Standard Documents
            $docDefinitions = [
                ['name' => 'Kartu Tanda Penduduk (KTP) Klien', 'type' => 'identitas', 'file' => 'identitas_ktp.pdf'],
                ['name' => 'Surat Kuasa Khusus Pengajuan', 'type' => 'surat_kuasa', 'file' => 'surat_kuasa_khusus.pdf'],
                ['name' => 'Dokumen Perjanjian / Kontrak Bukti', 'type' => 'bukti_perkara', 'file' => 'dokumen_perjanjian_kontrak.pdf'],
                ['name' => 'Bukti Pembayaran / Dokumen Pendukung', 'type' => 'dokumen_pendukung', 'file' => 'bukti_transfer_pembayaran.pdf'],
            ];

            $docStatus = StatusDokumen::Terkirim->value;
            if (in_array($cData['status'], [StatusPengajuan::BerkasLengkap->value, StatusPengajuan::JadwalDipilih->value, StatusPengajuan::Selesai->value])) {
                $docStatus = StatusDokumen::Valid->value;
            } elseif ($cData['status'] === StatusPengajuan::BerkasTidakLengkap->value) {
                $docStatus = StatusDokumen::PerluPerbaikan->value;
            }

            $createdDocs = [];
            foreach ($docDefinitions as $dIdx => $docDef) {
                $statusItem = ($dIdx === 3 && $cData['status'] === StatusPengajuan::BerkasTidakLengkap->value)
                    ? StatusDokumen::PerluPerbaikan->value
                    : $docStatus;

                $doc = DokumenPerkara::updateOrCreate(
                    [
                        'id_pendaftaran' => $pengajuan->id_pendaftaran,
                        'nama_dokumen' => $docDef['name'],
                    ],
                    [
                        'jenis_dokumen' => $docDef['type'],
                        'file_path' => $sampleFiles[$docDef['file']] ?? 'dokumen-perkara/identitas_ktp.pdf',
                        'status_dokumen' => $statusItem,
                        'created_at' => $cData['tgl_pengajuan'],
                        'updated_at' => $cData['tgl_pengajuan'],
                    ]
                );
                $createdDocs[] = $doc;
            }

            // 4. Verifikasi Berkas & History (If verified)
            if (isset($cData['tgl_verif'])) {
                $legalUser = $legalList[$cData['legal_idx'] ?? 0];
                $statusVerif = in_array($cData['status'], [StatusPengajuan::BerkasLengkap->value, StatusPengajuan::JadwalDipilih->value, StatusPengajuan::Selesai->value])
                    ? 'berkas_lengkap'
                    : 'berkas_tidak_lengkap';

                $catatanUmum = $cData['catatan_verif'] ?? $cData['catatan_perbaikan'] ?? 'Seluruh dokumen pendukung telah diperiksa dan dinyatakan memenuhi syarat kelengkapan berkas perkara.';

                $verifikasi = VerifikasiBerkas::updateOrCreate(
                    [
                        'id_pendaftaran' => $pengajuan->id_pendaftaran,
                        'id_user' => $legalUser->id_user,
                    ],
                    [
                        'status_verifikasi' => $statusVerif,
                        'tanggal_verifikasi' => $cData['tgl_verif'],
                        'catatan_umum' => $catatanUmum,
                        'created_at' => $cData['tgl_verif'],
                        'updated_at' => $cData['tgl_verif'],
                    ]
                );

                // Add Document-Specific Verification Note for incomplete cases
                if ($statusVerif === 'berkas_tidak_lengkap' && isset($createdDocs[3])) {
                    CatatanVerifikasi::updateOrCreate(
                        [
                            'id_verifikasi' => $verifikasi->id_verifikasi,
                            'id_dokumen' => $createdDocs[3]->id_dokumen,
                        ],
                        [
                            'isi_catatan' => 'Dokumen belum terlegalisir atau resolusi scan buram. Mohon unggah ulang dengan kualitas jelas.',
                            'status_perbaikan' => ($cData['status'] === StatusPengajuan::MenungguVerifikasiUlang->value) ? 'sudah_diperbaiki' : 'perlu_perbaikan',
                            'created_at' => $cData['tgl_verif'],
                            'updated_at' => $cData['tgl_verif'],
                        ]
                    );
                }

                // Add Riwayat Status for verification
                RiwayatStatus::updateOrCreate(
                    [
                        'id_pendaftaran' => $pengajuan->id_pendaftaran,
                        'status' => $statusVerif,
                    ],
                    [
                        'id_user' => $legalUser->id_user,
                        'keterangan' => $catatanUmum,
                        'created_at' => $cData['tgl_verif'],
                        'updated_at' => $cData['tgl_verif'],
                    ]
                );
            }

            // 5. Booking Konsultasi & Reschedule
            if (isset($cData['tgl_booking'])) {
                $jadwalObj = $jadwalSlots[$cData['jadwal_idx'] ?? 0];
                $metode = $cData['metode'] ?? 'offline';

                $booking = BookingKonsultasi::updateOrCreate(
                    [
                        'id_pendaftaran' => $pengajuan->id_pendaftaran,
                        'id_jadwal' => $jadwalObj->id_jadwal,
                    ],
                    [
                        'id_user' => $clientUser->id_user,
                        'status_booking' => $cData['booking_status'] ?? StatusBooking::Aktif->value,
                        'tanggal_booking' => $cData['tgl_booking'],
                        'metode_konsultasi' => $metode,
                        'status_konfirmasi_konsultasi' => StatusKonfirmasi::Terkonfirmasi->value,
                        'lokasi_konsultasi' => ($metode === 'offline') ? 'Ruang Konsultasi Utama Lt. 2 Kantor TNY & PARTNERS, Jakarta Selatan' : null,
                        'link_konsultasi' => ($metode === 'online') ? 'https://meet.google.com/tny-law-firm-session' : null,
                        'catatan_konsultasi' => 'Mohon hadir 15 menit sebelum jadwal sesi konsultasi dimulai dan membawa salinan fisik berkas perkara.',
                        'dikonfirmasi_pada' => Carbon::parse($cData['tgl_booking'])->addHours(1),
                        'id_admin_konfirmasi' => $admin->id_user,
                        'created_at' => $cData['tgl_booking'],
                        'updated_at' => $cData['tgl_booking'],
                    ]
                );

                RiwayatStatus::updateOrCreate(
                    [
                        'id_pendaftaran' => $pengajuan->id_pendaftaran,
                        'status' => StatusPengajuan::JadwalDipilih->value,
                    ],
                    [
                        'id_user' => $clientUser->id_user,
                        'keterangan' => 'Klien telah memilih jadwal konsultasi pada ' . $jadwalObj->tanggal?->format('d/m/Y') . ' (' . substr((string) $jadwalObj->waktu_mulai, 0, 5) . ' WIB).',
                        'created_at' => $cData['tgl_booking'],
                        'updated_at' => $cData['tgl_booking'],
                    ]
                );

                // Handle Reschedule Requests
                if (!empty($cData['reschedule'])) {
                    $tglPengajuanRs = Carbon::parse($cData['tgl_booking'])->addDays(1);
                    $rsStatus = $cData['reschedule_status'] ?? StatusReschedule::MenungguPersetujuan->value;

                    $idJadwalBaru = null;
                    $idBookingBaru = null;

                    if ($rsStatus === StatusReschedule::Disetujui->value && isset($cData['reschedule_jadwal_baru_idx'])) {
                        $jadwalBaruObj = $jadwalSlots[$cData['reschedule_jadwal_baru_idx']];
                        $idJadwalBaru = $jadwalBaruObj->id_jadwal;

                        // Create secondary booking record for approved reschedule
                        $bookingBaru = BookingKonsultasi::updateOrCreate(
                            [
                                'id_pendaftaran' => $pengajuan->id_pendaftaran,
                                'id_jadwal' => $jadwalBaruObj->id_jadwal,
                            ],
                            [
                                'id_user' => $clientUser->id_user,
                                'status_booking' => StatusBooking::Aktif->value,
                                'tanggal_booking' => $tglPengajuanRs->copy()->addHours(3),
                                'metode_konsultasi' => $metode,
                                'status_konfirmasi_konsultasi' => StatusKonfirmasi::Terkonfirmasi->value,
                                'lokasi_konsultasi' => ($metode === 'offline') ? 'Ruang Konsultasi Utama Lt. 2 Kantor TNY & PARTNERS, Jakarta Selatan' : null,
                                'link_konsultasi' => ($metode === 'online') ? 'https://meet.google.com/tny-law-firm-session' : null,
                                'catatan_konsultasi' => 'Jadwal baru hasil persetujuan permohonan reschedule Klien.',
                                'dikonfirmasi_pada' => $tglPengajuanRs->copy()->addHours(3),
                                'id_admin_konfirmasi' => $admin->id_user,
                                'created_at' => $tglPengajuanRs->copy()->addHours(3),
                                'updated_at' => $tglPengajuanRs->copy()->addHours(3),
                            ]
                        );
                        $idBookingBaru = $bookingBaru->id_booking;

                        // Mark old booking as rescheduled/cancelled
                        $booking->update(['status_booking' => StatusBooking::Dibatalkan->value]);
                    }

                    PermintaanReschedule::updateOrCreate(
                        [
                            'id_booking' => $booking->id_booking,
                            'id_user' => $clientUser->id_user,
                        ],
                        [
                            'alasan_reschedule' => $cData['reschedule_alasan'],
                            'preferensi_jadwal' => $cData['reschedule_preferensi'] ?? null,
                            'preferensi_metode' => $metode,
                            'status_reschedule' => $rsStatus,
                            'id_jadwal_baru' => $idJadwalBaru,
                            'id_booking_baru' => $idBookingBaru,
                            'catatan_admin' => $cData['reschedule_catatan_admin'] ?? ($rsStatus === StatusReschedule::Disetujui->value ? 'Permohonan perubahan jadwal disetujui oleh Admin.' : null),
                            'tanggal_pengajuan' => $tglPengajuanRs,
                            'tanggal_keputusan' => ($rsStatus !== StatusReschedule::MenungguPersetujuan->value) ? $tglPengajuanRs->copy()->addHours(4) : null,
                            'created_at' => $tglPengajuanRs,
                            'updated_at' => $tglPengajuanRs,
                        ]
                    );
                }
            }

            // 6. Case Completion (Status Selesai)
            if (isset($cData['tgl_selesai'])) {
                RiwayatStatus::updateOrCreate(
                    [
                        'id_pendaftaran' => $pengajuan->id_pendaftaran,
                        'status' => StatusPengajuan::Selesai->value,
                    ],
                    [
                        'id_user' => $admin->id_user,
                        'keterangan' => 'Sesi konsultasi hukum telah tuntas dilaksanakan. Seluruh tahapan pra-pendaftaran perkara selesai.',
                        'created_at' => $cData['tgl_selesai'],
                        'updated_at' => $cData['tgl_selesai'],
                    ]
                );
            }
        }
    }
}
