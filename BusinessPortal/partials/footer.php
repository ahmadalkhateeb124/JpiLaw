  </div>
</main>

<footer class="bp-footer">
  <div class="container-bp bp-footer-inner">
    <span>© <?= date('Y') ?> JPI Law Firm</span>
    <span class="dot">·</span>
    <span class="ver">v<?= e(APP_VERSION) ?></span>
    <span class="dot">·</span>
    <span class="dev-credit">تطوير <a href="https://webkoit.com" target="_blank" rel="noopener">Webkoit</a></span>
  </div>
</footer>

<script>
// ── User dropdown: close on outside click ──────────────────────────────────
document.addEventListener('click', e => {
  document.querySelectorAll('.bp-user-menu.open').forEach(m => {
    if (!m.parentElement.contains(e.target)) m.classList.remove('open');
  });
});

// ── Mobile drawer toggle ───────────────────────────────────────────────────
const _mobToggle = document.getElementById('bp-mobile-toggle');
const _mobNav    = document.getElementById('bp-nav');
const _mobBack   = document.getElementById('bp-mobile-backdrop');

function _closeMobileNav() {
  if (!_mobNav) return;
  _mobNav.classList.remove('open');
  _mobBack && _mobBack.classList.remove('open');
  _mobToggle && _mobToggle.classList.remove('open');
  _mobToggle && _mobToggle.setAttribute('aria-expanded', 'false');
  document.body.classList.remove('mobile-nav-open');
  if (_mobToggle) _mobToggle.querySelector('i').className = 'fa-solid fa-bars';
}
function _openMobileNav() {
  if (!_mobNav) return;
  _mobNav.classList.add('open');
  _mobBack && _mobBack.classList.add('open');
  _mobToggle && _mobToggle.classList.add('open');
  _mobToggle && _mobToggle.setAttribute('aria-expanded', 'true');
  document.body.classList.add('mobile-nav-open');
  if (_mobToggle) _mobToggle.querySelector('i').className = 'fa-solid fa-xmark';
}
if (_mobToggle) {
  _mobToggle.addEventListener('click', () => {
    _mobNav.classList.contains('open') ? _closeMobileNav() : _openMobileNav();
  });
}
if (_mobBack) _mobBack.addEventListener('click', _closeMobileNav);
document.addEventListener('keydown', e => { if (e.key === 'Escape') _closeMobileNav(); });
// Close drawer when a child link is clicked
document.querySelectorAll('.bp-nav-menu a').forEach(a => {
  a.addEventListener('click', () => { if (window.innerWidth <= 768) _closeMobileNav(); });
});
// Close drawer on viewport resize back to desktop
window.addEventListener('resize', () => { if (window.innerWidth > 768) _closeMobileNav(); });

// ── Top-nav dropdowns ──────────────────────────────────────────────────────
document.querySelectorAll('[data-nav-toggle]').forEach(btn => {
  btn.addEventListener('click', e => {
    e.stopPropagation();
    const menu = btn.nextElementSibling;
    const isOpen = menu.classList.contains('open');

    // Close all other open menus + reset their triggers
    document.querySelectorAll('.bp-nav-menu.open').forEach(m => m.classList.remove('open'));
    document.querySelectorAll('[data-nav-toggle][aria-expanded="true"]').forEach(b => b.setAttribute('aria-expanded', 'false'));

    if (!isOpen) {
      menu.classList.add('open');
      btn.setAttribute('aria-expanded', 'true');
    }
  });
});

// Close nav dropdowns on outside click
document.addEventListener('click', e => {
  if (!e.target.closest('.bp-nav-dropdown')) {
    document.querySelectorAll('.bp-nav-menu.open').forEach(m => m.classList.remove('open'));
    document.querySelectorAll('[data-nav-toggle][aria-expanded="true"]').forEach(b => b.setAttribute('aria-expanded', 'false'));
  }
});

// Close nav dropdowns on Escape
document.addEventListener('keydown', e => {
  if (e.key === 'Escape') {
    document.querySelectorAll('.bp-nav-menu.open').forEach(m => m.classList.remove('open'));
    document.querySelectorAll('[data-nav-toggle][aria-expanded="true"]').forEach(b => b.setAttribute('aria-expanded', 'false'));
  }
});

// ── Custom confirmation dialog ─────────────────────────────────────────────
function jpiConfirm(opts) {
  return new Promise(resolve => {
    const danger = opts.danger !== false;
    const backdrop = document.createElement('div');
    backdrop.className = 'jpi-confirm-backdrop';
    backdrop.innerHTML = `
      <div class="jpi-confirm-dialog" role="dialog" aria-modal="true">
        <div class="jpi-confirm-icon ${danger ? 'danger' : ''}">
          <i class="fa-solid ${danger ? 'fa-trash-can' : 'fa-circle-question'}"></i>
        </div>
        <h3>${opts.title || 'تأكيد الإجراء'}</h3>
        <p>${opts.message || 'هل أنت متأكد؟'}</p>
        <div class="jpi-confirm-actions">
          <button type="button" class="btn btn-outline" data-act="cancel">${opts.cancelText || 'إلغاء'}</button>
          <button type="button" class="btn ${danger ? 'btn-danger' : 'btn-gold'}" data-act="ok">${opts.confirmText || 'تأكيد'}</button>
        </div>
      </div>`;
    document.body.appendChild(backdrop);

    requestAnimationFrame(() => backdrop.classList.add('open'));

    const close = (result) => {
      backdrop.classList.add('closing');
      backdrop.classList.remove('open');
      setTimeout(() => {
        backdrop.remove();
        document.removeEventListener('keydown', escHandler);
        resolve(result);
      }, 180);
    };

    backdrop.querySelector('[data-act="cancel"]').addEventListener('click', () => close(false));
    backdrop.querySelector('[data-act="ok"]').addEventListener('click', () => close(true));
    backdrop.addEventListener('click', e => { if (e.target === backdrop) close(false); });

    const escHandler = e => {
      if (e.key === 'Escape') close(false);
      if (e.key === 'Enter') close(true);
    };
    document.addEventListener('keydown', escHandler);

    setTimeout(() => backdrop.querySelector('[data-act="ok"]').focus(), 80);
  });
}

// Hook custom confirm into all forms with data-confirm
document.querySelectorAll('form[data-confirm]').forEach(f => {
  f.addEventListener('submit', async e => {
    if (f.dataset._confirmed === '1') return;     // already confirmed → let it submit
    e.preventDefault();
    const ok = await jpiConfirm({
      title: 'تأكيد الحذف',
      message: f.dataset.confirm,
      confirmText: 'نعم، احذف',
      cancelText: 'إلغاء',
      danger: true,
    });
    if (ok) {
      f.dataset._confirmed = '1';
      f.submit();
    }
  });
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

// ── Submit-button spinner state ────────────────────────────────────────────
// Only kicks in for forms that ACTUALLY submit. If confirm dialog cancelled
// the submit (preventDefault), this handler skips so the button stays normal.
document.querySelectorAll('form').forEach(f => {
  f.addEventListener('submit', e => {
    if (e.defaultPrevented) return;   // confirm dialog still open / cancelled

    const btn = f.querySelector('button[type="submit"]');
    if (!btn) return;

    btn.dataset.originalText = btn.innerHTML;
    btn.disabled = true;

    const isIconOnly = btn.classList.contains('btn-icon');
    const isDelete   = f.hasAttribute('data-confirm') || btn.classList.contains('btn-danger');

    if (isIconOnly) {
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i>';
    } else if (isDelete) {
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جارٍ الحذف…';
    } else {
      btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> جارٍ الحفظ…';
    }
  });
});
</script>
</body>
</html>
