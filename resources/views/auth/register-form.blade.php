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

<body
    class="min-h-screen bg-zinc-950 text-zinc-100 antialiased font-sans flex flex-col md:flex-row overflow-hidden selection:bg-zinc-800 selection:text-zinc-100"
    style="font-family: 'Inter', sans-serif;">
    <!-- Left Side: Branding / Visual (Hidden on mobile) -->
    <div
        class="hidden md:flex md:w-1/2 flex-col justify-between p-12 lg:p-16 relative overflow-hidden bg-zinc-950 border-r border-zinc-900">
        <!-- Abstract Background Artifacts (Clean & Corporate) -->
        <div
            class="absolute top-0 right-0 w-full h-full bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-zinc-800/10 via-emerald-950/20 to-zinc-950 pointer-events-none">
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
                Unlock your true<br />
                <span class="text-transparent bg-clip-text bg-gradient-to-r from-zinc-100 to-zinc-500">potential.</span>
            </h1>
            <p
                class="text-zinc-400 text-base lg:text-lg leading-relaxed font-light hover:text-zinc-300 transition-colors duration-300">
                Bergabung sekarang dan dapatkan keistimewaan tak tertandingi dengan akses model terbaik kami untuk Anda
                dan tim.
            </p>
        </div>
    </div>

    <!-- Right Side: Register Form -->
    <main
        class="flex-1 flex flex-col justify-center items-center px-6 py-12 lg:px-12 relative bg-[#09090b] md:bg-zinc-950 overflow-auto">
        <!-- Mobile header -->
        <div class="md:hidden flex items-center justify-center gap-3 mb-12 w-full max-w-sm">
            <div class="size-10 rounded-md bg-zinc-100 grid place-items-center shadow-md">
                <span class="text-zinc-950 text-lg font-black tracking-tighter">JG</span>
            </div>
            <a href="{{ url('/') }}" class="text-xl font-bold tracking-tight text-white">JriGPT</a>
        </div>

        <div
            class="w-full max-w-md bg-zinc-950/80 md:bg-transparent p-8 sm:p-10 border border-zinc-800/50 md:border-none shadow-2xl md:shadow-none rounded-2xl md:rounded-none relative z-10">

            <div class="mb-10 text-center md:text-left">
                <h2 class="text-3xl font-semibold text-white tracking-tight mb-2">Daftar Akun VIP</h2>
                <p class="text-sm text-zinc-400 font-light">
                    @if(request('plan') == 'pro')
                        Anda memilih <strong>Paket Pro</strong>. Silakan lengkapi data diri Anda.
                    @else
                        Lengkapi kredensial untuk mendaftar.
                    @endif
                </p>
            </div>

            <form method="POST" action="{{ route('register') }}" class="space-y-5">
                @csrf

                {{-- Hidden Plan Field --}}
                @if(request('plan'))
                    <input type="hidden" name="plan" value="{{ request('plan') }}">
                @endif

                {{-- Name --}}
                <div class="space-y-2 group/input">
                    <label for="name"
                        class="block text-sm font-medium text-zinc-300 transition-colors group-focus-within/input:text-zinc-100">Nama
                        Lengkap</label>
                    <div class="relative">
                        <input id="name" type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full rounded-md bg-zinc-900 border border-zinc-800 px-4 py-3 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-1 focus:ring-zinc-500 focus:border-zinc-500 transition-all shadow-sm"
                            placeholder="Jhon Doe">
                    </div>
                    @error('name')
                        <p class="mt-1.5 text-xs text-red-500/90 flex items-center gap-1.5 font-medium">
                            <svg class="size-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                {{-- Email --}}
                <div class="space-y-2 group/input">
                    <label for="email"
                        class="block text-sm font-medium text-zinc-300 transition-colors group-focus-within/input:text-zinc-100">Alamat
                        Email</label>
                    <div class="relative">
                        <input id="email" type="email" name="email" value="{{ old('email') }}" required
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
                    <label for="password"
                        class="block text-sm font-medium text-zinc-300 transition-colors group-focus-within/input:text-zinc-100">Kata
                        Sandi</label>
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

                {{-- Password Confirmation --}}
                <div class="space-y-2 group/input">
                    <label for="password_confirmation"
                        class="block text-sm font-medium text-zinc-300 transition-colors group-focus-within/input:text-zinc-100">Konfirmasi
                        Kata Sandi</label>
                    <div class="relative">
                        <input id="password_confirmation" type="password" name="password_confirmation" required
                            class="w-full rounded-md bg-zinc-900 border border-zinc-800 px-4 py-3 text-sm text-zinc-100 placeholder:text-zinc-600 focus:outline-none focus:ring-1 focus:ring-zinc-500 focus:border-zinc-500 transition-all shadow-sm"
                            placeholder="••••••••">
                    </div>
                </div>

                {{-- Submit --}}
                <div class="pt-5 group/btn">
                    <button type="submit"
                        class="w-full flex justify-center items-center gap-2 rounded-md bg-zinc-100 hover:bg-white px-4 py-3 text-sm font-semibold text-zinc-900 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-500 focus:ring-offset-2 focus:ring-offset-zinc-950">
                        Daftar Sekarang
                        <svg class="w-4 h-4 opacity-50 group-hover/btn:opacity-100 group-hover/btn:translate-x-0.5 transition-all"
                            fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                        </svg>
                    </button>
                </div>
            </form>

            {{-- Secondary actions --}}
            <div class="mt-8 flex flex-col items-center gap-4 text-sm">
                <p class="text-zinc-500">
                    Sudah daftar?
                    <a href="{{ route('login') }}"
                        class="text-zinc-300 hover:text-white font-medium transition-colors underline decoration-zinc-800 underline-offset-4">Masuk
                        ke akun Anda</a>
                </p>

                <a href="{{ route('pricing') }}"
                    class="inline-flex items-center gap-1.5 text-zinc-500 hover:text-zinc-300 transition-colors">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7 7-7M3 12h18"></path>
                    </svg>
                    Kembali ke Pilihan Paket
                </a>
            </div>

            {{-- Compliance --}}
            <div class="mt-12 md:mt-16 text-center md:text-left text-xs text-zinc-600 font-medium">
                © {{ date('Y') }} JriGPT Enterprise. <br class="md:hidden" />
                <span class="hidden md:inline">|</span>
                <a href="{{ url('/terms') }}" class="hover:text-zinc-400 transition-colors"
                    target="_blank">Ketentuan</a>
                &middot;
                <a href="{{ url('/privacy') }}" class="hover:text-zinc-400 transition-colors"
                    target="_blank">Privasi</a>
            </div>
        </div>
    </main>

</body>

</html>