<?php
// Include the client authentication check
require_once 'client_auth_check.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cliente - Buscar Chamados</title>
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f8fb; margin: 0; padding: 0; transition: background 0.3s, color 0.3s; }
        .container { max-width: 800px; margin: 40px auto; background: #fff; border-radius: 16px; box-shadow: 0 6px 32px #0002; padding: 30px; transition: background 0.3s, color 0.3s; }
        h2 { 
            color: #111 !important; /* preto no light */ 
            text-align: center; 
            margin-bottom: 30px; 
            font-size: 1.8rem; 
            transition: color 0.3s; 
        }
        body.night h2 { 
            color: #fff !important; /* branco no night */
        }
        label { 
            color: #111 !important; /* preto no light */
            font-weight: 500;
            margin-right: 8px;
            font-size: 1rem;
            transition: color 0.3s;
        }
        body.night label { 
            color: #fff !important; /* branco no night */
        }
        input[type="email"], input[type="text"] {
            padding: 10px;
            width: 180px;
            border-radius: 8px;
            border: 1.5px solid #cfd8dc;
            margin-right: 12px;
            margin-bottom: 6px;
            background: #fafdff;
            color: #222;
            font-size: 1rem;
            transition: border 0.2s, background 0.3s;
        }        input[type="email"]:focus, input[type="text"]:focus {
            border: 1.5px solid #000000;
            background: #f0f0f0;
            outline: none;
        }
        button {
            padding: 10px 20px;
            background: linear-gradient(90deg, #000000 60%, #555555 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1rem;
            box-shadow: 0 2px 8px #00000010;
            transition: background 0.2s;
        }
        button:hover { background: #333333; }
        .results {
            margin-top: 30px;
            border-top: 1px solid #e0e0e0;
            padding-top: 18px;
        }
        .ticket {
            margin-bottom: 20px;
            padding: 15px;
            background: #f9fbff;
            border-radius: 10px;
            border: 1px solid #e0e0e0;
            box-shadow: 0 1px 6px #0001;
            transition: background 0.3s;
        }        .ticket strong { color: #000000; font-weight: 600; }
        .ticket:last-child { margin-bottom: 0; }
        .btn-group { margin-top: 10px; display: flex; flex-wrap: wrap; gap: 10px; }
        .btn {
            padding: 8px 16px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 500;
            font-size: 0.9rem;
            text-decoration: none;
            display: inline-block;
            text-align: center;
            box-shadow: 0 1px 4px #0001;
            transition: background 0.2s, color 0.2s;
        }
        .btn-primary {
            background: #000000;
            color: #fff;
        }
        .btn-primary:hover { background: #333333; color: #fff; }
        .btn-success {
            background: #43a047;
            color: #fff;
        }
        .btn-success:hover { background: #2e7d32; color: #fff; }
        .btn-danger {
            background: #d32f2f;
            color: #fff;
        }
        .btn-danger:hover { background: #b71c1c; color: #fff; }
        .error {
            color: #d32f2f;
            margin-top: 20px;
            padding: 10px;
            background: #ffebee;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #ffcdd2;
        }        .loading {
            text-align: center;
            margin-top: 20px;
            color: #000000;
            font-weight: 500;
        }
        /* Night mode */
        body.night { background: #181c24 !important; }
        .container.night { background: #232a36 !important; color: #ffffff; box-shadow: 0 6px 32px #0006; }
        body.night input[type="email"],
        body.night input[type="text"] {
            background: #232837 !important;
            color: #ffffff !important;
            border: 1.5px solid #333 !important;
        }
        body.night input[type="email"]:focus,
        body.night input[type="text"]:focus {
            border: 1.5px solid #ffffff !important;
            background: #232a36 !important;
        }
        body.night .ticket { background: #232837 !important; color: #ffffff; border: 1px solid #333; }
        body.night .ticket strong { color: #ffffff !important; }
        body.night .error { background: #311b1b !important; color: #f48fb1; border: 1px solid #4a1b1b; }
        body.night .loading { color: #90caf9 !important; }
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
        }        /* Navbar */
        .navbar {
            background: #000000;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .navbar .logo {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .navbar .nav-links {
            display: flex;
            gap: 20px;
        }
        .navbar .nav-links a {
            color: white;
            text-decoration: none;
            font-weight: 500;
        }
        .navbar .nav-links a:hover {
            text-decoration: underline;
        }
        body.night .navbar {
            background: #232a36;
        }
        .welcome-box {
            background: #e3f2fd;
            padding: 15px;
            border-radius: 10px;
            margin-bottom: 25px;
            border: 1px solid #bbdefb;
        }
        body.night .welcome-box {
            background: #0d47a1;
            border-color: #1565c0;
        }
    </style>
</head>
<body>
    <!-- Navbar -->
    <div class="navbar">
        <div class="logo">Plataforma de VODs</div>
        <div class="nav-links">
            <a href="cliente_buscar.php">Buscar Chamados</a>
            <a href="cliente_open.php">Abrir Chamado</a>
            <a href="logout.php">Sair</a>
        </div>
    </div>

    <!-- Switch de modo claro/noturno -->
    <div class="mode-switch light" id="modeSwitch">
        <span class="icon" id="modeIcon">🌞</span>
        <input type="checkbox" id="modeToggle" aria-label="Alternar modo claro/noturno">
        <span id="modeLabel">Claro</span>
    </div>

    <div class="container" id="container">
        <h2 id="pageTitle">🔍 Buscar Chamados</h2>
        
        <div class="welcome-box">
            <p>Bem-vindo, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>! Aqui você pode visualizar e acompanhar seus chamados.</p>
        </div>
        
        <div>
            <label for="email" id="labelEmail">📧 E-mail:</label>
            <input type="email" id="email" placeholder="Seu e-mail" value="<?= htmlspecialchars($_SESSION['user_data']['email'] ?? '') ?>">
            
            <label for="telefone" id="labelTelefone">📱 Telefone:</label>
            <input type="text" id="telefone" placeholder="Seu telefone">
            
            <button id="btnBuscar" onclick="buscarChamados()">Buscar</button>
        </div>
        
        <div id="results" class="results" style="display:none;"></div>
        <div id="loading" class="loading" style="display:none;">Buscando chamados...</div>
        <div id="error" class="error" style="display:none;"></div>
    </div>

    <script>
        function buscarChamados() {
            const email = document.getElementById('email').value.trim();
            const telefone = document.getElementById('telefone').value.trim();
            
            if (!email && !telefone) {
                showError("Por favor, informe seu e-mail ou telefone.");
                return;
            }
            
            showLoading();
            
            const params = new URLSearchParams();
            if (email) params.append('email', email);
            if (telefone) params.append('telefone', telefone);
            
            fetch(`tickets_api.php?${params.toString()}`)
                .then(response => response.json())
                .then(data => {
                    hideLoading();
                    
                    if (data.error) {
                        showError(data.error);
                        return;
                    }
                    
                    if (!data.tickets || data.tickets.length === 0) {
                        showError("Nenhum chamado encontrado para os dados informados.");
                        return;
                    }
                    
                    renderTickets(data.tickets);
                })
                .catch(error => {
                    hideLoading();
                    showError("Erro ao buscar chamados. Por favor, tente novamente.");
                    console.error("Erro:", error);
                });
        }
        
        function renderTickets(tickets) {
            const resultsDiv = document.getElementById('results');
            resultsDiv.innerHTML = '';
            resultsDiv.style.display = 'block';
            
            document.getElementById('error').style.display = 'none';
            
            tickets.forEach((ticket, index) => {
                const ticketDiv = document.createElement('div');
                ticketDiv.className = 'ticket';
                
                let statusLabel = '';
                if (ticket.status === 'resolvido') {
                    statusLabel = '<span style="color:green;font-weight:bold;">✅ Resolvido</span>';
                } else if (ticket.status === 'em_analise') {
                    statusLabel = '<span style="color:orange;font-weight:bold;">⏳ Em análise</span>';
                } else {
                    statusLabel = '<span style="color:red;font-weight:bold;">🔒 Não aberto</span>';
                }
                
                ticketDiv.innerHTML = `
                    <p><strong>🆔 ID:</strong> ${index + 1}</p>
                    <p><strong>👤 Nome:</strong> ${ticket.name || '-'}</p>
                    <p><strong>📧 E-mail:</strong> ${ticket.email || '-'}</p>
                    <p><strong>📦 Produto:</strong> ${ticket.produto || '-'}</p>
                    <p><strong>📝 Assunto:</strong> ${ticket.subject || '-'}</p>
                    <p><strong>💬 Mensagem:</strong> ${ticket.message || '-'}</p>
                    <p><strong>📊 Status:</strong> ${statusLabel}</p>
                    <div class="btn-group">
                        <a href="chat_frontend.html?id=${index + 1}" class="btn btn-success" target="_blank">Chat</a>
                    </div>
                `;
                
                resultsDiv.appendChild(ticketDiv);
            });
        }
        
        function showError(message) {
            const errorDiv = document.getElementById('error');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            document.getElementById('results').style.display = 'none';
            document.getElementById('loading').style.display = 'none';
        }
        
        function showLoading() {
            document.getElementById('loading').style.display = 'block';
            document.getElementById('results').style.display = 'none';
            document.getElementById('error').style.display = 'none';
        }
        
        function hideLoading() {
            document.getElementById('loading').style.display = 'none';
        }
        
        // Night mode
        const modeSwitch = document.getElementById('modeSwitch');
        const modeToggle = document.getElementById('modeToggle');
        const modeIcon = document.getElementById('modeIcon');
        const modeLabel = document.getElementById('modeLabel');
        
        function setMode(night) {
            document.body.classList.toggle('night', night);
            document.getElementById('container').classList.toggle('night', night);
            document.getElementById('pageTitle').classList.toggle('night', night);
            document.getElementById('labelEmail').classList.toggle('night', night);
            document.getElementById('labelTelefone').classList.toggle('night', night);
            
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
        
        // Initialize
        if(localStorage.getItem('nightMode')) setMode(true);
        else setMode(false);
        
        // Auto search when page loads
        document.addEventListener('DOMContentLoaded', function() {
            buscarChamados();
        });
    </script>
</body>
</html>
