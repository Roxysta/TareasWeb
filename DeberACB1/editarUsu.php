<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: inicioSesion.php');
    exit;
}

if (!isset($_GET['id'])) {
    header('Location: lista_Usuario.php');
    exit;
}

require_once 'conexionBD.php';

$id = intval($_GET['id']);

$stmt = $conexion->prepare('SELECT id, cedula, nombre, correo, fecha_registro FROM usuarios WHERE id = ?');
$stmt->bind_param('i', $id);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
$conexion->close();

if (!$usuario) {
    die('Usuario no encontrado.');
}
$palabras = explode(' ', trim($usuario['nombre']));
$iniciales = strtoupper(
    substr($palabras[0], 0, 1) . (isset($palabras[1]) ? substr($palabras[1], 0, 1) : '')
);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="estilosCSS/editarPerfil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

<div class="editar-card">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="avatar-circle">
            <?= htmlspecialchars($iniciales) ?>
        </div>
        <div>
            <p class="mb-0 fw-500" style="font-size:16px;">
                <?= htmlspecialchars($usuario['nombre']) ?>
            </p>
            <span style="font-size:13px; color:#888;">Editando perfil</span>
        </div>
    </div>

    <hr style="border-color: rgba(0,0,0,0.07); margin: 0 0 1.25rem;">

    <form action="procesarCambios.php" method="POST">
        <input type="hidden" name="id" value="<?= $usuario['id'] ?>">

        <div class="d-flex flex-column gap-3">
            <div>
                <p class="campo-label">Nombre completo</p>
                <input
                    class="campo-input"
                    type="text"
                    name="nombre"
                    value="<?= htmlspecialchars($usuario['nombre']) ?>"
                    placeholder="Nombre completo"
                    required>
            </div>

            <div>
                <p class="campo-label">Correo electrónico</p>
                <input
                    class="campo-input"
                    type="email"
                    name="correo"
                    value="<?= htmlspecialchars($usuario['correo']) ?>"
                    placeholder="correo@ejemplo.com"
                    required>
            </div>
        </div>

        <hr style="border-color: rgba(0,0,0,0.07); margin: 1.25rem 0;">

        <div class="d-flex flex-column gap-2">
            <button type="submit" class="btn-guardar">Guardar cambios</button>
            <a href="perfil.php" class="btn-volver">← Volver al perfil</a>
        </div>

    </form>

</div>

</body>
</html>

