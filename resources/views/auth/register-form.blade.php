{{-- resources/views/auth/register-form.blade.php --}}
<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Daftar Member VIP - VIP Enterprise</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="{{ Vite::asset('resources/css/public-chat.css') }}">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="min-h-screen md:h-screen md:overflow-hidden bg-zinc-950 text-zinc-100 antialiased font-sans flex flex-col md:flex-row selection:bg-zinc-800 selection:text-zinc-100" style="font-family: 'Inter', sans-serif;">
  
  <!-- Left Side: Branding & Feature Showcase (Hidden on mobile) -->
  <div class="hidden lg:flex lg:w-[55%] xl:w-[60%] flex-col justify-between p-8 lg:p-12 xl:p-16 relative overflow-hidden bg-[#050505] border-r border-white/5">
    <!-- Abstract Background Artifacts -->
    <div class="absolute top-[-20%] left-[-10%] w-[70%] h-[70%] bg-emerald-600/10 blur-[120px] rounded-full pointer-events-none"></div>
    <div class="absolute bottom-[-10%] right-[-10%] w-[50%] h-[50%] bg-emerald-900/10 blur-[100px] rounded-full pointer-events-none"></div>
    
    <!-- Fine Grid Background -->
    <div class="absolute inset-0 opacity-[0.02] pointer-events-none" style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>

    <!-- Header / Logo -->
    <div class="relative z-10 flex items-center justify-between w-full">
        <div class="flex items-center gap-3">
          <div class="size-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 grid place-items-center shadow-[0_0_20px_rgba(16,185,129,0.3)] border border-emerald-400/20">
            <span class="text-white text-lg font-black tracking-tighter">JG</span>
          </div>
          <a href="{{ url('/') }}" class="text-2xl font-bold tracking-tight text-white hover:text-emerald-400 transition-colors">JriGPT<span class="text-emerald-500">.</span></a>
        </div>
        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-emerald-500/10 border border-emerald-500/20 text-xs font-bold text-emerald-400 tracking-wide uppercase">
          <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)] animate-pulse"></span>
          Enterprise Access
        </div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 max-w-2xl mt-12 mb-10">
      <h1 class="text-4xl xl:text-5xl font-semibold text-white leading-[1.15] tracking-tight mb-4">
        Unlock your true <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">potential.</span>
      </h1>
      <p class="text-zinc-400 text-base xl:text-lg leading-relaxed font-light">
        Bergabung sekarang dan dapatkan keistimewaan tak tertandingi dengan akses infrastruktur AI Agentic terbaik kami untuk Anda dan tim korporat Anda.
      </p>
    </div>

    <!-- Premium Feature Grid -->
    <div class="relative z-10 grid grid-cols-1 xl:grid-cols-2 gap-4 lg:gap-6 mt-auto">
        <!-- Feature 1 -->
        <div class="group relative bg-[#0c0c0e]/80 backdrop-blur-xl rounded-2xl border border-white/5 p-6 hover:bg-[#111115] hover:border-emerald-500/30 transition-all duration-500 overflow-hidden cursor-default shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10 flex items-start gap-4">
                <div class="p-3 bg-white/5 rounded-xl border border-white/10 group-hover:bg-emerald-500/10 group-hover:border-emerald-500/30 transition-colors">
                    <svg class="size-6 text-zinc-400 group-hover:text-emerald-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9"></path></svg>
                </div>
                <div>
                    <h3 class="text-zinc-100 font-semibold text-lg mb-1 group-hover:text-emerald-300 transition-colors">Autonomous Web Agent</h3>
                    <p class="text-sm text-zinc-500 group-hover:text-zinc-400 transition-colors leading-relaxed">Arsitektur ReAct cerdas. Mampu menyusun rencana dan meriset informasi lintas web secara mendalam dan otonom.</p>
                </div>
            </div>
        </div>
        <!-- Feature 2 -->
        <div class="group relative bg-[#0c0c0e]/80 backdrop-blur-xl rounded-2xl border border-white/5 p-6 hover:bg-[#111115] hover:border-emerald-500/30 transition-all duration-500 overflow-hidden cursor-default shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10 flex items-start gap-4">
                <div class="p-3 bg-white/5 rounded-xl border border-white/10 group-hover:bg-emerald-500/10 group-hover:border-emerald-500/30 transition-colors">
                    <svg class="size-6 text-zinc-400 group-hover:text-emerald-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path></svg>
                </div>
                <div>
                    <h3 class="text-zinc-100 font-semibold text-lg mb-1 group-hover:text-emerald-300 transition-colors">Artifacts Studio 2.0</h3>
                    <p class="text-sm text-zinc-500 group-hover:text-zinc-400 transition-colors leading-relaxed">Render kode HTML/CSS, visualisasi instruksi, dan diagram Mermaid instan dalam panel *preview* interaktif UI.</p>
                </div>
            </div>
        </div>
        <!-- Feature 3 -->
        <div class="group relative bg-[#0c0c0e]/80 backdrop-blur-xl rounded-2xl border border-white/5 p-6 hover:bg-[#111115] hover:border-emerald-500/30 transition-all duration-500 overflow-hidden cursor-default shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10 flex items-start gap-4">
                <div class="p-3 bg-white/5 rounded-xl border border-white/10 group-hover:bg-emerald-500/10 group-hover:border-emerald-500/30 transition-colors">
                    <svg class="size-6 text-zinc-400 group-hover:text-emerald-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                </div>
                <div>
                    <h3 class="text-zinc-100 font-semibold text-lg mb-1 group-hover:text-emerald-300 transition-colors">True RAG & Workspaces</h3>
                    <p class="text-sm text-zinc-500 group-hover:text-zinc-400 transition-colors leading-relaxed">Memori sistem tiada batas. JriGPT memahami puluhan kode, PDF, dan sejarah komunikasi proyek Anda lewat *Embeddings*.</p>
                </div>
            </div>
        </div>
        <!-- Feature 4 -->
        <div class="group relative bg-[#0c0c0e]/80 backdrop-blur-xl rounded-2xl border border-white/5 p-6 hover:bg-[#111115] hover:border-emerald-500/30 transition-all duration-500 overflow-hidden cursor-default shadow-lg">
            <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500"></div>
            <div class="relative z-10 flex items-start gap-4">
                <div class="p-3 bg-white/5 rounded-xl border border-white/10 group-hover:bg-emerald-500/10 group-hover:border-emerald-500/30 transition-colors">
                    <svg class="size-6 text-zinc-400 group-hover:text-emerald-400 transition-colors" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path></svg>
                </div>
                <div>
                    <h3 class="text-zinc-100 font-semibold text-lg mb-1 group-hover:text-emerald-300 transition-colors">Emotional Tone Sync</h3>
                    <p class="text-sm text-zinc-500 group-hover:text-zinc-400 transition-colors leading-relaxed">Kecerdasan emosional real-time. UI dan gaya bahasa AI akan secara otomatis beradaptasi dengan *mood* dan urgensi percakapan.</p>
                </div>
            </div>
        </div>
    </div>
  </div>

  <!-- Right Side: Register Form -->
  <main class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 py-8 relative bg-[#050505] lg:bg-[#0a0a0c] w-full min-h-screen lg:min-h-0 lg:h-screen lg:overflow-y-auto">
    <!-- Mobile header -->
    <div class="lg:hidden flex items-center justify-center gap-3 mb-10 w-full max-w-md relative z-10">
      <div class="size-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 grid place-items-center shadow-lg border border-emerald-400/20">
        <span class="text-white text-lg font-black tracking-tighter">JG</span>
      </div>
      <a href="{{ url('/') }}" class="text-2xl font-bold tracking-tight text-white focus:outline-none">JriGPT<span class="text-emerald-500">.</span></a>
    </div>

    <div class="w-full max-w-md bg-[#0c0c0e] lg:bg-transparent p-8 sm:p-10 border border-white/5 lg:border-none shadow-2xl lg:shadow-none rounded-3xl lg:rounded-none relative z-10">
      <div class="mb-10 text-center lg:text-left">
        <h2 class="text-3xl font-semibold text-white tracking-tight mb-3">Daftar Akun VIP</h2>
        <p class="text-sm text-zinc-400 font-light">
          @if(request('plan') == 'pro')
            Anda memilih <strong class="text-emerald-400 font-medium">Paket Pro</strong>. Silakan lengkapi identitas Enterprise Anda.
          @else
            Lengkapi kredensial untuk mendaftar ekosistem AI JriGPT.
          @endif
        </p>
      </div>

      <form method="POST" action="{{ route('register') }}" class="space-y-6">
        @csrf
        @if(request('plan'))
          <input type="hidden" name="plan" value="{{ request('plan') }}">
        @endif

        {{-- Name --}}
        <div class="space-y-2 group/input">
          <label for="name" class="block text-sm font-medium text-zinc-400 transition-colors group-focus-within/input:text-emerald-400">Nama Lengkap</label>
          <div class="relative">
            <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
              class="w-full rounded-xl bg-zinc-900/50 border border-white/10 px-4 py-3.5 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all shadow-inner"
              placeholder="Jhon Doe">
          </div>
          @error('name')
            <p class="mt-1.5 text-xs text-red-500/90 flex items-center gap-1.5 font-medium">
              <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Email --}}
        <div class="space-y-2 group/input">
          <label for="email" class="block text-sm font-medium text-zinc-400 transition-colors group-focus-within/input:text-emerald-400">Email Enterprise</label>
          <div class="relative">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required
              class="w-full rounded-xl bg-zinc-900/50 border border-white/10 px-4 py-3.5 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all shadow-inner"
              placeholder="nama@perusahaan.com">
          </div>
          @error('email')
            <p class="mt-1.5 text-xs text-red-500/90 flex items-center gap-1.5 font-medium">
              <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Password --}}
        <div class="space-y-2 group/input">
          <label for="password" class="block text-sm font-medium text-zinc-400 transition-colors group-focus-within/input:text-emerald-400">Security Key (Kata Sandi)</label>
          <div class="relative">
            <input id="password" type="password" name="password" required
              class="w-full rounded-xl bg-zinc-900/50 border border-white/10 px-4 py-3.5 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all shadow-inner"
              placeholder="••••••••">
          </div>
          @error('password')
            <p class="mt-1.5 text-xs text-red-500/90 flex items-center gap-1.5 font-medium">
              <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Password Confirmation --}}
        <div class="space-y-2 group/input">
          <label for="password_confirmation" class="block text-sm font-medium text-zinc-400 transition-colors group-focus-within/input:text-emerald-400">Konfirmasi Security Key</label>
          <div class="relative">
            <input id="password_confirmation" type="password" name="password_confirmation" required
              class="w-full rounded-xl bg-zinc-900/50 border border-white/10 px-4 py-3.5 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all shadow-inner"
              placeholder="••••••••">
          </div>
        </div>

        {{-- Submit --}}
        <div class="pt-4 group/btn">
          <button type="submit"
            class="w-full flex justify-center items-center gap-2 rounded-xl bg-white hover:bg-zinc-200 px-4 py-3.5 text-sm font-bold text-black transition-all shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_25px_rgba(255,255,255,0.2)] focus:outline-none focus:ring-2 focus:ring-white/50 focus:ring-offset-2 focus:ring-offset-[#0a0a0c]">
             Daftar & Inisiasi Ruang Kerja
            <svg class="w-4 h-4 opacity-50 group-hover/btn:opacity-100 group-hover/btn:translate-x-1 transition-all"
              fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
          </button>
        </div>
      </form>

      {{-- Secondary actions --}}
      <div class="mt-8 flex flex-col items-center justify-center gap-4 text-sm w-full border-t border-white/5 pt-8">
        <p class="text-zinc-500 text-center">
          Sudah terdaftar?
          <a href="{{ route('login') }}" class="text-zinc-300 hover:text-emerald-400 font-medium transition-colors ml-1">Masuk ke Portal</a>
        </p>
        <a href="{{ route('pricing') }}" class="inline-flex items-center gap-1.5 text-zinc-600 hover:text-zinc-300 transition-colors mt-2">
          <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7 7-7M3 12h18"></path>
          </svg>
          Kembali ke Pilihan Paket
        </a>
      </div>
    </div>
    
    {{-- Compliance Footer --}}
    <div class="absolute bottom-6 w-full text-center text-[11px] text-zinc-600 font-medium tracking-wide">
      © {{ date('Y') }} JriGPT Enterprise. System secured by FAJRIAG.
    </div>
  </main>
</body>

</html>