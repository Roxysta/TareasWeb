<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: inicioSesion.php');
    exit;
}

require_once 'conexionBD.php';

$resultado = $conexion->query('SELECT id, cedula, nombre, correo, fecha_registro FROM usuarios ORDER BY nombre ASC');
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Usuarios</title>
    <link rel="stylesheet" href="estilosCSS/lista_usuario.css">
</head>
<body>
<div class="contenedor">

    <div class="header">
        <h2>Lista de Usuarios</h2>
    </div>

    <?php if ($resultado->num_rows === 0): ?>
        <div class="table-wrap">
            <p class="vacio">No hay usuarios registrados.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Cédula</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Fecha de registro</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['cedula']) ?></td>
                        <td><?= htmlspecialchars($usuario['nombre']) ?></td>
                        <td><?= htmlspecialchars($usuario['correo']) ?></td>
                        <td><?= htmlspecialchars($usuario['fecha_registro']) ?></td>
                        <td>
                            <div class="acciones">
                                <a href="borrarUsu.php?id=<?= $usuario['id'] ?>" class="btn-del"
                                   onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">Eliminar</a>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <p class="footer-link"><a href="perfil.php">← Ver Perfil del Usuario</a></p>
</div>
</body>
</html>