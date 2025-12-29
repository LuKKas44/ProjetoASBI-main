<?php
session_start();

// apenas dentistas podem acessar
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

    // traz todos os pacientes de todas as consultas
    $stmt = $pdo->query("
        SELECT 
            u.nome_crianca AS paciente,
            c.data_consulta,
            c.procedimento,
            c.observacoes,
            d.nome_completo AS dentista
        FROM consultas c
        JOIN usuario u ON u.id = c.usuario_id
        JOIN dentistas d ON d.id = c.dentista_id
        ORDER BY c.data_consulta DESC
    ");

    $pacientes = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die('Erro ao buscar pacientes gerais: ' . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Pacientes Gerais</title>
    <link rel="stylesheet" href="pacientes_gerais.css">
</head>

<body>

    <div class="container">
        <h2><span>🧾</span> Pacientes Gerais</h2>

        <div class="search-box">
            <input type="text" id="searchInput"
                placeholder="🔍 Buscar paciente, dentista, procedimento ou observação...">
            <a href="painel_medico.php" class="back-btn">⬅ Voltar ao Painel</a>
        </div>

        <?php if (empty($pacientes)) : ?>
        <p style="color: gray;">Nenhum registro encontrado.</p>
        <?php else : ?>
        <table id="pacientesTable">
            <thead>
                <tr>
                    <th>Nome do Paciente</th>
                    <th>Data</th>
                    <th>Procedimento</th>
                    <th>Profissional</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pacientes as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['paciente']) ?></td>
                    <td><?= date('d/m/Y', strtotime($p['data_consulta'])) ?></td>
                    <td><?= htmlspecialchars($p['procedimento'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($p['dentista'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($p['observacoes'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>


    </div>

    <script>
    // filtro simples em tempo real
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