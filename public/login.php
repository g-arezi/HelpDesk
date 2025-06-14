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
    if ($_SESSION['role'] === 'cliente') {
        header('Location: buscarchamados_page.php');
    } else {
        header('Location: dashboard.php');
    }
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
    'admin' => ['senha' => 'admin321', 'email' => 'admin@sistema.com'],
    'tecnico' => ['senha' => 'tecnico321', 'email' => 'tecnico@sistema.com'],
];

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = $_POST['login'] ?? '';
    $senha = $_POST['senha'] ?? '';
      // Verificar usuários administradores/técnicos
    if (isset($USERS[$login]) && $USERS[$login]['senha'] === $senha) {
        $_SESSION['auth'] = true;
        $_SESSION['user'] = $login;
        // Define o papel do usuário
        if ($login === 'tecnico') {
            $_SESSION['role'] = 'tecnico';
        } else {
            $_SESSION['role'] = 'admin';
        }
        header('Location: dashboard.php');
        exit;
    } else {
    // Verificar usuários clientes
        $users_file = __DIR__ . '/../logs/user_registrations.txt';
          if (file_exists($users_file)) {
            $registrations = json_decode(file_get_contents($users_file), true) ?: [];
            
            foreach ($registrations as $user) {
                // Verifica se o login corresponde ao nome de usuário OU ao email
                if (($user['username'] === $login || $user['email'] === $login) && $user['status'] === 'approved') {
                    // Verificar senha
                    if (password_verify($senha, $user['password'])) {
                        $_SESSION['auth'] = true;
                        $_SESSION['user'] = $user['username'];  // Sempre usa o username para a sessão
                        $_SESSION['role'] = 'cliente';
                        $_SESSION['user_data'] = $user;
                        
                        header('Location: buscarchamados_page.php');
                        exit;
                    }
                }
            }
        }
        
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
    <style>
        body {
            background: #f4f6fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            position: relative;
        }
        body::before {
            content: '';
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            z-index: 0;
            background: url('assets/bg-login.png') no-repeat center center fixed;
            background-size: cover;
            opacity: 1;
            pointer-events: none;
            transition: background 0.3s;
        }
        body.night::before {
            /* Remove overlay escuro, imagem clara no modo noturno */
            background: url('assets/bg-login.png') no-repeat center center fixed;
            background-size: cover;
        }
        .container {
            background: #fff;
            max-width: 350px;
            margin: 0 auto;
            padding: 38px 32px 30px 32px;
            border-radius: 18px;
            box-shadow: 0 8px 32px #0002;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }        body.night .container {
            background: #232a36 !important;
            color: #e0e0e0;
            box-shadow: 0 8px 32px #0006;
        }
        h2 { color: #000000; margin-bottom: 18px; text-align: center; font-size: 2em; }
        body.night h2 {
            color: #ffffff !important;
        }
        label { color: #000000; font-weight: 500; margin-bottom: 8px; display: block; }
        body.night label {
            color: #ffffff !important;
        }
        input[type="text"], input[type="password"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1.5px solid #cfd8dc;
            margin-bottom: 18px;
            font-size: 1rem;
            background: #fafdff;
            color: #222;
            box-sizing: border-box;
            transition: border 0.2s, background 0.3s;
        }
        body.night input[type="text"],
        body.night input[type="password"] {
            background: #232837 !important;
            color: #e0e0e0 !important;
            border: 1.5px solid #333 !important;
        }
        input[type="text"]:focus, input[type="password"]:focus {
            border: 1.5px solid #1976d2;
            background: #e3f0ff;
            outline: none;
        }
        body.night input[type="text"]:focus,
        body.night input[type="password"]:focus {
            border: 1.5px solid #90caf9 !important;
            background: #232a36 !important;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #1976d2 60%, #63a4ff 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px #1976d210;
            margin-top: 8px;
            transition: background 0.2s;
        }
        body.night .btn {
            background: #b71c1c !important;
            color: #fff !important;
        }
        body.night .btn:hover {
            background: #ff6b6b !important;
            color: #fff !important;
        }
        .btn:hover { background: #125ea7; }
        /* Night/Light mode switcher - canto inferior esquerdo */
        .mode-switch {
            position: fixed;
            left: 18px;
            bottom: 18px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 8px;
            background: #232a36;
            border-radius: 18px;
            padding: 6px 14px 6px 10px;
            box-shadow: 0 2px 12px #0003;
            color: #fff;
            font-size: 1.05rem;
            font-weight: 500;
            border: 1px solid #232a36;
            transition: background 0.3s, color 0.3s;
        }
        .mode-switch.light {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #b3c6e0;
        }
        .mode-switch input[type="checkbox"] {
            width: 36px;
            height: 20px;
            appearance: none;
            background: #bdbdbd;
            outline: none;
            border-radius: 12px;
            position: relative;
            transition: background 0.3s;
            cursor: pointer;
        }
        .mode-switch input[type="checkbox"]:checked {
            background: #1976d2;
        }
        .mode-switch input[type="checkbox"]::before {
            content: '';
            position: absolute;
            left: 3px;
            top: 3px;
            width: 14px;
            height: 14px;
            background: #fff;
            border-radius: 50%;
            transition: left 0.3s;
        }
        .mode-switch input[type="checkbox"]:checked::before {
            left: 19px;
        }
        .mode-switch .icon {
            font-size: 1.1em;
        }
        .night-toggle { position:fixed; top:18px; right:18px; z-index:1000; background:linear-gradient(90deg,#ff6b6b,#b71c1c); color:#fff; border:1px solid #b71c1c; border-radius:20px; padding:8px 18px; cursor:pointer; font-weight:bold; box-shadow:0 2px 12px #0003; font-size: 1.1rem; transition: background 0.3s, color 0.3s; }
        .night-toggle.night { background:linear-gradient(90deg,#b71c1c,#ff6b6b); color:#fff; border-color:#fff; }        body.night { background: #181c24 !important; color: #e0e0e0; }
        body.night a { color: #ffffff !important; }
        @media (max-width: 500px) {
            .container { max-width: 98vw; padding: 18px 4vw; }
        }
    </style>
</head>
<body>
    <!-- Switch de modo claro/noturno -->
    <div class="mode-switch light" id="modeSwitch">
        <span class="icon" id="modeIcon">🌞</span>
        <input type="checkbox" id="modeToggle" aria-label="Alternar modo claro/noturno">
        <span id="modeLabel">Claro</span>
    </div>    <div class="container" id="loginContainer">
        <h2 id="loginTitle">🔐 Login - Plataforma de VODs</h2>
        <?php if ($error): ?>
            <div style="color:red; margin-bottom: 15px; text-align: center;"> <?= htmlspecialchars($error) ?> </div>
        <?php endif; ?>
        <form method="post">
            <label id="labelLogin">👤 Usuário ou Email:<br><input type="text" name="login" id="loginInput" required></label><br><br>
            <label id="labelSenha">🔒 Senha:<br><input type="password" name="senha" id="senhaInput" required></label><br><br>            <button type="submit" class="btn" id="btnEntrar">Entrar</button>
        </form>        <div style="margin-top: 20px; text-align: center;">
            <a href="register.php" style="color: #000000; text-decoration: none; font-weight: 500;">Não tem uma conta? Cadastre-se</a>
            <br><br>
            <a href="forgot_password.php" style="color: #000000; text-decoration: none; font-weight: 500;">Esqueceu sua senha?</a>
        </div>
    </div>
    <script>
    // Novo switch de modo
    const modeSwitch = document.getElementById('modeSwitch');
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    const modeLabel = document.getElementById('modeLabel');
    function setMode(night) {
        document.body.classList.toggle('night', night);
        modeSwitch.classList.toggle('light', !night);
        modeSwitch.classList.toggle('night', night);
        modeToggle.checked = night;
        if(night) {
            modeIcon.textContent = '🌙';
            modeLabel.textContent = 'Noturno';
            localStorage.setItem('nightMode','1');
        } else {
            modeIcon.textContent = '🌞';
            modeLabel.textContent = 'Claro';
            localStorage.removeItem('nightMode');
        }
    }
    modeToggle.addEventListener('change', function() {
        setMode(this.checked);
    });
    // Inicialização
    if(localStorage.getItem('nightMode')) setMode(true);
    else setMode(false);
    </script>
</body>
</html>
