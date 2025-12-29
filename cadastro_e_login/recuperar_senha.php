<?php
 if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['path' => '/']);
    session_start();
}

include(__DIR__ . "/../header.php");
include(__DIR__ . "/../conexao.php");

// Ensure a PDO connection is available for this script
try {
    $pdo = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro ao conectar ao banco: " . $e->getMessage());
}

$feedback = ['message'=>'','type'=>''];
$etapa = $_POST['etapa'] ?? '1';

// debug log helper (temporary)
function rs_log($msg) {
    $f = __DIR__ . '/../scripts/recuperar_debug.log';
    file_put_contents($f, date('[Y-m-d H:i:s] ') . $msg . "\n", FILE_APPEND);
}
rs_log("REQ_START ip=" . ($_SERVER['REMOTE_ADDR'] ?? 'cli') . " method=" . ($_SERVER['REQUEST_METHOD'] ?? '') . " POST=" . json_encode($_POST));

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    if ($etapa == '1') {
        $email = trim($_POST['email'] ?? '');
        $pin = trim($_POST['pin'] ?? '');

        if ($email === '' || $pin === '') {
            $feedback = ['message'=>'❌ Preencha email e PIN.', 'type'=>'error'];
            $etapa = '1';
            rs_log("STEP1: missing email or pin");
        } else {
            try {
                // busca por email e verifica PIN em PHP (suporta hash com password_verify)
                function findAndVerifyPin($pdo, $table, $email, $pin) {
                    // tente selecionar colunas comuns; inclui duas possíveis colunas de PIN
                    $stmt = $pdo->prepare("SELECT * FROM {$table} WHERE email = ? LIMIT 1");
                    $stmt->execute([$email]);
                    $row = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (!$row) return null;

                    // possível nomes de coluna: twofa_pin (preferido) ou pin
                    $hashCandidates = [];
                    if (!empty($row['twofa_pin'])) $hashCandidates[] = $row['twofa_pin'];
                    if (!empty($row['pin'])) $hashCandidates[] = $row['pin'];

                    foreach ($hashCandidates as $stored) {
                        // se parece ser um hash do password_hash (começa com $2y$ / $2b$ / $argon2...), usar password_verify
                        if (preg_match('/^\$2[aby]\$|^\$argon2/i', $stored)) {
                            if (password_verify($pin, $stored)) return $row;
                        } else {
                            // fallback: comparação direta (compatibilidade com texto plano)
                            if (hash_equals((string)$stored, (string)$pin)) return $row;
                        }
                    }
                    return null;
                }

                $usuario = findAndVerifyPin($pdo, 'dentistas', $email, $pin);
                $tipo = 'medico';
                rs_log("STEP1: searched dentistas email={$email} => " . json_encode($usuario));

                if (!$usuario) {
                    $usuario = findAndVerifyPin($pdo, 'usuario', $email, $pin);
                    $tipo = 'cliente';
                    rs_log("STEP1: searched usuario email={$email} => " . json_encode($usuario));
                }

                if ($usuario) {
                    // usuário encontrado: permitir alteração de senha
                    $_SESSION['recupera_id'] = $usuario['id'];
                    $_SESSION['recupera_tipo'] = $tipo;
                    // opcional: armazenar email para exibir e confirmar
                    $_SESSION['recupera_email'] = $email;
                    $feedback = ['message'=>'✅ Usuário verificado. Defina sua nova senha.', 'type'=>'success'];
                    $etapa = '2';
                    rs_log("STEP1: found user id=" . $usuario['id'] . " tipo=" . $tipo);
                } else {
                    $feedback = ['message'=>'❌ Email ou PIN incorretos.', 'type'=>'error'];
                    $etapa = '1';
                    rs_log("STEP1: not found: email={$email} pin={$pin}");
                }

            } catch (PDOException $e) {
                $feedback = ['message'=>"❌ Erro na consulta: ".$e->getMessage(), 'type'=>'error'];
                $etapa = '1';
                rs_log("STEP1: exception: " . $e->getMessage());
            }
        }

    } elseif ($etapa == '2') {
        rs_log("STEP2: POST nova_senha present?=" . (!empty($_POST['nova_senha']) ? '1':'0') . " session_recu_id=" . ($_SESSION['recupera_id'] ?? 'null') . " session_tipo=" . ($_SESSION['recupera_tipo'] ?? 'null'));
        $nova_senha = $_POST['nova_senha'] ?? '';
        $confirma_senha = $_POST['confirma_senha'] ?? '';

        if (strlen($nova_senha) < 6) {
            $feedback = ['message'=>'❌ A senha deve ter pelo menos 6 caracteres.', 'type'=>'error'];
            $etapa = '2';
        } elseif ($nova_senha !== $confirma_senha) {
            $feedback = ['message'=>'❌ As senhas não coincidem.', 'type'=>'error'];
            $etapa = '2';
        } else {
            $id_usuario = $_SESSION['recupera_id'] ?? null;
            $tipo = $_SESSION['recupera_tipo'] ?? null;

            if ($id_usuario && $tipo) {
                $nova_senha_hash = password_hash($nova_senha, PASSWORD_DEFAULT);

                $table = ($tipo == 'medico') ? 'dentistas' : 'usuario';
                try {
                    $stmt_update = $pdo->prepare("UPDATE {$table} SET senha = ? WHERE id = ?");
                    $stmt_update->execute([$nova_senha_hash, $id_usuario]);

                    // limpar sessão de recuperação
                    unset($_SESSION['recupera_id']);
                    unset($_SESSION['recupera_tipo']);
                    unset($_SESSION['recupera_email']);

                    $feedback = ['message'=>'✅ Senha alterada com sucesso! Faça login.', 'type'=>'success'];
                    $etapa = '3';
                    rs_log("STEP2: password updated for id={$id_usuario} table={$table}");
                } catch (PDOException $e) {
                    $feedback = ['message'=>'❌ Erro ao atualizar senha: '.$e->getMessage(), 'type'=>'error'];
                    $etapa = '2';
                    rs_log("STEP2: update exception: " . $e->getMessage());
                }

            } else {
                $feedback = ['message'=>'❌ Sessão inválida. Inicie novamente o processo.', 'type'=>'error'];
                $etapa = '1';
                rs_log("STEP2: invalid session info");
            }
        }
    }
}
?>

 <div class="form-container">
     <h2>Recuperar Senha</h2>

     <?php if (!empty($feedback['message'])): ?>
     <div class="feedback-message <?php echo $feedback['type']; ?>">
         <?php echo htmlspecialchars($feedback['message'], ENT_QUOTES, 'UTF-8'); ?>
     </div>
     <?php endif; ?>

     <?php if ($etapa == '1'): ?>
     <form action="recuperar_senha.php" method="POST">
         <input type="hidden" name="etapa" value="1">

         <label for="email">E‑mail cadastrado:</label>
         <input type="email" id="email" name="email" required>

         <label for="pin">PIN cadastrado:</label>
         <input type="text" id="pin" name="pin" required pattern="[0-9A-Za-z\-]{3,}">

         <button type="submit">Continuar</button>
     </form>

     <?php elseif ($etapa == '2'): ?>
     <p>Alterando senha para: <strong><?php echo htmlspecialchars($_SESSION['recupera_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></p>
     <form action="recuperar_senha.php" method="POST">
         <input type="hidden" name="etapa" value="2">

         <label for="nova_senha">Nova Senha (mín. 6 caracteres):</label>
         <input type="password" id="nova_senha" name="nova_senha" required minlength="6">

         <label for="confirma_senha">Confirmar Senha:</label>
         <input type="password" id="confirma_senha" name="confirma_senha" required>

         <button type="submit">Alterar Senha</button>
     </form>

     <?php else: ?>
     <p><a href="login.php">Voltar para Login</a></p>
     <?php endif; ?>
 </div>

 <?php include(__DIR__ . "/../footer.php");?>