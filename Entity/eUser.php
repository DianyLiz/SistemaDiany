<?php
    namespace entity;

    class eUser {
        public $id;
        public $email;
        public $password;
        public $descripcion;
        public $estado;
        public $creacion;

        public $Found;

        public function __construct() {
            $this->Found = false;
        }
    }
?>