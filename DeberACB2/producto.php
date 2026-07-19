<?php
$id = isset($_GET['id']) ? htmlspecialchars($_GET['id']) : null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Producto <?php echo $id ?? '?'; ?></title>
  <style>
    body { font-family: Arial, sans-serif; max-width: 700px; margin: 60px auto; padding: 0 20px; color: #222; }
    .info { background: #eafaf1; border-left: 4px solid #27ae60; padding: 15px 20px; }
    a { color: #2980b9; }
  </style>
</head>
<body>
  <h1>Detalle del producto</h1>

  <?php if ($id): ?>
    <div class="info">
      <p><strong>ID recibido:</strong> <?php echo $id; ?></p>
      <p><strong>URL amigable usada:</strong> /producto/<?php echo $id; ?></p>
      <p><strong>URL real interna:</strong> /producto.php?id=<?php echo $id; ?></p>
    </div>
  <?php else: ?>
    <p>No se recibió ningún ID de producto.</p>
  <?php endif; ?>

  <p><a href="/">&larr; Volver al inicio</a></p>
</body>
</html>
