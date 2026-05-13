<?php
session_start();
 
if (isset($_SESSION['usuario_id'])) {
    header('Location: lista_Usuario.php');
    exit;
}
 
require_once 'conexionBD.php';
 
$error = '';
$exito = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula     = trim($_POST['cedula']    ?? '');
    $nombre     = trim($_POST['nombre']    ?? '');
    $correo     = trim($_POST['correo']    ?? '');
    $contrasena = $_POST['password']       ?? '';
 
    // Validaciones
    if (empty($cedula) || empty($nombre) || empty($correo) || empty($contrasena)) {
        $error = 'Todos los campos son obligatorios.';
    } elseif (!ctype_digit($cedula)) {
        $error = 'La cédula solo debe contener números.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Formato de correo inválido.';
    } elseif (strlen($contrasena) < 8) {
        $error = 'La contraseña debe tener al menos 8 caracteres.';
    } else {
        // Verificar correo duplicado
        $check = $conexion->prepare('SELECT id FROM usuarios WHERE correo = ?');
        $check->bind_param('s', $correo);
        $check->execute();
        $check->store_result();
 
        if ($check->num_rows > 0) {
            $error = 'El correo ya está registrado.';
        } else {
            $fecha_registro  = date('Y-m-d H:i:s');
            $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);
 
            $stmt = $conexion->prepare(
                'INSERT INTO usuarios (cedula, nombre, correo, password, fecha_registro) VALUES (?, ?, ?, ?, ?)'
            );
            $stmt->bind_param('sssss', $cedula, $nombre, $correo, $contrasena_hash, $fecha_registro);
 
            if ($stmt->execute()) {
                $exito = 'Usuario registrado exitosamente.';
            } else {
                $error = 'Error al registrar. Intente de nuevo.';
            }
            $stmt->close();
        }
        $check->close();
        $conexion->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro</title>
    <link rel="stylesheet" href="estilosCSS/estiloRegistro.css">
    <link href="https://fonts.googleapis.com/css2?family=Syne:wght@400;600;700;800&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet"/>
</head>
<body>
<div class="wrapper">
 
    <div class="badge">
        <span class="badge-dot"></span>
        Registro
    </div>
 
    <h1>Crea tu cuenta</h1>
    <p class="subtitulo">Completa los datos para unirte a la plataforma.</p>
 
    <?php if ($error): ?>
        <p class="msg error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>
 
    <?php if ($exito): ?>
        <p class="msg exito"><?= htmlspecialchars($exito) ?></p>
        <p style="text-align:center"><a href="inicioSesion.php">Iniciar sesión</a></p>
    <?php else: ?>
 
    <div class="card">
        <form class="form-grid" action="guardarUsu.php" method="POST" id="regForm">
 
            <div class="field">
                <label for="cedula">Número de cédula</label>
                <div class="input-wrap">
                    <input type="text" id="cedula" name="cedula"
                           placeholder="0123456789" inputmode="numeric" maxlength="13"
                           value="<?= htmlspecialchars($_POST['cedula'] ?? '') ?>" required/>
                </div>
            </div>
 
            <div class="field">
                <label for="nombre">Nombres completos</label>
                <div class="input-wrap">
                    <input type="text" id="nombre" name="nombre"
                           placeholder="Tu nombre y apellido"
                           value="<?= htmlspecialchars($_POST['nombre'] ?? '') ?>" required/>
                </div>
            </div>
 
            <div class="field">
                <label for="correo">Correo electrónico</label>
                <div class="input-wrap">
                    <input type="email" id="correo" name="correo"
                           placeholder="ejemplo@correo.com"
                           value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required/>
                </div>
            </div>
 
            <div class="field">
                <label for="password">Contraseña</label>
                <div class="input-wrap">
                    <input type="password" id="password" name="password"
                           placeholder="Mínimo 8 caracteres" required/>
                    <button type="button" class="toggle-pw"
                            aria-label="Mostrar contraseña"
                            onclick="togglePw(this)">👁</button>
                </div>
            </div>
 
            <div class="divider">Datos seguros y encriptados</div>
 
            <button type="submit" class="btn">Crear cuenta →</button>
        </form>
    </div>
 
    <?php endif; ?>
 
    <p class="footer-link">¿Ya tienes cuenta? <a href="inicioSesion.php">Inicia sesión</a></p>
</div>
 
<script>
function togglePw(btn) {
    const input = btn.previousElementSibling;
    input.type = input.type === 'password' ? 'text' : 'password';
}
</script>
</body>
</html>