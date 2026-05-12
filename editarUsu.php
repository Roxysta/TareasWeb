<?php
if (isset($_GET["id"])) {
    $conexion = new mysqli("localhost", "root", "", "sistema_php");
    if ($conexion->connect_error) die("Error de conexión");

    $id = intval($_GET["id"]);
    $insertar = $conexion->prepare("SELECT id, cedula, nombre, correo, fecha_registro FROM usuarios WHERE id = ?");
    $insertar->bind_param("i", $id);
    $insertar->execute();
    $resultado = $insertar->get_result();
    $usuario = $resultado->fetch_assoc();
    }                                       
?>