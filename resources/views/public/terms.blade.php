<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Ketentuan Layanan - JriGPT</title>

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
            class="absolute top-0 left-1/2 -translate-x-1/2 w-full max-w-lg h-60 bg-emerald-600/10 blur-[100px] rounded-full pointer-events-none -z-10">
        </div>

        <div class="mb-12 text-center">
            <h1 class="text-4xl lg:text-5xl font-extrabold text-white tracking-tight mb-4">Ketentuan Layanan</h1>
            <p class="text-gray-400">Pembaruan Terakhir: {{ date('j F Y') }}</p>
        </div>

        <article class="prose prose-invert prose-emerald max-w-none text-gray-300 leading-relaxed space-y-8">
            <p>
                Selamat datang di <strong>JriGPT</strong>. Dokumen ini (beserta seluruh dokumen yang disebutkan di
                dalamnya) memuat syarat dan ketentuan yang mengatur penggunaan Anda atas situs, produk, dan layanan
                cerdas kami ("Ketentuan Layanan").
            </p>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">1. Penerimaan Ketentuan
                </h2>
                <p>Dengan mengakses atau menggunakan platform JriGPT, Anda mengonfirmasi bahwa Anda menerima Ketentuan
                    ini dan setuju untuk mematuhinya. Jika Anda tidak setuju, mohon untuk tidak menggunakan layanan
                    kami.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">2. Penggunaan Model
                    Berbasis AI</h2>
                <p>Layanan utama kami menyediakan akses ke keluaran, hasil generasi teks, analisis data, atau modul
                    komputasi cerdas (disebut sebagai "Keluaran AI").
                    Keluaran AI disediakan "sebagaimana adanya" (<em>as is</em>). JriGPT tidak bertanggung jawab atas
                    keakuratan, orisinalitas, atau implikasi hukum dari hasil yang diberikan oleh mesin kepada Anda.</p>
                <ul>
                    <li>Anda tidak diperkenankan menggunakan AI untuk menghasilkan konten ilegal, berbahaya, ancaman,
                        kekerasan, pelanggaran hak cipta, atau yang secara sah melanggar nilai-nilai etis.</li>
                    <li>Anda menanggung segala bentuk risiko dari kode (<em>source code</em>) atau instruksi
                        (<em>prompt</em>) yang dihasilkan oleh JriGPT dan dieksekusi di ranah publik atau korporat Anda.
                    </li>
                </ul>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">3. Akun Pengguna /
                    Anggota VIP</h2>
                <p>Jika Anda memilih atau diberi akun Anggota VIP berserta kata sandinya, Anda setuju untuk menjaga
                    kerahasiaan materi informasi tersebut. Akses VIP diperuntukkan semata-mata untuk individu yang
                    terdaftar dan tidak dapat dialihkan. Kami berhak menangguhkan akun jika kami memverifikasi adanya
                    pencederaan terhadap tata laksana ini.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">4. Pembatasan Tanggung
                    Jawab</h2>
                <p>Sampai pada batas maksimal yang diizinkan oleh hukum yang berlaku, JriGPT, afiliasinya, dan pemberi
                    lisensinya tidak bertanggung jawab atas segala kerusakan tidak langsung, insidental, atau logis,
                    termasuk namun tidak terbatas pada kehilangan keuntungan, data, atau integritas perangkat akibat
                    kendala teknis dari sistem.</p>
            </section>

            <section>
                <h2 class="text-2xl font-semibold text-white mb-4 border-b border-white/10 pb-2">5. Pembaruan Dokumen
                </h2>
                <p>Kami dapat mengubah Syarat dan Ketentuan Layanan sewaktu-waktu tanpa pemberitahuan mutlak selain
                    merevisi halaman ini. Diharapkan Anda sering mengunjungi halaman ini untuk mempelajari pembaruan
                    lebih lanjut.</p>
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