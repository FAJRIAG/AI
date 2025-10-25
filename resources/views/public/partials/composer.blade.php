{{-- Composer (input bar) untuk halaman chat publik) --}}
<footer class="border-t border-white/10 bg-gradient-to-b from-transparent to-[#0b0f15]/95">
  <div class="max-w-3xl mx-auto px-4 py-4">
    <div class="flex items-end gap-2">
      <textarea id="prompt" rows="1" placeholder="Tulis pesan…"
        class="flex-1 resize-none rounded-2xl bg-[#0c1117] border border-white/10 focus:outline-none focus:ring-2 focus:ring-emerald-600/60
               px-4 py-3 leading-6 text-gray-100 placeholder:text-gray-500"></textarea>

      <button id="send"
        class="shrink-0 rounded-2xl bg-emerald-600 hover:bg-emerald-500 px-4 py-3 font-semibold transition">
        Kirim
      </button>

      <button id="stop"
        class="shrink-0 hidden rounded-2xl bg-white/10 px-4 py-3">
        Stop
      </button>
    </div>

    <div class="mt-2 flex items-center justify-between text-[11px] text-gray-400">
      <div>Enter = kirim • Shift+Enter = baris baru • Markdown & code didukung</div>
      {{-- <button id="regen" class="px-2 py-1 rounded bg-white/5 border border-white/10 hover:bg-white/10">Regenerate</button> --}}
    </div>
  </div>
</footer>
