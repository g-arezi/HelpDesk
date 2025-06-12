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
        .night-toggle { position:fixed; bottom:24px; left:24px; top:auto; right:auto; z-index:1000; background:linear-gradient(90deg,#ff6b6b,#b71c1c); color:#fff; border:1px solid #b71c1c; border-radius:20px; padding:10px 22px; cursor:pointer; font-weight:bold; box-shadow:0 2px 12px #0003; font-size: 1.1rem; transition: background 0.3s, color 0.3s; }
        .night-toggle.night { background:linear-gradient(90deg,#b71c1c,#ff6b6b); color:#fff; border-color:#fff; }
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
        <button class="night-toggle" id="nightToggle" type="button" onclick="toggleNightMode()">🌙 Modo Noturno</button>
        <div class="info-box" id="infoBox">
            <strong>E-mail informado:</strong> <span style="color:#1976d2;"> <?= htmlspecialchars($lastTicket['email'] ?? '') ?> </span><br>
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
function toggleNightMode(force) {
    let night;
    if (typeof force === 'boolean') {
        night = force;
        document.body.classList.toggle('night', night);
    } else {
        night = document.body.classList.toggle('night');
    }
    document.querySelector('.container').classList.toggle('night', night);
    if(document.getElementById('infoBox')) document.getElementById('infoBox').classList.toggle('night', night);
    document.querySelectorAll('.btn').forEach(e=>e.classList.toggle('night', night));
    document.getElementById('nightToggle').classList.toggle('night', night);
    if(night) localStorage.setItem('nightMode','1');
    else localStorage.removeItem('nightMode');
}
if(localStorage.getItem('nightMode')) setTimeout(()=>toggleNightMode(true), 100);
</script>
</body>

</html>
