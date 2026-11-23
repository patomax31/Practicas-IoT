<?php
declare(strict_types=1);

session_start();

if (!empty($_SESSION['admin_id'])) {
    header('Location: index.php');
    exit;
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión | Control NFC</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body class="auth-page">

    <div class="auth-card">
        <div class="auth-icon">🔐</div>

        <h1>Control NFC</h1>
        <p class="auth-description">
            Inicia sesión para administrar el acceso a las habitaciones.
        </p>

        <?php if (isset($_GET['error'])): ?>
            <div class="auth-error">
                Usuario o contraseña incorrectos.
            </div>
        <?php endif; ?>

        <form action="auth.php" method="POST">
            <label for="usuario">Usuario</label>
            <input
                type="text"
                id="usuario"
                name="usuario"
                placeholder="Ingresa tu usuario"
                autocomplete="username"
                required
            >

            <label for="password">Contraseña</label>
            <input
                type="password"
                id="password"
                name="password"
                placeholder="Ingresa tu contraseña"
                autocomplete="current-password"
                required
            >

            <button type="submit" class="auth-button">
                Iniciar sesión
            </button>
        </form>

        <div class="auth-footer">
            Sistema de control de acceso NFC
        </div>
    </div>

</body>
</html>
