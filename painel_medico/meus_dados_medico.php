<?php
session_start();
if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'medico') {
    header("Location: ../cadastro_e_login/login.php");
    exit;
}

include("../conexao.php");


date_default_timezone_set('America/Sao_Paulo');

try {
    $pdo = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $pdo->prepare("SELECT * FROM dentistas WHERE id = ?");
    $stmt->execute([$_SESSION['id']]);
    $dentista = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$dentista) {
        die("<p>❌ Dados não encontrados.</p>");
    }
} catch (PDOException $e) {
    die("<p>Erro: " . $e->getMessage() . "</p>");
}
?>

<?php
// Avatar URL
$avatarUrl = null;
$avatarFiles = glob(__DIR__ . '/../img/avatars/' . ($_SESSION['id'] ?? '') . '.*');
if ($avatarFiles && count($avatarFiles) > 0) {
    $file = basename($avatarFiles[0]);
    $avatarUrl = '/projeto_ASBI-main/img/avatars/' . $file;
}
?>

<body>
    <div class="container">
        <h2><span>🪥</span> Meus Dados Profissionais</h2>

        <!-- Upload de Foto de Perfil -->
        <div style="margin-bottom:18px; display:flex; gap:16px; align-items:center;">
            <div
                style="width:72px; height:72px; border-radius:50%; overflow:hidden; background:#f1f1f1; display:flex; align-items:center; justify-content:center;">
                <?php if ($avatarUrl): ?>
                <img src="<?= $avatarUrl ?>" alt="Avatar" style="width:100%; height:100%; object-fit:cover;" />
                <?php else: ?>
                <span
                    style="font-weight:700; color:#6b46c1;"><?php echo strtoupper(substr($dentista['nome_completo'],0,2)); ?></span>
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
            <div class="info-label">Nome completo</div>
            <div class="info-value"><?= htmlspecialchars($dentista['nome_completo']) ?></div>
        </div>

        <div class="info-item">
            <div class="info-label">CRO</div>
            <div class="info-value"><?= htmlspecialchars($dentista['cro']) ?></div>
        </div>

        <div class="info-item">
            <div class="info-label">CPF</div>
            <div class="info-value"><?= htmlspecialchars($dentista['cpf']) ?></div>
        </div>

        <div class="info-item">
            <div class="info-label">Email</div>
            <div class="info-value"><?= htmlspecialchars($dentista['email']) ?></div>
        </div>

        <div class="info-item">
            <div class="info-label">Telefone</div>
            <div class="info-value"><?= htmlspecialchars($dentista['telefone']) ?></div>
        </div>

        <div class="info-item">
            <div class="info-label">Especialidade</div>
            <div class="info-value"><?= htmlspecialchars($dentista['especialidade'] ?? '—') ?></div>
        </div>

        <div class="info-item">
            <div class="info-label">Nome da clínica</div>
            <div class="info-value"><?= htmlspecialchars($dentista['nome_clinica'] ?? '—') ?></div>
        </div>

        <div class="info-item">
            <div class="info-label">Endereço da clínica</div>
            <div class="info-value">
                <?= htmlspecialchars($dentista['rua_clinica'] . ', ' . $dentista['numero_clinica'] . ' - ' . $dentista['bairro_clinica'] . ', ' . $dentista['cidade'] . ' / ' . $dentista['estado']) ?>
            </div>
        </div>

        <div class="info-item">
            <div class="info-label">CEP da clínica</div>
            <div class="info-value"><?= htmlspecialchars($dentista['cep_clinica'] ?? '—') ?></div>
        </div>


        <div class="info-item">
            <div class="info-label">Cadastrado em</div>
            <div class="info-value"><?= date('d/m/Y H:i', strtotime($dentista['criado_em'])) ?></div>
        </div>

        <a href="painel_medico.php" class="back-btn">← Voltar ao Painel</a>

        <a href="editar_dados_medico.php" class="back-btn" style="margin-left:12px; background:#6b46c1; color:#fff;">✏️
            Editar meus dados</a>
    </div>
</body>

<style>
body {
    font-family: 'Inter', sans-serif;
    background: #f8faff;
    padding: 40px;
    color: #333;
}

.container {
    max-width: 650px;
    margin: 0 auto;
    background: #fff;
    border-radius: 16px;
    box-shadow: 0 4px 14px rgba(0, 0, 0, 0.05);
    padding: 36px 40px;
}

h2 {
    display: flex;
    align-items: center;
    gap: 10px;
    color: #333;
    margin-bottom: 28px;
    font-size: 24px;
}

h2 span {
    background: #f1edff;
    color: #5613f1ff;
    border-radius: 10px;
    padding: 8px;
    font-size: 20px;
}

.info-item {
    background: #faf9ff;
    border-radius: 12px;
    padding: 14px 18px;
    border-left: 4px solid #5613f1ff;
    margin-bottom: 14px;
}

.info-label {
    font-weight: 600;
    color: #5613f1ff;
    margin-bottom: 4px;
    display: block;
    font-size: 14px;
}

.info-value {
    color: #333;
    font-size: 15px;
    line-height: 1.5;
}

.back-btn {
    display: inline-block;
    margin-top: 25px;
    padding: 10px 20px;
    background: #f7f3ff;
    color: #5a32b5;
    border-radius: 12px;
    font-weight: 600;
    text-decoration: none;
    transition: background 0.2s;
}

.back-btn:hover {
    background: #ece3ff;
}

#escolherImagem {
    display: none;
}

label[for="escolherImagem"] {
    display: inline-block;
    padding: 6px 20px;
    background: #6b46c1;
    color: white;
    border-radius: 5px;
    cursor: pointer;
    font-weight: 600;
    transition: background 0.2s;
    margin-left: 10px;
}
</style>