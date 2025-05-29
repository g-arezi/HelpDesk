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
