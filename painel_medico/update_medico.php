<?php
session_start();

if (!isset($_SESSION['id']) || $_SESSION['tipo'] !== 'medico') {
    header('Location: ../cadastro_e_login/login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: editar_dados_medico.php');
    exit;
}

try {
    $pdo = new PDO("mysql:host=127.0.0.1:3316;dbname=clinica_1;charset=utf8mb4", "root", "");
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $medico_id = $_SESSION['id'];

    $fields = [
        'nome_completo' => FILTER_SANITIZE_STRING,
        'cro' => FILTER_SANITIZE_STRING,
        'cpf' => FILTER_SANITIZE_STRING,
        'email' => FILTER_SANITIZE_EMAIL,
        'telefone' => FILTER_SANITIZE_STRING,
        'especialidade' => FILTER_SANITIZE_STRING,
        'nome_clinica' => FILTER_SANITIZE_STRING,
        'rua_clinica' => FILTER_SANITIZE_STRING,
        'numero_clinica' => FILTER_SANITIZE_STRING,
        'bairro_clinica' => FILTER_SANITIZE_STRING,
        'cidade' => FILTER_SANITIZE_STRING,
        'estado' => FILTER_SANITIZE_STRING,
        'cep_clinica' => FILTER_SANITIZE_STRING
    ];

    $data = filter_input_array(INPUT_POST, $fields);

    if (empty($data['nome_completo']) || empty($data['email'])) {
        $_SESSION['update_error'] = 'Nome e e-mail são obrigatórios.';
        header('Location: editar_dados_medico.php');
        exit;
    }

    $stmt = $pdo->prepare("UPDATE dentistas SET nome_completo = :nome_completo, cro = :cro, cpf = :cpf, email = :email, telefone = :telefone, especialidade = :especialidade, nome_clinica = :nome_clinica, rua_clinica = :rua_clinica, numero_clinica = :numero_clinica, bairro_clinica = :bairro_clinica, cidade = :cidade, estado = :estado, cep_clinica = :cep_clinica WHERE id = :id");

    $stmt->execute([
        ':nome_completo' => $data['nome_completo'],
        ':cro' => $data['cro'],
        ':cpf' => $data['cpf'],
        ':email' => $data['email'],
        ':telefone' => $data['telefone'],
        ':especialidade' => $data['especialidade'],
        ':nome_clinica' => $data['nome_clinica'],
        ':rua_clinica' => $data['rua_clinica'],
        ':numero_clinica' => $data['numero_clinica'],
        ':bairro_clinica' => $data['bairro_clinica'],
        ':cidade' => $data['cidade'],
        ':estado' => $data['estado'],
        ':cep_clinica' => $data['cep_clinica'],
        ':id' => $medico_id
    ]);

    $_SESSION['update_success'] = 'Dados atualizados com sucesso.';
    header('Location: editar_dados_medico.php');
    exit;

} catch (PDOException $e) {
    $_SESSION['update_error'] = 'Erro ao atualizar: ' . $e->getMessage();
    header('Location: editar_dados_medico.php');
    exit;
}
