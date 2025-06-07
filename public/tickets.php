<?php
session_start();
// Processa alteração de status e deleção ANTES de qualquer saída
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    $file = __DIR__ . '/../tickets.txt';
    $tickets = [];
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $tickets = json_decode($content, true) ?: [];
    }
    // Deletar ticket (apenas se não for tecnico)
    if (isset($_POST['delete_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tecnico')) {
        $deleteId = (int)$_POST['delete_id'];
        if (isset($tickets[$deleteId])) {
            array_splice($tickets, $deleteId, 1);
            file_put_contents($file, json_encode($tickets, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            header('Location: tickets.php');
            exit;
        }
    }
    // Alterar status (tecnico ou outros podem)
    if (isset($_POST['id'], $_POST['status'])) {
        $id = (int)$_POST['id'];
        $newStatus = $_POST['status'];
        if (isset($tickets[$id])) {
            $tickets[$id]['status'] = $newStatus;
            file_put_contents($file, json_encode($tickets, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
            header('Location: tickets.php');
            exit;
        }
    }
}

// Página para listar todos os tickets registrados
$tickets = [];
$file = __DIR__ . '/../tickets.txt';
if (file_exists($file)) {
    $content = file_get_contents($file);
    $tickets = json_decode($content, true) ?: [];
}
$auth = isset($_SESSION['auth']) && $_SESSION['auth'] === true;
$role = $_SESSION['role'] ?? null;

// Permitir acesso apenas para admin e tecnico
if (!$auth || ($role !== 'admin' && $role !== 'tecnico')) {
    header('Location: login.php');
    exit;
}

// NOVO: Contagem de chamados por status para o técnico
$em_aberto = 0;
$em_andamento = 0;
$encerrados = 0;
foreach ($tickets as $ticket) {
    $status = $ticket['status'] ?? 'nao_aberto';
    if ($status === 'nao_aberto') $em_aberto++;
    elseif ($status === 'em_analise') $em_andamento++;
    elseif ($status === 'resolvido') $encerrados++;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Lista de Tickets - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
</head>
<body style="margin:0;padding:0;background:#f4f6fa;">
    <div class="container" style="max-width:none;width:100vw;padding:0 0 30px 0;">
        <h2 style="margin-left:30px;">Lista de Tickets</h2>
        <?php if ($auth): ?>
            <a href="logout.php" class="btn" style="float:right;margin-top:-40px;margin-right:30px;">Sair</a>
            <a href="open.php" class="btn" style="float:right;margin-top:-40px;margin-right:140px;">Abrir novo chamado</a>
        <?php else: ?>
            <a href="login.php" class="btn" style="float:right;margin-top:-40px;margin-right:30px;">Login</a>
        <?php endif; ?>
        <?php if ($auth): ?>
            <div style="margin: 20px 30px 0 30px; padding: 16px; background: #fff; border-radius: 8px; box-shadow: 0 1px 6px #e0e0e0; display: flex; gap: 32px; max-width: 600px;">
                <div><strong>Chamados em aberto:</strong> <span style="color:#d70022; font-weight:bold;"><?= $em_aberto ?></span></div>
                <div><strong>Em andamento:</strong> <span style="color:#ff9800; font-weight:bold;"><?= $em_andamento ?></span></div>
                <div><strong>Encerrados:</strong> <span style="color:#388e3c; font-weight:bold;"><?= $encerrados ?></span></div>
            </div>
        <?php endif; ?>
        <div style="overflow-x:auto; margin: 0 30px;">
        <table class="ticket-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Assunto</th>
                    <th>Mensagem</th>
                    <th>Imagem</th>
                    <th>Telefone</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (
                    $tickets as $i => $ticket): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($ticket['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($ticket['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($ticket['subject'] ?? '') ?></td>
                        <td style="max-width:250px;word-break:break-word;">
                            <?= nl2br(htmlspecialchars($ticket['message'] ?? '')) ?>
                        </td>
                        <td>
                            <?php if (!empty($ticket['imagePath'])): ?>
                                <a href="<?= htmlspecialchars($ticket['imagePath']) ?>" target="_blank">
                                    <img src="<?= htmlspecialchars($ticket['imagePath']) ?>" alt="Imagem" style="max-width:80px;max-height:80px;border-radius:6px;box-shadow:0 1px 4px #ccc;">
                                </a>
                            <?php else: ?>
                                <span style="color:#aaa;">-</span>
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($ticket['telefone'] ?? '') ?></td>
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
                            <form method="post" action="tickets.php" style="margin-top:5px;display:inline-block;">
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
                        <td>
                            <?php if ($auth && ($role === 'tecnico' || $role === 'admin')): ?>
                                <a href="buscarchamados.html?email=<?= urlencode($ticket['email'] ?? '') ?>" class="btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#0078d7;" target="_blank">Chat</a>
                            <?php endif; ?>
                            <?php if ($auth && $role !== 'tecnico'): ?>
                            <form method="post" action="tickets.php" style="margin-top:5px;display:inline-block;">
                                <input type="hidden" name="delete_id" value="<?= $i ?>">
                                <button type="submit" class="btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#d70022;" onclick="return confirm('Tem certeza que deseja deletar este ticket?');">Deletar</button>
                            </form>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <br>
        <!--<a href="open.php" class="btn">Abrir novo chamado</a>-->
    </div>
    <style>
    html, body {
        height: 100%;
        width: 100%;
        margin: 0;
        padding: 0;
    }
    .container {
        width: 100vw;
        max-width: none;
        margin: 0;
        padding: 0 0 30px 0;
        background: #f4f6fa;
        min-height: 100vh;
    }
    .ticket-table {
        width: 100%;
        border-collapse: collapse;
        background: #fff;
        margin-top: 20px;
        font-size: 15px;
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
    @media (max-width: 900px) {
        .ticket-table th, .ticket-table td {
            font-size: 13px;
            padding: 7px 4px;
        }
        h2 {
            font-size: 20px;
        }
    }
    </style>


</body>
</html>
