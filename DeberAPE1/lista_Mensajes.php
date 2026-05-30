<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: iniciarSesion.php');
    exit;
}

require_once 'conexionBaseDatos.php';

$resultado = $conexion->query('SELECT id, nombre, correo, mensaje, fecha_envio FROM mensajes ORDER BY nombre ASC');
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Mensajes</title>
    <link rel="stylesheet" href="estilos/lista_mensajes.css">
</head>
<body>
<div class="contenedor">

 <div class="header">
        <h2>Lista de <em>mensajes</em></h2>
        <a href="cerrarSesion.php" class="btn-cerrar">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none"
                 stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                <polyline points="16 17 21 12 16 7"/>
                <line x1="21" y1="12" x2="9" y2="12"/>
            </svg>
            Cerrar sesión
        </a>
    </div>

    <?php if ($resultado->num_rows === 0): ?>
        <div class="table-wrap">
            <p class="vacio">No hay mensajes registrados.</p>
        </div>
    <?php else: ?>
        <div class="table-wrap">
            <table>
                <thead>
                    <tr>
                        <th>Id</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Mensaje</th>
                        <th>Fecha de envío</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($mensaje = $resultado->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($mensaje['id']) ?></td>
                        <td><?= htmlspecialchars($mensaje['nombre']) ?></td>
                        <td><?= htmlspecialchars($mensaje['correo']) ?></td>
                        <td><?= nl2br(htmlspecialchars($mensaje['mensaje'])) ?></td>
                        <td><?= htmlspecialchars($mensaje['fecha_envio']) ?></td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>

    <p class="footer-link"><a href="index.html">← Contacto del Estudiante</a></p>
</div>
</body>
</html>