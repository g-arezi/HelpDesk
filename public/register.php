<?php
require_once __DIR__ . '/api_cors.php';
session_start();

// If user is already logged in, redirect to appropriate page
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    if ($_SESSION['role'] === 'cliente') {
        header('Location: buscarchamados.html');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

$error = '';
$success = '';

// Process registration form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {    // Get form data
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = trim($_POST['password'] ?? '');
    $confirm_password = trim($_POST['confirm_password'] ?? '');
    $server_name = trim($_POST['server_name'] ?? '');
    $panel_username = trim($_POST['panel_username'] ?? '');
    $last_recharge_date = trim($_POST['last_recharge_date'] ?? '');
    $credit_amount = trim($_POST['credit_amount'] ?? '');
    
    // Validate form data
    if (empty($username) || empty($email) || empty($password) || empty($confirm_password) || 
        empty($server_name) || empty($panel_username) || 
        empty($last_recharge_date) || empty($credit_amount)) {
        $error = "Todos os campos são obrigatórios!";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Por favor, informe um endereço de email válido!";
    } elseif ($password !== $confirm_password) {
        $error = "As senhas não coincidem!";
    } elseif (strlen($password) < 6) {
        $error = "A senha deve ter pelo menos 6 caracteres!";
    } else {
        // All validations passed, save the registration
        $users_file = __DIR__ . '/../logs/user_registrations.txt';
        
        // Load existing registrations
        $registrations = [];
        if (file_exists($users_file)) {
            $content = file_get_contents($users_file);
            $registrations = json_decode($content, true) ?: [];
        }
          // Check if username already exists
        foreach ($registrations as $reg) {
            if ($reg['username'] === $username) {
                $error = "Nome de usuário já existe!";
                break;
            }
            if ($reg['email'] === $email) {
                $error = "Este endereço de email já está em uso!";
                break;
            }
        }
        
        if (empty($error)) {            // Add new registration
            $registrations[] = [
                'username' => $username,
                'email' => $email,
                'password' => password_hash($password, PASSWORD_DEFAULT),
                'server_name' => $server_name,
                'panel_username' => $panel_username,
                'last_recharge_date' => $last_recharge_date,
                'credit_amount' => $credit_amount,
                'status' => 'pending', // pending, approved, rejected
                'created_at' => date('Y-m-d H:i:s')
            ];
            
            // Save registrations
            file_put_contents($users_file, json_encode($registrations, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            $success = "Cadastro realizado com sucesso! Aguarde a aprovação de um administrador.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cadastro - Plataforma de VODs</title>
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
            max-width: 450px;
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
            color: #e0e0e0;
            box-shadow: 0 8px 32px #0006;
        }        h2 { 
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
        input[type="text"], input[type="password"], input[type="date"] {
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
        body.night input[type="text"],
        body.night input[type="password"],
        body.night input[type="date"] {
            background: #232837 !important;
            color: #e0e0e0 !important;
            border: 1.5px solid #333 !important;
        }        input[type="text"]:focus, input[type="password"]:focus, input[type="date"]:focus {
            border: 1.5px solid #000000;
            background: #f0f0f0;
            outline: none;
        }
        body.night input[type="text"]:focus,
        body.night input[type="password"]:focus,
        body.night input[type="date"]:focus {
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
        }        body.night .btn {
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
        }
        .form-group {
            margin-bottom: 15px;
        }
        .login-link {
            text-align: center;
            margin-top: 20px;
            color: #1976d2;
        }
        body.night .login-link a {
            color: #90caf9 !important;
        }
        .login-link a {
            color: #1976d2;
            text-decoration: none;
            font-weight: 500;
        }
        .login-link a:hover {
            text-decoration: underline;
        }
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
        @media (max-width: 500px) {
            .container { max-width: 95vw; padding: 20px 15px; }
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
    
    <div class="container" id="registerContainer">
        <h2 id="registerTitle">📝 Cadastro - Plataforma de VODs</h2>
        
        <?php if ($error): ?>
            <div class="error-message"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success-message"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="post">            <div class="form-group">
                <label for="username">👤 Usuário:</label>
                <input type="text" id="username" name="username" required value="<?= htmlspecialchars($username ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="email">📧 Email:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($email ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="password">🔒 Senha:</label>
                <input type="password" id="password" name="password" required>
            </div>
              <div class="form-group">
                <label for="confirm_password">🔄 Confirmar Senha:</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            
            <h3 style="margin-top: 20px; color: #000000; font-size: 1.3em; text-align: center;">Dados para Validação</h3>
            
            <div class="form-group">
                <label for="server_name">🖥️ Nome do Servidor:</label>
                <input type="text" id="server_name" name="server_name" required value="<?= htmlspecialchars($server_name ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="panel_username">👑 Usuário do Painel:</label>
                <input type="text" id="panel_username" name="panel_username" required value="<?= htmlspecialchars($panel_username ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="last_recharge_date">📅 Data da Última Recarga:</label>
                <input type="date" id="last_recharge_date" name="last_recharge_date" required value="<?= htmlspecialchars($last_recharge_date ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="credit_amount">💰 Quantidade de Créditos Abastecido:</label>
                <input type="text" id="credit_amount" name="credit_amount" required value="<?= htmlspecialchars($credit_amount ?? '') ?>">
            </div>
            
            <button type="submit" class="btn">Cadastrar</button>
        </form>
        
        <div class="login-link">
            Já possui uma conta? <a href="login.php">Faça Login</a>
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
