{{-- resources/views/vip/partials/modals/project_settings.blade.php --}}
<div id="projectSettingsModal" class="fixed inset-0 z-[100] hidden overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="flex min-h-screen items-end justify-center px-4 pt-4 pb-20 text-center sm:block sm:p-0">
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="closeProjectSettings()"></div>

        <span class="hidden sm:inline-block sm:h-screen sm:align-middle" aria-hidden="true">&#8203;</span>

        <!-- Modal panel -->
        <div class="inline-block transform overflow-hidden rounded-2xl bg-[#0d1117] text-left align-bottom shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg sm:align-middle border border-white/10 ring-1 ring-white/10">
            <div class="bg-[#0d1117] px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex h-12 w-12 shrink-0 items-center justify-center rounded-full bg-emerald-500/10 sm:mx-0 sm:h-10 sm:w-10">
                        <svg class="h-6 w-6 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                        </svg>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                        <h3 class="text-lg font-bold leading-6 text-gray-100" id="modal-title">Workspace Settings</h3>
                        <p class="mt-1 text-xs text-gray-500 italic">Atur memori jangka panjang untuk workspace ini.</p>
                        
                        <form id="projectSettingsForm" method="POST" action="" class="mt-4 space-y-4">
                            @csrf
                            @method('PATCH')
                            
                            <div>
                                <label for="project_name" class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Nama Workspace</label>
                                <input type="text" name="name" id="project_name" class="w-full rounded-lg bg-white/5 border border-white/10 px-3 py-2 text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50">
                            </div>

                            <div>
                                <label for="project_description" class="block text-xs font-semibold uppercase tracking-wider text-gray-400 mb-1">Project Memory (Long-term Context)</label>
                                <textarea name="description" id="project_description" rows="5" placeholder="Contoh: Proyek ini menggunakan Laravel 12 dan Tailwind. JWA adalah framework CSS internal..." class="w-full rounded-lg bg-white/5 border border-white/10 px-3 py-2 text-sm text-gray-100 focus:outline-none focus:ring-2 focus:ring-emerald-500/50 resize-none"></textarea>
                                <p class="mt-1 text-[10px] text-gray-500 leading-relaxed">Informasi ini akan selalu diingat JriGPT di setiap chat dalam workspace ini.</p>
                            </div>
                        </form>

                        <form id="deleteProjectForm" method="POST" action="" class="mt-6 pt-4 border-t border-white/5">
                            @csrf
                            @method('DELETE')
                            <div class="flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-xs font-bold text-red-400 uppercase tracking-widest">Zona Bahaya</span>
                                    <span class="text-[10px] text-gray-500">Hapus workspace ini selamanya.</span>
                                </div>
                                <button type="button" onclick="confirmDeleteProject()" class="text-xs font-bold text-red-500 hover:text-red-400 underline decoration-red-500/30 underline-offset-4">Hapus Workspace</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="bg-white/[.02] px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-white/5">
                <button type="button" onclick="document.getElementById('projectSettingsForm').submit()" class="inline-flex w-full justify-center rounded-lg bg-emerald-600 px-4 py-2 text-sm font-bold text-white shadow-sm hover:bg-emerald-700 sm:ml-3 sm:w-auto transition-all duration-200">Simpan Perubahan</button>
                <button type="button" onclick="closeProjectSettings()" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white/5 px-4 py-2 text-sm font-semibold text-gray-300 shadow-sm ring-1 ring-inset ring-white/10 hover:bg-white/10 sm:mt-0 sm:w-auto transition-all">Batal</button>
            </div>
        </div>
    </div>
</div>

<script>
    let currentProjectId = null;

    function openProjectSettings(id, name, description) {
        currentProjectId = id;
        const modal = document.getElementById('projectSettingsModal');
        const form = document.getElementById('projectSettingsForm');
        const deleteForm = document.getElementById('deleteProjectForm');
        const nameInput = document.getElementById('project_name');
        const descInput = document.getElementById('project_description');
        
        form.action = `/projects/${id}`;
        deleteForm.action = `/projects/${id}`;
        nameInput.value = name;
        descInput.value = description || '';
        
        modal.classList.remove('hidden');
    }

    function confirmDeleteProject() {
        if (confirm('APAKAH KAMU YAKIN? Menghapus workspace ini akan menghapus semua chat di dalamnya selamanya.')) {
            document.getElementById('deleteProjectForm').submit();
        }
    }

    function closeProjectSettings() {
        document.getElementById('projectSettingsModal').classList.add('hidden');
    }
</script>
