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

        public function forUserName($username, $password) {
            $sql = "SELECT * FROM Users WHERE username = :username AND password = :password";
            $stmt = $this->Conexion->prepare($sql);
            $stmt->bindParam(":username", $username);
            $stmt->bindParam(":password", $password);
            $stmt->execute();
            return $stmt->fetch(\PDO::FETCH_OBJ);
        }

        public function save($entity){
            $sql = "call SP_User (";
            $sql .= "'".$entity->id."', ";
            $sql .= "'".$entity->name."', ";
            $sql .= "'".$entity->username."', ";
            $sql .= "'".$entity->password."', ";
            $sql .= "'".$entity->service_id."', ";
            $sql .= "'".$entity->role_id."', ";
            $sql .= "'".$entity->email."', ";
            $sql .= "'".$entity->phone."', ";
            $sql .= "'".$entity->status."'";
            $sql .= ");";

            $stmt = $this->Conexion->prepare($sql);
            $stmt->execute();
        }
    }
?>