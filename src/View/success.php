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
        <div style="margin: 18px 0; padding: 12px; background: #f7f7f7; border-radius: 6px;">
            <strong>E-mail informado:</strong> <span style="color:#007bff;"> <?= htmlspecialchars($ticket->email ?? '') ?> </span><br>
            <strong>Mensagem enviada:</strong><br>
            <span style="color:#333;"> <?= nl2br(htmlspecialchars($ticket->message ?? '')) ?> </span>
        </div>
        <a href="open.php" style="display:inline-block;padding:10px 20px;margin:5px 10px 5px 0;background:#007bff;color:#fff;text-decoration:none;border-radius:4px;">Abrir novo chamado</a>
        <a href="buscarchamados.html" style="display:inline-block;padding:10px 20px;margin:5px 0;background:#28a745;color:#fff;text-decoration:none;border-radius:4px;">Buscar chamados</a>

        <?php if (!empty($ticket->imagePath)): ?>
            <div>
                <p><strong>Imagem enviada:</strong></p>
                <img src="<?php echo htmlspecialchars($ticket->imagePath); ?>" alt="Imagem do chamado" style="max-width:300px;max-height:300px;">
            </div>
        
        <?php endif; ?>
    </div>
            
    <footer>
        <p>&copy; Projeto - HelpDesk - <a href="https://portifolio-beta-five-52.vercel.app/">Dev. Gabriel Arezi</a>. Todos os direitos reservados.</p>
    </footer>
</body>

</html>
