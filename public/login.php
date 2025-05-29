<?php
session_start();

// Array de usuários e senhas personalizáveis
$USERS = [
    'admin' => '1234',
    'joao' => 'senha123',
    'maria' => 'abc@2024'
];

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';
    if (isset($USERS[$login]) && $USERS[$login] === $senha) {
        $_SESSION['auth'] = true;
        $_SESSION['user'] = $login;
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
