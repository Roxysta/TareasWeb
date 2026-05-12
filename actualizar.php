<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $conexion = new mysqli("localhost", "root", "", "sistema_php");
    if ($conexion->connect_error) die("Error de conexión");

    $id = intval($_POST['id']);
    $cedula = intval($_POST['cedula']);
    $nombre = $_POST['nombre'];
    $correo = $_POST['correo'];
    $fecha_registro = date("Y-m-d H:i:s");

    $stmt = $conexion->prepare("UPDATE usuarios SET cedula = ?, nombre = ?, apellidos = ?, correo = ?, fecha_registro = ? WHERE id = ?");
    $stmt->bind_param("iissis", $cedula, $nombre, $apellidos, $correo, $fecha_registro, $id);

    if ($stmt->execute()) {
        header("Location: lista_Usuario.php");
    } else {
        echo "Error al actualizar el usuario.";
    }

    $stmt->close();
    $conexion->close();
}
?>