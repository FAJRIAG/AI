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
      <main class="flex-1 flex flex-col relative" 
            @dragover.prevent="isDragging = true" 
            @dragleave.prevent="if($event.relatedTarget === null || $event.relatedTarget.nodeName === 'HTML') isDragging = false" 
            @drop.prevent="isDragging = false; handleFileDrop($event)">
        <!-- Dropzone Overlay -->
        <div x-show="isDragging" class="absolute inset-0 bg-indigo-50/90 z-50 flex items-center justify-center border-4 border-dashed border-indigo-400 m-4 rounded-xl backdrop-blur-sm" style="display: none;">
          <span class="text-indigo-600 font-bold text-xl drop-shadow-sm pointer-events-none">Lepaskan gambar di sini...</span>
        </div>
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
            <div class="max-w-3xl mx-auto p-3">
              <!-- Image Preview -->
              <div x-show="attachmentPreview" class="mb-2 relative inline-block">
                <img :src="attachmentPreview" class="h-20 w-auto rounded border border-gray-300 object-cover">
                <button @click="removeImage" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs hover:bg-red-600">&times;</button>
              </div>
              <div class="flex gap-2 items-end">
                <input type="file" x-ref="imageInput" @change="handleFileUpload" accept="image/*" class="hidden">
                <button @click="$refs.imageInput.click()" type="button" class="p-2 text-gray-500 hover:text-indigo-600 rounded" :disabled="isUploading">
                  <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                  </svg>
                </button>
                <textarea x-model="input" x-on:keydown.enter.prevent="submit" placeholder="Tulis pesan…"
                  class="flex-1 resize-none rounded border border-gray-300 bg-white text-gray-800 p-2"></textarea>
                <button x-bind:disabled="loading || isUploading || (!input.trim() && !attachmentUrl)" x-on:click="submit"
                  class="px-4 py-2 rounded bg-indigo-600 text-white disabled:opacity-60 flex items-center justify-center min-w-[5rem]">
                  <span x-show="isUploading" class="animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full mr-1"></span>
                  <span x-text="isUploading ? '...' : 'Kirim'"></span>
                </button>
              </div>
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
        attachmentUrl: null, attachmentPreview: null, isUploading: false, isDragging: false,

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
          if (m.attachment_url) {
            const img = document.createElement('img');
            img.src = '/storage/' + m.attachment_url;
            img.className = 'max-w-xs mt-2 rounded border border-gray-200';
            bubble.appendChild(img);
          }
          if (m.role === 'user') { row.appendChild(bubble); row.appendChild(avatar); } else { row.appendChild(avatar); row.appendChild(bubble); }
          wrap.appendChild(row); return wrap;
        },
        addMsg(m) { this.messages.push(m); document.getElementById('msgList').appendChild(this.renderMsg(m)); },
        showTyping(v) { document.getElementById('typing').classList.toggle('hidden', !v); },
        scrollBottom() { const s = document.getElementById('scrollArea'); s.scrollTop = s.scrollHeight; },
        handleFileDrop(e) {
          if (this.isUploading) return;
          if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
            this.handleFileUpload({ target: { files: e.dataTransfer.files } });
          }
        },
        async handleFileUpload(e) {
          const file = e.target.files[0];
          if (!file) return;
          this.isUploading = true;
          this.attachmentPreview = URL.createObjectURL(file);
          
          const fd = new FormData();
          fd.append('image', file);
          
          try {
            const res = await fetch('/chat/upload-image', {
              method: 'POST',
              headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
              body: fd
            });
            const data = await res.json();
            if (res.ok) {
              this.attachmentUrl = data.attachment_url;
            } else {
              alert('Gagal mengupload gambar.');
              this.removeImage();
            }
          } catch (err) {
            alert('Gagal mengupload gambar.');
            this.removeImage();
          } finally {
            this.isUploading = false;
          }
        },
        removeImage() {
          this.attachmentUrl = null;
          this.attachmentPreview = null;
          this.$refs.imageInput.value = '';
        },
        async submit() {
          if ((!this.input.trim() && !this.attachmentUrl) || this.loading || this.isUploading) return;
          const content = this.input; 
          const attachment = this.attachmentUrl;
          this.input = ''; 
          this.removeImage();
          this.addMsg({ role: 'user', content, attachment_url: attachment }); 
          this.scrollBottom();
          this.loading = true; this.showTyping(true);

          const resp = await fetch(`/sessions/${this.sessionId}/stream`, {
            method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ content, attachment_url: attachment })
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