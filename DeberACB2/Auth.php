<?php

require_once 'Conexion.php';

class Auth {

    /**
     * Inicia sesión verificando credenciales contra la BD.
     * Usa password_verify() para comparar con el hash almacenado.
     *
     * @return array ['ok' => bool, 'mensaje' => string]
     */
    public static function login(string $username, string $password): array {
        // Sanitizar: el username solo puede tener letras, números, guiones y puntos
        if (!preg_match('/^[a-zA-Z0-9._-]{3,50}$/', $username)) {
            return ['ok' => false, 'mensaje' => 'Usuario o contraseña incorrectos.'];
        }

        $pdo  = Conexion::obtener();
        $stmt = $pdo->prepare('SELECT id, username, password_hash, rol FROM usuarios WHERE username = ? LIMIT 1');
        $stmt->execute([$username]);
        $usuario = $stmt->fetch();

        // Verificar que existe el usuario Y que la contraseña coincide con el hash
        if (!$usuario || !password_verify($password, $usuario['password_hash'])) {
            return ['ok' => false, 'mensaje' => 'Usuario o contraseña incorrectos.'];
        }

        // Regenerar ID de sesión para prevenir Session Fixation
        session_regenerate_id(true);

        // Guardar datos mínimos en sesión (nunca la contraseña)
        $_SESSION['user_id']   = $usuario['id'];
        $_SESSION['username']  = $usuario['username'];
        $_SESSION['rol']       = $usuario['rol'];
        $_SESSION['iniciado']  = time();

        return ['ok' => true, 'mensaje' => 'Bienvenido, ' . $usuario['username'] . '.'];
    }

    /**
     * Cierra la sesión destruyendo todos los datos.
     */
    public static function logout(): void {
        $_SESSION = [];

        // Eliminar la cookie de sesión del navegador
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '',
                time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();
    }

    /**
     * Verifica si hay una sesión activa.
     */
    public static function estaAutenticado(): bool {
        return !empty($_SESSION['user_id']) && !empty($_SESSION['rol']);
    }

    /**
     * Devuelve el rol del usuario actual.
     */
    public static function obtenerRol(): string {
        return $_SESSION['rol'] ?? '';
    }

    public static function obtenerUsername(): string {
        return $_SESSION['username'] ?? '';
    }

    public static function tieneRol(string $rol): bool {
        return self::obtenerRol() === $rol;
    }

    public static function requerirSesion(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (!self::estaAutenticado()) {
            header('Location: login.php?msg=sesion_requerida');
            exit;
        }
    }

    /**
     * Redirige con error 403 si el usuario no tiene el rol requerido.
     */
    public static function requerirRol(string $rol): void {
        self::requerirSesion();

        if (!self::tieneRol($rol)) {
            header('Location: index.php?msg=sin_permiso&tipo=error');
            exit;
        }
    }
}
