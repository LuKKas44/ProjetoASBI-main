<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include __DIR__ . '/../header.php';

$pre = $_SESSION['pre_2fa'] ?? null;
if (!$pre) {
    header('Location: login.php');
    exit();
}

// debug logger (temporary)
function rs2_log($msg) {
    $f = __DIR__ . '/../scripts/2fa_debug.log';
    file_put_contents($f, date('[Y-m-d H:i:s] ') . $msg . "\n", FILE_APPEND);
}
rs2_log("PRE_SESSION=" . json_encode($pre));

$feedback = ['message'=>'','type'=>''];

try {
    $pdo_login = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8", "root", "");
    $pdo_login->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("❌ Erro ao conectar com PDO no login: " . $e->getMessage());
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pin = trim($_POST['pin'] ?? '');
    if (!preg_match('/^\d{6}$/', $pin)) {
        $feedback = ['message' => '❌ PIN inválido. Use 6 dígitos numéricos.', 'type' => 'error'];
    } else {
        $id = (int)$pre['id'];
        $tipo = $pre['tipo'];
        $table = ($tipo === 'medico') ? 'dentistas' : 'usuario';

        if ($tipo === 'medico') {
            // tabela dentistas possui 'nome_completo' (não há coluna 'nome')
            $selectCols = "twofa_pin,
                COALESCE(nome_completo, '') AS nome_completo,
                COALESCE(cro, '') AS cro,
                COALESCE(cpf, '') AS cpf,
                COALESCE(especialidade, '') AS especialidade,
                COALESCE(telefone, '') AS telefone,
                COALESCE(email, '') AS email";
        } else {
            $selectCols = "twofa_pin,
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
            $stmt = $pdo_login->prepare("SELECT {$selectCols} FROM {$table} WHERE id = ?");
            $stmt->execute([$id]);
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            rs2_log("SELECT row for id={$id} table={$table} => " . json_encode($row));
        } catch (PDOException $e) {
            rs2_log("SELECT ERROR: " . $e->getMessage());
            $row = false;
        }

        if (!$row) {
            rs2_log("NO ROW FOUND for id={$id} table={$table}");
            $feedback = ['message' => '❌ PIN 2FA não configurado para esta conta.', 'type' => 'error'];
        } elseif (empty($row['twofa_pin'])) {
            rs2_log("ROW FOUND but twofa_pin empty for id={$id}");
            $feedback = ['message' => '❌ PIN 2FA não configurado para esta conta.', 'type' => 'error'];
        } else {
            // check expiry if present
            if (!empty($row['twofa_pin_expire'])) {
                $exp = new DateTime($row['twofa_pin_expire']);
                $now = new DateTime();
                if ($now > $exp) {
                    rs2_log("twofa_pin expired for id={$id} expire={$row['twofa_pin_expire']}");
                    $feedback = ['message' => '❌ PIN expirado. Solicite novo PIN por e‑mail.', 'type' => 'error'];
                    // clear expired pin server-side
                    try { $u = $pdo_login->prepare("UPDATE {$table} SET twofa_pin = NULL, twofa_pin_expire = NULL WHERE id = ?"); $u->execute([$id]); } catch (Exception $e) {}
                }
            }
            if (password_verify($pin, $row['twofa_pin'])) {
                // completar sessão igual ao set_2fa
                session_regenerate_id(true);
                $_SESSION['id'] = $id;
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

                // remove pre_2fa
                unset($_SESSION['pre_2fa']);

                // atualiza ultimo_login (não bloqueante)
                try {
                    $uStmt = $pdo_login->prepare("UPDATE {$table} SET ultimo_login = NOW() WHERE id = ?");
                    $uStmt->execute([$id]);
                } catch (Exception $ex) {}

                // clear twofa_pin (one-time) and expiry
                try {
                    $c = $pdo_login->prepare("UPDATE {$table} SET twofa_pin = NULL, twofa_pin_expire = NULL WHERE id = ?");
                    $c->execute([$id]);
                } catch (Exception $ex) {}

                // redireciona
                if ($tipo === 'medico') {
                    header('Location: ../painel_medico/painel_medico.php');
                } else {
                    header('Location: ../painel_cliente/painel_cliente.php');
                }
                exit();
            } else {
                $feedback = ['message' => '❌ PIN incorreto.', 'type' => 'error'];
            }
        }
    }
}
?>

<div class="form-container">
    <h2>Verificação 2FA</h2>
    <p>Insira o PIN de 6 dígitos configurado para sua conta.</p>

    <?php if (!empty($feedback['message'])): ?>
    <div class="feedback-message <?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
    <?php endif; ?>

    <form method="POST" action="2fa.php">
        <label for="pin">PIN</label>
        <input type="password" id="pin" name="pin" pattern="\d{6}" maxlength="6" required>
        <div style="display:flex; gap:10px; align-items:center; margin-top:12px;">
            <button type="submit">Verificar</button>
        </div>
    </form>

    <script>
    document.getElementById('sendPinBtn').addEventListener('click', function() {
        fetch('send_pin_email.php', {
                method: 'POST',
                credentials: 'same-origin'
            })
            .then(r => r.json())
            .then(j => {
                alert(j.message || (j.success ? 'PIN enviado' : 'Falha'));
            }).catch(err => {
                alert('Erro ao solicitar PIN: ' + err.message);
            });
    });
    </script>

    <p style="margin-top:12px;"><a href="login.php">Voltar ao login</a></p>
</div>

<?php include __DIR__ . '/../footer.php'; ?>