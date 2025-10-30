(function loadHeader() {
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', loadHeader);
    return;
  }

  const container = document.getElementById('header-container');
  if (!container) {
    return;
  }

  if (container.dataset.loaded === 'true') {
    if (typeof window.initHeaderMenu === 'function') {
      window.initHeaderMenu();
    }
    return;
  }

  fetch('components/header.html', { credentials: 'include' })
    .then((res) => {
      if (!res.ok) {
        throw new Error('Failed to load header');
      }
      return res.text();
    })
    .then((html) => {
      container.innerHTML = html;
      container.dataset.loaded = 'true';

      const existingScript = document.querySelector('script[data-header-script="true"]');
      if (existingScript) {
        if (typeof window.initHeaderMenu === 'function') {
          window.initHeaderMenu();
        }
        return;
      }

      const script = document.createElement('script');
      script.src = 'js/header.js';
      script.dataset.headerScript = 'true';
      script.onload = () => {
        if (typeof window.initHeaderMenu === 'function') {
          window.initHeaderMenu();
        }
      };
      document.body.appendChild(script);
    })
    .catch((err) => {
      console.error('Header load error:', err);
    });
})();
