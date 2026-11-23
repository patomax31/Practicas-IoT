<?php
declare(strict_types=1);
// API para guardar personas, fotos y accesos en MySQL.
// Las tablas se crean solas la primera vez que se abre la página.
require __DIR__ . '/config.php';

function out(int $status, $data): void {
    http_response_code($status);
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store');
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

function conectar(): PDO {
    static $pdo = null;
    if ($pdo) return $pdo;
    try {
        $test = getenv('ACCESOS_TEST_SQLITE');   // solo para pruebas locales
        $pdo = $test
            ? new PDO('sqlite:' . $test)
            : new PDO('mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=utf8mb4', DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        crear_tablas($pdo);
    } catch (PDOException $e) {
        out(500, ['error' => 'Base de datos: ' . $e->getMessage()]);
    }
    return $pdo;
}

function crear_tablas(PDO $pdo): void {
    if ($pdo->getAttribute(PDO::ATTR_DRIVER_NAME) === 'sqlite') {
        $pdo->exec('CREATE TABLE IF NOT EXISTS personas (
            uid TEXT PRIMARY KEY, nombre TEXT NOT NULL, foto BLOB NULL,
            tiene_foto INTEGER NOT NULL DEFAULT 0, actualizado INTEGER NOT NULL DEFAULT 0)');
        $pdo->exec('CREATE TABLE IF NOT EXISTS accesos (
            id INTEGER PRIMARY KEY AUTOINCREMENT, uid TEXT NOT NULL, nombre TEXT NOT NULL DEFAULT \'\',
            ok INTEGER NOT NULL, ts INTEGER NOT NULL, UNIQUE (uid, ts))');
        return;
    }
    $pdo->exec('CREATE TABLE IF NOT EXISTS personas (
        uid VARCHAR(20) NOT NULL PRIMARY KEY,
        nombre VARCHAR(80) NOT NULL,
        foto MEDIUMBLOB NULL,
        tiene_foto TINYINT(1) NOT NULL DEFAULT 0,
        actualizado INT UNSIGNED NOT NULL DEFAULT 0
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
    $pdo->exec('CREATE TABLE IF NOT EXISTS accesos (
        id INT UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY,
        uid VARCHAR(20) NOT NULL,
        nombre VARCHAR(80) NOT NULL DEFAULT \'\',
        ok TINYINT(1) NOT NULL,
        ts INT UNSIGNED NOT NULL,
        UNIQUE KEY uid_ts (uid, ts),
        KEY idx_ts (ts)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4');
}

function leer_json(int $limite): array {
    $raw = file_get_contents('php://input', false, null, 0, $limite + 1);
    if ($raw === false || strlen($raw) > $limite) out(413, ['error' => 'Solicitud demasiado grande']);
    $j = json_decode($raw === '' ? '{}' : $raw, true);
    if (!is_array($j)) out(400, ['error' => 'JSON inválido']);
    return $j;
}

function limpiar_uid($s): string {
    $u = preg_replace('/[^0-9A-F]/', '', strtoupper((string)$s));
    return preg_match('/^([0-9A-F]{8}|[0-9A-F]{14}|[0-9A-F]{20})$/', $u) ? $u : '';
}

$a = $_GET['a'] ?? '';
$m = $_SERVER['REQUEST_METHOD'];
$pdo = conectar();

try {
    // Guarda en la base de datos los accesos que el Photon tiene en su variable "historial"
    if ($a === 'sync' && $m === 'POST') {
        $logs = leer_json(20 * 1024)['logs'] ?? [];
        if (!is_array($logs)) out(400, ['error' => 'Formato inválido']);
        $nuevos = 0;
        $buscaNombre = $pdo->prepare('SELECT nombre FROM personas WHERE uid = ?');
        $existe = $pdo->prepare('SELECT 1 FROM accesos WHERE uid = ? AND ts = ?');
        $inserta = $pdo->prepare('INSERT INTO accesos (uid, nombre, ok, ts) VALUES (?, ?, ?, ?)');
        foreach (array_slice($logs, 0, 20) as $l) {
            $uid = limpiar_uid($l['u'] ?? '');
            $ts = (int)($l['t'] ?? 0);
            if ($uid === '' || $ts <= 0) continue;
            $ok = !empty($l['ok']) ? 1 : 0;
            $existe->execute([$uid, $ts]);
            if ($existe->fetchColumn()) continue;
            $nombre = '';
            if ($ok) {
                $buscaNombre->execute([$uid]);
                $nombre = (string)($buscaNombre->fetchColumn() ?: mb_substr((string)($l['n'] ?? ''), 0, 80));
            }
            try {
                $inserta->execute([$uid, $nombre, $ok, $ts]);
                $nuevos++;
            } catch (PDOException $e) { /* ya estaba guardado */ }
        }
        out(200, ['nuevos' => $nuevos]);
    }

    // Historial (más reciente primero)
    if ($a === 'logs' && $m === 'GET') {
        $limite = max(1, min(1000, (int)($_GET['limit'] ?? 200)));
        $rows = $pdo->query(
            'SELECT a.id, a.uid, a.nombre, a.ok, a.ts, COALESCE(p.tiene_foto, 0) AS foto, COALESCE(p.actualizado, 0) AS fv
             FROM accesos a LEFT JOIN personas p ON p.uid = a.uid
             ORDER BY a.ts DESC, a.id DESC LIMIT ' . $limite
        )->fetchAll();
        foreach ($rows as &$r) {
            $r['ok'] = (int)$r['ok']; $r['ts'] = (int)$r['ts']; $r['foto'] = (int)$r['foto']; $r['fv'] = (int)$r['fv'];
        }
        out(200, $rows);
    }

    // Personas
    if ($a === 'people' && $m === 'GET') {
        $rows = $pdo->query('SELECT uid, nombre, tiene_foto AS foto, actualizado AS fv FROM personas ORDER BY nombre')->fetchAll();
        foreach ($rows as &$r) { $r['foto'] = (int)$r['foto']; $r['fv'] = (int)$r['fv']; }
        out(200, $rows);
    }

    if ($a === 'people' && $m === 'POST') {
        $b = leer_json(2 * 1024 * 1024);
        $uid = limpiar_uid($b['uid'] ?? '');
        $nombre = trim((string)($b['nombre'] ?? ''));
        if ($uid === '') out(400, ['error' => 'UID inválido (8, 14 o 20 caracteres hexadecimales)']);
        if ($nombre === '' || mb_strlen($nombre) > 80) out(400, ['error' => 'El nombre es obligatorio (máximo 80 caracteres)']);

        $foto = null;
        if (!empty($b['photo'])) {
            if (!preg_match('#^data:image/jpeg;base64,([A-Za-z0-9+/=]+)$#', (string)$b['photo'], $mm)) out(400, ['error' => 'La foto debe ser JPEG']);
            $foto = base64_decode($mm[1], true);
            if ($foto === false || strlen($foto) > 800 * 1024) out(400, ['error' => 'Foto inválida o demasiado grande']);
        }

        $ahora = time();
        $ex = $pdo->prepare('SELECT 1 FROM personas WHERE uid = ?');
        $ex->execute([$uid]);
        if ($ex->fetchColumn()) {
            if ($foto !== null) {
                $st = $pdo->prepare('UPDATE personas SET nombre = ?, foto = ?, tiene_foto = 1, actualizado = ? WHERE uid = ?');
                $st->bindValue(1, $nombre);
                $st->bindValue(2, $foto, PDO::PARAM_LOB);
                $st->bindValue(3, $ahora, PDO::PARAM_INT);
                $st->bindValue(4, $uid);
                $st->execute();
            } else {
                $pdo->prepare('UPDATE personas SET nombre = ?, actualizado = ? WHERE uid = ?')->execute([$nombre, $ahora, $uid]);
            }
        } else {
            $st = $pdo->prepare('INSERT INTO personas (uid, nombre, foto, tiene_foto, actualizado) VALUES (?, ?, ?, ?, ?)');
            $st->bindValue(1, $uid);
            $st->bindValue(2, $nombre);
            $st->bindValue(3, $foto, $foto === null ? PDO::PARAM_NULL : PDO::PARAM_LOB);
            $st->bindValue(4, $foto === null ? 0 : 1, PDO::PARAM_INT);
            $st->bindValue(5, $ahora, PDO::PARAM_INT);
            $st->execute();
        }
        out(200, ['ok' => true]);
    }

    if ($a === 'people' && $m === 'DELETE') {
        $uid = limpiar_uid($_GET['uid'] ?? '');
        if ($uid === '') out(400, ['error' => 'UID inválido']);
        $pdo->prepare('DELETE FROM personas WHERE uid = ?')->execute([$uid]);
        out(200, ['ok' => true]);
    }

    // Foto de una persona (imagen JPEG)
    if ($a === 'foto' && $m === 'GET') {
        $uid = limpiar_uid($_GET['uid'] ?? '');
        $st = $pdo->prepare('SELECT foto FROM personas WHERE uid = ? AND tiene_foto = 1');
        $st->execute([$uid]);
        $f = $st->fetchColumn();
        if (is_resource($f)) $f = stream_get_contents($f);
        if (!$f) out(404, ['error' => 'Sin foto']);
        header('Content-Type: image/jpeg');
        header('Cache-Control: public, max-age=31536000');   // la URL cambia (&v=) si se cambia la foto
        header('X-Content-Type-Options: nosniff');
        echo $f;
        exit;
    }

    out(404, ['error' => 'No encontrado']);
} catch (PDOException $e) {
    out(500, ['error' => 'Base de datos: ' . $e->getMessage()]);
}
