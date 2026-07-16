<?php

session_start();
require_once 'Auth.php';

// Solo acepta POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

// Validar token CSRF
$tokenEnviado = $_POST['csrf_token'] ?? '';
$tokenSesion  = $_SESSION['csrf_token'] ?? '';

if (!hash_equals($tokenSesion, $tokenEnviado)) {
    $_SESSION['login_error'] = 'Solicitud inválida. Recarga la página e inténtalo de nuevo.';
    header('Location: login.php');
    exit;
}

$username = trim($_POST['username'] ?? '');
$password = $_POST['password'] ?? '';   // No hacer trim a contraseñas

if ($username === '' || $password === '') {
    $_SESSION['login_error'] = 'Completa todos los campos.';
    header('Location: login.php');
    exit;
}

$resultado = Auth::login($username, $password);

if ($resultado['ok']) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    header('Location: index.php');
} else {
    $_SESSION['login_error'] = $resultado['mensaje'];
    header('Location: login.php');
}
exit;
