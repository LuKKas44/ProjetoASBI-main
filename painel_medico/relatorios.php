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

    $dentista_id = $_SESSION['id'];

    // Consultas de hoje
    $stmtHoje = $pdo->prepare("
        SELECT COUNT(*) FROM consultas 
        WHERE dentista_id = :id AND DATE(data_consulta) = CURDATE()
    ");
    $stmtHoje->execute(['id' => $dentista_id]);
    $consultasHoje = $stmtHoje->fetchColumn();

    // Pacientes únicos atendidos
    $stmtPacientes = $pdo->prepare("
        SELECT COUNT(DISTINCT usuario_id) FROM consultas 
        WHERE dentista_id = :id
    ");
    $stmtPacientes->execute(['id' => $dentista_id]);
    $pacientesAtendidos = $stmtPacientes->fetchColumn();

    // Consultas no mês atual
    $stmtMes = $pdo->prepare("
        SELECT COUNT(*) FROM consultas 
        WHERE dentista_id = :id 
        AND MONTH(data_consulta) = MONTH(CURDATE())
        AND YEAR(data_consulta) = YEAR(CURDATE())
    ");
    $stmtMes->execute(['id' => $dentista_id]);
    $consultasMes = $stmtMes->fetchColumn();

    // Consultas por mês (para o gráfico)
    $stmtGrafico = $pdo->prepare("
        SELECT MONTH(data_consulta) AS mes, COUNT(*) AS total
        FROM consultas 
        WHERE dentista_id = :id
        GROUP BY MONTH(data_consulta)
        ORDER BY mes
    ");
    $stmtGrafico->execute(['id' => $dentista_id]);
    $graficoDados = $stmtGrafico->fetchAll(PDO::FETCH_ASSOC);

    $meses = ['Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
} catch (PDOException $e) {
    die('Erro ao buscar relatórios: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Relatórios</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="relatorios.css">
</head>

<body>

    <div class="container">
        <h2><span>📊</span> Relatórios de Atendimentos</h2>

        <div class="stats">
            <div class="stat-card">
                <div class="stat-value"><?= $consultasHoje ?></div>
                <div class="stat-label">Consultas Hoje</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $pacientesAtendidos ?></div>
                <div class="stat-label">Pacientes Atendidos</div>
            </div>
            <div class="stat-card">
                <div class="stat-value"><?= $consultasMes ?></div>
                <div class="stat-label">Consultas no Mês</div>
            </div>
        </div>

        <div class="chart">
            <h3>Consultas por Mês</h3>
            <canvas id="graficoConsultas" height="120"></canvas>
        </div>

        <a href="painel_medico.php" class="back-btn">⬅ Voltar ao Painel</a>
    </div>

    <script>
    const ctx = document.getElementById('graficoConsultas');

    const data = {
        labels: <?= json_encode(array_map(fn($g) => $meses[$g['mes'] - 1], $graficoDados)); ?>,
        datasets: [{
            label: 'Consultas',
            data: <?= json_encode(array_column($graficoDados, 'total')); ?>,
            backgroundColor: '#6b46c1cc',
            borderRadius: 8,
            borderWidth: 1,
            hoverBackgroundColor: '#5a32b5',
        }]
    };

    new Chart(ctx, {
        type: 'bar',
        data: data,
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return context.parsed.y + ' consultas';
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        precision: 0
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            }
        }
    });
    </script>

</body>

</html>