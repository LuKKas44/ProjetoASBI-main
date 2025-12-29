<?php
session_start();

if (!isset($_SESSION['id'])) {
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
            c.data_consulta,
            c.procedimento,
            c.observacoes,
            d.nome_completo AS dentista_nome
        FROM consultas c
        JOIN dentistas d ON d.id = c.dentista_id
        WHERE c.usuario_id = :usuario_id
        ORDER BY c.data_consulta DESC
    ");

    $stmt->execute(['usuario_id' => $_SESSION['id']]);
    $consultas = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    die("Erro ao buscar histórico: " . $e->getMessage());
}
?>


<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Histórico de Consultas</title>
    <link rel="stylesheet" href="historico.css">
</head>

<body>

    <div class="container">
        <h2><span>📋</span> Histórico de Consultas</h2>

        <?php if (empty($consultas)) : ?>
        <p style="color: gray;">Nenhum atendimento encontrado.</p>
        <?php else : ?>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Procedimento</th>
                    <th>Profissional</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($consultas as $c): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($c['data_consulta'])) ?></td>
                    <td><?= htmlspecialchars($c['procedimento'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($c['dentista_nome'] ?? '—') ?></td>
                    <td><?= htmlspecialchars($c['observacoes'] ?? '—') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php endif; ?>

        <a href="painel_cliente.php" class="back-btn">⬅ Voltar ao Painel</a>
    </div>

</body>

</html>