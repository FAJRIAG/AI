{{-- resources/views/public/partials/sidebar.blade.php --}}
@php
  /** @var array $sessions */
  $sessions = $sessions ?? [];
  $sid = $sid ?? null;
@endphp

<aside class="sidebar hidden md:flex flex-col h-full min-h-0 bg-[#0c1117] text-gray-200 border-r border-white/10">

  {{-- Header (tetap di atas) --}}
  <div class="shrink-0 sticky top-0 z-10 bg-[#0c1117] border-b border-white/10">
    <div class="px-3 pt-3 pb-2 flex items-center gap-2 cursor-pointer hover:bg-white/5 rounded transition"
      data-toggle="sidebar" aria-label="Toggle sidebar">
      <div class="size-8 rounded bg-emerald-600 grid place-items-center font-bold">JG</div>
      <div class="font-semibold tracking-tight">JriGPT</div>
    </div>

    <div class="px-3 pb-3">
      <form method="POST" action="{{ route('public.new') }}" class="w-full">
        @csrf
        <button id="newChatBtn" class="w-full flex items-center justify-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition
                 bg-black text-white border-black hover:bg-[#111]" type="submit">
          <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          New chat
        </button>
      </form>

      <div class="mt-2 relative">
        <input id="chatSearch" placeholder="Search chats" class="w-full rounded-lg bg-white/5 border border-white/10 px-3 py-2 text-sm placeholder:text-gray-500
                      focus:outline-none focus:ring-2 focus:ring-emerald-600/60">
        <svg class="w-4 h-4 absolute right-2 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none">
          <path stroke="currentColor" stroke-width="2" d="m21 21-4.3-4.3M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
        </svg>
      </div>
    </div>
  </div>

  {{-- List (bagian yang scroll) --}}
  <div id="sessionList" class="flex-1 min-h-0 overflow-y-auto px-2 py-2 space-y-1">
    @forelse($sessions as $s)
      @php
        $sSid = $s['sid'] ?? '';
        $sTitle = $s['title'] ?? 'Untitled';
        $active = ($sid === $sSid);
      @endphp

      <div
        class="group rounded-lg border border-white/5 {{ $active ? 'bg-white/10' : 'bg-white/[.03] hover:bg-white/[.06]' }} transition">
        <div class="flex items-center gap-2 pl-2 pr-1">
          <svg class="w-4 h-4 text-gray-400 shrink-0 mt-[2px]" viewBox="0 0 24 24" fill="none">
            <path stroke="currentColor" stroke-width="2" d="M4 6h16v10H7l-3 3V6z" />
          </svg>

          <a href="{{ route('public.switch', $sSid) }}"
            class="flex-1 py-2 text-sm truncate {{ $active ? 'text-emerald-300' : 'text-gray-200' }}">
            {{ $sTitle }}
          </a>

          <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
            {{-- Tombol Rename: pakai data-* (tidak ada inline onclick) --}}
            <button type="button" class="p-1 rounded hover:bg-white/10" data-rename data-sid="{{ $sSid }}"
              data-title="{{ $sTitle }}" title="Rename">
              <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
                <path stroke="currentColor" stroke-width="2"
                  d="m3 21 3-3m0 0 11-11a2.828 2.828 0 1 1 4 4L10 22m-4-4 4 4" />
              </svg>
            </button>

            <form method="POST" action="{{ route('public.delete', $sSid) }}"
              onsubmit="return confirm('Yakin hapus chat ini?')">
              @csrf
              <button class="p-1 rounded hover:bg-white/10" title="Delete">
                <svg class="w-4 h-4 text-red-400" viewBox="0 0 24 24" fill="none">
                  <path stroke="currentColor" stroke-width="2"
                    d="M19 7H5m3 0V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m1 0v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7" />
                </svg>
              </button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <p class="text-sm text-gray-500 px-3">No chats yet.</p>
    @endforelse
  </div>

  {{-- Footer (SELALU di bawah) --}}
  <div id="sidebarFooter" class="mt-auto shrink-0 bg-[#0c1117] border-t border-white/10">
    <div class="px-3 py-2 space-y-2">
      <a href="/login"
        class="w-full inline-flex items-center gap-2 text-sm rounded-lg px-3 py-2 bg-white/5 hover:bg-white/10 border border-white/10">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
          <path stroke="currentColor" stroke-width="2" d="M15 3h4a2 2 0 0 1 2 2v4M10 14 21 3M21 3h-6" />
        </svg>
        VIP Login
      </a>
      @auth
        <a href="{{ route('logout') }}"
          class="w-full inline-flex items-center gap-2 text-sm rounded-lg px-3 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-red-500 font-semibold">
          <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
            <path stroke="currentColor" stroke-width="2"
              d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12" />
          </svg>
          Logout
        </a>
      @endauth

    </div>
  </div>
</aside>