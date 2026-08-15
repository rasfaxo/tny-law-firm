<x-app-layout title="Detail Staf Legal" :breadcrumbs="[['label' => 'Admin'], ['label' => 'Staf Legal', 'url' => route('admin.staf-legal.index')], ['label' => 'Detail']]">

    <div class="space-y-6">
        <div class="flex justify-between items-center">
            <x-secondary-button href="{{ route('admin.staf-legal.index') }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                <span>{{ __('Kembali') }}</span>
            </x-secondary-button>

            <x-primary-button href="{{ route('admin.staf-legal.edit', $stafLegal) }}" tag="a" class="gap-2">
                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                </svg>
                <span>{{ __('Edit') }}</span>
            </x-primary-button>
        </div>

        <div class="max-w-2xl mx-auto space-y-6">
            @if (session('success'))
                <x-alert-banner type="success">
                    {{ session('success') }}
                </x-alert-banner>
            @endif

            <x-card class="p-0 overflow-hidden">
                <div class="p-6 text-gray-900 space-y-4">
                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Nama</div>
                        <div class="mt-1 text-sm font-semibold text-navy-dark">{{ $stafLegal->nama }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Email</div>
                        <div class="mt-1 text-sm font-semibold text-navy-dark">{{ $stafLegal->email }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">No. Telepon</div>
                        <div class="mt-1 text-sm font-semibold text-navy-dark">{{ $stafLegal->no_telepon ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Role</div>
                        <div class="mt-1 text-sm font-semibold text-navy-dark">Staf Legal</div>
                    </div>

                    <div>
                        <div class="text-xs font-bold text-gray-400 uppercase tracking-wider">Status Akun</div>
                        <div class="mt-1">
                            <span class="inline-flex rounded-full px-2.5 py-0.5 text-xs font-semibold leading-5 {{ $stafLegal->status_akun === 'aktif' ? 'bg-green-50 text-green-700 border border-green-200' : 'bg-red-50 text-red-700 border border-red-200' }}">
                                {{ ucfirst($stafLegal->status_akun) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-end pt-4 border-t border-[#F1F5F9]">
                        <form method="POST" action="{{ route('admin.staf-legal.status', $stafLegal) }}">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status_akun" value="{{ $stafLegal->status_akun === 'aktif' ? 'nonaktif' : 'aktif' }}">
                            <x-primary-button>
                                {{ $stafLegal->status_akun === 'aktif' ? __('Nonaktifkan Akun') : __('Aktifkan Akun') }}
                            </x-primary-button>
                        </form>
                    </div>
                </div>
            </x-card>
        </div>
    </div>
</x-app-layout>
