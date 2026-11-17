document.addEventListener('DOMContentLoaded', () => {
    const htmlEl = document.documentElement;

    const themeSwitch = document.getElementById('darkThemeSwitch');
    if (themeSwitch) {
        themeSwitch.addEventListener('change', () => {
            const next = themeSwitch.checked ? 'dark' : 'light';
            htmlEl.setAttribute('data-bs-theme', next);

            fetch(window.APP_URL + '/configuracoes/tema', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'theme=' + encodeURIComponent(next),
            }).catch(() => {});
        });
    }

    const STORAGE_KEY = 'studyhub_prefs';
    const savedPrefs = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');

    document.querySelectorAll('.pref-toggle').forEach((toggle) => {
        const key = toggle.dataset.pref;
        if (key in savedPrefs) {
            toggle.checked = savedPrefs[key];
        }
        toggle.addEventListener('change', () => {
            const prefs = JSON.parse(localStorage.getItem(STORAGE_KEY) || '{}');
            prefs[key] = toggle.checked;
            localStorage.setItem(STORAGE_KEY, JSON.stringify(prefs));
        });
    });
});
