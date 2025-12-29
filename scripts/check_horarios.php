<?php
// Quick diagnostic: list some rows from horarios
$host = '127.0.0.1:3316';
$db = 'clinica_1';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("DB connect error: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT COUNT(*) as c FROM horarios");
$count = $stmt->fetch(PDO::FETCH_ASSOC)['c'];
echo "Total horarios: $count\n";

$stmt = $pdo->query("SELECT h.id,h.data,h.hora,h.status,h.dentista_id,h.usuario_id, d.nome_completo as dentista, u.nome_crianca as usuario FROM horarios h LEFT JOIN dentistas d ON d.id=h.dentista_id LEFT JOIN usuario u ON u.id=h.usuario_id ORDER BY h.data,h.hora LIMIT 20");
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
if (!$rows) {
    echo "No sample rows.\n";
} else {
    foreach ($rows as $r) {
        echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
    }
}
