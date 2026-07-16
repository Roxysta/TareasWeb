<?php


session_start();
require_once 'Auth.php';
require_once 'GestorArchivos.php';

Auth::requerirSesion();

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

if (!isset($_FILES['archivo']) || $_FILES['archivo']['error'] === UPLOAD_ERR_NO_FILE) {
    header('Location: index.php?msg=' . urlencode('No se seleccionó ningún archivo.') . '&tipo=error');
    exit;
}

// Obtener el ID del usuario de la sesión para guardarlo en BD
$usuarioId = (int) $_SESSION['user_id'];

$gestor    = new GestorArchivos(__DIR__ . '/uploads');
$resultado = $gestor->subir($_FILES['archivo'], $usuarioId);

if ($resultado['ok']) {
    header('Location: index.php?msg=subida_ok&tipo=ok');
} else {
    header('Location: index.php?msg=' . urlencode($resultado['mensaje']) . '&tipo=error');
}
exit;
