<?php
require_once __DIR__ . '/../modelos/Conexion.php';

class CategoriaController {
    private $conn;

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->getConexion();
    }

    public function obtenerCategorias() {
        $sql = "SELECT * FROM categorias";
        return $this->conn->query($sql);
    }
}
?>
