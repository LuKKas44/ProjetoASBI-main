<?php
// diagnostic helper: show twofa_pin presence and expiry for a given email (edit $email)
$email = $argv[1] ?? 'michaelcosme2@gmail.com';
try {
    $pdo = new PDO('mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8','root','');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $stmt = $pdo->prepare('SELECT id,email,CASE WHEN twofa_pin IS NULL THEN 0 ELSE 1 END as has_pin,twofa_pin_expire FROM dentistas WHERE email = ?');
    $stmt->execute([$email]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$r) {
        echo "No dentist found with email $email\n";
    } else {
        echo json_encode($r, JSON_UNESCAPED_UNICODE) . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
