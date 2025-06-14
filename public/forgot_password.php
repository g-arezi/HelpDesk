<?php
require_once __DIR__ . '/api_cors.php';
session_start();

// Função auxiliar para gerar URLs seguras
function getSecureUrl($path) {
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://';
    return $protocol . $_SERVER['HTTP_HOST'] . $path;
}

// Se o usuário já estiver logado, redirecione
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    if ($_SESSION['role'] === 'cliente') {
        header('Location: buscarchamados_page.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$message = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    
    if (empty($username) || empty($email)) {
        $error = "Por favor, informe seu nome de usuário e email.";
    } else {
        // Verificar se o usuário existe (para clientes)
        $users_file = __DIR__ . '/../logs/user_registrations.txt';
        $token_file = __DIR__ . '/../logs/password_reset_tokens.txt';
        $user_found = false;
        
        if (file_exists($users_file)) {
            $registrations = json_decode(file_get_contents($users_file), true) ?: [];
            
            foreach ($registrations as $idx => $user) {
                if ($user['username'] === $username && $user['email'] === $email && $user['status'] === 'approved') {
                    // Usuário encontrado e aprovado, gerar token
                    $user_found = true;
                    
                    // Gerar token único
                    $token = bin2hex(random_bytes(32));
                    $expiry = time() + (60 * 60); // Token válido por 1 hora
                    
                    // Carregar tokens existentes
                    $tokens = [];
                    if (file_exists($token_file)) {
                        $tokens = json_decode(file_get_contents($token_file), true) ?: [];
                    }
                    
                    // Adicionar novo token
                    $tokens[] = [
                        'username' => $username,
                        'email' => $email, // Armazenar email no token para verificação adicional
                        'token' => $token,
                        'expiry' => $expiry,
                        'used' => false
                    ];
                    
                    // Salvar tokens
                    file_put_contents($token_file, json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    
                    // Exibir mensagem com o link (em produção, enviaria por email)
                    $reset_link = "http://{$_SERVER['HTTP_HOST']}/reset_password.php?token=$token";
                    $message = "Um link para redefinição de senha foi criado. Em um sistema real, este link seria enviado por email.<br><br>
                                <a href='$reset_link' style='color:#000;'>$reset_link</a>";
                    break;
                }
            }
        }
          // Verificar usuários administradores/técnicos
        $USERS = [
            'admin' => ['senha' => 'admin321', 'email' => 'admin@sistema.com'],
            'tecnico' => ['senha' => 'tecnico321', 'email' => 'tecnico@sistema.com'],
        ];
        
        if (!$user_found && array_key_exists($username, $USERS) && $USERS[$username]['email'] === $email) {
            $user_found = true;
            // Usuários admin/tecnico também podem redefinir senha
            // Gerar token único
            $token = bin2hex(random_bytes(32));
            $expiry = time() + (60 * 60); // Token válido por 1 hora
            
            // Carregar tokens existentes
            $tokens = [];
            if (file_exists($token_file)) {
                $tokens = json_decode(file_get_contents($token_file), true) ?: [];
            }
            
            // Adicionar novo token
            $tokens[] = [
                'username' => $username,
                'email' => $email, // Armazenar email no token para verificação adicional
                'token' => $token,
                'expiry' => $expiry,
                'used' => false,
                'is_admin' => true
            ];
            
            // Salvar tokens
            file_put_contents($token_file, json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Exibir mensagem com o link (em produção, enviaria por email)
            $reset_link = "http://{$_SERVER['HTTP_HOST']}/reset_password.php?token=$token";
            $message = "Um link para redefinição de senha foi criado. Em um sistema real, este link seria enviado por email.<br><br>
                        <a href='$reset_link' style='color:#000;'>$reset_link</a>";
        }
          if (!$user_found) {
            // Por segurança, não informamos se o usuário existe ou não
            $message = "Se o usuário e email informados forem válidos, um link para redefinição de senha será enviado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Esqueci Minha Senha - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/mobile.css">
    <style>
        body {
            background: #f4f6fb;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0;
            position: relative;
            font-family: 'Segoe UI', Arial, sans-serif;
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
        .container {
            background: #fff;
            max-width: 400px;
            margin: 0 auto;
            padding: 38px 32px 30px 32px;
            border-radius: 18px;
            box-shadow: 0 8px 32px #0002;
            display: flex;
            flex-direction: column;
            position: relative;
            z-index: 1;
            width: 100%;
        }
        body.night .container {
            background: #232a36 !important;
            color: #ffffff;
            box-shadow: 0 8px 32px #0006;
        }
        h2 { 
            color: #000000; 
            margin-bottom: 24px; 
            text-align: center; 
            font-size: 1.8em; 
        }
        body.night h2 {
            color: #ffffff !important;
        }
        label { 
            color: #000000; 
            font-weight: 500; 
            margin-bottom: 6px; 
            display: block; 
        }
        body.night label {
            color: #ffffff !important;
        }
        input[type="text"] {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1.5px solid #cfd8dc;
            margin-bottom: 14px;
            font-size: 1rem;
            background: #fafdff;
            color: #222;
            box-sizing: border-box;
            transition: border 0.2s, background 0.3s;
        }
        body.night input[type="text"] {
            background: #232837 !important;
            color: #ffffff !important;
            border: 1.5px solid #333 !important;
        }
        input[type="text"]:focus {
            border: 1.5px solid #000000;
            background: #f0f0f0;
            outline: none;
        }
        body.night input[type="text"]:focus {
            border: 1.5px solid #ffffff !important;
            background: #232a36 !important;
        }
        .btn {
            width: 100%;
            padding: 12px;
            background: linear-gradient(90deg, #000000 60%, #555555 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            font-size: 1.1rem;
            font-weight: bold;
            cursor: pointer;
            box-shadow: 0 2px 8px #00000010;
            margin-top: 10px;
            transition: background 0.2s;
        }
        body.night .btn {
            background: #333333 !important;
            color: #fff !important;
        }
        body.night .btn:hover {
            background: #555555 !important;
            color: #fff !important;
        }
        .btn:hover { background: #333333; }
        .error-message {
            color: #d32f2f;
            margin-bottom: 15px;
            font-weight: 500;
            text-align: center;
        }
        .success-message {
            color: #388e3c;
            margin-bottom: 15px;
            font-weight: 500;
            text-align: center;
            padding: 10px;
            background: #e8f5e9;
            border-radius: 8px;
            border: 1px solid #c8e6c9;
        }
        body.night .success-message {
            background: #1b3724 !important;
            color: #a5d6a7 !important;
            border: 1px solid #2e7d32 !important;
        }
        body.night .error-message {
            color: #f48fb1 !important;
        }
        .back-link {
            margin-top: 20px;
            text-align: center;
        }
        .back-link a {
            color: #000000;
            text-decoration: none;
        }
        body.night .back-link a {
            color: #ffffff !important;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
        /* Night/Light mode switcher */
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
            color: #000000;
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
            background: #000000;
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
    </style>
</head>
<body>
    <!-- Switch de modo claro/noturno -->
    <div class="mode-switch light" id="modeSwitch">
        <span class="icon" id="modeIcon">🌞</span>
        <input type="checkbox" id="modeToggle" aria-label="Alternar modo claro/noturno">
        <span id="modeLabel">Claro</span>
    </div>
    
    <div class="container" id="container">
        <h2>🔑 Esqueci Minha Senha</h2>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($message): ?>
            <div class="success-message"><?php echo $message; ?></div>
        <?php else: ?>            <form method="post" action="">
                <label for="username">Nome de Usuário</label>
                <input type="text" id="username" name="username" placeholder="Digite seu nome de usuário" required>
                
                <label for="email">Email</label>
                <input type="email" id="email" name="email" placeholder="Digite seu email" required>
                
                <button type="submit" class="btn">Solicitar Redefinição de Senha</button>
            </form>
        <?php endif; ?>
        
        <div class="back-link">
            <a href="login.php">Voltar para o Login</a>
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
        document.getElementById('container').classList.toggle('night', night);
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
