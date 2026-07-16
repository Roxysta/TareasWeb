<?php
class Conexion {
    private const DB_HOST   = 'localhost';
    private const DB_NOMBRE = 'gestor_archivos2';
    private const DB_USUARIO = 'root';
    private const DB_CLAVE   = '';         
    private const DB_CHARSET = 'utf8mb4';

    private static ?PDO $instancia = null;

    private function __construct() {}

    public static function obtener(): PDO {
        if (self::$instancia === null) {
            $dsn = sprintf(
                'mysql:host=%s;dbname=%s;charset=%s',
                self::DB_HOST,
                self::DB_NOMBRE,
                self::DB_CHARSET
            );

            try {
                self::$instancia = new PDO($dsn, self::DB_USUARIO, self::DB_CLAVE, [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES   => false, // Consultas preparadas reales
                ]);
            } catch (PDOException $e) {
                // No exponer detalles de la BD al usuario
                error_log('Error de conexión BD: ' . $e->getMessage());
                die('Error interno del servidor. Contacta al administrador.');
            }
        }

        return self::$instancia;
    }
}
