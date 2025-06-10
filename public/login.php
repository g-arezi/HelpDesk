<?php
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

require_once __DIR__ . '/api_cors.php';

session_start();

// Verifica se o usuário já está autenticado
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    header('Location: tickets.php');
    exit;
}
// Define o cabeçalho de resposta HTTP
header('Content-Type: text/html; charset=utf-8');
// Define o fuso horário padrão
date_default_timezone_set('America/Sao_Paulo');
// Define o título da página
$title = 'Login - HelpDesk';
// Define o caminho para o diretório raiz do projeto
define('ROOT_DIR', dirname(__DIR__));
// Array de usuários e senhas personalizáveis
$USERS = [
    'admin' => 'admin321', //podendo ser utilizado API ou banco de dados para autenticação
    'tecnico' => 'tecnico321', // senha para o usuário tecnico
];

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';
    if (isset($USERS[$login]) && $USERS[$login] === $senha) {
        $_SESSION['auth'] = true;
        $_SESSION['user'] = $login;
        // Define o papel do usuário
        if ($login === 'tecnico') {
            $_SESSION['role'] = 'tecnico';
        } else {
            $_SESSION['role'] = 'admin';
        }
        header('Location: tickets.php');
        exit;
    } else {
        $error = 'Login ou senha inválidos!';
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h2>Login</h2>
        <?php if ($error): ?>
            <div style="color:red;"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>
        <form method="post">
            <label>Usuário:<br><input type="text" name="login" required></label><br><br>
            <label>Senha:<br><input type="password" name="senha" required></label><br><br>
            <button type="submit" class="btn">Entrar</button>
        </form>
    </div>
</body>
</html>
