<?php
    namespace entity;

    class eUser {
        public $id;
        public $name;
        public $username;
        public $password;
        public $service_id;
        public $role_id;
        public $email;
        public $phone;
        public $status;

        public $Found;

        public function __construct() {
            $this->Found = false;
        }
    }
?>