{{-- resources/views/auth/register.blade.php --}}
<!doctype html>
<html lang="id" class="h-full">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Website Dalam Pengembangan</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite([
    'resources/css/app.css',
    'resources/css/public-chat.css',
  ])
</head>
<body class="h-full bg-[#0b0f15] text-gray-100">
  <div class="min-h-screen grid place-items-center px-4">
    <div class="w-full max-w-lg">
      <div class="text-center mb-6">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full border border-white/10 bg-white/5 text-xs text-gray-300">
          <span class="size-1.5 rounded-full bg-amber-400 animate-pulse"></span>
          Website Dalam Pengembangan
        </div>
        <h1 class="mt-4 text-2xl font-semibold tracking-tight">Fitur Pendaftaran Belum Tersedia</h1>
        <p class="mt-1 text-sm text-gray-400">Kami sedang menyiapkan versi berikutnya dari layanan ini. Terima kasih atas kesabaran Anda.</p>
      </div>

      <div class="rounded-2xl border border-white/10 bg-[#0c1117] p-6">
        <div class="space-y-4 text-sm text-gray-300">
          <p>Beberapa fitur utama yang sedang kami kerjakan:</p>
          <ul class="list-disc list-inside text-gray-400 space-y-1">
            <li>Pendaftaran & verifikasi email</li>
            <li>Manajemen profil & keamanan</li>
            <li>Akses VIP tanpa batas</li>
          </ul>
          <p class="text-gray-400">Sementara itu, Anda masih bisa mencoba AI Chat publik di beranda.</p>
        </div>

        <div class="mt-6 grid grid-cols-1 sm:grid-cols-2 gap-2">
          <a href="{{ route('public.chat') }}"
             class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 border border-white/10 bg-white/5 hover:bg-white/10 transition">
            ← Kembali ke Beranda
          </a>
          <a href="{{ route('login') }}"
             class="inline-flex items-center justify-center gap-2 rounded-lg px-4 py-2 border border-emerald-500/30 bg-emerald-600/90 hover:bg-emerald-600 transition">
            Login VIP
          </a>
        </div>
      </div>

      <p class="mt-4 text-center text-xs text-gray-500">
        Perlu bantuan? <a href="mailto:jridev2@gmail.com" class="underline decoration-dotted hover:text-gray-300">Hubungi admin</a>.
      </p>
    </div>
  </div>
</body>
</html>
