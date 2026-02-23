{{-- resources/views/layouts/chat.blade.php --}}
<!doctype html>
<html lang="id" class="h-full dark" data-theme="dark" style="background-color: #0b0f15;">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'JriGPT')</title>
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite([
    'resources/css/app.css',
    'resources/css/public-chat.css',
    'resources/js/app.js',
    'resources/js/public-chat.js',  // aktif di data-page="public-chat"
    'resources/js/vip-chat.js',     // aktif di data-page="vip-chat"
    'resources/js/sidebar-toggle.js',
  ])
</head>

<body class="h-full text-gray-100 bg-[#0b0f15]" style="background-color: #0b0f15;">
  <div id="appLayout" class="layout h-screen w-full grid grid-cols-1 md:grid-cols-[280px,1fr]">

    {{-- SIDEBAR --}}
    <aside class="sidebar hidden md:flex flex-col min-h-0 bg-[#0c1117] text-gray-200 border-r border-white/10">
      @hasSection('sidebar')
        @yield('sidebar')
      @else
        @include('public.partials.sidebar', [
          'sessions' => $sessions ?? [],
          'sid' => $sid ?? null,
        ])
      @endif
    </aside>

    {{-- BACKDROP (mobile) --}}
    <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 hidden md:hidden"></div>
 
     {{-- MAIN --}}
  <div class="flex flex-col min-h-0">
      {{-- Topbar (mobile) --}}
     @include('public.partials.topbar')
 
       <main class="flex-1 min-h-0 flex flex-col">
        @yield('content')
      </main>
    {{-- Composer dipanggil di view masing-masing agar tidak dobel --}}
    </div>
</div>


@stack('modals')
</body>
</html>
