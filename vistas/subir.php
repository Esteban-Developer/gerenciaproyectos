<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit;
}

require_once __DIR__ . '/../controladores/DocumentoController.php';
require_once __DIR__ . '/../controladores/CategoriaController.php';
require_once __DIR__ . '/../modelos/Conexion.php'; // para crear categoría desde el modal (mysqli)

$mensaje = "";

// Manejo de formularios en la misma vista
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Subir documento
    if (isset($_POST['accion']) && $_POST['accion'] === 'subir') {
        $docController = new DocumentoController();
        if ($docController->subirDocumento($_SESSION['usuario_id'], $_POST['categoria_id'], $_FILES['archivo'])) {
            $mensaje = "✅ Archivo subido con éxito";
        } else {
            $mensaje = "❌ Error al subir archivo";
        }
    }

    // Crear categoría (modal)
    if (isset($_POST['accion']) && $_POST['accion'] === 'crear_categoria') {
        $nombreCat = trim($_POST['nueva_categoria'] ?? "");
        if ($nombreCat !== "") {
            $conexion = new Conexion();
            $conn = $conexion->getConexion();
            $stmt = $conn->prepare("INSERT INTO categorias (nombre) VALUES (?)");
            $stmt->bind_param("s", $nombreCat);
            if ($stmt->execute()) {
                $mensaje = "🗂️ Categoría creada: " . htmlspecialchars($nombreCat);
            } else {
                $mensaje = "❌ Error al crear categoría";
            }
            $stmt->close();
        } else {
            $mensaje = "⚠️ El nombre de la categoría no puede estar vacío.";
        }
    }
}

// Obtener categorías actualizadas
$catController = new CategoriaController();
$categorias = $catController->obtenerCategorias(); // mysqli_result esperado
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Subir - Gestor de Archivos</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;800&display=swap');

    * { margin:0; padding:0; box-sizing:border-box; font-family:'Orbitron', sans-serif; }

    body {
      min-height: 100vh;
      background: #000;
      color: #e0e0e0;
      padding: 40px 20px;
      overflow-x: hidden;
      position: relative;
    }
    body::before{
      content:"";
      position:absolute; width:200%; height:200%;
      background:
        radial-gradient(circle, rgba(0,195,255,0.15) 0%, transparent 70%),
        radial-gradient(circle, rgba(188,19,254,0.15) 0%, transparent 80%);
      animation: move 35s linear infinite;
    }
    @keyframes move{ 0%{transform:translate(0,0)} 50%{transform:translate(-10%,-15%)} 100%{transform:translate(0,0)} }

    h1{
      font-size:26px; text-align:center; margin-bottom:30px; color:#00c3ff;
      text-shadow:0 0 10px #00c3ff, 0 0 20px #bc13fe;
      animation: glow 3s infinite alternate;
      z-index:2; position:relative;
    }
    @keyframes glow{
      from{ text-shadow:0 0 10px #00c3ff, 0 0 20px #bc13fe; }
      to  { text-shadow:0 0 18px #bc13fe, 0 0 28px #00c3ff; }
    }

    .mensaje{
      max-width: 900px; margin: 0 auto 15px auto; text-align:center;
      color:#00ffcc; text-shadow:0 0 10px #00ffcc; z-index:2; position:relative;
    }

    .panel {
      width: 100%; max-width: 600px; margin: 0 auto;
      background: rgba(20,20,20,0.9);
      border-radius: 20px; padding: 30px;
      box-shadow: 0 0 20px rgba(0,195,255,0.2);
      z-index:2; position:relative; text-align:center;
      border:1px solid rgba(255,255,255,0.05);
    }

    .upload-box {
      background: rgba(20, 20, 20, 0.85);
      border: 2px dashed rgba(0,195,255,0.6);
      border-radius: 20px; padding: 30px; transition: .3s; backdrop-filter: blur(10px);
    }
    .upload-box:hover { border-color:#bc13fe; box-shadow:0 0 20px #bc13fe, 0 0 30px #00c3ff; }
    .upload-box input[type="file"]{ display:none; }

    .upload-label {
      display:inline-block; padding:15px 30px;
      background: linear-gradient(90deg, #00c3ff, #1a73e8, #bc13fe);
      color:#fff; border-radius:12px; cursor:pointer; text-transform:uppercase;
      font-weight:600; font-size:14px; letter-spacing:2px; transition:.3s;
    }
    .upload-label:hover { transform:scale(1.05); box-shadow:0 0 25px #bc13fe, 0 0 40px #00c3ff; }

    select, .text-input {
      margin-top: 20px; padding: 12px; border-radius: 12px; border: none;
      background: rgba(30,30,30,0.9); color: #fff; font-size: 14px; outline: none; cursor: pointer;
      box-shadow: 0 0 10px #00c3ff; width: 100%;
    }

    .btn {
      margin-top: 20px; padding: 13px 30px;
      background: linear-gradient(90deg, #bc13fe, #1a73e8, #00c3ff);
      border: none; border-radius: 12px; font-size: 15px; color: #fff; cursor: pointer;
      font-weight: 600; letter-spacing: 2px; transition: .4s; box-shadow: 0 0 15px rgba(0,195,255,0.4);
      display:inline-block; text-decoration:none;
    }
    .btn:hover { transform: scale(1.05); box-shadow: 0 0 25px #bc13fe, 0 0 40px #00c3ff; }

    .btn-outline {
      padding:12px 20px; background: rgba(25,25,25,0.9); border:1px solid #00c3ff;
      border-radius:10px; color:#00c3ff; text-decoration:none; transition:.3s; display:inline-block;
    }
    .btn-outline:hover { background:#00c3ff; color:#000; box-shadow:0 0 15px #bc13fe, 0 0 25px #00c3ff; }

    .links { margin-top: 25px; display:flex; gap:15px; justify-content:center; }

    /* Modal */
    .modal {
      display:none; position:fixed; inset:0; background:rgba(0,0,0,0.8);
      align-items:center; justify-content:center; z-index: 9999;
    }
    .modal-content{
      background:#111; width: 90%; max-width: 420px; border-radius:15px; padding:25px;
      box-shadow:0 0 30px rgba(0,195,255,0.35); position:relative; animation: pop .25s ease-out;
      border:1px solid rgba(255,255,255,0.05);
    }
    @keyframes pop { from{transform:scale(.9); opacity:0} to{transform:scale(1); opacity:1} }
    .modal-title{
      color:#00c3ff; text-shadow:0 0 10px #00c3ff; margin-bottom:12px; font-size:18px; text-align:center;
    }
    .close{
      position:absolute; top:10px; right:14px; font-size:22px; color:#bc13fe; cursor:pointer;
    }
    .close:hover{ color:#fff; }
    .file-name { margin-top:10px; font-size:12px; color:#9adfff; opacity:.9; }
  </style>
</head>
<body>
  <h1>📤 GESTOR DE ARCHIVOS GERENCIA DE PROYECTOS</h1>

  <?php if (!empty($mensaje)): ?>
    <div class="mensaje"><?= $mensaje ?></div>
  <?php endif; ?>

  <div class="panel">
    <div class="upload-box">
      <form method="post" enctype="multipart/form-data">
        <input type="hidden" name="accion" value="subir">
        <label for="archivo" class="upload-label">Seleccionar Archivo</label>
        <input type="file" id="archivo" name="archivo" required onchange="mostrarNombre()">
        <div id="fileName" class="file-name"></div>

        <select name="categoria_id" required>
          <option value="">-- Selecciona Categoría --</option>
          <?php while ($c = $categorias->fetch_assoc()): ?>
            <option value="<?= $c['id']; ?>"><?= htmlspecialchars($c['nombre']); ?></option>
          <?php endwhile; ?>
        </select>

        <button type="submit" class="btn">Subir</button>
      </form>
    </div>

    <div class="links" style="margin-top:22px;">
      <a href="#" class="btn-outline" id="abrirModal">🗂 Crear categoría</a>
      <a href="listar.php" class="btn-outline">📑 Ver documentos</a>
      <a href="logout.php" class="btn-outline">🚪 Cerrar sesión</a>
    </div>
  </div>

  <!-- Modal Crear Categoría -->
  <div class="modal" id="modalCat">
    <div class="modal-content">
      <span class="close" id="cerrarModal">&times;</span>
      <div class="modal-title">Crear nueva categoría</div>
      <form method="post">
        <input type="hidden" name="accion" value="crear_categoria">
        <input type="text" name="nueva_categoria" class="text-input" placeholder="Nombre de la categoría" required>
        <button type="submit" class="btn" style="width:100%;">Guardar</button>
      </form>
    </div>
  </div>

  <script>
    const modal = document.getElementById('modalCat');
    const abrir = document.getElementById('abrirModal');
    const cerrar = document.getElementById('cerrarModal');

    abrir.addEventListener('click', (e)=>{ e.preventDefault(); modal.style.display='flex'; });
    cerrar.addEventListener('click', ()=> modal.style.display='none');
    window.addEventListener('click', (e)=>{ if(e.target===modal){ modal.style.display='none'; }});

    function mostrarNombre(){
      const inp = document.getElementById('archivo');
      const out = document.getElementById('fileName');
      out.textContent = inp.files.length ? 'Seleccionado: ' + inp.files[0].name : '';
    }
  </script>
</body>
</html>
