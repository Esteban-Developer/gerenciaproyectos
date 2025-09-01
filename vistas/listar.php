<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../controladores/DocumentoController.php';
require_once __DIR__ . '/../controladores/CategoriaController.php';

$docController = new DocumentoController();
$catController = new CategoriaController();

// Categorías para el select
$categorias = $catController->obtenerCategorias();

// Filtro de categoría
$categoria_id = isset($_POST['categoria_id']) && $_POST['categoria_id'] !== "" 
                ? intval($_POST['categoria_id']) 
                : null;

// Documentos (si no hay filtro, se listan TODOS)
$documentos = $docController->listarDocumentos($categoria_id);

// Mensajes de acciones
$mensaje = "";
if (isset($_GET['deleted'])) {
    $mensaje = "🗑️ Documento eliminado con éxito";
} elseif (isset($_GET['error'])) {
    $mensaje = "❌ Ocurrió un error al procesar la acción";
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>📑 Documentos - Gestor</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;800&display=swap');

    *{margin:0;padding:0;box-sizing:border-box;font-family:'Orbitron',sans-serif;}

    body{
      min-height:100vh;background:#000;color:#e0e0e0;padding:40px 20px;
      overflow-x:hidden;position:relative;
    }
    body::before{
      content:"";position:absolute;width:200%;height:200%;
      background:
        radial-gradient(circle,rgba(0,195,255,0.15)0%,transparent 70%),
        radial-gradient(circle,rgba(188,19,254,0.15)0%,transparent 80%);
      animation:move 35s linear infinite;
    }
    @keyframes move{0%{transform:translate(0,0)}50%{transform:translate(-10%,-15%)}100%{transform:translate(0,0)}}

    h1{
      font-size:26px;text-align:center;margin-bottom:30px;color:#00c3ff;
      text-shadow:0 0 10px #00c3ff,0 0 20px #bc13fe;
      animation:glow 3s infinite alternate;z-index:2;position:relative;
    }
    @keyframes glow{
      from{text-shadow:0 0 10px #00c3ff,0 0 20px #bc13fe;}
      to{text-shadow:0 0 18px #bc13fe,0 0 28px #00c3ff;}
    }

    .mensaje{
      max-width:900px;margin:0 auto 15px auto;text-align:center;
      color:#00ffcc;text-shadow:0 0 10px #00ffcc;z-index:2;position:relative;
    }

    .panel{
      width:100%;max-width:900px;margin:0 auto;
      background:rgba(20,20,20,0.9);border-radius:20px;padding:25px;
      box-shadow:0 0 20px rgba(0,195,255,0.2);
      border:1px solid rgba(255,255,255,0.05);z-index:2;position:relative;
    }

    .filtro{
      display:flex;gap:10px;flex-wrap:wrap;margin-bottom:20px;
    }
    select,button,a.btn{
      padding:10px 15px;border-radius:10px;border:none;
      background:rgba(30,30,30,0.9);color:#fff;font-size:13px;cursor:pointer;
      box-shadow:0 0 10px #00c3ff;transition:.3s;
    }
    button:hover,a.btn:hover{background:#00c3ff;color:#000;box-shadow:0 0 15px #bc13fe,0 0 25px #00c3ff;}

    table{
      width:100%;border-collapse:collapse;margin-top:15px;
    }
    th,td{
      padding:10px;text-align:center;border:1px solid rgba(255,255,255,0.1);
    }
    th{background:#111;color:#00c3ff;}
    tr:hover{background:rgba(0,195,255,0.05);}
    a.link{color:#00c3ff;text-decoration:none;}
    a.link:hover{text-decoration:underline;}

    .acciones a{
      display:inline-block;margin:0 3px;padding:6px 10px;border-radius:8px;font-size:12px;
      background:linear-gradient(90deg,#bc13fe,#00c3ff);color:#fff;text-decoration:none;
      transition:.3s;
    }
    .acciones a:hover{transform:scale(1.05);}
    .links{margin-top:20px;display:flex;gap:15px;justify-content:center;}
    .btn-outline{
      padding:10px 15px;background:rgba(25,25,25,0.9);border:1px solid #00c3ff;
      border-radius:10px;color:#00c3ff;text-decoration:none;transition:.3s;
    }
    .btn-outline:hover{background:#00c3ff;color:#000;box-shadow:0 0 15px #bc13fe,0 0 25px #00c3ff;}
  </style>
</head>
<body>
  <h1>📑 LISTA DE DOCUMENTOS</h1>

  <?php if($mensaje): ?>
    <div class="mensaje"><?= $mensaje ?></div>
  <?php endif; ?>

  <div class="panel">
    <!-- FILTRO -->
    <form method="POST" class="filtro">
      <select name="categoria_id">
        <option value="">-- Todas las categorías --</option>
        <?php while($cat=$categorias->fetch_assoc()): ?>
          <option value="<?= $cat['id'] ?>" <?= ($categoria_id==$cat['id'])?"selected":"" ?>>
            <?= htmlspecialchars($cat['nombre']) ?>
          </option>
        <?php endwhile; ?>
      </select>
      <button type="submit">Filtrar</button>
    </form>

    <!-- TABLA -->
    <div class="table-responsive">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Archivo</th>
            <th>Categoría</th>
            <th>Usuario</th>
            <th>Fecha</th>
            <th>Acciones</th>
          </tr>
        </thead>
        <tbody>
          <?php if($documentos && $documentos->num_rows>0): ?>
            <?php while($doc=$documentos->fetch_assoc()): ?>
              <tr>
                <td><?= $doc['id'] ?></td>
                <td><a class="link" href="<?= htmlspecialchars($doc['ruta_archivo']) ?>" target="_blank"><?= htmlspecialchars($doc['nombre_archivo']) ?></a></td>
                <td><?= htmlspecialchars($doc['categoria']) ?></td>
                <td><?= htmlspecialchars($doc['usuario']) ?></td>
                <td><?= $doc['fecha'] ?></td>
                <td class="acciones">
                  <a href="<?= htmlspecialchars($doc['ruta_archivo']) ?>" download>⬇️ Descargar</a>
                  <a href="eliminar.php?id=<?= $doc['id'] ?>" onclick="return confirm('¿Seguro que deseas eliminar este documento?')">🗑️ Eliminar</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr><td colspan="6">⚠️ No hay documentos disponibles</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="links">
      <a href="subir.php" class="btn-outline">📤 Subir Documento</a>
      <a href="logout.php" class="btn-outline">🚪 Cerrar sesión</a>
    </div>
  </div>
</body>
</html>
