<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}
require_once __DIR__ . '/../conexao.php';

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$horario = null;
if ($id > 0) {
    $stmt = $conn->prepare('SELECT * FROM horarios WHERE id = ? LIMIT 1');
    $stmt->bind_param('i', $id);
    $stmt->execute();
    $res = $stmt->get_result();
    $horario = $res->fetch_assoc();
}

// fetch dentists and users for selects
$dentistas = [];
$r = $conn->query('SELECT id, nome_completo FROM dentistas ORDER BY nome_completo');
while ($row = $r->fetch_assoc()) $dentistas[] = $row;
$usuarios = [];
$r2 = $conn->query('SELECT id, nome_crianca FROM usuario ORDER BY nome_crianca');
while ($row = $r2->fetch_assoc()) $usuarios[] = $row;
?>
<?php
// Return only the form fragment for AJAX requests to avoid nesting full pages inside the modal
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
if (!$isAjax) {
    ?><!doctype html>
    <html>
    <head>
        <meta charset="utf-8">
        <title><?= $id ? 'Editar' : 'Criar' ?> Horário</title>
        <link rel="stylesheet" href="admin.css">
        <style>body{background:transparent}</style>
    </head>
    <body>
    <div class="form-card">
    <?php
}
?>

    <form method="post" action="save_horario.php">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">

        <label>Data</label>
        <input type="date" name="data" required value="<?= htmlspecialchars($horario['data'] ?? '') ?>">

        <label>Hora</label>
        <div style="margin-bottom:8px">
            <strong><?= htmlspecialchars($horario['hora'] ?? '—') ?></strong>
        </div>
        <input type="hidden" name="hora" value="<?= htmlspecialchars($horario['hora'] ?? '') ?>">

        <label>Status</label>
        <select name="status">
            <?php $opts = ['disponivel','ocupado','finalizado','cancelado']; foreach($opts as $o): ?>
            <option value="<?= $o ?>" <?= (($horario['status'] ?? '')===$o)?'selected':'' ?>><?= ucfirst($o) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Dentista</label>
        <select name="dentista_id">
            <option value="">—</option>
            <?php foreach($dentistas as $d): ?>
            <option value="<?= $d['id'] ?>" <?= (($horario['dentista_id'] ?? '')==$d['id'])?'selected':'' ?>><?= htmlspecialchars($d['nome_completo'] ?? $d['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <label>Paciente (opcional)</label>
        <select name="usuario_id">
            <option value="">—</option>
            <?php foreach($usuarios as $u): ?>
            <option value="<?= $u['id'] ?>" <?= (($horario['usuario_id'] ?? '')==$u['id'])?'selected':'' ?>><?= htmlspecialchars($u['nome_crianca'] ?? $u['nome']) ?></option>
            <?php endforeach; ?>
        </select>

        <div class="actions" style="margin-top:10px">
            <button class="button" type="submit">Salvar</button>
            <button class="button cancel" type="button" id="cancelBtn">Cancelar</button>
        </div>
    </form>

    <form method="post" action="save_horario.php" onsubmit="return confirm('Confirmar exclusão deste horário?')" style="margin-top:8px">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        <input type="hidden" name="delete" value="1">
        <button class="button secondary" type="submit">Excluir Horário</button>
    </form>

<script>
document.getElementById('cancelBtn').addEventListener('click', function () {
  if (window.parent && window.parent.document) {
    try { window.parent.document.getElementById('modalClose').click(); } catch(e) {}
  }
});
</script>

<?php if (!$isAjax) { ?>
    </div>
    </body>
    </html>
<?php } ?>
