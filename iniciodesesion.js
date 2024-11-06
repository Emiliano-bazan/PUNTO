document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('loginForm');

    loginForm.addEventListener('submit', async (e) => {
        e.preventDefault(); // Previene el envío del formulario de manera tradicional

        const email = document.getElementById('email').value;
        const password = document.getElementById('password').value;

        try {
            const response = await fetch('iniciodesesion.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: new URLSearchParams({
                    email: email,
                    password: password
                })
            });

            const result = await response.text();

            if (result === 'success') {
                // Redirige a la página de indumentaria si el inicio de sesión es exitoso
                window.location.href = 'indumentaria.html';
            } else {
                // Muestra el mensaje de error
                alert(result);
            }
        } catch (error) {
            console.error('Error:', error);
            alert('Hubo un problema al procesar tu solicitud. Por favor, intenta de nuevo.');
        }
    });
});

