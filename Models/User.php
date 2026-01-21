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
            // Verificar contraseña usando hash (bcrypt)
            if(password_verify($password, $user->password)){
                return $user;
            }

            return false;

        }

        public function save($entity){
            // Hash password and use prepared statement to avoid SQL injection
            $hashed = password_hash($entity->password, PASSWORD_BCRYPT);
            $sql = "CALL SP_User(:id, :email, :password, :descripcion, :estado, :creacion)";
            $stmt = $this->Conexion->prepare($sql);
            $stmt->bindValue(':id', $entity->id);
            $stmt->bindValue(':email', $entity->email);
            $stmt->bindValue(':password', $hashed);
            $stmt->bindValue(':descripcion', $entity->descripcion);
            $stmt->bindValue(':estado', $entity->estado);
            $stmt->bindValue(':creacion', $entity->creacion);
            $stmt->execute();
        }
    }
?>