document.getElementById('current-date').textContent = new Date().toLocaleDateString('es-ES', {
    year: 'numeric',
    month: 'long',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit'
});

setTimeout(() => {
    document.querySelector('.pending-overlay').style.animation = 'fadeOut 0.3s ease-out forwards';
    setTimeout(() => {
        document.querySelector('.pending-overlay').remove();
    }, 300);
}, 2000);
