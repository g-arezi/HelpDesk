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
$file = __DIR__ . '/../logs/tickets.txt';
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
            <a href="open.php" class="btn" style="float:right;margin-top:-40px;margin-right:140px;">Novo chamado</a>
            <a href="dashboard.php" class="btn" style="float:right;margin-top:-40px;margin-right:350px;">Dashboard</a>
        <?php else: ?>
            <a href="login.php" class="btn" style="float:right;margin-top:-40px;margin-right:30px;">Login</a>
        <?php endif; ?>
        <?php if ($auth): ?>
            <div style="margin: 20px 30px 0 30px; padding: 16px; background: #fff; border-radius: 8px; box-shadow: 0 1px 6px #e0e0e0; display: flex; gap: 32px; max-width: 600px;">
                <div><strong>Chamados em aberto:</strong> <span style="color:#d70022; font-weight:bold;" id="span-aberto"><?= $em_aberto ?></span></div>
                <div><strong>Em andamento:</strong> <span style="color:#ff9800; font-weight:bold;" id="span-andamento"><?= $em_andamento ?></span></div>
                <div><strong>Encerrados:</strong> <span style="color:#388e3c; font-weight:bold;" id="span-encerrados"><?= $encerrados ?></span></div>
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
            <tbody id="tickets-tbody">
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
                            <form method="post" action="tickets.php" style="margin-top:5px;display:inline-block;">
                                <input type="hidden" name="id" value="<?= $i ?>">
                                <select name="status" style="padding:2px 6px;">
                                    <option value="nao_aberto" <?= $status==='nao_aberto'?'selected':''; ?>>Não aberto</option>
                                    <option value="em_analise" <?= $status==='em_analise'?'selected':''; ?>>Em análise</option>
                                    <option value="resolvido" <?= $status==='resolvido'?'selected':''; ?>>Resolvido</option>
                                </select>
                                <button type="submit" class="btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#0078d7;">Alterar</button>
                            </form>
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
    </div>
    <script>
    function renderTickets(data) {
        // Atualiza contadores
        document.getElementById('span-aberto').innerText = data.chamados.aberto;
        document.getElementById('span-andamento').innerText = data.chamados.analise;
        document.getElementById('span-encerrados').innerText = data.chamados.resolvido;
        // Atualiza tabela
        let tbody = document.getElementById('tickets-tbody');
        tbody.innerHTML = '';
        data.tickets.forEach(function(ticket, i) {
            let row = document.createElement('tr');
            row.innerHTML = `
                <td>${i+1}</td>
                <td>${ticket.name ? escapeHtml(ticket.name) : ''}</td>
                <td>${ticket.email ? escapeHtml(ticket.email) : ''}</td>
                <td>${ticket.subject ? escapeHtml(ticket.subject) : ''}</td>
                <td style='max-width:250px;word-break:break-word;'>${ticket.message ? escapeHtml(ticket.message).replace(/\n/g,'<br>') : ''}</td>
                <td>${ticket.imagePath ? `<a href='${escapeHtml(ticket.imagePath)}' target='_blank'><img src='${escapeHtml(ticket.imagePath)}' alt='Imagem' style='max-width:80px;max-height:80px;border-radius:6px;box-shadow:0 1px 4px #ccc;'></a>` : '<span style=\"color:#aaa;\">-</span>'}</td>
                <td>${ticket.telefone ? escapeHtml(ticket.telefone) : ''}</td>
                <td>${renderStatus(ticket.status,i)}</td>
                <td>
                    <a href='buscarchamados.html?email=${encodeURIComponent(ticket.email ? ticket.email : '')}' class='btn' style='padding:2px 10px;font-size:13px;margin-left:4px;background:#0078d7;' target='_blank'>Chat</a>
                    <button class='btn' style='padding:2px 10px;font-size:13px;margin-left:4px;background:#d70022;' onclick='deleteTicket(${i})'>Deletar</button>
                </td>
            `;
            tbody.appendChild(row);
        });
    }
    function escapeHtml(text) {
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    function renderStatus(status, i) {
        let label = '';
        if(status==='resolvido') label = '<span style="color:green;font-weight:bold;">Resolvido</span>';
        else if(status==='em_analise') label = '<span style="color:orange;font-weight:bold;">Em análise</span>';
        else label = '<span style="color:red;font-weight:bold;">Não aberto</span>';
        return label + `<select onchange='changeStatus(${i}, this.value)' style='padding:2px 6px;'><option value='nao_aberto' ${status==='nao_aberto'?'selected':''}>Não aberto</option><option value='em_analise' ${status==='em_analise'?'selected':''}>Em análise</option><option value='resolvido' ${status==='resolvido'?'selected':''}>Resolvido</option></select>`;
    }
    function changeStatus(id, status) {
        fetch('update_ticket_status.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id, status })
        }).then(r => r.json()).then(res => {
            if(res.success) updateTickets();
            else alert('Erro ao alterar status!');
        });
    }
    function deleteTicket(id) {
        if(!confirm('Tem certeza que deseja deletar este ticket?')) return;
        fetch('delete_ticket.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ id })
        }).then(r => r.json()).then(res => {
            if(res.success) updateTickets();
            else alert('Erro ao deletar!');
        });
    }
    function updateTickets() {
        fetch('dashboard_data.php')
            .then(r => r.json())
            .then(renderTickets);
    }
    setInterval(updateTickets, 3000);
    window.onload = updateTickets;
    </script>
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
