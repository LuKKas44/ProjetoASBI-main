<?php
// filepath: c:\xampp\htdocs\FRONT-ASBI\painel_medico.php
session_start();

// Verifica se o usuário está logado e se é um médico
if (!isset($_SESSION['id']) || $_SESSION['tipo'] != 'medico') {
    header("Location: login.php");
    exit();
}
date_default_timezone_set('America/Sao_Paulo');

// Simular dados do médico (em produção, buscar do banco de dados)
$medico = [
    'nome' => $_SESSION['nome_completo'],
    'cro' => $_SESSION['cro'],
    'cpf' => $_SESSION['cpf'],
    'especialidade' => $_SESSION['especialidade'],
    'telefone' => $_SESSION['telefone'],
    'email' => $_SESSION['email'],
    'registro_asbi' => 'ASBI-MED-001',
    'validade' => '31/12/2026',
    'status' => 'Ativo',
    'avaliacoes' => 4.9,
    'ultimo_login' => date('d/m/Y H:i')
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Médico - ASBI</title>
    <link rel="stylesheet" href="painel_medico.css">
</head>

<body>
    <!-- Botão Toggle do Menu -->
    <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">
        <span class="toggle-icon">☰</span>
    </button>

    <!-- Overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

    <div class="dashboard-container">
        <!-- Menu Lateral -->
        <nav class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <?php
                $base = "/projeto_ASBI-main";
                $avatarUrl = null;
                $avatarFiles = glob(__DIR__ . '/../img/avatars/' . ($_SESSION['id'] ?? '') . '.*');
                if ($avatarFiles && count($avatarFiles) > 0) {
                    $file = basename($avatarFiles[0]);
                    $avatarUrl = $base . '/img/avatars/' . $file;
                }
                ?>
                <div class="user-avatar">
                    <?php if ($avatarUrl): ?>
                    <img src="<?= $avatarUrl ?>" alt="Avatar"
                        style="width:100%; height:100%; object-fit:cover; border-radius:50%;" />
                    <?php else: ?>
                    <?php echo strtoupper(substr($medico['nome'], 0, 2)); ?>
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <h3>Dr(a). <?php echo htmlspecialchars($medico['nome']); ?></h3>
                    <p><?php echo $medico['especialidade']; ?></p>
                    <p><?php echo $medico['cro']; ?></p>
                </div>

                <!-- Informações de Login -->
                <div class="login-info">
                    <div class="login-info-item">
                        <span class="login-info-label">Status:</span>
                        <span class="login-info-value status-online">
                            <span class="status-dot"></span>
                            Online
                        </span>
                    </div>
                    <div class="login-info-item">
                        <span class="login-info-label">Último login:</span>
                        <span class="login-info-value"><?php echo $medico['ultimo_login']; ?></span>
                    </div>
                    <div class="login-info-item">
                        <span class="login-info-label">ID Sessão:</span>
                        <span class="login-info-value"><?php echo $_SESSION['id']; ?></span>
                    </div>
                </div>
            </div>

            <div class="sidebar-nav">
                <a href="../index.php" class="nav-item active" onclick="setActiveItem(this)">
                    <span>🏠</span> Home
                </a>
                <a href="../agendamento/agenda.php" class="nav-item" onclick="setActiveItem(this)">
                    <span>📅</span> Agenda
                </a>
                <a href="meus_dados_medico.php" class="nav-item" onclick="setActiveItem(this)">
                    <span>👤</span> Meus Dados
                </a>
                <a href="pacientes.php" class="nav-item" onclick="setActiveItem(this)">
                    <span>👥</span> Meus Pacientes
                </a>
                <a href="pacientes_gerais.php" class="nav-item" onclick="setActiveItem(this)">
                    <span>📋</span> Pacientes Gerais
                </a>
                <a href="relatorios.php" class="nav-item" onclick="setActiveItem(this)">
                    <span>📊</span> Relatórios
                </a>

                <div class="nav-separator"></div>
                <?php $base = "/projeto_ASBI-main";?>
                <a href="../cadastro_e_login/logout.php" class="nav-item logout">
                    <span>🚪</span> Sair
                </a>
            </div>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="main-content" id="mainContent">
            <div class="content-header">
                <h1>Bem-vindo, Dr(a). <?php echo htmlspecialchars($medico['nome']); ?>!</h1>
                <p>Painel profissional ASBI - Gerencie sua agenda e atendimentos</p>
            </div>

            <?php 
            $dentista_id = $_SESSION['id'] ?? null;

            if (!$dentista_id) {
                die("Sessão inválida");
            }


            try {
             $pdo_login = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8", "root", "");
                $pdo_login->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            } catch (PDOException $e) {
                die("❌ Erro ao conectar com PDO no login: " . $e->getMessage());
            }
            // =======================
            // CONSULTAS DE HOJE
            // =======================
            try {
                $stmt = $pdo_login->prepare("
                    SELECT COUNT(*) AS total
                    FROM horarios
                    WHERE dentista_id = :dentista_id
                    AND data = CURDATE()
                    AND status = 'ocupado'
                ");
                $stmt->execute(['dentista_id' => $dentista_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $consultasHoje = $result['total'] ?? 0;
            } catch (PDOException $e) {
                $consultasHoje = 0;
            }
            
            try {
                $stmt = $pdo_login->prepare("
                    SELECT COUNT(*) AS total
                    FROM horarios
                    WHERE dentista_id = :dentista_id
                    AND data <= CURDATE()
                    AND status = 'ocupado'
                ");
                $stmt->execute(['dentista_id' => $dentista_id]);
                $result = $stmt->fetch(PDO::FETCH_ASSOC);
                $pendencias = $result['total'] ?? 0;
            } catch (PDOException $e) {
                $pendencias = 0;
            }

            $medico['pendencias'] = $pendencias;

            // Se você usa o array $medico pra centralizar dados, define aqui:
            $medico['consultas_hoje'] = $consultasHoje;
            ?>





            <!-- Estatísticas -->
            <div class="stats-grid">
                <div class="stat-card">
                    <div class="stat-value"><?php echo $medico['consultas_hoje']; ?></div>
                    <div class="stat-label">Consultas Hoje</div>
                </div>
                <div class="stat-card warning">
                    <div class="stat-value"><?php echo $medico['pendencias']; ?></div>
                    <div class="stat-label">Pendências</div>
                </div>
                <div class="stat-card purple">
                    <div class="stat-value"><?php echo $medico['avaliacoes']; ?></div>
                    <div class="stat-label">Avaliação Média</div>
                </div>
            </div>

            <div class="cards-grid">
                <!-- Carteirinha Profissional -->
                <div class="carteirinha-profissional">
                    <div class="carteirinha-header">
                        <div class="logo-asbi">ASBI PROFISSIONAL</div>
                        <div class="registro-profissional"><?php echo $medico['registro_asbi']; ?></div>
                    </div>

                    <div
                        style="border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 12px; margin-bottom: 16px; position: relative; z-index: 2; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <h3>Dr(a). <?php echo htmlspecialchars($medico['nome']); ?></h3>
                            <p style="opacity: 0.9;"><?php echo $medico['especialidade']; ?></p>
                        </div>
                        <div
                            style="width:64px; height:64px; border-radius:8px; overflow:hidden; background:rgba(255,255,255,0.06);">
                            <?php if (!empty($avatarUrl)): ?>
                            <img src="<?= $avatarUrl ?>" alt="Avatar"
                                style="width:100%; height:100%; object-fit:cover;" />
                            <?php else: ?>
                            <div
                                style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700;">
                                <?php echo strtoupper(substr($medico['nome'],0,2)); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="carteirinha-body">
                        <div class="carteirinha-info">
                            <span class="info-label">CRO</span>
                            <span class="info-value"><?php echo $medico['cro']; ?></span>
                        </div>
                        <div class="carteirinha-info">
                            <span class="info-label">CPF</span>
                            <span class="info-value"><?php echo $medico['cpf']; ?></span>
                        </div>
                        <div class="carteirinha-info">
                            <span class="info-label">Especialidade</span>
                            <span class="info-value"><?php echo $medico['especialidade']; ?></span>
                        </div>
                        <div class="carteirinha-info">
                            <span class="info-label">Validade</span>
                            <span class="info-value"><?php echo $medico['validade']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Agenda do Dia -->
                <?php
                try {
                    $pdo = new PDO(
                            "mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4",
                            "root",
                            "",
                            [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
                        );
                    } catch (PDOException $e) {
                        die("<p>Erro na conexão: " . $e->getMessage() . "</p>");
                    }

                    $dentistaId = $_SESSION['id'] ?? null;
                    if (!$dentistaId) {
                        echo "<p style='color:gray;'>Sessão inválida. Faça login.</p>";
                        exit;
                    }

                    try {
                        $sql = "
                            SELECT 
                                h.data,
                                h.hora,
                                h.status,
                                u.nome_crianca AS paciente
                            FROM horarios h
                            LEFT JOIN usuario u ON u.id = h.usuario_id
                            WHERE h.dentista_id = :dentista_id
                            AND h.status = 'ocupado'
                            AND h.data >= CURDATE()
                            ORDER BY h.data ASC, h.hora ASC
                            LIMIT 3
                        ";

                        $stmt = $pdo->prepare($sql);
                        $stmt->execute(['dentista_id' => $dentistaId]);
                        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

                        echo '<div class="info-card">
                                <div class="card-header">
                                    <span>📅</span>
                                    <h3>Próximas Consultas</h3>
                                </div>
                                <div class="agenda-list">';

                        if ($rows) {
                            foreach ($rows as $r) {
                                $paciente = htmlspecialchars($r['paciente'] ?? 'Sem paciente');
                                $data = date('d/m/Y', strtotime($r['data']));
                                $hora = date('H:i', strtotime($r['hora']));
                                $status = htmlspecialchars($r['status']);

                                $classe = ($status === 'ocupado') ? 'status-agendado' : (($status === 'disponivel') ? 'status-pendente' : 'status-concluido');
                                $texto = ($status === 'ocupado') ? 'Agendado' : ucfirst($status);

                                echo "
                                    <div class='agenda-item' style='
                                        display: flex;
                                        align-items: center;
                                        justify-content: space-between;
                                        background: var(--secondary);
                                        padding: 10px;
                                        border-left: 3px solid var(--purple);
                                        border-radius: 8px;
                                    '>
                                        <div style='display: flex; flex-direction: column; gap: 4px;'>
                                            <div class='patient-name' style='font-weight: 600; font-size: 15px;'>{$paciente}</div>
                                            <div class='agenda-time' style='font-size: 14px; color: gray;'>{$data} às {$hora}</div>
                                            <div class='procedure' style='font-size: 13px; color: var(--text-secondary);'>Consulta</div>
                                        </div>
                                        <span class='status-badge {$classe}' style='
                                            padding: 4px 10px;
                                            border-radius: 12px;
                                            font-size: 12px;
                                            font-weight: 600;
                                            color: white;
                                            background: " . ($classe === 'status-agendado' ? 'var(--purple)' : 'var(--success)') . ";
                                        '>{$texto}</span>
                                    </div>
                                ";
                            }
                        } else {
                            echo "<p style='color:gray; padding:10px;'>Nenhum horário agendado.</p>";
                        }

                        echo '</div></div>';
                    } catch (PDOException $e) {
                        echo "<p>Erro ao carregar horários: " . $e->getMessage() . "</p>";
                    }
                    ?>

                <!-- Dados Profissionais -->
                <div class="info-card">
                    <div class="card-header">
                        <span>👤</span>
                        <h3>Dados Profissionais</h3>
                    </div>
                    <div style="display: grid; gap: 12px;">
                        <div>
                            <strong>CRO:</strong> <?php echo $medico['cro']; ?>
                        </div>
                        <div>
                            <strong>Telefone:</strong> <?php echo $medico['telefone']; ?>
                        </div>
                        <div>
                            <strong>E-mail:</strong> <?php echo $medico['email']; ?>
                        </div>
                        <div>
                            <strong>Status:</strong>
                            <span class="status-badge status-concluido"><?php echo $medico['status']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Pacientes Recentes -->

                <div class="info-card">
                    <div class="card-header">
                        <span>👥</span>
                        <h3>Pacientes Recentes</h3>
                    </div>
                    <div style="display: grid; gap: 12px;">
                        <?php
        // --- Conexão PDO isolada ---
        try {
            $pdo = new PDO(
                "mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4",
                "root", // usuário
                "",     // senha
                [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
            );
        } catch (PDOException $e) {
            die("<p>Erro na conexão: " . $e->getMessage() . "</p>");
        }

        // --- Consulta dos pacientes recentes ---
        try {
            $sql = "SELECT DISTINCT u.nome_crianca AS nome, c.data_consulta
                    FROM consultas c
                    JOIN usuario u ON u.id = c.usuario_id
                    WHERE c.dentista_id = :dentista_id
                    ORDER BY c.data_consulta DESC
                    LIMIT 3";

            // prepara e executa
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['dentista_id' => $_SESSION['id']]); // supondo que o id do dentista está na sessão

            // verifica se há resultados
            if ($stmt->rowCount() > 0) {
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    $nome = htmlspecialchars($row['nome']);
                    $data = date('d/m/Y', strtotime($row['data_consulta']));
                    echo "
                    <div style='padding: 8px; border-left: 3px solid var(--success); background: var(--secondary);'>
                        <strong>$nome</strong> - Última consulta: $data
                    </div>";
                }
            } else {
                echo "<p style='color:gray;'>Nenhum paciente recente encontrado.</p>";
            }
        } catch (PDOException $e) {
            echo "<p>Erro ao buscar pacientes: " . $e->getMessage() . "</p>";
        }
        ?>
                    </div>
                </div>

            </div>

    </div>


    </main>
    </div>

    <script>
    let sidebarOpen = false;

    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContent');

        sidebarOpen = !sidebarOpen;

        if (sidebarOpen) {
            sidebar.classList.add('open');
            overlay.classList.add('active');
            toggleBtn.classList.add('active');

            // Em desktop, desloca o conteúdo
            if (window.innerWidth > 768) {
                mainContent.classList.add('shifted');
            }
        } else {
            closeSidebar();
        }
    }

    function closeSidebar() {
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarToggle');
        const mainContent = document.getElementById('mainContent');

        sidebarOpen = false;

        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        toggleBtn.classList.remove('active');
        mainContent.classList.remove('shifted');
    }

    function setActiveItem(element) {
        // Remove active de todos os itens
        document.querySelectorAll('.nav-item').forEach(item => {
            item.classList.remove('active');
        });

        // Adiciona active ao item clicado
        element.classList.add('active');

        // Fecha o menu em mobile após clicar
        if (window.innerWidth <= 768) {
            closeSidebar();
        }
    }

    // Atualizar horário em tempo real
    function updateTime() {
        const now = new Date();
        const timeStr = now.toLocaleTimeString('pt-BR', {
            hour: '2-digit',
            minute: '2-digit'
        });
        const timeElement = document.getElementById('current-time');
        if (timeElement) {
            timeElement.textContent = timeStr;
        }
    }

    // Atualizar a cada minuto
    setInterval(updateTime, 60000);

    // Fechar sidebar quando redimensionar para desktop
    window.addEventListener('resize', () => {
        if (window.innerWidth > 768 && sidebarOpen) {
            document.getElementById('mainContent').classList.add('shifted');
        } else if (window.innerWidth <= 768) {
            document.getElementById('mainContent').classList.remove('shifted');
        }
    });

    // Atalho de teclado para toggle (Ctrl + M)
    document.addEventListener('keydown', (e) => {
        if (e.ctrlKey && e.key === 'm') {
            e.preventDefault();
            toggleSidebar();
        }
    });

    // Fechar com ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && sidebarOpen) {
            closeSidebar();
        }
    });
    </script>
</body>

</html>