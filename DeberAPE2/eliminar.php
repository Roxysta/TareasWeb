<?php

session_start();
require_once 'Auth.php';
require_once 'GestorArchivos.php';

Auth::requerirSesion();
Auth::requerirRol('admin');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$tokenEnviado = $_POST['csrf_token'] ?? '';
$tokenSesion  = $_SESSION['csrf_token'] ?? '';

if (!hash_equals($tokenSesion, $tokenEnviado)) {
    header('Location: index.php?msg=' . urlencode('Token CSRF inválido.') . '&tipo=error');
    exit;
}

$nombre = trim($_POST['nombre'] ?? '');

if ($nombre === '') {
    header('Location: index.php?msg=' . urlencode('No se especificó ningún archivo.') . '&tipo=error');
    exit;
}

$gestor    = new GestorArchivos(__DIR__ . '/uploads');
$resultado = $gestor->eliminar($nombre);

if ($resultado['ok']) {
    header('Location: index.php?msg=eliminar_ok&tipo=ok');
} else {
    header('Location: index.php?msg=' . urlencode($resultado['mensaje']) . '&tipo=error');
}
exit;
