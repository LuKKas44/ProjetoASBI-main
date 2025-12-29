<?php
try {
    $pdo = new PDO('mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8','root','');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->query('SHOW COLUMNS FROM dentistas');
    foreach ($stmt as $c) {
        echo $c['Field'] . "\n";
    }
} catch (PDOException $e) {
    echo 'Error: ' . $e->getMessage();
}
