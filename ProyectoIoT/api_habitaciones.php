<?php
// ============================================================
// 1) SQL: ejecútalo una vez en la base hotel_nfc
// ============================================================
/*
CREATE TABLE IF NOT EXISTS habitaciones (
  numero INT PRIMARY KEY,
  estado ENUM('ocupado','desocupado','no_molestar') NOT NULL DEFAULT 'desocupado'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

INSERT IGNORE INTO habitaciones (numero) VALUES (101),(102),(103),(104),(105),(106),(107),(108);

CREATE TABLE IF NOT EXISTS habitacion_historial (
  id INT AUTO_INCREMENT PRIMARY KEY,
  numero INT NOT NULL,
  estado ENUM('ocupado','desocupado','no_molestar') NOT NULL,
  nombre VARCHAR(80) NULL,
  uid VARCHAR(20) NULL,
  creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  INDEX (numero, creado_en)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
*/

// ============================================================
// 2) Pega estos casos dentro del switch/if que ya procesa
//    $_GET['a'] en tu api.php (usa el mismo $pdo / conectar()).
//    Si tu api.php ya hace header('Content-Type: application/json'),
//    no lo repitas.
// ============================================================

$pdo = conectar();
$a = $_GET['a'] ?? '';

if ($a === 'rooms' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode(
        $pdo->query("SELECT numero, estado FROM habitaciones ORDER BY numero")->fetchAll()
    );
    exit;
}

if ($a === 'room_history' && $_SERVER['REQUEST_METHOD'] === 'GET') {
    $q = $pdo->prepare(
        "SELECT estado, nombre, uid, UNIX_TIMESTAMP(creado_en) AS ts
         FROM habitacion_historial
         WHERE numero = ?
         ORDER BY creado_en DESC, id DESC
         LIMIT 100"
    );
    $q->execute([(int) ($_GET['room'] ?? 0)]);
    echo json_encode($q->fetchAll());
    exit;
}

if ($a === 'room_state' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $in = json_decode(file_get_contents('php://input'), true) ?: [];
    $room = (int) ($in['room'] ?? 0);
    $estado = (string) ($in['estado'] ?? '');

    if (!in_array($estado, ['ocupado', 'desocupado', 'no_molestar'], true) || $room <= 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Datos inválidos']);
        exit;
    }

    $pdo->prepare("UPDATE habitaciones SET estado = ? WHERE numero = ?")
        ->execute([$estado, $room]);

    $pdo->prepare("INSERT INTO habitacion_historial (numero, estado) VALUES (?, ?)")
        ->execute([$room, $estado]);

    echo json_encode(['ok' => true]);
    exit;
}