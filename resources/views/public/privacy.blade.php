<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Kebijakan Privasi - JriGPT</title>

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
  <header role="banner" class="h-16 flex items-center justify-between px-6 lg:px-12 border-b border-white/10 bg-[#0c1117]/80 backdrop-blur-md sticky top-0 z-50">
    <div class="flex items-center gap-3">
      <a href="{{ url('/') }}" class="size-8 rounded-lg bg-gradient-to-br from-emerald-500 to-emerald-700 grid place-items-center font-bold text-white shadow-lg shadow-emerald-900/50">JG</a>
      <a href="{{ url('/') }}" class="font-semibold tracking-tight text-white hover:text-emerald-400 transition">JriGPT</a>
      <span class="text-gray-600 hidden sm:inline-block">/</span>
      <span class="text-gray-400 text-sm font-medium hidden sm:inline-block tracking-wide">Pusat Privasi</span>
    </div>
    <div>
      <a href="{{ url()->previous() == url()->current() ? url('/') : url()->previous() }}" class="text-sm font-medium text-gray-300 hover:text-white bg-white/5 hover:bg-white/10 px-4 py-2 rounded-lg border border-white/10 transition">
        Kembali Utama
      </a>
    </div>
  </header>

  <!-- Hero Section -->
  <div class="relative bg-[#0c1117] border-b border-white/5 pt-20 pb-24 overflow-hidden">
    <!-- Abstract Glows -->
    <div class="absolute top-0 right-0 w-[600px] h-[600px] bg-blue-600/10 blur-[120px] rounded-full pointer-events-none translate-x-1/2 -translate-y-1/2"></div>
    <div class="absolute bottom-0 left-0 w-[400px] h-[400px] bg-emerald-600/10 blur-[100px] rounded-full pointer-events-none -translate-x-1/2 translate-y-1/2"></div>
    <div class="absolute inset-0 bg-dots opacity-30 mix-blend-overlay pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-6 lg:px-12 relative z-10 hidden sm:block">
      <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-blue-500/10 border border-blue-500/20 text-xs font-semibold text-blue-400 mb-6 uppercase tracking-wider">
        Keamanan Berpusat Pengguna
      </div>
      <h1 class="text-4xl lg:text-5xl xl:text-6xl font-extrabold text-white tracking-tight mb-6 leading-tight">
        Pedoman Pengelolaan <br />
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-blue-400 to-cyan-300">Kebijakan Privasi Global</span>
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
          <a href="#pengumpulan-data" class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">1. Kuantitas Koleksi Data</a>
          <a href="#pemanfaatan" class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">2. Operasional Pemanfaatan</a>
          <a href="#komputasi-eksternal" class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">3. Agensi Mesin Pihak Ketiga</a>
          <a href="#retensi" class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">4. Retensi & Sistem Keamanan</a>
          <a href="#korespondensi" class="text-sm text-gray-400 hover:text-blue-400 hover:bg-white/5 px-3 py-2 rounded-lg transition">5. Pusat Korespondensi</a>
        </nav>
      </div>
    </aside>

    <!-- Main Content -->
    <main class="flex-1 min-w-0">
      <div class="prose prose-invert prose-blue prose-lg max-w-3xl text-gray-300">
        <p class="lead text-xl text-gray-300 mb-12">
          Grup pengembangan perangkat lunak <strong>JriGPT</strong> beserta seluruh entitas afiliasinya menaruh penghormatan yang ekstrem atas perimeter privasi data Anda. Kebijakan Privasi yang diformulasikan ini mengartikulasikan peta metodologi mengenai ekskavasi, penyimpanan, isolasi, hingga kompartementalisasi jejak data profil Anda di kala mengeja interaksi layanan *Live Chat*, fitur komputasi mutakhir, maupun parameter konfigurasi VIP.
        </p>

        <section id="pengumpulan-data" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
          <h2 class="text-3xl font-bold text-white tracking-tight mb-6">1. Kuantitas Koleksi Data</h2>
          <p>
            Di tengah proses transisi menyuguhkan fasilitas komputasi AI kognitif yang memukau, instrumen pelacakan server JriGPT secara alamiah mengkoleksi agregasi lapisan informasi berikut dengan esensi moderasi minimum:
          </p>
          <ul class="space-y-4 mb-6">
            <li class="pl-2">
              <strong class="text-gray-200 block mb-1">Kredensi Primer:</strong>
              Terangkum di dalamnya susunan identifikasi absolut seperti surel (*e-mail*) yang terverifikasi dan sidik sandi termutasi kriptografi (*hashed cryptography*) dalam *database* relasional.
            </li>
            <li class="pl-2">
              <strong class="text-gray-200 block mb-1">Matriks Transmisi Obrolan (*Prompt History*):</strong>
              Secara konstan, tiap kalimat instruksi (*Chat Prompt*) yang diluncurkan menuju *server* beserta umpan balik balasan generatif (*AI Outputs*) akan dijilid di tabel sesi (*Session History*) guna memantik algoritma penalaran berkelanjutan dan mempertahankan hierarki UI percakapan.
            </li>
            <li class="pl-2">
              <strong class="text-gray-200 block mb-1">Telemetri Infrastruktural Dasar:</strong>
              Mengarsip atribut navigasi trivial seperti tanda baca *Internet Protocol* (*IP Address*) berskala anonim, arsitektur peramban interaksi, serta matriks frekuensi ketepatan interaksi GUI demi stabilitas diagnosa keramaian basis trafik.
            </li>
          </ul>
        </section>

        <section id="pemanfaatan" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
          <h2 class="text-3xl font-bold text-white tracking-tight mb-6">2. Operasional Pemanfaatan Terapan</h2>
          <p>
            Seluruh jejak komputasi yang direstorasi wajib dialokasikan di dalam batuk-batas hukum murni tanpa pretensi pemasaran (*Zero-Datalyst Marketing Trade*). Pemakaian data difokuskan total secara krusial demi:
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 my-8">
             <div class="bg-white/5 border border-white/10 rounded-xl p-5">
               <h4 class="text-white font-semibold mb-2">Presisi Mesin AI</h4>
               <p class="text-sm text-gray-400 m-0">Memompa efektivitas pembacaan skrip agar AI bisa menafsirkan variabel kode dan maksud perancangan yang diminta pengguna.</p>
             </div>
             <div class="bg-white/5 border border-white/10 rounded-xl p-5">
               <h4 class="text-white font-semibold mb-2">Resiliensi Server</h4>
               <p class="text-sm text-gray-400 m-0">Menyaring dan menambal potensi insiden peretasan masif, luapan jaringan DDoS (<em>Denial of Service</em>), maupun interupsi anomali lainnya.</p>
             </div>
          </div>
        </section>

        <section id="komputasi-eksternal" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
          <h2 class="text-3xl font-bold text-white tracking-tight mb-6">3. Kredibilitas Transmisi ke Agensi Pihak Ketiga</h2>
          <p>
            Jantung neuron artifisial JriGPT bernapas melalui integrasi API (*Application Programming Interface*) dengan utilitas berskalasi global (*LLM Groq/Llama Meta Core*). Segala baris perintah Anda akan dimutasikan temporer pada server mesin tersebut di awan. 
          </p>
          <div class="bg-blue-900/10 border-l-4 border-blue-500 rounded-r-xl p-6 my-6">
            <h4 class="text-blue-400 font-semibold mb-2 flex items-center gap-2">
              <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
              Sumpah Etika Model AI Bebas Pencemaran Pelatihan (*Training Deprivation Oaths*)
            </h4>
            <p class="text-sm text-gray-300 m-0">
              JriGPT secara mutlak mengatur perjanjian API komersial dimana <strong>penyedia AI Pihak Ketiga sepenuhnya dianulir dan diharamkan</strong> menggunakan, mengekstrak, atau menduplikasi satupun rekaman diskusi obrolan dan kode sintaks pribadi milik Anda sebagai bahan bakar pelestarian (*training datasets*) bagi Large Language Model asaliah mereka dalam kancah ranah korporat bebas.
            </p>
          </div>
        </section>

        <section id="retensi" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
          <h2 class="text-3xl font-bold text-white tracking-tight mb-6">4. Siklus Retensi dan Eksekusi Keamanan</h2>
          <p>
            Arsitektur basis data relasional kami diamankan di perimeter rahasia yang terkungkung konfigurasi mutakhir (<em>Transport Layer Security TLS 1.3</em>). Pengguna berhak mengeksekusi inisiatif sepihak untuk menebas eksistensi *database* riwayat mereka (`/delete/session`). Sinkronisasi ini akan langsung membakar riwayat bersangkutan (*Force-Nulling*). Namun, segmen data trivial telemetrik (arsip statistik) mungkin berada dalam antrian retensi teknis *log server* sebelum dihaluskan.
          </p>
        </section>

        <section id="korespondensi" class="scroll-mt-32 mb-16 border-t border-white/5 pt-10">
          <h2 class="text-3xl font-bold text-white tracking-tight mb-6">5. Pusat Korespondensi Legal</h2>
          <p>
            Silakan ajukan surat protes fungsional maupun formulasi pertanggungjawaban yuridis terkait regulasi data umum dunia yang berpotensi melintasi benak (*General Data Protection Regulation Rights*) melalui saluran korespondensi primer kami di jalur yang tersedia di wilayah *Dashboard* operasional secara interaktif.
          </p>
        </section>
      </div>

      <!-- Footer Context -->
      <footer class="mt-20 pt-8 border-t border-white/10 text-sm text-gray-500">
        <p>&copy; {{ date('Y') }} Platform Privasi Siber JriGPT | Dirangkum sesuai standardisasi protokol web.</p>
      </footer>
    </main>
  </div>
</body>

</html>