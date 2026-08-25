<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Portal Pra-Pendaftaran Perkara - TNY Law Firm</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-[#F8FAFC] text-navy-dark">
        <!-- Header / Navigation -->
        <header class="bg-white border-b border-[#E2E8F0] sticky top-0 z-50 transition-all duration-300">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 h-[82px] flex items-center justify-between">
                <!-- Logo -->
                <div class="flex items-center gap-2">
                    <span class="font-extrabold text-xl text-navy-dark tracking-wider">TNY Law Firm</span>
                </div>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center gap-8">
                    <a href="#beranda" class="text-sm font-medium text-accent-blue transition duration-150">Beranda</a>
                    <a href="#layanan" class="text-sm font-medium text-gray-500 hover:text-navy-dark transition duration-150">Layanan</a>
                    <a href="#alur" class="text-sm font-medium text-gray-500 hover:text-navy-dark transition duration-150">Alur</a>
                    <a href="#konsultasi" class="text-sm font-medium text-gray-500 hover:text-navy-dark transition duration-150">Konsultasi</a>
                    <a href="#tentang" class="text-sm font-medium text-gray-500 hover:text-navy-dark transition duration-150">Tentang</a>
                </nav>

                <!-- Auth Buttons -->
                <div class="flex items-center gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}" class="text-sm font-semibold text-accent-blue hover:text-navy-dark transition duration-150">
                            Dashboard
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-navy-primary hover:text-navy-dark transition duration-150">
                            Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-navy-primary text-white text-sm font-semibold px-5 py-2.5 rounded-xl hover:bg-navy-dark hover:shadow-lg transition duration-200">
                            Register
                        </a>
                    @endauth
                </div>
            </div>
        </header>

        <!-- Hero Section -->
        <section id="beranda" class="bg-white relative overflow-hidden py-16 md:py-24 border-b border-[#E2E8F0]">
            <!-- Decorative blobs -->
            <div class="absolute bg-navy-dark/5 rounded-full w-[420px] h-[420px] top-[70px] -right-[50px] blur-3xl -z-10"></div>
            <div class="absolute bg-[#F59E0B]/10 rounded-full w-[128px] h-[128px] top-[115px] right-[400px] blur-2xl -z-10"></div>

            <div class="max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                <!-- Hero Left -->
                <div class="lg:col-span-7 space-y-6">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-50 text-accent-blue border border-blue-100">
                        Portal Pra-Pendaftaran Perkara
                    </span>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-navy-dark leading-[1.1] tracking-tight">
                        Pra-Pendaftaran Perkara Lebih Mudah
                    </h1>
                    <p class="text-lg text-gray-500 max-w-2xl leading-relaxed">
                        Ajukan pra-pendaftaran perkara secara online, unggah dokumen pendukung, pantau status verifikasi, dan pilih jadwal konsultasi (online/offline) melalui satu portal sistem yang terstruktur.
                    </p>
                    <div class="flex flex-wrap items-center gap-4 pt-2">
                        @auth
                            <a href="{{ route('dashboard') }}" class="bg-accent-blue text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-blue-700 hover:shadow-lg transition duration-200">
                                Masuk Dashboard
                            </a>
                        @else
                            <a href="{{ route('register') }}" class="bg-accent-blue text-white font-semibold px-8 py-3.5 rounded-xl hover:bg-blue-700 hover:shadow-lg transition duration-200">
                                Mulai Registrasi
                            </a>
                            <a href="{{ route('login') }}" class="bg-white border border-[#E2E8F0] text-navy-dark font-semibold px-8 py-3.5 rounded-xl hover:bg-gray-50 hover:border-gray-300 transition duration-200">
                                Login Akun
                            </a>
                        @endauth
                    </div>
                </div>

                <!-- Hero Right (Dashboard Mockup) -->
                <div class="lg:col-span-5 flex justify-center">
                    <div class="bg-white border border-[#E2E8F0] rounded-3xl shadow-[0px_20px_40px_rgba(15,23,42,0.08)] overflow-hidden w-full max-w-[480px]">
                        <!-- Mockup Header -->
                        <div class="bg-navy-dark h-[62px] flex items-center px-6">
                            <span class="font-bold text-sm text-white">Dashboard Portal Klien</span>
                        </div>
                        <!-- Mockup Body -->
                        <div class="p-6 space-y-6">
                            <!-- Statistics Row -->
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-white border border-[#E2E8F0] p-4 rounded-xl shadow-sm">
                                    <span class="block text-xs font-semibold text-gray-400 uppercase">Status Pengajuan</span>
                                    <span class="block text-sm font-bold text-navy-dark mt-1">Berkas Lengkap</span>
                                    <span class="inline-flex items-center rounded-full bg-green-50 px-2 py-0.5 text-xs font-medium text-green-700 border border-green-200 mt-2">
                                        Valid
                                    </span>
                                </div>
                                <div class="bg-white border border-[#E2E8F0] p-4 rounded-xl shadow-sm">
                                    <span class="block text-xs font-semibold text-gray-400 uppercase">Konsultasi</span>
                                    <span class="block text-sm font-bold text-navy-dark mt-1">Jadwal Terpilih</span>
                                    <span class="inline-flex items-center rounded-full bg-blue-50 px-2 py-0.5 text-xs font-medium text-blue-700 border border-blue-200 mt-2">
                                        Terkonfirmasi
                                    </span>
                                </div>
                            </div>
                            <!-- Case Progress Box -->
                            <div class="bg-[#F8FAFC] border border-[#E2E8F0] p-4 rounded-xl space-y-3">
                                <span class="block text-xs font-bold text-navy-dark">Pengajuan Terbaru</span>
                                <div class="h-px bg-[#E2E8F0]"></div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500 font-medium">PP-001 • Wanprestasi Kontrak</span>
                                    <span class="text-green-600 font-semibold">Berkas Lengkap</span>
                                </div>
                                <div class="h-px bg-[#E2E8F0]"></div>
                                <div class="flex justify-between items-center text-xs">
                                    <span class="text-gray-500 font-medium">PP-002 • Sengketa Lahan</span>
                                    <span class="text-accent-blue font-semibold">Jadwal Dipilih</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="layanan" class="py-16 md:py-24 bg-[#F8FAFC]">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-3xl mx-auto space-y-4">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-navy-dark tracking-tight">
                        Layanan Pra-Pendaftaran
                    </h2>
                    <p class="text-gray-500">
                        Fitur utama yang membantu proses awal konsultasi perkara hukum menjadi lebih terstruktur, transparan, dan mudah dipantau.
                    </p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    <!-- Service 1 -->
                    <div class="bg-white border border-[#E2E8F0] p-6 rounded-xl shadow-sm hover:shadow-md transition duration-200 space-y-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-accent-blue">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-navy-dark">Pengajuan Data Awal</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Klien dapat mengisi kategori, judul, dan kronologi perkara secara rinci untuk memberikan deskripsi awal perkara hukum.
                        </p>
                    </div>

                    <!-- Service 2 -->
                    <div class="bg-white border border-[#E2E8F0] p-6 rounded-xl shadow-sm hover:shadow-md transition duration-200 space-y-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-accent-blue">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-navy-dark">Unggah Dokumen</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Klien dapat mengunggah berbagai berkas pendukung dalam format PDF atau gambar (JPG/PNG) dengan batas file aman 5 MB.
                        </p>
                    </div>

                    <!-- Service 3 -->
                    <div class="bg-white border border-[#E2E8F0] p-6 rounded-xl shadow-sm hover:shadow-md transition duration-200 space-y-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-accent-blue">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-navy-dark">Pantau Status Verifikasi</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Klien memantau status pengajuan secara real-time dan melihat catatan koreksi dokumen apabila berkas ditolak.
                        </p>
                    </div>

                    <!-- Service 4 -->
                    <div class="bg-white border border-[#E2E8F0] p-6 rounded-xl shadow-sm hover:shadow-md transition duration-200 space-y-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-accent-blue">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-navy-dark">Jadwal Konsultasi</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Setelah berkas dinyatakan lengkap, Klien dapat langsung memesan slot waktu konsultasi dengan tim pengacara TNY Law Firm.
                        </p>
                    </div>

                    <!-- Service 5 -->
                    <div class="bg-white border border-[#E2E8F0] p-6 rounded-xl shadow-sm hover:shadow-md transition duration-200 space-y-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-accent-blue">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-navy-dark">Online / Offline</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Klien dapat menentukan metode konsultasi online (via link video call) atau offline (tatap muka di kantor firma hukum).
                        </p>
                    </div>

                    <!-- Service 6 -->
                    <div class="bg-white border border-[#E2E8F0] p-6 rounded-xl shadow-sm hover:shadow-md transition duration-200 space-y-4">
                        <div class="w-12 h-12 bg-blue-50 rounded-xl flex items-center justify-center text-accent-blue">
                            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-navy-dark">Ajukan Reschedule</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            Apabila berhalangan hadir, Klien dapat mengajukan permohonan reschedule tanggal baru dengan persetujuan Admin.
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Workflow Section -->
        <section id="alur" class="py-16 md:py-24 bg-white border-t border-b border-[#E2E8F0]">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 space-y-12">
                <div class="text-center max-w-3xl mx-auto space-y-4">
                    <h2 class="text-3xl md:text-4xl font-extrabold text-navy-dark tracking-tight">
                        Alur Singkat Layanan Portal
                    </h2>
                    <p class="text-gray-500">
                        Kami merancang alur pendaftaran secara sederhana agar Klien memahami proses bisnis dari pendaftaran hingga penyelesaian.
                    </p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Step 1 -->
                    <div class="bg-white border border-[#E2E8F0] p-5 rounded-xl shadow-sm flex items-center gap-4">
                        <div class="bg-navy-dark text-white font-bold rounded-full w-10 h-10 shrink-0 flex items-center justify-center">1</div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">Register / Login</h4>
                            <p class="text-xs text-gray-500">Buat atau masuk ke akun Anda</p>
                        </div>
                    </div>
                    <!-- Step 2 -->
                    <div class="bg-white border border-[#E2E8F0] p-5 rounded-xl shadow-sm flex items-center gap-4">
                        <div class="bg-navy-dark text-white font-bold rounded-full w-10 h-10 shrink-0 flex items-center justify-center">2</div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">Lengkapi Profil</h4>
                            <p class="text-xs text-gray-500">Lengkapi data profil klien Anda</p>
                        </div>
                    </div>
                    <!-- Step 3 -->
                    <div class="bg-white border border-[#E2E8F0] p-5 rounded-xl shadow-sm flex items-center gap-4">
                        <div class="bg-navy-dark text-white font-bold rounded-full w-10 h-10 shrink-0 flex items-center justify-center">3</div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">Buat Pengajuan</h4>
                            <p class="text-xs text-gray-500">Isi kategori & deskripsi perkara</p>
                        </div>
                    </div>
                    <!-- Step 4 -->
                    <div class="bg-white border border-[#E2E8F0] p-5 rounded-xl shadow-sm flex items-center gap-4">
                        <div class="bg-navy-dark text-white font-bold rounded-full w-10 h-10 shrink-0 flex items-center justify-center">4</div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">Unggah Dokumen</h4>
                            <p class="text-xs text-gray-500">Kirim berkas pendukung perkara</p>
                        </div>
                    </div>
                    <!-- Step 5 -->
                    <div class="bg-white border border-[#E2E8F0] p-5 rounded-xl shadow-sm flex items-center gap-4">
                        <div class="bg-navy-dark text-white font-bold rounded-full w-10 h-10 shrink-0 flex items-center justify-center">5</div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">Verifikasi Berkas</h4>
                            <p class="text-xs text-gray-500">Diperiksa oleh Staf Legal</p>
                        </div>
                    </div>
                    <!-- Step 6 -->
                    <div class="bg-white border border-[#E2E8F0] p-5 rounded-xl shadow-sm flex items-center gap-4">
                        <div class="bg-navy-dark text-white font-bold rounded-full w-10 h-10 shrink-0 flex items-center justify-center">6</div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">Pilih Jadwal</h4>
                            <p class="text-xs text-gray-500">Pilih slot jadwal konsultasi</p>
                        </div>
                    </div>
                    <!-- Step 7 -->
                    <div class="bg-white border border-[#E2E8F0] p-5 rounded-xl shadow-sm flex items-center gap-4">
                        <div class="bg-navy-dark text-white font-bold rounded-full w-10 h-10 shrink-0 flex items-center justify-center">7</div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">Konfirmasi Admin</h4>
                            <p class="text-xs text-gray-500">Admin menentukan link/lokasi</p>
                        </div>
                    </div>
                    <!-- Step 8 -->
                    <div class="bg-white border border-[#E2E8F0] p-5 rounded-xl shadow-sm flex items-center gap-4">
                        <div class="bg-navy-dark text-white font-bold rounded-full w-10 h-10 shrink-0 flex items-center justify-center">8</div>
                        <div>
                            <h4 class="font-bold text-navy-dark text-sm">Selesai</h4>
                            <p class="text-xs text-gray-500">Konsultasi hukum dilaksanakan</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About Section -->
        <section id="tentang" class="py-16 md:py-24 bg-[#F8FAFC]">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <h2 class="text-3xl font-extrabold text-navy-dark tracking-tight">Tentang TNY Law Firm</h2>
                    <p class="text-gray-500 leading-relaxed">
                        TNY Law Firm adalah kantor hukum terpercaya yang didedikasikan untuk menyediakan jasa hukum berkualitas tinggi bagi klien individu maupun korporasi. Didukung oleh tim advokat berpengalaman, kami mendampingi setiap kasus secara profesional dengan integritas yang tinggi.
                    </p>
                    <p class="text-gray-500 leading-relaxed">
                        Portal ini dirancang untuk mewujudkan efisiensi layanan hukum sejak tahap persiapan perkara. Klien dapat dengan aman mengirim dokumen perkara dan memesan waktu pertemuan secara real-time dari mana saja.
                    </p>
                </div>
                <div class="bg-white p-8 rounded-3xl border border-[#E2E8F0] shadow-sm flex flex-col justify-center space-y-4">
                    <span class="text-xs font-bold text-accent-blue uppercase tracking-wide">Hubungi Kami</span>
                    <span class="text-xl font-bold text-navy-dark">Kantor Pusat TNY Law Firm</span>
                    <div class="text-sm text-gray-500 space-y-2">
                        <p class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                            <span>Jakarta, Indonesia</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.94.725l.548 2.2a1 1 0 01-.321.988l-1.305.98a10.582 10.582 0 004.872 4.872l.98-1.305a1 1 0 01.988-.321l2.2.548a1 1 0 01.725.94V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                            </svg>
                            <span>+62 812-3456-7890</span>
                        </p>
                        <p class="flex items-center gap-2">
                            <svg class="h-4 w-4 text-accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                            </svg>
                            <span>info@tnylawfirm.com</span>
                        </p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-navy-dark text-gray-400 py-12 border-t border-[#1E293B]">
            <div class="max-w-7xl mx-auto px-6 lg:px-8 flex flex-col md:flex-row items-center justify-between gap-6">
                <span class="text-sm font-semibold text-white tracking-wider">TNY Law Firm</span>
                <span class="text-xs">&copy; 2026 Sistem Informasi Pra-Pendaftaran Perkara. Hak Cipta Dilindungi.</span>
            </div>
        </footer>
    </body>
</html>
