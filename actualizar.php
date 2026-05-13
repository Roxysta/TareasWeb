<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicioSesion.php");
    exit;
}

include 'conexionBD.php';

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id     = intval($_POST['id']);
    $cedula = intval($_POST['cedula']);
    $nombre = trim($_POST['nombre'] ?? '');
    $correo = trim($_POST['correo'] ?? '');

    $stmt = $conexion->prepare(
        "UPDATE usuarios SET cedula = ?, nombre = ?, correo = ? WHERE id = ?"
    );
    $stmt->bind_param("issi", $cedula, $nombre, $correo, $id);

    if ($stmt->execute()) {
        header("Location: lista_Usuario.php");
        exit;
    } else {
        echo "Error al actualizar: " . htmlspecialchars($conexion->error);
    }

    $stmt->close();
    $conexion->close();
}
?>