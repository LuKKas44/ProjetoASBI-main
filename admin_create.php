<?php
/**
 * Script de instalação rápida: cria um usuário administrador na tabela `dentistas`.
 * Use via navegador: http://localhost/projeto_ASBI-main/admin_create.php
 * REMOVA este arquivo depois de criar o admin por segurança.
 */
require_once __DIR__ . '/conexao.php';

// Dados do admin — altere se desejar
$adminEmail = 'admin@asbi.local';
$adminName = 'Administrador ASBI';
$passwordPlain = bin2hex(random_bytes(4)); // senha gerada

// Cria tabela admins se não existir
$createSql = "CREATE TABLE IF NOT EXISTS admins (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(255) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
$conn->query($createSql);

// Verifica se já existe
$stmt = $conn->prepare('SELECT id FROM admins WHERE email = ? LIMIT 1');
$stmt->bind_param('s', $adminEmail);
$stmt->execute();
$res = $stmt->get_result();
if ($res && $res->num_rows > 0) {
    $row = $res->fetch_assoc();
    echo "Admin já existe com ID: " . $row['id'] . "<br>Se quiser recriar, remova o registro via phpMyAdmin e execute este script de novo.";
    exit;
}

$hash = password_hash($passwordPlain, PASSWORD_DEFAULT);

$stmt = $conn->prepare('INSERT INTO admins (nome, email, senha) VALUES (?,?,?)');
$stmt->bind_param('sss', $adminName, $adminEmail, $hash);
if ($stmt->execute()) {
    echo "Admin criado com sucesso.<br>E-mail: <strong>{$adminEmail}</strong><br>Senha: <strong>{$passwordPlain}</strong><br>Faça login e então altere a senha imediatamente.<br>IMPORTANTE: Remova este arquivo `admin_create.php` depois de usar.";
} else {
    echo "Falha ao criar admin: " . $conn->error;
}

?>
