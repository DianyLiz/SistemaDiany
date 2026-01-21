// Script para controlar el sidebar lateral

(function() {
    'use strict';

    const sidebar = document.getElementById('sidebar');
    const menuToggle = document.getElementById('menuToggle');
    const closeSidebar = document.getElementById('closeSidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const mainContent = document.querySelector('.main-content');
    const topHeader = document.querySelector('.top-header');

    // Función para abrir el sidebar
    function openSidebar() {
        sidebar.classList.add('show');
        if (window.innerWidth < 992) {
            sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        } else {
            updateContentPosition();
        }
    }

    // Función para cerrar el sidebar
    function closeSidebarFunc() {
        sidebar.classList.remove('show');
        sidebarOverlay.classList.remove('show');
        document.body.style.overflow = '';
        mainContent.classList.remove('shifted', 'sidebar-collapsed');
        topHeader.classList.remove('shifted', 'sidebar-collapsed');
    }

    // Función para colapsar/expandir el sidebar
    function toggleCollapse() {
        if (window.innerWidth >= 992 && sidebar.classList.contains('show')) {
            sidebar.classList.toggle('collapsed');
            updateContentPosition();
        }
    }

    // Función para actualizar la posición del contenido según el estado del sidebar
    function updateContentPosition() {
        if (window.innerWidth >= 992 && sidebar.classList.contains('show')) {
            if (sidebar.classList.contains('collapsed')) {
                mainContent.classList.add('shifted', 'sidebar-collapsed');
                topHeader.classList.add('shifted', 'sidebar-collapsed');
            } else {
                mainContent.classList.add('shifted');
                mainContent.classList.remove('sidebar-collapsed');
                topHeader.classList.add('shifted');
                topHeader.classList.remove('sidebar-collapsed');
            }
        }
    }

    // Event listeners
    if (menuToggle) {
        menuToggle.addEventListener('click', function() {
            if (window.innerWidth >= 992) {
                // En pantallas grandes, toggle colapsar/expandir
                if (sidebar.classList.contains('show')) {
                    toggleCollapse();
                } else {
                    openSidebar();
                }
            } else {
                // En móviles, abrir/cerrar normalmente
                if (sidebar.classList.contains('show')) {
                    closeSidebarFunc();
                } else {
                    openSidebar();
                }
            }
        });
    }

    if (closeSidebar) {
        closeSidebar.addEventListener('click', closeSidebarFunc);
    }

    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebarFunc);
    }

    // Cerrar sidebar al hacer clic en un enlace (en móviles)
    const sidebarLinks = document.querySelectorAll('.sidebar-nav .nav-link:not([data-bs-toggle])');
    sidebarLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                closeSidebarFunc();
            }
        });
    });

    // Manejar el resize de la ventana
    let resizeTimer;
    window.addEventListener('resize', function() {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function() {
            const isSidebarOpen = sidebar.classList.contains('show');
            
            if (window.innerWidth >= 992) {
                // En pantallas grandes, ajustar el overlay y el contenido
                sidebarOverlay.classList.remove('show');
                document.body.style.overflow = '';
                
                if (isSidebarOpen) {
                    updateContentPosition();
                } else {
                    mainContent.classList.remove('shifted', 'sidebar-collapsed');
                    topHeader.classList.remove('shifted', 'sidebar-collapsed');
                }
            } else {
                // En móviles, remover estado colapsado y ajustar según el estado del sidebar
                sidebar.classList.remove('collapsed');
                if (isSidebarOpen) {
                    sidebarOverlay.classList.add('show');
                    document.body.style.overflow = 'hidden';
                } else {
                    sidebarOverlay.classList.remove('show');
                    document.body.style.overflow = '';
                }
                mainContent.classList.remove('shifted', 'sidebar-collapsed');
                topHeader.classList.remove('shifted', 'sidebar-collapsed');
            }
        }, 250);
    });

    // Prevenir que el collapse de Bootstrap cierre el sidebar
    const themeToggle = document.querySelector('[data-bs-target="#themesSubmenu"]');
    if (themeToggle) {
        themeToggle.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }

    // Inicializar según el tamaño de pantalla
    if (window.innerWidth >= 992) {
        // En pantallas grandes, mostrar el sidebar colapsado por defecto
        sidebar.classList.add('show', 'collapsed');
        updateContentPosition();
    }

})();

