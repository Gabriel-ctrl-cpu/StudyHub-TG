document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('themeToggle');
    const htmlEl = document.documentElement;

    if (!toggleBtn) return;

    toggleBtn.addEventListener('click', () => {
        const current = htmlEl.getAttribute('data-bs-theme') || 'light';
        const next = current === 'light' ? 'dark' : 'light';
        htmlEl.setAttribute('data-bs-theme', next);

        fetch(window.APP_URL + '/configuracoes/tema', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: 'theme=' + encodeURIComponent(next),
        }).catch(() => {
        });
    });
});
