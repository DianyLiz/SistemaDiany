// Script para cambiar entre temas

(function() {
    'use strict';

    // Obtener el tema guardado en localStorage o usar el tema por defecto
    const savedTheme = localStorage.getItem('selectedTheme') || 'default';
    
    // Aplicar el tema guardado al cargar la página
    document.documentElement.className = 'theme-' + savedTheme;
    
    // Función para actualizar el icono del botón de tema
    function updateThemeIcon(theme) {
        const themeIcon = document.getElementById('themeIcon');
        if (themeIcon) {
            if (theme === 'dark') {
                themeIcon.className = 'fas fa-sun';
            } else {
                themeIcon.className = 'fas fa-moon';
            }
        }
    }
    
    // Marcar el tema activo en el menú
    function setActiveTheme(theme) {
        // Remover la clase active de todos los items
        document.querySelectorAll('.theme-option').forEach(item => {
            item.classList.remove('active');
        });
        
        // Agregar la clase active al tema seleccionado
        const activeItem = document.querySelector(`.theme-option[data-theme="${theme}"]`);
        if (activeItem) {
            activeItem.classList.add('active');
        }
        
        // Actualizar el icono del botón de tema
        updateThemeIcon(theme);
    }
    
    // Inicializar el tema activo
    setActiveTheme(savedTheme);
    
    // Manejar el botón de toggle de tema oscuro/claro
    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', function() {
            const currentTheme = localStorage.getItem('selectedTheme') || 'default';
            const newTheme = currentTheme === 'dark' ? 'default' : 'dark';
            
            // Cambiar el tema
            document.documentElement.className = 'theme-' + newTheme;
            
            // Guardar en localStorage
            localStorage.setItem('selectedTheme', newTheme);
            
            // Actualizar el tema activo
            setActiveTheme(newTheme);
        });
    }
    
    // Agregar event listeners a todos los items de tema
    document.querySelectorAll('.theme-option').forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            
            const theme = this.getAttribute('data-theme');
            
            // Cambiar el tema
            document.documentElement.className = 'theme-' + theme;
            
            // Guardar en localStorage
            localStorage.setItem('selectedTheme', theme);
            
            // Actualizar el tema activo
            setActiveTheme(theme);
            
            // Actualizar el icono del botón de toggle si cambia a dark o default
            if (theme === 'dark' || theme === 'default') {
                updateThemeIcon(theme);
            }
            
            // Mostrar notificación de cambio de tema (opcional, solo para temas del sidebar)
            if (theme !== 'dark' && theme !== 'default') {
                showThemeNotification(theme);
            }
        });
    });
    
    // Función para mostrar notificación de cambio de tema
    function showThemeNotification(theme) {
        // Crear elemento de notificación
        const notification = document.createElement('div');
        notification.className = 'alert alert-success alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3';
        notification.style.zIndex = '9999';
        notification.style.minWidth = '300px';
        notification.innerHTML = `
            <i class="fas fa-check-circle me-2"></i>
            Tema cambiado a: <strong>${getThemeName(theme)}</strong>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        `;
        
        document.body.appendChild(notification);
        
        // Remover después de 3 segundos
        setTimeout(() => {
            notification.remove();
        }, 3000);
    }
    
    // Función para obtener el nombre del tema
    function getThemeName(theme) {
        const themeNames = {
            'default': 'Azul (Default)',
            'dark': 'Oscuro',
            'green': 'Verde',
            'purple': 'Púrpura',
            'orange': 'Naranja',
            'red': 'Rojo'
        };
        return themeNames[theme] || theme;
    }
    
    // Agregar estilos para el item activo
    const style = document.createElement('style');
    style.textContent = `
        .sidebar-nav .theme-option.active {
            background-color: rgba(255, 255, 255, 0.3) !important;
            color: white !important;
            font-weight: 600;
        }
        .sidebar-nav .theme-option.active i {
            color: white !important;
        }
    `;
    document.head.appendChild(style);
    
})();

