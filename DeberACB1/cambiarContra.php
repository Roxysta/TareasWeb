<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}

require_once 'conexionBD.php';

$mensaje = '';
$tipo    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $actual   = $_POST['contrasena_actual']  ?? '';
    $nueva    = $_POST['contrasena_nueva']   ?? '';
    $confirma = $_POST['contrasena_confirma'] ?? '';


    $stmt = $conexion->prepare('SELECT password FROM usuarios WHERE id = ?');
    $stmt->bind_param('i', $_SESSION['usuario_id']);
    $stmt->execute();
    $fila = $stmt->get_result()->fetch_assoc();
    $stmt->close();

    if (!$fila) {
        $mensaje = 'Usuario no encontrado.';
        $tipo    = 'danger';

    } elseif (!password_verify($actual, $fila['password'])) {
        $mensaje = 'La contraseña actual es incorrecta.';
        $tipo    = 'danger';

    
    } elseif (strlen($nueva) < 8) {
        $mensaje = 'La nueva contraseña debe tener al menos 8 caracteres.';
        $tipo    = 'warning';

    
    } elseif ($nueva !== $confirma) {
        $mensaje = 'Las contraseñas nuevas no coinciden.';
        $tipo    = 'danger';

    } else {
        $hash = password_hash($nueva, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare('UPDATE usuarios SET password = ? WHERE id = ?');
        $stmt->bind_param('si', $hash, $_SESSION['usuario_id']);
        $stmt->execute();
        $stmt->close();

        $mensaje = 'Contraseña actualizada correctamente.';
        $tipo    = 'success';
    }

    $conexion->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cambiar contraseña</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
    <link rel="stylesheet" href="estilosCSS/perfil.css">
</head>
<body>
<div class="perfil-card">

    <div class="d-flex align-items-center gap-2 mb-4">
        <a href="perfil.php" class="btn btn-sm btn-light border">
            <i class="bi bi-arrow-left"></i>
        </a>
        <h5 class="mb-0 fw-semibold">Cambiar contraseña</h5>
    </div>

    <?php if ($mensaje): ?>
        <div class="alert alert-<?= $tipo ?> py-2 px-3" role="alert">
            <?= htmlspecialchars($mensaje) ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="cambiarContra.php">

        <div class="campo-fila mb-3">
            <div class="campo-icono"><i class="bi bi-lock"></i></div>
            <div class="flex-grow-1">
                <label class="campo-label">Contraseña actual</label>
                <input type="password" name="contrasena_actual"
                       class="form-control form-control-sm" required>
            </div>
        </div>

        <div class="campo-fila mb-3">
            <div class="campo-icono"><i class="bi bi-key"></i></div>
            <div class="flex-grow-1">
                <label class="campo-label">Nueva contraseña</label>
                <input type="password" name="contrasena_nueva"
                       class="form-control form-control-sm"
                       minlength="8" required>
            </div>
        </div>

        <div class="campo-fila mb-4">
            <div class="campo-icono"><i class="bi bi-key-fill"></i></div>
            <div class="flex-grow-1">
                <label class="campo-label">Confirmar nueva contraseña</label>
                <input type="password" name="contrasena_confirma"
                       class="form-control form-control-sm"
                       minlength="8" required>
            </div>
        </div>

        <button type="submit" class="btn-perfil justify-content-center">
            <i class="bi bi-check-circle" style="font-size:17px;"></i>
            Guardar cambios
        </button>

    </form>
</div>
</body>
</html>