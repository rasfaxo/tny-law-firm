<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-1 text-xxs font-semibold text-gray-400 uppercase tracking-wider mb-1">
            <span>Klien</span>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('klien.pra-pendaftaran.index') }}" class="hover:underline">Pengajuan</a>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="hover:underline text-gray-600 font-mono">PP-{{ str_pad($pengajuan->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}</a>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>Unggah Ulang</span>
        </div>
        <h2 class="font-extrabold text-2xl text-navy-dark leading-tight">
            {{ __('Unggah Ulang Dokumen') }}
        </h2>
    </x-slot>

    <div class="space-y-6">
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 border border-red-200 p-4 flex gap-3 text-sm text-red-700 shadow-sm">
                <svg class="h-5 w-5 text-red-500 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <!-- Alert Banner (Dokumen perlu perbaikan) -->
        <div class="bg-[#FFFBEB] border-l-4 border-[#F59E0B] p-4 rounded-r-xl border border-y-[#F59E0B]/20 border-r-[#F59E0B]/20 shadow-sm">
            <div class="flex gap-2 items-center">
                <svg class="h-5 w-5 text-[#D97706] shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
                <span class="font-bold text-[#92400E] text-sm">Dokumen perlu perbaikan</span>
            </div>
            <p class="text-xs text-[#92400E]/80 mt-2 pl-7 leading-relaxed">
                Staf Legal meminta perbaikan pada dokumen berikut. Unggah file baru sesuai catatan perbaikan yang diberikan.
            </p>
        </div>

        <form method="POST" action="{{ route('klien.perbaikan-dokumen.store', $catatanVerifikasi) }}" enctype="multipart/form-data">
            @csrf

            <!-- 2-Column Grid Layout -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                
                <!-- LEFT COLUMN: Dokumen Lama -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between">
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
                            <span class="bg-red-50 text-red-700 text-xxs font-bold px-2.5 py-0.5 rounded-full border border-red-200 uppercase tracking-wider">Perlu Perbaikan</span>
                        </div>

                        <!-- Catatan perbaikan -->
                        <div class="bg-amber-50/50 border border-amber-200 p-4 rounded-xl space-y-1">
                            <span class="block text-xxs font-bold text-amber-800 uppercase tracking-wider">Catatan Perbaikan dari Staf Legal</span>
                            <p class="text-xs text-amber-800/90 leading-relaxed whitespace-pre-line">{{ $catatanVerifikasi->isi_catatan }}</p>
                        </div>
                    </div>

                    <!-- Action: Lihat Dokumen Lama -->
                    <div class="p-6 sm:p-8 border-t border-[#F1F5F9] bg-[#F8FAFC]/50 flex items-center">
                        <a href="{{ route('klien.dokumen.show', $dokumen) }}" target="_blank" class="bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-sm inline-flex items-center gap-2">
                            <svg class="h-4 w-4 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                            Lihat Dokumen Lama
                        </a>
                    </div>
                </div>

                <!-- RIGHT COLUMN: Unggah File Baru -->
                <div class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden flex flex-col justify-between">
                    <div class="p-6 sm:p-8 space-y-6">
                        <div class="border-b border-[#F1F5F9] pb-4">
                            <h3 class="font-bold text-navy-dark text-lg">Unggah File Baru</h3>
                            <p class="text-xs text-gray-500 mt-1">Unggah dokumen perbaikan dengan format yang sah.</p>
                        </div>

                        <!-- Dropzone input file -->
                        <div class="space-y-4">
                            <label for="file" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">File Dokumen Pengganti</label>
                            
                            <div class="border-2 border-dashed border-[#E2E8F0] hover:border-accent-blue rounded-2xl p-8 bg-[#F8FAFC]/50 text-center transition cursor-pointer relative group">
                                <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" class="absolute inset-0 opacity-0 cursor-pointer z-10" required onchange="updateFileName(this)">
                                
                                <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-accent-blue transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                                </svg>
                                
                                <p id="upload-instruction" class="mt-3 text-sm font-bold text-navy-dark group-hover:text-accent-blue transition">Pilih file untuk diunggah</p>
                                <p id="file-name-display" class="mt-2 text-xs text-accent-blue font-bold hidden"></p>
                                <p class="mt-1 text-xxs text-gray-400 font-medium">Format: PDF, JPG, JPEG, PNG (Maksimal 5 MB)</p>
                            </div>
                            <x-input-error class="mt-2" :messages="$errors->get('file')" />
                        </div>

                        <div class="bg-blue-50/50 border border-blue-200 p-4 rounded-xl flex gap-3 text-xs text-blue-700 leading-relaxed">
                            <svg class="h-4.5 w-4.5 text-accent-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <span>Dokumen lama tidak akan ditimpa. Sistem akan menyimpan file baru sebagai dokumen pengganti yang sah secara terpisah di storage.</span>
                        </div>
                    </div>

                    <div class="p-6 sm:p-8 border-t border-[#F1F5F9] bg-[#F8FAFC]/50 flex items-center justify-end gap-3">
                        <a href="{{ route('klien.pra-pendaftaran.show', $pengajuan) }}" class="bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-sm inline-flex items-center">
                            Batal
                        </a>
                        <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm px-8 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 inline-flex items-center">
                            Upload Dokumen Pengganti
                        </button>
                    </div>
                </div>

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
