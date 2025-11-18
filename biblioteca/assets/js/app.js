document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('submit', event => {
            const message = element.getAttribute('data-confirm') || '¿Estás seguro?';
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
