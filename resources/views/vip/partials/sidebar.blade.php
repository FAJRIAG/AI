{{-- resources/views/vip/partials/sidebar.blade.php --}}
@php
  /** @var App\Models\Project $activeProject */
  $sessions = $sessions ?? [];
  $sid = $sid ?? null;
  $projects = $projects ?? collect();
@endphp

<aside class="sidebar hidden md:flex flex-col h-full min-h-0 bg-[#0c1117] text-gray-200 border-r border-white/10">

  {{-- Workspace Switcher (Header) --}}
  <div class="shrink-0 sticky top-0 z-10 bg-[#0c1117] border-b border-white/10 p-3 space-y-3">
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-2 cursor-pointer hover:bg-white/5 rounded transition"
          data-toggle="sidebar" aria-label="Toggle sidebar">
          <div class="size-8 rounded bg-emerald-600 grid place-items-center font-bold text-white shadow-[0_0_15px_rgba(5,150,105,0.3)]">JG</div>
          <div class="font-bold tracking-tight text-gray-100">JriGPT <span class="text-[10px] bg-emerald-500/20 text-emerald-400 px-1.5 py-0.5 rounded ml-1 border border-emerald-500/30">VIP</span></div>
        </div>
        <button onclick="openProjectSettings({{ $activeProject->id }}, '{{ addslashes($activeProject->name) }}', '{{ addslashes($activeProject->description) }}', '{{ $activeProject->last_indexed_at ? $activeProject->last_indexed_at->diffForHumans() : '' }}')" 
                class="p-2 hover:bg-white/10 rounded-lg text-gray-500 hover:text-emerald-400 transition-colors" title="Workspace Settings">
            <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37a1.724 1.724 0 002.572-1.065z" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
            </svg>
        </button>
    </div>

    {{-- Project Dropdown Selector --}}
    <div class="relative group">
        <label class="text-[10px] font-bold text-gray-500 uppercase tracking-widest ml-1 mb-1 block">Active Workspace</label>
        <div class="flex items-center gap-2">
            <select onchange="window.location.href='?project='+this.value" 
                    class="flex-1 bg-white/5 border border-white/10 rounded-lg px-3 py-2 text-sm text-gray-200 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 cursor-pointer appearance-none">
                @foreach($projects as $p)
                    <option value="{{ $p->id }}" {{ $activeProject->id == $p->id ? 'selected' : '' }}>
                        📁 {{ $p->name }}
                    </option>
                @endforeach
            </select>
            <form method="POST" action="{{ route('projects.store') }}">
                @csrf
                <button type="submit" class="p-2 bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 rounded-lg hover:bg-emerald-600/30 transition-all" title="Crate New Workspace">
                    <svg class="size-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </button>
            </form>
        </div>
    </div>

    <form method="POST" action="{{ route('sessions.store', $activeProject->id) }}" class="w-full">
        @csrf
        <button id="newChatBtn" class="w-full flex items-center justify-center gap-2 rounded-lg border px-3 py-2 text-sm font-bold transition
                 bg-emerald-600 text-white border-emerald-500 hover:bg-emerald-700 shadow-[0_4px_15px_rgba(5,150,105,0.2)]" type="submit">
          <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor">
            <path stroke-width="3" d="M12 4v16m8-8H4" />
          </svg>
          New Chat
        </button>
      </form>
  </div>

  {{-- List (bagian yang scroll) --}}
  <div id="sessionList" class="flex-1 min-h-0 overflow-y-auto px-2 py-4 space-y-1 custom-scrollbar">
    <div class="px-3 mb-2 flex items-center justify-between">
        <span class="text-[10px] font-bold text-gray-500 uppercase tracking-widest">Recent Chats</span>
        <span class="text-[10px] bg-white/5 px-1.5 py-0.5 rounded text-gray-400 border border-white/10">{{ count($sessions) }}</span>
    </div>
    @forelse($sessions as $s)
      @php
        $sSid = $s['sid'] ?? '';
        $sTitle = $s['title'] ?? 'Untitled';
        $active = ($sid == $sSid);
      @endphp

      <div class="group rounded-xl border border-transparent {{ $active ? 'bg-emerald-500/10 border-emerald-500/20' : 'hover:bg-white/[.04]' }} transition-all duration-200">
        <div class="flex items-center gap-2 pl-3 pr-1">
          <svg class="w-3.5 h-3.5 {{ $active ? 'text-emerald-400' : 'text-gray-500' }} shrink-0" viewBox="0 0 24 24" fill="none">
            <path stroke="currentColor" stroke-width="2.5" d="M4 6h16v10H7l-3 3V6z" />
          </svg>

          <a href="{{ route('vip.home', ['session' => $sSid, 'project' => $activeProject->id]) }}"
            class="flex-1 py-2.5 text-sm truncate {{ $active ? 'text-emerald-200 font-semibold' : 'text-gray-400 group-hover:text-gray-200' }}">
            {{ $sTitle }}
          </a>

          <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
            <form method="POST" action="{{ route('sessions.destroy', $sSid) }}"
              onsubmit="return confirm('Hapus chat ini?')">
              @csrf @method('DELETE')
              <button class="p-1.5 rounded-lg hover:bg-red-500/10 text-gray-500 hover:text-red-400 transition-colors" title="Delete">
                <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                  <path stroke="currentColor" stroke-width="2"
                    d="M19 7H5m3 0V5a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2m1 0v12a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2V7" />
                </svg>
              </button>
            </form>
          </div>
        </div>
      </div>
    @empty
      <div class="text-center py-10 px-4">
          <div class="size-12 rounded-full bg-white/5 border border-white/10 grid place-items-center mx-auto mb-3">
              <svg class="size-6 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
          </div>
          <p class="text-xs text-gray-500">Workspace ini masih kosong. Mulai chat pertama kamu!</p>
      </div>
    @endforelse
  </div>

  {{-- Sidebar Footer --}}
  <div id="sidebarFooter" class="mt-auto shrink-0 bg-[#0c1117] border-t border-white/10 p-3 space-y-2">
      <div class="flex items-center gap-3 px-3 py-2 bg-emerald-500/5 rounded-xl border border-emerald-500/10 mb-2">
          <div class="size-8 rounded-lg bg-emerald-500/20 grid place-items-center text-emerald-400 font-bold">
              {{ substr(auth()->user()->name, 0, 1) }}
          </div>
          <div class="flex-1 min-w-0">
              <p class="text-xs font-bold text-gray-200 truncate">{{ auth()->user()->name }}</p>
              <p class="text-[10px] text-emerald-500 font-medium tracking-tight">Enterprise User</p>
          </div>
      </div>
      <a href="{{ route('logout') }}"
        class="w-full inline-flex items-center gap-2 text-sm rounded-lg px-3 py-2 bg-red-500/5 hover:bg-red-500/10 border border-red-500/10 text-red-400 font-bold transition-all">
        <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
          <path stroke="currentColor" stroke-width="2"
            d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12" />
        </svg>
        Logout Session
      </a>
  </div>
</aside>