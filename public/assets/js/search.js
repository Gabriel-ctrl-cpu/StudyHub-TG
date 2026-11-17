/**
 * assets/js/search.js
 * Busca global do header: à medida que o usuário digita (com debounce),
 * consulta /busca e mostra um dropdown com os resultados agrupados por
 * tipo (Matérias, Tarefas, Anotações, Avaliações). Clicar num resultado
 * leva direto para a tela de edição daquele item.
 */
document.addEventListener('DOMContentLoaded', () => {
    const wrapper = document.getElementById('globalSearch');
    const input = document.getElementById('globalSearchInput');
    const resultsBox = document.getElementById('globalSearchResults');

    if (!wrapper || !input || !resultsBox) return;

    let debounceTimer = null;
    let currentController = null;

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    function hideResults() {
        resultsBox.classList.add('d-none');
    }

    function showResults() {
        resultsBox.classList.remove('d-none');
    }

    function renderLoading() {
        resultsBox.innerHTML = '<p class="text-muted small text-center py-3 mb-0">Buscando...</p>';
        showResults();
    }

    function renderEmpty(term) {
        resultsBox.innerHTML = `<p class="text-muted small text-center py-3 mb-0">Nenhum resultado para "${escapeHtml(term)}".</p>`;
        showResults();
    }

    function renderResults(data) {
        if (!data.results.length) {
            renderEmpty(data.term);
            return;
        }

        const groups = {};
        data.results.forEach((r) => {
            if (!groups[r.group]) groups[r.group] = [];
            groups[r.group].push(r);
        });

        let html = '';
        Object.keys(groups).forEach((groupName) => {
            html += `<div class="search-group-label">${escapeHtml(groupName)}</div>`;
            groups[groupName].forEach((r) => {
                const dot = r.color ? `<span class="search-result-dot" style="background:${escapeHtml(r.color)}"></span>` : '';
                html += `
                    <a href="${escapeHtml(r.url)}" class="search-result-item">
                        <div class="search-result-icon"><i class="fa-solid ${escapeHtml(r.icon)}"></i></div>
                        <div class="search-result-content">
                            <div class="search-result-title">${dot}${escapeHtml(r.title)}</div>
                            ${r.subtitle ? `<div class="search-result-subtitle">${escapeHtml(r.subtitle)}</div>` : ''}
                        </div>
                    </a>
                `;
            });
        });

        resultsBox.innerHTML = html;
        showResults();
    }

    async function runSearch(term) {
        if (currentController) currentController.abort();
        currentController = new AbortController();

        try {
            const response = await fetch(`${window.APP_URL}/busca?q=${encodeURIComponent(term)}`, {
                signal: currentController.signal,
            });
            const data = await response.json();
            renderResults(data);
        } catch (err) {
            if (err.name !== 'AbortError') {
                resultsBox.innerHTML = '<p class="text-muted small text-center py-3 mb-0">Erro ao buscar.</p>';
                showResults();
            }
        }
    }

    input.addEventListener('input', () => {
        const term = input.value.trim();
        clearTimeout(debounceTimer);

        if (term.length < 2) {
            hideResults();
            return;
        }

        renderLoading();
        debounceTimer = setTimeout(() => runSearch(term), 300);
    });

    input.addEventListener('focus', () => {
        if (input.value.trim().length >= 2 && resultsBox.innerHTML.trim() !== '') {
            showResults();
        }
    });

    document.addEventListener('click', (event) => {
        if (!wrapper.contains(event.target)) {
            hideResults();
        }
    });

    input.addEventListener('keydown', (event) => {
        if (event.key === 'Escape') {
            hideResults();
            input.blur();
        }
    });
});
