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
            border-radius: 22p    } else if (this.value === 'canais') {
        // Mostrar campos de tópico e mensagem apenas para canais
        obsDiv.textContent = 'Ao selecionar CANAIS, escolha uma das opções abaixo e forneça detalhes adicionais na mensagem.';
        obsDiv.style.display = 'block';
        canaisCampos.style.display = 'block';
        
        // Mostrar campos de tópico e mensagem para canais
        subjectField.style.display = 'block';
        subjectLabel.style.display = 'block';
        messageField.style.display = 'block';
        messageLabel.style.display = 'block';
        subjectField.required = true;
        messageField.required = true;            box-shadow: 0 8px 32px #0003, 0 1.5px 8px #1976d210;
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
        /* Padronização dos botões de opção (radio) */
        .custom-radio-group {
            display: flex;
            justify-content: center;
            gap: 28px;
            margin-bottom: 10px;
            align-items: center;
        }
        .custom-radio-label {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 1em;
            margin: 0;
            background: #e3f2fd;
            border: 1.5px solid #90caf9;
            border-radius: 8px;
            padding: 8px 18px;
            cursor: pointer;
            transition: background 0.2s, border 0.2s;
        }
        .custom-radio-label:hover, .custom-radio-label input:focus + span {
            background: #bbdefb;
            border-color: #1976d2;
        }
        .custom-radio-label input[type="radio"] {
            width: 18px;
            height: 18px;
            accent-color: #1976d2;
            margin: 0;
        }
        .custom-radio-label span {
            font-weight: 500;
            color: #1976d2;
        }
        /* Night mode para botões de opção (radio) */
        .container.night .custom-radio-label {
            background: #232a36;
            border: 1.5px solid #374151;
            color: #fff;
        }
        .container.night .custom-radio-label:hover, .container.night .custom-radio-label input:focus + span {
            background: #263238;
            border-color: #90caf9;
        }
        .container.night .custom-radio-label span {
            color: #90caf9;
        }
        .container.night .custom-radio-label input[type="radio"] {
            accent-color: #90caf9;
        }

        /* Ajuste para manter contraste e visual agradável */
        body.night .custom-radio-group label {
            color: #fff;
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
        <a href="buscarchamados.html">🔎 Buscar Chamados</a>
        <a href="open.php" style="background:#43a047;">🆕 Abrir Chamado</a>
    </div>
    <div class="container">
        <h2>Abrir Chamado</h2>        <form method="post" action="open.php" enctype="multipart/form-data">
            <!-- Campos de identificação removidos - agora usando dados da sessão -->
            <div class="user-info-alert" style="background: #e3f2fd; color: #1976d2; padding: 15px; border-radius: 10px; margin-bottom: 20px; border: 1px solid #b3e5fc;">
                <p style="margin: 0; font-size: 16px; font-weight: 500;">
                    <span style="font-weight: bold;">✅ Identificação automática:</span> Este chamado será aberto com as suas informações de usuário.
                </p>
            </div>            <label for="produto">📦 Produto/Serviço:</label>
            <select id="produto" name="produto" required>
                <option value="">Selecione um produto/serviço</option>
                <option value="canais">Canais</option>
                <option value="filmes">Filmes</option>
                <option value="series">Séries</option>
                <option value="outros">Outros</option>
            </select>           
             <div id="produto-observacao" style="display:none; margin-top:8px; color:#1976d2; font-size:0.98em;"></div>
            <!-- Padronização dos botões de opção para filmes -->
            <div id="filmes-campos" style="display:none; margin-bottom:10px;">
                <label style="display:block; text-align:center; margin-bottom:8px;">🤔 Qual opção desejada?</label>
                <div class="custom-radio-group">
                    <label for="filmes_adicao" class="custom-radio-label">
                        <input type="radio" id="filmes_adicao" name="filmes_obse" value="Solicitar conteúdo">
                        <span>🆕- Solicitar conteúdo</span>
                    </label>
                    <label for="filmes_correcao" class="custom-radio-label">
                        <input type="radio" id="filmes_correcao" name="filmes_obse" value="Corrigir conteúdo">
                        <span>🛠️-Corrigir conteúdo</span>
                    </label>
                </div>
                <label for="filme_nome">🍿 FILME:<span style="color:red">*</span></label>
                <input type="text" id="filme_nome" name="filme_nome" autocomplete="off" placeholder="Digite o nome do filme Ex: Mufasa: O Rei Leão (2024)">                <label for="filme_tmdb">🌟 TMDB:<span style="color:red">*</span></label>
                <input type="text" id="filme_tmdb" name="filme_tmdb" autocomplete="off" placeholder="Ex: https://www.themoviedb.org/movie/123456-mufasa">
                <div class="tmdb-observacao" style="margin-bottom:15px; margin-top:-5px; color:#1976d2; font-size:0.95em;">
                    <b>Observação:</b> Para encontrar o código TMDB, acesse 
                    <a href="#" 
                       style="color:#1976d2;text-decoration:underline;cursor:pointer;" 
                       class="tmdb-link"
                       data-link="https://www.themoviedb.org/?language=pt-BR"
                       tabindex="0"
                    >https://www.themoviedb.org/?language=pt-BR</a> 
                    (o link será copiado ao clicar, mas não abrirá a página).
                </div>
                <label for="filme_obs">⚠️OBSERVAÇÃO:</label>
                <input type="text" id="filme_obs" name="filme_obs" autocomplete="off" placeholder="Ex: idioma, qualidade, etc.">
            </div>
            <!-- Padronização dos botões de opção para séries -->
            <div id="series-campos" style="display:none; margin-bottom:10px;">
                <label style="display:block; text-align:left; margin-bottom:8px;">🤔 Qual opção desejada?</label>
                <div class="custom-radio-group">
                    <label for="series_adicao" class="custom-radio-label">
                        <input type="radio" id="series_adicao" name="series_obse" value="Solicitar conteúdo">
                        <span>🆕- Solicitar conteúdo</span>
                    </label>
                    <label for="series_correcao" class="custom-radio-label">
                        <input type="radio" id="series_correcao" name="series_obse" value="Corrigir conteúdo">
                        <span>🛠️-Corrigir conteúdo</span>
                    </label>
                </div>
                <label for="serie_nome">📽️ SÉRIE:<span style="color:red">*</span></label>
                <input type="text" id="serie_nome" name="serie_nome" autocomplete="off" placeholder="Digite o nome da série ex: Game of Thrones">                <label for="serie_tmdb">🌟 TMDB:<span style="color:red">*</span></label>
                <input type="text" id="serie_tmdb" name="serie_tmdb" autocomplete="off" placeholder="Ex: https://www.themoviedb.org/tv/121361-game-of-thrones">
                <div class="tmdb-observacao" style="margin-bottom:15px; margin-top:-5px; color:#1976d2; font-size:0.95em;">
                    <b>Observação:</b> Para encontrar o código TMDB, acesse 
                    <a href="#" 
                       style="color:#1976d2;text-decoration:underline;cursor:pointer;" 
                       class="tmdb-link"
                       data-link="https://www.themoviedb.org/?language=pt-BR"
                       tabindex="0"
                    >https://www.themoviedb.org/?language=pt-BR</a> 
                    (o link será copiado ao clicar, mas não abrirá a página).
                </div>
                <label for="serie_obs">⚠️OBSERVAÇÃO:</label>
                <input type="text" id="serie_obs" name="serie_obs" autocomplete="off" placeholder="Ex: temporada, idioma, etc.">
            </div>
            <!-- Padronização dos botões de opção para canais -->
            <div id="canais-campos" style="display:none; margin-bottom:10px;">
                <label style="display:block; text-align:center; margin-bottom:8px;">🤔 Qual opção desejada?</label>
                <div class="custom-radio-group">
                    <label for="canais_adicao" class="custom-radio-label">
                        <input type="radio" id="canais_adicao" name="canais_obse" value="Solicitar conteúdo">
                        <span>🆕- Solicitar conteúdo</span>
                    </label>
                    <label for="canais_correcao" class="custom-radio-label">
                        <input type="radio" id="canais_correcao" name="canais_obse" value="Corrigir conteúdo">
                        <span>🛠️-Corrigir conteúdo</span>
                    </label>
                </div>
            </div>
            
            <script>
            document.querySelectorAll('.tmdb-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const url = this.getAttribute('data-link');
                    navigator.clipboard.writeText(url).then(() => {
                        // Não abre a página, apenas copia para a área de transferência
                    });
                });
            });
            </script>

            <label for="subject" style="display: none;">🆘 Tópico de ajuda:</label>
            <select id="subject" name="subject" required style="display: none;">
                <option value="">Selecione um erro</option>
                <option value="tela_preta">Tela preta</option>
                <option value="travamento_canais">Travamento de canais</option>
                <option value="problemas_epg">Problemas com EPG</option>
                <option value="audio_fora_sincronia">Áudio fora de sincronia</option>
                <option value="outro">Outros</option>
            </select>

            <label for="message" style="display: none;">💬 Mensagem:</label>
            <textarea id="message" name="message" rows="5" required placeholder="Descreva detalhadamente o seu problema ou dúvida" style="display: none;"></textarea>

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
    var oldBtn = document.getElementById('nightToggle');    if(oldBtn) oldBtn.remove();    // Observação dinâmica e campos extras para filmes/séries/canais
const produtoSelect = document.getElementById('produto');
const obsDiv = document.getElementById('produto-observacao');
const filmesCampos = document.getElementById('filmes-campos');
const seriesCampos = document.getElementById('series-campos');
const canaisCampos = document.getElementById('canais-campos');
const filmeNome = document.getElementById('filme_nome');
const filmeTmdb = document.getElementById('filme_tmdb');
const serieNome = document.getElementById('serie_nome');
const serieTmdb = document.getElementById('serie_tmdb');
// Campos de tópico de ajuda e mensagem
const subjectField = document.getElementById('subject');
const subjectLabel = document.querySelector('label[for="subject"]');
const messageField = document.getElementById('message');
const messageLabel = document.querySelector('label[for="message"]');

produtoSelect.addEventListener('change', function() {
    // Reset all
    obsDiv.style.display = 'none';
    filmesCampos.style.display = 'none';
    seriesCampos.style.display = 'none';
    canaisCampos.style.display = 'none';
    filmeNome.required = false;
    filmeTmdb.required = false;
    serieNome.required = false;
    serieTmdb.required = false;
    
    // Ocultar campos de tópico e mensagem por padrão
    subjectField.style.display = 'none';
    subjectLabel.style.display = 'none';
    messageField.style.display = 'none';
    messageLabel.style.display = 'none';
    subjectField.required = false;
    messageField.required = false;
      if (this.value === 'filmes') {
        obsDiv.textContent = 'Ao selecionar FILMES, informe o nome do filme e o código TMDB (obrigatórios), além de uma observação se desejar.';
        obsDiv.style.display = 'block';
        filmesCampos.style.display = 'block';
        filmeNome.required = true;
        filmeTmdb.required = true;
    } else if (this.value === 'series') {
        obsDiv.textContent = 'Ao selecionar SÉRIES, informe o nome da série e o código TMDB (obrigatórios), além de uma observação se desejar.';
        obsDiv.style.display = 'block';
        seriesCampos.style.display = 'block';
        serieNome.required = true;
        serieTmdb.required = true;
    } else if (this.value === 'canais') {
        obsDiv.textContent = 'Ao selecionar CANAIS, escolha uma das opções abaixo e forneça detalhes adicionais na mensagem, se necessário.';
        obsDiv.style.display = 'block';
        canaisCampos.style.display = 'block';
    } else if (this.value === 'outros') {
        obsDiv.textContent = 'Você selecionou OUTROS. Por favor, descreva sua solicitação na mensagem.';
        obsDiv.style.display = 'block';
    }
});
    </script>
</body>
</html>
