<?php
$host = "000.0.0.0:3316";    // porta 3316 necessária
$usuario = "nomedeUsuario";       
$senha = "senha";            
$banco = "clinica_1";      
$conn = new mysqli($host, $usuario, $senha, $banco);

// necessário ser em conn
if ($conn->connect_error) {
    die("❌ Erro ao conectar: " . $conn->connect_error);
}


$conn->set_charset("utf8");
?>
