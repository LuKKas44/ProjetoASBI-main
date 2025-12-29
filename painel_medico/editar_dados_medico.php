<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'medico') {
    header("Location: ../cadastro_e_login/login.php");
    exit;
}

try {
    $pdo = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM dentistas WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $dentista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dentista) {
        die("Dados não encontrados.");
    }
} catch (PDOException $e) {
    die("Erro: " . $e->getMessage());
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
    <meta charset="utf-8">
    <title>Editar Meus Dados - Médico</title>
    <link rel="stylesheet" href="editar_dados_medico.css">
</head>

<body>
    <div class="container">
        <h2><span>✏️</span> Editar Meus Dados Profissionais</h2>
        <?= $msg ?>

        <form action="update_medico.php" method="post">
            <label>Nome completo<br>
                <input type="text" name="nome_completo" value="<?= htmlspecialchars($dentista['nome_completo']) ?>"
                    required>
            </label>

            <label>CRO<br>
                <input type="text" name="cro" value="<?= htmlspecialchars($dentista['cro']) ?>">
            </label>

            <label>CPF<br>
                <input type="text" name="cpf" value="<?= htmlspecialchars($dentista['cpf']) ?>">
            </label>

            <label>Email<br>
                <input type="email" name="email" value="<?= htmlspecialchars($dentista['email']) ?>" required>
            </label>

            <label>Telefone<br>
                <input type="text" name="telefone" value="<?= htmlspecialchars($dentista['telefone']) ?>">
            </label>

            <label>Especialidade<br>
                <input type="text" name="especialidade" value="<?= htmlspecialchars($dentista['especialidade']) ?>">
            </label>

            <label>Nome da clínica<br>
                <input type="text" name="nome_clinica" value="<?= htmlspecialchars($dentista['nome_clinica']) ?>">
            </label>

            <label>Rua da clínica<br>
                <input type="text" name="rua_clinica" value="<?= htmlspecialchars($dentista['rua_clinica']) ?>">
            </label>

            <label>Número da clínica<br>
                <input type="text" name="numero_clinica" value="<?= htmlspecialchars($dentista['numero_clinica']) ?>">
            </label>

            <label>Bairro<br>
                <input type="text" name="bairro_clinica" value="<?= htmlspecialchars($dentista['bairro_clinica']) ?>">
            </label>

            <label>Cidade<br>
                <input type="text" name="cidade" value="<?= htmlspecialchars($dentista['cidade']) ?>">
            </label>

            <label>Estado<br>
                <input type="text" name="estado" value="<?= htmlspecialchars($dentista['estado']) ?>">
            </label>

            <label>CEP<br>
                <input type="text" name="cep_clinica" value="<?= htmlspecialchars($dentista['cep_clinica']) ?>">
            </label>

            <div style="margin-top:12px; display:flex; gap:8px;">
                <button type="submit"
                    style="background:#6b46c1; color:#fff; padding:8px 14px; border-radius:8px; border:none;">Salvar
                    alterações</button>
                <a href="meus_dados_medico.php"
                    style="display:inline-block; padding:8px 14px; border-radius:8px; background:#f3f3f3; text-decoration:none; color:#333;">Cancelar</a>
            </div>
        </form>

        <p style="margin-top:18px; font-size:14px; color:#666;">Para alterar a foto de perfil, use o campo de Upload na
            página de <a href="meus_dados_medico.php">Meus Dados</a>.</p>

    </div>
</body>

</html>