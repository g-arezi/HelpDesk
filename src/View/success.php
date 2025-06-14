<!DOCTYPE html>

<html lang="pt-br"> 
    
<head>
    <meta charset="UTF-8">
    <title>Chamado Enviado - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
    <style>
        body { background: linear-gradient(120deg, #e3f2fd 0%, #f4f8fb 100%); font-family: Arial, sans-serif; margin:0; transition: background 0.3s, color 0.3s; }
        .container {
            background: #fff;
            max-width: 480px;
            margin: 60px auto 30px auto;
            padding: 36px 40px 30px 40px;
            border-radius: 18px;
            box-shadow: 0 8px 32px #0003;
            transition: background 0.3s, color 0.3s;
        }
        h2 {
            color: #1976d2;
            margin-bottom: 18px;
            text-align: center;
            font-size: 2.1em;
            letter-spacing: 1px;
        }
        .container > p { text-align:center; color:#333; font-size:1.1em; }
        .info-box {
            margin: 18px 0; padding: 16px; background: #f7f7f7; border-radius: 10px; box-shadow:0 1px 8px #1976d220; font-size:1.08em;
        }
        a.btn {
            display:inline-block;padding:12px 28px;margin:10px 10px 10px 0;background:#1976d2;color:#fff;text-decoration:none;border-radius:8px;font-weight:bold;box-shadow:0 2px 8px #1976d220;transition:background 0.2s,box-shadow 0.2s;font-size:1.08em;
        }
        a.btn.green { background:#43a047; }
        a.btn:hover { background:#125ea7; box-shadow:0 4px 16px #1976d240; }
        a.btn.green:hover { background:#388e3c; }
        .img-preview { max-width:300px;max-height:300px;border-radius:10px;box-shadow:0 2px 12px #1976d220;margin-top:10px; }
        footer { text-align:center; color:#888; margin-top:30px; font-size:0.98em; }
        /* Night mode */
        body.night { background: #181c24; color: #e0e0e0; }
        .container.night { background: #232a36; color: #fff; }
        .info-box.night { background: #232a36; color: #fff; border: 1px solid #374151; }
        .info-box.night strong, .info-box.night span { color: #fff !important; }
        a.btn.night { background: #232a36; color: #90caf9; }
        a.btn.night.green { background: #388e3c; color: #fff; }
        a.btn.night:hover { background: #263238; color: #fff; }
        a.btn.night.green:hover { background: #1976d2; color: #fff; }
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
            user-select: none;
        }
        .mode-switch.light {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #b3c6e0;
        }
        .mode-switch .icon {
            font-size: 1.2em;
            margin-right: 2px;
        }
        .mode-switch #modeLabel {
            font-weight: 600;
            font-size: 1.05em;
            margin-left: 2px;
        }
        .switch {
            position: relative;
            display: inline-block;
            width: 36px;
            height: 20px;
            margin: 0 4px;
            vertical-align: middle;
        }
        .switch input {
            opacity: 0;
            width: 100%;
            height: 100%;
            position: absolute;
            left: 0;
            top: 0;
            margin: 0;
            z-index: 2;
            cursor: pointer;
        }
        .slider {
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: #bdbdbd;
            border-radius: 12px;
            transition: background 0.3s;
            box-shadow: 0 1px 4px #0002;
            z-index: 1;
        }
        .switch input:checked + .slider {
            background: #1976d2;
        }
        .slider:before {
            content: '';
            position: absolute;
            left: 3px;
            top: 3px;
            width: 14px;
            height: 14px;
            background: #fff;
            border-radius: 50%;
            transition: left 0.3s;
            box-shadow: 0 1px 4px #0002;
        }
        .switch input:checked + .slider:before {
            left: 19px;
        }
        .switch input:focus + .slider {
            box-shadow: 0 0 0 2px #1976d2aa;
        }
        .switch:hover .slider {
            filter: brightness(0.95);
        }
        @media (max-width: 700px) {
            .mode-switch { font-size: 0.98rem; padding: 4px 8px 4px 6px; }
            .switch { width: 30px; height: 16px; }
            .slider:before { width: 10px; height: 10px; top: 3px; left: 2px; }
            .switch input:checked + .slider:before { left: 14px; }
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>✅ Chamado enviado com sucesso!</h2>
        <p>🎉 Seu chamado foi registrado. Em breve nossa equipe entrará em contato.</p>
        
        <?php
        // Recuperar o ID do chamado recém-enviado
        // Verifica se o arquivo tickets.txt existe e lê o conteúdo
        $file = __DIR__ . '/../../../logs/tickets.txt';
        $id = 0;
        if (file_exists($file) && isset($ticket)) {
            $content = file_get_contents($file);
            $tickets = json_decode($content, true) ?: [];
            // O último ticket do array é o recém-enviado
            $last = end($tickets);
            if ($last) {
                $id = count($tickets);
            }
        }
        // Exibe o ID do chamado e os detalhes
        ?>
        <!-- Switch de modo claro/noturno -->
        <div class="mode-switch light" id="modeSwitch">
            <span class="icon" id="modeIcon">🌞</span>
            <label class="switch">
                <input type="checkbox" id="modeToggle" aria-label="Alternar modo claro/noturno">
                <span class="slider"></span>
            </label>
            <span id="modeLabel">Claro</span>
        </div>        <div class="info-box" id="infoBox">
            <strong>Usuário:</strong> <span style="color:#1976d2;"> <?= htmlspecialchars(isset($lastTicket['user']) ? $lastTicket['user'] : ($_SESSION['user'] ?? 'Não identificado')) ?> </span><br>
            <strong>Mensagem enviada:</strong><br>
            <span style="color:#333;"> <?= nl2br(htmlspecialchars($lastTicket['message'] ?? '')) ?> </span>
        </div>
        <a href="open.php" class="btn">📝 Abrir novo chamado</a>
        <a href="buscarchamados.html" class="btn green">🔎 Buscar chamados</a>

        <?php if (!empty($ticket->imagePath)): ?>
            <div>
                <p><strong>Imagem enviada:</strong></p>
                <img src="<?php echo htmlspecialchars($ticket->imagePath); ?>" alt="Imagem do chamado" class="img-preview">
            </div>
        <?php endif; ?>
    </div>
            
    <footer>
        <p>&copy; Projeto - HelpDesk - <a href="https://portifolio-beta-five-52.vercel.app/">Dev. Gabriel Arezi</a>. Todos os direitos reservados.</p>
    </footer>
    <script>
// --- Modo noturno switch ---
    const modeSwitch = document.getElementById('modeSwitch');
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    const modeLabel = document.getElementById('modeLabel');
    function setMode(night) {
        document.body.classList.toggle('night', night);
        document.querySelector('.container').classList.toggle('night', night);
        let infoBox = document.getElementById('infoBox');
        if(infoBox) infoBox.classList.toggle('night', night);
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
    // Inicialização correta do switch
    if(localStorage.getItem('nightMode')) {
        setMode(true);
        modeToggle.checked = true;
    } else {
        setMode(false);
        modeToggle.checked = false;
    }
    modeToggle.addEventListener('change', function() {
        setMode(this.checked);
    });
    // Remove botão antigo se existir
    var oldBtn = document.getElementById('nightToggle');
    if(oldBtn) oldBtn.remove();
    </script>
</body>

</html>
