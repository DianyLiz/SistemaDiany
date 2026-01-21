<?php
namespace config;

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
            $this->conn = new \PDO("mysql:host={$this->host};port={$this->port};dbname={$this->db_name};charset={$this->charset}",
                $this->user,
                $this->password,
                [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ]
            );
        } catch (\PDOException $e) {
            echo "Connection error: " . $e->getMessage();
        }
    }

    public function getConexion() {
        return $this->conn;
    }
}

?>