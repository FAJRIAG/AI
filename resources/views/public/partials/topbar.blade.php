<header class="h-12 flex items-center justify-between px-4 border-b border-white/10 bg-[#0c1117]">
  {{-- Kiri: tombol toggle sidebar --}}
  <div class="flex items-center gap-2">
    <button class="font-semibold px-2 py-1 rounded hover:bg-white/10" data-toggle="sidebar" aria-label="Toggle sidebar">
      JriGPT
    </button>

    {{-- (opsional) breadcrumb kecil / judul halaman --}}
    @hasSection('topbar-title')
      <span class="hidden md:inline text-gray-500">/</span>
      <div class="hidden md:inline text-sm text-gray-300">
        @yield('topbar-title')
      </div>
    @endif
  </div>

  {{-- Kanan: aksi halaman + auth --}}
  <div class="flex items-center gap-2">
    {{-- Slot khusus halaman (mis. filter, tombol save, dsb) --}}
    @yield('topbar-actions')

    {{-- Theme toggle (global) --}}
    {{-- <button id="themeToggle"
      class="hidden sm:inline text-xs px-2 py-1 rounded border border-white/10 hover:bg-white/5">
      Theme
    </button> --}}



    {{-- Auth status: VIP Area / Login / Logout --}}
    @auth


      <a href="{{ route('logout') }}" class="text-xs px-3 py-1.5 rounded bg-white/10 hover:bg-white/20">
        Logout
      </a>
    @endauth

    @guest
      <a href="{{ route('login') }}" class="text-xs px-3 py-1.5 rounded bg-white/10 hover:bg-white/20">
        VIP
      </a>
    @endguest
  </div>
</header>