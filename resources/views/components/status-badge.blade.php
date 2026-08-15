@props([
    'status',
    'color' => null,
])

@php
    // Mapping slug database ke label UI bahasa Indonesia yang valid sesuai STATUS_RULES.md
    $labels = [
        // Status Pengajuan
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'berkas_tidak_lengkap' => 'Berkas Tidak Lengkap',
        'menunggu_verifikasi_ulang' => 'Menunggu Verifikasi Ulang',
        'berkas_lengkap' => 'Berkas Lengkap',
        'jadwal_dipilih' => 'Jadwal Dipilih',
        'selesai' => 'Selesai',

        // Status Dokumen
        'terkirim' => 'Terkirim',
        'valid' => 'Valid',
        'perlu_perbaikan' => 'Perlu Perbaikan',
        'diganti' => 'Diganti',

        // Metode & Konfirmasi Konsultasi
        'online' => 'Online',
        'offline' => 'Offline',
        'menunggu_konfirmasi' => 'Menunggu Konfirmasi',
        'terkonfirmasi' => 'Terkonfirmasi',

        // Status Reschedule
        'menunggu_persetujuan' => 'Menunggu Persetujuan',
        'disetujui' => 'Disetujui',
        'ditolak' => 'Ditolak',

        // Status Booking
        'aktif' => 'Aktif',
        'dibatalkan' => 'Dibatalkan',

        // Slot Jadwal
        'tersedia' => 'Tersedia',
        'terisi' => 'Terisi',
        'tidak_aktif' => 'Tidak Aktif',
    ];

    $slug = strtolower((string) $status);
    $label = $labels[$slug] ?? str_replace('_', ' ', ucfirst($slug));

    // Skema warna default jika tidak dispesifikasikan
    if (!$color) {
        $color = match ($slug) {
            'berkas_lengkap', 'selesai', 'valid', 'terkonfirmasi', 'disetujui', 'aktif', 'tersedia' => 'green',
            'menunggu_verifikasi', 'menunggu_verifikasi_ulang', 'menunggu_konfirmasi', 'menunggu_persetujuan', 'terkirim' => 'yellow',
            'berkas_tidak_lengkap', 'perlu_perbaikan', 'ditolak' => 'red',
            'jadwal_dipilih', 'online', 'terisi' => 'blue',
            default => 'gray',
        };
    }

    $colors = [
        'blue' => 'bg-blue-50 text-blue-700 border border-blue-200',
        'green' => 'bg-green-50 text-green-700 border border-green-200',
        'orange' => 'bg-orange-50 text-orange-700 border border-orange-200',
        'yellow' => 'bg-yellow-50 text-yellow-800 border border-yellow-200',
        'red' => 'bg-red-50 text-red-700 border border-red-200',
        'gray' => 'bg-gray-100 text-gray-700 border border-gray-200',
    ];

    $classes = $colors[$color] ?? $colors['blue'];
@endphp

<span {{ $attributes->merge(['class' => "inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-medium tracking-wide shadow-sm {$classes}"]) }}>
    {{ $label }}
</span>
