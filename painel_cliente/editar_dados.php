<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4",
        "root", "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $usuario_id = $_SESSION['id'];

    $stmt = $pdo->prepare("SELECT * FROM usuario WHERE id = :id");
    $stmt->execute(['id' => $usuario_id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$usuario) {
        die("Usuário não encontrado.");
    }

} catch (PDOException $e) {
    die("Erro ao buscar dados: " . $e->getMessage());
}

// Flash messages
$msg = '';
if (!empty($_SESSION['update_success'])) {
    $msg = '<div style="color:#16a34a; margin-bottom:12px;">' . $_SESSION['update_success'] . '</div>';
    unset($_SESSION['update_success']);
}
if (!empty($_SESSION['update_error'])) {
    $msg = '<div style="color:#b91c1c; margin-bottom:12px;">' . $_SESSION['update_error'] . '</div>';
    unset($_SESSION['update_error']);
}

?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Editar Meus Dados</title>
    <link rel="stylesheet" href="editar_dados.css">
</head>

<body>
    <div class="container">
        <h2><span>✏️</span> Editar Meus Dados</h2>
        <?= $msg ?>

        <form action="update_user.php" method="post">
            <label>Nome da Criança<br>
                <input type="text" name="nome_crianca" value="<?= htmlspecialchars($usuario['nome_crianca']) ?>"
                    required>
            </label>

            <label>Data de Nascimento<br>
                <input type="date" name="data_nascimento" value="<?= htmlspecialchars($usuario['data_nascimento']) ?>">
            </label>

            <label>Sexo<br>
                <select name="sexo">
                    <option value="" <?= $usuario['sexo'] == '' ? 'selected' : '' ?>>Não informado</option>
                    <option value="M" <?= $usuario['sexo'] == 'M' ? 'selected' : '' ?>>Masculino</option>
                    <option value="F" <?= $usuario['sexo'] == 'F' ? 'selected' : '' ?>>Feminino</option>
                </select>
            </label>

            <label>Nome do Responsável<br>
                <input type="text" name="nome_responsavel"
                    value="<?= htmlspecialchars($usuario['nome_responsavel']) ?>">
            </label>

            <label>CPF<br>
                <input type="text" name="cpf" value="<?= htmlspecialchars($usuario['cpf']) ?>">
            </label>

            <label>Telefone<br>
                <input type="text" name="telefone" value="<?= htmlspecialchars($usuario['telefone']) ?>">
            </label>

            <label>E-mail<br>
                <input type="email" name="email" value="<?= htmlspecialchars($usuario['email']) ?>">
            </label>

            <label>Rua<br>
                <input type="text" name="rua" value="<?= htmlspecialchars($usuario['rua']) ?>">
            </label>

            <label>Bairro<br>
                <input type="text" name="bairro" value="<?= htmlspecialchars($usuario['bairro']) ?>">
            </label>

            <label>Cidade<br>
                <input type="text" name="cidade" value="<?= htmlspecialchars($usuario['cidade']) ?>">
            </label>

            <label>Estado<br>
                <input type="text" name="estado" value="<?= htmlspecialchars($usuario['estado']) ?>">
            </label>

            <label>CEP<br>
                <input type="text" name="cep" value="<?= htmlspecialchars($usuario['cep']) ?>">
            </label>

            <div style="margin-top:12px; display:flex; gap:8px;">
                <button type="submit"
                    style="background:#6b46c1; color:#fff; padding:8px 14px; border-radius:8px; border:none;">Salvar
                    alterações</button>
                <a href="meus_dados.php"
                    style="display:inline-block; padding:8px 14px; border-radius:8px; background:#f3f3f3; text-decoration:none; color:#333;">Cancelar</a>
            </div>
        </form>

        <p style="margin-top:18px; font-size:14px; color:#666;">Para alterar a foto de perfil, use o campo de Upload na
            página de <a href="meus_dados.php">Meus Dados</a>.</p>

    </div>
</body>

</html>