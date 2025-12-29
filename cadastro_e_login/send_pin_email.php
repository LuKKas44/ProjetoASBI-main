<?php
// Endpoint: generate a 6-digit PIN, store its hash and expiry, and send via email.
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['pre_2fa'])) {
    echo json_encode(['success'=>false,'message'=>'Fluxo 2FA inválido.']);
    exit;
}

$pre = $_SESSION['pre_2fa'];
$id = (int)$pre['id'];
$tipo = $pre['tipo']; // 'medico' or 'cliente'
$table = ($tipo === 'medico') ? 'dentistas' : 'usuario';

try {
    $pdo = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4","root","",[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);

    // generate secure 6-digit PIN
    $pin = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    $hash = password_hash($pin, PASSWORD_DEFAULT);
    $expire = (new DateTime('+10 minutes'))->format('Y-m-d H:i:s');

    $stmt = $pdo->prepare("UPDATE {$table} SET twofa_pin = ?, twofa_pin_expire = ? WHERE id = ?");
    $stmt->execute([$hash, $expire, $id]);

    // fetch email
    $stmt2 = $pdo->prepare("SELECT email FROM {$table} WHERE id = ?");
    $stmt2->execute([$id]);
    $to = $stmt2->fetchColumn();

    if (!$to) {
        echo json_encode(['success'=>false,'message'=>'E-mail do usuário não encontrado.']);
        exit;
    }

    // send via PHPMailer (SMTP)
    try {
        require_once __DIR__ . '/../agendamento/vendor/autoload.php';
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
        echo json_encode(['success'=>true,'message'=>'PIN enviado por e-mail.']);
    } catch (Exception $e) {
        error_log('send_pin_email PHPMailer error: ' . $e->getMessage());
        echo json_encode(['success'=>false,'message'=>'PIN gerado e salvo, mas envio por e-mail falhou no servidor. Verifique configuração SMTP.']);
    }

} catch (Exception $e) {
    echo json_encode(['success'=>false,'message'=>'Erro: '.$e->getMessage()]);
}