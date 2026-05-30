<?php
require_once 'conexionBaseDatos.php';

header('Content-Type: text/plain; charset=utf-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo 'Método no permitido.';
    exit;
}

$nombre  = trim($_POST['nombre']  ?? '');
$correo  = trim($_POST['correo']  ?? '');
$mensaje = trim($_POST['mensaje'] ?? '');

if (empty($nombre) || empty($correo) || empty($mensaje)) {
    http_response_code(400);
    echo 'Todos los campos son obligatorios.';
    exit;
}

if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo 'Correo inválido.';
    exit;
}

if (strlen($mensaje) > 1000) {
    http_response_code(400);
    echo 'El mensaje es demasiado largo.';
    exit;
}

$stmt = $conexion->prepare(
    'INSERT INTO mensajes (nombre, correo, mensaje, fecha_envio) VALUES (?, ?, ?, NOW())'
);
$stmt->bind_param('sss', $nombre, $correo, $mensaje);

if ($stmt->execute()) {
    echo 'ok';
} else {
    http_response_code(500);
    echo 'Error al guardar el mensaje.';
}

$stmt->close();
$conexion->close();
