<?php
declare(strict_types=1);

// Configuración de la base de datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'hotel_nfc');
define('DB_USER', 'root');
define('DB_PASS', '');

// Conexión PDO
function conectar(): PDO
{
    static $conexion = null;

    if ($conexion === null) {
        $conexion = new PDO(
            "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
            DB_USER,
            DB_PASS,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false
            ]
        );
    }

    return $conexion;
}
