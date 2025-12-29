<?php
// filepath: c:\xampp\htdocs\FRONT-ASBI\painel_cliente.php
session_start();

// Verifica se o usuário está logado e se é um cliente
if (!isset($_SESSION['id']) || $_SESSION['tipo'] != 'cliente') {
    header("Location: cadastro_e_login/login.php");
    exit();
}


include(__DIR__ . "/../conexao.php");



$id_usuario = $_SESSION['id'];

$sql = "SELECT nome_crianca, cpf, data_nascimento, telefone, email, cep, cidade, bairro, rua, estado
        FROM usuario
        WHERE id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario); // i = integer
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $cliente = $result->fetch_assoc();
} else {
    die("❌ Usuário não encontrado no banco de dados.");
}
date_default_timezone_set('America/Sao_Paulo');

// Simular dados do cliente (em produção, buscar do banco de dados)
$cliente = [
    'nome' => $_SESSION['nome_crianca'],
    'cpf' => $_SESSION['cpf'],
    'data_nascimento' => $_SESSION['data_nascimento'],
    'telefone' => $_SESSION['telefone'],
    'email' => $_SESSION['email'],
    'cep' => $_SESSION['cep'],
    'cidade' => $_SESSION['cidade'],
    'rua' => $_SESSION['rua'],
    'bairro' => $_SESSION['bairro'],
    'estado' => $_SESSION['estado'],
    'plano' => 'ASBI Básico',
    'numero_carteira' => 'ASBI-2024-0001',
    'validade' => '31/12/2026',
    'status' => 'Ativo',
    'ultimo_login' =>  date('d/m/Y H:i')
];
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Cliente - ASBI</title>
    <link rel="stylesheet" href="painel_cliente1.css">
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
                    <?php echo strtoupper(substr($cliente['nome'], 0, 2)); ?>
                    <?php endif; ?>
                </div>
                <div class="user-info">
                    <h3><?php echo htmlspecialchars($cliente['nome']); ?></h3>
                    <p>Cliente ASBI</p>
                    <p>Carteira: <?php echo $cliente['numero_carteira']; ?></p>
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
                        <span class="login-info-value"><?php echo $cliente['ultimo_login']; ?></span>
                    </div>
                    <div class="login-info-item">
                        <span class="login-info-label">ID Sessão:</span>
                        <span class="login-info-value"><?php echo $_SESSION['id']; ?></span>
                    </div>
                    <div class="login-info-item">
                        <span class="login-info-label">Plano:</span>
                        <span class="login-info-value"><?php echo $cliente['plano']; ?></span>
                    </div>
                </div>
            </div>

            <div class="sidebar-nav">
                <a href="../index.php" class="nav-item active" onclick="setActiveItem(this)">
                    <span>🏠</span> Home
                </a>
                <a href="../agendamento/agenda.php" class="nav-item" onclick="setActiveItem(this)">
                    <span>📅</span> Agendamentos
                </a>
                <a href="historico.php" class="nav-item" onclick="setActiveItem(this)">
                    <span>🦷</span> Histórico
                </a>
                <a href="meus_dados.php" class="nav-item" onclick="setActiveItem(this)">
                    <span>👤</span> Meus Dados
                </a>

                <div class="nav-separator"></div>

                <a href="../cadastro_e_login/logout.php" class="nav-item logout">
                    <span>🚪</span> Sair
                </a>
            </div>
        </nav>

        <!-- Conteúdo Principal -->
        <main class="main-content" id="mainContent">
            <div class="content-header">
                <h1>Bem-vindo, <?php echo htmlspecialchars($cliente['nome']); ?>!</h1>
                <p>Gerencie suas informações e acompanhe seus cuidados de saúde bucal</p>
            </div>

            <!-- Informações Rápidas -->
            <div class="quick-info">
                <div class="quick-info-item">
                    <div class="quick-info-label">Hoje</div>
                    <div class="quick-info-value"><?php echo date('d/m/Y'); ?></div>
                </div>
                <div class="quick-info-item">
                    <div class="quick-info-label">Horário</div>
                    <div class="quick-info-value" id="current-time">
                        <?php date_default_timezone_set('America/Sao_Paulo'); echo date('H:i'); ?></div>
                </div>

                <div class="quick-info-item">
                    <div class="quick-info-label">Status da Conta</div>
                    <div class="quick-info-value status-online">
                        <span class="status-dot"></span>
                        <?php echo $cliente['status']; ?>
                    </div>
                </div>
            </div>

            <div class="cards-grid">
                <!-- Carteirinha Digital -->
                <div class="carteirinha">
                    <div class="carteirinha-header">
                        <div class="logo-asbi">ASBI</div>
                        <div class="numero-carteira"><?php echo $cliente['numero_carteira']; ?></div>
                    </div>

                    <div
                        style="border-bottom: 1px solid rgba(255,255,255,0.3); padding-bottom: 12px; margin-bottom: 16px; position: relative; z-index: 2; display:flex; align-items:center; justify-content:space-between;">
                        <div>
                            <h3><?php echo htmlspecialchars($cliente['nome']); ?></h3>
                            <p style="opacity: 0.9;">Associação de Saúde Bucal Infantil</p>
                        </div>
                        <div
                            style="width:64px; height:64px; border-radius:8px; overflow:hidden; background:rgba(255,255,255,0.06);">
                            <?php if (!empty($avatarUrl)): ?>
                            <img src="<?= $avatarUrl ?>" alt="Avatar"
                                style="width:100%; height:100%; object-fit:cover;" />
                            <?php else: ?>
                            <div
                                style="width:100%; height:100%; display:flex; align-items:center; justify-content:center; color:white; font-weight:700;">
                                <?php echo strtoupper(substr($cliente['nome'],0,2)); ?></div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="carteirinha-body">
                        <div class="carteirinha-info">
                            <span class="info-label">CPF</span>
                            <span class="info-value"><?php echo $cliente['cpf']; ?></span>
                        </div>
                        <div class="carteirinha-info">
                            <span class="info-label">Data Nasc.</span>
                            <span
                                class="info-value"><?= date('d/m/Y', strtotime($cliente['data_nascimento'] ?? '')) ?></span>
                        </div>
                        <div class="carteirinha-info">
                            <span class="info-label">Plano</span>
                            <span class="info-value"><?php echo $cliente['plano']; ?></span>
                        </div>
                        <div class="carteirinha-info">
                            <span class="info-label">Validade</span>
                            <span class="info-value"><?php echo $cliente['validade']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Informações Pessoais -->
                <div class="info-card">
                    <div class="card-header">
                        <span>👤</span>
                        <h3>Dados Pessoais</h3>
                    </div>
                    <div style="display: grid; gap: 12px;">
                        <div>
                            <strong>Telefone:</strong> <?php echo $cliente['telefone']; ?>
                        </div>
                        <div>
                            <strong>E-mail:</strong> <?php echo $cliente['email']; ?>
                        </div>
                        <div>
                            <strong>Status:</strong>
                            <span class="status-badge status-ativo"><?php echo $cliente['status']; ?></span>
                        </div>
                    </div>
                </div>

                <!-- Endereço -->
                <div class="info-card">
                    <div class="card-header">
                        <span>📍</span>
                        <h3>Endereço</h3>
                    </div>
                    <div style="display: grid; gap: 12px;">
                        <div>
                            <strong>CEP:</strong> <?php echo $cliente['cep']; ?>
                        </div>
                        <div>
                            <strong>Cidade:</strong><br>
                            <?php echo $cliente['cidade']; ?>
                        </div>
                        <div>
                            <strong>Bairro:</strong><br>
                            <?php echo $cliente['bairro']; ?>
                        </div>
                        <div>
                            <strong>Rua:</strong><br>
                            <?php echo $cliente['rua']; ?>
                        </div>
                        <div>
                            <strong>Estado:</strong><br>
                            <?php echo $cliente['estado']; ?>
                        </div>
                    </div>
                </div>


                <?php 
                
                $usuario_id = $_SESSION['id'] ?? null;
                    try {
                    $pdo_login = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8", "root", "");
                    $pdo_login->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
                } catch (PDOException $e) {
                    die("❌ Erro ao conectar com PDO no login: " . $e->getMessage());
                }


                    if (!$usuario_id) {
                        $proximaConsulta = 'Nenhuma';
                    } else {
                        try {
                            $stmt = $pdo_login->prepare("
                                SELECT 
                                    h.data,
                                    h.hora,
                                    d.nome_completo AS dentista
                                FROM horarios h
                                LEFT JOIN dentistas d ON d.id = h.dentista_id
                                WHERE h.usuario_id = :usuario_id
                                AND h.status = 'ocupado'
                                AND h.data >= CURDATE()
                                ORDER BY h.data ASC, h.hora ASC
                                LIMIT 1
                            ");
                            $stmt->execute(['usuario_id' => $usuario_id]);
                            $consulta = $stmt->fetch(PDO::FETCH_ASSOC);

                            if ($consulta) {
                                $data = date('d/m/Y', strtotime($consulta['data']));
                                $hora = date('H:i', strtotime($consulta['hora']));
                                $dentista = htmlspecialchars($consulta['dentista'] ?? 'Dr(a). -');
                                $proximaConsulta = "{$data} às {$hora} com {$dentista}";
                            } else {
                                $proximaConsulta = 'Nenhuma';
                            }
                        } catch (PDOException $e) {
                            $proximaConsulta = 'Erro';
                        }
                    }
?>

                <!-- Próximas Consultas -->
                <div class="info-card proximas-consultas">
                    <div class="card-header">
                        <div class="icon-circle">
                            <span>📅</span>
                        </div>
                        <h3>Próximas Consultas</h3>
                    </div>

                    <div class="consulta-content">
                        <?php if (!empty($consulta)) : ?>
                        <p class="consulta-detalhe">
                            Consulta com <strong>Dr(a). <?= htmlspecialchars($dentista) ?></strong> <br> às
                            <?= htmlspecialchars($hora) ?>h
                        </p>
                        <p class="consulta-data">
                            <?= htmlspecialchars($data) ?>
                        </p>
                        <?php else : ?>
                        <p class="consulta-info">Nenhuma consulta agendada</p>
                        <?php endif; ?>
                        <br>
                        <a href="../agendamento/agenda.php" class="action-btn">
                            <span>➕</span> Agendar Consulta
                        </a>
                    </div>
                </div>


                <?php
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
                            LIMIT 1
                        ");

                        $stmt->execute(['usuario_id' => $_SESSION['id']]);
                        $consulta = $stmt->fetch(PDO::FETCH_ASSOC);

                        if ($consulta) {
                            $data = date('d/m/Y', strtotime($consulta['data_consulta']));
                            $procedimento = htmlspecialchars($consulta['procedimento'] ?? '—');
                            $dentista = htmlspecialchars($consulta['dentista_nome'] ?? '—');
                            $obs = htmlspecialchars($consulta['observacoes'] ?? '—');
                        } else {
                            $data = $procedimento = $dentista = $obs = 'Nenhum atendimento encontrado';
                        }

                    } catch (PDOException $e) {
                        $data = $procedimento = $dentista = $obs = 'Erro ao buscar dados';
                    }
                    ?>



                <!-- Último Atendimento -->
                <div class="info-card">
                    <div class="card-header">
                        <div class="icon-circle">
                            <span>🦷</span>
                        </div>
                        <h3>Último Atendimento</h3>
                    </div>

                    <div class="consulta-content">
                        <div class="atendimento-info">
                            <p><strong>Data:</strong> <?= $data ?></p>
                            <p><strong>Procedimento:</strong> <?= $procedimento ?></p>
                            <p><strong>Profissional:</strong> <?= $dentista ?></p>
                            <p><strong>Observações:</strong> <?= $obs ?></p>
                        </div>
                    </div>
                </div>

            </div>

            <!-- Botões de Ação Rápida -->
            <div class="action-buttons">
                <a href="historico.php" class="action-btn">
                    <span>📋</span> Ver Histórico Completo
                </a>
                <a href="#" class="action-btn">
                    <span>💳</span> Baixar Carteirinha
                </a>
                <a href="mailto:associacaoasbi@gmail.com" class="action-btn">
                    <span>✉️</span> Entrar em Contato
                </a>
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