<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}
require_once __DIR__ . '/../conexao.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user = null;
if ($id > 0) {
    $stmt = $conn->prepare('SELECT * FROM usuario WHERE id = ?');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $user = $res->fetch_assoc();
}
?>
<?php
// If this is an AJAX request, return only the form fragment (no full HTML) to avoid nesting full pages inside the modal.
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (!$isAjax) {
    ?><!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?= $id ? 'Editar' : 'Criar' ?> Usuário</title>
        <link rel="stylesheet" href="admin.css">
    </head>
    <body>
    <div class="admin-container">
    <?php
}
?>

    <div class="form-card">
        <form method="post" action="save_user.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
            <label>Nome da Criança</label>
            <input name="nome_crianca" required value="<?= htmlspecialchars($user['nome_crianca'] ?? '') ?>">

               <label>Email</label>
               <input type="email" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>" required>

            <div class="form-row">
                <div>
                    <label>Telefone</label>
                    <input name="telefone" value="<?= htmlspecialchars($user['telefone'] ?? '') ?>">
                </div>
                <div>
                    <label>CPF</label>
                    <input name="cpf" value="<?= htmlspecialchars($user['cpf'] ?? '') ?>">
                </div>
            </div>

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
    // Close modal if present
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
