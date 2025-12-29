<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}
require_once __DIR__ . '/../conexao.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$nome = $_POST['nome_crianca'] ?? '';
$email = $_POST['email'] ?? '';
$telefone = $_POST['telefone'] ?? '';
$cpf = $_POST['cpf'] ?? '';
$senha = $_POST['senha'] ?? '';

if ($id > 0) {
    if (!empty($senha)) {
        $hash = password_hash($senha, PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE usuario SET nome_crianca=?, email=?, telefone=?, cpf=?, senha=? WHERE id=?");
        $stmt->bind_param('sssssi', $nome, $email, $telefone, $cpf, $hash, $id);
    } else {
        $stmt = $conn->prepare("UPDATE usuario SET nome_crianca=?, email=?, telefone=?, cpf=? WHERE id=?");
        $stmt->bind_param('ssssi', $nome, $email, $telefone, $cpf, $id);
    }
    $stmt->execute();
} else {
    $hash = password_hash($senha ?: bin2hex(random_bytes(4)), PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO usuario (nome_crianca,email,telefone,cpf,senha) VALUES (?,?,?,?,?)");
    $stmt->bind_param('sssss', $nome, $email, $telefone, $cpf, $hash);
    $stmt->execute();
}

// If request is AJAX, return a short message instead of redirecting to avoid injecting full page into modal
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if ($isAjax) {
    echo 'Usuário salvo com sucesso.';
    exit;
}

header('Location: index.php');
exit;
