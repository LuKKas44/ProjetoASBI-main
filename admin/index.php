<?php
session_start();
if (!isset($_SESSION['id']) || ($_SESSION['tipo'] ?? '') !== 'admin') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}
require_once __DIR__ . '/../conexao.php';

// Fetch users
$users = [];
$sql = "SELECT id, nome_crianca AS nome, email, telefone FROM usuario ORDER BY id DESC";
if ($res = $conn->query($sql)) {
    while ($row = $res->fetch_assoc()) $users[] = $row;
}

$medicos = [];
$sql2 = "SELECT id, nome_completo AS nome, email, cro FROM dentistas ORDER BY id DESC";
if ($res2 = $conn->query($sql2)) {
    while ($row = $res2->fetch_assoc()) $medicos[] = $row;
}

// Fetch horarios (agendamentos)
$horarios = [];
$sql3 = "SELECT h.id, h.data, h.hora, h.status, h.dentista_id, d.nome_completo AS dentista, h.usuario_id, u.nome_crianca AS usuario
         FROM horarios h
         LEFT JOIN dentistas d ON d.id = h.dentista_id
         LEFT JOIN usuario u ON u.id = h.usuario_id
         ORDER BY h.data DESC, h.hora DESC LIMIT 500";
if ($res3 = $conn->query($sql3)) {
    while ($row = $res3->fetch_assoc()) $horarios[] = $row;
}
// helper to find avatar path
function avatar_path_for($id, $type = 'user') {
    $baseDir = __DIR__ . '/../img/avatars/';
    if (!$id) return null;
    // try exact id
    $matches = glob($baseDir . $id . '.*');
    if ($matches) return str_replace('\\','/', str_replace(__DIR__ . '/../', '../', $matches[0]));
    // try prefixed (medico or dentista)
    $matches = glob($baseDir . ($type=='medico'? 'medico_':'dentista_') . $id . '.*');
    if ($matches) return str_replace('\\','/', str_replace(__DIR__ . '/../', '../', $matches[0]));
    return null;
}
?>
<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Admin - Usuários e Dentistas</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="admin-container">
    <div class="admin-topbar">
        <div style="display:flex;align-items:center;gap:12px">
            <img src="../img/LOGOASBI.png" alt="logo" height="48" style="border-radius:8px;object-fit:cover"/>
            <div>
                <div class="title">Painel Administrativo</div>
                <div class="sub">Gerencie usuários, dentistas e agendamentos</div>
            </div>
        </div>
        <div class="actions">
            <a class="button" href="../index.php">Voltar ao site</a>
            <a class="button secondary" href="../cadastro_e_login/logout.php" style="margin-left:8px">Logout</a>
        </div>
    </div>

    <!-- Tabs -->
    <div class="tabs" role="tablist" style="margin-top:12px; margin-bottom:16px">
        <button class="tab active" data-tab="users" role="tab">Usuários</button>
        <button class="tab" data-tab="medicos" role="tab">Dentistas</button>
        <button class="tab" data-tab="horarios" role="tab">Agendamentos</button>
    </div>

        <section class="tab-panel" id="tab-users">
        <h2 style="margin-bottom:8px">Usuários</h2>
        <div class="actions">
            <a class="button" href="edit_user.php">Criar Usuário</a>
            <input id="searchUsers" class="search-input" placeholder="Pesquisar usuários por nome, email ou telefone" style="margin-left:12px">
        </div>
    <table>
        <tr><th></th><th>ID</th><th>Nome</th><th>Email</th><th>Telefone</th><th>Ações</th></tr>
        <?php foreach($users as $u): ?>
        <tr>
            <?php $av = avatar_path_for($u['id'],'user'); ?>
            <td class="avatar-cell"><?php if($av): ?><img src="<?= htmlspecialchars($av) ?>" class="avatar-sm" alt="avatar"><?php else: $parts = explode(' ',trim($u['nome'])); $initials = strtoupper(substr($parts[0] ?? '',0,1) . substr($parts[1] ?? '',0,1)); ?><span class="initials-badge"><?= htmlspecialchars($initials) ?></span><?php endif; ?></td>
            <td><?= htmlspecialchars($u['id']) ?></td>
            <td><?= htmlspecialchars($u['nome']) ?></td>
            <td><?= htmlspecialchars($u['email']) ?></td>
            <td><?= htmlspecialchars($u['telefone']) ?></td>
            <td>
                <span class="table-actions"><button type="button" class="button small open-modal" data-url="edit_user.php?id=<?= $u['id'] ?>">Editar</button>
                <a href="delete.php?type=user&id=<?= $u['id'] ?>" onclick="return confirm('Confirma exclusão?')" class="link">Excluir</a></span>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
        </section>

    <section class="tab-panel" id="tab-medicos" style="margin-top:18px;display:none">
    <h2 style="margin-bottom:8px">Dentistas</h2>
        <div class="actions"><button class="button" onclick="location.href='edit_medico.php'">Criar Dentista</button>
            <input id="searchMedicos" class="search-input" placeholder="Pesquisar dentistas por nome, email ou CRO" style="margin-left:12px">
        </div>
    <table>
        <tr><th></th><th>ID</th><th>Nome</th><th>Email</th><th>CRO</th><th>Ações</th></tr>
        <?php foreach($medicos as $m): ?>
        <tr>
            <?php $avm = avatar_path_for($m['id'],'medico'); ?>
            <td class="avatar-cell"><?php if($avm): ?><img src="<?= htmlspecialchars($avm) ?>" class="avatar-sm" alt="avatar"><?php else: $parts = explode(' ',trim($m['nome'])); $initials = strtoupper(substr($parts[0] ?? '',0,1) . substr($parts[1] ?? '',0,1)); ?><span class="initials-badge"><?= htmlspecialchars($initials) ?></span><?php endif; ?></td>
            <td><?= htmlspecialchars($m['id']) ?></td>
            <td><?= htmlspecialchars($m['nome']) ?></td>
            <td><?= htmlspecialchars($m['email']) ?></td>
            <td><?= htmlspecialchars($m['cro']) ?></td>
            <td class="table-actions">
                <button type="button" class="button small open-modal" data-url="edit_medico.php?id=<?= $m['id'] ?>">Editar</button> |
                <a href="delete.php?type=medico&id=<?= $m['id'] ?>" onclick="return confirm('Confirma exclusão?')">Excluir</a>
            </td>
        </tr>
        <?php endforeach; ?>
    </table>
    </section>

    <section class="tab-panel" id="tab-horarios" style="margin-top:22px;display:none">
    <h2 style="margin-bottom:8px">Agendamentos</h2>
    <div class="actions"><input id="searchHorarios" class="search-input" placeholder="Pesquisar agendamentos por paciente, dentista ou data"></div>
    <table id="horariosTable">
        <thead><tr><th>ID</th><th>Data</th><th>Hora</th><th>Status</th><th>Dentista</th><th>Paciente</th><th>Ações</th></tr></thead>
        <tbody>
        <?php foreach($horarios as $h): ?>
        <tr>
            <td><?= htmlspecialchars($h['id']) ?></td>
            <td><?= htmlspecialchars($h['data']) ?></td>
            <td><?= htmlspecialchars($h['hora']) ?></td>
            <td><?= htmlspecialchars($h['status']) ?></td>
            <td><?= htmlspecialchars($h['dentista'] ?? '—') ?></td>
            <td><?= htmlspecialchars($h['usuario'] ?? ($h['usuario_id'] ? 'ID:'+ $h['usuario_id'] : '—')) ?></td>
            <td class="table-actions"><button type="button" class="button small open-modal" data-url="edit_horario.php?id=<?= $h['id'] ?>">Editar</button> | <a href="../agendamento/desmarcar_horario.php?id=<?= $h['id'] ?>" onclick="return confirm('Confirma exclusão?')">Excluir</a></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </section>

        <p class="footer-note">Painel administrativo — apenas para administradores.</p>
    
        <!-- Modal (reaproveitado para editar/criar) -->
        <div id="adminModal" class="modal" aria-hidden="true">
            <div class="modal-card" role="dialog" aria-modal="true">
                <button class="modal-close" id="modalClose">✕</button>
                <div id="modalContent">Carregando…</div>
            </div>
        </div>

        <script src="admin.js"></script>
    </div>
</body>
</html>
