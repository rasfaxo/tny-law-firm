<x-app-layout title="Edit Data Klien" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Data Klien', 'url' => route('admin.klien.index')], ['label' => 'Edit Data']]">

    <div class="space-y-6">
        <div class="flex justify-start">
            <a href="{{ route('admin.klien.show', $klien) }}" class="inline-flex items-center justify-center bg-white border border-[#E2E8F0] hover:border-accent-blue text-navy-dark hover:text-accent-blue font-bold text-xs px-4 py-2.5 rounded-xl transition shadow-sm gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali ke Detail') }}</span>
            </a>
        </div>

        <div class="max-w-2xl mx-auto">
            <div class="bg-white border border-[#E2E8F0] p-6 sm:p-8 rounded-2xl shadow-sm" x-data="{ isSubmitting: false }">
                <div class="mb-6">
                    <h2 class="text-lg font-bold text-navy-dark">Edit Data Dasar Klien</h2>
                    <p class="text-sm text-gray-500 mt-1">Ubah nama, email, nomor telepon, atau status akun klien.</p>
                </div>
                
                <form method="POST" action="{{ route('admin.klien.update', $klien) }}" class="space-y-6" @submit="isSubmitting = true">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="nama" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Nama Klien</label>
                        <input id="nama" name="nama" type="text" class="mt-1 block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm" value="{{ old('nama', $klien->nama) }}" required autofocus />
                        <x-input-error class="mt-2" :messages="$errors->get('nama')" />
                    </div>

                    <div>
                        <label for="email" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Email Klien</label>
                        <input id="email" name="email" type="email" class="mt-1 block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm" value="{{ old('email', $klien->email) }}" required />
                        <x-input-error class="mt-2" :messages="$errors->get('email')" />
                    </div>

                    <div>
                        <label for="no_telepon" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">No. Telepon (Opsional)</label>
                        <input id="no_telepon" name="no_telepon" type="text" class="mt-1 block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm" value="{{ old('no_telepon', $klien->no_telepon) }}" />
                        <x-input-error class="mt-2" :messages="$errors->get('no_telepon')" />
                    </div>

                    <div>
                        <label for="status_akun" class="block text-xs font-bold text-gray-400 uppercase tracking-wider">Status Akun</label>
                        <select id="status_akun" name="status_akun" class="mt-1 block w-full bg-[#F8FAFC] border-[#E2E8F0] focus:border-accent-blue focus:ring focus:ring-accent-blue/20 rounded-xl text-sm transition shadow-sm" required>
                            <option value="aktif" @selected(old('status_akun', $klien->status_akun) === 'aktif')>Aktif</option>
                            <option value="nonaktif" @selected(old('status_akun', $klien->status_akun) === 'nonaktif')>Nonaktif</option>
                        </select>
                        <x-input-error class="mt-2" :messages="$errors->get('status_akun')" />
                    </div>

                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-[#F1F5F9]">
                        <a href="{{ route('admin.klien.show', $klien) }}" class="text-sm font-semibold text-gray-500 hover:text-navy-dark transition">
                            {{ __('Batal') }}
                        </a>
                        <button type="submit" class="bg-[#1e3a8a] hover:bg-blue-900 text-white font-bold text-sm h-11 px-6 rounded-xl flex items-center justify-center transition shadow-md shadow-blue-900/20" x-bind:disabled="isSubmitting" x-bind:class="{ 'opacity-70 cursor-not-allowed': isSubmitting }">
                            <span x-show="!isSubmitting">Simpan Perubahan</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                Menyimpan...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
