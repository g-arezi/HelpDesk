<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Abrir Chamado - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div style="position: fixed; top: 0; right: 0; z-index: 1000; display: flex; gap: 8px; padding: 12px;">
        <a href="login.php" style="background: #007bff; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold;">Login</a>
        <a href="tickets.php" style="background: #28a745; color: #fff; padding: 8px 16px; border-radius: 4px; text-decoration: none; font-weight: bold;">Lista de Chamados</a>
    </div>
    <div class="container">
        <h2>Abrir Chamado</h2>
        <form method="post" action="open.php" enctype="multipart/form-data">
            <label for="name">Nome:</label>
            <input type="text" id="name" name="name" required>

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
            <script>
            const imageInput = document.getElementById('image');
            const pasteArea = document.getElementById('paste-area');
            const preview = document.getElementById('preview');
            const pasteHint = document.getElementById('paste-hint');

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

            <button type="submit">Enviar</button>
        </form>
    </div>
</body>
</html>
