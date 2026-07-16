<?php
// producto.php
// Este script recibe el parametro "id" tradicionalmente asi:
//   misitio.com/producto.php?id=3
// Gracias a la regla de mod_rewrite del .htaccess, el visitante puede
// escribir la URL amigable:
//   misitio.com/producto/3
// y Apache internamente la reescribe hacia este mismo script.

$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Producto</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 60px auto; padding: 0 20px; }
    </style>
</head>
<body>
    <p><a href="../index.php">&larr; Volver al inicio</a></p>
    <h1>Ficha de producto</h1>
    <?php if ($id): ?>
        <p>Estas viendo el producto con ID: <strong><?php echo $id; ?></strong></p>
        <p>La URL real procesada por PHP fue <code>producto.php?id=<?php echo $id; ?></code>,
        pero gracias al <code>.htaccess</code> el usuario pudo escribir una URL amigable.</p>
    <?php else: ?>
        <p>No se recibio ningun ID de producto.</p>
    <?php endif; ?>
</body>
</html>
