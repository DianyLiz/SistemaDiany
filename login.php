<?php
    session_start();

    require_once "Define.php";
    require_once "Config\Conexion.php";
    require_once 'Entity\eUser.php';
    require_once "Models\User.php";

    use Models\User as User;
    $user = new User();

    $email = "";
    $error = "";

    // Cerrar sesión solo si el parámetro existe
    if (isset($_GET["op"]) && $_GET["op"] === "exit") {
        session_unset();
        session_destroy();
        // limpiar token por pestaña y permanecer en /Login
        echo "<!DOCTYPE html><html><head><meta charset='utf-8'><script>try{sessionStorage.removeItem('AUTH_TAB');}catch(e){}; location.replace('/Login');</script></head><body></body></html>";
        exit();
    }

    // Si viene con reauth=1 y ya existe sesión, mostrar formulario (no redirigir automáticamente)


    if(isset($_POST) && isset($_POST['email'])){
        $email = $_POST['email'];
        $password = $_POST['password'];

        // Autenticar por email y validar contraseña (hash o texto plano)
        $data = $user->authenticateByEmail($email, $password);

        if($data){
                        $_SESSION["system"] = [
                "username" => $data->email,
                "user_id" => $data->id ?? null,
            ];
                        // Establecer token por pestaña y redirigir a la ruta solicitada (si es válida)
                        $ret = isset($_GET['return']) ? $_GET['return'] : '/';
                        // Permitir solo rutas relativas
                        if (!is_string($ret) || strpos($ret, '/') !== 0) { $ret = '/'; }
                        $ret = htmlspecialchars($ret, ENT_QUOTES, 'UTF-8');
                        echo "<!DOCTYPE html><html><head><meta charset='utf-8'><script>try{sessionStorage.setItem('AUTH_TAB','1');}catch(e){}; location.replace('{$ret}');</script></head><body></body></html>";
            exit();
        } else {
            $error = "Correo o contraseña incorrectos";
        }
    }

                if(isset($_SESSION["system"]["username"]) && !isset($_GET['reauth'])) {
            // Si ya hay sesión y no es reautenticación, asegurar token por pestaña y enviar a inicio
            echo "<!DOCTYPE html><html><head><meta charset='utf-8'><script>try{sessionStorage.setItem('AUTH_TAB','1');}catch(e){}; location.replace('/');</script></head><body></body></html>";
            exit();
        }
    
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Sistema Web</title>
    
    <!-- Bootstrap CSS -->
    <link href="Content/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS -->
    <link href="Content/dist/css/custom-themes.css" rel="stylesheet">
    <link href="Content/dist/css/login.css" rel="stylesheet">
    
    <!-- Font Awesome para iconos (CDN) -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body class="login-body">
    <!-- Botón de modo oscuro 
    <button class="btn-theme-toggle-login" id="themeToggleLogin" type="button" aria-label="Alternar tema">
        <i class="fas fa-moon" id="themeIconLogin"></i>
    </button>-->
    
    <div class="login-container">
        <div class="login-wrapper">
            <!-- Logo y título -->
            <div class="login-header text-center mb-5">
                <div class="login-logo mb-4">
                    <div class="logo-glow"></div>
                    <img class="login-logo" width="20" height="40" src="..\\Content\\Demo\\img\\LOGO_TP_ROJO_bg.png" alt="">
                </div>
                <h1 class="login-title mb-2">Bienvenido</h1>
                <p class="login-subtitle">Ingresa tus credenciales para continuar</p>
            </div>

            <!-- Formulario de login -->
            <div class="login-card">
                <!-- Manejo de errores (prevención XSS) -->
                <?php if($error): ?>
                    <div class="alert alert-danger text-center">
                        <?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?>
                    </div>
                <?php endif; ?>

                <form id="loginForm" method="post">
                    <!-- Campo de correo -->
                    <div class="form-floating-modern mb-4">
                        <div class="input-wrapper">
                            <i class="input-icon fas fa-envelope"></i>
                               <input type="email" class="form-control-modern" id="email" name="email"
                                   value="<?php echo htmlspecialchars($email, ENT_QUOTES, 'UTF-8'); ?>" required autocomplete="off">
                            <label for="email" class="floating-label">Correo electrónico</label>
                            <div class="input-line"></div>
                        </div>
                    </div>

                    <!-- Campo de contraseña -->
                    <div class="form-floating-modern mb-4">
                        <div class="input-wrapper">
                            <i class="input-icon fas fa-lock"></i>
                            <input type="password" class="form-control-modern" id="password" name="password" required>
                            <label for="password" class="floating-label">Contraseña</label>
                            <div class="input-line"></div>
                            
                        </div>
                    </div>

                    <!-- Botón de login -->
                    <button type="submit" class="btn btn-login-modern w-100 mb-4">
                        <span class="btn-text">
                            <i class="fas fa-sign-in-alt me-2"></i>Iniciar Sesión
                        </span>
                        <span class="btn-loader"></span>
                    </button>


                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS Bundle -->
    <script src="Content/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS para login 
    <script src="Content/dist/js/login.js"></script>-->
    
    <!-- Custom JS para tema oscuro en login -->
    <script src="Content/dist/js/login-theme.js"></script>
</body>
</html>
