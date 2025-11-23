console.log('App.js loaded');

document.addEventListener('DOMContentLoaded', () => {
    console.log('DOM Content Loaded');
    console.log('Window Data:', { 
        users: window.usersData, 
        books: window.booksData 
    });

    document.querySelectorAll('[data-confirm]').forEach(element => {
        element.addEventListener('submit', event => {
            const message = element.getAttribute('data-confirm') || '¿Estás seguro?';
            if (!confirm(message)) {
                event.preventDefault();
            }
        });
    });
});
