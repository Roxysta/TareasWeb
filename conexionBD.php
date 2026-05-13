<?php
$host     = 'localhost';
$usuario  = 'root';
$contrasena = '';
$base_datos = 'sistema_php';

$conexion = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}