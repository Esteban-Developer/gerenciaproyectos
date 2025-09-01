<?php
session_start();
require_once __DIR__ . '/../controladores/UsuarioController.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller = new UsuarioController();
    $usuario = $controller->login($_POST['nombre'], $_POST['password']);

    if ($usuario) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        header("Location: subir.php");
        exit;
    } else {
        $error = "Usuario o contraseña incorrectos ❌";
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Login - Gestor de Archivos</title>
  <style>
    @import url('https://fonts.googleapis.com/css2?family=Orbitron:wght@400;600;800&display=swap');

    * {
      margin: 0; padding: 0;
      box-sizing: border-box;
      font-family: 'Orbitron', sans-serif;
    }

    body {
      height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: #000; /* 👈 Fondo negro sólido */
      overflow: hidden;
      position: relative;
      color: #e0e0e0;
    }

    /* Luces animadas futuristas */
    body::before {
      content: "";
      position: absolute;
      width: 200%;
      height: 200%;
      background: 
        radial-gradient(circle, rgba(0,195,255,0.15) 0%, transparent 70%),
        radial-gradient(circle, rgba(188,19,254,0.15) 0%, transparent 80%);
      animation: move 35s linear infinite;
    }
    @keyframes move {
      0% { transform: translate(0,0); }
      50% { transform: translate(-10%, -15%); }
      100% { transform: translate(0,0); }
    }

    .login-box {
      position: relative;
      width: 420px;
      padding: 40px 35px;
      background: rgba(20, 20, 20, 0.85);
      border-radius: 18px;
      box-shadow: 0 8px 40px rgba(0,0,0,0.9);
      backdrop-filter: blur(14px);
      text-align: center;
      border: 1px solid rgba(255,255,255,0.08);
      animation: fadeIn 1.5s ease;
    }

    @keyframes fadeIn {
      from { opacity: 0; transform: translateY(-40px) scale(0.9); }
      to { opacity: 1; transform: translateY(0) scale(1); }
    }

    .login-box h1 {
      font-size: 20px;
      font-weight: 800;
      margin-bottom: 25px;
      text-transform: uppercase;
      color: #00c3ff;
      text-shadow: 0 0 8px #00c3ff, 0 0 20px #bc13fe;
      animation: glow 3s infinite alternate;
    }

    @keyframes glow {
      from { text-shadow: 0 0 10px #00c3ff, 0 0 20px #bc13fe; }
      to { text-shadow: 0 0 18px #bc13fe, 0 0 28px #00c3ff; }
    }

    .login-box input {
      width: 100%;
      padding: 12px;
      margin: 15px 0;
      border: none;
      outline: none;
      border-radius: 10px;
      font-size: 15px;
      background: rgba(255,255,255,0.07);
      color: #e0e0e0;
      text-align: center;
      letter-spacing: 1px;
      transition: 0.3s;
    }

    .login-box input:focus {
      background: rgba(0,195,255,0.2);
      box-shadow: 0 0 12px #00c3ff;
    }

    .login-box button {
      width: 100%;
      padding: 13px;
      background: linear-gradient(90deg, #00c3ff, #1a73e8, #bc13fe);
      border: none;
      border-radius: 10px;
      font-size: 15px;
      color: #fff;
      cursor: pointer;
      text-transform: uppercase;
      font-weight: 600;
      letter-spacing: 2px;
      transition: 0.4s;
      box-shadow: 0 0 15px rgba(0,195,255,0.4);
    }

    .login-box button:hover {
      transform: scale(1.05);
      box-shadow: 0 0 25px #bc13fe, 0 0 40px #00c3ff;
    }

    .error {
      color: #ff5c8d;
      margin-bottom: 12px;
      font-weight: bold;
    }
  </style>
</head>
<body>
  <div class="login-box">
    <h1>GESTOR DE ARCHIVOS <br> GERENCIA DE PROYECTOS </h1>
    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
      <input type="text" name="nombre" placeholder="👤 Usuario" required>
      <input type="password" name="password" placeholder="🔑 Contraseña" required>
      <button type="submit">Ingresar</button>
    </form>
  </div>
</body>
</html>
