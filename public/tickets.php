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
                    <th>Status</th>
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
                        <td>
                            <?php 
                            $status = isset($ticket['status']) ? $ticket['status'] : 'nao_aberto';
                            $statusLabel = [
                                'resolvido' => '<span style="color:green;font-weight:bold;">Resolvido</span>',
                                'em_analise' => '<span style="color:orange;font-weight:bold;">Em análise</span>',
                                'nao_aberto' => '<span style="color:red;font-weight:bold;">Não aberto</span>'
                            ];
                            echo $statusLabel[$status] ?? $statusLabel['nao_aberto'];
                            ?>
                            <?php if ($auth): ?>
                            <form method="post" action="tickets.php" style="margin-top:5px;">
                                <input type="hidden" name="id" value="<?= $i ?>">
                                <select name="status" style="padding:2px 6px;">
                                    <option value="nao_aberto" <?= $status==='nao_aberto'?'selected':''; ?>>Não aberto</option>
                                    <option value="em_analise" <?= $status==='em_analise'?'selected':''; ?>>Em análise</option>
                                    <option value="resolvido" <?= $status==='resolvido'?'selected':''; ?>>Resolvido</option>
                                </select>
                                <button type="submit" class="btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#0078d7;">Alterar</button>
                            </form>
                            <?php endif; ?>
                        </td>
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
<?php
// Processa alteração de status
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['status']) && $auth) {
    $id = (int)$_POST['id'];
    $newStatus = $_POST['status'];
    if (isset($tickets[$id])) {
        $tickets[$id]['status'] = $newStatus;
        // Salva todos os tickets de volta no arquivo
        $lines = [];
        foreach ($tickets as $t) {
            $lines[] = json_encode($t, JSON_UNESCAPED_UNICODE);
        }
        file_put_contents($file, implode("\n", $lines));
        // Redireciona para evitar reenvio do formulário
        header('Location: tickets.php');
        exit;
    }
}
?>
