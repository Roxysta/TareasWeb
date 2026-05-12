<?php 

    include 'conexionBD.php';

    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        
        $id = trim($_POST["id"]);
        $cedula = trim($_POST["cedula"]);
        $nombre = trim($_POST["nombre"]);
        $correo = trim($_POST["correo"]);
        $contrasena = $_POST["contrasena"];
        $fecha_registro = date("Y-m-d H:i:s");

        $contraseña_segura = password_hash($contrasena, PASSWORD_DEFAULT);

        $verificacion = $conexion->prepare("SELECT * FROM usuarios WHERE correo = ?");

        $verificacion->bind_param("s", $correo);

        $verificacion->execute();

        $verificacion->store_result();

        if ($verificacion->num_rows > 0) {
            echo "El correo ya está registrado.";
        } else {
            $insertar = $conexion->prepare("INSERT INTO usuarios (cedula, nombre, correo, contrasena, fecha_registro) VALUES (?, ?, ?, ?, ?)");

            $insertar->bind_param("isss", $cedula, $nombre, $correo, $contraseña_segura, $fecha_registro);

            if ($insertar->execute()) {
                echo "Usuario registrado exitosamente.";
                echo "<p style='text-align: center;'><a href='lista_Usuario.php'>Ver lista de usuarios</a></p>";
            } else {
                echo "Error al registrar el usuario: " . $conexion->error;
            }


            $insertar->close();
        }

        $verificacion->close();

    }

    $conexion->close();
?>