<?php
// Fix horarios.status where empty: if usuario_id IS NOT NULL -> 'ocupado', else 'disponivel'
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

$updated = 0;

$stmt = $pdo->prepare("SELECT id, usuario_id FROM horarios WHERE status IS NULL OR status = ''");
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($rows as $r) {
    $newStatus = ($r['usuario_id']) ? 'ocupado' : 'disponivel';
    $u = $pdo->prepare("UPDATE horarios SET status = ? WHERE id = ?");
    $u->execute([$newStatus, $r['id']]);
    $updated++;
}

echo "Updated $updated rows.\n";
