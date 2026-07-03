<?php

session_start();
require_once 'Auth.php';

if (Auth::estaAutenticado()) {
    header('Location: index.php');
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensajes = [
    'sesion_requerida' => ['texto' => 'Debes iniciar sesión para acceder.', 'tipo' => 'info'],
    'logout_ok'        => ['texto' => 'Sesión cerrada correctamente.',       'tipo' => 'ok'],
];

$msg  = $_GET['msg'] ?? '';
$alerta = $mensajes[$msg] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión — GestorArchivos</title>
    <link rel="stylesheet" href="assets/estilo.css">
    <link rel="stylesheet" href="assets/estilologin.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=IBM+Plex+Mono:wght@400;600&family=IBM+Plex+Sans:wght@300;400;600&display=swap" rel="stylesheet">
</head>
<body>

<header class="site-header">
    <div class="container header-inner">
        <div class="brand">
            <span class="brand-icon">&#9632;</span>
            <span class="brand-name">GestorArchivos<span class="brand-dot">-Richard Zambrano</span></span>
        </div>
        <nav class="site-nav" aria-label="Acceso">
            <span class="nav-link" style="color:var(--muted);cursor:default">Acceso restringido</span>
        </nav>
    </div>
</header>

<main class="main-content container" style="justify-content:center;align-items:center;display:flex">
    <div class="login-wrap">

        <div class="login-brand" aria-hidden="true">
            <span class="brand-icon">&#9632;</span>
            <div class="brand-name">GestorArchivos<span class="brand-dot">-Richard Zambrano</span></div>
            <div class="login-sub">Sistema de Gestión de archivos</div>
        </div>

        <?php if ($alerta): ?>
        <div class="alerta alerta-<?= htmlspecialchars($alerta['tipo'], ENT_QUOTES, 'UTF-8') ?>" role="alert" style="margin-bottom:1rem">
            <span class="alerta-icono"><?= $alerta['tipo'] === 'ok' ? '✔' : 'ℹ' ?></span>
            <?= htmlspecialchars($alerta['texto'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['login_error'])): ?>
        <div class="alerta alerta-error" role="alert" style="margin-bottom:1rem">
            <span class="alerta-icono">✖</span>
            <?= htmlspecialchars($_SESSION['login_error'], ENT_QUOTES, 'UTF-8') ?>
        </div>
        <?php unset($_SESSION['login_error']); ?>
        <?php endif; ?>

        <section class="card" aria-labelledby="titulo-login">
            <h1 id="titulo-login" class="card-titulo" style="margin-bottom:1.25rem">
                <span class="num">→</span> Iniciar sesión
            </h1>

            <form action="procesar_login.php" method="POST" novalidate>
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

                <div class="form-group">
                    <label for="username" class="form-label">Usuario</label>
                    <input type="text"
                           id="username"
                           name="username"
                           class="form-input"
                           placeholder="Tu nombre de usuario"
                           autocomplete="username"
                           maxlength="50"
                           required>
                </div>

                <div class="form-group">
                    <label for="password" class="form-label">Contraseña</label>
                    <input type="password"
                           id="password"
                           name="password"
                           class="form-input"
                           placeholder="••••••••"
                           autocomplete="current-password"
                           maxlength="100"
                           required>
                </div>

                <button type="submit" class="btn btn-primario btn-login">
                    Entrar →
                </button>
            </form>
        </section>

    </div>
</main>

<footer class="site-footer">
    <div class="container footer-inner">
        <span>GestorArchivos.php &copy; <?= date('Y') ?></span>
        <span class="footer-sep">·</span>
        <span>Acceso seguro con sesiones PHP</span>
    </div>
</footer>

<script src="assets/app.js"></script>
</body>
</html>
