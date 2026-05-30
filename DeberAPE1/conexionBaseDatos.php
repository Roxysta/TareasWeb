<?php
$host     = 'sql305.infinityfree.com';
$usuario  = 'if0_42050770';
$contrasena = 'TGuimlxDh3zW';
$base_datos = 'if0_42050770_pagina_web';

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}