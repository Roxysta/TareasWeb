# Laboratorio .htaccess - Guia de instalacion y pruebas

Este paquete contiene un mini sitio web listo para probar 4 configuraciones
clasicas de Apache mediante `.htaccess`. Sigue estos pasos en tu propia
maquina (no es necesario internet, todo funciona en local).

## 1) Requisitos

Instala un servidor local con Apache + PHP y mod_rewrite habilitado. Las
opciones mas comunes:

- **XAMPP** (Windows / Linux / Mac): https://www.apachefriends.org
- **WAMP** (Windows): https://www.wampserver.com
- **MAMP** (Mac): https://www.mamp.info

## 2) Copiar el proyecto

1. Copia toda la carpeta `htaccess-lab` dentro de la carpeta publica de tu
   servidor:
   - XAMPP: `C:\xampp\htdocs\htaccess-lab` (Windows) o `/opt/lampp/htdocs/htaccess-lab` (Linux)
   - WAMP: `C:\wamp64\www\htaccess-lab`
   - MAMP: `/Applications/MAMP/htdocs/htaccess-lab`

## 3) Habilitar mod_rewrite y AllowOverride

Para que las reglas de `.htaccess` funcionen, Apache debe permitirlo:

1. Abre `httpd.conf` y descomenta la linea:
   `LoadModule rewrite_module modules/mod_rewrite.so`
2. Busca el bloque `<Directory>` que apunta a tu carpeta `htdocs`/`www` y
   cambia:
   `AllowOverride None`  por  `AllowOverride All`
3. Reinicia Apache.

## 4) Ajustar la ruta del .htpasswd

Edita `protegido/.htaccess` y reemplaza la linea `AuthUserFile` con la ruta
REAL en tu computadora, por ejemplo:

```
AuthUserFile "C:/xampp/htdocs/htaccess-lab/.htpasswd"
```

o en Linux/Mac:

```
AuthUserFile "/opt/lampp/htdocs/htaccess-lab/.htpasswd"
```

## 5) Usuario y contrasena de prueba

El archivo `.htpasswd` ya incluye un usuario de ejemplo:

- **Usuario:** `estudiante`
- **Contrasena:** `ClaveSegura2026`

Si quieres generar tu propio usuario/contrasena, usa (si tienes el
paquete apache2-utils / xampp shell):

```
htpasswd -c .htpasswd tu_usuario
```

o con OpenSSL:

```
openssl passwd -apr1 tu_contrasena
```

y pega el resultado en `.htpasswd` con el formato `usuario:hash`.

## 6) Probar HTTPS local (opcional, requiere certificado)

Para probar la redireccion http -> https en local, activa el modulo SSL de
tu servidor (XAMPP trae `httpd-ssl.conf` con un certificado autofirmado ya
configurado) y accede primero a `http://localhost/htaccess-lab/` para
comprobar que redirige automaticamente a `https://localhost/htaccess-lab/`.
El navegador mostrara una advertencia de certificado no confiable porque es
autofirmado; es normal en un entorno de pruebas y se puede continuar.

## 7) Pruebas a realizar (y capturar en pantalla)

1. **HTTP -> HTTPS**: entra por `http://localhost/htaccess-lab/` y verifica
   que la barra de direcciones cambia sola a `https://`.
2. **Carpeta protegida**: entra a `http://localhost/htaccess-lab/protegido/`
   y verifica que el navegador pide usuario y contrasena antes de mostrar
   la pagina.
3. **Error 404 personalizado**: entra a una URL inexistente, por ejemplo
   `http://localhost/htaccess-lab/pagina-que-no-existe` y verifica que se
   muestra la pagina de error personalizada (no la de Apache por defecto).
4. **URL amigable**: entra a `http://localhost/htaccess-lab/producto/3` y
   verifica que se muestra la ficha del producto con ID 3, igual que si
   hubieras entrado a `producto.php?id=3`.

## 8) Video

Graba tu pantalla mostrando las 4 pruebas anteriores y explica, para cada
una, que linea del `.htaccess` la produce (puedes apoyarte en los
comentarios que ya estan dentro de cada archivo `.htaccess`).
