<x-app-layout title="Verifikasi Berkas" :breadcrumbs="[['label' => 'Staf Legal'], ['label' => 'Pengajuan Verifikasi', 'url' => route('staf-legal.verifikasi-berkas.index')], ['label' => 'PP-' . sprintf('%03d', $praPendaftaranPerkara->id_pendaftaran), 'url' => route('staf-legal.verifikasi-berkas.show', $praPendaftaranPerkara)], ['label' => 'Verifikasi']]">

    <div class="space-y-6" x-data="{ 
        statusVerifikasi: '{{ old('status_verifikasi', 'berkas_lengkap') }}',
        docStatus: {
            @foreach ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                '{{ $dokumen->id_dokumen }}': '{{ old("dokumen.{$dokumen->id_dokumen}.status_dokumen", "valid") }}',
            @endforeach
        },
        isSubmitting: false,
        setToLengkap() {
            this.statusVerifikasi = 'berkas_lengkap';
            // Set all docs to valid automatically
            for (let id in this.docStatus) {
                this.docStatus[id] = 'valid';
            }
        }
    }">
        @if ($errors->any())
            <x-alert-banner type="error">
                <div class="font-bold flex items-center gap-1.5" x-init="$nextTick(() => { $el.scrollIntoView({ behavior: 'smooth', block: 'start' }); })">
                    Data verifikasi belum valid. Silakan periksa kembali:
                </div>
                <ul class="mt-2 list-disc list-inside space-y-1 pl-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </x-alert-banner>
        @endif

        <!-- Instruksi Verifikasi Alert -->
        <x-alert-banner type="info" title="Instruksi Verifikasi">
            Tentukan hasil verifikasi berkas. Jika berkas belum lengkap, berikan catatan yang jelas agar Klien memahami dokumen yang perlu diperbaiki.
        </x-alert-banner>

        <form method="POST" action="{{ route('staf-legal.verifikasi-berkas.store', $praPendaftaranPerkara) }}" class="space-y-6" @submit="isSubmitting = true">
            @csrf

            <!-- Form Split Cards -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Hasil Verifikasi Card -->
                <x-card class="space-y-5 flex flex-col justify-between h-full">
                    <div class="space-y-4">
                        <div class="border-b border-[#f1f5f9] pb-4">
                            <h3 class="font-bold text-[16px] text-[#0f172a]">Hasil Verifikasi</h3>
                            <p class="text-[13px] text-[#64748b] mt-1">Pilih keputusan berdasarkan pemeriksaan dokumen.</p>
                        </div>

                        <!-- Radio Options -->
                        <div class="space-y-3">
                            <!-- Berkas Lengkap -->
                            <label class="flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition duration-150"
                                   :class="statusVerifikasi === 'berkas_lengkap' ? 'bg-[#eff6ff] border-[#1d4ed8] text-[#1d4ed8]' : 'bg-white border-[#e2e8f0] hover:bg-slate-50 text-[#334155]'">
                                <input type="radio" name="status_verifikasi" value="berkas_lengkap" 
                                       class="border-gray-300 text-indigo-600 focus:ring-indigo-500"
                                       @click="setToLengkap()"
                                       :checked="statusVerifikasi === 'berkas_lengkap'">
                                <span class="text-[14px] font-semibold">Berkas Lengkap</span>
                            </label>

                            <!-- Berkas Tidak Lengkap -->
                            <label class="flex items-center gap-3 p-4 rounded-xl border cursor-pointer transition duration-150"
                                   :class="statusVerifikasi === 'berkas_tidak_lengkap' ? 'bg-[#fef2f2] border-[#dc2626] text-[#b91c1c]' : 'bg-white border-[#e2e8f0] hover:bg-slate-50 text-[#334155]'">
                                <input type="radio" name="status_verifikasi" value="berkas_tidak_lengkap" 
                                       class="border-gray-300 text-red-600 focus:ring-red-500"
                                       @click="statusVerifikasi = 'berkas_tidak_lengkap'"
                                       :checked="statusVerifikasi === 'berkas_tidak_lengkap'">
                                <span class="text-[14px] font-semibold">Berkas Tidak Lengkap</span>
                            </label>
                        </div>
                    </div>

                    <!-- Dynamic Preview Badge and Info -->
                    <div class="pt-4 border-t border-[#f1f5f9] flex flex-col gap-2">
                        <div class="flex items-center gap-2">
                            <span class="text-[12px] font-semibold text-[#64748b]">Keputusan terpilih:</span>
                            <span class="inline-flex items-center rounded-full px-2.5 py-0.5 text-xs font-semibold tracking-wide border shadow-sm"
                                  :class="statusVerifikasi === 'berkas_lengkap' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'">
                                <span x-text="statusVerifikasi === 'berkas_lengkap' ? 'Berkas Lengkap' : 'Berkas Tidak Lengkap'"></span>
                            </span>
                        </div>
                        <p class="text-[12.5px] text-[#64748b]">
                            <span x-show="statusVerifikasi === 'berkas_lengkap'">Jika berkas dinyatakan lengkap, Klien dapat memilih jadwal konsultasi.</span>
                            <span x-show="statusVerifikasi === 'berkas_tidak_lengkap'">Jika berkas tidak lengkap, Klien harus memperbaiki dokumen pendukung yang bermasalah.</span>
                        </p>
                    </div>
                </x-card>

                <!-- Catatan Umum Card -->
                <x-card class="space-y-4 h-full">
                    <div class="border-b border-[#f1f5f9] pb-4">
                        <h3 class="font-bold text-[16px] text-[#0f172a]">Catatan Umum Verifikasi</h3>
                        <p class="text-[13px] text-[#64748b] mt-1">Opsional jika berkas lengkap. Wajib jika berkas tidak lengkap.</p>
                    </div>

                    <div class="space-y-2">
                        <x-input-label for="catatan_umum" value="Catatan Umum" />
                        <x-text-input tag="textarea" id="catatan_umum" name="catatan_umum" rows="6" 
                                  class="w-full resize-none p-4"
                                  placeholder="Tuliskan catatan umum hasil verifikasi...">{{ old('catatan_umum') }}</x-text-input>
                    </div>
                </x-card>
            </div>

            <!-- Catatan Per Dokumen Card -->
            <x-card class="space-y-4">
                <div class="border-b border-[#f1f5f9] pb-4">
                    <h3 class="font-bold text-[16px] text-[#0f172a]">Catatan Per Dokumen</h3>
                    <p class="text-[13px] text-[#64748b] mt-1">Tandai status setiap dokumen dan tambahkan catatan jika perlu perbaikan.</p>
                </div>

                <!-- Desktop Table Layout -->
                <div class="hidden md:block overflow-x-auto border border-[#E2E8F0] rounded-xl">
                    <table class="min-w-full divide-y divide-[#e2e8f0]">
                        <thead class="bg-[#f8fafc]">
                            <tr>
                                <th class="px-5 py-4 text-left text-xs font-semibold text-[#64748b] tracking-wider uppercase w-1/4">Dokumen</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold text-[#64748b] tracking-wider uppercase w-1/4">Status</th>
                                <th class="px-5 py-4 text-left text-xs font-semibold text-[#64748b] tracking-wider uppercase w-2/5">Catatan Perbaikan</th>
                                <th class="px-5 py-4 text-right text-xs font-semibold text-[#64748b] tracking-wider uppercase w-[10%]">Aksi</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-[#f1f5f9]">
                            @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                                <tr class="align-top">
                                    <td class="px-5 py-4 text-[13px]">
                                        <div class="font-semibold text-[#334155]">{{ $dokumen->nama_dokumen }}</div>
                                        <div class="text-xs text-[#64748b] mt-0.5">{{ $dokumen->jenis_dokumen }}</div>
                                    </td>
                                    <td class="px-5 py-4">
                                        <div class="flex flex-col gap-2">
                                            <!-- Option Valid -->
                                            <label class="inline-flex items-center gap-2 text-[13px] cursor-pointer"
                                                   :class="statusVerifikasi === 'berkas_lengkap' ? 'opacity-60 cursor-not-allowed' : ''">
                                                <input type="radio" 
                                                       :name="'dummy_status_' + '{{ $dokumen->id_dokumen }}'" 
                                                       value="valid" 
                                                       class="border-gray-300 text-green-600 focus:ring-green-500"
                                                       x-model="docStatus['{{ $dokumen->id_dokumen }}']"
                                                       :disabled="statusVerifikasi === 'berkas_lengkap'">
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-green-50 text-green-700 border border-green-200">Valid</span>
                                            </label>

                                            <!-- Option Perlu Perbaikan -->
                                            <label class="inline-flex items-center gap-2 text-[13px] cursor-pointer"
                                                   :class="statusVerifikasi === 'berkas_lengkap' ? 'opacity-60 cursor-not-allowed' : ''">
                                                <input type="radio" 
                                                       :name="'dummy_status_' + '{{ $dokumen->id_dokumen }}'" 
                                                       value="perlu_perbaikan" 
                                                       class="border-gray-300 text-red-600 focus:ring-red-500"
                                                       x-model="docStatus['{{ $dokumen->id_dokumen }}']"
                                                       :disabled="statusVerifikasi === 'berkas_lengkap'">
                                                <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-red-50 text-red-700 border border-red-200">Perlu Perbaikan</span>
                                            </label>
                                        </div>
                                    </td>
                                     <td class="px-5 py-4">
                                        <x-text-input tag="textarea" name="dokumen[{{ $dokumen->id_dokumen }}][catatan]" rows="3" 
                                                  class="w-full resize-none"
                                                  placeholder="Tuliskan alasan penolakan atau catatan perbaikan dokumen ini..."
                                                  x-model="docStatus['{{ $dokumen->id_dokumen }}'] === 'perlu_perbaikan' ? undefined : (docStatus['{{ $dokumen->id_dokumen }}'] = 'valid' ? '' : '')"
                                                  :disabled="statusVerifikasi === 'berkas_lengkap' || docStatus['{{ $dokumen->id_dokumen }}'] !== 'perlu_perbaikan'"
                                                  :required="statusVerifikasi === 'berkas_tidak_lengkap' && docStatus['{{ $dokumen->id_dokumen }}'] === 'perlu_perbaikan'">{{ old("dokumen.{$dokumen->id_dokumen}.catatan") }}</x-text-input>
                                    </td>
                                    <td class="px-5 py-4 text-right text-[12px] font-semibold">
                                        <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" class="text-[#1d4ed8] hover:text-[#1e40af] transition duration-150">
                                            Lihat
                                        </a>
                                    </td>
                                </tr>
                                <!-- Hidden input for Laravel request binding -->
                                <input type="hidden" name="dokumen[{{ $dokumen->id_dokumen }}][status_dokumen]" :value="docStatus['{{ $dokumen->id_dokumen }}']">
                            @empty
                                <tr>
                                    <td colspan="4" class="px-5 py-8 text-center text-[13px] text-[#64748b]">
                                        <x-empty-state title="Belum ada dokumen" message="Belum ada dokumen yang diunggah." />
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card Layout -->
                <div class="block md:hidden space-y-4">
                    @forelse ($praPendaftaranPerkara->dokumenAktif as $dokumen)
                        <div class="bg-white border border-[#e2e8f0] rounded-[16px] p-4 space-y-4">
                            <div class="flex justify-between items-start gap-2">
                                <div>
                                    <div class="font-semibold text-navy-dark text-[13px]">{{ $dokumen->nama_dokumen }}</div>
                                    <div class="text-xs text-[#64748b] mt-0.5">{{ $dokumen->jenis_dokumen }}</div>
                                </div>
                                <a href="{{ route('staf-legal.dokumen.show', $dokumen) }}" class="text-[#1d4ed8] hover:text-[#1e40af] text-xs font-bold shrink-0 transition duration-150">
                                    Lihat Dokumen
                                </a>
                            </div>

                            <div class="space-y-2 pt-2 border-t border-[#f1f5f9]">
                                <span class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Status Dokumen</span>
                                <div class="flex items-center gap-4">
                                    <!-- Option Valid -->
                                    <label class="inline-flex items-center gap-2 text-[13px] cursor-pointer"
                                           :class="statusVerifikasi === 'berkas_lengkap' ? 'opacity-60 cursor-not-allowed' : ''">
                                        <input type="radio" 
                                               :name="'dummy_status_mobile_' + '{{ $dokumen->id_dokumen }}'" 
                                               value="valid" 
                                               class="border-gray-300 text-green-600 focus:ring-green-500"
                                               x-model="docStatus['{{ $dokumen->id_dokumen }}']"
                                               :disabled="statusVerifikasi === 'berkas_lengkap'">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-green-50 text-green-700 border border-green-200">Valid</span>
                                    </label>

                                    <!-- Option Perlu Perbaikan -->
                                    <label class="inline-flex items-center gap-2 text-[13px] cursor-pointer"
                                           :class="statusVerifikasi === 'berkas_lengkap' ? 'opacity-60 cursor-not-allowed' : ''">
                                        <input type="radio" 
                                               :name="'dummy_status_mobile_' + '{{ $dokumen->id_dokumen }}'" 
                                               value="perlu_perbaikan" 
                                               class="border-gray-300 text-red-600 focus:ring-red-500"
                                               x-model="docStatus['{{ $dokumen->id_dokumen }}']"
                                               :disabled="statusVerifikasi === 'berkas_lengkap'">
                                        <span class="inline-flex items-center rounded-full px-2 py-0.5 text-xs font-semibold bg-red-50 text-red-700 border border-red-200">Perlu Perbaikan</span>
                                    </label>
                                </div>
                            </div>

                            <div class="space-y-1.5">
                                <span class="block text-xs font-bold text-gray-600 uppercase tracking-wider">Catatan Perbaikan</span>
                                <x-text-input tag="textarea" name="dokumen[{{ $dokumen->id_dokumen }}][catatan_mobile]" rows="2" 
                                          class="w-full resize-none"
                                          placeholder="Tuliskan alasan penolakan atau catatan perbaikan..."
                                          x-model="docStatus['{{ $dokumen->id_dokumen }}'] === 'perlu_perbaikan' ? undefined : (docStatus['{{ $dokumen->id_dokumen }}'] = 'valid' ? '' : '')"
                                          :disabled="statusVerifikasi === 'berkas_lengkap' || docStatus['{{ $dokumen->id_dokumen }}'] !== 'perlu_perbaikan'"
                                          :required="statusVerifikasi === 'berkas_tidak_lengkap' && docStatus['{{ $dokumen->id_dokumen }}'] === 'perlu_perbaikan'">{{ old("dokumen.{$dokumen->id_dokumen}.catatan") }}</x-text-input>
                            </div>
                        </div>
                    @empty
                        <div class="py-12">
                            <x-empty-state title="Belum ada dokumen" message="Belum ada dokumen yang diunggah." />
                        </div>
                    @endforelse
                </div>
            </x-card>

            <!-- Alert Warning -->
            <x-alert-banner type="warning">
                <strong>Penting:</strong> Jika Anda memilih <em>Berkas Tidak Lengkap</em>, pastikan minimal satu dokumen ditandai <em>Perlu Perbaikan</em> dan memiliki <em>Catatan Perbaikan</em>. Sebaliknya, jika memilih <em>Berkas Lengkap</em>, seluruh dokumen otomatis ditandai sebagai <em>Valid</em>.
            </x-alert-banner>

            <!-- Form Action Buttons -->
            <div class="flex items-center justify-end gap-3 pt-2">
                <x-secondary-button href="{{ route('staf-legal.verifikasi-berkas.show', $praPendaftaranPerkara) }}" tag="a">
                    Batal
                </x-secondary-button>
                <x-primary-button ::disabled="isSubmitting">
                    <span x-show="!isSubmitting">Simpan Verifikasi</span>
                    <span x-show="isSubmitting" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <span>Menyimpan...</span>
                    </span>
                </x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
