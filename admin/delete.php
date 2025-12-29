<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}
require_once __DIR__ . '/../conexao.php';

$type = $_GET['type'] ?? '';
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

if ($id <= 0) {
    header('Location: index.php'); exit;
}

if ($type === 'user') {
    $stmt = $conn->prepare('DELETE FROM usuario WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
} elseif ($type === 'medico') {
    $stmt = $conn->prepare('DELETE FROM dentistas WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
}

header('Location: index.php');
exit;
