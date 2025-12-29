<?php
if (session_status() === PHP_SESSION_NONE) {
    // Define o cookie da sessão para todo o site
    session_set_cookie_params(['path' => '/']);
    session_start();
}

include(__DIR__ . "/../conexao.php");
include(__DIR__ . "/../header.php");

$base = "/projeto_ASBI-main";

$feedback = ['message'=>'','type'=>''];
if (isset($_SESSION['id'])) {
    if (($_SESSION['tipo'] ?? '') === 'medico') {
        $dashboard_link = "$base/painel_medico/painel_medico.php";
    } elseif (($_SESSION['tipo'] ?? '') === 'admin') {
        $dashboard_link = "$base/admin/index.php";
    } else {
        $dashboard_link = "$base/painel_cliente/painel_cliente.php";
    }
    header("Location: " . $dashboard_link);
    exit();
}

try {
    $pdo_login = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8", "root", "");
    $pdo_login->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erro ao conectar com PDO no login: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $identificador = trim($_POST['identificador']);
    $senha = $_POST['senha'];
    $usuario = null;
    $tipo = null;

    try {
        // Admin: busca por e-mail na tabela admins
        $stmt_admin = $pdo_login->prepare("SELECT * FROM admins WHERE email = ?");
        $stmt_admin->execute([$identificador]);
        $usuario = $stmt_admin->fetch(PDO::FETCH_ASSOC);
        if ($usuario) {
            $tipo = 'admin';
        } else {
            // Médico: busca por CRO ou e-mail (aceita login por e-mail também)
            $stmt_medico = $pdo_login->prepare("SELECT * FROM dentistas WHERE cro = ? OR email = ?");
            $stmt_medico->execute([$identificador, $identificador]);
            $usuario = $stmt_medico->fetch(PDO::FETCH_ASSOC);
            if ($usuario) {
                $tipo = 'medico';
            } else {
                // Cliente: CPF ou e-mail
                $stmt_cliente = $pdo_login->prepare("SELECT * FROM usuario WHERE cpf = ? OR email = ?");
                $stmt_cliente->execute([$identificador, $identificador]);
                $usuario = $stmt_cliente->fetch(PDO::FETCH_ASSOC);
                if ($usuario) {
                    $tipo = 'cliente';
                }
            }
        }
    } catch (PDOException $e) {
        $feedback = ['message'=>"❌ Erro na consulta: " . $e->getMessage(), 'type'=>'error'];
    }

    if ($usuario && !$feedback['message']) {
        if (password_verify($senha, $usuario['senha'])) {
            // Se a conta já possui twofa_pin definido, iniciar fluxo 2FA
            if (isset($usuario['twofa_pin']) && !empty($usuario['twofa_pin'])) {
                $_SESSION['pre_2fa'] = [
                    'id' => (int)$usuario['id'],
                    'tipo' => $tipo,
                    'nome' => $usuario['nome_completo'] ?? $usuario['nome_responsavel'] ?? $usuario['nome_crianca'] ?? ($usuario['nome'] ?? '')
                ];
                header("Location: 2fa.php");
                exit();
            }

            // Sem 2FA: login direto
            session_regenerate_id(true);
            $_SESSION['id'] = $usuario['id'];
            $_SESSION['tipo'] = $tipo;

            // Se for admin, armazena nome/email específicos
            if ($tipo === 'admin') {
                $_SESSION['nome_admin'] = $usuario['nome'] ?? '';
                $_SESSION['email'] = $usuario['email'] ?? '';
            }

            if ($tipo == "medico") {
                $_SESSION['nome_completo'] = $usuario['nome_completo'];
                $_SESSION['cro'] = $usuario['cro'];
                $_SESSION['cpf'] = $usuario['cpf'];
                $_SESSION['telefone'] = $usuario['telefone'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['especialidade'] = $usuario['especialidade'];
            } else { // cliente
                $_SESSION['nome_crianca'] = $usuario['nome_crianca'];
                $_SESSION['cpf'] = $usuario['cpf'];
                $_SESSION['data_nascimento'] = $usuario['data_nascimento'];
                $_SESSION['telefone'] = $usuario['telefone'];
                $_SESSION['email'] = $usuario['email'];
                $_SESSION['cep'] = $usuario['cep'];
                $_SESSION['cidade'] = $usuario['cidade'];
                $_SESSION['bairro'] = $usuario['bairro'];
                $_SESSION['rua'] = $usuario['rua'];
                $_SESSION['estado'] = $usuario['estado'];
            }

            // Atualizar ultimo_login
            try {
                if ($tipo === 'admin') {
                    $stmt2 = $pdo_login->prepare("UPDATE admins SET ultimo_login = NOW() WHERE id = ?");
                    $stmt2->execute([$usuario['id']]);
                } elseif ($tipo === 'medico') {
                    $stmt2 = $pdo_login->prepare("UPDATE dentistas SET ultimo_login = NOW() WHERE id = ?");
                    $stmt2->execute([$usuario['id']]);
                } else {
                    $stmt2 = $pdo_login->prepare("UPDATE usuario SET ultimo_login = NOW() WHERE id = ?");
                    $stmt2->execute([$usuario['id']]);
                }
            } catch (Exception $ex) {}

            if ($tipo === 'admin') {
                $dashboard_link = "$base/admin/index.php";
            } elseif ($tipo === 'medico') {
                $dashboard_link = "$base/painel_medico/painel_medico.php";
            } else {
                $dashboard_link = "$base/painel_cliente/painel_cliente.php";
            }

            header("Location: " . $dashboard_link);
            exit();

        } else {
            $feedback = ['message'=>"❌ Senha incorreta.", 'type'=>'error'];
        }
    } elseif (!$feedback['message']) {
        $feedback = ['message'=>"❌ Usuário não encontrado.", 'type'=>'error'];
    }
} else {
    if (isset($_GET['status'])) {
        if ($_GET['status'] == 'registered') {
            $feedback = ['message'=>"✅ Cadastro realizado com sucesso! Faça o login.", 'type'=>'success'];
        }
        if ($_GET['status'] == 'pass_changed') {
            $feedback = ['message' => "✅ Senha alterada com sucesso! Faça o login.", 'type' => 'success'];
        }
    }
}
?>

<div class="form-container">
    <h2>Login</h2>

    <?php if (!empty($feedback['message'])): ?>
    <div class="feedback-message <?php echo $feedback['type']; ?>">
        <?php echo $feedback['message']; ?>
    </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
        <label for="identificador">CRO (Dentista), E‑mail ou CPF (Associado)</label>
        <input type="text" id="identificador" name="identificador" required>

        <label for="senha">Senha:</label>
        <input type="password" id="senha" name="senha" required>

        <button type="submit">Entrar</button>
    </form>

    <div class="form-links">
        <a href="recuperar_senha.php">Esqueci minha Senha</a>
        <hr style="margin: 20px 0;">
        <p>Ainda não tem conta?</p>
        <a href="cadastro_cliente.php" class="link-button">Cadastrar como Associado</a>
        <a href="cadastro_medico.php" class="link-button">Cadastrar como Voluntário/Dentista</a>
    </div>
</div>

<?php include(__DIR__ . '/../footer.php'); ?>