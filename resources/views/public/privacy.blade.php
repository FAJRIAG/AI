<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kebijakan Privasi - JriGPT</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-[#06080b] text-gray-100 antialiased overflow-y-auto">
    <!-- Top Bar -->
    <header role="banner"
        class="h-14 flex items-center px-6 border-b border-white/10 bg-[#0c1117] sticky top-0 z-50 backdrop-blur-md bg-opacity-80">
        <div class="flex items-center gap-3">
            <a href="{{ url('/') }}"
                class="size-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 grid place-items-center font-bold text-white shadow-lg shadow-emerald-900/50">JG</a>
            <a href="{{ url('/') }}"
                class="font-semibold tracking-tight text-white hover:text-emerald-400 transition">JriGPT</a>
        </div>
    </header>

    <!-- Content -->
    <main class="max-w-3xl mx-auto px-6 py-12 lg:py-20 flex-1 relative">

        <!-- Decorative Glow -->
        <div
            class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-lg h-60 bg-blue-600/10 blur-[100px] rounded-full pointer-events-none -z-10">
        </div>

        <div class="mb-12 text-center">
            <h1 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-4">Kebijakan Privasi</h1>
            <p class="text-gray-400">Efektif Sejak: {{ date('j F Y') }}</p>
        </div>

        <article class="prose prose-invert prose-emerald max-w-none text-gray-300 leading-relaxed space-y-8">
            <p>
                <strong>JriGPT</strong> dan afiliasinya menghormati wilayah privasi data Anda. Kebijakan ini
                mengartikulasikan cara kami menyimpan, menggunakan, mengamankan, dan tidak mendistribusikan secara asal
                akan jejak data pribadi Anda saat menggunakan aplikasi Obrolan (Chat), Profiling (VIP), dan modul
                terkait ("Layanan").
            </p>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">1. Data yang Kami
                    Kumpulkan</h2>
                <p>Dalam menjalankan operasional fungsional AI, sistem JriGPT menginisiasi pencatatan jenis data
                    berikut:</p>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li><strong>Informasi Autentikasi:</strong> Termasuk nama, kata sandi terenkripsi (<em>hashed</em>),
                        dan alamat surel ketika Anda membuat akun Anggota VIP.</li>
                    <li><strong>Riwayat Percakapan (Prompt History):</strong> Sesi percakapan, instruksi kode mandiri
                        (<em>prompts</em>), dan hasil jawaban dikumpulkan untuk menyediakan histori <em>chat</em> dan
                        konteks antrian model (LLM).</li>
                    <li><strong>Data Navigasi (Log & Metrik):</strong> Pola perangkat, alamat IP yang direduksi,
                        referensi masuk, atau klik interaksi komponen UI pada level dasar demi diagnosis server.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">2. Bagaimana Kami
                    Menggunakan Informasi Anda</h2>
                <p>Pada prinsipnya rekam data akan dialokasikan hanya untuk keperluan yang menunjang eksistensi aplikasi
                    ini secara rasional:</p>
                <ul class="list-disc pl-5 mt-2 space-y-1">
                    <li>Merespons rentetan pesan teknis atau pertanyaan cerdas Anda (<em>Prompt Execution</em>).</li>
                    <li>Mengaktifkan keamanan infrastruktur terhadap sirkulasi <em>robot</em>, interupsi jaringan, atau
                        ancaman <em>malware</em>.</li>
                    <li>Mengomunikasikan pesan administratif seperti rincian perbaruan, insiden keamanan, dan/atau
                        laporan sesi pembaruan sistem yang genting.</li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">3. Berbagi Konten
                    dengan Mesin AI Eksternal</h2>
                <p>JriGPT pada poros vitalnya dikuasakan oleh pendelegasian struktur API (<em>Application Programming
                        Interface</em>) kepada pihak ketiga berskalasi tinggi, contohnya layanan perantara komputasi
                    <em>Groq</em> maupun model bahasa <em>Llama</em>.</p>
                <p>Sangat krusial untuk dipahami bahwa, dalam momen eksekusi kueri langsung bersama AI, instruksi
                    tekstual obrolan Anda diproses dan ditransfer melalui mesin komputasi terkait. Kendati begitu, kami
                    <strong>tidak secara sengaja memberikan persetujuan kepada penyedia model</strong> ini untuk
                    meletakkan masukan pribadi Anda ke dalam kumpulan matriks pelatihan <em>Large Language Models</em>
                    (LLMs) global mereka.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">4. Retensi dan Keamanan
                    Data</h2>
                <p>Informasi kredensial ditransmisikan menggunakan pelindung SSL tingkat terkini yang diotorisasi, dan
                    basis logis basis data secara konstan terproteksi perimeternya di server terbatas. Anda dapat
                    menghapus data atau menonaktifkan akun sewaktu-waktu; namun, sebagian data <em>log session</em>
                    analitis minimal (non-indentitas absolut) berpotensi terkunci memori sistem sampai batas
                    kadaluarsanya.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">5. Kontak Privasi</h2>
                <p>Bila memiliki keluhan operasional dan/atau menuntut klarifikasi ekstra seputar hak privasi (contoh:
                    subjek data <em>General Data Protection Regulation</em>), silakan mengajukan surel pelaporan
                    eksklusif kepada Administrasi JriGPT di direktori terdekat
                    (<em>administrator@domain-tujuan.ai</em>).</p>
            </section>
        </article>

        <div class="mt-16 pt-8 border-t border-white/10 text-center">
            <a href="{{ url()->previous() == url()->current() ? url('/') : url()->previous() }}"
                class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-white/5 hover:bg-white/10 text-white font-medium transition-all">
                <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7 7-7M3 12h18"></path>
                </svg>
                Kembali ke Halaman Sebelumnya
            </a>
        </div>

    </main>

    <footer class="border-t border-white/10 bg-[#0c1117] py-6 text-center text-sm text-gray-500">
        <p>&copy; {{ date('Y') }} JriGPT. Seluruh Hak Cipta Dilindungi.</p>
    </footer>
</body>

</html>