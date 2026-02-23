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

<body
  class="min-h-screen bg-zinc-950 text-zinc-100 antialiased font-sans flex flex-col items-center justify-center p-6 selection:bg-zinc-800 selection:text-zinc-100"
  style="font-family: 'Inter', sans-serif;">
  <!-- Abstract Background Artifacts (Clean & Corporate) -->
  <div
    class="fixed top-0 inset-x-0 h-full w-full bg-[radial-gradient(ellipse_at_top,_var(--tw-gradient-stops))] from-zinc-900/30 via-zinc-950 to-zinc-950 pointer-events-none -z-10">
  </div>
  <!-- Fine Grid Background -->
  <div class="fixed inset-0 opacity-[0.03] -z-10"
    style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 32px 32px;">
  </div>

  <!-- Header -->
  <div class="w-full max-w-5xl mx-auto mb-12 text-center relative z-10 pt-10">
    <div
      class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-zinc-900 border border-zinc-800 text-xs font-semibold text-zinc-300 mb-6">
      <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
      Pilih Paket Akses
    </div>
    <h1 class="text-4xl lg:text-5xl font-semibold tracking-tight text-white mb-4">Mulai Perjalanan AI Anda</h1>
    <p class="text-base lg:text-lg text-zinc-400 font-light max-w-2xl mx-auto">
      Dapatkan akses penuh ke kapabilitas analitik tercanggih. Pilih paket yang sesuai dengan kebutuhan personal atau
      korporat Anda.
    </p>
  </div>

  <!-- Pricing Cards -->
  <div
    class="w-full max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 lg:gap-8 relative z-10 pb-12">

    <!-- Tier 1: Basic / Free -->
    <div
      class="rounded-2xl border border-zinc-800/80 bg-zinc-900/40 p-8 flex flex-col backdrop-blur-sm transition-all hover:bg-zinc-900/60 hover:border-zinc-700">
      <div class="mb-6">
        <h3 class="text-lg font-medium text-zinc-100 mb-2">Publik</h3>
        <div class="flex items-baseline gap-1">
          <span class="text-3xl font-bold text-white">Gratis</span>
        </div>
        <p class="text-sm text-zinc-400 mt-3 h-10">Eksplorasi kemampuan dasar JriGPT tanpa biaya.</p>
      </div>
      <ul class="space-y-4 mb-8 flex-1">
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-zinc-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Saran kueri harian terbatas
        </li>
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-zinc-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Model standar
        </li>
        <li class="flex items-start text-sm text-zinc-500">
          <svg class="h-4 w-4 text-zinc-700 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
          </svg>
          Akses ke analitik privat
        </li>
      </ul>
      <a href="{{ route('public.chat') }}"
        class="w-full rounded-md px-4 py-3 text-sm font-medium text-center text-zinc-300 border border-zinc-700 bg-transparent hover:bg-zinc-800 hover:text-white transition-colors">
        Lanjutkan Mode Publik
      </a>
    </div>

    <!-- Tier 2: Pro (Highlighted) -->
    <div
      class="rounded-2xl border border-emerald-500/30 bg-zinc-900/80 p-8 flex flex-col backdrop-blur-md shadow-[0_0_30px_rgba(16,185,129,0.05)] relative transform md:-translate-y-2 transition-all hover:border-emerald-500/50">
      <div
        class="absolute top-0 left-1/2 -translate-x-1/2 -translate-y-1/2 bg-gradient-to-r from-emerald-500 to-teal-400 text-zinc-950 text-xs font-bold px-3 py-1 rounded-full uppercase tracking-wider">
        Paling Populer
      </div>
      <div class="mb-6">
        <h3 class="text-lg font-medium text-zinc-100 mb-2">Pro</h3>
        <div class="flex items-baseline gap-1">
          <span class="text-xs font-medium text-zinc-400 align-top mt-1">Rp</span>
          <span class="text-3xl font-bold text-white">399.000</span>
          <span class="text-sm font-normal text-zinc-500">/ bulan</span>
        </div>
        <p class="text-sm text-zinc-400 mt-3 h-10">Kecepatan tinggi untuk profesional individual.</p>
      </div>
      <ul class="space-y-4 mb-8 flex-1">
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-emerald-400 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Kueri tanpa batas
        </li>
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-emerald-400 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Prioritas akses model generasi terbaru
        </li>
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-emerald-400 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Simpan histori percakapan
        </li>
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-emerald-400 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Dukungan prioritas
        </li>
      </ul>
      <a href="{{ route('register', ['plan' => 'pro']) }}"
        class="w-full inline-block rounded-md px-4 py-3 text-sm font-semibold text-center text-zinc-950 bg-zinc-100 hover:bg-white shadow-sm transition-all focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 focus:ring-offset-zinc-900">
        Pilih Paket Pro
      </a>
    </div>

    <!-- Tier 3: Enterprise -->
    <div
      class="rounded-2xl border border-zinc-800/80 bg-zinc-900/40 p-8 flex flex-col backdrop-blur-sm transition-all hover:bg-zinc-900/60 hover:border-zinc-700">
      <div class="mb-6">
        <h3 class="text-lg font-medium text-zinc-100 mb-2">Enterprise</h3>
        <div class="flex items-baseline gap-1">
          <span class="text-3xl font-bold text-white">Kustom</span>
        </div>
        <p class="text-sm text-zinc-400 mt-3 h-10">Keamanan maksimal & instansiasi model privat untuk tim.</p>
      </div>
      <ul class="space-y-4 mb-8 flex-1">
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-zinc-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Semua fitur Pro
        </li>
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-zinc-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Single Sign-On (SSO) & RBAC
        </li>
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-zinc-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Isolasi data / No training
        </li>
        <li class="flex items-start text-sm text-zinc-300">
          <svg class="h-4 w-4 text-zinc-500 mr-3 mt-0.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
          </svg>
          Dedicated Account Manager
        </li>
      </ul>
      <a href="mailto:jridev2@gmail.com"
        class="w-full rounded-md px-4 py-3 text-sm font-medium text-center text-zinc-300 border border-zinc-700 bg-transparent hover:bg-zinc-800 hover:text-white transition-colors">
        Hubungi Sales
      </a>
    </div>

  </div>

  <!-- Footer Actions -->
  <div class="mt-4 text-center text-sm relative z-10 w-full pb-8">
    <p class="text-zinc-500 mb-4">
      Sudah memiliki akun?
      <a href="{{ route('login') }}"
        class="text-zinc-300 hover:text-white font-medium transition-colors underline decoration-zinc-800 underline-offset-4">Masuk
        ke Dashboard</a>
    </p>
    <div class="text-xs text-zinc-600 font-medium">
      © {{ date('Y') }} JriGPT Enterprise.
      <span class="mx-2">|</span>
      <a href="{{ url('/terms') }}" class="hover:text-zinc-400 transition-colors" target="_blank">Ketentuan</a>
      &middot;
      <a href="{{ url('/privacy') }}" class="hover:text-zinc-400 transition-colors" target="_blank">Privasi</a>
    </div>
  </div>

</body>

</html>