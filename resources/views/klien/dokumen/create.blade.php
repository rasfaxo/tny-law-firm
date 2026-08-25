<x-app-layout title="Upload Dokumen Perkara" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Pengajuan', 'url' => route('klien.pra-pendaftaran.index')], ['label' => 'PP-' . str_pad($praPendaftaranPerkara->id_pendaftaran, 3, '0', STR_PAD_LEFT), 'url' => route('klien.pra-pendaftaran.show', $praPendaftaranPerkara)], ['label' => 'Upload Dokumen']]">

    <div class="max-w-2xl mx-auto" x-data="{ isSubmitting: false }">
        <form method="POST" action="{{ route('klien.dokumen.store', $praPendaftaranPerkara) }}" enctype="multipart/form-data" class="bg-white border border-[#E2E8F0] rounded-2xl shadow-sm overflow-hidden space-y-6" @submit="isSubmitting = true">
            @csrf

            <!-- Header Info Panel -->
            <div class="p-6 sm:p-8 border-b border-[#F1F5F9] bg-[#F8FAFC]/50 space-y-2">
                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Perkara Pengajuan</span>
                <h3 class="font-bold text-navy-dark text-lg">{{ $praPendaftaranPerkara->judul_perkara }}</h3>
                <div class="flex items-center gap-2 pt-1">
                    <x-status-badge :status="$praPendaftaranPerkara->status_pengajuan" />
                </div>
            </div>

            <div class="p-6 sm:p-8 pt-0 space-y-6">
                @if ($errors->any())
                    <x-alert-banner type="error" x-init="$nextTick(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }); })">
                        <ul class="list-disc list-inside space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert-banner>
                @endif

                <!-- Nama Dokumen -->
                <div>
                    <x-input-label for="nama_dokumen" :value="__('Nama Dokumen')" class="!text-xs !font-bold !text-gray-600 !uppercase !tracking-wider" />
                    <x-text-input id="nama_dokumen" name="nama_dokumen" type="text" class="mt-2 block w-full" :value="old('nama_dokumen')" required placeholder="Contoh: KTP Pemohon, Bukti Kwitansi, dll." />
                    <x-input-error class="mt-2" :messages="$errors->get('nama_dokumen')" />
                </div>

                <!-- Jenis Dokumen -->
                <div>
                    <x-input-label for="jenis_dokumen" :value="__('Jenis Dokumen')" class="!text-xs !font-bold !text-gray-600 !uppercase !tracking-wider" />
                    <x-text-input id="jenis_dokumen" name="jenis_dokumen" type="text" class="mt-2 block w-full" :value="old('jenis_dokumen')" required placeholder="Contoh: Identitas, Bukti Perkara, Surat Kuasa" />
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
                        <p class="mt-1 text-xs text-gray-400 font-medium">Format: PDF, JPG, JPEG, PNG (Maksimal 5 MB)</p>
                    </div>
                    <x-input-error class="mt-2" :messages="$errors->get('file')" />
                </div>

                <!-- Info Alert -->
                <x-alert-banner type="info">
                    File disimpan dengan nama unik oleh sistem demi alasan keamanan data. Nama file asli dari perangkat Anda tidak akan dipublikasikan.
                </x-alert-banner>
            </div>

            <!-- Footer Actions -->
            <div class="p-6 sm:p-8 border-t border-[#F1F5F9] bg-[#F8FAFC]/50 flex items-center justify-end gap-3">
                <x-secondary-button href="{{ route('klien.pra-pendaftaran.show', $praPendaftaranPerkara) }}" tag="a">
                    Batal
                </x-secondary-button>
                <x-primary-button ::disabled="isSubmitting">
                    <span x-show="!isSubmitting">Upload Dokumen</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2" style="display: none;">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Mengupload...</span>
                    </span>
                </x-primary-button>
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
