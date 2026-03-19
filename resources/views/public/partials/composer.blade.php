{{-- Composer (input bar) dibagi antara public dan vip --}}
<footer class="border-t border-white/10 bg-gradient-to-b from-transparent relative">
  <div id="dropZone" class="absolute inset-x-0 bottom-full mb-2 mx-auto max-w-3xl hidden items-center justify-center p-6 border-2 border-dashed border-emerald-500 rounded-2xl bg-black/50 backdrop-blur z-10 transition-all">
     <span class="text-emerald-400 font-semibold tracking-wide">Lepaskan gambar di sini...</span>
  </div>
  <div class="max-w-3xl mx-auto px-4 py-4 relative">
    <!-- Image Preview -->
    <div id="attachmentPreviewContainer" class="hidden mb-3 relative inline-block">
      <img id="attachmentPreviewImg" src="" class="h-20 w-auto rounded-lg border border-white/20 object-cover shadow-lg">
      <button id="removeAttachmentBtn" class="absolute -top-2 -right-2 bg-red-500 hover:bg-red-600 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs shadow-md transition-transform hover:scale-110">&times;</button>
    </div>

    <div class="flex items-end gap-2">
      <input type="file" id="fileInput" accept="image/*,.pdf,.txt,.csv,.docx" class="hidden">
      <button id="attachBtn" class="shrink-0 p-3 text-gray-400 hover:text-emerald-500 transition rounded-2xl bg-[#0c1117] border border-white/10 flex items-center justify-center">
        <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
      </button>
      <textarea id="prompt" rows="1" placeholder="Tulis pesan atau drop gambar/dokumen ke sini…" class="flex-1 resize-none rounded-2xl bg-[#0c1117] border border-white/10 focus:outline-none focus:ring-2 focus:ring-emerald-600/60
                   px-4 py-3 leading-6 text-gray-100 placeholder:text-gray-500"></textarea>
      <button id="send"
        class="shrink-0 rounded-2xl bg-emerald-600 hover:bg-emerald-500 px-4 py-3 font-semibold transition flex items-center gap-1 min-w-[5rem] justify-center">
        <span id="sendSpinner" class="hidden animate-spin h-5 w-5 border-2 border-white border-t-transparent rounded-full"></span>
        <span id="sendText">Kirim</span>
      </button>
      <button id="stop" class="shrink-0 hidden rounded-2xl bg-white/10 px-4 py-3">Stop</button>
    </div>
    <div class="mt-2 flex items-center justify-between text-[11px] text-gray-400">
      <div>Enter = kirim • Shift+Enter = baris baru • Mendukung attach/drag gambar</div>
    </div>
  </div>
</footer>
