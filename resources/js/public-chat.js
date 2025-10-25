// Jalan hanya di halaman public chat
const pageRoot = document.querySelector('[data-page="public-chat"]');
if (!pageRoot) { /* noop */ }
else {
  // Elemen
  const SID        = pageRoot.dataset.sid;
  const chatList   = document.getElementById('chatList');
  const promptEl   = document.getElementById('prompt');
  const sendBtn    = document.getElementById('send');
  const stopBtn    = document.getElementById('stop');
  const typingEl   = document.getElementById('typing');
  const scrollEl   = document.getElementById('scrollArea');
  const toBottom   = document.getElementById('toBottom');
  const regenBtn   = document.getElementById('regen');
  const searchEl   = document.getElementById('chatSearch');

  const layout     = document.getElementById('appLayout');
  const backdrop   = document.getElementById('sidebarBackdrop');
  const desktopBtn = document.getElementById('sidebarCollapseDesktop');
  const mobileBtn  = document.getElementById('sidebarToggle');
  const themeBtn   = document.getElementById('themeToggle');

  const renameModal = document.getElementById('renameModal');
  const renameForm  = document.getElementById('renameForm');
  const renameInput = document.getElementById('renameInput');
  const csrfToken   = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // State
  let controller = null;
  let lastUserMsg = '';

  // Utils
  const on=(el,ev,fn)=> el&&el.addEventListener(ev,fn);
  const qsAll=(s,root=document)=> Array.from(root.querySelectorAll(s));
  const scrollBottom=()=> { if(scrollEl) scrollEl.scrollTop = scrollEl.scrollHeight; };
  const md=(s)=> window.DOMPurify ? DOMPurify.sanitize(marked.parse(s||'')) : (s||'');
  const escapeHtml=(s)=> s.replace(/[&<>"']/g, m => ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#039;'}[m]));
  const autoResize=(el)=> { if(!el) return; el.style.height='auto'; el.style.height=Math.min(el.scrollHeight,200)+'px'; };

  function enhanceCode(scope){
    scope.querySelectorAll('pre code').forEach(b => { try{ window.hljs && hljs.highlightElement(b); }catch(e){} });
    scope.querySelectorAll('pre').forEach(pre => {
      pre.style.position='relative';
      if (pre.querySelector('.copy-btn')) return;
      const btn=document.createElement('button'); btn.className='copy-btn'; btn.type='button'; btn.textContent='Copy';
      btn.addEventListener('click',()=>{ const code=pre.querySelector('code')?.innerText||''; navigator.clipboard.writeText(code).then(()=>{ btn.textContent='Copied!'; setTimeout(()=>btn.textContent='Copy',900); });});
      pre.appendChild(btn);
    });
  }

  function renderUser(text){
    const row=document.createElement('div'); row.className='fade-in flex';
    row.innerHTML=`<div class="ml-auto max-w-[80%] rounded-2xl bg-[#1a1f2a] px-4 py-2 ring-1 ring-white/10">
      <div class="whitespace-pre-wrap leading-6 text-gray-100">${escapeHtml(text)}</div></div>`;
    chatList.appendChild(row); scrollBottom();
  }
  function renderAI(content, replace=false){
    if(replace && chatList.lastChild && chatList.lastChild.classList?.contains('streaming')){
      const art=chatList.lastChild.querySelector('article'); art.innerHTML=md(content); enhanceCode(chatList.lastChild); scrollBottom(); return;
    }
    const row=document.createElement('div'); row.className='fade-in flex gap-3 streaming';
    row.innerHTML=`<div class="shrink-0 mt-1 size-8 rounded-full bg-[#1f2937] grid place-items-center text-xs">AI</div>
                   <article class="prose prose-invert max-w-none">${md(content)}</article>`;
    chatList.appendChild(row); enhanceCode(row); scrollBottom();
  }

  // ===== Sidebar Toggle =====
  const collapsedPref = localStorage.getItem('sidebarCollapsed') === '1';
  if (collapsedPref && window.matchMedia('(min-width: 768px)').matches) {
    layout?.classList.add('sidebar-hidden');
  }
  function openMobileSidebar(open){
    if(!layout||!backdrop) return;
    if(open){ layout.classList.add('mobile-open'); backdrop.classList.remove('hidden'); document.body.style.overflow='hidden'; }
    else { layout.classList.remove('mobile-open'); backdrop.classList.add('hidden'); document.body.style.overflow=''; }
  }
  function toggleDesktopSidebar(){
    if(!layout) return;
    layout.classList.toggle('sidebar-hidden');
    const isHidden = layout.classList.contains('sidebar-hidden');
    localStorage.setItem('sidebarCollapsed', isHidden ? '1' : '0');
  }
  on(mobileBtn,'click',()=>openMobileSidebar(true));
  on(backdrop,'click',()=>openMobileSidebar(false));
  qsAll('#sessionList a').forEach(a=> on(a,'click',()=>openMobileSidebar(false)));
  on(desktopBtn,'click',toggleDesktopSidebar);
  on(window,'resize',()=>{ if(window.innerWidth>=768) openMobileSidebar(false); });

  // ===== Theme toggle (persist) =====
  try { if(localStorage.getItem('themeDark')==='1') document.documentElement.classList.add('dark'); } catch(e){}
  on(themeBtn,'click',()=>{ document.documentElement.classList.toggle('dark'); try{ localStorage.setItem('themeDark', document.documentElement.classList.contains('dark') ? '1':'0'); }catch(e){} });

  // ===== Scroll UI =====
  on(scrollEl,'scroll',()=>{ const nearBottom=(scrollEl.scrollHeight - scrollEl.scrollTop - scrollEl.clientHeight)<80; toBottom?.classList.toggle('hidden', nearBottom); });
  on(toBottom,'click',scrollBottom);
  on(promptEl,'input',()=>autoResize(promptEl));

  // ===== Streaming =====
  async function sendMessage(contentOverride=null){
    const content=(contentOverride ?? promptEl.value).trim();
    if(!content || controller) return;

    lastUserMsg=content; promptEl.value=''; autoResize(promptEl); renderUser(content);

    controller=new AbortController();
    sendBtn?.classList.add('opacity-60','pointer-events-none');
    stopBtn?.classList.remove('hidden');
    typingEl?.classList.remove('hidden');

    let ai='';
    try{
      const resp=await fetch(`${window.location.origin}/public/stream/${encodeURIComponent(SID)}`,{
        method:'POST', headers:{'Content-Type':'application/json','X-CSRF-TOKEN':csrfToken}, body:JSON.stringify({content}), signal:controller.signal
      });
      if(!resp.ok || !resp.body) throw new Error('Bad response');
      const reader=resp.body.getReader(); const decoder=new TextDecoder();
      while(true){
        const {value,done}=await reader.read(); if(done) break;
        const chunk=decoder.decode(value,{stream:true});
        for(const block of chunk.split('\n\n')){
          const lines=block.split('\n'); if(lines.length<2) continue;
          const evt=lines[0].replace('event:','').trim(); const data=lines[1].replace('data:','').trim();
          try{ const obj=JSON.parse(data); if(evt==='token'){ ai+=obj.token; renderAI(ai,true); } }catch(e){}
        }
      }
    } catch (e) {
      // >>> Perubahan: tampilkan pesan limit non-VIP saat gagal
      if (e.name !== 'AbortError') {
        renderAI('(Batas harian non-VIP untuk IP ini telah tercapai. Login VIP untuk akses tanpa batas.)');
      }
    } finally {
      typingEl?.classList.add('hidden');
      sendBtn?.classList.remove('opacity-60','pointer-events-none');
      stopBtn?.classList.add('hidden');
      if(chatList.lastChild) chatList.lastChild.classList.remove('streaming');
      controller=null;
    }
  }
  on(sendBtn,'click',()=>sendMessage());
  on(stopBtn,'click',()=>{ if(controller){ controller.abort(); } });
  on(regenBtn,'click',()=>{ if(lastUserMsg) sendMessage(lastUserMsg); });
  on(promptEl,'keydown',e=>{
    if(e.key==='Enter' && !e.shiftKey){ e.preventDefault(); sendMessage(); }
    if((e.metaKey||e.ctrlKey) && e.key==='Enter'){ e.preventDefault(); sendMessage(); }
  });

  // ===== Search =====
  on(searchEl,'input',e=>{
    const q=(e.target.value||'').toLowerCase();
    qsAll('#sessionList > div').forEach(item=>{
      const txt=item.querySelector('a')?.textContent?.toLowerCase() || '';
      item.style.display = txt.includes(q) ? '' : 'none';
    });
  });

  // ===== Modal rename (fungsi sudah ada) =====
  window.__openRename=(sid,title='')=>{
    if(!renameModal||!renameForm||!renameInput) return;
    renameForm.action = `${window.location.origin}/public/rename/${encodeURIComponent(sid)}`;
    renameInput.value = title;
    renameModal.classList.remove('hidden');
    setTimeout(()=>renameInput.focus(),50);
  };
  on(renameModal?.querySelector('[data-close-rename]'),'click',()=>renameModal.classList.add('hidden'));
  on(document,'keydown',e=>{ if(e.key==='Escape' && !renameModal?.classList.contains('hidden')) renameModal.classList.add('hidden'); });

  // ====== NEW: Bind tombol Rename dari sidebar (tanpa inline onclick) ======
  document.querySelectorAll('[data-rename]').forEach(btn => {
    btn.addEventListener('click', () => {
      const sid   = btn.getAttribute('data-sid') || '';
      const title = btn.getAttribute('data-title') || '';
      if (typeof window.__openRename === 'function') {
        window.__openRename(sid, title);
      }
    });
  });

  // Settings placeholder
  window.__openSettings=()=> alert('Settings placeholder: atur model, temperature, dsb.');

  // Init
  try{ enhanceCode(document); }catch(e){}
  try{ promptEl?.focus(); }catch(e){}
}
