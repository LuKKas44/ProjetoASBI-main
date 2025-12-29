<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: login.php');
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
?>

<?php
// Avatar URL for cliente
$avatarUrl = null;
$avatarFiles = glob(__DIR__ . '/../img/avatars/' . ($_SESSION['id'] ?? '') . '.*');
if ($avatarFiles && count($avatarFiles) > 0) {
    $file = basename($avatarFiles[0]);
    $avatarUrl = '/projeto_ASBI-main/img/avatars/' . $file;
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Meus Dados</title>
    <link rel="stylesheet" href="meus_dados1.css">
</head>

<body>

    <div class="container">
        <h2><span>👤</span> Meus Dados</h2>

        <!-- Upload de Foto de Perfil -->
        <div style="margin-bottom:18px; display:flex; gap:16px; align-items:center;">
            <div
                style="width:72px; height:72px; border-radius:50%; overflow:hidden; background:#f1f1f1; display:flex; align-items:center; justify-content:center;">
                <?php if ($avatarUrl): ?>
                <img src="<?= $avatarUrl ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;" />
                <?php else: ?>
                <span
                    style="font-weight:700; color:#6b46c1;"><?php echo strtoupper(substr($usuario['nome_crianca'],0,2)); ?></span>
                <?php endif; ?>
            </div>
            <form action="/projeto_ASBI-main/upload_avatar.php" method="post" enctype="multipart/form-data">
                <input type="file" name="avatar" accept="image/*" required id="escolherImagem" />
                <label for="escolherImagem">Escolha uma imagem:</label>
                <button type="submit"
                    style="margin-left:30px; padding:8px 12px; border-radius:5px; background:#6b46c1; color:white; border:none;">Definir
                    Foto</button>
                <?php if (!empty($_SESSION['avatar_error'])): ?>
                <div style="color:#b91c1c; margin-top:6px;">
                    <?= $_SESSION['avatar_error']; unset($_SESSION['avatar_error']); ?></div>
                <?php endif; ?>
                <?php if (!empty($_SESSION['avatar_success'])): ?>
                <div style="color:#16a34a; margin-top:6px;">
                    <?= $_SESSION['avatar_success']; unset($_SESSION['avatar_success']); ?></div>
                <?php endif; ?>
            </form>
        </div>

        <div class="info-item">
            <span class="info-label">Nome da Criança</span>
            <span class="info-value"><?= htmlspecialchars($usuario['nome_crianca']); ?></span>
        </div>


        <div class="info-item">
            <span class="info-label">Data de Nascimento</span>
            <span class="info-value"><?= date('d/m/Y', strtotime($usuario['data_nascimento'])); ?></span>
        </div>


        <div class="info-item">
            <span class="info-label">Sexo</span>
            <span class="info-value">
                <?= htmlspecialchars($usuario['sexo'] ?? 'Não informado'); ?>
            </span>
        </div>

        <div class="info-item">
            <span class="info-label">Nome do Responsável</span>
            <span class="info-value"><?= htmlspecialchars($usuario['nome_responsavel']); ?></span>
        </div>


        <div class="info-item">
            <span class="info-label">CPF</span>
            <span class="info-value"><?= htmlspecialchars($usuario['cpf']); ?></span>
        </div>



        <div class="info-item">
            <span class="info-label">Telefone</span>
            <span class="info-value"><?= htmlspecialchars($usuario['telefone']); ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">E-mail</span>
            <span class="info-value"><?= htmlspecialchars($usuario['email']); ?></span>
        </div>

        <div class="info-item">
            <span class="info-label">Endereço</span>
            <span class="info-value">
                <?= htmlspecialchars($usuario['rua']); ?>, <?= htmlspecialchars($usuario['bairro']); ?><br>
                <?= htmlspecialchars($usuario['cidade']); ?> - <?= htmlspecialchars($usuario['estado']); ?>,
                <?= htmlspecialchars($usuario['cep']); ?>
            </span>
        </div>



        <div class="info-item">
            <div class="info-label">Cadastrado em</div>
            <div class="info-value"><?= date('d/m/Y H:i', strtotime($usuario['criado_em'])) ?></div>
        </div>

        <a href="painel_cliente.php" class="back-btn">⬅ Voltar à Carteirinha</a>
        <a href="editar_dados.php" class="back-btn" style="margin-left:12px; background:#6b46c1; color:#fff;">✏️ Editar
            meus dados</a>
    </div>

</body>

</html>