// resources/js/sidebar-toggle.js
(() => {
  'use strict';

  /* =======================================================
     THEME (GLOBAL) — bekerja meski halaman tanpa sidebar
     ======================================================= */
  // Forcing dark theme globally
  const htmlEl = document.documentElement;
  htmlEl.setAttribute('data-theme', 'dark');

  const themeBtn = document.getElementById('themeToggle');
  if (themeBtn) {
    themeBtn.style.display = 'none';
  }

  /* =======================================================
     SIDEBAR TOGGLE — hanya berjalan jika elemen ada
     ======================================================= */
  const layout = document.getElementById('appLayout');
  const backdrop = document.getElementById('sidebarBackdrop');

  // Jika tidak ada layout/backdrop (mis. halaman auth lain), cukup selesai di theme.
  if (!layout || !backdrop) return;

  // Helpers
  const on = (el, ev, fn) => el && el.addEventListener(ev, fn);
  const $all = (sel, root = document) => Array.from(root.querySelectorAll(sel));
  const mm = window.matchMedia ? window.matchMedia('(min-width:768px)') : { matches: innerWidth >= 768 };
  const isDesktop = () => mm.matches;

  const lsGet = (k) => { try { return localStorage.getItem(k); } catch (e) { return null; } };
  const lsSet = (k, v) => { try { localStorage.setItem(k, v); } catch (e) { } };

  // Persist desktop collapse
  if (lsGet('sidebarCollapsed') === '1' && isDesktop()) {
    layout.classList.add('sidebar-hidden');
  }

  // Desktop collapse/expand
  function toggleDesktop() {
    layout.classList.toggle('sidebar-hidden');
    const hidden = layout.classList.contains('sidebar-hidden');
    lsSet('sidebarCollapsed', hidden ? '1' : '0');
  }

  // Mobile drawer open/close
  function openMobile(open) {
    if (open) {
      layout.classList.add('mobile-open');
      backdrop.classList.remove('hidden');
      document.body.style.overflow = 'hidden';
    } else {
      layout.classList.remove('mobile-open');
      backdrop.classList.add('hidden');
      document.body.style.overflow = '';
    }
  }
  const isMobileOpen = () => layout.classList.contains('mobile-open');
  const toggleMobile = () => openMobile(!isMobileOpen());
  const closeMobileIfOpen = () => { if (isMobileOpen()) openMobile(false); };

  // Anti double-tap (throttle)
  let lastTap = 0;
  const canTap = () => {
    const now = Date.now();
    if (now - lastTap < 200) return false;
    lastTap = now;
    return true;
  };

  // Trigger: semua elemen [data-toggle="sidebar"] (contoh: tombol "AI Chat" di topbar)
  document.addEventListener('click', (e) => {
    const t = e.target.closest('[data-toggle="sidebar"]');
    if (!t) return;
    if (!canTap()) return;

    if (isDesktop()) toggleDesktop();
    else toggleMobile();
  });

  // Backdrop close
  on(backdrop, 'click', () => openMobile(false));

  // ESC close (mobile)
  on(document, 'keydown', (e) => { if (e.key === 'Escape') closeMobileIfOpen(); });

  // Saat beralih ke desktop, pastikan drawer tertutup
  const onBpChange = () => { if (isDesktop()) closeMobileIfOpen(); };
  if (mm.addEventListener) mm.addEventListener('change', onBpChange);
  else if (mm.addListener) mm.addListener(onBpChange); // Safari lama
  on(window, 'resize', onBpChange);

  // Auto-close ketika klik item chat / footer actions (mobile)
  const rehook = () => {
    $all('#sessionList a, #sidebarFooter a, #sidebarFooter button').forEach(el => {
      if (el.__boundClose) return;
      el.__boundClose = true;
      on(el, 'click', () => closeMobileIfOpen());
    });
  };
  rehook();

  // Jika list/sidebar di-render ulang via JS, panggil ini:
  window.__rehookSidebarAutoClose = rehook;
})();
