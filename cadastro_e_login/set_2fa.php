<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../header.php';

// conexão PDO dedicada (mesma que os outros arquivos)
try {
    $pdo_login = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8", "root", "");
    $pdo_login->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erro ao conectar com PDO no login: " . $e->getMessage());
}

// Permite tanto o fluxo pós-cadastro (pending_2fa) quanto usuário já logado
$pending = $_SESSION['pending_2fa'] ?? null;
$logged = isset($_SESSION['id']) ? ['id' => $_SESSION['id'], 'tipo' => $_SESSION['tipo']] : null;
$context = $pending ?: $logged;

if (!$context) {
    header('Location: login.php');
    exit();
}

$feedback = ['message'=>'','type'=>''];

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $pin = trim($_POST['pin'] ?? '');
    $pin_conf = trim($_POST['pin_conf'] ?? '');

    if (!preg_match('/^\d{6}$/', $pin)) {
        $feedback = ['message' => '❌ O PIN deve ter exatamente 6 dígitos numéricos.', 'type' => 'error'];
    } elseif ($pin !== $pin_conf) {
        $feedback = ['message' => '❌ PINs não conferem.', 'type' => 'error'];
    } else {
        $hash = password_hash($pin, PASSWORD_DEFAULT);
        $id = (int)$context['id'];
        $tipo = $context['tipo'];
        $table = ($tipo === 'medico') ? 'dentistas' : 'usuario';

        try {
            $stmt = $pdo_login->prepare("UPDATE {$table} SET twofa_pin = ? WHERE id = ?");
            $stmt->execute([$hash, $id]);
            if ($stmt->rowCount() === 0) {
                $feedback = ['message' => '❌ Usuário não encontrado para salvar PIN.', 'type' => 'error'];
            }
        } catch (PDOException $e) {
            // tenta criar coluna se não existir e reexecutar
            try {
                $pdo_login->exec("ALTER TABLE {$table} ADD COLUMN IF NOT EXISTS twofa_pin VARCHAR(255) DEFAULT NULL");
                $stmt = $pdo_login->prepare("UPDATE {$table} SET twofa_pin = ? WHERE id = ?");
                $stmt->execute([$hash, $id]);
            } catch (PDOException $e2) {
                $feedback = ['message' => '❌ Erro ao salvar PIN 2FA: ' . $e2->getMessage(), 'type' => 'error'];
            }
        }

        if (!$feedback['message']) {
            // busca colunas necessárias para popular sessão conforme painel
            if ($tipo === 'medico') {
                // tabelas de dentistas usam 'nome_completo' (não existe coluna 'nome')
                $selectCols = "id,
                    COALESCE(nome_completo, '') AS nome_completo,
                    COALESCE(cro, '') AS cro,
                    COALESCE(cpf, '') AS cpf,
                    COALESCE(especialidade, '') AS especialidade,
                    COALESCE(telefone, '') AS telefone,
                    COALESCE(email, '') AS email";
                // we'll manually set 'nome' in session from nome_completo below
            } else {
                $selectCols = "id,
                    COALESCE(nome_responsavel, '') AS nome_responsavel,
                    COALESCE(nome_crianca, '') AS nome_crianca,
                    COALESCE(data_nascimento, '') AS data_nascimento,
                    COALESCE(cpf, '') AS cpf,
                    COALESCE(telefone, '') AS telefone,
                    COALESCE(email, '') AS email,
                    COALESCE(cep, '') AS cep,
                    COALESCE(rua, '') AS rua,
                    COALESCE(bairro, '') AS bairro,
                    COALESCE(cidade, '') AS cidade,
                    COALESCE(estado, '') AS estado";
            }

            try {
                $stmt2 = $pdo_login->prepare("SELECT {$selectCols} FROM {$table} WHERE id = ?");
                $stmt2->execute([$id]);
                $row = $stmt2->fetch(PDO::FETCH_ASSOC);
            } catch (PDOException $e) {
                $row = false;
            }

            if (!$row) {
                $feedback = ['message' => '❌ Falha ao buscar dados do usuário após salvar PIN.', 'type' => 'error'];
            } else {
                // limpa pending
                unset($_SESSION['pending_2fa']);

                // popula sessão completa usada pelos painéis
                session_regenerate_id(true);
                $_SESSION['id'] = (int)$row['id'];
                $_SESSION['tipo'] = $tipo;

                if ($tipo === 'medico') {
                    $_SESSION['nome_completo'] = $row['nome_completo'] ?? '';
                    $_SESSION['nome'] = $row['nome'] ?? $_SESSION['nome_completo'];
                    $_SESSION['cro'] = $row['cro'] ?? '';
                    $_SESSION['cpf'] = $row['cpf'] ?? '';
                    $_SESSION['especialidade'] = $row['especialidade'] ?? '';
                    $_SESSION['telefone'] = $row['telefone'] ?? '';
                    $_SESSION['email'] = $row['email'] ?? '';
                } else {
                    $_SESSION['nome_responsavel'] = $row['nome_responsavel'] ?? '';
                    $_SESSION['nome_crianca'] = $row['nome_crianca'] ?? '';
                    // para compatibilidade com código que usa 'nome' como child's name
                    $_SESSION['nome'] = $_SESSION['nome_crianca'];
                    $_SESSION['data_nascimento'] = $row['data_nascimento'] ?? '';
                    $_SESSION['cpf'] = $row['cpf'] ?? '';
                    $_SESSION['telefone'] = $row['telefone'] ?? '';
                    $_SESSION['email'] = $row['email'] ?? '';
                    $_SESSION['cep'] = $row['cep'] ?? '';
                    $_SESSION['rua'] = $row['rua'] ?? '';
                    $_SESSION['bairro'] = $row['bairro'] ?? '';
                    $_SESSION['cidade'] = $row['cidade'] ?? '';
                    $_SESSION['estado'] = $row['estado'] ?? '';
                }

                // atualiza ultimo_login (não bloqueante)
                try {
                    $uStmt = $pdo_login->prepare("UPDATE {$table} SET ultimo_login = NOW() WHERE id = ?");
                    $uStmt->execute([$id]);
                } catch (Exception $ex) {}

                // redireciona
                if ($tipo === 'medico') {
                    header('Location: ../painel_medico/painel_medico.php');
                } else {
                    header('Location: ../painel_cliente/painel_cliente.php');
                }
                exit();
            }
        }
    }
}
?>

<div class="form-container">
    <h2>Configurar PIN 2FA</h2>

    <?php if (!empty($feedback['message'])): ?>
        <div class="feedback-message <?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
    <?php endif; ?>

    <p>Escolha um PIN de 6 dígitos que será usado como segundo fator de autenticação.</p>

    <form method="POST" action="set_2fa.php">
        <label for="pin">PIN (6 dígitos)</label>
        <input type="password" id="pin" name="pin" pattern="\d{6}" maxlength="6" required>

        <label for="pin_conf">Confirme o PIN</label>
        <input type="password" id="pin_conf" name="pin_conf" pattern="\d{6}" maxlength="6" required>

        <button type="submit">Salvar PIN e Entrar</button>
    </form>

    <p style="margin-top:12px;"><a href="login.php">Voltar ao login</a></p>
</div>

<?php include __DIR__ . '/../footer.php'; ?>