@extends('layouts.app')

@section('content')
  <div x-data="chatApp()" class="min-h-[calc(100vh-64px)] bg-gray-50">
    <div class="flex h-full">

      {{-- Sidebar --}}
      <aside class="w-72 border-r border-gray-200 bg-white p-3 space-y-3">
        <div class="flex items-center justify-between">
          <h2 class="font-semibold text-gray-800">Projects</h2>
          <form method="POST" action="{{ route('projects.store') }}">
            @csrf
            <input type="hidden" name="name" value="Project {{ now()->format('H:i:s') }}">
            <button
              class="inline-flex items-center gap-1 px-2 py-1 text-sm rounded bg-indigo-600 text-white hover:bg-indigo-700">+
              Project</button>
          </form>
        </div>

        <div class="space-y-2 overflow-y-auto max-h-[calc(100vh-160px)] pr-1">
          @forelse($projects as $p)
            <div class="rounded-md border border-gray-200">
              <div class="px-2 py-1.5 flex items-center justify-between">
                <form method="POST" action="{{ route('projects.update', $p) }}" class="flex-1">
                  @csrf @method('PATCH')
                  <input name="name" value="{{ $p->name }}"
                    class="w-full bg-transparent text-sm font-medium text-gray-800 focus:outline-none" />
                </form>
                <form method="POST" action="{{ route('projects.destroy', $p) }}" onsubmit="return confirm('Delete project?')"
                  class="pl-2">
                  @csrf @method('DELETE')
                  <button class="text-red-500 hover:underline text-xs">Delete</button>
                </form>
              </div>

              <div class="px-2 pb-2">
                <form method="POST" action="{{ route('sessions.store', $p) }}">@csrf
                  <button class="text-xs text-indigo-600 hover:underline" type="submit">+ Chat</button>
                </form>

                <div class="mt-2 space-y-1">
                  @foreach($p->sessions as $s)
                    <a href="{{ route('chat.dashboard', ['session' => $s->id]) }}"
                      class="block text-sm px-2 py-1 rounded {{ (optional($currentSession)->id === $s->id) ? 'bg-indigo-50 text-indigo-700' : 'text-gray-700 hover:bg-gray-100' }}">
                      {{ $s->title }}
                    </a>
                  @endforeach
                </div>
              </div>
            </div>
          @empty
            <p class="text-sm text-gray-500">Belum ada project.</p>
          @endforelse
        </div>
      </aside>

      {{-- Main --}}
      <main class="flex-1 flex flex-col">
        <div class="flex items-center justify-between px-4 py-3 border-b border-gray-200 bg-white">
          <div class="flex items-center gap-2">
            @if($currentSession)
              <form method="POST" action="{{ route('sessions.update', $currentSession) }}" class="flex items-center">
                @csrf @method('PATCH')
                <input name="title" value="{{ $currentSession->title }}"
                  class="px-2 py-1 rounded text-sm bg-transparent border border-transparent focus:border-indigo-400 text-gray-800" />
              </form>
            @endif
          </div>
          @if($currentSession)
            <form method="POST" action="{{ route('sessions.destroy', $currentSession) }}"
              onsubmit="return confirm('Delete chat?')">
              @csrf @method('DELETE')
              <button class="text-red-500 text-sm hover:underline">Delete Chat</button>
            </form>
          @endif
        </div>

        <div class="flex-1 overflow-y-auto" id="scrollArea">
          @if($currentSession)
            <template x-init="initSession({{ $currentSession->id }}, @json($currentSession->messages))"></template>
            <div id="msgList" class="max-w-3xl mx-auto px-4 py-6 space-y-4"></div>
            <div id="typing" class="max-w-3xl mx-auto px-4 pb-4 hidden">
              <div class="flex items-end gap-2 text-gray-400">
                <img src="https://ui-avatars.com/api/?name=JG" class="w-7 h-7 rounded-full" alt="JriGPT">
                <div class="bg-gray-100 rounded-lg px-3 py-2 text-sm text-gray-700">JriGPT is typing…</div>
              </div>
            </div>
          @else
            <div class="h-full grid place-items-center text-gray-500">
              <p>Pilih atau buat chat untuk memulai.</p>
            </div>
          @endif
        </div>

        @if($currentSession)
          <div class="border-t border-gray-200 bg-white">
            <div class="max-w-3xl mx-auto p-3 flex gap-2">
              <textarea x-model="input" x-on:keydown.enter.prevent="submit" placeholder="Tulis pesan…"
                class="flex-1 resize-none rounded border border-gray-300 bg-white text-gray-800 p-2"></textarea>
              <button x-bind:disabled="loading || !input.trim()" x-on:click="submit"
                class="px-4 py-2 rounded bg-indigo-600 text-white disabled:opacity-60">Kirim</button>
            </div>
          </div>
        @endif
      </main>
    </div>
  </div>

  <script src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
  <script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/dompurify@3.1.7/dist/purify.min.js"></script>
  <script>
    function chatApp() {
      return {
        sessionId: null, messages: [], input: '', loading: false, _aiStreamingNode: null,
        initSession(id, initial) { this.sessionId = id; this.messages = initial || []; this.renderAll(); this.scrollBottom(); },
        renderAll() { const list = document.getElementById('msgList'); list.innerHTML = ''; for (const m of this.messages) { list.appendChild(this.renderMsg(m)); } },
        renderMsg(m) {
          const wrap = document.createElement('div'); wrap.className = 'max-w-3xl';
          const row = document.createElement('div'); row.className = 'flex gap-2 items-start';
          const avatar = document.createElement('img'); avatar.className = 'w-7 h-7 rounded-full';
          const isUser = m.role === 'user';
          avatar.src = isUser ? 'https://ui-avatars.com/api/?name=U' : 'https://ui-avatars.com/api/?name=JG';
          const bubble = document.createElement('div');
          bubble.className = (m.role === 'user' ? 'bg-indigo-600 text-white ml-auto' : 'bg-gray-100 text-gray-800') + ' rounded-lg px-3 py-2 prose prose-invert max-w-full';
          let html = DOMPurify.sanitize(marked.parse(m.content || ''));
          bubble.innerHTML = html;
          if (m.role === 'user') { row.appendChild(bubble); row.appendChild(avatar); } else { row.appendChild(avatar); row.appendChild(bubble); }
          wrap.appendChild(row); return wrap;
        },
        addMsg(m) { this.messages.push(m); document.getElementById('msgList').appendChild(this.renderMsg(m)); },
        showTyping(v) { document.getElementById('typing').classList.toggle('hidden', !v); },
        scrollBottom() { const s = document.getElementById('scrollArea'); s.scrollTop = s.scrollHeight; },
        async submit() {
          if (!this.input.trim() || this.loading) return;
          const content = this.input; this.input = ''; this.addMsg({ role: 'user', content }); this.scrollBottom();
          this.loading = true; this.showTyping(true);

          const resp = await fetch(`/sessions/${this.sessionId}/stream`, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ content })
          });
          if (!resp.ok || !resp.body) { this.addMsg({ role: 'assistant', content: '(Error JriGPT)' }); this.loading = false; this.showTyping(false); return; }

          let aiContent = ''; const reader = resp.body.getReader(); const decoder = new TextDecoder();
          while (true) {
            const { value, done } = await reader.read(); if (done) break;
            const chunk = decoder.decode(value, { stream: true });
            for (const block of chunk.split("\n\n")) {
              const lines = block.split("\n"); if (lines.length < 2) continue;
              const evt = lines[0].replace('event:', '').trim(); const data = lines[1].replace('data:', '').trim();
              try {
                const obj = JSON.parse(data);
                if (evt === 'token') { aiContent += obj.token; this.renderStreaming(aiContent); }
                else if (evt === 'done') { this.finishStreaming(aiContent); }
              } catch (e) { }
            }
          }
          this.loading = false; this.showTyping(false);
        },
        renderStreaming(text) {
          if (!this._aiStreamingNode) {
            this._aiStreamingNode = { role: 'assistant', content: '' };
            const node = this.renderMsg(this._aiStreamingNode); this._aiStreamingNode._el = node;
            document.getElementById('msgList').appendChild(node);
          }
          this._aiStreamingNode.content = text;
          const bubble = this._aiStreamingNode._el.querySelector('.prose');
          bubble.innerHTML = DOMPurify.sanitize(marked.parse(text));
          this.scrollBottom();
        },
        finishStreaming(text) {
          if (this._aiStreamingNode) { this.messages.push({ role: 'assistant', content: text }); this._aiStreamingNode = null; }
        }
      }
    }

  </script>
@endsection