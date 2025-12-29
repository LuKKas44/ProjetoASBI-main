<?php
// Test script to generate and send PIN via send_pin_email.php
// Usage: php test_send_pin_cli.php

session_start();
$email = 'csmreginaldo@gmail.com';

try {
    $pdo = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4","root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

    // look in dentistas
    $stmt = $pdo->prepare("SELECT id FROM dentistas WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $_SESSION['pre_2fa'] = ['id' => (int)$row['id'], 'tipo' => 'medico'];
        echo "Found in dentistas, id={$row['id']}\n";
    } else {
        // look in usuario
        $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($row) {
            $_SESSION['pre_2fa'] = ['id' => (int)$row['id'], 'tipo' => 'cliente'];
            echo "Found in usuario, id={$row['id']}\n";
        }
    }

    if (!isset($_SESSION['pre_2fa'])) {
        echo "User with email {$email} not found in dentistas or usuario.\n";
        exit(1);
    }

    // Include the send_pin_email endpoint logic
    // Capture output
    ob_start();
    include __DIR__ . '/send_pin_email.php';
    $out = ob_get_clean();

    echo "Response:\n" . $out . "\n";

} catch (Exception $e) {
    echo "Exception: " . $e->getMessage() . "\n";
    exit(1);
}
