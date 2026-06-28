<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Laporan') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-medium text-gray-900">{{ __('Pilih Jenis Laporan') }}</h3>
                    <p class="mt-1 text-sm text-gray-600">
                        {{ __('Laporan dibuat dari data sistem yang sudah ada dan dapat dicetak menggunakan browser print.') }}
                    </p>

                    <div class="mt-6 grid grid-cols-1 gap-4 md:grid-cols-2 xl:grid-cols-3">
                        <a href="{{ route('admin.laporan.pra-pendaftaran') }}" class="block rounded-lg border border-gray-200 p-5 hover:border-indigo-300 hover:bg-indigo-50">
                            <div class="text-base font-semibold text-gray-900">{{ __('Laporan Pra-Pendaftaran Perkara') }}</div>
                            <div class="mt-2 text-sm text-gray-600">{{ __('Data pengajuan perkara berdasarkan tanggal, status, dan kategori.') }}</div>
                        </a>

                        <a href="{{ route('admin.laporan.verifikasi-berkas') }}" class="block rounded-lg border border-gray-200 p-5 hover:border-indigo-300 hover:bg-indigo-50">
                            <div class="text-base font-semibold text-gray-900">{{ __('Laporan Verifikasi Berkas') }}</div>
                            <div class="mt-2 text-sm text-gray-600">{{ __('Data hasil verifikasi Staf Legal dan catatan umum.') }}</div>
                        </a>

                        <a href="{{ route('admin.laporan.booking-konsultasi') }}" class="block rounded-lg border border-gray-200 p-5 hover:border-indigo-300 hover:bg-indigo-50">
                            <div class="text-base font-semibold text-gray-900">{{ __('Laporan Booking Konsultasi') }}</div>
                            <div class="mt-2 text-sm text-gray-600">{{ __('Data booking, metode konsultasi, dan status konfirmasi.') }}</div>
                        </a>

                        <a href="{{ route('admin.laporan.reschedule-konsultasi') }}" class="block rounded-lg border border-gray-200 p-5 hover:border-indigo-300 hover:bg-indigo-50">
                            <div class="text-base font-semibold text-gray-900">{{ __('Laporan Reschedule Konsultasi') }}</div>
                            <div class="mt-2 text-sm text-gray-600">{{ __('Data permintaan reschedule konsultasi dan keputusan Admin.') }}</div>
                        </a>

                        <a href="{{ route('admin.laporan.pengajuan-selesai') }}" class="block rounded-lg border border-gray-200 p-5 hover:border-indigo-300 hover:bg-indigo-50">
                            <div class="text-base font-semibold text-gray-900">{{ __('Laporan Pengajuan Selesai') }}</div>
                            <div class="mt-2 text-sm text-gray-600">{{ __('Data pengajuan yang sudah selesai dengan tanggal selesai dari riwayat status.') }}</div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
