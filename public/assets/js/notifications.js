document.addEventListener('DOMContentLoaded', () => {
    const badge = document.getElementById('notifBadge');
    const list = document.getElementById('notifList');
    const markAllBtn = document.getElementById('notifMarkAllRead');
    const notifButton = document.getElementById('notifButton');

    if (!badge || !list) return;

    const typeIcons = {
        tarefa: 'fa-list-check',
        avaliacao: 'fa-file-pen',
        meta: 'fa-bullseye',
        sistema: 'fa-circle-info',
    };

    function timeAgo(dateStr) {
        const diffMs = Date.now() - new Date(dateStr.replace(' ', 'T')).getTime();
        const diffMin = Math.floor(diffMs / 60000);
        if (diffMin < 1) return 'agora';
        if (diffMin < 60) return `há ${diffMin} min`;
        const diffH = Math.floor(diffMin / 60);
        if (diffH < 24) return `há ${diffH}h`;
        const diffD = Math.floor(diffH / 24);
        return `há ${diffD}d`;
    }

    function render(data) {
        if (data.unread > 0) {
            badge.textContent = data.unread > 9 ? '9+' : data.unread;
            badge.classList.remove('d-none');
        } else {
            badge.classList.add('d-none');
        }

        if (!data.items.length) {
            list.innerHTML = '<p class="text-muted small text-center py-3 mb-0">Nenhuma notificação por aqui.</p>';
            return;
        }

        list.innerHTML = data.items.map((n) => `
            <div class="notif-item ${n.isRead ? '' : 'is-unread'}">
                <div class="notif-item-icon"><i class="fa-solid ${typeIcons[n.type] || 'fa-circle-info'}"></i></div>
                <div class="notif-item-content">
                    <div class="notif-item-title">${escapeHtml(n.title)}</div>
                    <div class="notif-item-message">${escapeHtml(n.message)}</div>
                    <div class="notif-item-time">${timeAgo(n.date)}</div>
                </div>
            </div>
        `).join('');
    }

    function escapeHtml(str) {
        const div = document.createElement('div');
        div.textContent = str;
        return div.innerHTML;
    }

    async function loadNotifications() {
        try {
            const response = await fetch(window.APP_URL + '/notificacoes');
            const data = await response.json();
            render(data);
        } catch (err) {
            list.innerHTML = '<p class="text-muted small text-center py-3 mb-0">Não foi possível carregar.</p>';
        }
    }

    if (markAllBtn) {
        markAllBtn.addEventListener('click', async (e) => {
            e.preventDefault();
            try {
                await fetch(window.APP_URL + '/notificacoes/marcar-lidas', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'csrf_token=' + encodeURIComponent(window.CSRF_TOKEN),
                });
                loadNotifications();
            } catch (err) { /* silencioso */ }
        });
    }

    if (notifButton) {
        notifButton.addEventListener('click', () => loadNotifications(), { once: false });
    }

    loadNotifications();
});
