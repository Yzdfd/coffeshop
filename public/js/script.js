/* Global frontend helpers.
 * - Auto refresh halaman kasir/pesanan
 * - Popup/notification via Bootstrap Toast (driven by flashdata)
 */

(function () {
  const LS_KEY_KASIR_AUTO_REFRESH = 'kasir_auto_refresh_enabled';

  function showToast({ type, title, message }) {
    // Bootstrap toast expected
    if (typeof bootstrap === 'undefined') return;

    const safeType = type === 'error' ? 'danger' : (type === 'warning' ? 'warning' : 'success');
    const toastEl = document.createElement('div');
    toastEl.className = `toast align-items-center text-bg-${safeType} border-0 show`;
    toastEl.setAttribute('role', 'alert');
    toastEl.setAttribute('aria-live', 'assertive');
    toastEl.setAttribute('aria-atomic', 'true');
    toastEl.style.position = 'fixed';
    toastEl.style.top = '20px';
    toastEl.style.right = '20px';
    toastEl.style.zIndex = '1080';
    toastEl.innerHTML = `
      <div class="d-flex">
        <div class="toast-body">
          <div class="fw-semibold mb-1">${escapeHtml(title || '')}</div>
          <div>${escapeHtml(message || '')}</div>
        </div>
        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
      </div>
    `;

    document.body.appendChild(toastEl);

    const toast = bootstrap.Toast.getOrCreateInstance(toastEl, { delay: 3500 });
    toast.show();
    toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
  }

  function escapeHtml(str) {
    return String(str)
      .replaceAll('&', '&amp;')
      .replaceAll('<', '&lt;')
      .replaceAll('>', '&gt;')
      .replaceAll('"', '&quot;')
      .replaceAll("'", '&#039;');
  }

  function initKasirAutoRefresh() {
    const onBtn = document.getElementById('autoRefreshOn');
    const offBtn = document.getElementById('autoRefreshOff');
    const statusEl = document.getElementById('autoRefreshStatus');

    // Only init on pages that render the toggle
    if (!onBtn || !offBtn || !statusEl) return;

    let intervalId = null;
    let isReloading = false;

    function setUI(enabled) {
      onBtn.classList.toggle('btn-success', enabled);
      onBtn.classList.toggle('btn-outline-success', !enabled);
      offBtn.classList.toggle('btn-danger', !enabled);
      offBtn.classList.toggle('btn-outline-danger', enabled);

      statusEl.textContent = enabled ? 'ON' : 'OFF';
    }

    function start() {
      if (intervalId) return;
      intervalId = setInterval(() => {
        if (document.visibilityState !== 'visible') return;
        if (isReloading) return;
        isReloading = true;
        window.location.reload();
      }, 10000);
    }

    function stop() {
      if (!intervalId) return;
      clearInterval(intervalId);
      intervalId = null;
    }

    const enabled = (localStorage.getItem(LS_KEY_KASIR_AUTO_REFRESH) === '1');
    setUI(enabled);
    if (enabled) start();

    onBtn.addEventListener('click', function () {
      localStorage.setItem(LS_KEY_KASIR_AUTO_REFRESH, '1');
      setUI(true);
      start();
    });

    offBtn.addEventListener('click', function () {
      localStorage.setItem(LS_KEY_KASIR_AUTO_REFRESH, '0');
      setUI(false);
      stop();
    });

    window.addEventListener('beforeunload', () => stop());
  }

  function initPopup() {
    const popup = window.CAFEF_POPUP;
    if (!popup || !popup.message) return;
    showToast({
      type: popup.type || 'success',
      title: popup.title || 'Info',
      message: popup.message,
    });
  }

  document.addEventListener('DOMContentLoaded', function () {
    initKasirAutoRefresh();
    initPopup();
  });
})();

