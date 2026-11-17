/**
 * assets/js/confirm-delete.js
 * Intercepta o envio de formulários marcados com a classe
 * `.form-confirm-delete` e exibe uma confirmação nativa antes de
 * enviar, usando o texto definido em `data-confirm`.
 */
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
