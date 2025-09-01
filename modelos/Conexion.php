<?php
class Conexion {
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $db = "gerenciaproyectos";
    public $conn;

    public function __construct() {
        $this->conn = new mysqli($this->host, $this->user, $this->pass, $this->db);
        if ($this->conn->connect_error) {
            die("Error en la conexión: " . $this->conn->connect_error);
        }
    }

    public function getConexion() {
        return $this->conn;
    }
}
?>
