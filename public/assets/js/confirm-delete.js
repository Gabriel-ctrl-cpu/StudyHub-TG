document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.form-confirm-delete').forEach((form) => {
        form.addEventListener('submit', (event) => {
            const message = form.getAttribute('data-confirm') || 'Tem certeza que deseja excluir este item?';
            if (!window.confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
