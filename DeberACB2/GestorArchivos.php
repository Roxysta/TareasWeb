<?php

require_once 'Conexion.php';

class GestorArchivos {
    private string $directorio;
    private array $tiposPermitidos = ['image/jpeg', 'image/png', 'application/pdf'];
    private array $extensionesPermitidas = ['jpg', 'jpeg', 'png', 'pdf'];
    private int $tamanoMaximo = 5 * 1024 * 1024; // 5 MB
    private PDO $pdo;

    public function __construct(string $directorio) {
        $this->directorio = rtrim($directorio, '/') . '/';
        $this->pdo        = Conexion::obtener();

        if (!is_dir($this->directorio)) {
            mkdir($this->directorio, 0755, true);
        }
    }

    public function subir(array $archivo, int $usuarioId): array {
        // 1. Verificar errores de subida
        if ($archivo['error'] !== UPLOAD_ERR_OK) {
            return $this->respuesta(false, $this->mensajeError($archivo['error']));
        }

        // 2. Valida tamaño
        if ($archivo['size'] > $this->tamanoMaximo) {
            return $this->respuesta(false, 'El archivo supera el límite de 5 MB.');
        }

        // 3. Validar tipo MIME real con finfo (no confiar en el del cliente)
        $finfo    = new finfo(FILEINFO_MIME_TYPE);
        $tipoMime = $finfo->file($archivo['tmp_name']);
        if (!in_array($tipoMime, $this->tiposPermitidos, true)) {
            return $this->respuesta(false, "Tipo de archivo no permitido: {$tipoMime}");
        }

        // 4. Validar extensión del nombre original
        $nombreOriginal = basename($archivo['name']);
        $extension      = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));
        if (!in_array($extension, $this->extensionesPermitidas, true)) {
            return $this->respuesta(false, "Extensión no permitida: .{$extension}");
        }

        // 5. Verificar coherencia MIME
        $mapaCoherencia = [
            'image/jpeg'      => ['jpg', 'jpeg'],
            'image/png'       => ['png'],
            'application/pdf' => ['pdf'],
        ];
        if (!in_array($extension, $mapaCoherencia[$tipoMime], true)) {
            return $this->respuesta(false, 'El tipo de archivo no coincide con la extensión.');
        }

        // 6. Renombrar con hash aleatorio
        $nombreSeguro = bin2hex(random_bytes(16)) . '.' . $extension;
        $rutaDestino  = $this->directorio . $nombreSeguro;

        if (!move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
            return $this->respuesta(false, 'Error al guardar el archivo en el servidor.');
        }

        $stmt = $this->pdo->prepare(
            'INSERT INTO archivos (nombre_disco, nombre_original, tamano_bytes, tipo_mime, extension, subido_por)
             VALUES (?, ?, ?, ?, ?, ?)'
        );
        $stmt->execute([
            $nombreSeguro,
            htmlspecialchars($nombreOriginal, ENT_QUOTES, 'UTF-8'),
            $archivo['size'],
            $tipoMime,
            $extension,
            $usuarioId,
        ]);

        return $this->respuesta(true, 'Archivo subido correctamente.', $nombreSeguro);
    }

    public function listar(): array {
        $stmt = $this->pdo->query(
            'SELECT a.nombre_disco, a.nombre_original, a.tamano_bytes,
                    a.extension, a.subido_en, u.username AS subido_por
             FROM archivos a
             JOIN usuarios u ON u.id = a.subido_por
             ORDER BY a.subido_en DESC'
        );

        $archivos = [];
        foreach ($stmt->fetchAll() as $fila) {
            $archivos[] = [
                'nombre'          => $fila['nombre_disco'],
                'nombre_original' => $fila['nombre_original'],
                'tamano'          => $this->formatearTamano((int) $fila['tamano_bytes']),
                'fecha'           => date('d/m/Y H:i', strtotime($fila['subido_en'])),
                'extension'       => $fila['extension'],
                'subido_por'      => $fila['subido_por'],
            ];
        }

        return $archivos;
    }

    public function eliminar(string $nombre): array {
        // Sanitizar: solo basename
        $nombre = basename($nombre);

        // Validar formato: 32 hex chars + extensión válida
        if (!preg_match('/^[a-f0-9]{32}\.(jpg|jpeg|png|pdf)$/', $nombre)) {
            return $this->respuesta(false, 'Nombre de archivo inválido.');
        }

        $ruta = $this->directorio . $nombre;

        // Verificar que la ruta real esté dentro del directorio (anti path traversal)
        $rutaReal       = realpath($ruta);
        $directorioReal = realpath($this->directorio);

        if ($rutaReal === false || strpos($rutaReal, $directorioReal) !== 0) {
            return $this->respuesta(false, 'Acceso denegado: ruta fuera del directorio permitido.');
        }

        if (!file_exists($rutaReal)) {
            return $this->respuesta(false, 'El archivo no existe.');
        }

        // Eliminar del disco
        if (!unlink($rutaReal)) {
            return $this->respuesta(false, 'No se pudo eliminar el archivo.');
        }

        // Eliminar registro de la BD
        $stmt = $this->pdo->prepare('DELETE FROM archivos WHERE nombre_disco = ?');
        $stmt->execute([$nombre]);

        return $this->respuesta(true, 'Archivo eliminado correctamente.');
    }

    // ─── Métodos privados ───────────────────────────────────────────────────

    private function respuesta(bool $ok, string $mensaje, string $dato = ''): array {
        return ['ok' => $ok, 'mensaje' => $mensaje, 'dato' => $dato];
    }

    private function mensajeError(int $codigo): string {
        return match ($codigo) {
            UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El archivo supera el tamaño máximo permitido.',
            UPLOAD_ERR_PARTIAL  => 'El archivo se subió de forma incompleta.',
            UPLOAD_ERR_NO_FILE  => 'No se seleccionó ningún archivo.',
            default             => "Error de subida (código {$codigo}).",
        };
    }

    private function formatearTamano(int $bytes): string {
        if ($bytes >= 1048576) return round($bytes / 1048576, 2) . ' MB';
        if ($bytes >= 1024)    return round($bytes / 1024, 1)    . ' KB';
        return $bytes . ' B';
    }
}
