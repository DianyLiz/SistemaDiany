// Script para el botón de tema oscuro en login

(function() {
    'use strict';

    const themeToggle = document.getElementById('themeToggleLogin');
    const themeIcon = document.getElementById('themeIconLogin');
    const loginBody = document.querySelector('.login-body');

    // Obtener el tema guardado o usar el tema claro por defecto
    const savedTheme = localStorage.getItem('loginTheme') || 'light';

    // Aplicar el tema guardado al cargar
    if (savedTheme === 'dark') {
        loginBody.classList.add('dark-theme');
        if (themeIcon) {
            themeIcon.className = 'fas fa-sun';
        }
    } else {
        loginBody.classList.remove('dark-theme');
        if (themeIcon) {
            themeIcon.className = 'fas fa-moon';
        }
    }

    // Manejar el click en el botón de tema
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const isDark = loginBody.classList.contains('dark-theme');
            
            if (isDark) {
                // Cambiar a tema claro
                loginBody.classList.remove('dark-theme');
                localStorage.setItem('loginTheme', 'light');
                if (themeIcon) {
                    themeIcon.className = 'fas fa-moon';
                }
            } else {
                // Cambiar a tema oscuro
                loginBody.classList.add('dark-theme');
                localStorage.setItem('loginTheme', 'dark');
                if (themeIcon) {
                    themeIcon.className = 'fas fa-sun';
                }
            }
        });
    }

})();

