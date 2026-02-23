{{-- resources/views/layouts/chat.blade.php --}}
<!doctype html>
<html lang="id" class="h-full dark" data-theme="dark" style="background-color: #0b0f15;">

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>@yield('title', 'JriGPT')</title>
  <link rel="icon" type="image/svg+xml"
    href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><rect width='100' height='100' rx='20' fill='%23059669'/><text x='50%25' y='50%25' font-size='40' font-weight='bold' fill='white' font-family='Arial, sans-serif' text-anchor='middle' dominant-baseline='central'>JG</text></svg>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/katex@0.16.21/dist/katex.min.css"
    integrity="sha384-zh0CIsljE7vayia65P8Huv8Lq47h94K6vE/MToU4Q1/xscYAAu+rS7zWuP345E6A" crossorigin="anonymous">
  <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.21/dist/katex.min.js"
    integrity="sha384-Rma6DA2S8Uwh7pUIE7B9U/8HZ/+da9WlS8zYW4V4v3D8E1n/B0G3D2O7L2W6pT" crossorigin="anonymous"></script>
  <script defer src="https://cdn.jsdelivr.net/npm/katex@0.16.21/dist/contrib/auto-render.min.js"
    integrity="sha384-h7lkLVDM7wOMuc5qR7n37VNKuLXx27Y5BACTYdgK/tdr9H8sqHaw7Wn9zGzVzU" crossorigin="anonymous"></script>
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
