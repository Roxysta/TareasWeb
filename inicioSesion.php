
Copiar

<?php
// login.php
session_start();
 
// Si ya está autenticado, redirigir al perfil
if (isset($_SESSION['usuario_id'])) {
    header('Location: perfil.php');
    exit;
}
 
require_once 'config/conexion.php';
$error = '';
 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo   = trim($_POST['correo']   ?? '');
    $password = $_POST['password']      ?? '';
 
    if (empty($correo) || empty($password)) {
        $error = 'Completa todos los campos.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Formato de correo inválido.';
    } else {
        $conn = getConexion();
        $stmt = $conn->prepare(
            'SELECT id, nombre, correo, password FROM usuarios WHERE correo = ?'
        );
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $result = $stmt->get_result();
 
        if ($row = $result->fetch_assoc()) {
            if (password_verify($password, $row['password'])) {
                // Crear sesión
                $_SESSION['usuario_id']     = $row['id'];
                $_SESSION['usuario_nombre'] = $row['nombre'];
                $_SESSION['usuario_correo'] = $row['correo'];
 
                header('Location: perfil.php');
                exit;
            }
        }
        $error = 'Credenciales incorrectas.';
        $stmt->close();
        $conn->close();
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
<div class="card">
    <h2>Iniciar sesión</h2>
    <?php if ($error): ?><p class="msg error"><?= htmlspecialchars($error) ?></p><?php endif; ?>
 
    <form method="POST">
        <label>Correo electrónico</label>
        <input type="email" name="correo" required>
 
        <label>Contraseña</label>
        <input type="password" name="password" required>
 
        <button type="submit">Entrar</button>
    </form>
    <p class="link"><a href="registro.php">¿No tienes cuenta? Regístrate</a></p>
</div>
</body>
</html>
 