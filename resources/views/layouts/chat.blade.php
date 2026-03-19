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
  <meta name="csrf-token" content="{{ csrf_token() }}">
  @vite([
    'resources/css/app.css',
    'resources/css/public-chat.css',
    'resources/js/app.js',
    'resources/js/public-chat.js',  // aktif di data-page="public-chat"
    'resources/js/vip-chat.js',     // aktif di data-page="vip-chat"
    'resources/js/sidebar-toggle.js',
  ])
  {{-- Mermaid.js for Artifacts --}}
  <script src="https://cdn.jsdelivr.net/npm/mermaid@10.9.0/dist/mermaid.min.js"></script>
</head>

<body class="h-full text-gray-100 bg-[#0b0f15]" style="background-color: #0b0f15;">
  <div id="appLayout" class="layout h-screen w-full grid grid-cols-1 md:grid-cols-[280px,1fr] transition-[grid-template-columns] duration-300 ease-in-out" data-artifacts="closed">

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
      <div id="sidebarBackdrop" class="fixed inset-0 bg-black/40 hidden md:hidden z-40"></div>
 
      {{-- MAIN --}}
 <div class="flex flex-col min-h-0 min-w-0">
     {{-- Topbar (mobile) --}}
     @include('public.partials.topbar')
 
          <main class="flex-1 min-h-0 flex flex-col relative">
         @yield('content')
         </main>
    {{-- Composer dipanggil di view masing-masing agar tidak dobel --}}
    </div>

    {{-- ARTIFACTS PANEL (Claude style) --}}
    <aside id="artifactsSidebar" class="hidden lg:flex flex-col bg-[#0d1117] border-l border-white/10 translate-x-full transition-transform duration-300 fixed inset-y-0 right-0 w-[45%] z-30 lg:relative lg:translate-x-0 lg:w-auto">
      <div class="flex flex-col h-full">
        {{-- Header --}}
        <div class="h-14 border-b border-white/10 flex items-center justify-between px-4 shrink-0">
          <div class="flex items-center gap-2">
            <span class="p-1.5 rounded-lg bg-emerald-500/10 text-emerald-500">
               <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
            </span>
            <div class="flex flex-col">
              <span id="artifactTitle" class="text-sm font-medium text-gray-200 truncate max-w-[200px]">Artifact Preview</span>
              <span id="artifactSubtitle" class="text-[10px] text-gray-500 uppercase tracking-wider">v1</span>
            </div>
          </div>
          <div class="flex items-center gap-1">
             <button id="artifactCopy" class="p-2 hover:bg-white/5 rounded-lg text-gray-400 transition-colors" title="Copy Code">
               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
             </button>
             <button id="artifactDownload" class="p-2 hover:bg-white/5 rounded-lg text-gray-400 transition-colors" title="Download">
               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
             </button>
             <button id="artifactClose" class="p-2 hover:bg-white/5 rounded-lg text-gray-400 transition-colors lg:hidden" title="Close">
               <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
             </button>
          </div>
        </div>

        {{-- Content Area --}}
        <div id="artifactContent" class="flex-1 overflow-auto bg-[#0b0f15] relative p-4">
           {{-- Iframe for HTML/JS --}}
           <iframe id="artifactIframe" class="w-full h-full border-none hidden" sandbox="allow-scripts allow-same-origin"></iframe>
           {{-- Div for Mermaid --}}
           <div id="artifactMermaid" class="w-full h-full hidden overflow-auto flex items-center justify-center p-4"></div>
           {{-- Empty State --}}
           <div id="artifactEmpty" class="w-full h-full flex flex-col items-center justify-center text-gray-500 gap-3">
              <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" stroke-linecap="round" stroke-linejoin="round" class="opacity-20"><rect width="18" height="18" x="3" y="3" rx="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg>
              <p class="text-sm font-light">Generate code or diagrams to see preview here</p>
           </div>
        </div>
      </div>
    </aside>

  </div>



@stack('modals')
</body>
</html>
