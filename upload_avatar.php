<?php
session_start();

// Verifica se usuário logado
if (!isset($_SESSION['id'])) {
    http_response_code(403);
    echo "Usuário não autenticado.";
    exit;
}

$userId = intval($_SESSION['id']);

// Pasta de destino (caminho do sistema de arquivos)
$uploadDir = __DIR__ . '/img/avatars';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

if (!isset($_FILES['avatar']) || $_FILES['avatar']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['avatar_error'] = 'Nenhum arquivo enviado ou erro no upload.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}

$file = $_FILES['avatar'];

// Validações simples
$allowedTypes = ['image/png' => 'png', 'image/jpeg' => 'jpg', 'image/jpg' => 'jpg', 'image/gif' => 'gif'];
if (!array_key_exists($file['type'], $allowedTypes)) {
    $_SESSION['avatar_error'] = 'Tipo de arquivo não permitido. Use PNG, JPG ou GIF.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}

if ($file['size'] > 2 * 1024 * 1024) { // 2MB
    $_SESSION['avatar_error'] = 'Arquivo muito grande. Máx 2MB.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}

$ext = $allowedTypes[$file['type']];
$targetName = $userId . '.' . $ext;
$targetPath = $uploadDir . DIRECTORY_SEPARATOR . $targetName;

// Remove arquivos antigos do usuário (com outras extensões)
$files = glob($uploadDir . DIRECTORY_SEPARATOR . $userId . '.*');
foreach ($files as $f) {
    if (is_file($f)) @unlink($f);
}

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    $_SESSION['avatar_error'] = 'Falha ao mover o arquivo.';
    header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
    exit;
}

// Opcional: ajustar permissões
@chmod($targetPath, 0644);

$_SESSION['avatar_success'] = 'Foto de perfil atualizada com sucesso.';
header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
exit;

?>
