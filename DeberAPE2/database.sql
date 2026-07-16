-- ═══════════════════════════════════════════════════════════════
-- database.sql
-- Ejecutar en phpMyAdmin → pestaña SQL → Continuar
-- ═══════════════════════════════════════════════════════════════

-- 1. Crear la base de datos
CREATE DATABASE IF NOT EXISTS gestor_archivos
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE gestor_archivos;

-- 2. Tabla de usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    rol           ENUM('admin', 'usuario') NOT NULL DEFAULT 'usuario',
    creado_en     DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3. Tabla de archivos
CREATE TABLE IF NOT EXISTS archivos (
    id               INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre_disco     VARCHAR(100) NOT NULL UNIQUE,
    nombre_original  VARCHAR(255) NOT NULL,
    tamano_bytes     INT UNSIGNED NOT NULL,
    tipo_mime        VARCHAR(50)  NOT NULL,
    extension        VARCHAR(10)  NOT NULL,
    subido_por       INT UNSIGNED NOT NULL,
    subido_en        DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (subido_por) REFERENCES usuarios(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4. Insertar usuarios
--    admin    → Admin2024!
--    usuario1 → User2024!
INSERT INTO usuarios (username, password_hash, rol) VALUES
(
    'admin',
    '$2y$12$j8cV9kGF6x5X28lWaGNVju2bdydRr9INXR5Y/gKBTnw.JHZuUyznm',
    'admin'
),
(
    'usuario1',
    '$2y$12$RN49bhC7B.Dr6wO/ms3rleHfXL2Wet3UasUfliNpIgKg8sSTGUQxK',
    'usuario'
);
