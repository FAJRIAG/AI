{{-- Composer (input bar) dibagi antara public dan vip --}}
<footer class="border-t border-white/10 bg-gradient-to-b from-transparent relative">
  <div id="dropZone" class="absolute inset-x-0 bottom-full mb-2 mx-auto max-w-3xl hidden items-center justify-center p-6 border-2 border-dashed border-emerald-500 rounded-2xl bg-black/50 backdrop-blur z-10 transition-all">
     <span class="text-emerald-400 font-semibold tracking-wide">Lepaskan gambar di sini...</span>
  </div>
  <div class="composer-wrapper max-w-3xl mx-auto px-4 py-4 relative">
    <!-- Mode Selector -->
    <div id="modeSelector" class="flex flex-wrap gap-2 mb-3">
        @php
            $modes = [
                'default' => ['label' => 'JriGPT', 'icon' => '✨'],
                'koding' => ['label' => 'Koding', 'icon' => '💻'],
                'translate' => ['label' => 'Translate', 'icon' => '🌐'],
                'storyteller' => ['label' => 'Storyteller', 'icon' => '📖'],
            ];
            $selectedMode = $currentMode ?? 'default';
        @endphp
        @foreach($modes as $key => $m)
            <button type="button" data-mode="{{ $key }}" 
                    class="mode-btn flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-medium border transition-all 
                    {{ $selectedMode === $key ? 'bg-emerald-600/20 border-emerald-500/50 text-emerald-400' : 'bg-white/5 border-white/10 text-gray-400 hover:bg-white/10' }}">
                <span>{{ $m['icon'] }}</span>
                <span>{{ $m['label'] }}</span>
            </button>
        @endforeach
        <input type="hidden" id="selectedMode" value="{{ $selectedMode }}">
    </div>

    <!-- Image Preview -->
    <div id="attachmentPreviewContainer" class="hidden mb-3 relative inline-block">
      <img id="attachmentPreviewImg" src="" class="h-20 w-auto rounded-lg border border-white/20 object-cover shadow-lg">
      <button id="removeAttachmentBtn" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow-md transition-transform hover:scale-110">&times;</button>
    </div>

    <div class="unified-composer flex items-end gap-2 p-1.5 rounded-[24px] bg-[#1a1f2a]/80 backdrop-blur-xl border border-white/10 shadow-2xl transition-all focus-within:border-emerald-500/50 focus-within:ring-4 focus-within:ring-emerald-500/10">
      <input type="file" id="fileInput" accept="image/*,.pdf,.txt,.csv,.docx" class="hidden">
      <button id="attachBtn" class="shrink-0 p-3 text-gray-400 hover:text-emerald-400 transition-colors rounded-2xl hover:bg-white/5" title="Attach Files">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
      </button>
      
      <textarea id="prompt" rows="1" placeholder="Tulis pesan atau drop gambar ke sini…" 
        class="flex-1 resize-none bg-transparent border-none focus:ring-0 px-2 py-3 leading-6 text-gray-100 placeholder:text-gray-500 min-h-[48px] max-h-[200px]"></textarea>
      
      <button id="send"
        class="shrink-0 rounded-2xl bg-emerald-600 hover:bg-emerald-500 px-5 py-3 font-bold text-white transition-all hover:scale-[1.02] active:scale-[0.98] shadow-lg shadow-emerald-900/20 flex items-center gap-2">
        <span id="sendSpinner" class="hidden animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
        <span id="sendText">Kirim</span>
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round" class="opacity-80"><line x1="22" y1="2" x2="11" y2="13"/><polyline points="22 2 15 22 11 13 2 9 22 2"/></svg>
      </button>
      <button id="stop" class="shrink-0 hidden rounded-2xl bg-white/10 hover:bg-white/20 px-4 py-3 transition-colors text-sm font-medium">Stop</button>
    </div>
    
    <div class="mt-3 flex items-center justify-between px-2 text-[10px] text-gray-500 font-medium tracking-wide">
      <div class="flex items-center gap-3">
          <span>Enter ↵ Kirim</span>
          <span class="opacity-40">•</span>
          <span>Shift+Enter Baris Baru</span>
      </div>
      <div class="flex items-center gap-1 text-emerald-500/60 uppercase tracking-tighter">
          <svg xmlns="http://www.w3.org/2000/svg" width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
          Encrypted
      </div>
    </div>
  </div>
</footer>
