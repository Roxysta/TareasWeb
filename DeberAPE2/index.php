<?php

session_start();
require_once 'Auth.php';
require_once 'GestorArchivos.php';

Auth::requerirSesion();

$gestor   = new GestorArchivos(__DIR__ . '/uploads');
$archivos = $gestor->listar();
$esAdmin  = Auth::tieneRol('admin');
$username = Auth::obtenerUsername();
$rol      = Auth::obtenerRol();

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$mensaje = '';
$tipo    = '';
if (!empty($_GET['msg'])) {
    $msg  = $_GET['msg'];
    $tipo = $_GET['tipo'] ?? 'info';
    $tabla = [
        'subida_ok'   => 'Archivo subido correctamente.',
        'eliminar_ok' => 'Archivo eliminado correctamente.',
        'sin_permiso' => 'No tienes permisos para realizar esa acción.',
    ];
    $mensaje = htmlspecialchars($tabla[$msg] ?? $msg, ENT_QUOTES, 'UTF-8');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GestorArchivos — Panel de archivos</title>
    <link rel="stylesheet" href="assets/estilo.css">
    <link rel="stylesheet" href="assets/estiloindex.css">
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
        <nav class="site-nav" aria-label="Navegación principal">
            <a href="index.php" class="nav-link active">Archivos</a>
            <a href="#subir" class="nav-link">Subir</a>
            <a href="README.md" class="nav-link" target="_blank">Docs</a>
        </nav>
  
        <div class="user-chip">
            <span class="user-name"><?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?></span>
            <span class="rol-badge rol-<?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?>">
                <?= htmlspecialchars($rol, ENT_QUOTES, 'UTF-8') ?>
            </span>
            <form action="logout.php" method="POST" style="margin:0">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <button type="submit" class="btn-logout" aria-label="Cerrar sesión">
                    Salir →
                </button>
            </form>
        </div>
    </div>
</header>

<main class="main-content container">

    <?php if ($mensaje): ?>
    <div class="alerta alerta-<?= htmlspecialchars($tipo, ENT_QUOTES, 'UTF-8') ?>"
         role="alert" aria-live="polite">
        <span class="alerta-icono">
            <?= $tipo === 'ok' ? '✔' : ($tipo === 'error' ? '✖' : 'ℹ') ?>
        </span>
        <?= $mensaje ?>
    </div>
    <?php endif; ?>

    <!-- ── SECCIÓN: SUBIR ARCHIVO ── -->
    <section class="card" id="subir" aria-labelledby="titulo-subir">
        <h2 id="titulo-subir" class="card-titulo">
            <span class="num">01</span> Subir archivo
        </h2>
        <p class="card-desc">
            Formatos aceptados: <strong>PDF, JPG, PNG</strong> · Tamaño máximo: <strong>5 MB</strong>
        </p>

        <form action="subir.php" method="POST" enctype="multipart/form-data"
              class="upload-form" id="formSubir">
            <input type="hidden" name="csrf_token"
                   value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">

            <label for="archivo" class="drop-area" id="dropArea">
                <div class="drop-icon">&#8679;</div>
                <span class="drop-text">
                    Arrastra un archivo aquí o <u>haz clic para seleccionar</u>
                </span>
                <span class="drop-nombre" id="dropNombre"></span>
                <input type="file" id="archivo" name="archivo"
                       accept=".pdf,.jpg,.jpeg,.png" class="file-input" required>
            </label>

            <button type="submit" class="btn btn-primario">Subir archivo</button>
        </form>
    </section>

    <!-- ── LISTADO ── -->
    <section class="card" aria-labelledby="titulo-lista">
        <h2 id="titulo-lista" class="card-titulo">
            <span class="num">02</span> Archivos subidos
            <span class="badge"><?= count($archivos) ?></span>
        </h2>

        <?php if (empty($archivos)): ?>
            <p class="vacio">Aún no hay archivos. Sube el primero desde el formulario.</p>
        <?php else: ?>
        <div class="tabla-wrapper">
            <table class="tabla-archivos" role="grid">
                <thead>
                    <tr>
                        <th scope="col">Tipo</th>
                        <th scope="col">Nombre original</th>
                        <th scope="col">Tamaño</th>
                        <th scope="col">Fecha de subida</th>
                        <th scope="col">Subido por</th>
                        <th scope="col">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($archivos as $a): ?>
                    <tr>
                        <td>
                            <span class="ext-badge ext-<?= htmlspecialchars($a['extension'], ENT_QUOTES, 'UTF-8') ?>">
                                <?= strtoupper(htmlspecialchars($a['extension'], ENT_QUOTES, 'UTF-8')) ?>
                            </span>
                        </td>
                        <td class="nombre-col"
                            title="<?= htmlspecialchars($a['nombre_original'], ENT_QUOTES, 'UTF-8') ?>">
                            <?= htmlspecialchars($a['nombre_original'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="mono"><?= htmlspecialchars($a['tamano'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="mono"><?= htmlspecialchars($a['fecha'], ENT_QUOTES, 'UTF-8') ?></td>
                        <td class="subido-por">
                            <?= htmlspecialchars($a['subido_por'], ENT_QUOTES, 'UTF-8') ?>
                        </td>
                        <td class="acciones-col">
                            <a href="uploads/<?= urlencode($a['nombre']) ?>"
                               download="<?= htmlspecialchars($a['nombre_original'], ENT_QUOTES, 'UTF-8') ?>"
                               class="btn btn-sm btn-secundario"
                               aria-label="Ver <?= htmlspecialchars($a['nombre_original'], ENT_QUOTES, 'UTF-8') ?>">
                                &#8681; Ver
                            </a>

                            <?php if ($esAdmin): ?>
                            <!-- Botón eliminar: SOLO visible para administradores -->
                            <button class="btn btn-sm btn-peligro"
                                    onclick="confirmarEliminar(
                                        '<?= htmlspecialchars($a['nombre'], ENT_QUOTES, 'UTF-8') ?>',
                                        '<?= htmlspecialchars($a['nombre_original'], ENT_QUOTES, 'UTF-8') ?>'
                                    )"
                                    aria-label="Eliminar <?= htmlspecialchars($a['nombre_original'], ENT_QUOTES, 'UTF-8') ?>">
                                &#10006; Eliminar
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php endif; ?>

        <?php if (!$esAdmin): ?>
        <p style="font-family:var(--mono);font-size:0.72rem;color:var(--muted);margin-top:1rem">
            ℹ Tu rol <strong style="color:#60A5FA">usuario</strong>
            solo permite subir y ver archivos.
            Contacta al administrador para eliminar archivos.
        </p>
        <?php endif; ?>
    </section>

</main>

<!-- ════════════════CONFIRMACIÓN (solo el admin lo ve) ════════════════ -->
<?php if ($esAdmin): ?>
<div id="modal" class="modal-overlay" role="dialog" aria-modal="true"
     aria-labelledby="modal-titulo" hidden>
    <div class="modal-caja">
        <h3 id="modal-titulo" class="modal-titulo">Confirmar eliminación</h3>
        <p id="modal-desc" class="modal-desc"></p>
        <div class="modal-acciones">
            <button class="btn btn-secundario" onclick="cerrarModal()">Cancelar</button>
            <form id="formEliminar" action="eliminar.php" method="POST">
                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token'], ENT_QUOTES, 'UTF-8') ?>">
                <input type="hidden" name="nombre" id="inputNombreEliminar">
                <button type="submit" class="btn btn-peligro">Sí, eliminar</button>
            </form>
        </div>
    </div>
</div>
<?php endif; ?>

<footer class="site-footer">
    <div class="container footer-inner">
        <span>GestorArchivos.php &copy; <?= date('Y') ?></span>
        <span class="footer-sep">·</span>
        <span>Sesión activa:
            <strong style="color:var(--accent)">
                <?= htmlspecialchars($username, ENT_QUOTES, 'UTF-8') ?>
            </strong>
        </span>
    </div>
</footer>

<script src="assets/app.js"></script>
</body>
</html>
