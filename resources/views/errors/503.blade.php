<!doctype html>
<html lang="id" class="h-full" data-theme="dark">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Sistem Sedang Dipelihara - JriGPT Enterprise</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
</head>

<body
    class="min-h-screen bg-zinc-950 text-zinc-100 antialiased font-sans flex flex-col items-center justify-center p-6 selection:bg-zinc-800 selection:text-zinc-100 overflow-hidden"
    style="font-family: 'Inter', sans-serif;">
    <!-- Abstract Background Artifacts (Clean & Corporate) -->
    <div
        class="fixed top-0 right-0 w-3/4 h-3/4 bg-[radial-gradient(circle_at_top_right,_var(--tw-gradient-stops))] from-amber-500/5 via-zinc-950 to-zinc-950 pointer-events-none -z-10">
    </div>
    <div
        class="fixed bottom-0 left-0 w-3/4 h-3/4 bg-[radial-gradient(circle_at_bottom_left,_var(--tw-gradient-stops))] from-zinc-800/10 via-zinc-950 to-zinc-950 pointer-events-none -z-10">
    </div>

    <!-- Fine Grid Background -->
    <div class="fixed inset-0 opacity-[0.03] -z-10"
        style="background-image: linear-gradient(to right, #ffffff 1px, transparent 1px), linear-gradient(to bottom, #ffffff 1px, transparent 1px); background-size: 32px 32px;">
    </div>

    <div
        class="w-full max-w-2xl bg-zinc-950/80 p-8 sm:p-12 border border-zinc-800/50 shadow-2xl rounded-2xl relative z-10 backdrop-blur-xl text-center">

        <!-- Animated Icon Container -->
        <div class="flex justify-center mb-8">
            <div class="relative">
                <!-- Outer Pulse -->
                <div class="absolute inset-0 bg-amber-500/20 rounded-full blur-xl animate-pulse"></div>
                <!-- Box -->
                <div
                    class="relative size-16 rounded-xl bg-zinc-900 border border-zinc-700/50 grid place-items-center shadow-lg shadow-black/50 overflow-hidden">
                    <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-amber-600 to-yellow-400"></div>
                    <svg class="w-8 h-8 text-amber-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                        </path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <!-- Main Content -->
        <h1 class="text-3xl sm:text-4xl font-semibold tracking-tight text-white mb-4">
            Peningkatan Sistem
        </h1>

        <p class="text-base sm:text-lg text-zinc-400 font-light leading-relaxed mb-8 max-w-lg mx-auto">
            JriGPT sedang dalam mode pemeliharaan rutin untuk meningkatkan kinerja keamanan dan menambah kapabilitas
            model analitik terbaru.
        </p>

        <!-- Status Card -->
        <div
            class="rounded-xl border border-zinc-800 bg-zinc-900/40 p-5 mb-8 text-left flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <span class="relative flex h-3 w-3">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                </span>
                <div>
                    <h4 class="text-sm font-medium text-zinc-200">Status Server: Offline Sementara</h4>
                    <p class="text-xs text-zinc-500 mt-0.5">Sedang melakukan sinkronisasi database...</p>
                </div>
            </div>

            <div class="text-xs font-mono text-zinc-500 bg-zinc-950 px-2.5 py-1.5 rounded border border-zinc-800">
                Error 503
            </div>
        </div>

        <!-- Actions -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <button onclick="window.location.reload()"
                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-md bg-zinc-100 hover:bg-white px-6 py-3.5 text-sm font-semibold text-zinc-900 transition-all shadow-sm focus:outline-none focus:ring-2 focus:ring-zinc-500">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
                Muat Ulang Halaman
            </button>
            <a href="mailto:jridev2@gmail.com"
                class="inline-flex w-full sm:w-auto items-center justify-center gap-2 rounded-md px-6 py-3.5 text-sm font-medium text-zinc-300 border border-zinc-800 bg-zinc-900/80 hover:bg-zinc-800 hover:text-white transition-all focus:outline-none focus:ring-2 focus:ring-zinc-500">
                Hubungi Support
            </a>
        </div>

    </div>

    <!-- Footer -->
    <div class="mt-12 text-center text-xs text-zinc-600 font-medium">
        © {{ date('Y') }} JriGPT Enterprise. Semua sistem akan segera kembali online.
    </div>

</body>

</html>