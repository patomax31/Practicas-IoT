<?php
declare(strict_types=1);

session_start();
require_once __DIR__ . '/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: login.php');
    exit;
}

$usuario = trim($_POST['usuario'] ?? '');
$password = $_POST['password'] ?? '';

try {
    $conexion = new PDO(
        "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=utf8mb4",
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $consulta = $conexion->prepare(
        "SELECT id, usuario, password FROM usuarios WHERE usuario = ? LIMIT 1"
    );

    $consulta->execute([$usuario]);

    $admin = $consulta->fetch(PDO::FETCH_ASSOC);

    if ($admin && password_verify($password, $admin['password'])) {
        session_regenerate_id(true);

        $_SESSION['admin_id'] = $admin['id'];
        $_SESSION['admin_usuario'] = $admin['usuario'];

        header('Location: index.php');
        exit;
    }

    header('Location: login.php?error=1');
    exit;

} catch (PDOException $e) {
    http_response_code(500);
    exit('No se pudo iniciar sesión. Intenta nuevamente.');
}
