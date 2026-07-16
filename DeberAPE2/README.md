# GestorArchivos.php — Módulo Seguro de Gestión de Archivos

Sistema web desarrollado en PHP con Programación Orientada a Objetos (POO), autenticación por sesiones, control de roles y base de datos MySQL.

## Descripción del sistema

GestorArchivos permite gestionar archivos de forma segura desde el navegador. El sistema cuenta con dos niveles de acceso:

| Acción | Rol `usuario` | Rol `admin` |
|---|:---:|:---:|
| Iniciar sesión | ✔ | ✔ |
| Subir archivos (PDF, JPG, PNG) | ✔ | ✔ |
| Ver el listado de archivos | ✔ | ✔ |
| Eliminar archivos | ✖ | ✔ |

---

## Estructura de archivos

```
gestor-archivos/
│
├── login.php            ← Formulario de inicio de sesión
├── procesar_login.php   ← Valida credenciales y crea la sesión
├── logout.php           ← Cierra la sesión
│
├── index.php            ← Panel principal (protegido)
├── subir.php            ← Procesa la subida de archivos (protegido)
├── eliminar.php         ← Elimina archivos (protegido, solo admin)
│
├── Auth.php             ← Clase de autenticación y sesiones
├── Conexion.php         ← Clase Singleton para conexión PDO/MySQL
├── GestorArchivos.php   ← Clase principal de gestión de archivos
│
├── database.sql         ← Script SQL para crear la base de datos
├── README.md            ← Este archivo
│
├── assets/
│   ├── estilo.css       ← Hoja de estilos propia
│   └── app.js           ← JavaScript (drag & drop y modal)
│
└── uploads/
    └── .htaccess        ← Bloquea ejecución de scripts en esta carpeta
```

---

## Instrucciones de instalación

Siga estos pasos en orden. Saltarse alguno causará errores de conexión o de login.

### Paso 1 — Iniciar XAMPP

Abra XAMPP Control Panel y asegúrese de que **Apache** y **MySQL** estén corriendo.

### Paso 2 — Copiar el proyecto

Copia la carpeta `gestor-archivos` dentro de:
```
C:\xampp\htdocs\
```
Debe quedar algo así:
```
C:\xampp\htdocs\gestor-archivos\
```

### Paso 3 — Crear la base de datos en phpMyAdmin

1. Abre el navegador y ve a `http://localhost/phpmyadmin`
2. Haz clic en la pestaña **SQL** (en la barra superior)
3. Abre el archivo `database.sql` con un editor de texto (Notepad, VSCode, etc.)
4. Copia todo el contenido y pégalo en el cuadro de texto de phpMyAdmin
5. Haz clic en **Continuar**

Esto creará automáticamente:
- La base de datos `gestor_archivos`
- La tabla `usuarios` con dos usuarios de prueba
- La tabla `archivos` donde se guardan los metadatos de los archivos subidos

### Paso 4 — Ingresar al sistema

Abre el navegador y ve a:
```
http://localhost/gestor-archivos/login.php
```

Usa las siguientes credenciales:

| Usuario | Contraseña | Rol |
|---|---|---|
| admin | Admin2024! | Administrador — puede subir, ver y eliminar |
| usuario1 | User2024! | Usuario básico — solo puede subir y ver |

---

## Medidas de seguridad aplicadas

### Autenticación y sesiones

| Medida | Implementación |
|---|---|
| Contraseñas con hash bcrypt | `password_hash()` y `password_verify()` |
| Prevención de Session Fixation | `session_regenerate_id(true)` tras login exitoso |
| Token CSRF en todos los formularios | Token único por sesión verificado con `hash_equals()` |
| Logout protegido contra CSRF | El cierre de sesión requiere POST + token válido |
| Mensajes de error genéricos | "Usuario o contraseña incorrectos" sin revelar cuál falla |
| Control de acceso por rol | `Auth::requerirRol('admin')` en `eliminar.php` |
| Ocultamiento del botón Eliminar | El HTML del botón solo se genera si el rol es admin |

### Manejo de archivos

| Medida | Implementación |
|---|---|
| Validación de tipo MIME real | `finfo(FILEINFO_MIME_TYPE)` analiza el contenido binario del archivo |
| Lista blanca de extensiones | Solo `.pdf`, `.jpg`, `.jpeg`, `.png` |
| Coherencia MIME ↔ extensión | Un `.jpg` con contenido PDF es rechazado |
| Renombrado con hash aleatorio | `bin2hex(random_bytes(16))` genera un nombre de 32 caracteres |
| Prevención de path traversal | `basename()` + expresión regular + `realpath()` |
| Bloqueo de ejecución en `/uploads` | `.htaccess` deshabilita PHP dentro de la carpeta |
| Cabecera anti-MIME sniffing | `X-Content-Type-Options: nosniff` |

### Base de datos

| Medida | Implementación |
|---|---|
| Consultas preparadas | `PDO::prepare()` + `execute([...])` en todos los accesos a BD |
| Prevención de SQL Injection | Sin concatenación de variables en ninguna consulta SQL |
| Errores de BD no expuestos | `error_log()` en el servidor, mensaje genérico al usuario |

/------GRACIAS POR LEER Y VER MI TAREA-------/
