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

<body class="min-h-screen bg-[var(--bg)] text-gray-100 antialiased">
  <!-- Top Bar -->
  <header role="banner" class="h-12 flex items-center justify-between px-4 border-b border-white/10 bg-[#0c1117]">
    <div class="flex items-center gap-2">
      <a href="{{ url('/') }}" class="size-8 rounded bg-emerald-600 grid place-items-center font-bold">JG</a>
      <a href="{{ url('/') }}" class="font-semibold tracking-tight hover:opacity-90">JriGPT</a>
    </div>
    <div class="flex items-center gap-2">
      <button id="themeToggle" class="text-xs px-2 py-1 rounded border border-white/10 hover:bg-white/5">
        Theme
      </button>
    </div>
  </header>

  <!-- Main -->
  <main class="min-h-[calc(100vh-3rem)] grid place-items-center px-4">
    <div class="w-full max-w-md">
      <div class="rounded-2xl border border-white/10 bg-[#0c1117]/70 backdrop-blur p-6 shadow-lg">
        <div class="mb-6 text-center">
          <div class="mx-auto mb-3 size-12 rounded-xl bg-emerald-600 grid place-items-center font-bold">VIP</div>
          <h1 class="text-xl font-semibold">Masuk Akun VIP</h1>
          <p class="mt-1 text-sm text-gray-400">Akses fitur premium & prioritas antrian model.</p>
        </div>

        <form method="POST" action="{{ route('login') }}" class="space-y-4">
          @csrf

          {{-- Email --}}
          <div>
            <label for="email" class="block text-sm mb-1">Email</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus
              class="w-full rounded-lg bg-white/5 border border-white/10 px-3 py-2.5 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[var(--ring)]"
              placeholder="Masukkan email Anda">
            @error('email')
              <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
          </div>

          {{-- Password --}}
          <div>
            <label for="password" class="block text-sm mb-1">Kata sandi</label>
            <input id="password" type="password" name="password" required
              class="w-full rounded-lg bg-white/5 border border-white/10 px-3 py-2.5 placeholder:text-gray-500 focus:outline-none focus:ring-2 focus:ring-[var(--ring)]"
              placeholder="••••••••">
            @error('password')
              <p class="mt-1 text-xs text-red-400">{{ $message }}</p>
            @enderror
          </div>

          {{-- Options --}}
          <div class="flex items-center justify-between text-sm">
            <label class="inline-flex items-center gap-2 select-none">
              <input type="checkbox" name="remember" class="rounded border-white/20 bg-white/5">
              <span>Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
              <a href="{{ route('password.request') }}" class="text-gray-300 hover:text-white">
                Lupa sandi?
              </a>
            @endif
          </div>

          {{-- Submit --}}
          <button type="submit"
            class="w-full rounded-xl bg-emerald-600 hover:bg-emerald-500 px-4 py-2.5 font-semibold transition focus:outline-none focus:ring-2 focus:ring-[var(--ring)]">
            Masuk
          </button>
        </form>

        {{-- Divider --}}
        <div class="my-6 flex items-center gap-4 text-xs text-gray-500">
          <div class="h-px flex-1 bg-white/10"></div>
          <span>atau</span>
          <div class="h-px flex-1 bg-white/10"></div>
        </div>

        {{-- Secondary actions --}}
        <div class="space-y-2 text-sm">
          @if (Route::has('register'))
            <div class="flex items-center justify-between">
              <span class="text-gray-400">Belum punya akun?</span>
              <a href="{{ route('register') }}"
                class="px-3 py-1.5 rounded-lg bg-white/5 hover:bg-white/10 border border-white/10">
                Daftar VIP
              </a>
            </div>
          @endif

          <a href="{{ url('/') }}" class="mt-1 inline-flex items-center gap-2 text-gray-300 hover:text-white">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
              <path stroke="currentColor" stroke-width="2" d="M10 19l-7-7 7-7M3 12h18" />
            </svg>
            Kembali ke Halaman Chat
          </a>
        </div>
      </div>

      {{-- Compliance / Note --}}
      <p class="mt-4 text-center text-xs text-gray-500">
        Dengan masuk, Anda menyetujui <a href="#" class="underline hover:text-gray-300">Ketentuan</a> dan <a href="#"
          class="underline hover:text-gray-300">Kebijakan Privasi</a>.
      </p>
    </div>
  </main>

  <!-- Script: theme toggle & (no-sidebar) safe -->
  <script type="module" src="{{ Vite::asset('resources/js/sidebar-toggle.js') }}"></script>
</body>

</html>