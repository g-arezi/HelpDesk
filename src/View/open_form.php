<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Abrir Chamado - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { background: linear-gradient(120deg, #e3f2fd 0%, #f4f8fb 100%); font-family: 'Segoe UI', Arial, sans-serif; margin:0; transition: background 0.3s, color 0.3s; }
        .container {
            background: #fff;
            max-width: 480px;
            margin: 60px auto 30px auto;
            padding: 38px 44px 34px 44px;
            border-radius: 22px;
            box-shadow: 0 8px 32px #0003, 0 1.5px 8px #1976d210;
            transition: background 0.3s, color 0.3s;
        }
        h2 {
            color: #1976d2;
            margin-bottom: 22px;
            text-align: center;
            font-size: 2.2em;
            letter-spacing: 1.2px;
            font-weight: 700;
        }
        label {
            display: block;
            margin-top: 18px;
            margin-bottom: 6px;
            font-weight: 600;
            color: #1976d2;
            font-size: 1.08em;
            letter-spacing: 0.2px;
        }
        input, select, textarea {
            width: 100%;
            padding: 13px;
            border: 1.5px solid #b0bec5;
            border-radius: 10px;
            font-size: 1.12em;
            margin-bottom: 12px;
            background: #f7fafd;
            transition: border 0.2s, background 0.2s, color 0.2s;
            color: #222;
        }
        input:focus, select:focus, textarea:focus {
            border: 1.5px solid #1976d2;
            background: #e3f2fd;
            outline: none;
            color: #1976d2;
        }
        input[type="file"] {
            padding: 0;
            background: none;
        }
        .btn {
            background: linear-gradient(90deg, #1976d2 60%, #43a047 100%);
            color: #fff;
            border: none;
            padding: 15px 0;
            width: 100%;
            border-radius: 10px;
            font-size: 1.18em;
            font-weight: bold;
            margin-top: 26px;
            box-shadow: 0 2px 12px #1976d220;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .btn:hover {
            background: linear-gradient(90deg, #125ea7 60%, #388e3c 100%);
            box-shadow: 0 4px 18px #1976d240;
        }
        #paste-area {
            border: 2px dashed #90caf9;
            background: #e3f2fd;
            padding: 18px;
            text-align: center;
            margin-bottom: 10px;
            border-radius: 12px;
            cursor: pointer;
            transition: background 0.2s, border 0.2s;
        }
        #paste-area:hover {
            background: #bbdefb;
            border-color: #1976d2;
        }
        #preview {
            display: block;
            margin: 12px auto 0 auto;
            max-width: 220px;
            max-height: 220px;
            border-radius: 10px;
            box-shadow: 0 1px 8px #1976d220;
        }
        .topnav {
            display: flex;
            justify-content: center;
            gap: 12px;
            padding: 18px 0 0 0;
        }
        .topnav a {
            background: #1976d2;
            color: #fff;
            padding: 10px 24px;
            border-radius: 10px;
            text-decoration: none;
            font-weight: bold;
            font-size: 1.08em;
            box-shadow: 0 2px 8px #1976d220;
            transition: background 0.2s, box-shadow 0.2s;
        }
        .topnav a:hover {
            background: #125ea7;
            box-shadow: 0 4px 16px #1976d240;
        }
        /* Night/Light mode switcher - canto inferior esquerdo (ajuste visual igual dashboard) */
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
        @media (max-width: 600px) {
            .container { padding: 18px 4vw; }
            .topnav a { padding: 8px 10px; font-size: 0.98em; }
        }
        /* Night mode styles */
        body.night { background: #181c24; color: #e0e0e0; }
        .container.night { background: #232a36; color: #fff; box-shadow: 0 8px 32px #0006; }
        .topnav.night a { background: #232a36; color:rgb(255, 255, 255); }
        .topnav.night a:hover { background: #263238; }
        input.night, select.night, textarea.night { background: #232a36; color: #fff; border: 1px solid #374151; }
        input.night:focus, select.night:focus, textarea.night:focus { background: #181c24; border: 1.5px solid #90caf9; color: #90caf9; }
        #paste-area.night { background: #232a36; border-color: #90caf9; color: #e0e0e0; }
        #paste-area.night:hover { background: #263238; border-color: #1976d2; }
        .btn.night { background: linear-gradient(90deg, #1976d2 60%, #232a36 100%); color: #fff; }
        .btn.night:hover { background: linear-gradient(90deg, #125ea7 60%, #181c24 100%); }
        body.night, .container.night, .container.night label, .container.night h2, .container.night input, .container.night textarea, .container.night select, .container.night option {
            color: #fff !important;
        }
        .container.night input, .container.night textarea, .container.night select {
            background: #232a36 !important;
            border: 1px solid #444 !important;
            color: #fff !important;
        }
        .container.night input::placeholder, .container.night textarea::placeholder {
            color: #b0b0b0 !important;
            opacity: 1;
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
    <!-- ...existing code... -->
    <div class="topnav">
        <a href="login.php">Login</a>
        <a href="tickets.php">Lista de Chamados</a>
        <a href="buscarchamados.html">Buscar Chamados</a>
        <a href="open.php" style="background:#43a047;">Abrir Chamado</a>
    </div>
    <div class="container">
        <h2>Abrir Chamado</h2>
        <form method="post" action="open.php" enctype="multipart/form-data">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" required>

            <label for="telefone">Telefone para contato:</label>
            <input type="text" id="telefone" name="telefone" required>

            <label for="email">E-mail:</label>
            <input type="email" id="email" name="email" required>

            <label for="subject">Tópico de ajuda:</label>
            <select id="subject" name="subject" required>
                <option value="">Selecione um erro</option>
                <option value="sem_sinal">Sem sinal</option>
                <option value="conexao_internet">Problemas de conexão com a internet</option>
                <option value="erro_sistema">Erro no sistema</option>
                <option value="erro_reprodutor">Erro no reprodutor</option>
                <option value="erro_servidor">Erro no servidor</option>
                <option value="erro_configuracao">Erro de configuração</option>
                <option value="tela_preta">Tela preta</option>
                <option value="travamento_canais">Travamento de canais</option>
                <option value="erro_autenticacao">Erro de autenticação</option>
                <option value="problemas_epg">Problemas com EPG</option>
                <option value="audio_fora_sincronia">Áudio fora de sincronia</option>
                <option value="outro">Outros</option>
            </select>

            <label for="message">Mensagem:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <label for="image">Anexar imagem:</label>
            <input type="file" id="image" name="image" accept="image/*">
            <div id="paste-area">
                <span id="paste-hint">Cole uma imagem aqui (Ctrl+V) ou arraste uma imagem</span>
                <img id="preview" src="" alt="Pré-visualização" style="display:none;"/>
            </div>
            <button type="submit" class="btn">Enviar Chamado</button>
        </form>
    </div>
    <script>
    const pasteArea = document.getElementById('paste-area');
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('preview');
    const pasteHint = document.getElementById('paste-hint');
    imageInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function(ev) {
                preview.src = ev.target.result;
                preview.style.display = 'block';
                pasteHint.style.display = 'none';
            };
            reader.readAsDataURL(this.files[0]);
        }
    });
    pasteArea.addEventListener('paste', function(e) {
        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const file = items[i].getAsFile();
                const dt = new DataTransfer();
                dt.items.add(file);
                imageInput.files = dt.files;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                    pasteHint.style.display = 'none';
                };
                reader.readAsDataURL(file);
                break;
            }
        }
    });
    pasteArea.addEventListener('dragover', function(e) {
        e.preventDefault();
        pasteArea.style.background = '#f0f0f0';
    });
    pasteArea.addEventListener('dragleave', function(e) {
        e.preventDefault();
        pasteArea.style.background = '';
    });
    pasteArea.addEventListener('drop', function(e) {
        e.preventDefault();
        pasteArea.style.background = '';
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            const file = e.dataTransfer.files[0];
            if (file.type.indexOf('image') !== -1) {
                const dt = new DataTransfer();
                dt.items.add(file);
                imageInput.files = dt.files;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                    pasteHint.style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        }
    });
    // Novo switch de modo
    const modeSwitch = document.getElementById('modeSwitch');
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    const modeLabel = document.getElementById('modeLabel');
    function setMode(night) {
        document.body.classList.toggle('night', night);
        document.querySelector('.container').classList.toggle('night', night);
        document.querySelector('.topnav').classList.toggle('night', night);
        document.querySelectorAll('input, select, textarea').forEach(e=>e.classList.toggle('night', night));
        document.getElementById('paste-area').classList.toggle('night', night);
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
    // Remove botão antigo
    var oldBtn = document.getElementById('nightToggle');
    if(oldBtn) oldBtn.remove();
    </script>
</body>
</html>
