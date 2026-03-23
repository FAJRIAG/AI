{{-- resources/views/auth/register.blade.php --}}
<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Registrasi - VIP Enterprise</title>
  <link rel="icon" type="image/svg+xml"
    href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23059669'/><text x='50%25' y='50%25' font-size='40' font-weight='bold' fill='white' font-family='Arial, sans-serif' text-anchor='middle' dominant-baseline='central'>JG</text></svg>">
  <meta name="csrf-token" content="{{ csrf_token() }}">

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="{{ Vite::asset('resources/css/public-chat.css') }}">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body class="min-h-screen bg-[#050505] text-zinc-100 antialiased font-sans flex flex-col items-center justify-center p-6 lg:p-12 relative overflow-x-hidden selection:bg-zinc-800 selection:text-zinc-100" style="font-family: 'Inter', sans-serif;">
  
  <!-- Abstract Premium Background -->
  <div class="fixed top-[-20%] left-[10%] w-[60%] h-[70%] bg-emerald-600/10 blur-[150px] rounded-full pointer-events-none -z-10"></div>
  <div class="fixed bottom-[-10%] right-[5%] w-[40%] h-[50%] bg-emerald-900/10 blur-[120px] rounded-full pointer-events-none -z-10"></div>
  
  <!-- Fine Grid Background -->
  <div class="fixed inset-0 opacity-[0.02] pointer-events-none -z-10" style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 32px 32px;"></div>

  <!-- Navbar / Logo Area -->
  <div class="w-full max-w-6xl mx-auto flex items-center justify-between relative z-10 mb-8 mt-4">
    <div class="flex items-center gap-3">
      <div class="size-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 grid place-items-center shadow-[0_0_20px_rgba(16,185,129,0.3)] border border-emerald-400/20">
        <span class="text-white text-lg font-black tracking-tighter">JG</span>
      </div>
      <a href="{{ url('/') }}" class="text-2xl font-bold tracking-tight text-white hover:text-emerald-400 transition-colors">JriGPT<span class="text-emerald-500">.</span></a>
    </div>
    <a href="{{ route('login') }}" class="text-sm font-semibold text-zinc-400 hover:text-white transition-colors">Kembali ke Login</a>
  </div>

  <!-- Header -->
  <div class="w-full max-w-5xl mx-auto mb-12 text-center relative z-10">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-bold text-emerald-400 tracking-wide uppercase mb-6 shadow-sm backdrop-blur-md">
      <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 shadow-[0_0_8px_rgba(52,211,153,0.8)] animate-pulse"></span>
      Pilih Paket Akses
    </div>
    <h1 class="text-4xl lg:text-5xl xl:text-6xl font-semibold tracking-tight text-white mb-5 leading-[1.1]">
      Mulai Perjalanan <span class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-cyan-400">AI Anda</span>
    </h1>
    <p class="text-base lg:text-lg text-zinc-400 font-light max-w-2xl mx-auto leading-relaxed">
      Dapatkan akses tak terbatas ke kapabilitas Agentic & RAG tercanggih. Pilih paket ekosistem yang sesuai dengan kebutuhan personal atau tim korporat Anda.
    </p>
  </div>

  <!-- Pricing Cards -->
  <div class="w-full max-w-6xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 relative z-10 pb-16">

    <!-- Tier 1: Basic / Free -->
    <div class="group relative bg-[#0c0c0e]/80 backdrop-blur-2xl rounded-3xl border border-white/5 p-8 flex flex-col transition-all duration-500 hover:bg-[#111115] hover:border-white/10 shadow-lg">
      <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl"></div>
      
      <div class="mb-8 relative z-10">
        <h3 class="text-xl font-medium text-zinc-300 mb-3">Publik</h3>
        <div class="flex items-baseline gap-1">
          <span class="text-4xl font-bold text-white tracking-tight">Gratis</span>
        </div>
        <p class="text-sm text-zinc-500 mt-4 leading-relaxed h-10">Eksplorasi kemampuan dasar JriGPT tanpa biaya komitmen.</p>
      </div>
      
      <ul class="space-y-4 mb-10 flex-1 relative z-10">
        <li class="flex items-start text-sm text-zinc-300 font-medium">
          <svg class="h-5 w-5 text-zinc-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
          Saran kueri harian terbatas
        </li>
        <li class="flex items-start text-sm text-zinc-300 font-medium">
          <svg class="h-5 w-5 text-zinc-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
          Model standar & kecepatan normal
        </li>
        <li class="flex items-start text-sm text-zinc-600 font-medium">
          <svg class="h-5 w-5 text-zinc-700 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          Akses ke Tool Web Agent & RAG
        </li>
      </ul>
      
      <a href="{{ route('public.chat') }}" class="relative z-10 w-full rounded-xl px-5 py-4 text-sm font-bold text-center text-zinc-300 border border-white/10 bg-white/5 hover:bg-white/10 hover:text-white transition-all focus:outline-none focus:ring-2 focus:ring-white/20">
        Lanjutkan Mode Publik
      </a>
    </div>

    <!-- Tier 2: Pro (Highlighted) -->
    <div class="group relative bg-[#0a0a0c]/90 backdrop-blur-2xl rounded-3xl border border-emerald-500/40 p-8 flex flex-col shadow-[0_0_40px_rgba(16,185,129,0.1)] transform lg:-translate-y-4 transition-all duration-500 hover:shadow-[0_0_50px_rgba(16,185,129,0.2)] hover:border-emerald-400/60 z-20">
      <div class="absolute inset-0 bg-gradient-to-br from-emerald-500/10 to-transparent opacity-100 rounded-3xl pointer-events-none"></div>
      
      <div class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-emerald-400 to-teal-500 text-black text-xs font-extrabold px-4 py-1.5 rounded-full uppercase tracking-widest shadow-[0_0_15px_rgba(52,211,153,0.5)]">
        Paling Populer
      </div>
      
      <div class="mb-8 relative z-10 pt-2">
        <h3 class="text-xl font-medium text-emerald-400 mb-3">Pro</h3>
        <div class="flex items-baseline gap-1">
          <span class="text-sm font-semibold text-zinc-400 align-top mt-1">Rp</span>
          <span class="text-4xl font-bold text-white tracking-tight">399.000</span>
          <span class="text-sm font-medium text-zinc-500">/ bulan</span>
        </div>
        <p class="text-sm text-zinc-400 mt-4 leading-relaxed h-10">Kecepatan tinggi & fitur Agentic penuh untuk profesional individual.</p>
      </div>
      
      <ul class="space-y-4 mb-10 flex-1 relative z-10">
        <li class="flex items-start text-sm text-zinc-200 font-medium">
          <svg class="h-5 w-5 text-emerald-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
          Kueri AI tanpa batas jarak waktu & limit
        </li>
        <li class="flex items-start text-sm text-zinc-200 font-medium">
          <svg class="h-5 w-5 text-emerald-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
          Autonomous Web Agent (ReAct)
        </li>
        <li class="flex items-start text-sm text-zinc-200 font-medium">
          <svg class="h-5 w-5 text-emerald-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
          Artifacts Studio 2.0 (Live Preview)
        </li>
        <li class="flex items-start text-sm text-zinc-200 font-medium">
          <svg class="h-5 w-5 text-emerald-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
          Penyimpanan histori permanen (Workspaces)
        </li>
        <li class="flex items-start text-sm text-zinc-200 font-medium">
          <svg class="h-5 w-5 text-emerald-500 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
          Tanpa pemotongan kecepatan (No Throttle)
        </li>
      </ul>
      
      <a href="{{ route('register', ['plan' => 'pro']) }}" class="relative z-10 w-full inline-block rounded-xl px-5 py-4 text-sm font-bold text-center text-black bg-white hover:bg-zinc-200 shadow-[0_0_20px_rgba(255,255,255,0.1)] hover:shadow-[0_0_25px_rgba(255,255,255,0.2)] transition-all focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-[#0a0a0c]">
        Pilih Paket Pro
      </a>
    </div>

    <!-- Tier 3: Enterprise -->
    <div class="group relative bg-[#0c0c0e]/80 backdrop-blur-2xl rounded-3xl border border-white/5 p-8 flex flex-col transition-all duration-500 hover:bg-[#111115] hover:border-white/10 shadow-lg">
      <div class="absolute inset-0 bg-gradient-to-br from-white/5 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-500 rounded-3xl"></div>
      
      <div class="mb-8 relative z-10">
        <h3 class="text-xl font-medium text-zinc-300 mb-3">Enterprise</h3>
        <div class="flex items-baseline gap-1">
          <span class="text-4xl font-bold text-white tracking-tight">Kustom</span>
        </div>
        <p class="text-sm text-zinc-500 mt-4 leading-relaxed h-10">Keamanan level B2B & integrasi vector database (RAG) kustom untuk tim.</p>
      </div>
      
      <ul class="space-y-4 mb-10 flex-1 relative z-10">
        <li class="flex items-start text-sm text-zinc-300 font-medium">
          <svg class="h-5 w-5 text-zinc-400 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
          Semua kapabilitas super dari Paket Pro
        </li>
        <li class="flex items-start text-sm text-zinc-300 font-medium">
          <svg class="h-5 w-5 text-zinc-400 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
          Custom True RAG (Deep Research Base)
        </li>
        <li class="flex items-start text-sm text-zinc-300 font-medium">
          <svg class="h-5 w-5 text-zinc-400 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
          Isolasi privasi data terenkripsi penuh
        </li>
        <li class="flex items-start text-sm text-zinc-300 font-medium">
          <svg class="h-5 w-5 text-zinc-400 mr-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7" /></svg>
          Dedicated Account Manager & SSO
        </li>
      </ul>
      
      <a href="mailto:jridev2@gmail.com" class="relative z-10 w-full rounded-xl px-5 py-4 text-sm font-bold text-center text-zinc-300 border border-white/10 bg-white/5 hover:bg-white/10 hover:text-white transition-all focus:outline-none focus:ring-2 focus:ring-white/20">
        Hubungi Sales
      </a>
    </div>

  </div>

  <!-- Footer Actions -->
  <div class="mt-4 text-center text-sm relative z-10 w-full pb-8">
    <div class="text-xs text-zinc-600 font-medium tracking-wide">
      © {{ date('Y') }} JriGPT Enterprise. System secured by FAJRIAG.
      <br class="md:hidden mt-2"/>
      <span class="hidden md:inline mx-2">|</span>
      <a href="{{ url('/terms') }}" class="hover:text-zinc-400 transition-colors" target="_blank">Ketentuan Lapangan</a>
      <span class="mx-1">&middot;</span>
      <a href="{{ url('/privacy') }}" class="hover:text-zinc-400 transition-colors" target="_blank">Privasi Data</a>
    </div>
  </div>
</body>

</html>