<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ketentuan Layanan - JriGPT</title>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23059669'/><text x='50%25' y='50%25' font-size='40' font-weight='bold' fill='white' font-family='Arial, sans-serif' text-anchor='middle' dominant-baseline='central'>JG</text></svg>">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Subtle dot pattern for the premium header feel */
        .bg-dots {
            background-image: radial-gradient(rgba(255, 255, 255, 0.1) 1px, transparent 1px);
            background-size: 24px 24px;
        }
    </style>
</head>

<body class="min-h-screen bg-[#06080b] text-gray-100 antialiased font-sans selection:bg-emerald-500/30">
    <!-- Top Navigation (Sticky) -->
    <header role="banner"
        class="h-16 flex items-center justify-between px-6 lg:px-12 border-b border-white/10 bg-[#0c1117]/80 backdrop-blur-md sticky top-0 z-50">
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}"
                class="size-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 grid place-items-center font-bold text-white shadow-lg shadow-emerald-900/50">JG</a>
            <a href="{{ url('/') }}"
                class="font-semibold tracking-tight text-white hover:text-emerald-400 transition">JriGPT</a>
            <span class="text-gray-600 hidden sm:inline-block">/</span>
            <span class="text-gray-400 text-sm font-medium hidden sm:inline-block tracking-wide">Pusat Hukum</span>
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
            class="absolute top-0 right-0 w-[600px] h-[600px] bg-emerald-600/10 blur-[120px] rounded-full pointer-events-none translate-x-1/2 -translate-y-1/2">
        </div>
        <div
            class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-teal-600/10 blur-[100px] rounded-full pointer-events-none -translate-x-1/2 translate-y-1/2">
        </div>
        <div class="absolute inset-0 bg-dots opacity-30 mix-blend-overlay pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-6 lg:px-12 relative z-10 hidden sm:block">
            <div
                class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-xs font-semibold text-emerald-400 mb-6 uppercase tracking-wider">
                Dokumentasi Berlaku
            </div>
            <h1 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white tracking-tight mb-6 leading-tight">
                Syarat dan Ketentuan <br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">Penggunaan
                    Layanan</span>
            </h1>
            <p class="text-lg text-gray-400 max-w-2xl leading-relaxed">
                Pembaruan Terakhir: <strong class="text-gray-200">{{ date('j F Y') }}</strong>
            </p>
        </div>
    </div>

    <!-- Mobile Hero (Compact) -->
    <div class="sm:hidden px-6 pt-10 pb-8 text-center bg-[#0c1117] border-b border-white/5">
        <h1 class="text-3xl font-bold text-white mb-2">Syarat & Ketentuan</h1>
        <p class="text-sm text-gray-400">Pembaruan: {{ date('j F Y') }}</p>
    </div>

    <!-- Main Layout -->
    <div class="max-w-7xl mx-auto px-6 lg:px-12 py-12 lg:py-20 flex flex-col lg:flex-row gap-12 lg:gap-24 relative">

        <!-- Sidebar Navigation (Desktop) -->
        <aside class="hidden lg:block w-64 shrink-0">
            <div class="sticky top-28 space-y-1">
                <h3 class="text-xs font-bold text-gray-500 uppercase tracking-widest mb-4 px-3">Daftar Isi</h3>
                <nav class="flex flex-col space-y-1" aria-label="Table of Contents">
                    <a href="#penerimaan"
                        class="text-sm text-gray-400 hover:text-emerald-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">1.
                        Penerimaan Ketentuan</a>
                    <a href="#model-ai"
                        class="text-sm text-gray-400 hover:text-emerald-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">2.
                        Layanan Berbasis AI</a>
                    <a href="#akun"
                        class="text-sm text-gray-400 hover:text-emerald-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">3.
                        Akun Pengguna & VIP</a>
                    <a href="#kepatuhan-hukum"
                        class="text-sm text-gray-400 hover:text-emerald-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">4.
                        Batasan Tanggung Jawab</a>
                    <a href="#pembaruan"
                        class="text-sm text-gray-400 hover:text-emerald-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">5.
                        Amandemen Dokumen</a>
                </nav>
            </div>
        </aside>

        <!-- Main Content -->
        <main class="flex-1 min-w-0">
            <div class="prose prose-invert prose-emerald prose-lg max-w-3xl text-gray-300">
                <p class="lead text-xl text-gray-300 mb-12">
                    Selamat datang di platform JriGPT. Dokumen mengikat ini memuat syarat dan ketentuan tata tertib yang
                    mengatur penggunaan fungsional Anda atas situs, integrasi antarmuka, dan layanan komputasi cerdas
                    kami.
                </p>

                <section id="penerimaan" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">1. Penerimaan Ketentuan</h2>
                    <p>
                        Dengan menginisiasi koneksi jaringan, mengakses, atau secara aktif menggunakan platform JriGPT,
                        Anda menyatakan konfirmasi absolut bahwa Anda menerima regulasi ini secara utuh dan mengikatkan
                        diri ke dalamnya secara hukum. Jika Anda berhalangan dalam menyetujui parameter ini, mohon agar
                        Anda menghentikan pemakaian fasilitas (*terminate usage*) seketika.
                    </p>
                </section>

                <section id="model-ai" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">2. Pemakaian Model Generatif AI</h2>
                    <p>
                        Prinsip operasi layanan kami adalah menyediakan kueri langsung terhadap ekosistem komputasi
                        kecerdasan buatan (*Large Language Models*)—menghasilkan teks, logika skrip, respons analitis,
                        hingga komponen antarmuka yang didefinisikan secara kolektif sebagai "Keluaran Mesin".
                    </p>
                    <div class="bg-blue-900/10 border-l-4 border-blue-500 rounded-r-xl p-6 my-8">
                        <h4 class="text-blue-400 font-semibold mb-2">Penafian Akurasi Otomatis</h4>
                        <p class="text-sm text-gray-300 m-0">
                            Setiap Keluaran Mesin bersifat asimtotik dan diberikan secara "apa adanya" (*as is*).
                            Entitas JriGPT tidak menanggung garansi terhadap kebenaran absolut, kepemilikan orisinal hak
                            cipta, atau kemanjuran logika pemrograman (misal: kode yang rentan serangan siber) dari
                            Keluaran tersebut.
                        </p>
                    </div>
                    <p>Selaku eksekutor akhir, pengguna **DILARANG KERAS** mendikte model bahasa kami guna mensintesis:
                    </p>
                    <ul class="space-y-2 mb-6">
                        <li><strong class="text-gray-200">Eksploitasi Kriminal:</strong> Termasuk skrip *malware*,
                            peretasan korporat terenkripsi, atau penipuan jaringan ganda (*phishing*).</li>
                        <li><strong class="text-gray-200">Konten Intimidasi:</strong> Narasi teror, ujaran kebencian
                            rasial/politis yang ekstrem, atau manipulasi kekerasan.</li>
                        <li><strong class="text-gray-200">Peretasan Moral:</strong> Pembangkitan material eksplisit yang
                            dilarang undang-undang domestik maupun internasional.</li>
                    </ul>
                </section>

                <section id="akun" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">3. Kredensial Akun & Akses VIP</h2>
                    <p>
                        Manakala Anda mendaftarkan profil pengguna (*User Account*) atau diberikan mandataris
                        keanggotaan VIP, Anda tunduk pada kewajiban perlindungan ganda: menjaga kerahasiaan token sesi
                        (<em>password/api keys</em>) serta membatasi pertukaran otorisasi Anda. Hak privilese berjalur
                        VIP bersifat eksklusif untuk perseorangan terkait dan diharamkan untuk disewakan massal secara
                        ilegal.
                    </p>
                </section>

                <section id="kepatuhan-hukum" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">4. Pembatasan Tanggung Jawab</h2>
                    <p>
                        Sampai melintasi batas ekstrem yuridis, pihak operasional JriGPT, insinyur penyusun, dan
                        kolaborator ketiga (contoh: lisensi server) dinyatakan kebal dari tuntutan material tak
                        berwujud, insidental yang membengkak, maupun efek komersial logis yang muncul—baik berupa
                        lenyapnya daya komputasi (*server downtime*), hilangnya kapital/data pelanggan Anda, hingga
                        sabotase *interface* imbas kegagalan penolakan sistem.
                    </p>
                </section>

                <section id="pembaruan" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
                    <h2 class="text-3xl font-bold text-white tracking-tight mb-6">5. Keputusan dan Amandemen Dokumen
                    </h2>
                    <p>
                        Kami membawahi wewenang sepihak (*unilateral discretion*) untuk mengevaluasi, menambahkan, atau
                        membongkar poin-poin krusial dari Syarat Layanan ini seiring berjalannya dinamika teknologi dan
                        rezim regulasi. Peringatan sekunder di luar halaman web tidak selalu dijamin; dengan itu,
                        durabilitas persetujuan fungsional Anda mematenkan perlunya peninjauan proaktif atas halaman
                        dokumentasi ini.
                    </p>
                </section>
            </div>

        </main>
    </div>

    <!-- Footer Context -->
    <footer class="w-full border-t border-white/10 bg-[#0c1117] text-sm text-gray-500 mt-12">
        <div class="max-w-7xl mx-auto px-6 lg:px-12 pt-8 pb-12 sm:pb-8">
            <p class="leading-relaxed">&copy; {{ date('Y') }} Platform Kecerdasan Buatan Terpadu JriGPT <span
                    class="hidden sm:inline">|</span><br class="sm:hidden" /> Seluruh rumusan korporat sah dan
                dilindungi.</p>
        </div>
    </footer>
</body>

</html>
