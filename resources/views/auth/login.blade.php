<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - VIP Enterprise</title>
  <link rel="icon" type="image/svg+xml"
    href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23059669'/><text x='50%25' y='50%25' font-size='40' font-weight='bold' fill='white' font-family='Arial, sans-serif' text-anchor='middle' dominant-baseline='central'>JG</text></svg>">
  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="{{ Vite::asset('resources/css/public-chat.css') }}">

  <!-- Highlight js for consistency -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body
  class="min-h-screen md:h-screen md:overflow-hidden bg-zinc-950 text-zinc-100 antialiased font-sans flex flex-col md:flex-row selection:bg-zinc-800 selection:text-zinc-100"
  style="font-family: 'Inter', sans-serif;">
  <!-- Left Side: Branding / Visual (Hidden on mobile) -->
  <div
    class="hidden md:flex md:w-1/2 flex-col justify-between p-12 lg:p-16 relative overflow-hidden bg-zinc-950 border-r border-zinc-900">
    <!-- Abstract Background Artifacts (Clean & Corporate) -->
    <div
      class="absolute top-0 right-0 w-full h-full bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-zinc-800/10 via-zinc-950 to-zinc-950 pointer-events-none">
    </div>
    <!-- Fine Grid Background -->
    <div class="absolute inset-0 opacity-[0.03]"
      style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 32px 32px;">
    </div>

    <!-- Logo Section -->
    <div class="relative z-10 flex items-center gap-3">
      <div class="size-10 rounded-md bg-zinc-100 grid place-items-center shadow-md">
        <span class="text-zinc-950 text-lg font-black tracking-tighter">JG</span>
      </div>
      <a href="{{ url('/') }}"
        class="text-xl font-bold tracking-tight text-white hover:text-zinc-300 transition-colors">JriGPT</a>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 max-w-lg mt-auto pb-8">
      <div
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-md bg-zinc-900 border border-zinc-800 text-xs font-semibold text-zinc-300 mb-8">
        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]"></span>
        Enterprise Access
      </div>
      <h1 class="text-4xl lg:text-5xl font-semibold text-white leading-[1.15] tracking-tight mb-5">
        Empowering teams with<br />
        <span class="text-transparent bg-clip-text bg-gradient-to-r from-zinc-100 to-zinc-500">intelligent data.</span>
      </h1>
      <p
        class="text-zinc-400 text-base lg:text-lg leading-relaxed font-light hover:text-zinc-300 transition-colors duration-300">
        Masuk sebagai anggota VIP untuk prioritas generasi model, limit penggunaan tak terbatas, dan analisis data
        korporat terenkripsi.
      </p>
    </div>
  </div>

  <!-- Right Side: Login Form -->
  <main
    class="flex-1 flex flex-col justify-center items-center px-4 sm:px-6 py-8 md:py-12 lg:px-12 relative bg-[#09090b] md:bg-zinc-950 w-full min-h-screen md:min-h-0 md:h-screen md:overflow-y-auto">
    <!-- Mobile header -->
    <div class="md:hidden flex items-center justify-center gap-3 mb-12 w-full max-w-sm">
      <div class="size-10 rounded-md bg-zinc-100 grid place-items-center shadow-md">
        <span class="text-zinc-950 text-lg font-black tracking-tighter">JG</span>
      </div>
      <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-white">JriGPT</a>
    </div>

    <div
      class="w-full max-w-md bg-zinc-950/80 md:bg-transparent p-6 sm:p-10 border border-zinc-800/50 md:border-none shadow-2xl md:shadow-none rounded-2xl md:rounded-none relative z-10 mb-8 md:mb-0">

      <div class="mb-10 text-center md:text-left">
        <h2 class="text-3xl font-semibold text-white tracking-tight mb-2">Selamat Datang</h2>
        <p class="text-sm text-zinc-400 font-light">Silakan masukkan kredensial enterprise Anda.</p>
      </div>

      <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div class="space-y-2 group/input">
          <label for="email"
            class="block text-sm font-medium text-zinc-300 transition-colors group-focus-within/input:text-zinc-100">Alamat
            Email</label>
          <div class="relative">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
              class="w-full rounded-md bg-zinc-900 border border-zinc-800 px-4 py-3 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-1 focus:ring-zinc-500 focus:border-zinc-500 transition-all shadow-sm"
              placeholder="nama@perusahaan.com">
          </div>
          @error('email')
            <p class="mt-1.5 text-xs text-red-500/90 flex items-center gap-1.5 font-medium">
              <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Password --}}
        <div class="space-y-2 group/input">
          <div class="flex items-center justify-between">
            <label for="password"
              class="block text-sm font-medium text-zinc-300 transition-colors group-focus-within/input:text-zinc-100">Kata
              Sandi</label>
          </div>
          <div class="relative">
            <input id="password" type="password" name="password" required
              class="w-full rounded-md bg-zinc-900 border border-zinc-800 px-4 py-3 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-1 focus:ring-zinc-500 focus:border-zinc-500 transition-all shadow-sm"
              placeholder="••••••••">
          </div>
          @error('password')
            <p class="mt-1.5 text-xs text-red-500/90 flex items-center gap-1.5 font-medium">
              <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Options --}}
        <div class="pt-1 flex items-center justify-between text-sm">
          <label class="inline-flex items-center gap-2 cursor-pointer group/check">
            <div class="relative flex items-center justify-center">
              <input type="checkbox" name="remember"
                class="peer appearance-none w-4 h-4 rounded-sm border border-zinc-700 bg-zinc-900 checked:bg-zinc-100 checked:border-zinc-100 focus:outline-none focus:ring-2 focus:ring-zinc-500/30 focus:ring-offset-1 focus:ring-offset-zinc-950 transition-colors cursor-pointer">
              <svg
                class="absolute w-2.5 h-2.5 text-zinc-900 pointer-events-none opacity-0 peer-checked:opacity-100 transition-opacity"
                fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
              </svg>
            </div>
            <span class="text-zinc-400 group-hover/check:text-zinc-200 transition-colors font-medium">Ingat saya</span>
          </label>

          @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}"
              class="text-zinc-400 hover:text-zinc-200 transition-colors font-medium">Lupa sandi?</a>
          @endif
        </div>

        {{-- Submit --}}
        <div class="pt-5 group/btn">
          <button type="submit"
            class="w-full flex justify-center items-center gap-2 rounded-md bg-zinc-100 hover:bg-white px-4 py-3 text-sm font-semibold text-zinc-900 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 focus:ring-offset-zinc-950">
            Masuk ke Dashboard
            <svg class="w-4 h-4 opacity-50 group-hover/btn:opacity-100 group-hover/btn:translate-x-0.5 transition-all"
              fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
          </button>
        </div>
      </form>

      {{-- Secondary actions --}}
      <div class="mt-8 flex flex-col items-center gap-4 text-sm">
        @if (Route::has('register'))
          <p class="text-zinc-500">
            Belum punya akun?
            <a href="{{ route('register') }}"
              class="text-zinc-300 hover:text-white font-medium transition-colors underline decoration-zinc-800 underline-offset-4">Daftar
              Member VIP</a>
          </p>
        @endif

        <a href="{{ url('/') }}"
          class="inline-flex items-center gap-1.5 text-zinc-500 hover:text-zinc-300 transition-colors">
          <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7 7-7M3 12h18"></path>
          </svg>
          Kembali ke Fitur Dasar
        </a>
      </div>

      {{-- Compliance --}}
      <div class="mt-12 md:mt-16 text-center md:text-left text-xs text-zinc-600 font-medium">
        © {{ date('Y') }} JriGPT Enterprise. <br class="md:hidden" />
        <span class="hidden md:inline">|</span>
        <a href="{{ url('/terms') }}" class="hover:text-zinc-400 transition-colors" target="_blank">Ketentuan</a>
        &middot;
        <a href="{{ url('/privacy') }}" class="hover:text-zinc-400 transition-colors" target="_blank">Privasi</a>
      </div>
    </div>
  </main>

  <script type="module" src="{{ Vite::asset('resources/js/sidebar-toggle.js') }}"></script>
</body>

</html>