<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'cliente') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: editar_dados.php');
    exit;
}

try {
    $pdo = new PDO(
        "mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4",
        "root", "",
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $usuario_id = $_SESSION['id'];

    // Collect and sanitize inputs
    $fields = [
        'nome_crianca' => FILTER_SANITIZE_STRING,
        'data_nascimento' => FILTER_SANITIZE_STRING,
        'sexo' => FILTER_SANITIZE_STRING,
        'nome_responsavel' => FILTER_SANITIZE_STRING,
        'cpf' => FILTER_SANITIZE_STRING,
        'telefone' => FILTER_SANITIZE_STRING,
        'email' => FILTER_SANITIZE_EMAIL,
        'rua' => FILTER_SANITIZE_STRING,
        'bairro' => FILTER_SANITIZE_STRING,
        'cidade' => FILTER_SANITIZE_STRING,
        'estado' => FILTER_SANITIZE_STRING,
        'cep' => FILTER_SANITIZE_STRING
    ];

    $data = filter_input_array(INPUT_POST, $fields);

    // Basic validation
    if (empty($data['nome_crianca']) || empty($data['email'])) {
        $_SESSION['update_error'] = 'Nome da criança e e-mail são obrigatórios.';
        header('Location: editar_dados.php');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE usuario SET nome_crianca = :nome_crianca, data_nascimento = :data_nascimento, sexo = :sexo, nome_responsavel = :nome_responsavel, cpf = :cpf, telefone = :telefone, email = :email, rua = :rua, bairro = :bairro, cidade = :cidade, estado = :estado, cep = :cep WHERE id = :id");

    $stmt->execute([
        ':nome_crianca' => $data['nome_crianca'],
        ':data_nascimento' => $data['data_nascimento'] ?: null,
        ':sexo' => $data['sexo'],
        ':nome_responsavel' => $data['nome_responsavel'],
        ':cpf' => $data['cpf'],
        ':telefone' => $data['telefone'],
        ':email' => $data['email'],
        ':rua' => $data['rua'],
        ':bairro' => $data['bairro'],
        ':cidade' => $data['cidade'],
        ':estado' => $data['estado'],
        ':cep' => $data['cep'],
        ':id' => $usuario_id
    ]);

    $_SESSION['update_success'] = 'Dados atualizados com sucesso.';
    header('Location: editar_dados.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['update_error'] = 'Erro ao atualizar: ' . $e->getMessage();
    header('Location: editar_dados.php');
    exit;
}