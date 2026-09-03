<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Cetak Laporan' }} - {{ config('firm.name', 'TNY & PARTNERS') }}</title>

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        @page {
            size: A4 portrait;
            margin: 12mm 15mm;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #0f172a !important;
                font-size: 10.5pt;
                line-height: 1.35;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            .print-container {
                width: 100% !important;
                max-width: none !important;
                padding: 0 !important;
                margin: 0 !important;
                box-shadow: none !important;
                border: none !important;
            }
            table {
                page-break-inside: auto;
            }
            tr {
                page-break-inside: avoid;
                page-break-after: auto;
            }
            thead {
                display: table-header-group;
            }
            tfoot {
                display: table-footer-group;
            }
        }
    </style>
</head>
<body class="bg-slate-100 font-sans text-slate-800 antialiased min-h-screen">
    <!-- Floating Action Toolbar (Screen Only) -->
    <header class="no-print sticky top-0 z-50 bg-[#0F1E3A] text-white border-b border-slate-700 shadow-md">
        <div class="max-w-5xl mx-auto px-6 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <span class="font-extrabold text-base tracking-wider text-white uppercase">{{ config('firm.name', 'TNY & PARTNERS') }}</span>
                <span class="text-xs text-slate-300 hidden sm:inline">• Pratinjau Dokumen Cetak</span>
            </div>
            <div class="flex items-center gap-3">
                <button type="button" onclick="window.close()" class="px-4 py-2 text-xs font-semibold rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 transition cursor-pointer">
                    ← Tutup Tab
                </button>
                <button type="button" onclick="window.print()" class="px-5 py-2 text-xs font-bold rounded-lg bg-blue-600 hover:bg-blue-500 text-white flex items-center gap-2 shadow-sm transition cursor-pointer">
                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                    </svg>
                    <span>Cetak Sekarang</span>
                </button>
            </div>
        </div>
    </header>

    <!-- Printable Paper Sheet Container -->
    <main class="max-w-5xl mx-auto my-6 sm:p-6 no-print:px-4">
        <div class="print-container bg-white border border-slate-200 shadow-lg rounded-xl p-8 sm:p-12">
            <!-- Kop Surat Resmi -->
            <x-kop-surat />

            <!-- Document Content -->
            {{ $slot }}

            <!-- Signature Block -->
            <div class="mt-12 flex justify-end">
                <div class="text-center text-xs space-y-1 w-64">
                    <p class="text-slate-600">
                        {{ config('firm.city', 'Jakarta Selatan') }}, {{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}
                    </p>
                    <p class="font-bold text-slate-800">Mengetahui,</p>
                    <p class="text-slate-700 font-semibold">Bagian Administrasi Perkara</p>
                    <div class="h-20 flex items-center justify-center">
                        <span class="text-slate-300 italic text-[10px]">[ Tanda Tangan & Cap Resmi ]</span>
                    </div>
                    <p class="font-bold text-slate-900 underline">
                        {{ auth()->user()->nama ?? 'Administrator' }}
                    </p>
                    <p class="text-[10px] text-slate-500 font-mono">
                        {{ config('firm.name', 'TNY & PARTNERS') }} • ID: ADM-{{ str_pad(auth()->id() ?? 1, 4, '0', STR_PAD_LEFT) }}
                    </p>
                </div>
            </div>
        </div>
    </main>

    <script>
        // Trigger print dialog on load
        window.addEventListener('DOMContentLoaded', () => {
            setTimeout(() => {
                window.print();
            }, 500);
        });
    </script>
</body>
</html>
