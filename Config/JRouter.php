<?php
    namespace Config;

    class JRouter {
        public static function run(JRequest $request) {
            $controllerName = $request->getController()."Controller";
            $method = $request->getMethod();
            $argument = $request->getArgument();

            $path = ROOT."Controllers".DS. $controllerName.".php";

            if(is_readable($path)) {
                require $path;
                $show = "Controllers\\". $controllerName;
                
                $controller = new $show();

                if($argument) {
                    $JData = call_user_func_array(array($controller, $method), $argument);
                } else {
                    $JData = call_user_func(array($controller, $method));
                }

            } else {
                echo "Página no encontrada 404.";
                exit();
            }

            $path = ROOT."Views".DS.$request->getController().DS.$method.".php";

            if(is_readable($path)) {
                require $path;
            }
        }
    }

?>