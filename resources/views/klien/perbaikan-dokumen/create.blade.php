<x-app-layout title="Unggah Ulang Dokumen" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Pengajuan', 'url' => route('klien.pra-pendaftaran.index')], ['label' => 'PP-' . str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT), 'url' => route('klien.pra-pendaftaran.show', $pengajuan)], ['label' => 'Unggah Ulang']]">

    <div class="space-y-6" x-data="{ isSubmitting: false }">
        @if ($errors->any())
            <x-alert-banner type="error" x-init="$nextTick(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }); })">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert-banner>
        @endif

        <!-- Alert Banner (Dokumen perlu perbaikan) -->
        <x-alert-banner type="warning" title="Dokumen perlu perbaikan">
            Staf Legal meminta perbaikan pada dokumen berikut. Unggah file baru sesuai catatan perbaikan yang diberikan.
        </x-alert-banner>

        <form method="POST" action="{{ route('klien.perbaikan-dokumen.store', $catatanVerifikasi) }}" enctype="multipart/form-data" @submit="isSubmitting = true">
            @csrf

            <!-- 2-Column Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                
                <!-- LEFT COLUMN: Dokumen Lama -->
                <x-card class="p-0 overflow-hidden sm:p-0 flex flex-col justify-between">
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="border-b border-[#F1F5F9] pb-4">
                            <h3 class="font-bold text-navy-dark text-lg">Dokumen Lama</h3>
                            <p class="text-xs text-gray-500 mt-1">Dokumen lama ditampilkan sebagai referensi perbaikan.</p>
                        </div>

                        <div class="border-b border-[#F1F5F9]/60 pb-4 flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Dokumen</span>
                            <span class="text-sm font-bold text-navy-dark">{{ $dokumen->nama_dokumen }}</span>
                        </div>

                        <div class="border-b border-[#F1F5F9]/60 pb-4 flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis Dokumen</span>
                            <span class="text-sm font-semibold text-gray-600">{{ $dokumen->jenis_dokumen }}</span>
                        </div>

                        <div class="border-b border-[#F1F5F9]/60 pb-4 flex justify-between items-center">
                            <span class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Dokumen</span>
                            <span class="bg-red-50 text-red-700 text-xs font-extrabold px-2.5 py-0.5 rounded-full border border-red-200 uppercase tracking-wider">Perlu Perbaikan</span>
                        </div>

                        <!-- Catatan perbaikan -->
                        <div class="bg-amber-50/50 border border-amber-200 p-4 rounded-xl space-y-1">
                            <span class="block text-xs font-bold text-amber-800 uppercase tracking-wider">Catatan Perbaikan dari Staf Legal</span>
                            <p class="text-xs text-amber-800/90 leading-relaxed whitespace-pre-line">{{ $catatanVerifikasi->isi_catatan }}</p>
                        </div>
                    </div>

                    <!-- Action: Lihat Dokumen Lama -->
                    <div class="p-6 sm:p-8 border-t border-[#F1F5F9] bg-[#F8FAFC]/50 flex items-center">
                        <x-secondary-button href="{{ route('klien.dokumen.show', $dokumen) }}" tag="a" target="_blank" class="gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Lihat Dokumen Lama
                        </x-secondary-button>
                    </div>
                </x-card>

                <!-- RIGHT COLUMN: Unggah File Baru -->
                <x-card class="p-0 overflow-hidden sm:p-0 flex flex-col justify-between">
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="border-b border-[#F1F5F9] pb-4">
                            <h3 class="font-bold text-navy-dark text-lg">Unggah File Baru</h3>
                            <p class="text-xs text-gray-500 mt-1">Unggah dokumen perbaikan dengan format yang sah.</p>
                        </div>

                        <!-- Dropzone input file -->
                        <div class="space-y-4">
                            <x-input-label for="file" :value="__('File Dokumen Pengganti')" />
                            
                            <div class="border-2 border-dashed border-[#E2E8F0] hover:border-accent-blue rounded-xl p-8 bg-[#F8FAFC]/50 text-center transition cursor-pointer relative group">
                                <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" class="absolute inset-0 opacity-0 cursor-pointer z-10" required onchange="updateFileName(this)">
                                
                                <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-accent-blue transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                
                                <p id="upload-instruction" class="mt-3 text-sm font-bold text-navy-dark group-hover:text-accent-blue transition">Pilih file untuk diunggah</p>
                                <p id="file-name-display" class="mt-2 text-xs text-accent-blue font-bold hidden"></p>
                                <p class="mt-1 text-xs text-gray-400 font-medium">Format: PDF, JPG, JPEG, PNG (Maksimal 5 MB)</p>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('file')" />
                        </div>

                        <x-alert-banner type="info">
                            Dokumen lama tidak akan ditimpa. Sistem akan menyimpan file baru sebagai dokumen pengganti yang sah secara terpisah di storage.
                        </x-alert-banner>
                    </div>

                    <div class="p-6 sm:p-8 border-t border-[#F1F5F9] bg-[#F8FAFC]/50 flex items-center justify-end gap-3">
                        <x-secondary-button href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" tag="a">
                            Batal
                        </x-secondary-button>
                        <x-primary-button ::disabled="isSubmitting">
                            <span x-show="!isSubmitting">Upload Dokumen Pengganti</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2" style="display: none;">
                                <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                <span>Mengupload...</span>
                            </span>
                        </x-primary-button>
                    </div>
                </x-card>

            </div>
        </form>
    </div>

    <script>
        function updateFileName(input) {
            const display = document.getElementById('file-name-display');
            const instruction = document.getElementById('upload-instruction');
            if (input.files && input.files.length > 0) {
                display.textContent = input.files[0].name;
                display.classList.remove('hidden');
                instruction.textContent = "Mengubah file:";
            } else {
                display.classList.add('hidden');
                instruction.textContent = "Pilih file untuk diunggah";
            }
        }
    </script>
</x-app-layout>
