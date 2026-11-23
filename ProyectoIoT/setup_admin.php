<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

try {
    $conexion = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
        ]
    );

    $conexion->exec("
        CREATE TABLE IF NOT EXISTS usuarios (
            id INT AUTO_INCREMENT PRIMARY KEY,
            usuario VARCHAR(50) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4
    ");

    $total = (int) $conexion->query(
        "SELECT COUNT(*) FROM usuarios"
    )->fetchColumn();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        if ($total > 0) {
            exit('Ya existe un administrador.');
        }

        $usuario = trim($_POST['usuario'] ?? '');
        $password = $_POST['password'] ?? '';

        if ($usuario === '' || strlen($password) < 8) {
            exit('Ingresa un usuario y una contraseña de al menos 8 caracteres.');
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $consulta = $conexion->prepare(
            "INSERT INTO usuarios (usuario, password) VALUES (?, ?)"
        );

        $consulta->execute([$usuario, $hash]);

        echo "Administrador creado correctamente. Elimina setup_admin.php del servidor.";
        exit;
    }

} catch (PDOException $e) {
    exit('No se pudo completar la operación. Revisa la configuración de la base de datos.');
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Crear administrador</title>
</head>
<body>
    <h2>Crear administrador</h2>

    <?php if ($total > 0): ?>
        <p>Ya existe un administrador registrado.</p>
    <?php else: ?>
        <form method="POST">
            <label>Usuario:</label>
            <input type="text" name="usuario" required>

            <br><br>

            <label>Contraseña:</label>
            <input type="password" name="password" minlength="8" required>

            <br><br>

            <button type="submit">Crear administrador</button>
        </form>
    <?php endif; ?>
</body>
</html>
