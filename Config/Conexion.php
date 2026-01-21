<?php
namespace Config;

//$conn = new Conexion("vientoenpopa");

class Conexion {
    private $host="localhost";
    private $db_name="vientoenpopa";
    private $user="root";
    private $password="";
    private $conn=null;
    private $port="3306";
    private $charset="utf8mb4";

    public function __construct() {

        try {
            $this->conn = new \PDO("mysql:host=".$this->host.";port=".$this->port.";dbname=".$this->db_name.";charset=".$this->charset, $this->user, $this->password);
            // throw exceptions on SQL errors to make debugging easier
            $this->conn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
            
            //echo "Conexión Exitosa...";

        } catch (\Throwable $th) {
            die("Conexión Fallida... ".$th->getMessage());
        } 
    }

    public function getConexion() {
        return $this->conn;
    }
}

?>