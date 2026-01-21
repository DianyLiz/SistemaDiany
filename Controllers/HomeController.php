<?php
    namespace Controllers;

    class HomeController {
        public function index() {
            $JData = "Hola, desde el HomeController";
            //echo $JData;
            return $JData;
        }

    }
?>