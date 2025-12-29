<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}
require_once __DIR__ . '/../conexao.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nome = $_POST['nome_completo'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$cro = $_POST['cro'] ?? '';
$especialidade = $_POST['especialidade'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($id > 0) {
    if (!empty($senha)) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE dentistas SET nome_completo=?, email=?, telefone=?, cro=?, especialidade=?, cpf=?, senha=? WHERE id=?");
        $stmt->bind_param('sssssssi', $nome, $email, $telefone, $cro, $especialidade, $cpf, $hash, $id);
    } else {
        $stmt = $conn->prepare("UPDATE dentistas SET nome_completo=?, email=?, telefone=?, cro=?, especialidade=?, cpf=? WHERE id=?");
        $stmt->bind_param('ssssssi', $nome, $email, $telefone, $cro, $especialidade, $cpf, $id);
    }
    $stmt->execute();
} else {
    $hash = password_hash($senha ?: bin2hex(random_bytes(4)), PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO dentistas (nome_completo,email,telefone,cro,especialidade,cpf,senha) VALUES (?,?,?,?,?,?,?)");
    $stmt->bind_param('sssssss', $nome, $email, $telefone, $cro, $especialidade, $cpf, $hash);
    $stmt->execute();
}

// If request is AJAX, return a short message instead of redirecting
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($isAjax) {
    echo 'Dentista salvo com sucesso.';
    exit;
}

header('Location: index.php');
exit;
