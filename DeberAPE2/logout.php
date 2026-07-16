<?php

session_start();
require_once 'Auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$tokenEnviado = $_POST['csrf_token'] ?? '';
$tokenSesion  = $_SESSION['csrf_token'] ?? '';

if (!hash_equals($tokenSesion, $tokenEnviado)) {
    header('Location: index.php?msg=error_csrf&tipo=error');
    exit;
}

Auth::logout();
header('Location: login.php?msg=logout_ok');
exit;
