<x-app-layout title="Buat Pengajuan Perkara" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Pengajuan', 'url' => route('klien.pra-pendaftaran.index')], ['label' => 'Buat']]">

    <div class="max-w-6xl mx-auto space-y-6" x-data="{ isSubmitting: false }">
        <!-- Error Alert -->
        @if ($errors->any())
            <div class="rounded-xl bg-red-50 p-4 text-sm text-red-700 shadow-sm border border-red-200" x-init="$nextTick(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }); })">
                <div class="font-bold flex items-center gap-1.5">
                    <svg class="h-4 w-4 shrink-0 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                    </svg>
                    Data pengajuan belum valid. Silakan periksa kembali:
                </div>
                <ul class="mt-2 list-disc list-inside space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form method="POST" action="{{ route('klien.pra-pendaftaran.store') }}" enctype="multipart/form-data" class="space-y-6" @submit="isSubmitting = true">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                
                <!-- Kiri: Informasi Pengajuan -->
                <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm flex flex-col justify-between space-y-6">
                    <div>
                        <div class="border-b border-[#F1F5F9] pb-4">
                            <h3 class="font-bold text-navy-dark text-lg">Informasi Pengajuan</h3>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Lengkapi informasi awal perkara yang akan didaftarkan.</p>
                        </div>

                        <div class="pt-6 space-y-6">
                            <!-- Kategori Perkara -->
                            <div class="space-y-2">
                                <label for="id_kategori" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Kategori Perkara</label>
                                <div class="relative">
                                    <select id="id_kategori" name="id_kategori" required 
                                            class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4 appearance-none">
                                        <option value="">Pilih kategori perkara</option>
                                        @foreach ($kategoriPerkara as $kategori)
                                            <option value="{{ $kategori->id_kategori }}" @selected(old('id_kategori') == $kategori->id_kategori)>
                                                {{ $kategori->nama_kategori }}
                                            </option>
                                        @endforeach
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <x-input-error class="mt-1" :messages="$errors->get('id_kategori')" />
                            </div>

                            <!-- Judul Perkara -->
                            <div class="space-y-2">
                                <label for="judul_perkara" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Judul Perkara</label>
                                <input type="text" name="judul_perkara" id="judul_perkara" value="{{ old('judul_perkara') }}" placeholder="Masukkan judul perkara" required 
                                       class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4">
                                <x-input-error class="mt-1" :messages="$errors->get('judul_perkara')" />
                            </div>

                            <!-- Kronologi Perkara -->
                            <div class="space-y-2">
                                <label for="kronologi" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Kronologi Perkara</label>
                                <textarea id="kronologi" name="kronologi" rows="6" placeholder="Tuliskan kronologi perkara secara ringkas dan jelas" required 
                                          class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm px-4 py-3 resize-none">{{ old('kronologi') }}</textarea>
                                <x-input-error class="mt-1" :messages="$errors->get('kronologi')" />
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Kanan: Dokumen Pendukung -->
                <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm flex flex-col justify-between space-y-6">
                    <div>
                        <div class="border-b border-[#F1F5F9] pb-4">
                            <h3 class="font-bold text-navy-dark text-lg">Dokumen Pendukung</h3>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Unggah dokumen awal yang diperlukan untuk verifikasi berkas.</p>
                        </div>

                        <div class="pt-6 space-y-6">
                            <!-- Nama Dokumen -->
                            <div class="space-y-2">
                                <label for="nama_dokumen" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Dokumen</label>
                                <input type="text" name="nama_dokumen" id="nama_dokumen" value="{{ old('nama_dokumen') }}" placeholder="Masukkan nama dokumen" required 
                                       class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4">
                                <x-input-error class="mt-1" :messages="$errors->get('nama_dokumen')" />
                            </div>

                            <!-- Jenis Dokumen -->
                            <div class="space-y-2">
                                <label for="jenis_dokumen" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Jenis Dokumen</label>
                                <div class="relative">
                                    <select id="jenis_dokumen" name="jenis_dokumen" required 
                                            class="block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4 appearance-none">
                                        <option value="">Pilih jenis dokumen</option>
                                        <option value="ktp" @selected(old('jenis_dokumen') == 'ktp')>KTP</option>
                                        <option value="kk" @selected(old('jenis_dokumen') == 'kk')>Kartu Keluarga</option>
                                        <option value="surat_kuasa" @selected(old('jenis_dokumen') == 'surat_kuasa')>Surat Kuasa</option>
                                        <option value="bukti_transfer" @selected(old('jenis_dokumen') == 'bukti_transfer')>Bukti Transfer</option>
                                        <option value="dokumen_lainnya" @selected(old('jenis_dokumen') == 'dokumen_lainnya')>Dokumen Lainnya</option>
                                    </select>
                                    <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-4">
                                        <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                        </svg>
                                    </div>
                                </div>
                                <x-input-error class="mt-1" :messages="$errors->get('jenis_dokumen')" />
                            </div>

                            <!-- Upload File -->
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Upload File</label>
                                
                                <div class="border-2 border-dashed border-[#E2E8F0] hover:border-accent-blue rounded-2xl p-8 bg-[#F8FAFC]/50 text-center transition cursor-pointer relative group">
                                    <input type="file" id="file_dokumen" name="file_dokumen" accept=".pdf,.jpg,.jpeg,.png" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" onchange="updateFileName(this)">
                                    
                                    <svg class="mx-auto h-10 w-10 text-gray-400 group-hover:text-accent-blue transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                    </svg>
                                    
                                    <p id="file-name-display" class="mt-3 text-sm font-bold text-navy-dark group-hover:text-accent-blue transition">Pilih file atau tarik dokumen ke sini</p>
                                    <p id="file-desc-display" class="mt-2 text-xs text-gray-400 font-medium">PDF, JPG, JPEG, PNG • Maksimal 5 MB</p>
                                </div>
                                <x-input-error class="mt-1" :messages="$errors->get('file_dokumen')" />
                            </div>
                        </div>
                    </div>
                </div>

            </div> <!-- End of 2 Columns -->

            <!-- Bottom: Review Sebelum Submit -->
            <div class="bg-white border border-[#E2E8F0] p-6 rounded-2xl shadow-sm flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <h4 class="font-bold text-navy-dark text-sm">Review Sebelum Submit</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Pastikan semua data dan dokumen sudah benar. Pengajuan yang sudah dikirim tidak dapat diedit oleh Klien.
                    </p>
                </div>
                
                <div class="flex items-center gap-3 w-full md:w-auto shrink-0 justify-end">
                    <a href="{{ route('klien.pra-pendaftaran.index') }}" 
                       class="bg-white border border-[#E2E8F0] hover:bg-gray-50 text-gray-700 font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-sm w-full md:w-auto text-center">
                        Batal
                    </a>
                    <button type="submit" 
                            :disabled="isSubmitting"
                            class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm px-6 py-2.5 rounded-xl transition shadow-md shadow-blue-900/20 flex items-center justify-center gap-2 w-full md:w-auto whitespace-nowrap disabled:opacity-50 disabled:cursor-not-allowed">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim Pengajuan
                        </span>
                        <span x-show="isSubmitting" class="flex items-center gap-2">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengirim...
                        </span>
                    </button>
                </div>
            </div>

        </form>
    </div>

    <!-- Script for File Input UI -->
    @push('scripts')
    <script>
        function updateFileName(input) {
            const fileNameDisplay = document.getElementById('file-name-display');
            const fileDescDisplay = document.getElementById('file-desc-display');
            
            if (input.files && input.files.length > 0) {
                const fileName = input.files[0].name;
                const fileSize = (input.files[0].size / (1024 * 1024)).toFixed(2); // in MB
                
                fileNameDisplay.textContent = fileName;
                fileNameDisplay.classList.add('text-blue-600');
                
                fileDescDisplay.textContent = `Ukuran: ${fileSize} MB`;
            } else {
                fileNameDisplay.textContent = 'Pilih file atau tarik dokumen ke sini';
                fileNameDisplay.classList.remove('text-blue-600');
                fileDescDisplay.textContent = 'PDF, JPG, JPEG, PNG • Maksimal 5 MB';
            }
        }
    </script>
    @endpush
</x-app-layout>
