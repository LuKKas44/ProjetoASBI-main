<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('HTTP/1.1 403 Forbidden');
    echo 'Acesso negado.';
    exit;
}
require_once __DIR__ . '/../conexao.php';

$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
$delete = isset($_POST['delete']) ? 1 : 0;

if ($delete && $id>0) {
    $stmt = $conn->prepare('DELETE FROM horarios WHERE id = ?');
    $stmt->bind_param('i',$id);
    $stmt->execute();
    echo 'Horário excluído com sucesso.';
    exit;
}

$data = $_POST['data'] ?? null;
$hora = $_POST['hora'] ?? null;
$status = $_POST['status'] ?? 'disponivel';
$dentista_id = isset($_POST['dentista_id']) && $_POST['dentista_id']!=='' ? intval($_POST['dentista_id']) : null;
$usuario_id = isset($_POST['usuario_id']) && $_POST['usuario_id']!=='' ? intval($_POST['usuario_id']) : null;

if (!$data || !$hora) {
    echo 'Data e hora são obrigatórios.';
    exit;
}

try {
    if ($id>0) {
        $stmt = $conn->prepare('UPDATE horarios SET data = ?, hora = ?, status = ?, dentista_id = ?, usuario_id = ? WHERE id = ?');
        $stmt->bind_param('ssssii', $data, $hora, $status, $dentista_id, $usuario_id, $id);
        $stmt->execute();
        echo 'Horário salvo com sucesso.';
        exit;
    } else {
        $stmt = $conn->prepare('INSERT INTO horarios (data, hora, status, dentista_id, usuario_id) VALUES (?, ?, ?, ?, ?)');
        $stmt->bind_param('sssss', $data, $hora, $status, $dentista_id, $usuario_id);
        $stmt->execute();
        echo 'Horário criado com sucesso.';
        exit;
    }
} catch (Exception $e) {
    echo 'Erro: ' . $e->getMessage();
}
