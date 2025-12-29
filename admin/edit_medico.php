<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}
require_once __DIR__ . '/../conexao.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$med = null;
if ($id > 0) {
    $stmt = $conn->prepare('SELECT * FROM dentistas WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $med = $res->fetch_assoc();
}
?>
<?php
// Return only form fragment for AJAX to avoid full-page nesting in modal
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (!$isAjax) {
    ?><!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?= $id ? 'Editar' : 'Criar' ?> Dentista</title>
        <link rel="stylesheet" href="admin.css">
    </head>
    <body>
    <div class="admin-container">
    <?php
}
?>

    <div class="form-card">
        <form method="post" action="save_medico.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

            <label>Nome completo</label>
            <input name="nome_completo" required value="<?= htmlspecialchars($med['nome_completo'] ?? '') ?>">

            <label>Email</label>
            <input name="email" type="email" required value="<?= htmlspecialchars($med['email'] ?? '') ?>">

            <div class="form-row">
                <div>
                    <label>Telefone</label>
                    <input name="telefone" value="<?= htmlspecialchars($med['telefone'] ?? '') ?>">
                </div>
                <div>
                    <label>CRO</label>
                    <input name="cro" value="<?= htmlspecialchars($med['cro'] ?? '') ?>">
                </div>
            </div>

            <label>Especialidade</label>
            <input name="especialidade" value="<?= htmlspecialchars($med['especialidade'] ?? '') ?>">

            <label>CPF</label>
            <input name="cpf" value="<?= htmlspecialchars($med['cpf'] ?? '') ?>">

            <label>Senha <span class="muted">(deixe em branco para não alterar)</span></label>
            <input name="senha" type="password">

            <div class="actions">
                <button class="button" type="submit">Salvar</button>
                <button class="button cancel" type="button" id="cancelBtn">Cancelar</button>
            </div>
        </form>
    </div>

    <script>
    document.getElementById('cancelBtn').addEventListener('click', function () {
        if (window.parent && window.parent.document) {
            try { window.parent.document.getElementById('modalClose').click(); } catch(e) {}
        }
    });
    </script>

<?php if (!$isAjax) { ?>
    <p class="footer-note">Os dados são sensíveis — verifique antes de salvar.</p>
    </div>
    </body>
    </html>
<?php } ?>
