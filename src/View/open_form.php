<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Abrir Chamado - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        .tab-btns { display: flex; gap: 10px; margin-bottom: 20px; }
        .tab-btns button { padding: 8px 18px; border: none; border-radius: 4px; background: #007bff; color: #fff; cursor: pointer; font-weight: bold; }
        .tab-btns button.active { background: #0056b3; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
    </style>
</head>
<body>
    <div style="position: fixed; top: 0; right: 0; z-index: 1000; display: flex; gap: 8px; padding: 12px;">
        <a href="login.php" style="background: #007bff; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold;">Login</a>
        <a href="tickets.php" style="background: #28a745; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold;">Lista de Chamados</a>
    </div>
    <div class="container">
        <div class="tab-btns">
            <button id="tab-abrir" class="active" onclick="showTab('abrir')">Abrir Chamado</button>
            <button id="tab-buscar" onclick="showTab('buscar')">Buscar Chamados</button>
        </div>
        <div id="tab-content-abrir" class="tab-content active">
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
                <div id="paste-area" style="border:1px dashed #aaa;padding:10px;text-align:center;margin-bottom:10px;cursor:pointer;">
                    <span id="paste-hint">Cole uma imagem aqui (Ctrl+V) ou arraste uma imagem</span>
                    <img id="preview" src="" alt="Pré-visualização" style="display:none;max-width:200px;max-height:200px;margin-top:10px;"/>
                </div>
                <button type="submit" class="btn">Enviar Chamado</button>
            </form>
        </div>
        <div id="tab-content-buscar" class="tab-content">
            <h2 style="color:#007bff;">Buscar chamados por e-mail ou telefone</h2>
            <form id="buscar-form" onsubmit="return buscarChamados();" style="background:#f7f7f7;padding:18px 24px;border-radius:8px;box-shadow:0 1px 6px #e0e0e0;max-width:420px;margin-bottom:18px;">
                <div style="margin-bottom:12px;">
                    <label for="buscar-email" style="font-weight:bold;">E-mail utilizado:</label>
                    <input type="email" id="buscar-email" name="buscar-email" style="width:220px;padding:6px 10px;border-radius:4px;border:1px solid #ccc;">
                </div>
                <div style="margin-bottom:12px;">
                    <span style="margin:0 10px;font-weight:bold;">ou</span>
                </div>
                <div style="margin-bottom:18px;">
                    <label for="buscar-telefone" style="font-weight:bold;">Telefone utilizado:</label>
                    <input type="text" id="buscar-telefone" name="buscar-telefone" style="width:180px;padding:6px 10px;border-radius:4px;border:1px solid #ccc;">
                </div>
                <button type="submit" class="btn" style="width:100%;font-size:16px;">Buscar meus chamados</button>
                <div style="font-size:13px;color:#888;margin-top:10px;">Preencha <b>apenas um</b> dos campos acima para consultar todos os seus chamados.</div>
            </form>
            <div id="resultado-busca" style="margin-top:20px;"></div>
        </div>
    </div>
    <script>
    function showTab(tab) {
        document.getElementById('tab-content-abrir').classList.remove('active');
        document.getElementById('tab-content-buscar').classList.remove('active');
        document.getElementById('tab-abrir').classList.remove('active');
        document.getElementById('tab-buscar').classList.remove('active');
        document.getElementById('tab-content-' + tab).classList.add('active');
        document.getElementById('tab-' + tab).classList.add('active');
    }
    function buscarChamados() {
        const email = document.getElementById('buscar-email').value.trim();
        const telefone = document.getElementById('buscar-telefone').value.trim();
        if (!email && !telefone) {
            document.getElementById('resultado-busca').innerHTML = '<div style="background:#ffeaea;color:#d70022;padding:12px 18px;border-radius:6px;font-weight:bold;">Por favor, informe o <b>e-mail</b> ou <b>telefone</b> utilizado para abrir o chamado.</div>';
            return false;
        }
        const params = new URLSearchParams();
        if (email) params.append('email', email);
        if (telefone) params.append('telefone', telefone);
        fetch('buscarchamados.php?' + params.toString())
            .then(res => res.json())
            .then(data => {
                let html = '';
                if (data && data.length > 0) {
                    html = '<div style="background:#e8f5e9;color:#388e3c;padding:10px 18px;border-radius:6px;font-weight:bold;margin-bottom:18px;">Chamados encontrados:</div>';
                    data.forEach(function(ticket, idx) {
                        html += `<div style='margin-bottom:18px;padding:16px 18px;background:#f7f7f7;border-radius:8px;box-shadow:0 1px 4px #e0e0e0;'>` +
                            `<div style='font-size:15px;'><b>Status:</b> <span style='font-weight:bold;'>${ticket.status}</span></div>` +
                            `<div style='font-size:15px;'><b>Assunto:</b> ${ticket.subject}</div>` +
                            `<div style='font-size:15px;'><b>Mensagem:</b> ${ticket.message}</div>` +
                            `<div style='font-size:15px;'><b>Telefone:</b> ${ticket.telefone || '-'}</div>` +
                            `</div>`;
                    });
                } else {
                    html = '<div style="background:#ffeaea;color:#d70022;padding:12px 18px;border-radius:6px;font-weight:bold;">Nenhum chamado encontrado para o e-mail ou telefone informado.</div>';
                }
                document.getElementById('resultado-busca').innerHTML = html;
            });
        return false;
    }
    // Preview for file input
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

    // Paste event
    pasteArea.addEventListener('paste', function(e) {
        const items = (e.clipboardData || e.originalEvent.clipboardData).items;
        for (let i = 0; i < items.length; i++) {
            if (items[i].type.indexOf('image') !== -1) {
                const file = items[i].getAsFile();
                imageInput.files = new DataTransfer().files;
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

    // Drag & drop support
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
    </script>
</body>
</html>
