<?php
session_start();
// Para ambiente de desenvolvimento/teste, definir perfil admin se não estiver logado
if (!isset($_SESSION['perfil'])) {
    $_SESSION['perfil'] = 'admin';
}

// Simulação de autenticação (substitua pelo seu sistema real)
// $_SESSION['perfil'] = 'admin'; // ou 'tecnico'
if (!in_array($_SESSION['perfil'], ['admin', 'tecnico'])) {
    header('Location: login.php');
    exit;
}

// Leitura real dos chamados
$ticketsFile = __DIR__ . '/../logs/tickets.txt';
$chamados = [
    'aberto' => 0, // não aberto
    'analise' => 0, // em analise
    'resolvido' => 0
];
$tickets = [];
if (file_exists($ticketsFile)) {
    $tickets = json_decode(file_get_contents($ticketsFile), true);
    if (!is_array($tickets)) $tickets = [];
    foreach ($tickets as $t) {
        $status = strtolower($t['status'] ?? 'nao_aberto');
        if ($status === 'nao_aberto') $chamados['aberto']++;
        elseif ($status === 'em_analise') $chamados['analise']++;
        elseif ($status === 'resolvido') $chamados['resolvido']++;
        else $chamados['aberto']++;
    }
}

function card($color, $icon, $label, $count) {
    return "<div style='flex:1;min-width:180px;margin:10px;padding:20px;background:$color;color:#fff;border-radius:10px;box-shadow:0 2px 8px #0001;text-align:center;'>
        <div style='font-size:2.5em;margin-bottom:10px;'>$icon</div>
        <div style='font-size:1.2em;font-weight:bold;'>$label</div>
        <div style='font-size:2em;margin-top:5px;'>$count</div>
    </div>";
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard - Helpdesk</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f8fb; margin:0; }
        .sidebar { width:220px; background:#e3f2fd; height:100vh; position:fixed; left:0; top:0; padding:30px 0; }
        .sidebar h2 { color:#1976d2; text-align:center; margin-bottom:30px; }
        .sidebar a { display:block; color:#1976d2; text-decoration:none; padding:12px 30px; margin:8px 0; border-radius:6px; }
        .sidebar a:hover { background:#bbdefb; }
        .main { margin-left:240px; padding:30px; }
        .header { display:flex; justify-content:space-between; align-items:center; }
        .header h1 { color:#1976d2; }
        .logout { background:#d32f2f; color:#fff; border:none; padding:8px 18px; border-radius:5px; cursor:pointer; }
        .cards { display:flex; gap:20px; margin:30px 0 20px 0; flex-wrap:wrap; }
        .section { background:#fff; border-radius:10px; box-shadow:0 2px 8px #0001; padding:20px; margin-bottom:20px; }
        .section h3 { color:#1976d2; margin-top:0; }
        .chat-link { display:inline-block; margin:10px 0 0 0; background:#1976d2; color:#fff; padding:8px 16px; border-radius:6px; text-decoration:none; font-weight:bold; }
        .chat-link:hover { background:#1565c0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { padding: 10px; text-align: left; border-bottom: 1px solid #ddd; }
        th { background-color: #f2f2f2; }
        tr:hover { background-color: #f5f5f5; }
        .btn { padding: 6px 12px; background-color: #0078d7; color: white; border: none; border-radius: 4px; cursor: pointer; }
        .btn:hover { background-color: #0056a3; }
        @media (max-width:900px) { .main { margin-left:0; } .sidebar { position:static; width:100%; height:auto; } .cards { flex-direction:column; } }
    </style>
</head>
<body>
    <div class="sidebar">
        <h2>Helpdesk System</h2>
        <a href="dashboard.php">Home</a>
        <a href="tickets.php">Tickets</a>
        <a href="dashboard.php">Dashboard</a>
    </div>
    <div class="main">
        <div class="header">
            <h1>Dashboard</h1>
            <form method="post" action="logout.php" style="margin:0;">
                <button class="logout">Logout</button>
            </form>
        </div>
        <div style="margin-bottom:10px;">
            <span style="color:#d32f2f;font-weight:bold;">Chamados em aberto: <span id="span-aberto"><?=$chamados['aberto']?></span></span>
            <span style="color:#fbc02d;font-weight:bold;margin-left:20px;">Em análise: <span id="span-analise"><?=$chamados['analise']?></span></span>
            <span style="color:#388e3c;font-weight:bold;margin-left:20px;">Encerrados: <span id="span-resolvido"><?=$chamados['resolvido']?></span></span>
        </div>
        <div class="cards">
            <div id="card-aberto" style="flex:1;min-width:180px;margin:10px;padding:20px;background:#43a047;color:#fff;border-radius:10px;box-shadow:0 2px 8px #0001;text-align:center;">
                <div style='font-size:2.5em;margin-bottom:10px;'>&#128994;</div>
                <div style='font-size:1.2em;font-weight:bold;'>NÃO ABERTO</div>
                <div id="card-aberto-value" style='font-size:2em;margin-top:5px;'><?=$chamados['aberto']?></div>
            </div>
            <div id="card-analise" style="flex:1;min-width:180px;margin:10px;padding:20px;background:#fbc02d;color:#fff;border-radius:10px;box-shadow:0 2px 8px #0001;text-align:center;">
                <div style='font-size:2.5em;margin-bottom:10px;'>&#128308;</div>
                <div style='font-size:1.2em;font-weight:bold;'>EM ANÁLISE</div>
                <div id="card-analise-value" style='font-size:2em;margin-top:5px;'><?=$chamados['analise']?></div>
            </div>
            <div id="card-resolvido" style="flex:1;min-width:180px;margin:10px;padding:20px;background:#d32f2f;color:#fff;border-radius:10px;box-shadow:0 2px 8px #0001;text-align:center;">
                <div style='font-size:2.5em;margin-bottom:10px;'>&#128274;</div>
                <div style='font-size:1.2em;font-weight:bold;'>RESOLVIDO</div>
                <div id="card-resolvido-value" style='font-size:2em;margin-top:5px;'><?=$chamados['resolvido']?></div>
            </div>
        </div>
        <div class="section">
            <h3>Chat do Chamado</h3>
            <form method="get" action="chat_frontend.html" target="_blank" style="margin-top:10px;">
                <label for="chat_id">ID do chamado:</label>
                <input type="number" min="1" name="id" id="chat_id" required style="width:80px;">
                <button type="submit" class="chat-link">Abrir Chat</button>
            </form>
        </div>
        <div class="section">
            <h3>Lista de Tickets</h3>
            <div style="overflow-x:auto; margin: 0 0 20px 0;">
            <table class="ticket-table" style="width:100%;background:#fff;border-radius:8px;box-shadow:0 1px 6px #e0e0e0;">
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
                    <?php foreach ($tickets as $i => $ticket): ?>
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
                            <form method="post" action="dashboard.php" style="margin-top:5px;display:inline-block;">
                                <input type="hidden" name="id" value="<?= $i ?>">
                                <select name="status" style="padding:2px 6px;">
                                    <option value="nao_aberto" <?= $status==='nao_aberto'?'selected':''; ?>>Não aberto</option>
                                    <option value="em_analise" <?= $status==='em_analise'?'selected':''; ?>>Em análise</option>
                                    <option value="resolvido" <?= $status==='resolvido'?'selected':''; ?>>Resolvido</option>
                                </select>
                                <button type="submit" class="btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#0078d7;color:#fff;">Alterar</button>
                            </form>
                        </td>
                        <td>
                            <a href="chat_frontend.html?id=<?= $i+1 ?>" class="btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#0078d7;color:#fff;" target="_blank">Chat</a>
                            <form method="post" action="dashboard.php" style="margin-top:5px;display:inline-block;">
                                <input type="hidden" name="delete_id" value="<?= $i ?>">
                                <button type="submit" class="btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#d70022;color:#fff;" onclick="return confirm('Tem certeza que deseja deletar este ticket?');">Deletar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
    </div>
    <script>
    function renderDashboard(data) {
        // Atualiza cards
        document.getElementById('card-aberto-value').innerText = data.chamados.aberto;
        document.getElementById('card-analise-value').innerText = data.chamados.analise;
        document.getElementById('card-resolvido-value').innerText = data.chamados.resolvido;
        document.getElementById('span-aberto').innerText = data.chamados.aberto;
        document.getElementById('span-analise').innerText = data.chamados.analise;
        document.getElementById('span-resolvido').innerText = data.chamados.resolvido;
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
                    <a href='chat_frontend.html?id=${i+1}' class='btn' style='padding:2px 10px;font-size:13px;margin-left:4px;background:#0078d7;color:#fff;' target='_blank'>Chat</a>
                    <button class='btn' style='padding:2px 10px;font-size:13px;margin-left:4px;background:#d70022;color:#fff;' onclick='deleteTicket(${i})'>Deletar</button>
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
            if(res.success) updateDashboard();
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
            if(res.success) updateDashboard();
            else alert('Erro ao deletar!');
        });
    }
    function updateDashboard() {
        fetch('dashboard_data.php')
            .then(r => r.json())
            .then(renderDashboard);
    }
    setInterval(updateDashboard, 3000);
    window.onload = updateDashboard;
    </script>
</body>
</html>
