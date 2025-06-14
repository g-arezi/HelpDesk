<?php
// Include the client authentication check
require_once 'client_auth_check.php';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Cliente - Abrir Chamado</title>
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
            display: block;
            margin-bottom: 6px;
        }
        body.night label { 
            color: #fff !important; /* branco no night */
        }
        input[type="text"], input[type="email"], textarea, select {
            width: 100%;
            padding: 10px;
            border-radius: 8px;
            border: 1.5px solid #cfd8dc;
            margin-bottom: 18px;
            background: #fafdff;
            color: #222;
            font-size: 1rem;
            box-sizing: border-box;
            transition: border 0.2s, background 0.3s;
        }
        textarea {
            min-height: 120px;
            resize: vertical;
        }
        input[type="text"]:focus, input[type="email"]:focus, textarea:focus, select:focus {
            border: 1.5px solid #1976d2;
            background: #e3f0ff;
            outline: none;
        }
        .form-group {
            margin-bottom: 18px;
        }        button {
            padding: 12px 24px;
            background: linear-gradient(90deg, #000000 60%, #555555 100%);
            color: #fff;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px #00000010;
            transition: background 0.2s;
            display: block;
            width: 100%;
            margin-top: 10px;
        }
        button:hover { background: #333333; }
        .success-message {
            color: #388e3c;
            margin-top: 20px;
            padding: 15px;
            background: #e8f5e9;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #c8e6c9;
            font-weight: 500;
        }
        .error-message {
            color: #d32f2f;
            margin-top: 20px;
            padding: 15px;
            background: #ffebee;
            border-radius: 8px;
            text-align: center;
            border: 1px solid #ffcdd2;
            font-weight: 500;
        }
        /* Night mode */
        body.night { background: #181c24 !important; }
        .container.night { background: #232a36 !important; color: #e0e0e0; box-shadow: 0 6px 32px #0006; }
        body.night input[type="text"],
        body.night input[type="email"],
        body.night textarea,
        body.night select {
            background: #232837 !important;
            color: #e0e0e0 !important;
            border: 1.5px solid #333 !important;
        }
        body.night input[type="text"]:focus,
        body.night input[type="email"]:focus,
        body.night textarea:focus,
        body.night select:focus {
            border: 1.5px solid #90caf9 !important;
            background: #232a36 !important;
        }
        body.night .success-message { background: #1b3724 !important; color: #a5d6a7; border: 1px solid #2e7d32; }
        body.night .error-message { background: #311b1b !important; color: #f48fb1; border: 1px solid #4a1b1b; }
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
            background: #f0f0f0;
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
        <h2 id="pageTitle">📝 Abrir Novo Chamado</h2>
        
        <div class="welcome-box">
            <p>Bem-vindo, <strong><?= htmlspecialchars($_SESSION['user']) ?></strong>! Aqui você pode abrir um novo chamado de suporte.</p>
        </div>
        
        <div id="successMessage" class="success-message" style="display: none;"></div>
        <div id="errorMessage" class="error-message" style="display: none;"></div>
        
        <form id="ticketForm" onsubmit="return submitForm(event)">
            <div class="form-group">
                <label for="name">👤 Nome:</label>
                <input type="text" id="name" name="name" required value="<?= htmlspecialchars($_SESSION['user']) ?>">
            </div>
            
            <div class="form-group">
                <label for="email">📧 E-mail:</label>
                <input type="email" id="email" name="email" required value="<?= htmlspecialchars($_SESSION['user_data']['email'] ?? '') ?>">
            </div>
            
            <div class="form-group">
                <label for="telefone">📱 Telefone:</label>
                <input type="text" id="telefone" name="telefone">
            </div>
            
            <div class="form-group">
                <label for="produto">📦 Produto:</label>
                <select id="produto" name="produto" required>
                    <option value="">Selecione um produto</option>
                    <option value="filmes">Filmes</option>
                    <option value="series">Séries</option>
                    <option value="iptv">IPTV</option>
                    <option value="conta">Conta</option>
                    <option value="outro">Outro</option>
                </select>
            </div>
            
            <div id="productSpecificFields"></div>
            
            <div class="form-group">
                <label for="subject">📝 Assunto:</label>
                <input type="text" id="subject" name="subject" required>
            </div>
            
            <div class="form-group">
                <label for="message">💬 Mensagem:</label>
                <textarea id="message" name="message" required></textarea>
            </div>
            
            <button type="submit">Enviar Chamado</button>
        </form>
    </div>

    <script>
        // Dynamic fields based on product selection
        document.getElementById('produto').addEventListener('change', function() {
            const product = this.value;
            const container = document.getElementById('productSpecificFields');
            
            container.innerHTML = '';
            
            if (product === 'filmes') {
                container.innerHTML = `
                    <div class="form-group">
                        <label for="filme_nome">🍿 Nome do Filme:</label>
                        <input type="text" id="filme_nome" name="filme_nome">
                    </div>
                    <div class="form-group">
                        <label for="filme_tmdb">🌟 ID do TMDB (se souber):</label>
                        <input type="text" id="filme_tmdb" name="filme_tmdb">
                    </div>
                    <div class="form-group">
                        <label for="filme_obs">📋 Observações sobre o filme:</label>
                        <textarea id="filme_obs" name="filme_obs"></textarea>
                    </div>
                `;
            } else if (product === 'series') {
                container.innerHTML = `
                    <div class="form-group">
                        <label for="serie_nome">📽 Nome da Série:</label>
                        <input type="text" id="serie_nome" name="serie_nome">
                    </div>
                    <div class="form-group">
                        <label for="serie_tmdb">🌟 ID do TMDB (se souber):</label>
                        <input type="text" id="serie_tmdb" name="serie_tmdb">
                    </div>
                    <div class="form-group">
                        <label for="serie_obs">📋 Observações sobre a série:</label>
                        <textarea id="serie_obs" name="serie_obs"></textarea>
                    </div>
                `;
            }
        });
        
        // Form submission
        function submitForm(event) {
            event.preventDefault();
            
            const form = document.getElementById('ticketForm');
            const formData = new FormData(form);
            
            // Convert formData to JSON
            const data = {};
            formData.forEach((value, key) => {
                data[key] = value;
            });
            
            // Send request
            fetch('open.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify(data)
            })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    showSuccess(result.message || 'Chamado aberto com sucesso!');
                    form.reset();
                } else {
                    showError(result.error || 'Erro ao abrir chamado. Por favor, tente novamente.');
                }
            })
            .catch(error => {
                showError('Erro ao enviar o formulário. Por favor, tente novamente.');
                console.error('Error:', error);
            });
            
            return false;
        }
        
        function showSuccess(message) {
            const successDiv = document.getElementById('successMessage');
            successDiv.textContent = message;
            successDiv.style.display = 'block';
            document.getElementById('errorMessage').style.display = 'none';
            
            // Scroll to success message
            successDiv.scrollIntoView({ behavior: 'smooth' });
            
            // Hide after 5 seconds
            setTimeout(() => {
                successDiv.style.display = 'none';
            }, 5000);
        }
        
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            document.getElementById('successMessage').style.display = 'none';
            
            // Scroll to error message
            errorDiv.scrollIntoView({ behavior: 'smooth' });
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
            
            const labels = document.querySelectorAll('label');
            labels.forEach(label => label.classList.toggle('night', night));
            
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
    </script>
</body>
</html>
