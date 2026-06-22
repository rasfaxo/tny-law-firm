<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Detail Staf Legal') }}
            </h2>

            <a href="{{ route('admin.staf-legal.edit', $stafLegal) }}" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Edit') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8 space-y-6">
            @if (session('success'))
                <div class="rounded-md bg-green-50 p-4 text-sm text-green-700">
                    {{ session('success') }}
                </div>
            @endif

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 space-y-4">
                    <div>
                        <div class="text-sm font-medium text-gray-500">Nama</div>
                        <div class="mt-1">{{ $stafLegal->nama }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Email</div>
                        <div class="mt-1">{{ $stafLegal->email }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">No. Telepon</div>
                        <div class="mt-1">{{ $stafLegal->no_telepon ?? '-' }}</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Role</div>
                        <div class="mt-1">Staf Legal</div>
                    </div>

                    <div>
                        <div class="text-sm font-medium text-gray-500">Status Akun</div>
                        <div class="mt-1">
                            <span class="inline-flex rounded-full px-2 text-xs font-semibold leading-5 {{ $stafLegal->status_akun === 'aktif' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($stafLegal->status_akun) }}
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center justify-between pt-4">
                        <a href="{{ route('admin.staf-legal.index') }}" class="text-sm text-gray-600 hover:text-gray-900">
                            {{ __('Kembali') }}
                        </a>

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
            </div>
        </div>
    </div>
</x-app-layout>
