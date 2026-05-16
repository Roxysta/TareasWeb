# TareasWeb

IMPORTANTE:

No olvidar que para el correcto funcionamiento de toda la tarea se debe abrir todo archivo desde un localhost. asi: http://localhost/DesarrolloWeb/portada.html de lo contrario no funcionara correctamente

DESCRIPCION:
En esta tarea se dio uso al Lenguaje PHP. HTML Y CSS principalmente para la creacion de las paginas web añadiendo una conexion a una base de datos la cual fue hecha de la siguiente forma:


CREATE DATABASE IF NOT EXISTS sistema_php
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

use sistema_php;

CREATE TABLE IF NOT EXISTS usuarios (
    id             INT(11)      NOT NULL AUTO_INCREMENT,
    cedula         VARCHAR(20)  NOT NULL UNIQUE,
    nombre         VARCHAR(100) NOT NULL,
    correo         VARCHAR(150) NOT NULL UNIQUE,
    password       VARCHAR(255) NOT NULL,          -- bcrypt hash (password_hash)
    fecha_registro DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

//Ejemplos de como insertar en la base de datos usando el lenguaje SQL

INSERT INTO usuarios (cedula, nombre, correo, password) VALUES (
    '0912345678',
    'Juan Pérez',
    'juan@ejemplo.com',
    '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi'
);

INSERT INTO usuarios (cedula, nombre, correo, password)VALUES (
'1309085328',
'Rosalia',
'rosali@gmail.com',
'rosalia+18'
);

INSERT INTO usuarios (cedula, nombre, correo, password) VALUES (
'22780712',
'Emily',
'jani@gmail.com',
'janine18+200'
);

/Esto mayormente se daria uso por algun error que se cometio mediante las numerosas prubeas que se realizaron
drop database sistema_php

Cabe destacar que para la Base de Datos se uso la aplicacion DBeaver la cual encarecidamente recomiendo usar por su interfaz simple e intuitiva.

Lo primero que se debe abrir es el archico portada.html para asi visualizar los nombres del estudiante (yo mismo) y el video que realizo, desde esa pagina podra redirigirse para iniciar sesion y verificar el funcionamiento de todo archivo y proceso, por ultimo quisiera recomendar la pagina CodePen de la tome insiparacion para la portada pues contiene muchos elementos similares a un framework