<?php
$host     = 'localhost';
$usuario  = 'user_pagina';
$contrasena = 'Clavepagina123!';
$base_datos = 'pagina_web';

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}