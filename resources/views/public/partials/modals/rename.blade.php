<div id="renameModal" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm">
  <div class="absolute inset-0 grid place-items-center p-4">
    <div class="w-full max-w-sm rounded-xl bg-[#0c1117] border border-white/10 p-4">
      <h3 class="font-semibold mb-3">Rename chat</h3>
      <form id="renameForm" method="POST" class="space-y-3">
        @csrf
        <input id="renameInput" name="title"
               class="w-full rounded-lg bg-white/5 border border-white/10 px-3 py-2 focus:outline-none focus:ring-2 focus:ring-emerald-600/60">
        <div class="flex justify-end gap-2">
          <button type="button" data-close-rename
                  class="px-3 py-2 text-sm rounded bg-white/5 border border-white/10 hover:bg-white/10">Cancel</button>
          <button class="px-3 py-2 text-sm rounded bg-emerald-600 hover:bg-emerald-500">Save</button>
        </div>
      </form>
    </div>
  </div>
</div>
