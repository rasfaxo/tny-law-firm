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
            <a href="{{ route('klien.pra-pendaftaran.show', $praPendaftaranPerkara) }}" class="hover:underline text-gray-600 font-mono">PP-{{ str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT) }}</a>
            <svg class="h-3 w-3 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
            </svg>
            <span>Upload Dokumen</span>
        </div>
        <h2 class="font-extrabold text-2xl text-navy-dark leading-tight">
            {{ __('Upload Dokumen Perkara') }}
        </h2>
    </x-slot>

    <div class="max-w-2xl mx-auto">
        <form method="POST" action="{{ route('klien.dokumen.store', $praPendaftaranPerkara) }}" enctype="multipart/form-data" class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden space-y-6">
            @csrf

            <!-- Header Info Panel -->
            <div class="p-6 sm:p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]/50 space-y-2">
                <span class="block text-xxs font-bold text-gray-400 uppercase tracking-wider">Perkara Pengajuan</span>
                <h3 class="font-bold text-navy-dark text-lg">{{ $praPendaftaranPerkara->judul_perkara }}</h3>
                <div class="flex items-center gap-2 pt-1">
                    <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
                </div>
            </div>

            <div class="p-6 sm:p-8 pt-0 space-y-6">
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

                <!-- Nama Dokumen -->
                <div class="space-y-2">
                    <label for="nama_dokumen" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Dokumen</label>
                    <input type="text" id="nama_dokumen" name="nama_dokumen" required value="{{ old('nama_dokumen') }}" placeholder="Contoh: KTP Pemohon, Bukti Kwitansi, dll."
                           class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4">
                    <x-input-error class="mt-2" :messages="$errors->get('nama_dokumen')" />
                </div>

                <!-- Jenis Dokumen -->
                <div class="space-y-2">
                    <label for="jenis_dokumen" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis Dokumen</label>
                    <input type="text" id="jenis_dokumen" name="jenis_dokumen" required value="{{ old('jenis_dokumen') }}" placeholder="Contoh: Identitas, Bukti Perkara, Surat Kuasa"
                           class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4">
                    <x-input-error class="mt-2" :messages="$errors->get('jenis_dokumen')" />
                </div>

                <!-- File Dokumen -->
                <div class="space-y-2">
                    <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Upload File Dokumen</label>
                    
                    <div class="border-2 border-dashed border-[#E2E8F0] hover:border-accent-blue rounded-2xl p-8 bg-[#F8FAFC]/50 text-center transition cursor-pointer relative group">
                        <input id="file" name="file" type="file" accept=".pdf,.jpg,.jpeg,.png,application/pdf,image/jpeg,image/png" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" required onchange="updateFileName(this)">
                        
                        <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-accent-blue transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path>
                        </svg>
                        
                        <p id="upload-instruction" class="mt-3 text-sm font-bold text-navy-dark group-hover:text-accent-blue transition">Pilih file atau tarik dokumen ke sini</p>
                        <p id="file-name-display" class="mt-2 text-xs text-accent-blue font-bold hidden"></p>
                        <p class="mt-1 text-xxs text-gray-400 font-medium">Format: PDF, JPG, JPEG, PNG (Maksimal 5 MB)</p>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                </div>

                <!-- Info Alert -->
                <div class="bg-blue-50/50 border border-blue-200 p-4 rounded-xl flex gap-3 text-xs text-blue-700 leading-relaxed">
                    <svg class="h-4.5 w-4.5 text-accent-blue shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                    <span>File disimpan dengan nama unik oleh sistem demi alasan keamanan data. Nama file asli dari perangkat Anda tidak akan dipublikasikan.</span>
                </div>
            </div>

            <!-- Footer Actions -->
            <div class="p-6 sm:p-8 border-t border-[#F1F5F9] bg-[#F8FAFC]/50 flex items-center justify-end gap-3">
                <a href="{{ route('klien.pra-pendaftaran.show', $praPendaftaranPerkara) }}" class="bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-sm">
                    Batal
                </a>
                <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm px-8 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20">
                    Upload Dokumen
                </button>
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
                instruction.textContent = "Pilih file atau tarik dokumen ke sini";
            }
        }
    </script>
</x-app-layout>
