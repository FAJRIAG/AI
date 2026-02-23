{{-- resources/views/vip/partials/sidebar.blade.php --}}
@php
  /** @var \Illuminate\Support\Collection|\App\Models\Project[] $projects */
  $projects = $projects ?? collect();

  // Jika controller sudah kirim $sessions (flat), pakai itu. Jika tidak, flatten dari $projects.
  $sessionsList = collect($sessions ?? [])
    ->when(empty($sessions ?? null), function ($c) use ($projects) {
      return $projects->flatMap(function ($p) {
        return collect($p->sessions ?? [])->map(function ($s) {
          return [
            'sid' => $s->id,
            'title' => $s->title ?? 'Untitled',
          ];
        });
      })->values();
    })
    ->values();

  $activeSessionId = (string) request('session', '');
@endphp

{{-- =========================
HEADER (sticky)
========================= --}}
<div class="shrink-0 sticky top-0 z-10 bg-[#0c1117] border-b border-white/10">
  <div class="px-3 pt-3 pb-2 flex items-center gap-2 cursor-pointer hover:bg-white/5 rounded transition"
    data-toggle="sidebar" aria-label="Toggle sidebar">
    <div class="size-8 rounded bg-emerald-600 grid place-items-center font-bold">VIP</div>
    <div class="font-semibold tracking-tight">JriGPT</div>
  </div>

  <div class="px-3 pb-3 space-y-2">
    @if(($projects->count() ?? 0) > 0)
      @php $firstProject = $projects->first(); @endphp
      {{-- New chat di project pertama --}}
      <form method="POST" action="{{ route('sessions.store', $firstProject) }}" class="w-full">
        @csrf
        <button id="newChatBtn" class="w-full flex items-center justify-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition
                                     bg-black text-white border-black hover:bg-[#111]" type="submit">
          <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          New chat
        </button>
      </form>
    @else
      {{-- Belum ada project → buat project dulu (nama default) --}}
      <form method="POST" action="{{ route('projects.store') }}" class="w-full">
        @csrf
        <input type="hidden" name="name" value="My First Project">
        <button id="newChatBtn" class="w-full flex items-center justify-center gap-2 rounded-lg border px-3 py-2 text-sm font-semibold transition
                                     bg-black text-white border-black hover:bg-[#111]" type="submit">
          <svg viewBox="0 0 24 24" class="w-4 h-4" fill="none" stroke="currentColor">
            <path stroke-width="2" d="M12 4v16m8-8H4" />
          </svg>
          New project
        </button>
      </form>
    @endif

    <div class="mt-2 relative">
      <input id="chatSearch" placeholder="Search chats" class="w-full rounded-lg bg-white/5 border border-white/10 px-3 py-2 text-sm placeholder:text-gray-500
                    focus:outline-none focus:ring-2 focus:ring-emerald-600/60">
      <svg class="w-4 h-4 absolute right-2 top-1/2 -translate-y-1/2 text-gray-400" viewBox="0 0 24 24" fill="none">
        <path stroke="currentColor" stroke-width="2" d="m21 21-4.3-4.3M17 10a7 7 0 1 1-14 0 7 7 0 0 1 14 0Z" />
      </svg>
    </div>
  </div>
</div>

{{-- =========================
LIST (scroll area) — flat sessions + Aksi (Rename/Delete)
========================= --}}
<div id="sessionList" class="flex-1 overflow-y-auto min-h-0 px-2 py-2 space-y-1">
  @forelse($sessionsList as $s)
    @php
      $sid = (string) ($s['sid'] ?? '');
      $title = $s['title'] ?? 'Untitled';
      $active = $sid !== '' && $sid === $activeSessionId;
    @endphp

    <div
      class="group rounded-lg border border-white/5 {{ $active ? 'bg-white/10' : 'bg-white/[.03] hover:bg-white/[.06]' }} transition">
      <div class="flex items-center gap-2 pl-2 pr-1">
        <svg class="w-4 h-4 text-gray-400 shrink-0 mt-[2px]" viewBox="0 0 24 24" fill="none">
          <path stroke="currentColor" stroke-width="2" d="M4 6h16v10H7l-3 3V6z" />
        </svg>

        <a href="{{ url('/vip') }}?session={{ $sid }}"
          class="flex-1 py-2 text-sm truncate {{ $active ? 'text-emerald-300' : 'text-gray-200' }}">
          {{ $title }}
        </a>

        {{-- Aksi: Rename & Delete (mirip publik) --}}
        <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition">
          {{-- Tombol Rename → pakai modal global (public.partials.modals.rename) --}}
          <button type="button" class="p-1 rounded hover:bg-white/10" data-rename
            data-url="{{ route('sessions.update', $sid) }}" data-title="{{ $title }}" title="Rename">
            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
              <path stroke="currentColor" stroke-width="2" d="m3 21 3-3m0 0 11-11a2.828 2.828 0 1 1 4 4L10 22m-4-4 4 4" />
            </svg>
          </button>

          {{-- Delete session --}}
          <form method="POST" action="{{ route('sessions.destroy', $sid) }}" onsubmit="return confirm('Hapus chat ini?')">
            @csrf
            @method('DELETE')
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

{{-- =========================
FOOTER (tetap di bawah)
========================= --}}
<div id="sidebarFooter" class="mt-auto bg-[#0c1117] border-t border-white/10">
  <div class="px-3 py-2 space-y-2">
    <a href="{{ route('logout') }}"
      class="w-full inline-flex items-center gap-2 text-sm rounded-lg px-3 py-2 bg-white/5 hover:bg-white/10 border border-white/10 text-red-500 font-semibold">
      <svg class="w-4 h-4" viewBox="0 0 24 24" fill="none">
        <path stroke="currentColor" stroke-width="2"
          d="M15.75 9V5.25A2.25 2.25 0 0 0 13.5 3h-6A2.25 2.25 0 0 0 5.25 5.25v13.5A2.25 2.25 0 0 0 7.5 21h6a2.25 2.25 0 0 0 2.25-2.25V15M12 9l-3 3m0 0 3 3m-3-3h12" />
      </svg>
      Logout
    </a>
  </div>
</div>