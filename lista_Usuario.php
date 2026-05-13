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
    <link rel="stylesheet" href="musica1.css">
</head>
<body>
<div class="contenedor">
    <h2>Lista de Usuarios</h2>

    <p><a href="registrar_usu.html">+ Registrar nuevo usuario</a></p>

    <?php if ($resultado->num_rows === 0): ?>
        <p>No hay usuarios registrados.</p>
    <?php else: ?>
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
                        <a href="editarUsu.php?id=<?= $usuario['id'] ?>">Editar</a>
                        <a href="borrarUsu.php?id=<?= $usuario['id'] ?>"
                           onclick="return confirm('¿Seguro que deseas eliminar este usuario?')">
                           Eliminar
                        </a>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <p><a href="perfil.php">← Volver al perfil</a></p>
</div>
</body>
</html>