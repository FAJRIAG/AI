<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kebijakan Privasi - JriGPT</title>
    <link rel="icon" type="image/svg+xml"
        href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23059669'/><text x='50%25' y='50%25' font-size='40' font-weight='bold' fill='white' font-family='Arial, sans-serif' text-anchor='middle' dominant-baseline='central'>JG</text></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .bg-dots {
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="min-h-screen bg-[#06080b] text-gray-100 antialiased font-sans selection:bg-blue-500/30">
    <!-- Top Navigation (Sticky) -->
    <header role="banner"
        class="h-16 flex items-center justify-between px-6 lg:px-12 border-b border-white/10 bg-[#0c1117]/80 backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}"
                class="size-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 grid place-items-center font-bold text-white shadow-lg shadow-emerald-900/50">JG</a>
            <a href="{{ url('/') }}"
                class="font-semibold tracking-tight text-white hover:text-emerald-400 transition">JriGPT</a>
            <span class="text-gray-600 hidden sm:inline-block">/</span>
            <span class="text-gray-400 text-sm font-medium hidden sm:inline-block tracking-wide">Pusat Privasi</span>
        </div>
        <div>
            <a href="{{ url()->previous() == url()->current() ? url('/') : url()->previous() }}"
                class="text-sm font-medium text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg border border-white/10 transition">
                Kembali Utama
            </a>
        </div>
    </header>

    <!-- Hero Section -->
    <div class="relative bg-[#0c1117] border-b border-white/5 pt-20 pb-24 overflow-hidden">
        <!-- Abstract Glows -->
        <div
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-600/10 blur-[120px] rounded-full pointer-events-none translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-emerald-600/10 blur-[100px] rounded-full pointer-events-none -translate-x-1/2 translate-y-1/2">
        </div>
        <div class="absolute inset-0 bg-dots opacity-30 mix-blend-overlay pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-12 relative z-10 hidden sm:block">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-xs font-semibold text-blue-400 mb-6 uppercase tracking-wider">
                Keamanan Berpusat Pengguna
            </div>
            <h1 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white tracking-tight mb-6 leading-tight">
                Pedoman Pengelolaan <br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Kebijakan Privasi
                    Global</span>
            </h1>
            <p class="text-lg text-gray-400 max-w-2xl leading-relaxed">
                Efektif Berlaku Sejak: <strong class="text-gray-200">{{ date('j F Y') }}</strong>
            </p>
        </div>
    </div>

    <!-- Mobile Hero (Compact) -->
    <div class="sm:hidden px-6 pt-10 pb-8 text-center bg-[#0c1117] border-b border-white/5">
        <h1 class="text-3xl font-bold text-white mb-2">Kebijakan Privasi</h1>
        <p class="text-sm text-gray-400">Efektif: {{ date('j F Y') }}</p>
    </div>

    <!-- Main Layout -->
    <div class="max-w-7xl mx-auto px-6 lg:px-12 py-12 lg:py-20 flex flex-col lg:flex-row gap-12 lg:gap-24 relative">

        <!-- Sidebar Navigation (Desktop) -->
        <aside class="hidden lg:block w-64 shrink-0">
            <div class="sticky top-28 space-y-1">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 px-3">Daftar Isi</h3>
                <nav class="flex flex-col space-y-1" aria-label="Table of Contents">
                    <a href="#pengumpulan-data"
                        class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">1.
                        Kuantitas Koleksi Data</a>
                    <a href="#pemanfaatan"
                        class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">2.
                        Operasional Pemanfaatan</a>
                    <a href="#komputasi-eksternal"
                        class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">3.
                        Agensi Mesin Pihak Ketiga</a>
                    <a href="#retensi"
                        class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">4.
                        Retensi & Sistem Keamanan</a>
                    <a href="#korespondensi"
                        class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">5.
                        Pusat Korespondensi</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0">
            <div class="prose prose-invert prose-blue prose-lg max-w-3xl text-gray-300">
                <p class="lead text-xl text-gray-300 mb-12">
                    Tim pengembang <strong>JriGPT</strong> sangat menghargai privasi data Anda. Kebijakan Privasi ini
                    menjelaskan bagaimana kami mengumpulkan, menggunakan, melindungi, dan menyebarkan informasi pribadi
                    Anda ketika berinteraksi dengan layanan obrolan, komputasi cerdas, maupun penggunaan akses VIP kami.
                </p>

                <section id="pengumpulan-data" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">1. Kuantitas Koleksi Data</h2>
                    <p>
                        Dalam menyediakan layanan AI cerdas kami, JriGPT perlu mengumpulkan beberapa informasi dasar
                        pengguna, yang meliputi:
                    </p>
                    <ul class="space-y-4 mb-6">
                        <li class="pl-2">
                            <strong class="text-gray-200 block mb-1">Informasi Akun:</strong>
                            Data yang Anda berikan secara langsung, seperti alamat email (*) saat pendaftaran,
                            serta kata sandi yang disimpan dalam format terenkripsi (*hashed*).
                        </li>
                        <li class="pl-2">
                            <strong class="text-gray-200 block mb-1">Riwayat Interaksi (Log Percakapan):</strong>
                            Teks perintah (prompt) yang Anda masukkan serta tanggapan yang dihasilkan oleh AI
                            akan disimpan sementara untuk mempertahankan konteks dalam sesi percakapan Anda.
                        </li>
                        <li class="pl-2">
                            <strong class="text-gray-200 block mb-1">Data Perangkat & Akses:</strong>
                            Pengumpulan log server standar seperti alamat Protokol Internet (IP), jenis browser,
                            dan waktu akses guna memantau stabilitas jaringan dan mencegah penyalahgunaan.
                        </li>
                    </ul>
                </section>

                <section id="pemanfaatan" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">2. Operasional Pemanfaatan Terapan
                    </h2>
                    <p>
                        Informasi yang kami kumpulkan akan kami operasikan secara berhati-hati sesuai batas hukum
                        yang berlaku. Kami tidak menjual rekam jejak Anda kepada pihak periklanan. Penggunaan data
                        difokuskan hanya untuk:
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-8">
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                            <h4 class="text-white font-semibold mb-2">Penyesuaian Model AI</h4>
                            <p class="text-sm text-gray-400 m-0">Memastikan balasan AI dapat menafsirkan konteks
                                pembicaraan dan permintaan Anda dengan lebih presisi.</p>
                        </div>
                        <div class="bg-white/5 border border-white/10 rounded-xl p-5">
                            <h4 class="text-white font-semibold mb-2">Keamanan & Keandalan</h4>
                            <p class="text-sm text-gray-400 m-0">Menganalisis anomali pada tingkat penggunaan,
                                menambal kerentanan jaringan, serta mitigasi serangan DDoS.</p>
                        </div>
                    </div>
                </section>

                <section id="komputasi-eksternal" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">3. Kredibilitas Transmisi ke Pihak
                        Ketiga</h2>
                    <p>
                        Layanan JriGPT dibangun menggunakan integrasi API dengan model bahasa besar (*Large Language
                        Models*) dari pihak ketiga secara awan (*cloud computation*). Perintah kueri yang Anda ajukan
                        dapat diproses pada server eksternal ini untuk memberikan balasan *generative* yang tepat.
                    </p>
                    <div class="bg-blue-900/10 border-l-4 border-blue-500 rounded-r-xl p-6 my-6">
                        <h4 class="text-blue-400 font-semibold mb-2 flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            Persetujuan Model Bebas Pelatihan (*Zero Data Retention*)
                        </h4>
                        <p class="text-sm text-gray-300 m-0">
                            Kami memastikan bahwa penyedia API pihak ketiga yang kami gunakan diwajibkan oleh perjanjian
                            untuk <strong>tidak menggunakan rekaman percakapan Anda maupun data internal sistem
                                lainnya</strong>
                            sebagai basis data pelatihan (*training datasets*) pemodelan AI mereka yang bersifat publik.
                        </p>
                    </div>
                </section>

                <section id="retensi" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">4. Siklus Retensi dan Keamanan Data
                    </h2>
                    <p>
                        Basis data kami dilindungi menggunakan standar enkripsi terkini (<em>Transport Layer Security
                            TLS 1.3</em>).
                        Anda memiliki kendali penuh terhadap sesi obrolan Anda dan dapat menghapus riwayat tersebut.
                        Saat sebuah
                        sesi dihapus, rekaman percakapan akan langsung ditiadakan dari sistem basis data.
                        Namun, data diagnostik dan agregat teknis pada *server logs* mungkin dipertahankan selama
                        periode tertentu
                        guna mematuhi prosedur operasional.
                    </p>
                </section>

                <section id="korespondensi" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">5. Pusat Bantuan & Pertanyaan Privasi</h2>
                    <p>
                        Apabila Anda memiliki pertanyaan lebih lanjut, kerahasiaan terkait data, atau ingin mengajukan 
                        permintaan peninjauan hak pengguna Anda (*General Data Protection*), silakan hubungi tim layanan 
                        bantuan kami melalui saluran resmi yang tersedia di *Dashboard* akun Anda.
                    </p>
                </section>
            </div>

        </main>
    </div>

    <!-- Footer Context -->
    <footer class="w-full border-t border-white/10 bg-[#0c1117] text-sm text-gray-500 mt-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 pt-8 pb-12 sm:pb-8">
            <p class="leading-relaxed">&copy; {{ date('Y') }} Platform Privasi Siber JriGPT <span
                    class="hidden sm:inline">|</span><br class="sm:hidden" /> Dirangkum sesuai standardisasi
                protokol web.</p>
        </div>
    </footer>
</body>

</html>