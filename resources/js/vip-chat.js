// resources/js/vip-chat.js
import { marked } from 'marked';
import DOMPurify from 'dompurify';
import hljs from 'highlight.js';

// Configure marked options
marked.setOptions({
  breaks: true,       // convert \n to <br>
  gfm: true,          // GitHub Flavored Markdown (tables, code fences)
});

if (!window.__VIP_CHAT_INIT__) {
  window.__VIP_CHAT_INIT__ = true;

  const vipRoot = document.querySelector('[data-page="vip-chat"]');
  if (!vipRoot) { /* noop */ }
  else {
    const SESSION_ID = vipRoot.dataset.sessionId || '';
    const chatList = document.getElementById('chatList');
    const promptEl = document.getElementById('prompt');
    const sendBtn = document.getElementById('send');
    const stopBtn = document.getElementById('stop');
    const typingEl = document.getElementById('typing');
    const scrollEl = document.getElementById('scrollArea');
    const toBottom = document.getElementById('toBottom');
    // ===== Theme toggle dihapus (Dark Only) =====
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';
    const searchEl = document.getElementById('chatSearch');

    let controller = null;
    let lastUserMsg = '';

    const on = (el, ev, fn) => el && el.addEventListener(ev, fn);
    const scrollBottom = () => { if (scrollEl) scrollEl.scrollTop = scrollEl.scrollHeight; };
    const md = (s) => DOMPurify.sanitize(marked.parse(s || ''));
    const escapeHtml = (s) => (s || '').replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));
    const autoResize = (el) => { if (!el) return; el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 200) + 'px'; };

    // Preview modal
    function openPreview(codeText, lang) {
      const existing = document.getElementById('livePreviewModal');
      if (existing) existing.remove();

      let html = codeText;
      if (lang === 'css') html = `<style>${codeText}</style><div class="preview-msg">CSS dimuat. Tambahkan HTML untuk melihat hasilnya.</div>`;
      if (lang === 'js' || lang === 'javascript') html = `<script defer>${codeText}<\/script><div class="preview-msg">JavaScript dimuat.</div>`;

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
      const load = () => { iframe.contentDocument.open(); iframe.contentDocument.write(html); iframe.contentDocument.close(); };
      load();

      const closeModal = () => {
        modal.classList.remove('lp-open');
        modal.style.pointerEvents = 'none';
        setTimeout(() => modal.remove(), 400); // 400ms corresponds to transition time in css
      };

      modal.querySelector('.lp-close').addEventListener('click', closeModal);
      modal.querySelector('.lp-refresh').addEventListener('click', load);
      modal.querySelector('.lp-backdrop').addEventListener('click', closeModal);
      requestAnimationFrame(() => modal.classList.add('lp-open'));
    }

    function enhanceCode(scope) {
      scope.querySelectorAll('pre code').forEach(block => {
        try { hljs.highlightElement(block); } catch (e) { }
      });
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
          const text = code?.innerText || '';
          navigator.clipboard.writeText(text).then(() => {
            const btn = e.currentTarget;
            btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg> Tersalin!`;
            setTimeout(() => { btn.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Salin`; }, 1500);
          });
        });

        if (isPreviewable) {
          header.querySelector('.code-preview-btn').addEventListener('click', () => {
            openPreview(code?.innerText || '', lang.toLowerCase());
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
      row.innerHTML = `<div class="ml-auto max-w-[80%] rounded-2xl bg-[#1a1f2a] px-4 py-3 ring-1 ring-white/10">
        <div class="whitespace-pre-wrap leading-relaxed text-sm text-gray-100">${escapeHtml(text)}</div></div>`;
      chatList.appendChild(row); scrollBottom();
    }
    function renderAI(content, replace = false) {
      if (replace && chatList.lastChild && chatList.lastChild.classList?.contains('streaming')) {
        const art = chatList.lastChild.querySelector('article'); art.innerHTML = md(content); enhanceCode(chatList.lastChild); scrollBottom(); return;
      }
      const row = document.createElement('div'); row.className = 'fade-in flex gap-3 streaming';
      row.innerHTML = `<div class="shrink-0 mt-1 size-8 rounded-full bg-[#1f2937] grid place-items-center text-xs font-semibold">AI</div>
                     <article class="ai-prose prose prose-sm prose-invert max-w-none flex-1 min-w-0">${md(content)}</article>`;
      chatList.appendChild(row); enhanceCode(row); scrollBottom();
    }

    // Scroll UI + input
    on(scrollEl, 'scroll', () => { const nearBottom = (scrollEl.scrollHeight - scrollEl.scrollTop - scrollEl.clientHeight) < 80; toBottom?.classList.toggle('hidden', nearBottom); });
    on(toBottom, 'click', scrollBottom);
    on(promptEl, 'input', () => autoResize(promptEl));

    // Filter list
    on(searchEl, 'input', e => {
      const q = (e.target.value || '').toLowerCase();
      document.querySelectorAll('#sessionList > div').forEach(item => {
        const txt = item.querySelector('a')?.textContent?.toLowerCase() || '';
        item.style.display = txt.includes(q) ? '' : 'none';
      });
    });

    // Bind tombol rename (opsional)
    document.querySelectorAll('[data-rename]').forEach(btn => {
      btn.addEventListener('click', () => {
        const url = btn.getAttribute('data-url');
        const title = btn.getAttribute('data-title') || '';
        const modal = document.getElementById('renameModal');
        const form = document.getElementById('renameForm');
        const input = document.getElementById('renameInput');
        if (!modal || !form || !input) return;

        form.action = url;
        let m = form.querySelector('input[name="_method"]');
        if (!m) {
          m = document.createElement('input');
          m.type = 'hidden';
          m.name = '_method';
          form.appendChild(m);
        }
        m.value = 'PATCH';

        input.value = title;
        modal.classList.remove('hidden');
        setTimeout(() => input.focus(), 50);
      });
    });

    async function sendMessage(contentOverride = null) {
      const content = (contentOverride ?? promptEl.value).trim();
      if (!content || controller) return;

      if (!SESSION_ID) {
        renderAI('(Pilih / buat chat di sidebar dulu.)');
        return;
      }

      lastUserMsg = content; promptEl.value = ''; autoResize(promptEl); renderUser(content);

      controller = new AbortController();
      sendBtn?.classList.add('opacity-60', 'pointer-events-none');
      stopBtn?.classList.remove('hidden');
      typingEl?.classList.remove('hidden');

      let ai = '';
      try {
        const resp = await fetch(`${window.location.origin}/sessions/${encodeURIComponent(SESSION_ID)}/stream`, {
          method: 'POST',
          headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken },
          body: JSON.stringify({ content }),
          signal: controller.signal
        });

        // Debug yang jelas kalau non-OK
        if (!resp.ok || !resp.body) {
          const text = await resp.text().catch(() => '');
          throw new Error(`Bad response: ${resp.status} ${text?.slice(0, 200) || ''}`);
        }

        const reader = resp.body.getReader(); const decoder = new TextDecoder();
        while (true) {
          const { value, done } = await reader.read(); if (done) break;
          const chunk = decoder.decode(value, { stream: true });
          for (const block of chunk.split('\n\n')) {
            const lines = block.split('\n'); if (lines.length < 2) continue;
            const evt = lines[0].replace('event:', '').trim(); const data = lines[1].replace('data:', '').trim();
            try {
              const obj = JSON.parse(data);
              if (evt === 'token') { ai += obj.token; renderAI(ai, true); }
              if (evt === 'error') { renderAI('(Gagal menghubungi model. Periksa API key atau jaringan.)'); }
            } catch (e) { }
          }
        }
      } catch (e) {
        if (e.name !== 'AbortError') { renderAI('(Gagal menghubungi model. Periksa API key atau jaringan.)'); console.error(e); }
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
    on(promptEl, 'keydown', e => {
      if (e.key === 'Enter' && !e.shiftKey) { e.preventDefault(); sendMessage(); }
      if ((e.metaKey || e.ctrlKey) && e.key === 'Enter') { e.preventDefault(); sendMessage(); }
    });

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
}
