<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Chamado Enviado - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h2>Chamado enviado com sucesso!</h2>
        <p>Seu chamado foi registrado. Em breve nossa equipe entrará em contato.</p>
        
        <?php
        // Recuperar o ID do chamado recém-enviado
        $file = __DIR__ . '/../../../tickets.txt';
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
        ?>
        <div style="margin: 18px 0; padding: 12px; background: #f7f7f7; border-radius: 6px;">
            <strong>ID do seu chamado:</strong> <span style="color:#007bff;font-size:18px;">#<?= $id ?></span><br>
            <strong>E-mail informado:</strong> <span style="color:#007bff;"> <?= htmlspecialchars($ticket->email ?? '') ?> </span><br>
            <strong>Mensagem enviada:</strong><br>
            <span style="color:#333;"> <?= nl2br(htmlspecialchars($ticket->message ?? '')) ?> </span>
        </div>
        <a href="open.php">Abrir novo chamado</a>

        <?php if (!empty($ticket->imagePath)): ?>
            <div>
                <p><strong>Imagem enviada:</strong></p>
                <img src="<?php echo htmlspecialchars($ticket->imagePath); ?>" alt="Imagem do chamado" style="max-width:300px;max-height:300px;">
            </div>
        <?php endif; ?>
    </div>

    <footer>
        <p>&copy; 2023 HelpDesk. Todos os direitos reservados.</p>
    </footer>
</body>
</html>
