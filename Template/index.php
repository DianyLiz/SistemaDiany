<?php
// // Definir rutas centralizadas de assets (puedes cambiarlas aquí)
// if (!defined('ROOT')) {
//     define('ROOT', realpath(__DIR__ . '/..'));
// }
// if (!defined('DIST_BASE_URL')) define('DIST_BASE_URL', '/Content/dist');
// if (!defined('DIST_PATH')) define('DIST_PATH', ROOT . '/Content/Dist');

$template = new Template();

class Template {
        private $body;

        function __construct() {
            ob_start();
            include ROOT . "/Template/Default/index.php";
            $file = ob_get_clean(); 
            $this->body = explode("{JBODY}", $file);
            echo $this->body[0];
        }

        function __destruct() {
            echo $this->body[1];
        }
    }
?>