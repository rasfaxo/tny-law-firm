<x-app-layout title="Buat Pengajuan Perkara" :breadcrumbs="[['label' => 'Klien'], ['label' => 'Pengajuan', 'url' => route('klien.pra-pendaftaran.index')], ['label' => 'Buat']]">

    <div class="max-w-6xl mx-auto space-y-6" x-data="{ 
        isSubmitting: false,
        dokumenList: [{id: Date.now(), fileName: '', fileSize: ''}],
        addDokumen() {
            if (this.dokumenList.length < 5) {
                this.dokumenList.push({id: Date.now(), fileName: '', fileSize: ''});
            }
        },
        removeDokumen(index) {
            if (this.dokumenList.length > 1) {
                this.dokumenList.splice(index, 1);
            }
        },
        updateFileName(input, index) {
            if (input.files && input.files.length > 0) {
                this.dokumenList[index].fileName = input.files[0].name;
                this.dokumenList[index].fileSize = (input.files[0].size / (1024 * 1024)).toFixed(2) + ' MB';
            } else {
                this.dokumenList[index].fileName = '';
                this.dokumenList[index].fileSize = '';
            }
        }
    }">
        <!-- Error Alert -->
        @if ($errors->any())
            <x-alert-banner type="error">
                <div class="font-bold flex items-center gap-1.5" x-init="$nextTick(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }); })">
                    Data pengajuan belum valid. Silakan periksa kembali:
                </div>
                <ul class="mt-2 list-disc list-inside space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert-banner>
        @endif

        <form method="POST" action="{{ route('klien.pra-pendaftaran.store') }}" enctype="multipart/form-data" class="space-y-6" @submit="isSubmitting = true">
            @csrf
            
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-stretch">
                
                <!-- Kiri: Informasi Pengajuan -->
                <x-card class="flex flex-col justify-between space-y-6">
                    <div>
                        <div class="border-b border-[#F1F5F9] pb-4">
                            <h3 class="font-bold text-navy-dark text-lg">Informasi Pengajuan</h3>
                            <p class="text-xs text-gray-500 mt-1 leading-relaxed">Lengkapi informasi awal perkara yang akan didaftarkan.</p>
                        </div>

                        <div class="pt-6 space-y-6">
                            <!-- Kategori Perkara -->
                            <div class="space-y-2">
                                <x-input-label for="id_kategori" :value="__('Kategori Perkara')" />
                                <x-select id="id_kategori" name="id_kategori" required>
                                    <option value="">Pilih kategori perkara</option>
                                    @foreach ($kategoriPerkara as $kategori)
                                        <option value="{{ $kategori->id_kategori }}" @selected(old('id_kategori') == $kategori->id_kategori)>
                                            {{ $kategori->nama_kategori }}
                                        </option>
                                    @endforeach
                                </x-select>
                                <x-input-error class="mt-1" :messages="$errors->get('id_kategori')" />
                            </div>

                            <!-- Judul Perkara -->
                            <div class="space-y-2">
                                <x-input-label for="judul_perkara" :value="__('Judul Perkara')" />
                                <x-text-input type="text" name="judul_perkara" id="judul_perkara" :value="old('judul_perkara')" placeholder="Masukkan judul perkara" required />
                                <x-input-error class="mt-1" :messages="$errors->get('judul_perkara')" />
                            </div>

                            <!-- Kronologi Perkara -->
                            <div class="space-y-2">
                                <x-input-label for="kronologi" :value="__('Kronologi Perkara')" />
                                <x-text-input tag="textarea" id="kronologi" name="kronologi" rows="6" placeholder="Tuliskan kronologi perkara secara ringkas dan jelas" required class="resize-none">{{ old('kronologi') }}</x-text-input>
                                <x-input-error class="mt-1" :messages="$errors->get('kronologi')" />
                            </div>
                        </div>
                    </div>
                </x-card>

                <!-- Kanan: Dokumen Pendukung -->
                <x-card class="flex flex-col justify-start space-y-6">
                    <div class="border-b border-[#F1F5F9] pb-4">
                        <h3 class="font-bold text-navy-dark text-lg">Dokumen Pendukung</h3>
                        <p class="text-xs text-gray-500 mt-1 leading-relaxed">Unggah dokumen awal yang diperlukan untuk verifikasi berkas (Maksimal 5 dokumen).</p>
                    </div>

                    <div class="space-y-6">
                        <template x-for="(doc, index) in dokumenList" :key="doc.id">
                            <div class="space-y-5 p-5 border border-[#E2E8F0] bg-[#F8FAFC]/30 rounded-xl relative">
                                
                                <div class="flex justify-between items-center border-b border-[#E2E8F0] pb-3">
                                    <h4 class="font-bold text-navy-dark text-sm" x-text="'Dokumen #' + (index + 1)"></h4>
                                    <button type="button" @click="removeDokumen(index)" x-show="dokumenList.length > 1" class="text-rose-500 hover:bg-rose-50 p-1.5 rounded-lg transition" title="Hapus Dokumen">
                                        <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                        </svg>
                                    </button>
                                </div>

                                <!-- Nama Dokumen -->
                                <div class="space-y-2">
                                    <label :for="'nama_dokumen_' + index" class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Nama Dokumen <span class="text-red-500">*</span></label>
                                    <input type="text" :name="'dokumen[' + index + '][nama_dokumen]'" :id="'nama_dokumen_' + index" placeholder="Contoh: KTP Pelapor" required 
                                           class="block w-full bg-white border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm placeholder-gray-400 transition shadow-sm h-11 px-4">
                                </div>

                                <!-- Jenis Dokumen -->
                                <div class="space-y-2">
                                    <label :for="'jenis_dokumen_' + index" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Jenis Dokumen <span class="text-red-500">*</span></label>
                                    <x-select ::name="'dokumen[' + index + '][jenis_dokumen]'" ::id="'jenis_dokumen_' + index" required>
                                        <option value="">Pilih jenis dokumen</option>
                                        <option value="ktp">KTP</option>
                                        <option value="kk">Kartu Keluarga</option>
                                        <option value="surat_kuasa">Surat Kuasa</option>
                                        <option value="bukti_transfer">Bukti Transfer</option>
                                        <option value="dokumen_lainnya">Dokumen Lainnya</option>
                                    </x-select>
                                </div>

                                <!-- Upload File -->
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Upload File <span class="text-red-500">*</span></label>
                                    <div class="border-2 border-dashed border-[#E2E8F0] hover:border-accent-blue rounded-xl p-6 bg-white text-center transition cursor-pointer relative group">
                                        <input type="file" :name="'dokumen[' + index + '][file_dokumen]'" accept=".pdf,.jpg,.jpeg,.png" required class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" @change="updateFileName($event.target, index)">
                                        
                                        <svg class="mx-auto h-8 w-8 text-gray-400 group-hover:text-accent-blue transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" />
                                        </svg>
                                        
                                        <p class="mt-2 text-sm font-bold transition" :class="doc.fileName ? 'text-accent-blue' : 'text-navy-dark group-hover:text-accent-blue'" x-text="doc.fileName || 'Pilih file PDF/JPG/PNG'"></p>
                                        <p class="mt-1 text-xs font-medium text-gray-400" x-text="doc.fileSize ? ('Ukuran: ' + doc.fileSize) : 'Maksimal 5 MB'"></p>
                                    </div>
                                </div>

                            </div>
                        </template>

                        <button type="button" @click="addDokumen()" x-show="dokumenList.length < 5 && dokumenList[dokumenList.length - 1].fileName !== ''" 
                                class="w-full flex justify-center items-center gap-2 py-3 border-2 border-dashed border-accent-blue text-accent-blue hover:bg-blue-50 rounded-xl font-bold text-sm transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                            </svg>
                            Tambah Dokumen Lain
                        </button>
                    </div>
                </x-card>

            </div> <!-- End of 2 Columns -->

            <!-- Bagian Bawah: Pernyataan & Tombol Aksi -->
            <x-card class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="space-y-1">
                    <h4 class="font-bold text-navy-dark text-sm">Review Sebelum Submit</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        Pastikan semua data dan dokumen sudah benar. Pengajuan yang sudah dikirim tidak dapat diedit oleh Klien.
                    </p>
                </div>
                
                <div class="flex items-center gap-3 w-full md:w-auto shrink-0 justify-end">
                    <x-secondary-button href="{{ route('klien.pra-pendaftaran.index') }}" tag="a" class="w-full md:w-auto">
                        Batal
                    </x-secondary-button>
                    <x-primary-button type="submit" ::disabled="isSubmitting" class="w-full md:w-auto whitespace-nowrap">
                        <span x-show="!isSubmitting" class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                            </svg>
                            Kirim Pengajuan
                        </span>
                        <span x-show="isSubmitting" class="flex items-center gap-2" style="display: none;">
                            <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Mengirim...
                        </span>
                    </x-primary-button>
                </div>
            </x-card>
        </form>
    </div>

    <!-- Errors for specific doc validation mapping (Optional support) -->
    @if ($errors->has('dokumen.*'))
        <!-- The error box at the top will handle displaying array errors automatically via $errors->all() -->
    @endif
</x-app-layout>
