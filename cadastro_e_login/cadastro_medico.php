<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include(__DIR__ . "/../header.php");
include(__DIR__ . "/../conexao.php");


$feedback = ['message'=>'','type'=>''];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = trim($_POST['nome']);
    $nome_mae = trim($_POST['nome_mae']);
    $cpf = trim($_POST['cpf']);
    $telefone = trim($_POST['telefone']);
    $email = trim($_POST['email']);
    $cep = trim($_POST['cep']);
    $rua = trim($_POST['rua']);
    $numero = trim($_POST['numero']);
    $bairro = trim($_POST['bairro']);
    $cidade = trim($_POST['cidade']);
    $estado = trim($_POST['estado']);
    $senha = $_POST['senha'];
    $confirma_senha = $_POST['confirma_senha'];
    $cro = trim($_POST['cro']);
    $data_nascimento = $_POST['data_nascimento'];
    $nome_clinica = trim($_POST['nome_clinica']);
    $cep_clinica = trim($_POST['cep_clinica']);
    $rua_clinica = trim($_POST['rua_clinica']);
    $numero_clinica = trim($_POST['numero_clinica']);
    $bairro_clinica = trim($_POST['bairro_clinica']);
    $especialidade = trim($_POST['especialidade']);

    try {
        $pdo_login = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8", "root", "");
        $pdo_login->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    } catch (PDOException $e) {
        die("❌ Erro ao conectar com PDO no login: " . $e->getMessage());
    }

    // Validações
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $feedback = ['message'=>"❌ E‑mail em formato inválido.", 'type'=>'error'];
    } elseif (strlen($senha) < 6) {
        $feedback = ['message'=>"❌ A senha deve ter pelo menos 6 caracteres.", 'type'=>'error'];
    } elseif ($senha !== $confirma_senha) {
        $feedback = ['message'=>"❌ As senhas não coincidem.", 'type'=>'error'];
    } else {
        $senha_hash = password_hash($senha, PASSWORD_DEFAULT);

        try {
            $stmt = $pdo_login->prepare(
              "INSERT INTO dentistas (nome_completo, nome_mae, cpf, telefone, email, cep, rua, numero, bairro, cidade, estado, senha, cro, data_nascimento, nome_clinica, cep_clinica, rua_clinica, bairro_clinica, numero_clinica, especialidade) 
              VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)"
            );

            $stmt->execute([$nome, $nome_mae, $cpf, $telefone, $email, $cep, $rua, $numero, $bairro, $cidade, $estado, $senha_hash, $cro, $data_nascimento, $nome_clinica, $cep_clinica, $rua_clinica, $bairro_clinica, $numero_clinica, $especialidade]);

            // pega id do novo dentista e redireciona para configurar 2FA
            $newId = $pdo_login->lastInsertId();
            $_SESSION['pending_2fa'] = [
                'id' => (int)$newId,
                'tipo' => 'medico',
                'nome' => $nome
            ];

            header("Location: set_2fa.php");
            exit();
        } catch (PDOException $e) {
            if (!empty($e->errorInfo[1]) && $e->errorInfo[1] == 1062) {
                $feedback = ['message'=>"❌ Erro: O CPF, E‑mail ou CRO informado já está cadastrado.", 'type'=>'error'];
            } else {
                $feedback = ['message'=>"❌ Erro ao cadastrar: " . $e->getMessage(), 'type'=>'error'];
            }
        }
    }
}
?>

<div class="form-container">
    <h2>Cadastro de Dentista</h2>
    <?php if (!empty($feedback['message'])): ?>
    <div class="feedback-message <?php echo $feedback['type']; ?>"><?php echo $feedback['message']; ?></div>
    <?php endif; ?>

    <form id="formMedico" method="POST" action="cadastro_medico.php">
        <h4>Dados Pessoais</h4>
        <label>Nome Completo:</label>
        <input type="text" name="nome" required>

        <label>Nome da Mãe:</label>
        <input type="text" name="nome_mae" required>

        <label>CPF:</label>
        <input type="text" name="cpf" id="cpf" required>

        <label>Telefone:</label>
        <input type="text" name="telefone" id="telefone" required>

        <label>Email:</label>
        <input type="email" name="email" required>

        <label>Senha (mín. 6 caracteres):</label>
        <input type="password" name="senha" id="senha" required minlength="6">

        <label>Confirmar Senha:</label>
        <input type="password" name="confirma_senha" id="confirma_senha" required>

        <h4>Dados Profissionais</h4>
        <label>CRO:</label>
        <input type="text" name="cro" required>

        <label>Especialidade:</label>
        <select name="especialidade" required>
            <option value="">Selecione a especialidade...</option>
            <option value="Clínica Geral">Clínico</option>
            <option value="Pediatria">Pediatria</option>
        </select>

        <h4>Endereço:</h4>
        <label>CEP:</label>
        <input type="text" name="cep" id="cep">

        <label>Rua:</label>
        <input type="text" name="rua" id="rua">

        <label>Número:</label>
        <input type="text" name="numero">

        <label>Bairro:</label>
        <input type="text" name="bairro" id="bairro">

        <label>Cidade:</label>
        <input type="text" name="cidade" id="cidade">

        <label>Estado:</label>
        <input type="text" name="estado" id="estado">

        <label>Data de Nascimento:</label>
        <input type="date" name="data_nascimento" required>

        <h4>Local de Atendimento:</h4>
        <label>Nome da Cliníca:</label>
        <input type="text" name="nome_clinica" id="nome_clinica">

        <label>CEP:</label>
        <input type="text" name="cep_clinica" id="cep_clinica">

        <label>Rua:</label>
        <input type="text" name="rua_clinica" id="rua_clinica">

        <label>Número:</label>
        <input type="text" name="numero_clinica">

        <label>Bairro:</label>
        <input type="text" name="bairro_clinica" id="bairro_clinica">

        <button type="submit" id="btnCadastrar">Cadastrar</button>
    </form>
</div>

<?php include(__DIR__ . "/../footer.php");?>

<script>
function onlyDigits(str) {
    return str.replace(/\D/g, '');
}

function maskCPF(value) {
    const v = onlyDigits(value).slice(0, 11);
    let out = v;
    if (v.length > 9) out = v.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
    else if (v.length > 6) out = v.replace(/^(\d{3})(\d{3})(\d{1,3}).*/, '$1.$2.$3');
    else if (v.length > 3) out = v.replace(/^(\d{3})(\d{1,3}).*/, '$1.$2');
    return out;
}

function maskCEP(value) {
    const v = onlyDigits(value).slice(0, 8);
    if (v.length > 5) return v.replace(/^(\d{5})(\d{1,3}).*/, '$1-$2');
    return v;
}

function validaCPF(cpf) {
    cpf = onlyDigits(cpf);
    if (cpf.length !== 11) return false;
    if (/^(\d)\1+$/.test(cpf)) return false;

    let sum = 0;
    for (let i = 0; i < 9; i++) sum += parseInt(cpf.charAt(i)) * (10 - i);
    let rev = 11 - (sum % 11);
    if (rev === 10 || rev === 11) rev = 0;
    if (rev !== parseInt(cpf.charAt(9))) return false;

    sum = 0;
    for (let i = 0; i < 10; i++) sum += parseInt(cpf.charAt(i)) * (11 - i);
    rev = 11 - (sum % 11);
    if (rev === 10 || rev === 11) rev = 0;
    if (rev !== parseInt(cpf.charAt(10))) return false;

    return true;
}

document.getElementById('telefone').addEventListener('input', function(e) {
    let v = e.target.value.replace(/\D/g, '');

    if (v.length > 11) v = v.slice(0, 11);


    if (v.length >= 1) v = "(" + v;
    if (v.length > 2) v = v.slice(0, 3) + ") " + v.slice(3);
    if (v.length > 7) v = v.slice(0, 10) + "-" + v.slice(10);

    e.target.value = v;
});

function validaCEP(cep) {
    const digits = onlyDigits(cep);
    return /^\d{8}$/.test(digits);
}

function validaCRO(cro) {
    const c = cro.trim();
    return /^[A-Za-z0-9-]{4,12}$/.test(c);
}

document.addEventListener('DOMContentLoaded', function() {
    const cpfInput = document.querySelector('#cpf');
    const cepInput = document.querySelector('#cep');
    const cep_clinicaInput = document.querySelector('#cep_clinica');
    const croInput = document.querySelector('input[name="cro"]');
    const form = document.querySelector('#formMedico');

    const ruaInput = document.querySelector('#rua');
    const bairroInput = document.querySelector('#bairro');
    const cidadeInput = document.querySelector('#cidade');
    const estadoInput = document.querySelector('#estado');
    const rua_clinicaInput = document.querySelector('#rua_clinica');
    const bairro_clinicaInput = document.querySelector('#bairro_clinica');

    if (cpfInput) {
        cpfInput.addEventListener('input', function() {
            this.value = maskCPF(this.value);
            this.setSelectionRange(this.value.length, this.value.length);
        });
    }

    if (cepInput) {
        cepInput.addEventListener('input', function() {
            this.value = maskCEP(this.value);
            this.setSelectionRange(this.value.length, this.value.length);
        });

        cepInput.addEventListener('blur', function() {
            const cep = onlyDigits(this.value);
            if (cep.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            if (ruaInput) ruaInput.value = data.logradouro || '';
                            if (bairroInput) bairroInput.value = data.bairro || '';
                            if (cidadeInput) cidadeInput.value = data.localidade || '';
                            if (estadoInput) estadoInput.value = data.uf || '';
                        } else {
                            alert('❌ CEP não encontrado.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('❌ Erro ao buscar CEP.');
                    });
            }
        });
    }



    if (cep_clinicaInput) {
        cep_clinicaInput.addEventListener('input', function() {
            this.value = maskCEP(this.value);
            this.setSelectionRange(this.value.length, this.value.length);
        });

        cep_clinicaInput.addEventListener('blur', function() {
            const cep_clinica = onlyDigits(this.value);
            if (cep_clinica.length === 8) {
                fetch(`https://viacep.com.br/ws/${cep_clinica}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!data.erro) {
                            if (rua_clinicaInput) rua_clinicaInput.value = data.logradouro || '';
                            if (bairro_clinicaInput) bairro_clinicaInput.value = data.bairro || '';
                        } else {
                            alert('❌ CEP não encontrado.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('❌ Erro ao buscar CEP.');
                    });
            }
        });
    }

    if (form) {
        form.addEventListener('submit', function(e) {
            const cpfVal = cpfInput ? cpfInput.value : '';
            const cepVal = cepInput ? cepInput.value : '';
            const croVal = croInput ? croInput.value : '';

            if (!validaCPF(cpfVal)) {
                e.preventDefault();
                alert('❌ CPF inválido.');
                if (cpfInput) cpfInput.focus();
                return false;
            }

            if (!validaCEP(cepVal)) {
                e.preventDefault();
                alert('❌ CEP inválido.');
                if (cepInput) cepInput.focus();
                return false;
            }

            if (!validaCRO(croVal)) {
                e.preventDefault();
                alert('❌ CRO inválido.');
                if (croInput) croInput.focus();
                return false;
            }
        });
    }
});
</script>