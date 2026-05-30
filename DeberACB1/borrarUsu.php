<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: inicioSesion.php");
    exit;
}

require_once 'conexionBD.php';

    if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    $stmt = $conexion->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: lista_Usuario.php");
                exit;
    } else {
        echo "Error al borrar el usuario: " . htmlspecialchars($conexion->error);
    }

    $stmt->close();
    $conexion->close();
}
?>