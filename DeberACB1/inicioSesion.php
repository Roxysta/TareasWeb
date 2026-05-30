<?php
session_start();

if (isset($_SESSION['usuario_id'])) {
    header('Location: lista_Usuario.php');
    exit;
}

require_once 'conexionBD.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo   = trim($_POST['correo']   ?? '');
    $password = $_POST['password']      ?? '';

    if (empty($correo) || empty($password)) {
        $error = 'Completa todos los campos.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Formato de correo inválido.';
    } else {
        $stmt = $conexion->prepare(
            'SELECT id, nombre, correo, password FROM usuarios WHERE correo = ?'
        );
        $stmt->bind_param('s', $correo);
        $stmt->execute();
        $result = $stmt->get_result();
        $row    = $result->fetch_assoc();
        $stmt->close();
        $conexion->close();

        if ($row && password_verify($password, $row['password'])) {
            $_SESSION['usuario_id']     = $row['id'];
            $_SESSION['usuario_nombre'] = $row['nombre'];
            $_SESSION['usuario_correo'] = $row['correo'];

            header('Location: lista_Usuario.php');
            exit;
        }

        $error = 'Credenciales incorrectas.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="estilosCSS/estiloInicio.css">
</head>
<body>
<div class="card">
    <h2>Iniciar sesión</h2>

    <?php if ($error): ?>
        <p class="msg error"><?= htmlspecialchars($error) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Correo electrónico</label>
        <input type="email" name="correo"
               value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>" required>

        <label>Contraseña</label>
        <input type="password" name="password" required>

        <button type="submit">Entrar</button>
    </form>

    <p class="link"><a href="guardarUsu.php">¿No tienes cuenta? Regístrate</a></p>
</div>
</body>
</html>