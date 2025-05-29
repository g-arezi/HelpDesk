<?php
session_start();
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: login.php');
    exit;
}
// Página para listar todos os tickets registrados
$tickets = [];
$file = __DIR__ . '/../tickets.txt';
if (file_exists($file)) {
    $lines = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $tickets[] = json_decode($line, true);
    }
}
$auth = isset($_SESSION['auth']) && $_SESSION['auth'] === true;
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tickets - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body>
    <div class="container">
        <h2>Lista de Tickets</h2>
        <?php if ($auth): ?>
            <a href="logout.php" class="btn" style="float:right;margin-top:-40px;">Sair</a>
        <?php else: ?>
            <a href="login.php" class="btn" style="float:right;margin-top:-40px;">Login</a>
        <?php endif; ?>
        <div style="overflow-x:auto;">
        <table class="ticket-table">
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Assunto</th>
                    <th>Mensagem</th>
                    <th>Imagem</th>
                    <?php if ($auth): ?><th>Ações</th><?php endif; ?>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tickets as $i => $ticket): ?>
                    <tr>
                        <td><?= htmlspecialchars($ticket['name']) ?></td>
                        <td><?= htmlspecialchars($ticket['email']) ?></td>
                        <td><?= htmlspecialchars($ticket['subject']) ?></td>
                        <td style="max-width:250px;word-break:break-word;"><?= nl2br(htmlspecialchars($ticket['message'])) ?></td>
                        <td>
                            <?php if (!empty($ticket['imagePath'])): ?>
                                <a href="<?= htmlspecialchars($ticket['imagePath']) ?>" target="_blank">
                                    <img src="<?= htmlspecialchars($ticket['imagePath']) ?>" alt="Imagem" style="max-width:80px;max-height:80px;border-radius:6px;box-shadow:0 1px 4px #ccc;">
                                </a>
                            <?php else: ?>
                                <span style="color:#aaa;">-</span>
                            <?php endif; ?>
                        </td>
                        <?php if ($auth): ?>
                        <td>
                            <a href="edit_ticket.php?id=<?= $i ?>" class="btn" style="background:#f0ad4e;">Editar</a>
                            <a href="delete_ticket.php?id=<?= $i ?>" class="btn" style="background:#d9534f;" onclick="return confirm('Tem certeza que deseja excluir este ticket?');">Excluir</a>
                        </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <br>
        <a href="open.php" class="btn">Abrir novo chamado</a>
    </div>
    <style>
    .ticket-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        margin-top: 20px;
    }
    .ticket-table th, .ticket-table td {
        border: 1px solid #e0e0e0;
        padding: 10px 8px;
        text-align: left;
    }
    .ticket-table th {
        background: #f7f7f7;
        position: sticky;
        top: 0;
        z-index: 2;
    }
    .ticket-table tr:nth-child(even) {
        background: #fafbfc;
    }
    .btn {
        display: inline-block;
        background: #0078d7;
        color: #fff;
        padding: 8px 18px;
        border-radius: 4px;
        text-decoration: none;
        margin-top: 10px;
        transition: background 0.2s;
    }
    .btn:hover {
        background: #005fa3;
    }
    </style>
</body>
</html>
