<?php
    session_start();

    require_once "Define.php";
    require_once "Config\Conexion.php";
    require_once 'Entity\eUser.php';
    require_once "Models\User.php";
    // Ensure Audit model is available when logging login events
    require_once "Models\Audit.php";

    use Models\User as User;
    $user = new User();

    $username = "";

    if(isset($_GET["op"])) {
      if($_GET["op"]=="exit"){
        session_destroy();
        header("Location: /Login");
        exit();
      }
    }

    if(isset($_POST) && isset($_POST['username'])){
        $username = $_POST['username'];
        $password = $_POST['password'];

        $data = $user->forUserName($username, $password);
        //echo json_encode($data);

        if($data) {
                    $_SESSION["system"]["username"]=$data->username;
                    $_SESSION["system"]["name"]=$data->name;
                    $_SESSION["system"]["user_id"] = isset($data->id) ? $data->id : null;

                    // Log audit: user login
                      try {
                          $audit = new \Models\Audit();
                          $audit->log($data->id, null, null, 'login', 'Usuario inició sesión: ' . $data->username);
                      } catch (\Exception $e) {
                          // ignore logging failures
                      }
        }

        
    }

    if(isset($_SESSION["system"]["username"])) {
      //echo "Sesión iniciada";
      header("Location: /");
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
                <form id="loginForm" method="post">
                    <!-- Campo de usuario -->
                    <div class="form-floating-modern mb-4">
                        <div class="input-wrapper">
                            <i class="input-icon fas fa-user"></i>
                            <input type="text" class="form-control-modern" id="username" name="username"
                                 value="<?php echo $username ?>" required autocomplete="off">
                            <label for="username" class="floating-label">Usuario</label>
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

