<x-guest-layout>
    <div class="space-y-5">
        <div>
            <p class="text-xs font-semibold uppercase tracking-wider text-accent-blue">Privasi dan Data Pribadi</p>
            <h1 class="mt-1 text-2xl font-bold text-navy-dark">Kebijakan Privasi</h1>
            @if (config('privacy.effective_date'))
                <p class="mt-1 text-sm text-gray-500">Berlaku sejak {{ config('privacy.effective_date') }} · Versi {{ config('privacy.policy_version') }}</p>
            @endif
        </div>

        @if (session('status') === 'email-verified-policy-pending')
            <div class="rounded-lg border border-blue-200 bg-blue-50 p-4 text-sm font-semibold text-blue-900" role="status">
                Email Anda telah terverifikasi. Layanan Klien akan tersedia setelah kebijakan privasi disahkan.
            </div>
        @endif

        @unless (config('privacy.ready'))
            <div class="rounded-lg border border-amber-300 bg-amber-50 p-4 text-sm font-semibold text-amber-900" role="status">
                USULAN—WAJIB DISETUJUI FIRMA. Naskah ini belum berlaku dan persetujuan tidak dapat diberikan sampai konfigurasi release diaktifkan.
            </div>
        @endunless

        <div class="space-y-4 text-sm leading-6 text-gray-700">
            <section aria-labelledby="privacy-purpose">
                <h2 id="privacy-purpose" class="font-bold text-navy-dark">Data dan tujuan pemrosesan</h2>
                <p class="mt-1">Aplikasi memproses data akun, profil Klien, informasi pra-pendaftaran perkara, dokumen pendukung, hasil verifikasi, jadwal, dan informasi konsultasi untuk menyediakan layanan pra-pendaftaran perkara.</p>
            </section>

            <section aria-labelledby="privacy-security">
                <h2 id="privacy-security" class="font-bold text-navy-dark">Akses dan keamanan</h2>
                <p class="mt-1">Dokumen perkara disimpan sebagai resource privat dan hanya tersedia melalui akun yang berwenang. Isi dokumen, nomor identitas, kronologi, password, token, dan isi catatan hukum tidak disimpan dalam metadata audit aplikasi.</p>
            </section>

            <section aria-labelledby="privacy-retention">
                <h2 id="privacy-retention" class="font-bold text-navy-dark">Usulan masa retensi</h2>
                <p class="mt-1">Seluruh periode berikut masih berupa usulan dan belum menjadi dasar penghapusan otomatis.</p>
                <div class="mt-3 overflow-x-auto rounded-lg border border-slate-200">
                    <table class="min-w-full divide-y divide-slate-200 text-left text-xs">
                        <thead class="bg-slate-50 text-slate-700">
                            <tr>
                                <th scope="col" class="px-3 py-2 font-semibold">Kelompok data</th>
                                <th scope="col" class="px-3 py-2 font-semibold">Usulan retensi</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 bg-white">
                            <tr><td class="px-3 py-2">Akun, profil, dan bukti persetujuan</td><td class="px-3 py-2">Masa layanan + 5 tahun</td></tr>
                            <tr><td class="px-3 py-2">Pengajuan, dokumen, verifikasi, dan riwayat</td><td class="px-3 py-2">5 tahun setelah proses ditutup</td></tr>
                            <tr><td class="px-3 py-2">Booking dan reschedule</td><td class="px-3 py-2">5 tahun setelah konsultasi selesai</td></tr>
                            <tr><td class="px-3 py-2">Audit log aplikasi</td><td class="px-3 py-2">2 tahun</td></tr>
                            <tr><td class="px-3 py-2">Log aplikasi</td><td class="px-3 py-2">14 hari</td></tr>
                            <tr><td class="px-3 py-2">Failed jobs</td><td class="px-3 py-2">7 hari</td></tr>
                            <tr><td class="px-3 py-2">Backup operasional</td><td class="px-3 py-2">Target rolling 30 hari, menunggu kemampuan provider</td></tr>
                            <tr><td class="px-3 py-2">Soft-deleted blob/container</td><td class="px-3 py-2">14 hari</td></tr>
                            <tr><td class="px-3 py-2">Versi Blob sebelumnya</td><td class="px-3 py-2">90 hari</td></tr>
                        </tbody>
                    </table>
                </div>
            </section>

            <section aria-labelledby="privacy-rights">
                <h2 id="privacy-rights" class="font-bold text-navy-dark">Permintaan terkait data pribadi</h2>
                <p class="mt-1">Klien dapat mengajukan permintaan akses, koreksi, atau penghapusan melalui kanal resmi {{ config('firm.name') }}. Pelaksanaannya mengikuti kewajiban retensi dan ketentuan hukum yang telah disahkan firma.</p>
            </section>

            <p>Naskah final akan ditinjau dengan mengacu pada <a class="font-semibold text-accent-blue hover:underline" href="https://jdih.komdigi.go.id/produk_hukum/view/id/832/t/undangundang%2Bnomor%2B27%2Btahun%2B2022" target="_blank" rel="noopener noreferrer">Undang-Undang Nomor 27 Tahun 2022 tentang Pelindungan Data Pribadi</a>.</p>

            <div class="rounded-lg bg-gray-50 p-4">
                <p class="font-semibold text-navy-dark">Kontak resmi</p>
                <p class="mt-1">Email: {{ config('firm.email') }}</p>
                <p>Telepon/WhatsApp: {{ config('firm.phone') }}</p>
                <p>Alamat: {{ config('firm.address') }}</p>
            </div>
            <p class="text-xs text-gray-500">Dasar pemrosesan, periode retensi, mekanisme permintaan, versi, dan tanggal berlaku harus disahkan oleh pihak firma sebelum aplikasi digunakan pada production.</p>
        </div>
    </div>
</x-guest-layout>
