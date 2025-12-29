<?php
// Usage: run with PHP CLI or include in browser for diagnostics
$email = 'michaelcosme2@gmail.com';

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

echo "Searching for email: $email\n\n";

$stmt = $pdo->prepare("SELECT id,email,nome_completo,cro,cpf,data_nascimento,nome_mae FROM dentistas WHERE email = ? OR cro = ? LIMIT 1");
$stmt->execute([$email,$email]);
$d = $stmt->fetch(PDO::FETCH_ASSOC);
if ($d) {
    echo "Found in dentistas:\n" . json_encode($d, JSON_UNESCAPED_UNICODE) . "\n\n";
} else {
    echo "Not found in dentistas.\n\n";
}

$stmt2 = $pdo->prepare("SELECT id,email,nome_responsavel,nome_crianca,cpf,data_nascimento,nome_mae FROM usuario WHERE email = ? OR cpf = ? LIMIT 1");
$stmt2->execute([$email,$email]);
$u = $stmt2->fetch(PDO::FETCH_ASSOC);
if ($u) {
    echo "Found in usuario:\n" . json_encode($u, JSON_UNESCAPED_UNICODE) . "\n\n";
} else {
    echo "Not found in usuario.\n\n";
}

// Also show session cookie params (if run in browser)
if (php_sapi_name() !== 'cli') {
    echo "\nSession cookie params:\n";
    print_r(session_get_cookie_params());
}
