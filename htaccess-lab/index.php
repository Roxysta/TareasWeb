<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Laboratorio .htaccess</title>
    <style>
        body { font-family: Arial, sans-serif; max-width: 700px; margin: 60px auto; padding: 0 20px; color: #222; }
        h1 { color: #2c3e50; }
        a { color: #2980b9; }
        .box { background: #f4f6f7; border-left: 4px solid #2980b9; padding: 15px 20px; margin: 20px 0; }
        code { background: #eee; padding: 2px 6px; border-radius: 3px; }
    </style>
</head>
<body>
    <h1>Sitio de prueba para el laboratorio de .htaccess</h1>
    <p>Esta pagina forma parte de la actividad practica sobre configuraciones de Apache mediante .htaccess.</p>

    <div class="box">
        <strong>Enlaces de prueba:</strong><br>
        1. Carpeta protegida con contrasena: <a href="protegido/">/protegido/</a><br>
        2. URL amigable (reescritura): <a href="producto/3">/producto/3</a> (equivale a producto.php?id=3)<br>
        3. Pagina de error personalizada: <a href="pagina-que-no-existe">/pagina-que-no-existe</a>
    </div>

    <p>Si esta pagina se carga con <code>http://</code> en el navegador local, la regla del .htaccess deberia
    redirigirla automaticamente a <code>https://</code> cuando el sitio se sirve con SSL habilitado.</p>
</body>
</html>
