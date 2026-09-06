<x-guest-layout>
    <div class="space-y-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-accent-blue">Langkah terakhir</p>
            <h1 class="mt-1 text-2xl font-bold text-navy-dark">Persetujuan Kebijakan Privasi</h1>
            <p class="mt-2 text-sm leading-6 text-gray-600">Baca kebijakan privasi sebelum melanjutkan ke layanan Klien.</p>
        </div>

        <a class="inline-flex text-sm font-semibold text-accent-blue hover:underline" href="{{ route('privacy.policy') }}" target="_blank" rel="noopener noreferrer">
            Buka Kebijakan Privasi
        </a>

        <form method="POST" action="{{ route('privacy.consent.store') }}" class="space-y-5">
            @csrf
            <label class="flex items-start gap-3 rounded-lg border border-gray-200 p-4 text-sm leading-6 text-gray-700">
                <input class="mt-1 rounded border-gray-300 text-accent-blue focus:ring-accent-blue" type="checkbox" name="privacy_consent" value="1" required>
                <span>Saya telah membaca dan menyetujui Kebijakan Privasi versi {{ config('privacy.policy_version') }}.</span>
            </label>
            <x-input-error :messages="$errors->get('privacy_consent')" />

            <x-primary-button class="w-full justify-center">Simpan Persetujuan</x-primary-button>
        </form>
    </div>
</x-guest-layout>
