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
  output: 'html',
}));

// Mermaid Init
if (window.mermaid) {
  window.mermaid.initialize({
    startOnLoad: false,
    theme: 'dark',
    securityLevel: 'loose',
    fontFamily: 'Inter, sans-serif',
  });
}

function renderMath(el) {
  // Relying on marked-katex-extension for math rendering.
  // This function is kept empty to prevent calls from breaking.
}

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
  const selectedModeInput = document.getElementById('selectedMode');
  const modeButtons = document.querySelectorAll('.mode-btn');

  const layout = document.getElementById('appLayout');
  const backdrop = document.getElementById('sidebarBackdrop');
  const desktopBtn = document.getElementById('sidebarCollapseDesktop');
  const mobileBtn = document.getElementById('sidebarToggle');
  const themeBtn = document.getElementById('themeToggle');

  const renameModal = document.getElementById('renameModal');
  const renameForm = document.getElementById('renameForm');
  const renameInput = document.getElementById('renameInput');
  const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '';

  // Element Upload & Drag-Drop
  const dropZone = document.getElementById('dropZone');
  const fileInput = document.getElementById('fileInput');
  const attachBtn = document.getElementById('attachBtn');
  const attachmentPreviewContainer = document.getElementById('attachmentPreviewContainer');
  const attachmentPreviewImg = document.getElementById('attachmentPreviewImg');
  const removeAttachmentBtn = document.getElementById('removeAttachmentBtn');
  const sendSpinner = document.getElementById('sendSpinner');
  const sendText = document.getElementById('sendText');

  // State
  let controller = null;
  let lastUserMsg = '';
  let currentAttachmentUrl = null;
  let isUploading = false;

  // ===== Artifact Manager (Claude Style) =====
  const ArtifactManager = {
    panel: document.getElementById('artifactsSidebar'),
    iframe: document.getElementById('artifactIframe'),
    mermaidContainer: document.getElementById('artifactMermaid'),
    emptyState: document.getElementById('artifactEmpty'),
    title: document.getElementById('artifactTitle'),
    copyBtn: document.getElementById('artifactCopy'),
    downloadBtn: document.getElementById('artifactDownload'),
    closeBtn: document.getElementById('artifactClose'),
    currentContent: '',
    currentLang: '',

    init() {
      on(this.closeBtn, 'click', () => this.close());
      on(this.copyBtn, 'click', () => {
        navigator.clipboard.writeText(this.currentContent).then(() => {
          const original = this.copyBtn.innerHTML;
          this.copyBtn.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>';
          setTimeout(() => this.copyBtn.innerHTML = original, 1500);
        });
      });
      on(this.downloadBtn, 'click', () => this.download());
    },

    open() {
      if (!layout) return;
      layout.setAttribute('data-artifacts', 'open');
      this.panel.classList.remove('hidden');
      requestAnimationFrame(() => {
        this.panel.classList.remove('translate-x-full');
      });
    },

    close() {
      if (!layout) return;
      layout.setAttribute('data-artifacts', 'closed');
      this.panel.classList.add('translate-x-full');
      setTimeout(() => this.panel.classList.add('hidden'), 300);
    },

    update(content) {
      // Find code blocks: ```lang\ncode\n```
      const regex = /```(html|css|js|javascript|mermaid)[\s\n]+([\s\S]*?)(?:```|$)/gi;
      let lastBlock = null;
      let match;
      
      while ((match = regex.exec(content)) !== null) {
          lastBlock = { lang: match[1].toLowerCase(), code: match[2].trim() };
      }

      if (lastBlock) {
        this.render(lastBlock.code, lastBlock.lang);
        if (layout.getAttribute('data-artifacts') === 'closed') {
          this.open();
        }
      }
    },

    render(code, lang) {
      if (this.currentContent === code && this.currentLang === lang) return;
      this.currentContent = code;
      this.currentLang = lang;
      this.title.innerText = `Preview ${lang.toUpperCase()}`;
      
      this.emptyState.classList.add('hidden');
      document.getElementById('artifactContent').classList.add('has-artifact');

      if (lang === 'mermaid') {
        this.iframe.classList.add('hidden');
        this.mermaidContainer.classList.remove('hidden');
        this.renderMermaid(code);
      } else {
        this.mermaidContainer.classList.add('hidden');
        this.iframe.classList.remove('hidden');
        this.renderCode(code, lang);
      }
    },

    renderCode(code, lang) {
      let fullHtml = code;
      if (lang === 'css') {
        fullHtml = `<!DOCTYPE html><html><head><style>${code}</style></head><body>${this.getGenericTestHtml()}</body></html>`;
      } else if (lang === 'js' || lang === 'javascript') {
        fullHtml = `<!DOCTYPE html><html><body><script>${code}<\/script><p>JS is running. Check console if needed.</p></body></html>`;
      } else if (lang === 'html' && !code.toLowerCase().includes('<html')) {
        fullHtml = `<!DOCTYPE html><html><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0"><script src="https://cdn.tailwindcss.com"></script></head><body class="bg-white p-4 text-black">${code}</body></html>`;
      }

      const blob = new Blob([fullHtml], { type: 'text/html' });
      this.iframe.src = URL.createObjectURL(blob);
    },

    async renderMermaid(code) {
      if (!window.mermaid) return;
      try {
        const { svg } = await window.mermaid.render('mermaid-' + Date.now(), code);
        this.mermaidContainer.innerHTML = svg;
      } catch (e) {
        // Silently fail during typing
      }
    },

    getGenericTestHtml() {
       return `<div class="p-8">
          <h2 class="text-2xl font-bold mb-4">CSS Preview</h2>
          <button class="btn px-4 py-2 bg-blue-500 text-white rounded">Button</button>
          <div class="card p-4 border mt-4">Card Element</div>
        </div>`;
    },

    download() {
      const ext = this.currentLang === 'mermaid' ? 'svg' : 'html';
      const blob = new Blob([this.currentContent], { type: 'text/plain' });
      const url = URL.createObjectURL(blob);
      const a = document.createElement('a');
      a.href = url;
      a.download = `artifact-${Date.now()}.${ext}`;
      a.click();
    }
  };

  // ===== Mood Manager (Emotional Tone Sync) =====
  const MoodManager = {
    currentMood: 'calm',
    keywords: {
      panic: ['tolong', 'cepat', 'mati', 'error', 'gagal', 'woi', 'urgent', 'darurat', 'bahaya'],
      creative: ['buatkan', 'cerita', 'ide', 'saran', 'gambar', 'desain', 'puisi', 'lukis', 'imajinasi'],
      thoughtful: ['kenapa', 'bagaimana', 'jelaskan', 'mengapa', 'analisis', 'riset', 'mikir', 'logika']
    },
    
    analyze(text) {
      if (!text || text.length < 3) return 'calm';
      
      const lower = text.toLowerCase();
      
      // Panic detection (Aggressive)
      const isAllCaps = text.length > 5 && text === text.toUpperCase() && /[A-Z]/.test(text);
      const hasPanicKeywords = this.keywords.panic.some(k => lower.includes(k));
      if (isAllCaps || hasPanicKeywords) return 'panic';
      
      // Creative detection
      if (this.keywords.creative.some(k => lower.includes(k))) return 'creative';
      
      // Thoughtful detection
      if (this.keywords.thoughtful.some(k => lower.includes(k))) return 'thoughtful';
      
      return 'calm';
    },

    updateUI(mood) {
      if (this.currentMood === mood) return;
      this.currentMood = mood;
      document.documentElement.setAttribute('data-mood', mood);
    }
  };
  // Utils
  const on = (el, ev, fn) => el && el.addEventListener(ev, fn);
  const qsAll = (s, root = document) => Array.from(root.querySelectorAll(s));
  const scrollBottom = () => { if (scrollEl) scrollEl.scrollTo({ top: scrollEl.scrollHeight, behavior: 'smooth' }); };
  const autoResize = (el) => { if (!el) return; el.style.height = 'auto'; el.style.height = Math.min(el.scrollHeight, 200) + 'px'; };

  ArtifactManager.init();

  function hideIncompleteMath(text) {
    if (!text) return '';
    let openBlock = (text.match(/\\\[/g) || []).length;
    let closeBlock = (text.match(/\\\]/g) || []).length;
    if (openBlock > closeBlock) {
      const lastIndex = text.lastIndexOf('\\[');
      if (lastIndex !== -1) return text.substring(0, lastIndex);
    }
    let openInline = (text.match(/\\\(/g) || []).length;
    let closeInline = (text.match(/\\\)/g) || []).length;
    if (openInline > closeInline) {
      const lastIndex = text.lastIndexOf('\\(');
      if (lastIndex !== -1) return text.substring(0, lastIndex);
    }
    return text;
  }

  function preprocessMath(text) {
    if (!text) return '';
    // Fix blockquote leak: "> $$" -> "$$"
    text = text.replace(/^\s*>\s*\$\$/gm, () => '$$');
    text = text.replace(/\$\$>\s*/g, () => '$$');

    // Protect double backslashes inside math blocks so markdown doesn't collapse them
    // KaTeX needs \\ for newlines in matrices/aligned environments
    text = text.replace(/\\\\/g, '\\\\\\\\');

    // Convert \[ \] and \( \) to $$ and $ so marked-katex-extension can parse them
    text = text.replace(/\\\[/g, () => '$$');
    text = text.replace(/\\\]/g, () => '$$');
    text = text.replace(/\\\(/g, () => '$');
    text = text.replace(/\\\)/g, () => '$');

    // Auto-wrap bare \begin{...} \end{...} in $$
    const envs = ['pmatrix', 'bmatrix', 'vmatrix', 'Vmatrix', 'matrix', 'align', 'aligned', 'eqnarray', 'cases'];
    envs.forEach(env => {
      text = text.replace(new RegExp(`\\\\begin\\{${env}\\}`, 'g'), () => `$$\\begin{${env}}`);
      text = text.replace(new RegExp(`\\\\end\\{${env}\\}`, 'g'), () => `\\end{${env}}$$`);
    });

    // Clean up overlapping $$
    text = text.replace(/\$\$\s*\$\$/g, () => '$$');

    // Remove newlines inside $$ ... $$ so breaks:true doesn't insert <br> and ruin KaTeX parsing
    text = text.replace(/\$\$([\s\S]*?)\$\$/g, (match, inner) => {
      return '$$' + inner.replace(/\n/g, ' ') + '$$';
    });

    // Also for inline $ ... $ (no lookbehinds for older Safari support)
    text = text.replace(/(^|[^\$])\$([^\$]+)\$(?!\$)/g, (match, before, inner) => {
      return before + '$' + inner.replace(/\n/g, ' ') + '$';
    });

    return text;
  }

  const md = (s) => {
    const prepared = preprocessMath(s);
    const html = marked.parse(prepared || '');
    return DOMPurify.sanitize(html, {
      USE_PROFILES: { html: true, svg: true }, // KaTeX uses SVG for scalable elements
      ADD_TAGS: ['path'],
      ADD_ATTR: ['d', 'viewBox', 'preserveAspectRatio'],
      FORBID_TAGS: ['style', 'script'],
      KEEP_CONTENT: true,
      RETURN_DOM_FRAGMENT: false
    });
  };
  const escapeHtml = (s) => s.replace(/[&<>"']/g, m => ({ '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' }[m]));

  // openPreview removed and replaced by ArtifactManager.render/open

  function enhanceCode(scope) {
    scope.querySelectorAll('pre code').forEach(b => { try { hljs.highlightElement(b); } catch (e) { } });
    scope.querySelectorAll('pre').forEach(pre => {
      if (pre.querySelector('.code-header')) return; // already enhanced
      const code = pre.querySelector('code');
      const lang = code?.className?.match(/language-(\w+)/)?.[1] || '';
      const isPreviewable = ['html', 'css', 'js', 'javascript', 'mermaid'].includes(lang.toLowerCase());

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
          ArtifactManager.render(text, lang.toLowerCase());
          ArtifactManager.open();
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

  function renderUser(text, attachmentUrl = null) {
    const row = document.createElement('div'); row.className = 'fade-in flex';
    let imgHtml = '';
    if (attachmentUrl) {
      imgHtml = `<img src="${attachmentUrl}" class="max-w-xs mb-2 rounded border border-white/10" />`;
    }
    row.innerHTML = `<div class="ml-auto max-w-[80%] rounded-2xl bg-[#1a1f2a] px-4 py-2 ring-1 ring-white/10">
      ${imgHtml}
      <div class="whitespace-pre-wrap leading-6 text-gray-100">${escapeHtml(text)}</div></div>`;
    chatList.appendChild(row); scrollBottom();
  }
  function renderAI(content, replace = false) {
    if (replace && chatList.lastChild && chatList.lastChild.classList?.contains('streaming')) {
      const art = chatList.lastChild.querySelector('article');
      art.innerHTML = md(content);
      renderMath(art);
      enhanceCode(chatList.lastChild);
      ArtifactManager.update(content);
      scrollBottom();
      return;
    }
    const row = document.createElement('div');
    row.className = 'fade-in flex gap-3 streaming';
    row.innerHTML = `<div class="shrink-0 mt-1 size-8 rounded-full bg-[#1f2937] grid place-items-center text-xs font-semibold">JG</div>
                   <article class="ai-prose prose prose-sm prose-invert max-w-none flex-1 min-w-0">${md(content)}</article>`;
    chatList.appendChild(row);
    renderMath(row.querySelector('article'));
    enhanceCode(row);
    ArtifactManager.update(content);
    scrollBottom();
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
  on(scrollEl, 'scroll', () => { 
    const farFromBottom = (scrollEl.scrollHeight - scrollEl.scrollTop - scrollEl.clientHeight) > 300; 
    toBottom?.classList.toggle('hidden', !farFromBottom); 
  });
  on(toBottom, 'click', scrollBottom);
  on(promptEl, 'input', () => {
    autoResize(promptEl);
    const mood = MoodManager.analyze(promptEl.value);
    MoodManager.updateUI(mood);
  });

  // ===== Mode Logic =====
  modeButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const mode = btn.dataset.mode;
      if (selectedModeInput) selectedModeInput.value = mode;
      
      // Update UI
      modeButtons.forEach(b => {
        b.classList.remove('bg-emerald-600/20', 'border-emerald-500/50', 'text-emerald-400');
        b.classList.add('bg-white/5', 'border-white/10', 'text-gray-400', 'hover:bg-white/10');
      });
      
      btn.classList.add('bg-emerald-600/20', 'border-emerald-500/50', 'text-emerald-400');
      btn.classList.remove('bg-white/5', 'border-white/10', 'text-gray-400', 'hover:bg-white/10');
    });
  });

  // ===== Upload Logic =====
  const stopUploadState = () => { isUploading = false; sendSpinner.classList.add('hidden'); sendText.innerText = 'Kirim'; };
  const startUploadState = () => { isUploading = true; sendSpinner.classList.remove('hidden'); sendText.innerText = '...'; };
  const removeImage = () => {
    currentAttachmentUrl = null;
    attachmentPreviewContainer.classList.add('hidden');
    attachmentPreviewImg.src = '';
    if(fileInput) fileInput.value = '';
  };
  on(removeAttachmentBtn, 'click', removeImage);
  on(attachBtn, 'click', () => { if(!isUploading && fileInput) fileInput.click() });

  async function handleFileUpload(file) {
    if (!file) return;

    const isImage = file.type.startsWith('image/');
    const isDoc = file.type === 'application/pdf' || file.type === 'text/plain' || file.type === 'text/csv' || file.name.endsWith('.docx');

    if (!isImage && !isDoc) { 
      alert('Format file tidak didukung. Gunakan Gambar, PDF, TXT, atau CSV.'); 
      return; 
    }

    startUploadState();

    if (isImage) {
      attachmentPreviewImg.src = URL.createObjectURL(file);
    } else {
      // Placeholder icon untuk dokumen
      attachmentPreviewImg.src = 'https://cdn-icons-png.flaticon.com/512/2991/2991108.png';
    }

    attachmentPreviewContainer.classList.remove('hidden');
    
    const fd = new FormData(); fd.append('image', file);
    try {
      const res = await fetch('/public/upload-image', { method: 'POST', headers: { 'X-CSRF-TOKEN': csrfToken }, body: fd });
      const data = await res.json();
      if (res.ok) { 
          currentAttachmentUrl = data.attachment_url; 
          if (isImage) attachmentPreviewImg.src = data.url;
      }
      else { alert('Gagal mengupload file.'); removeImage(); }
    } catch(e) { alert('Gagal mengupload file.'); removeImage(); }
    finally { stopUploadState(); }
  }

  on(fileInput, 'change', e => { if(e.target.files && e.target.files[0]) handleFileUpload(e.target.files[0]) });

  // ===== Drag and Drop Logic =====
  if (dropZone) {
    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(ev => document.addEventListener(ev, e => e.preventDefault()));
    
    document.addEventListener('dragover', (e) => {
      if (isUploading) return;
      if (e.dataTransfer && e.dataTransfer.types.includes('Files')) {
        dropZone.classList.remove('hidden');
        dropZone.classList.add('flex');
      }
    });
    
    document.addEventListener('dragleave', (e) => {
      // Hide dropZone if drag leaves window
      if (e.relatedTarget === null || e.relatedTarget.nodeName === 'HTML') {
        dropZone.classList.add('hidden');
        dropZone.classList.remove('flex');
      }
    });

    document.addEventListener('drop', (e) => {
      dropZone.classList.add('hidden');
      dropZone.classList.remove('flex');
      if (isUploading) return;
      if (e.dataTransfer.files && e.dataTransfer.files.length > 0) {
        handleFileUpload(e.dataTransfer.files[0]);
      }
    });
  }

    function cleanAiContent(text) {
      if (!text) return '';
      if (text.includes('[HIDE_TOOL_CALL]')) {
        // Hapus blok tool call: { ... } yang diawali atau tidak oleh nama tool
        // Menggunakan regex yang lebih kuat untuk menangkap whitespace/newline di antara nama tool dan {
        text = text.replace(/(search_web|browse_url|tool_call_name)[\s\n]*\{[\s\S]*?\}/gi, '');
        text = text.replace(/\{[\s\S]*?"(query|url)"[\s\S]*?\}/gi, ''); // Fallback jika nama tool hilang

      // Deduplicate: Hanya tampilkan status [HIDE_TOOL_CALL] terakhir
      // Regex ini mencari blok [HIDE_TOOL_CALL]...⏳ yang diikuti oleh blok [HIDE_TOOL_CALL] lainnya
      const statusRegex = /\[HIDE_TOOL_CALL\][\s\S]*?⏳[\s\n]*/gi;
      const matches = text.match(statusRegex);
      if (matches && matches.length > 1) {
          // Ganti semua kecuali yang terakhir dengan string kosong
          let lastMatch = matches[matches.length - 1];
          let parts = text.split(statusRegex);
          // Gabungkan kembali teks tanpa status yang lama
          text = parts.join('') + lastMatch;
      }

      // Hapus marker itu sendiri
        text = text.replace(/\[HIDE\w*_TOOL_CALL\]/g, '');
      }
      return text;
    }

    // ===== Streaming =====
    async function sendMessage(contentOverride = null) {
      const content = (contentOverride ?? promptEl.value).trim();
      if ((!content && !currentAttachmentUrl) || controller || isUploading) return;

      lastUserMsg = content; 
      promptEl.value = ''; 
      autoResize(promptEl); 
      const attachedDbUrl = currentAttachmentUrl;
      const attachedPreviewSrc = attachmentPreviewImg.src;
      
      renderUser(content, attachedDbUrl ? attachedPreviewSrc : null);

      controller = new AbortController();
      sendBtn?.classList.add('opacity-60', 'pointer-events-none');
      stopBtn?.classList.remove('hidden');
      typingEl?.classList.remove('hidden');

      let ai = '';
      try {
        const mode = selectedModeInput?.value || 'default';
        const mood = MoodManager.currentMood;
        const payload = { content: content, attachment_url: attachedDbUrl, mode: mode, mood: mood };
        // Bersihkan UI staging SETELAH payload ditangkap
        removeImage(); 

        const resp = await fetch(`${window.location.origin}/public/stream/${encodeURIComponent(SID)}`, {
          method: 'POST', headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken }, body: JSON.stringify(payload), signal: controller.signal
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
            try {
              const obj = JSON.parse(data);
              if (evt === 'token') {
                ai += obj.token;
                
                let cleanedAi = cleanAiContent(ai);
                
                // HIDE_TOOL_CALL Filter: Bersihkan block JSON tool calls yg dibocorkan model
                if (cleanedAi.includes('[HIDE_TOOL_CALL]')) {
                    cleanedAi = cleanedAi.replace(/(search_web|browse_url|tool_call_name)[\s\S]*?\{[\s\S]*?\}/gi, '');
                    cleanedAi = cleanedAi.replace(/\[HIDE\w*_TOOL_CALL\]/g, '');
                }
                
                renderAI(hideIncompleteMath(cleanedAi), true);
              }
              if (evt === 'rename') {
                try {
                  const newTitle = obj.title;
                  const sid = obj.sid;
                  // Update sidebar
                  const sidebarLink = document.querySelector(`#sessionList a[href*="sid=${sid}"]`);
                  if (sidebarLink) sidebarLink.innerText = newTitle;
                  // Update active title
                  if (sid === SID) {
                    document.title = `${newTitle} - JriGPT`;
                  }
                  // Update rename modal data-title
                  const renameBtn = document.querySelector(`#sessionList [data-rename][data-sid="${sid}"]`);
                  if (renameBtn) renameBtn.setAttribute('data-title', newTitle);
                } catch (e) { }
              }
              if (evt === 'error') { renderAI('(Sedang sibuk atau API Error. Cobalah kembali nanti.)'); }
            } catch (e) { }
          }
        }
      } catch (e) {
        // >>> Perubahan: tampilkan pesan limit non-VIP saat gagal
        if (e.name !== 'AbortError') {
          renderAI('(Batas harian non-VIP untuk IP ini telah tercapai. Login VIP untuk akses tanpa batas.)');
        }
      } finally {
        if (ai) renderAI(cleanAiContent(ai), true);
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
      if (article) {
        article.innerHTML = md(el.textContent);
        renderMath(article);
      }
    });
    enhanceCode(document);
  } catch (e) { }
  try { promptEl?.focus(); } catch (e) { }
}
