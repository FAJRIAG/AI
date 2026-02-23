import { marked } from 'marked';
import markedKatex from 'marked-katex-extension';
import DOMPurify from 'dompurify';
import hljs from 'highlight.js';

marked.setOptions({
  breaks: true,
  gfm: true,
});

marked.use(markedKatex({
  throwOnError: false,
  output: 'html'
}));

const pageRoot = document.querySelector('[data-page="public-chat"]');
if (!pageRoot) { /* noop */ }
else {
  // Elemen
  const SID = pageRoot.dataset.sid;
  const chatList = document.getElementById('chatList');
  const promptEl = document.getElementById('prompt');
  const sendBtn = document.getElementById('send');
  const stopBtn = document.getElementById('stop');
  const typingEl = document.getElementById('typing');
  const scrollEl = document.getElementById('scrollArea');
  const toBottom = document.getElementById('toBottom');
  const regenBtn = document.getElementById('regen');
  const searchEl = document.getElementById('chatSearch');

  const layout = document.getElementById('appLayout');
  const backdrop = document.getElementById('sidebarBackdrop');
  const desktopBtn = document.getElementById('sidebarCollapseDesktop');
  const mobileBtn = document.getElementById('sidebarToggle');
  const themeBtn = document.getElementById('themeToggle');

  const renameModal = document.getElementById('renameModal');
  const renameForm = document.getElementById('renameForm');
  const renameInput = document.getElementById('renameInput');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // State
  let controller = null;
  let lastUserMsg = '';

  // Utils
  const on = (el, ev, fn) => el && el.addEventListener(ev, fn);
  const qsAll = (s, root = document) => Array.from(root.querySelectorAll(s));
  const scrollBottom = () => { if (scrollEl) scrollEl.scrollTop = scrollEl.scrollHeight; };
  const autoResize = (el) => { if (!el) return; el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 200) + 'px'; };

  function normalizeLaTeX(text) {
    if (!text) return '';
    // Convert \[ ... \] to $$ ... $$
    text = text.replace(/\\\[([\s\S]*?)\\\]/g, (_, equation) => `\n$$\n${equation.trim()}\n$$\n`);
    // Convert \( ... \) to $ ... $
    text = text.replace(/\\\(([\s\S]*?)\\\)/g, (_, equation) => `$${equation.trim()}$`);
    return text;
  }

  const md = (s) => {
    const normalized = normalizeLaTeX(s);
    const html = marked.parse(normalized || '');
    return DOMPurify.sanitize(html, {
      USE_PROFILES: { html: true },
      ADD_TAGS: ['math', 'semantics', 'mrow', 'msub', 'msup', 'msup', 'msubsup', 'mover', 'munder', 'munderover', 'mtable', 'mtr', 'mtd', 'maligngroup', 'malignmark', 'msline', 'annotation'],
      ADD_ATTR: ['encoding'],
      FORBID_TAGS: ['style', 'script'],
      KEEP_CONTENT: true
    });
  };
  const escapeHtml = (s) => s.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));

  function openPreview(codeText, lang) {
    const existing = document.getElementById('livePreviewModal');
    if (existing) existing.remove();

    let html = codeText;
    if (lang === 'css') {
      html = `<!DOCTYPE html><html><head><style>${codeText}</style></head>
        <body style="padding:1rem; font-family:sans-serif; background:#fff; color:#000;">
          <h2>CSS Terapan</h2>
          <p>Tampilan ini memuat CSS yang ada di samping. Agar terlihat sempurna, elemen HTML terkait biasanya diperlukan.</p>
          <div class="test-element hover-me button btn card">Elemen Uji Coba (Memiliki class umum seperti btn, card, dll)</div>
        </body></html>`;
    } else if (lang === 'js' || lang === 'javascript') {
      html = `<script defer>${codeText}<\/script><div style="padding:1rem">JavaScript dimuat di background. Buka console browser untuk melihat log.</div>`;
    }

    const modal = document.createElement('div');
    modal.id = 'livePreviewModal';
    modal.innerHTML = `
      <div class="lp-backdrop"></div>
      <div class="lp-panel">
        <div class="lp-header">
          <span class="lp-title">
            <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="20" height="14" x="2" y="3" rx="2"/><line x1="8" x2="16" y1="21" y2="21"/><line x1="12" x2="12" y1="17" y2="21"/></svg>
            Live Preview
            <span class="lp-lang">${lang}</span>
          </span>
          <div class="lp-actions">
            <button class="lp-btn lp-refresh" title="Refresh">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M3 12a9 9 0 1 0 9-9 9.75 9.75 0 0 0-6.74 2.74L3 8"/><path d="M3 3v5h5"/></svg>
            </button>
            <button class="lp-btn lp-close" title="Tutup">
              <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18"/><path d="m6 6 12 12"/></svg>
            </button>
          </div>
        </div>
        <iframe class="lp-iframe" sandbox="allow-scripts allow-same-origin"></iframe>
      </div>
    `;
    document.body.appendChild(modal);

    const iframe = modal.querySelector('.lp-iframe');
    const load = () => {
      const blob = new Blob([html], { type: 'text/html' });
      iframe.src = URL.createObjectURL(blob);
    };
    load();

    const closeModal = () => {
      modal.classList.remove('lp-open');
      modal.style.pointerEvents = 'none';
      setTimeout(() => modal.remove(), 400);
    };

    modal.querySelector('.lp-close').addEventListener('click', closeModal);
    modal.querySelector('.lp-refresh').addEventListener('click', load);
    modal.querySelector('.lp-backdrop').addEventListener('click', closeModal);
    requestAnimationFrame(() => modal.classList.add('lp-open'));
  }

  function enhanceCode(scope) {
    scope.querySelectorAll('pre code').forEach(b => { try { hljs.highlightElement(b); } catch (e) { } });
    scope.querySelectorAll('pre').forEach(pre => {
      if (pre.querySelector('.code-header')) return; // already enhanced
      const code = pre.querySelector('code');
      const lang = code?.className?.match(/language-(\w+)/)?.[1] || '';
      const isPreviewable = ['html', 'css', 'js', 'javascript'].includes(lang.toLowerCase());

      // Build Gemini-style header
      const header = document.createElement('div');
      header.className = 'code-header';
      header.innerHTML = `
        <span class="code-lang">${lang || 'code'}</span>
        <div style="display:flex;gap:6px;align-items:center">
          ${isPreviewable ? `<button class="code-preview-btn" type="button">▶ Preview</button>` : ''}
          <button class="code-copy-btn" type="button">
            <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>
            Salin
          </button>
        </div>
      `;
      header.querySelector('.code-copy-btn').addEventListener('click', (e) => {
        // Find raw unparsed text if available to prevent DOM sanitization stripping
        const rawEl = pre.closest('.ai-raw-content') || pre.closest('.streaming')?.previousElementSibling;
        let text = code?.textContent || '';
        if (rawEl && rawEl.classList.contains('ai-raw-content')) {
          const blocks = rawEl.textContent.split('```');
          for (let i = 1; i < blocks.length; i += 2) {
            if (blocks[i] && blocks[i].toLowerCase().startsWith(lang.toLowerCase())) {
              text = blocks[i].substring(lang.length).trim();
              break;
            }
          }
        }

        navigator.clipboard.writeText(text).then(() => {
          const btn = e.currentTarget;
          btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Tersalin!`;
          setTimeout(() => { btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Salin`; }, 1500);
        });
      });

      if (isPreviewable) {
        header.querySelector('.code-preview-btn').addEventListener('click', () => {
          // Find raw unparsed text if available to prevent DOM sanitization stripping
          const rawEl = pre.closest('article')?.previousElementSibling;
          let text = code?.textContent || '';
          if (rawEl && rawEl.classList.contains('ai-raw-content')) {
            const blocks = rawEl.textContent.split('```');
            for (let i = 1; i < blocks.length; i += 2) {
              if (blocks[i] && blocks[i].toLowerCase().startsWith(lang.toLowerCase())) {
                text = blocks[i].substring(lang.length).trim();
                break;
              }
            }
          }
          openPreview(text, lang.toLowerCase());
        });
      }

      // Wrap pre+header in a container
      const wrapper = document.createElement('div');
      wrapper.className = 'code-block-wrapper';
      pre.parentNode.insertBefore(wrapper, pre);
      wrapper.appendChild(header);
      wrapper.appendChild(pre);
    });
  }

  function renderUser(text) {
    const row = document.createElement('div'); row.className = 'fade-in flex';
    row.innerHTML = `<div class="ml-auto max-w-[80%] rounded-2xl bg-[#1a1f2a] px-4 py-2 ring-1 ring-white/10">
      <div class="whitespace-pre-wrap leading-6 text-gray-100">${escapeHtml(text)}</div></div>`;
    chatList.appendChild(row); scrollBottom();
  }
  function renderAI(content, replace = false) {
    if (replace && chatList.lastChild && chatList.lastChild.classList?.contains('streaming')) {
      const art = chatList.lastChild.querySelector('article'); art.innerHTML = md(content); enhanceCode(chatList.lastChild); scrollBottom(); return;
    }
    const row = document.createElement('div'); row.className = 'fade-in flex gap-3 streaming';
    row.innerHTML = `<div class="shrink-0 mt-1 size-8 rounded-full bg-[#1f2937] grid place-items-center text-xs">AI</div>
                   <article class="prose prose-invert max-w-none">${md(content)}</article>`;
    chatList.appendChild(row); enhanceCode(row); scrollBottom();
  }

  // ===== Sidebar Toggle =====
  const collapsedPref = localStorage.getItem('sidebarCollapsed') === '1';
  if (collapsedPref && window.matchMedia('(min-width: 768px)').matches) {
    layout?.classList.add('sidebar-hidden');
  }
  function openMobileSidebar(open) {
    if (!layout || !backdrop) return;
    if (open) { layout.classList.add('mobile-open'); backdrop.classList.remove('hidden'); document.body.style.overflow = 'hidden'; }
    else { layout.classList.remove('mobile-open'); backdrop.classList.add('hidden'); document.body.style.overflow = ''; }
  }
  function toggleDesktopSidebar() {
    if (!layout) return;
    layout.classList.toggle('sidebar-hidden');
    const isHidden = layout.classList.contains('sidebar-hidden');
    localStorage.setItem('sidebarCollapsed', isHidden ? '1' : '0');
  }
  on(mobileBtn, 'click', () => openMobileSidebar(true));
  on(backdrop, 'click', () => openMobileSidebar(false));
  qsAll('#sessionList a').forEach(a => on(a, 'click', () => openMobileSidebar(false)));
  on(desktopBtn, 'click', toggleDesktopSidebar);
  on(window, 'resize', () => { if (window.innerWidth >= 768) openMobileSidebar(false); });

  // ===== Theme toggle (persist) =====
  try { if (localStorage.getItem('themeDark') === '1') document.documentElement.classList.add('dark'); } catch (e) { }
  on(themeBtn, 'click', () => { document.documentElement.classList.toggle('dark'); try { localStorage.setItem('themeDark', document.documentElement.classList.contains('dark') ? '1' : '0'); } catch (e) { } });

  // ===== Scroll UI =====
  on(scrollEl, 'scroll', () => { const nearBottom = (scrollEl.scrollHeight - scrollEl.scrollTop - scrollEl.clientHeight) < 80; toBottom?.classList.toggle('hidden', nearBottom); });
  on(toBottom, 'click', scrollBottom);
  on(promptEl, 'input', () => autoResize(promptEl));

  // ===== Streaming =====
  async function sendMessage(contentOverride = null) {
    const content = (contentOverride ?? promptEl.value).trim();
    if (!content || controller) return;

    lastUserMsg = content; promptEl.value = ''; autoResize(promptEl); renderUser(content);

    controller = new AbortController();
    sendBtn?.classList.add('opacity-60', 'pointer-events-none');
    stopBtn?.classList.remove('hidden');
    typingEl?.classList.remove('hidden');

    let ai = '';
    try {
      const resp = await fetch(`${window.location.origin}/public/stream/${encodeURIComponent(SID)}`, {
        method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify({ content }), signal: controller.signal
      });
      if (!resp.ok || !resp.body) throw new Error('Bad response');
      const reader = resp.body.getReader(); const decoder = new TextDecoder();
      let buffer = '';
      while (true) {
        const { value, done } = await reader.read(); if (done) break;
        buffer += decoder.decode(value, { stream: true });

        let boundary = buffer.indexOf('\n\n');
        while (boundary !== -1) {
          const block = buffer.slice(0, boundary).trim();
          buffer = buffer.slice(boundary + 2);
          boundary = buffer.indexOf('\n\n');

          if (!block) continue;
          const lines = block.split('\n');
          if (lines.length < 2) continue;
          const evt = lines[0].replace('event:', '').trim();
          const data = lines.slice(1).join('\n').replace(/^data:\s*/, '').trim();
          try { const obj = JSON.parse(data); if (evt === 'token') { ai += obj.token; renderAI(ai, true); } } catch (e) { }
        }
      }
    } catch (e) {
      // >>> Perubahan: tampilkan pesan limit non-VIP saat gagal
      if (e.name !== 'AbortError') {
        renderAI('(Batas harian non-VIP untuk IP ini telah tercapai. Login VIP untuk akses tanpa batas.)');
      }
    } finally {
      typingEl?.classList.add('hidden');
      sendBtn?.classList.remove('opacity-60', 'pointer-events-none');
      stopBtn?.classList.add('hidden');
      if (chatList.lastChild) chatList.lastChild.classList.remove('streaming');
      controller = null;
    }
  }
  on(sendBtn, 'click', () => sendMessage());
  on(stopBtn, 'click', () => { if (controller) { controller.abort(); } });
  on(regenBtn, 'click', () => { if (lastUserMsg) sendMessage(lastUserMsg); });
  on(promptEl, 'keydown', e => {
    if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
    if ((e.metaKey || e.ctrlKey) && e.key === 'Enter') { e.preventDefault(); sendMessage(); }
  });

  // ===== Search =====
  on(searchEl, 'input', e => {
    const q = (e.target.value || '').toLowerCase();
    qsAll('#sessionList > div').forEach(item => {
      const txt = item.querySelector('a')?.textContent?.toLowerCase() || '';
      item.style.display = txt.includes(q) ? '' : 'none';
    });
  });

  // ===== Modal rename (fungsi sudah ada) =====
  window.__openRename = (sid, title = '') => {
    if (!renameModal || !renameForm || !renameInput) return;
    renameForm.action = `${window.location.origin}/public/rename/${encodeURIComponent(sid)}`;
    renameInput.value = title;
    renameModal.classList.remove('hidden');
    setTimeout(() => renameInput.focus(), 50);
  };
  on(renameModal?.querySelector('[data-close-rename]'), 'click', () => renameModal.classList.add('hidden'));
  on(document, 'keydown', e => { if (e.key === 'Escape' && !renameModal?.classList.contains('hidden')) renameModal.classList.add('hidden'); });

  // ====== NEW: Bind tombol Rename dari sidebar (tanpa inline onclick) ======
  document.querySelectorAll('[data-rename]').forEach(btn => {
    btn.addEventListener('click', () => {
      const sid = btn.getAttribute('data-sid') || '';
      const title = btn.getAttribute('data-title') || '';
      if (typeof window.__openRename === 'function') {
        window.__openRename(sid, title);
      }
    });
  });

  // Settings placeholder
  window.__openSettings = () => alert('Settings placeholder: atur model, temperature, dsb.');

  // Init
  try {
    document.querySelectorAll('.ai-raw-content').forEach(el => {
      const article = el.nextElementSibling;
      if (article) article.innerHTML = md(el.textContent);
    });
    enhanceCode(document);
  } catch (e) { }
  try { promptEl?.focus(); } catch (e) { }
}
