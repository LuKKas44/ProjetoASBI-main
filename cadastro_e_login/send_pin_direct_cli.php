<?php
// CLI helper to generate and send a PIN to an email for testing
// Usage: php send_pin_direct_cli.php

require __DIR__ . '/../agendamento/vendor/autoload.php';
date_default_timezone_set('America/Sao_Paulo');

if (PHP_SAPI !== 'cli') {
    echo "Run this script from CLI only.\n";
    exit(1);
}

$email = 'csmreginaldo@gmail.com';
echo "Testing PIN send to: $email\n";

try {
    $pdo = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4","root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

    // find user
    $stmt = $pdo->prepare("SELECT id FROM dentistas WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    $table = 'dentistas';
    if (!$row) {
        $stmt = $pdo->prepare("SELECT id FROM usuario WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $table = 'usuario';
    }

    if (!$row) {
        echo "User not found: $email\n";
        exit(1);
    }

    $id = (int)$row['id'];
    // generate PIN
    $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $hash = password_hash($pin, PASSWORD_DEFAULT);
    $expire = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("UPDATE {$table} SET twofa_pin = ?, twofa_pin_expire = ? WHERE id = ?");
    $stmt->execute([$hash, $expire, $id]);

    // fetch email (again)
    $stmt2 = $pdo->prepare("SELECT email FROM {$table} WHERE id = ?");
    $stmt2->execute([$id]);
    $to = $stmt2->fetchColumn();

    // send via PHPMailer
    $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'associacaoasbi@gmail.com';
    $mail->Password = 'vrms jdfa ksos pnap';
    $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->setFrom('associacaoasbi@gmail.com', 'Associação ASBI');
    $mail->addAddress($to);

    $mail->isHTML(false);
    $mail->CharSet = 'UTF-8';
    $mail->Subject = 'Seu PIN de verificação ASBI (válido 10 minutos)';
    $mail->Body = "Olá,\n\nSeu PIN de verificação é: {$pin}\n\nEle expira em 10 minutos. Não compartilhe com ninguém.\n\nAtenciosamente,\nEquipe ASBI";

    $mail->send();
    echo "PIN sent by SMTP. PIN (for testing only): $pin\n";
    echo "Expires at: $expire\n";
    exit(0);

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}