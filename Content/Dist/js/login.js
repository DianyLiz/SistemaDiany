// Script para la página de login

(function() {
    'use strict';

    // Toggle para mostrar/ocultar contraseña
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('password');
    const eyeIcon = document.getElementById('eyeIcon');

    if (togglePassword && passwordInput && eyeIcon) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Cambiar icono
            if (type === 'password') {
                eyeIcon.className = 'fas fa-eye';
            } else {
                eyeIcon.className = 'fas fa-eye-slash';
            }
        });
    }

    // Manejar el envío del formulario
    const loginForm = document.getElementById('loginForm');
    const loginButton = loginForm ? loginForm.querySelector('.btn-login-modern') : null;

    if (loginForm) {
        loginForm.addEventListener('submit', function(e) {
            e.preventDefault();
            
            const username = document.getElementById('username').value;
            const password = document.getElementById('password').value;
            const rememberMe = document.getElementById('rememberMe').checked;

            // Validación básica
            if (!username || !password) {
                showAlert('Por favor, completa todos los campos', 'warning');
                return;
            }

            // Mostrar estado de carga
            if (loginButton) {
                loginButton.classList.add('loading');
                loginButton.disabled = true;
                loginButton.style.pointerEvents = 'none';
            }

            // Simular proceso de login (aquí iría la llamada al servidor)
            setTimeout(function() {
                // Guardar preferencia de recordar sesión
                if (rememberMe) {
                    localStorage.setItem('rememberMe', 'true');
                }

                // Simular login exitoso
                showAlert('Inicio de sesión exitoso. Redirigiendo...', 'success');
                
                // Redirigir a la página principal después de 1 segundo
                setTimeout(function() {
                    if (loginButton) {
                        loginButton.classList.remove('loading');
                        loginButton.disabled = false;
                        loginButton.style.pointerEvents = 'auto';
                    }
                    window.location.href = 'index.html';
                }, 1000);
            }, 1500);
        });
    }

    // Función para mostrar alertas
    function showAlert(message, type) {
        // Remover alertas anteriores
        const existingAlert = document.querySelector('.login-alert');
        if (existingAlert) {
            existingAlert.remove();
        }

        // Crear nueva alerta
        const alert = document.createElement('div');
        alert.className = `alert alert-${type} alert-dismissible fade show login-alert position-fixed top-0 start-50 translate-middle-x mt-3`;
        alert.style.zIndex = '9999';
        alert.style.minWidth = '300px';
        alert.style.maxWidth = '90%';
        
        const icon = type === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle';
        alert.innerHTML = `
            <i class="fas ${icon} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(alert);

        // Remover automáticamente después de 5 segundos
        setTimeout(() => {
            if (alert.parentNode) {
                alert.remove();
            }
        }, 5000);
    }

    // Verificar si hay una sesión guardada
    if (localStorage.getItem('rememberMe') === 'true') {
        // Aquí podrías cargar datos de usuario guardados
        // Por ejemplo: document.getElementById('username').value = localStorage.getItem('savedUsername');
    }

    // Agregar efectos de entrada a los campos
    const inputs = document.querySelectorAll('.form-control');
    inputs.forEach(input => {
        input.addEventListener('focus', function() {
            this.parentElement.parentElement.classList.add('focused');
        });

        input.addEventListener('blur', function() {
            if (!this.value) {
                this.parentElement.parentElement.classList.remove('focused');
            }
        });
    });

})();

