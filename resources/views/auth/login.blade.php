<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="csrf-token" content="{{ csrf_token() }}">
  <title>Login - VIP JriGPT</title>

  @vite(['resources/css/app.css', 'resources/js/app.js'])
  <link rel="stylesheet" href="{{ Vite::asset('resources/css/public-chat.css') }}">

  <!-- Highlight js for consistency -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
</head>

<body class="min-h-screen bg-[#06080b] text-gray-100 antialiased overflow-hidden flex flex-col md:flex-row">
  <!-- Left Side: Branding / Visual (Hidden on mobile) -->
  <div
    class="hidden md:flex md:w-[45%] lg:w-[50%] flex-col justify-between p-12 relative overflow-hidden bg-gradient-to-br from-[#0c1117] to-[#05070a]">
    <!-- Abstract Background Artifacts -->
    <div
      class="absolute top-[-10%] left-[-10%] w-[50%] h-[50%] bg-emerald-600/10 blur-[100px] rounded-full pointer-events-none">
    </div>
    <div
      class="absolute bottom-[-10%] right-[-10%] w-[40%] h-[40%] bg-blue-600/10 blur-[100px] rounded-full pointer-events-none">
    </div>

    <div class="relative z-10 flex items-center gap-3">
      <a href="{{ url('/') }}"
        class="size-10 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700 grid place-items-center text-lg font-bold shadow-lg shadow-emerald-900/50 text-white">JG</a>
      <a href="{{ url('/') }}"
        class="text-xl font-bold tracking-tight text-white hover:text-emerald-400 transition">JriGPT</a>
    </div>

    <div class="relative z-10 max-w-lg mt-auto pb-12">
      <div
        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-white/5 border border-white/10 text-xs font-medium text-emerald-400 mb-6 backdrop-blur">
        <span class="relative flex h-2 w-2">
          <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
          <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-500"></span>
        </span>
        VIP Access
      </div>
      <h1 class="text-4xl lg:text-5xl font-bold text-white leading-tight tracking-tight mb-4">
        Unleash the <span
          class="text-transparent bg-clip-text bg-gradient-to-r from-emerald-400 to-teal-300">Intelligent</span> Future.
      </h1>
      <p class="text-gray-400 text-lg leading-relaxed">
        Masuk sebagai anggota VIP untuk mendapatkan prioritas generasi model, limit tak terbatas, dan kapabilitas
        analitik tercanggih.
      </p>
    </div>
  </div>

  <!-- Right Side: Login Form -->
  <main class="flex-1 flex flex-col justify-center px-6 py-12 lg:px-24 xl:px-32 relative bg-[#0b0f15]">
    <!-- Mobile header (only shows on mobile) -->
    <div class="md:hidden flex items-center gap-3 mb-10">
      <a href="{{ url('/') }}"
        class="size-10 rounded-xl bg-emerald-600 grid place-items-center text-lg font-bold text-white shadow-lg">JG</a>
      <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-white">JriGPT</a>
    </div>

    <div class="w-full max-w-sm mx-auto">
      <div class="mb-10 lg:mb-12">
        <h2 class="text-3xl font-bold text-white tracking-tight">Selamat Datang</h2>
        <p class="mt-2 text-sm text-gray-400">Silakan masuk ke akun VIP Anda.</p>
      </div>

      <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        {{-- Email --}}
        <div class="space-y-2">
          <label for="email" class="block text-sm font-medium text-gray-300">Alamat Email</label>
          <div class="relative">
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
              class="w-full rounded-xl bg-white/[0.03] border border-white/10 px-4 py-3.5 text-gray-100 placeholder:text-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all shadow-inner"
              placeholder="nama@email.com">
          </div>
          @error('email')
            <p class="mt-1 text-xs text-red-400 flex items-center gap-1">
              <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Password --}}
        <div class="space-y-2">
          <div class="flex items-center justify-between">
            <label for="password" class="block text-sm font-medium text-gray-300">Kata Sandi</label>
            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}"
                class="text-xs text-emerald-500 hover:text-emerald-400 transition font-medium">Lupa sandi?</a>
            @endif
          </div>
          <div class="relative">
            <input id="password" type="password" name="password" required
              class="w-full rounded-xl bg-white/[0.03] border border-white/10 px-4 py-3.5 text-gray-100 placeholder:text-gray-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500/50 transition-all shadow-inner"
              placeholder="••••••••">
          </div>
          @error('password')
            <p class="mt-1 text-xs text-red-400 flex items-center gap-1">
              <svg class="size-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
              </svg>
              {{ $message }}
            </p>
          @enderror
        </div>

        {{-- Options --}}
        <div class="pt-2 flex items-center justify-between text-sm">
          <label class="inline-flex items-center gap-2.5 cursor-pointer group">
            <div class="relative flex items-center justify-center">
              <input type="checkbox" name="remember" class="peer sr-only">
              <div
                class="size-5 rounded border border-white/20 bg-white/5 peer-checked:bg-emerald-600 peer-checked:border-emerald-500 transition-colors flex items-center justify-center">
                <svg class="size-3 text-white opacity-0 peer-checked:opacity-100 transition-opacity" fill="none"
                  viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                  <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"></path>
                </svg>
              </div>
            </div>
            <span class="text-gray-400 group-hover:text-gray-200 transition">Ingat saya</span>
          </label>
        </div>

        {{-- Submit --}}
        <div class="pt-4">
          <button type="submit"
            class="w-full group rounded-xl bg-gradient-to-r from-emerald-600 to-teal-500 hover:from-emerald-500 hover:to-teal-400 px-4 py-3.5 font-semibold text-white transition-all shadow-lg shadow-emerald-900/20 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 focus:ring-offset-[#0b0f15] flex justify-center items-center gap-2">
            Masuk ke Dashboard
            <svg class="w-4 h-4 opacity-70 group-hover:opacity-100 group-hover:translate-x-1 transition-all" fill="none"
              viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
              <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
            </svg>
          </button>
        </div>
      </form>

      {{-- Divider --}}
      <div class="my-8 flex items-center gap-4 text-xs text-gray-500">
        <div class="h-px flex-1 bg-gradient-to-r from-transparent to-white/10"></div>
        <span class="font-medium tracking-wide uppercase">Belum punya akun?</span>
        <div class="h-px flex-1 bg-gradient-to-l from-transparent to-white/10"></div>
      </div>

      {{-- Secondary actions --}}
      <div class="flex flex-col items-center gap-4 text-sm font-medium">
        @if (Route::has('register'))
          <a href="{{ route('register') }}"
            class="w-full text-center px-4 py-3.5 rounded-xl bg-transparent hover:bg-white/[0.02] border border-white/10 text-gray-300 hover:text-white transition-all">
            Daftar sebagai Member VIP
          </a>
        @endif

        <a href="{{ url('/') }}"
          class="inline-flex items-center gap-1.5 text-gray-500 hover:text-gray-300 transition-colors mt-2">
          <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7 7-7M3 12h18"></path>
          </svg>
          Kembali ke fitur Dasar
        </a>
      </div>

      {{-- Compliance --}}
      <p class="mt-12 text-center text-xs text-gray-600">
        © {{ date('Y') }} JriGPT. Dengan masuk, Anda menyetujui<br>
        <a href="{{ url('/terms') }}" class="underline hover:text-gray-400 transition" target="_blank">Ketentuan</a> dan
        <a href="{{ url('/privacy') }}" class="underline hover:text-gray-400 transition" target="_blank">Kebijakan
          Privasi</a>.
      </p>
    </div>
  </main>

  <script type="module" src="{{ Vite::asset('resources/js/sidebar-toggle.js') }}"></script>
</body>

</html>