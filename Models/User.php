<?php
    namespace Models;

    use Config\Conexion as Conexion;

    class User{
        private $Conexion;

        public function __construct(){
            $this->Conexion = new Conexion();
            $this->Conexion = $this->Conexion->getConexion();
        }

        public function toList(){
            $sql = "SELECT * FROM Users";
            $stmt = $this->Conexion->prepare($sql);
            $stmt->execute();
            return $stmt->fetchAll(\PDO::FETCH_OBJ);
        }

        public function getForId($id){
            $sql = "SELECT * FROM Users WHERE id = :id";
            $stmt = $this->Conexion->prepare($sql);
            $stmt->bindParam(":id", $id);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_OBJ);
        }

        public function getNewId(){
            $sql = "SELECT * FROM Users ORDER BY id DESC LIMIT 0,1";
            $stmt = $this->Conexion->prepare($sql);
            $stmt->execute();
            $data = $stmt->fetch(\PDO::FETCH_OBJ);

            if(!$data){
                return "1";
            } else {
                return intval($data->id) + 1;
            }
        }

        public function forUserName($useremail, $password) {
            $sql = "SELECT * FROM Users WHERE email = :email AND password = :password";
            $stmt = $this->Conexion->prepare($sql);
            $stmt->bindParam(":email", $useremail);
            $stmt->bindParam(":password", $password);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_OBJ);
        }

        // Autenticación segura: busca por email y verifica hash de contraseña
        public function authenticateByEmail($email, $password) {
            $sql = "SELECT * FROM Users WHERE email = :email LIMIT 1";
            $stmt = $this->Conexion->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_OBJ);

            if(!$user){
                return false;
            }

            // Verificar usando hash si existe; si no, comparar texto plano
            $isValid = false;
            if(is_string($user->password) && strlen($user->password) > 0){
                if(password_verify($password, $user->password)){
                    $isValid = true;
                } elseif ($user->password === $password) {
                    $isValid = true;
                }
            }

            return $isValid ? $user : false;
        }

        public function save($entity){
            $sql = "call SP_User (";
            $sql .= "'".$entity->id."', ";
            $sql .= "'".$entity->email."', ";
            $sql .= "'".$entity->password."', ";
            $sql .= "'".$entity->descripcion."', ";
            $sql .= "'".$entity->estado."', ";
            $sql .= "'".$entity->creacion."'";
            $sql .= ");";

            $stmt = $this->Conexion->prepare($sql);
            $stmt->execute();
        }
    }
?>