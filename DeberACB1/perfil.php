<?php
session_start();
 
if (!isset($_SESSION['usuario_id'])) {
    header('Location: inicioSesion.php');
    exit;
}
 
require_once 'conexionBD.php';
 

$stmt = $conexion->prepare(
    'SELECT id, cedula, nombre, correo, fecha_registro FROM usuarios WHERE id = ?'
);
$stmt->bind_param('i', $_SESSION['usuario_id']);
$stmt->execute();
$usuario = $stmt->get_result()->fetch_assoc();
$stmt->close();
 
if (!$usuario) {
    session_destroy();
    header('Location: inicioSesion.php');
    exit;
}
 
$conexion->close();

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
    <title>Mi Perfil</title>
    <link rel="stylesheet" href="estilosCSS/perfil.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>
<div class="perfil-card">

    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="avatar-circle">
            <?= htmlspecialchars($iniciales) ?>
        </div>
        <div>
            <p class="mb-1 fw-500" style="font-size:18px;">
                <?= htmlspecialchars($usuario['nombre']) ?>
            </p>
            <span class="badge-cedula">
                <i class="bi bi-fingerprint me-1"></i><?= htmlspecialchars($usuario['cedula']) ?>
            </span>
        </div>
    </div>

    <hr style="border-color: rgba(0,0,0,0.07); margin: 0 0 0.25rem;">

    <!-- Campos -->
    <div class="campo-fila">
        <div class="campo-icono"><i class="bi bi-person"></i></div>
        <div>
            <p class="campo-label">Nombre completo</p>
            <p class="campo-valor"><?= htmlspecialchars($usuario['nombre']) ?></p>
        </div>
    </div>

    <div class="campo-fila">
        <div class="campo-icono"><i class="bi bi-envelope"></i></div>
        <div>
            <p class="campo-label">Correo electrónico</p>
            <p class="campo-valor"><?= htmlspecialchars($usuario['correo']) ?></p>
        </div>
    </div>

    <div class="campo-fila">
        <div class="campo-icono"><i class="bi bi-calendar3"></i></div>
        <div>
            <p class="campo-label">Fecha de registro</p>
            <p class="campo-valor"><?= htmlspecialchars($usuario['fecha_registro']) ?></p>
        </div>
    </div>

    <hr style="border-color: rgba(0,0,0,0.07); margin: 0.25rem 0 1rem;">

    <!-- Acciones -->
    <div class="d-flex flex-column gap-2">

        <a href="cambiarContra.php" class="btn-perfil">
            <i class="bi bi-key" style="font-size:17px;"></i>
            Cambiar contraseña
            <i class="bi bi-chevron-right chevron"></i>
        </a>

        <a href="editarUsu.php?id=<?= $_SESSION['usuario_id'] ?>" class="btn-perfil">
            <i class="bi bi-pencil-square" style="font-size:17px;"></i>
            Editar perfil
            <i class="bi bi-chevron-right chevron"></i>
        </a>

        <a href="lista_Usuario.php" class="btn-perfil">
            <i class="bi bi-people" style="font-size:17px;"></i>
            Ver lista de usuarios
            <i class="bi bi-chevron-right chevron"></i>
        </a>

        <a href="logout.php" class="btn-perfil danger">
            <i class="bi bi-box-arrow-right" style="font-size:17px;"></i>
            Cerrar sesión
            <i class="bi bi-chevron-right chevron"></i>
        </a>
    </div>

</div>

</body>
</html>