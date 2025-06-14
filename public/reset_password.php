<?php
require_once __DIR__ . '/api_cors.php';
session_start();

// Se o usuário já estiver logado, redirecione
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    if ($_SESSION['role'] === 'cliente') {
        header('Location: buscarchamados_page.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$error = '';
$success = '';
$token = $_GET['token'] ?? '';
$valid_token = false;
$username = '';
$email = '';
$is_admin = false;

// Verificar se o token é válido
if ($token) {
    $token_file = __DIR__ . '/../logs/password_reset_tokens.txt';
    
    if (file_exists($token_file)) {
        $tokens = json_decode(file_get_contents($token_file), true) ?: [];
        
        foreach ($tokens as $idx => $token_data) {
            if ($token_data['token'] === $token && !$token_data['used'] && $token_data['expiry'] > time()) {
                // Token válido
                $valid_token = true;
                $username = $token_data['username'];
                $email = $token_data['email'] ?? ''; // O email está armazenado no token
                $is_admin = isset($token_data['is_admin']) && $token_data['is_admin'];
                break;
            }
        }
    }
    
    if (!$valid_token) {
        $error = "O token de redefinição é inválido ou expirou. Por favor, solicite uma nova redefinição de senha.";
    }
}

// Processar formulário de redefinição de senha
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $valid_token) {
    $new_password = trim($_POST['new_password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    
    // Validar senha
    if (empty($new_password) || empty($confirm_password)) {
        $error = "Todos os campos são obrigatórios!";
    } elseif ($new_password !== $confirm_password) {
        $error = "As senhas não coincidem!";
    } elseif (strlen($new_password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres!";
    } else {
        // Processar a atualização da senha
        if ($is_admin) {
            // Para administradores/técnicos, precisaria atualizar o arquivo de configuração
            // Como os usuários admin/tecnico estão fixos no código, mostramos uma mensagem
            $success = "Senha redefinida com sucesso! Como você é um usuário administrador/técnico, a senha real seria atualizada em um banco de dados ou arquivo de configuração.";
            
            // Marcar o token como usado
            $token_file = __DIR__ . '/../logs/password_reset_tokens.txt';
            $tokens = json_decode(file_get_contents($token_file), true) ?: [];
            
            foreach ($tokens as $idx => $token_data) {
                if ($token_data['token'] === $token) {
                    $tokens[$idx]['used'] = true;
                    break;
                }
            }
            
            file_put_contents($token_file, json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
        } else {
            // Atualizar senha do cliente
            $users_file = __DIR__ . '/../logs/user_registrations.txt';
            
            if (file_exists($users_file)) {
                $registrations = json_decode(file_get_contents($users_file), true) ?: [];
                $updated = false;
                
                foreach ($registrations as $idx => $user) {
                    if ($user['username'] === $username) {
                        // Atualizar senha
                        $registrations[$idx]['password'] = password_hash($new_password, PASSWORD_DEFAULT);
                        $updated = true;
                        break;
                    }
                }
                
                if ($updated) {
                    // Salvar usuários atualizados
                    file_put_contents($users_file, json_encode($registrations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    
                    // Marcar o token como usado
                    $token_file = __DIR__ . '/../logs/password_reset_tokens.txt';
                    $tokens = json_decode(file_get_contents($token_file), true) ?: [];
                    
                    foreach ($tokens as $idx => $token_data) {
                        if ($token_data['token'] === $token) {
                            $tokens[$idx]['used'] = true;
                            break;
                        }
                    }
                    
                    file_put_contents($token_file, json_encode($tokens, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
                    
                    $success = "Sua senha foi redefinida com sucesso! Agora você pode fazer login com sua nova senha.";
                } else {
                    $error = "Usuário não encontrado.";
                }
            } else {
                $error = "Erro ao processar a solicitação.";
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Redefinir Senha - HelpDesk</title>
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
        input[type="password"] {
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
        body.night input[type="password"] {
            background: #232837 !important;
            color: #ffffff !important;
            border: 1.5px solid #333 !important;
        }
        input[type="password"]:focus {
            border: 1.5px solid #000000;
            background: #f0f0f0;
            outline: none;
        }
        body.night input[type="password"]:focus {
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
        <h2>🔐 Redefinir Senha</h2>
        
        <?php if ($error): ?>
            <div class="error-message"><?php echo $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?php echo $success; ?></div>
            <div class="back-link">
                <a href="login.php">Voltar para o Login</a>
            </div>        <?php elseif ($valid_token): ?>
            <form method="post" action="">
                <p>Olá <strong><?php echo htmlspecialchars($username); ?></strong>, defina sua nova senha abaixo:</p>
                <p>Email associado: <strong><?php echo htmlspecialchars($email); ?></strong></p>
                
                <label for="new_password">Nova Senha</label>
                <input type="password" id="new_password" name="new_password" placeholder="Digite sua nova senha" required>
                
                <label for="confirm_password">Confirmar Senha</label>
                <input type="password" id="confirm_password" name="confirm_password" placeholder="Confirme sua nova senha" required>
                
                <button type="submit" class="btn">Redefinir Senha</button>
            </form>
        <?php else: ?>
            <div class="back-link">
                <a href="forgot_password.php">Solicitar uma nova redefinição de senha</a>
            </div>
        <?php endif; ?>
        
        <?php if (!$success): ?>
            <div class="back-link">
                <a href="login.php">Voltar para o Login</a>
            </div>
        <?php endif; ?>
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
