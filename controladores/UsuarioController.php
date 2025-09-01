<?php
require_once __DIR__ . '/../modelos/Conexion.php';

class UsuarioController {
    private $conn;

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->getConexion();
    }

    public function login($nombre, $password) {
        $sql = "SELECT * FROM usuarios WHERE nombre=? AND password=?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("ss", $nombre, $password);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            return $result->fetch_assoc();
        }
        return false;
    }
}
?>
