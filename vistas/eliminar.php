<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../controladores/DocumentoController.php';
$docController = new DocumentoController();

if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $docController->eliminarDocumento($id);
}

header("Location: listar.php");
exit;
