<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header('Location: inicioSesion.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: perfil.php');
    exit;
}

$id     = intval($_POST['id']);
$nombre = trim($_POST['nombre']);
$correo = trim($_POST['correo']);

if ($id !== $_SESSION['usuario_id']) {
    header('Location: perfil.php');
    exit;
}

if (empty($nombre) || empty($correo)) {
    header('Location: editarUsu.php?id=' . $id . '&error=campos_vacios');
    exit;
}

require_once 'conexionBD.php';

$stmt = $conexion->prepare(
    'UPDATE usuarios SET nombre = ?, correo = ? WHERE id = ?'
);
$stmt->bind_param('ssi', $nombre, $correo, $id);
$stmt->execute();
$stmt->close();
$conexion->close();

header('Location: perfil.php?actualizado=1');
exit;