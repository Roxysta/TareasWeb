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
?>