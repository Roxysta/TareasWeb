<?php
session_start();
 

if (!isset($_SESSION['usuario_id'])) {
    header('Location: inicioSesion.php');
    exit;
}
 
require_once 'config/conexionBD.php';
 
$error = '';
$exito = '';
$conn  = getConexion();
 
$stmt = $conn->prepare('SELECT id, cedula, nombre, correo, fecha_registro FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
 
// Procesar actualización de perfil
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');
 
    if (empty($nombre) || empty($correo)) {
        $error = 'Nombre y correo son obligatorios.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'El correo no tiene un formato válido.';
    } else {
        // Verificar que el nuevo correo no lo use otro usuario
        $check = $conn->prepare('SELECT id FROM usuarios WHERE correo = ? AND id != ?');
        $check->bind_param('si', $correo, $_SESSION['usuario_id']);
        $check->execute();
        $check->store_result();
 
        if ($check->num_rows > 0) {
            $error = 'Ese correo ya está registrado por otro usuario.';
        } else {
            $upd = $conn->prepare('UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?');
            $upd->bind_param('ssi', $nombre, $correo, $_SESSION['usuario_id']);
 
            if ($upd->execute()) {
                // Actualizar sesión
                $_SESSION['usuario_nombre'] = $nombre;
                $_SESSION['usuario_correo'] = $correo;
                $usuario['nombre'] = $nombre;
                $usuario['correo'] = $correo;
                $exito = 'Perfil actualizado correctamente.';
            } else {
                $error = 'Error al actualizar. Intente de nuevo.';
            }
            $upd->close();
        }
        $check->close();
    }
}
 
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="card">
    <h2>Mi Perfil</h2>
    <p class="bienvenida">Bienvenido/a, <strong><?= htmlspecialchars($_SESSION['usuario_nombre']) ?></strong></p>
 
    <?php if ($error): ?><p class="msg error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
    <?php if ($exito): ?><p class="msg exito"><?= htmlspecialchars($exito) ?></p><?php endif; ?>
 
    <form method="POST">
        <label>Cédula (no editable)</label>
        <input type="text" value="<?= htmlspecialchars($usuario['cedula']) ?>" disabled>
 
        <label>Nombre completo</label>
        <input type="text" name="nombre"
               value="<?= htmlspecialchars($usuario['nombre']) ?>" required>
 
        <label>Correo electrónico</label>
        <input type="email" name="correo"
               value="<?= htmlspecialchars($usuario['correo']) ?>" required>
 
        <button type="submit">Actualizar datos</button>
    </form>
 
    <div class="acciones">
        <a href="cambiar_password.php">🔒 Cambiar contraseña</a>
        <a href="logout.php" class="btn-logout">Cerrar sesión</a>
    </div>
</div>
</body>
</html>