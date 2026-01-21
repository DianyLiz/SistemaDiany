<?php
include_once __DIR__ . '/../../Config/Conexion.php';
        if (session_status() === PHP_SESSION_NONE) {
        session_start();
        }
?>

<!DOCTYPE html>
<html lang="es">
<head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <!-- Icono de la pestaña -->
        <link rel="icon" type="image/ico" href="/Content/Demo/img/LOGO_TP_ROJO_bg.ico">

        <!-- Título de la página -->
        <title>Sistema Diany</title>
    
        <!-- Bootstrap CSS -->
        <link href="/Content/dist/css/bootstrap.min.css" rel="stylesheet">
    
        <!-- Custom CSS -->
        <link href="/Content/dist/css/custom-themes.css" rel="stylesheet">
        <!-- Opcional: enlaces a archivos de tema separados (descomentar si creas archivos individuales) -->
        <!--
        <link href="/Content/dist/css/theme-default.css" rel="stylesheet">
        <link href="/Content/dist/css/theme-green.css" rel="stylesheet">
        <link href="/Content/dist/css/theme-purple.css" rel="stylesheet">
        <link href="/Content/dist/css/theme-orange.css" rel="stylesheet"> -->
        <link href="/Content/dist/css/theme-red.css" rel="stylesheet">

        <!-- Font Awesome para iconos (CDN) -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
                <!-- Ocultar scrollbar azul (WebKit) y en Firefox -->
                <style>
                        /* Firefox */
                        html { scrollbar-width: none; -ms-overflow-style: none; }
                        /* WebKit browsers (Chrome, Edge, Opera, Safari) */
                        html::-webkit-scrollbar { display: none; width: 0; height: 0; }
                        /* Alternativa para elementos específicos si fuese necesario */
                        ::-webkit-scrollbar-thumb { background: transparent; }
                </style>

        <!-- SweetAlert2 CSS -->
         <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

        <script src="/Content/Jquery/jquery-3.1.1.min.js"></script>
</head>
<body class="theme-red">
        <!-- Sidebar lateral -->
        <?php include __DIR__ . '/menu.php'; ?>

        <!-- Overlay para móviles -->
        <div class="sidebar-overlay" id="sidebarOverlay"></div>

        <!-- Header superior -->
        <header class="top-header">
                <div class="container-fluid d-flex align-items-center justify-content-between">
                        <button class="btn-menu-toggle" id="menuToggle">
                                <i class="fas fa-bars"></i>
                        </button>
            
                        <div class="d-flex align-items-center gap-2">
                                <!-- Botón de perfil del usuario -->
                                <div class="dropdown">
                                        <button class="btn btn-profile dropdown-toggle d-flex align-items-center" 
                                                        type="button" id="userProfileDropdown" data-bs-toggle="dropdown" 
                                                        aria-expanded="false">
                                                <i class="fas fa-user-circle me-2 fs-5"></i>
                                                <span class="d-none d-md-inline">
                                                    <?php
                                                    // mostrar nombre del usuario conectado (proteger con htmlspecialchars)
                                                    echo isset($_SESSION['system']['name']) ?
                                                        htmlspecialchars($_SESSION['system']['name'], ENT_QUOTES, 'UTF-8') :
                                                        (isset($_SESSION['system']['NameFull']) ?
                                                            htmlspecialchars($_SESSION['system']['NameFull'], ENT_QUOTES, 'UTF-8') :
                                                            'Usuario');
                                                    ?>
                                                </span>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userProfileDropdown">
                                                
                                                <li><a class="dropdown-item" href="/Login?op=exit">
                                                        <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                                                </a></li>
                                        </ul>
                                </div>
                        </div>
                </div>
        </header>

        <!-- Contenido principal -->
        <main class="main-content">
                <div class="container-fluid py-4">