  </div>
</main>

<script>
// ── User dropdown: close on outside click ──────────────────────────────────
document.addEventListener('click', e => {
  document.querySelectorAll('.bp-user-menu.open').forEach(m => {
    if (!m.parentElement.contains(e.target)) m.classList.remove('open');
  });
});

// ── Confirm before delete ──────────────────────────────────────────────────
document.querySelectorAll('form[data-confirm]').forEach(f => {
  f.addEventListener('submit', e => { if (!confirm(f.dataset.confirm)) e.preventDefault(); });
});

// ── Toast auto-dismiss with slide-out animation ────────────────────────────
document.querySelectorAll('.toast[data-auto-dismiss]').forEach(t => {
  const ms = parseInt(t.dataset.autoDismiss, 10) || 4000;
  setTimeout(() => {
    t.classList.add('toast-leaving');
    setTimeout(() => t.remove(), 300);
  }, ms);
});

// ── Stat value count-up ────────────────────────────────────────────────────
document.querySelectorAll('.stat-card .value[data-count]').forEach(el => {
  const target = parseInt(el.dataset.count, 10) || 0;
  const duration = 700;
  const start = performance.now();
  function frame(now) {
    const t = Math.min(1, (now - start) / duration);
    const eased = 1 - Math.pow(1 - t, 3);
    el.textContent = Math.round(target * eased).toLocaleString('ar-EG');
    if (t < 1) requestAnimationFrame(frame);
  }
  requestAnimationFrame(frame);
});

// ── Scroll active nav into view ────────────────────────────────────────────
const activeNav = document.querySelector('.bp-nav-link.active');
if (activeNav) activeNav.scrollIntoView({inline: 'center', block: 'nearest'});

// ── Submit-button spinner state ────────────────────────────────────────────
document.querySelectorAll('form').forEach(f => {
  f.addEventListener('submit', () => {
    const btn = f.querySelector('button[type="submit"]');
    if (btn) {
      btn.dataset.originalText = btn.innerHTML;
      btn.disabled = true;
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جارٍ الحفظ...';
    }
  });
});
</script>
</body>
</html>
