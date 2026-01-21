</div>
        
        <!-- Footer -->
        <footer class="footer bg-dark text-light mt-auto py-4">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="mb-3">
                        <img class="img-logo-selection" width="40" height="40" src="../Content/Demo/img/LOGO_TP_BLANCO_bg.png" alt=""></i>  <span style="font-weight:700;font-size:1.5rem">TicketPro</span>
                    </h5>
                    <p class="text-muted">Su solución permite mejorar la eficiencia, reducir tiempos de espera y optimizar los procesos en instituciones con alto volumen de usuarios</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5 class="mb-3">Sobre Nosotros</h5>
                    <ul class="list-unstyled">
    
                        <li>
                            <a href="#quienesContent" 
                            data-bs-toggle="collapse"
                            aria-expanded="false"
                            class="d-block">
                                Quiénes Somos
                            </a>

                            <ul class="collapse ps-3" id="quienesContent">
                                <li>
                                    TicketPro es una plataforma innovadora diseñada para gestionar y optimizar la 
                                    atención al cliente en instituciones con alto volumen de usuarios.
                                </li>
                            </ul>
                        </li>

                      
                        <li>
                            <a href="#misionContent" 
                            data-bs-toggle="collapse"
                            aria-expanded="false"
                            class="d-block">
                                Misión
                            </a>

                            <ul class="collapse ps-3" id="misionContent">
                                <li>
                                    Brindar soluciones tecnológicas eficientes que mejoren la experiencia del cliente
                                    y optimicen los procesos de atención en instituciones de alto tráfico.
                                </li>
                            </ul>
                        </li>

                       
                        <li>
                            <a href="#visionContent" 
                            data-bs-toggle="collapse"
                            aria-expanded="false"
                            class="d-block">
                                Visión
                            </a>

                            <ul class="collapse ps-3" id="visionContent">
                                <li>
                                    Ser la plataforma líder en gestión de atención al cliente, reconocida por su innovación,
                                    fiabilidad y capacidad para transformar la experiencia del usuario en instituciones de 
                                    alto volumen.
                                </li>
                            </ul>
                        </li>
                    </ul>


                </div>
                <div class="col-md-4">
                    <h5 class="mb-3">Contacto</h5>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-envelope me-2"></i>soportetecnico@ticketpro.hn
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-phone me-2"></i>+504 9659-5294
                        </li>
                        <li>
                            <div class="mt-2">
                                <a href="#" class="text-light me-3"><i class="fab fa-facebook fs-5"></i></a>
                                <a href="#" class="text-light me-3"><i class="fab fa-twitter fs-5"></i></a>
                                <a href="#" class="text-light me-3"><i class="fab fa-linkedin fs-5"></i></a>
                                <a href="#" class="text-light"><i class="fab fa-github fs-5"></i></a>
                            </div>
                        </li>
                    </ul>
                </div>
            </div>
            <hr class="my-3 bg-secondary">
            <div class="row">
                <div class="col-12 text-center">
                    <p class="mb-0 text-muted">
                        &copy; 2025 TicketPro. Todos los derechos reservados.
                    </p>
                </div>
            </div>
        </div>
        </footer>
    </main>

    <!-- Bootstrap JS Bundle -->
    <script src="/Content/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS para temas -->
    <script src="/Content/dist/js/theme-switcher.js"></script>
    
    <!-- Custom JS para sidebar -->
    <script src="/Content/dist/js/sidebar.js"></script>

    <!-- jQuery (si lo usas) -->
    <script src="https://code.jquery.com/jquery-3.1.1.min.js"></script>

    <!-- SweetAlert2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        <?php
        if (isset($_SESSION['alert'])) {
            $alert = $_SESSION['alert'];
            $icon = htmlspecialchars($alert['icon'] ?? 'info', ENT_QUOTES, 'UTF-8');
            $title = htmlspecialchars($alert['title'] ?? 'Aviso', ENT_QUOTES, 'UTF-8');
            $text = htmlspecialchars($alert['text'] ?? 'Operación completada.', ENT_QUOTES, 'UTF-8');
            
            echo "Swal.fire({
                icon: '{$icon}',
                title: '{$title}',
                text: '{$text}',
                showConfirmButton: true
            });";

            // Eliminar la sesión después de mostrar
            unset($_SESSION['alert']); 
        }
        ?>
    </script>

</body>
</html>