<?php
// Include the client authentication check
session_start();

// Check if user is authenticated
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    // Redirect to login page with a message
    header('Location: login.php?access=denied&redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Buscar Chamados</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/mobile.css">
    <style>
        body { font-family: Arial, sans-serif; background: #f4f8fb; margin:0; transition: background 0.3s, color 0.3s; }
        .container { max-width: 700px; margin: 60px auto 30px auto; background: #fff; border-radius: 14px; box-shadow: 0 4px 24px #0002; padding: 36px 40px 30px 40px; }
        h2 { color: #111 !important; text-align: center; }
        body.night h2 { color: #fff !important; text-align: center; }
        body.night h1 { color: #fff !important; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow:0 1px 8px #e0e0e0; }        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background-color: #f2f2f2; color: #000000; font-weight: bold; }
        tr:hover { background-color: #f5f5f5; }
        .btn { padding: 8px 16px; background-color: #0078d7; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; box-shadow:0 1px 4px #0001; transition: background 0.2s; }
        .btn:hover { background-color: #0056a3; }
        /* Night/Light mode switcher - padrão igual ao restante do sistema */
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
        }        .mode-switch.light {
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
        .night-toggle { position:fixed; bottom:24px; left:24px; right:auto; top:auto; z-index:1000; background:linear-gradient(90deg,#ff6b6b,#b71c1c); color:#fff; border:1px solid #b71c1c; border-radius:20px; padding:8px 18px; cursor:pointer; font-weight:bold; box-shadow:0 2px 8px #0002; transition: background 0.3s, color 0.3s; }
        .night-toggle.night { background:linear-gradient(90deg,#b71c1c,#ff6b6b); color:#fff; border-color:#fff; }        body.night { background: #181c24; color: #ffffff; }
        .container.night { background: #232a36; color: #ffffff; }
        table.night { background: #232a36; color: #ffffff; }
        th.night { background: #263238; color: #ffffff; }
        tr.night:hover { background-color: #222b38; }
        .btn.night { background: #1976d2; color: #fff; }
        .btn.night:hover { background: #1565c0; }
        .ticket-list { max-width: 700px; margin: 30px auto; }
        .ticket { border: 1px solid #ccc; border-radius: 8px; padding: 16px; margin-bottom: 16px; background: #fafafa; }
        .chat-messages { height: 200px; overflow-y: auto; border: 1px solid #eee; padding: 10px; background: #fff; margin-bottom: 10px; }
        .chat-message { margin-bottom: 8px; }
        .chat-message .author { font-weight: bold; }
        .chat-message .timestamp { color: #888; font-size: 0.85em; margin-left: 8px; }
        .chat-form { display: flex; gap: 8px; }
        .chat-form input, .chat-form button { padding: 8px; }
        .chat-form input { flex: 1; }
        .chat-area { margin-top: 10px; }
        .btn-chat {
            background: linear-gradient(90deg, #43a047 60%, #66bb6a 100%) !important;
            color: #fff !important;
            border: none !important;
        }
        .btn-chat:hover {
            background: #388e3c !important;
            color: #fff !important;
        }        .user-info {
            text-align: center;
            margin-bottom: 20px;
            font-size: 0.9rem;
            color: #000000;
        }
        body.night .user-info {
            color: #ffffff;
        }
        .nav-buttons {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin-top: 25px;
        }
        .nav-buttons .btn {
            min-width: 120px;
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
        <h2>🔎 Buscar Chamados</h2>
          <div class="user-info">
            Olá, <strong><?php echo htmlspecialchars($_SESSION['user']); ?></strong>! 
            <?php if($_SESSION['role'] === 'cliente'): ?>
            (Cliente)
            <?php endif; ?>
            <br>
            <p style="margin-top: 5px;">Seus chamados abertos serão exibidos abaixo.</p>
        </div>
        
        <form id="searchForm" style="margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <?php if(isset($_SESSION['user_data'])): ?>
            <!-- Para clientes autenticados, usamos os dados da sessão -->
            <input type="hidden" id="email" value="<?php echo htmlspecialchars($_SESSION['user_data']['email'] ?? ''); ?>">
            <input type="hidden" id="telefone" value="<?php echo htmlspecialchars($_SESSION['user_data']['telefone'] ?? ''); ?>">
            <button type="submit" class="btn" style="width:100%">Mostrar Meus Chamados</button>
            <?php else: ?>
            <!-- Formulário padrão para busca -->
            <input type="email" id="email" placeholder="📧 Seu e-mail" style="flex:1;min-width:180px;" class="form-field">
            <input type="text" id="telefone" placeholder="📱 Seu telefone" style="flex:1;min-width:180px;" class="form-field">
            <button type="submit" class="btn">Buscar</button>
            <?php endif; ?>
        </form>
        <div id="result"></div>
        
        <div class="nav-buttons">
            <a href="open.php" class="btn">Abrir Novo Chamado</a>
            <a href="logout.php" class="btn">Sair</a>
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
        document.querySelectorAll('table').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('th').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('tr').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('.btn').forEach(e=>e.classList.toggle('night', night));
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
    <script>
    // Executar busca automaticamente para clientes autenticados
    window.addEventListener('load', function() {
        if (document.getElementById('email').type === 'hidden') {
            document.getElementById('searchForm').dispatchEvent(new Event('submit'));
        }
    });
    
    document.getElementById('searchForm').onsubmit = function(e) {
        e.preventDefault();
        const email = document.getElementById('email').value.trim();
        const telefone = document.getElementById('telefone').value.trim();
        fetch('buscarchamados.php?email='+encodeURIComponent(email)+'&telefone='+encodeURIComponent(telefone))
            .then(r=>r.json())
            .then(data=>{
                let html = '';
                if(data && Array.isArray(data.tickets) && data.tickets.length) {                    html += '<table><thead><tr><th>🆔 ID</th><th>📋 Status</th><th>📝 Assunto</th><th>� Produto</th><th>� Mensagem</th><th></th></tr></thead><tbody>';
                    data.tickets.forEach(row=>{
                        // Passa email e telefone na URL para o chat
                        html += `<tr><td>${row.id}</td><td>${row.status}</td><td>${row.subject}</td><td>${row.produto}</td><td>${row.message}</td><td><a href="chat_frontend.html?id=${row.id}&email=${encodeURIComponent(email)}&telefone=${encodeURIComponent(telefone)}" class="btn btn-chat" target="_blank">Chat</a></td></tr>`;
                    });
                    html += '</tbody></table>';
                } else {
                    html = '<p style="color:#d32f2f;">Nenhum chamado encontrado para os dados informados.</p>';
                }
                document.getElementById('result').innerHTML = html;
                if(document.body.classList.contains('night')) toggleNightMode(true);
            });
    };
    </script>
</body>
</html>
