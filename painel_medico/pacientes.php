<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'medico') {
    header('Location: login.php');
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4",
        "root", "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $stmt = $pdo->prepare("
        SELECT 
            u.nome_crianca AS paciente,
            c.data_consulta,
            c.procedimento,
            c.observacoes
        FROM consultas c
        JOIN usuario u ON u.id = c.usuario_id
        WHERE c.dentista_id = :dentista_id
        ORDER BY c.data_consulta DESC
    ");
    $stmt->execute(['dentista_id' => $_SESSION['id']]);
    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Erro ao buscar pacientes: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Pacientes Atendidos</title>
    <link rel="stylesheet" href="pacientes.css">
</head>

<body>

    <div class="container">
        <h2><span>👥</span> Pacientes Atendidos</h2>

        <div class="search-box">
            <input type="text" id="searchInput" placeholder="🔍 Buscar paciente, procedimento ou observação...">
        </div>

        <?php if (empty($pacientes)) : ?>
        <p style="color: gray;">Nenhum paciente atendido ainda.</p>
        <?php else : ?>
        <table id="pacientesTable">
            <thead>
                <tr>
                    <th>Nome do Paciente</th>
                    <th>Data</th>
                    <th>Procedimento</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['paciente']) ?></td>
                    <td><?= date('d/m/Y', strtotime($p['data_consulta'])) ?></td>
                    <td><?= htmlspecialchars($p['procedimento'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($p['observacoes'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <a href="painel_medico.php" class="back-btn">⬅ Voltar ao Painel</a>
    </div>

    <script>
    // filtro simples da tabela
    document.getElementById("searchInput").addEventListener("keyup", function() {
        const filter = this.value.toLowerCase();
        const rows = document.querySelectorAll("#pacientesTable tbody tr");

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(filter) ? "" : "none";
        });
    });
    </script>

</body>

</html>