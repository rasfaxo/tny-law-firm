<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Detail Pra-Pendaftaran Perkara') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="rounded-md bg-red-50 p-4 text-sm text-red-700">
                    {{ session('error') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Judul Perkara</div>
                        <div class="mt-1">{{ $praPendaftaranPerkara->judul_perkara }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Kategori Perkara</div>
                        <div class="mt-1">{{ $praPendaftaranPerkara->kategori?->nama_kategori ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Status Pengajuan</div>
                        <div class="mt-1">
                            <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" color="yellow" />
                        </div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Tanggal Pengajuan</div>
                        <div class="mt-1">{{ $praPendaftaranPerkara->tanggal_pengajuan?->format('d M Y H:i') ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Kronologi</div>
                        <div class="mt-1 whitespace-pre-line">{{ $praPendaftaranPerkara->kronologi }}</div>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('klien.pra-pendaftaran.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Kembali ke daftar') }}
                        </a>
                    </div>
                </div>
            </div>

            @php
                $bookingAktif = $praPendaftaranPerkara->bookingAktif;
                $semuaPermintaanReschedule = $praPendaftaranPerkara->bookingKonsultasi
                    ->flatMap(fn ($booking) => $booking->permintaanReschedule);
                $permintaanRescheduleTerakhir = $semuaPermintaanReschedule->sortByDesc('tanggal_pengajuan')->first();
                $permintaanRescheduleMenunggu = $bookingAktif
                    ? $semuaPermintaanReschedule
                        ->where('id_booking', $bookingAktif->id_booking)
                        ->firstWhere('status_reschedule', 'menunggu_persetujuan')
                    : null;
                $bisaAjukanReschedule = $bookingAktif
                    && $bookingAktif->status_booking === 'aktif'
                    && $praPendaftaranPerkara->status_pengajuan === 'jadwal_dipilih'
                    && !$permintaanRescheduleMenunggu;
            @endphp

            @if ($bookingAktif || $praPendaftaranPerkara->status_pengajuan === 'berkas_lengkap')
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <div class="flex items-center justify-between gap-4">
                            <h3 class="text-lg font-medium text-gray-900">{{ __('Informasi Konsultasi') }}</h3>

                            @if (!$bookingAktif && $praPendaftaranPerkara->status_pengajuan === 'berkas_lengkap')
                                <a href="{{ route('klien.booking-konsultasi.create', $praPendaftaranPerkara) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Pilih Jadwal Konsultasi') }}
                                </a>
                            @elseif ($bisaAjukanReschedule)
                                <a href="{{ route('klien.permintaan-reschedule.create', $bookingAktif) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                    {{ __('Ajukan Reschedule') }}
                                </a>
                            @endif
                        </div>

                        @if ($bookingAktif)
                            @php
                                $jadwalBooking = $bookingAktif->jadwalKonsultasi;
                                $metodeBooking = $bookingAktif->metode_konsultasi ?? 'offline';
                                $statusKonfirmasi = $bookingAktif->status_konfirmasi_konsultasi ?? 'menunggu_konfirmasi';
                                $metodeColor = $metodeBooking === 'online' ? 'blue' : 'gray';
                                $konfirmasiColor = $statusKonfirmasi === 'terkonfirmasi' ? 'green' : 'yellow';
                            @endphp

                            @if ($statusKonfirmasi === 'menunggu_konfirmasi')
                                <div class="mt-4 rounded-md bg-yellow-50 p-4 text-sm text-yellow-700">
                                    {{ __('Informasi teknis konsultasi sedang menunggu konfirmasi Admin. Admin akan melengkapi link/lokasi konsultasi sebelum jadwal berlangsung.') }}
                                </div>
                            @endif

                            @if ($permintaanRescheduleTerakhir)
                                @php
                                    $statusRescheduleColor = match ($permintaanRescheduleTerakhir->status_reschedule) {
                                        'disetujui' => 'green',
                                        'ditolak' => 'red',
                                        default => 'yellow',
                                    };
                                @endphp
                                <div class="mt-4 rounded-md border border-gray-200 p-4 text-sm text-gray-700">
                                    <div class="flex flex-wrap items-center justify-between gap-3">
                                        <div>
                                            <div class="font-medium text-gray-900">{{ __('Status Permintaan Reschedule Terakhir') }}</div>
                                            <div class="mt-1">
                                                <x-status-badge :status="$permintaanRescheduleTerakhir->status_reschedule" :color="$statusRescheduleColor" />
                                            </div>
                                        </div>
                                        <a href="{{ route('klien.permintaan-reschedule.show', $permintaanRescheduleTerakhir) }}" class="text-indigo-600 hover:text-indigo-900">
                                            {{ __('Lihat Detail') }}
                                        </a>
                                    </div>

                                    @if ($permintaanRescheduleTerakhir->status_reschedule === 'menunggu_persetujuan')
                                        <p class="mt-3 text-yellow-700">
                                            {{ __('Permintaan reschedule sedang menunggu persetujuan Admin. Jadwal lama tetap berlaku sampai Admin menyetujui perubahan.') }}
                                        </p>
                                    @elseif ($permintaanRescheduleTerakhir->status_reschedule === 'ditolak')
                                        <p class="mt-3 text-red-700">
                                            {{ __('Permintaan reschedule ditolak. Jadwal lama tetap berlaku.') }}
                                        </p>
                                    @elseif ($permintaanRescheduleTerakhir->status_reschedule === 'disetujui')
                                        <p class="mt-3 text-green-700">
                                            {{ __('Permintaan reschedule disetujui. Informasi booking aktif di bawah ini mengikuti jadwal terbaru.') }}
                                        </p>
                                    @endif
                                </div>
                            @endif

                            <dl class="mt-4 grid grid-cols-1 gap-4 sm:grid-cols-2">
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status Booking</dt>
                                    <dd class="mt-1">
                                        <x-status-badge :status="$bookingAktif->status_booking" color="green" />
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Metode Konsultasi</dt>
                                    <dd class="mt-1">
                                        <x-status-badge :status="$metodeBooking" :color="$metodeColor" />
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Status Konfirmasi Konsultasi</dt>
                                    <dd class="mt-1">
                                        <x-status-badge :status="$statusKonfirmasi" :color="$konfirmasiColor" />
                                    </dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Booking</dt>
                                    <dd class="mt-1 text-gray-900">{{ $bookingAktif->tanggal_booking?->format('d M Y H:i') ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Tanggal Konsultasi</dt>
                                    <dd class="mt-1 text-gray-900">{{ $jadwalBooking?->tanggal?->format('d M Y') ?? '-' }}</dd>
                                </div>
                                <div>
                                    <dt class="text-sm font-medium text-gray-500">Waktu Konsultasi</dt>
                                    <dd class="mt-1 text-gray-900">
                                        {{ $jadwalBooking ? substr((string) $jadwalBooking->waktu_mulai, 0, 5) : '-' }}
                                        @if ($jadwalBooking)
                                            - {{ substr((string) $jadwalBooking->waktu_selesai, 0, 5) }}
                                        @endif
                                    </dd>
                                </div>
                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Catatan Preferensi Klien</dt>
                                    <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $bookingAktif->catatan_preferensi_klien ?: '-' }}</dd>
                                </div>

                                @if ($metodeBooking === 'online')
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Link Konsultasi</dt>
                                        <dd class="mt-1 text-gray-900">
                                            @if ($bookingAktif->link_konsultasi)
                                                <a href="{{ $bookingAktif->link_konsultasi }}" class="text-indigo-600 hover:text-indigo-900" target="_blank" rel="noopener noreferrer">
                                                    {{ $bookingAktif->link_konsultasi }}
                                                </a>
                                            @else
                                                {{ __('Link konsultasi belum tersedia.') }}
                                            @endif
                                        </dd>
                                    </div>
                                @else
                                    <div class="sm:col-span-2">
                                        <dt class="text-sm font-medium text-gray-500">Lokasi Konsultasi</dt>
                                        <dd class="mt-1 whitespace-pre-line text-gray-900">
                                            {{ $bookingAktif->lokasi_konsultasi ?: __('Lokasi konsultasi belum tersedia.') }}
                                        </dd>
                                    </div>
                                @endif

                                <div class="sm:col-span-2">
                                    <dt class="text-sm font-medium text-gray-500">Catatan Konsultasi dari Admin</dt>
                                    <dd class="mt-1 whitespace-pre-line text-gray-900">{{ $bookingAktif->catatan_konsultasi ?: '-' }}</dd>
                                </div>
                            </dl>
                        @else
                            <p class="mt-4 text-sm text-gray-500">
                                {{ __('Berkas sudah lengkap. Silakan pilih jadwal konsultasi yang tersedia.') }}
                            </p>
                        @endif
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between gap-4">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Dokumen Aktif') }}</h3>

                        @if ($praPendaftaranPerkara->status_pengajuan === 'menunggu_verifikasi')
                            <a href="{{ route('klien.dokumen.create', $praPendaftaranPerkara) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                {{ __('Upload Dokumen') }}
                            </a>
                        @endif
                    </div>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Upload</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $dokumen->nama_dokumen }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $dokumen->jenis_dokumen }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$dokumen->status_dokumen" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $dokumen->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('klien.dokumen.show', $dokumen) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Lihat/Unduh') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada dokumen aktif.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Riwayat Dokumen') }}</h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Dokumen lama yang sudah diganti tetap disimpan sebagai histori dan bersifat read-only.') }}
                    </p>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama Dokumen</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Jenis</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Upload</th>
                                    <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($praPendaftaranPerkara->riwayatDokumen as $dokumen)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-gray-900">{{ $dokumen->nama_dokumen }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $dokumen->jenis_dokumen }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                            <x-status-badge :status="$dokumen->status_dokumen" color="gray" />
                                        </td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $dokumen->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                            <a href="{{ route('klien.dokumen.show', $dokumen) }}" class="text-indigo-600 hover:text-indigo-900">
                                                {{ __('Lihat/Unduh') }}
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada riwayat dokumen.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            @if ($praPendaftaranPerkara->status_pengajuan === 'berkas_tidak_lengkap')
                @php
                    $catatanPerbaikan = $praPendaftaranPerkara->verifikasiBerkas
                        ->flatMap(fn ($verifikasi) => $verifikasi->catatanVerifikasi);
                @endphp

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">
                        <h3 class="text-lg font-medium text-gray-900">{{ __('Catatan Perbaikan Dokumen') }}</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Unggah dokumen pengganti hanya untuk catatan yang masih belum diperbaiki.') }}
                        </p>

                        <div class="mt-4 overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Dokumen</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Catatan</th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status Perbaikan</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @forelse ($catatanPerbaikan as $catatan)
                                        @php
                                            $dokumenCatatan = $catatan->dokumenPerkara;
                                            $bisaUploadPerbaikan = $catatan->status_perbaikan === 'belum_diperbaiki'
                                                && $dokumenCatatan
                                                && $dokumenCatatan->status_dokumen === 'perlu_perbaikan';
                                        @endphp
                                        <tr>
                                            <td class="px-4 py-3 text-gray-700">
                                                <div class="font-medium text-gray-900">{{ $dokumenCatatan?->nama_dokumen ?? '-' }}</div>
                                                <div class="mt-1 text-sm text-gray-500">
                                                    {{ $dokumenCatatan?->jenis_dokumen ?? '-' }}
                                                    &middot;
                                                    @if ($dokumenCatatan)
                                                        <x-status-badge :status="$dokumenCatatan->status_dokumen" color="gray" />
                                                    @else
                                                        -
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="px-4 py-3 text-gray-700 whitespace-pre-line">{{ $catatan->isi_catatan }}</td>
                                            <td class="px-4 py-3 whitespace-nowrap text-gray-700">
                                                <x-status-badge :status="$catatan->status_perbaikan" color="orange" />
                                            </td>
                                            <td class="px-4 py-3 whitespace-nowrap text-right text-sm font-medium">
                                                @if ($bisaUploadPerbaikan)
                                                    <a href="{{ route('klien.perbaikan-dokumen.create', $catatan) }}" class="text-indigo-600 hover:text-indigo-900">
                                                        {{ __('Upload Pengganti') }}
                                                    </a>
                                                @else
                                                    <span class="text-gray-400">{{ __('Tidak tersedia') }}</span>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                                {{ __('Belum ada catatan perbaikan dokumen.') }}
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Riwayat Status') }}</h3>

                    <div class="mt-4 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Keterangan</th>
                                    <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($praPendaftaranPerkara->riwayatStatus as $riwayat)
                                    <tr>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $riwayat->created_at?->format('d M Y H:i') ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ str_replace('_', ' ', ucfirst($riwayat->status)) }}</td>
                                        <td class="px-4 py-3 text-gray-700">{{ $riwayat->keterangan ?? '-' }}</td>
                                        <td class="px-4 py-3 whitespace-nowrap text-gray-700">{{ $riwayat->user?->nama ?? '-' }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                                            {{ __('Belum ada riwayat status.') }}
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
