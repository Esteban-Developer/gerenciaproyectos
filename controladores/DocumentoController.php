<?php
require_once __DIR__ . '/../modelos/Conexion.php';

class DocumentoController {
    private $conn;

    public function __construct() {
        $db = new Conexion();
        $this->conn = $db->getConexion();
    }

    public function subirDocumento($usuario_id, $categoria_id, $archivo) {
        $nombre = $archivo['name'];
        $tmp = $archivo['tmp_name'];
        $ruta = "uploads/" . time() . "_" . $nombre;

        if (move_uploaded_file($tmp, $ruta)) {
            $sql = "INSERT INTO documentos (usuario_id, categoria_id, nombre_archivo, ruta_archivo)
                    VALUES (?, ?, ?, ?)";
            $stmt = $this->conn->prepare($sql);
            $stmt->bind_param("iiss", $usuario_id, $categoria_id, $nombre, $ruta);
            return $stmt->execute();
        }
        return false;
    }

   public function listarDocumentos($categoria_id = null) {
    $sql = "SELECT d.id, d.nombre_archivo, d.ruta_archivo, d.fecha, 
                   c.nombre AS categoria, u.nombre AS usuario
            FROM documentos d
            INNER JOIN categorias c ON d.categoria_id = c.id
            INNER JOIN usuarios u ON d.usuario_id = u.id";

    if ($categoria_id) {
        $sql .= " WHERE d.categoria_id = ?";
        $stmt = $this->conn->prepare($sql);
        $stmt->bind_param("i", $categoria_id);
    } else {
        $stmt = $this->conn->prepare($sql);
    }

    $stmt->execute();
    $resultado = $stmt->get_result();

    return ($resultado->num_rows > 0) ? $resultado : false;
}
  

    // <-- NUEVA FUNCIÓN PARA EL SELECT DE CATEGORÍAS
    public function listarCategorias() {
        $sql = "SELECT id, nombre FROM categorias ORDER BY nombre ASC";
        return $this->conn->query($sql);
    }
    public function eliminarDocumento($id) {
    // Primero, obtenemos la ruta del archivo
    $sql = "SELECT ruta_archivo FROM documentos WHERE id = ?";
    $stmt = $this->conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 0) return false; // No existe

    $doc = $result->fetch_assoc();
    $ruta = $doc['ruta_archivo'];

    // Intentamos borrar el archivo físico
    if (file_exists($ruta)) {
        unlink($ruta);
    }

    // Ahora borramos la fila de la base de datos
    $sqlDelete = "DELETE FROM documentos WHERE id = ?";
    $stmtDelete = $this->conn->prepare($sqlDelete);
    $stmtDelete->bind_param("i", $id);
    return $stmtDelete->execute();
}

}
?>
