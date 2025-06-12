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
            color: #111 !important;
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
            color: #111 !important;
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
            left: 14px;
            bottom: 14px;
            z-index: 1000;
            display: flex;
            align-items: center;
            gap: 7px;
            background: #232a36;
            border-radius: 14px;
            padding: 4px 14px 4px 10px;
            box-shadow: 0 2px 12px #0002, 0 1px 4px #0001;
            color: #fff;
            font-size: 1rem;
            font-weight: 500;
            border: 1.5px solid #232a36;
            transition: background 0.3s, color 0.3s, box-shadow 0.3s, border 0.3s;
            min-width: 110px;
        }
        .mode-switch.light {
            background: #e3f2fd;
            color: #1976d2;
            border: 1.5px solid #b3c6e0;
            box-shadow: 0 2px 12px #b3c6e033, 0 1px 4px #b3c6e022;
        }
        .mode-switch input[type="checkbox"] {
            width: 32px;
            height: 18px;
            appearance: none;
            background: #bdbdbd;
            outline: none;
            border-radius: 10px;
            position: relative;
            transition: background 0.3s, border 0.3s, box-shadow 0.3s;
            cursor: pointer;
            border: 1.5px solid #b0bec5;
            box-shadow: 0 1px 4px #0001;
            margin: 0 2px;
        }
        .mode-switch input[type="checkbox"]:checked {
            background: #1976d2;
            border: 1.5px solid #1976d2;
        }
        .mode-switch input[type="checkbox"]::before {
            content: '';
            position: absolute;
            left: 2px;
            top: 50%;
            transform: translateY(-50%);
            width: 14px;
            height: 14px;
            background: #fff;
            border-radius: 50%;
            box-shadow: 0 2px 6px #0002, 0 1px 2px #1976d233;
            transition: left 0.3s, box-shadow 0.3s;
        }
        .mode-switch input[type="checkbox"]:checked::before {
            left: 16px;
        }
        .mode-switch .icon {
            font-size: 1.15em;
            margin-right: 2px;
        }
        .mode-switch #modeLabel {
            font-weight: 600;
            letter-spacing: 0.2px;
            font-size: 1em;
            margin-left: 2px;
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
        <a href="login.php">🔑 Login</a>
        <a href="tickets.php">📋 Lista de Chamados</a>
        <a href="buscarchamados.html">🔎 Buscar Chamados</a>
        <a href="open.php" style="background:#43a047;">🆕 Abrir Chamado</a>
    </div>
    <div class="container">
        <h2>Abrir Chamado</h2>
        <form method="post" action="open.php" enctype="multipart/form-data">
            <label for="name">👤 Nome:</label>
            <input type="text" id="name" name="name" required>

            <label for="telefone">📞 Telefone para contato:</label>
            <input type="text" id="telefone" name="telefone" required>

            <label for="email">✉️ E-mail:</label>
            <input type="email" id="email" name="email" required>
            <label for="produto">📦 Produto/Serviço:</label>
            <select id="subject" name="subject" required>
                <option value="">Selecione um produto/serviço</option>
                <option value="iptv">Canais</option>
                <option value="internet">Filmes</option>
                <option value="telefonia">Séries</option>
                <option value="outro">Outros</option>
             </select>
             
            <label for="subject">🆘 Tópico de ajuda:</label>
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

            <label for="message">💬 Mensagem:</label>
            <textarea id="message" name="message" rows="5" required></textarea>

            <label for="image">📷 Anexar imagem ou vídeo:</label>
            <input type="file" id="image" name="image" accept="image/*,video/*">
            <div id="paste-area">
                <span id="paste-hint">Cole uma imagem aqui (Ctrl+V) ou arraste uma imagem/vídeo</span>
                <img id="preview" src="" alt="Pré-visualização" style="display:none;"/>
                <video id="video-preview" controls style="display:none;max-width:220px;max-height:220px;border-radius:10px;box-shadow:0 1px 8px #1976d220;"></video>
            </div>
            <button type="submit" class="btn">Enviar Chamado</button>
        </form>
    </div>
    <script>
    const pasteArea = document.getElementById('paste-area');
    const imageInput = document.getElementById('image');
    const preview = document.getElementById('preview');
    const videoPreview = document.getElementById('video-preview');
    const pasteHint = document.getElementById('paste-hint');
    imageInput.addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            const file = this.files[0];
            if (file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                    pasteHint.style.display = 'none';
                    document.getElementById('video-preview').style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                const videoPreview = document.getElementById('video-preview');
                videoPreview.src = URL.createObjectURL(file);
                videoPreview.style.display = 'block';
                preview.style.display = 'none';
                pasteHint.style.display = 'none';
            }
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
                    document.getElementById('video-preview').style.display = 'none';
                };
                reader.readAsDataURL(file);
                break;
            }
        }
    });
    pasteArea.addEventListener('drop', function(e) {
        e.preventDefault();
        pasteArea.style.background = '';
        if (e.dataTransfer.files && e.dataTransfer.files[0]) {
            const file = e.dataTransfer.files[0];
            if (file.type.startsWith('image/')) {
                const dt = new DataTransfer();
                dt.items.add(file);
                imageInput.files = dt.files;
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                    pasteHint.style.display = 'none';
                    document.getElementById('video-preview').style.display = 'none';
                };
                reader.readAsDataURL(file);
            } else if (file.type.startsWith('video/')) {
                const dt = new DataTransfer();
                dt.items.add(file);
                imageInput.files = dt.files;
                const videoPreview = document.getElementById('video-preview');
                videoPreview.src = URL.createObjectURL(file);
                videoPreview.style.display = 'block';
                preview.style.display = 'none';
                pasteHint.style.display = 'none';
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
