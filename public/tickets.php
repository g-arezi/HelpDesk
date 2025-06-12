<?php
require_once __DIR__ . '/api_cors.php';
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
    <style>
    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fa; margin:0; padding:0; transition: background 0.3s, color 0.3s; }
    .container { max-width: 1200px; margin: 40px auto 30px auto; background: #fff; border-radius: 18px; box-shadow: 0 6px 32px #0002; padding: 36px 40px 30px 40px; min-height: 80vh; transition: background 0.3s, color 0.3s; }
    h2 { color: #1976d2; text-align: left; font-size: 2.1rem; margin-bottom: 24px; letter-spacing: 1px; }
    .summary-box { margin: 0 0 24px 0; padding: 18px 24px; background: #f8fafd; border-radius: 10px; box-shadow: 0 2px 8px #0001; display: flex; gap: 36px; max-width: 700px; }
    .summary-box div { font-size: 1.1rem; }
    .summary-box strong { font-weight: 600; }
    .summary-box span { font-size: 1.2rem; }
    .ticket-table { width: 100%; border-collapse: separate; border-spacing: 0; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 12px #0001; margin-top: 24px; font-size: 15px; }
    .ticket-table th, .ticket-table td { padding: 13px 10px; text-align: left; border-bottom: 1px solid #e0e0e0; }
    .ticket-table th { background: #f2f6fc; color: #1976d2; font-weight: 600; font-size: 1.05rem; letter-spacing: 0.5px; }
    .ticket-table tr:last-child td { border-bottom: none; }
    .ticket-table tr { transition: background 0.2s; }
    .ticket-table tr:hover { background: #f0f4fa; }
    .btn { padding: 7px 16px; background: #0078d7; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; box-shadow: 0 1px 4px #0001; transition: background 0.2s, color 0.2s; margin: 2px 0; }
    .btn:hover { background: #0056a3; }
    .btn[style*='background:#d70022'] { background: #d70022 !important; }
    .btn[style*='background:#d70022']:hover { background: #b71c1c !important; }
    .btn[style*='background:#0078d7'] { background: #0078d7 !important; }
    .btn[style*='background:#0078d7']:hover { background: #0056a3 !important; }
    .night-toggle { position:fixed; top:24px; left:24px; z-index:1000; background:linear-gradient(90deg,#ff6b6b,#b71c1c); color:#fff; border:1px solid #b71c1c; border-radius:20px; padding:10px 22px; cursor:pointer; font-weight:bold; box-shadow:0 2px 12px #0003; font-size: 1.1rem; transition: background 0.3s, color 0.3s; }
    .night-toggle.night { background:linear-gradient(90deg,#b71c1c,#ff6b6b); color:#fff; border-color:#fff; }
    /* Modo noturno */
    body.night { background: #181c24 !important; color: #e0e0e0; }
    .container.night { background: #232a36 !important; color: #e0e0e0; box-shadow: 0 6px 32px #0006; }
    .ticket-table.night { background: #232a36 !important; color: #e0e0e0; box-shadow: 0 2px 12px #0006; }
    .ticket-table.night th { background: #263238 !important; color: #90caf9; }
    .ticket-table.night tr:hover { background-color: #222b38 !important; }
    .btn.night { background: #b71c1c !important; color: #fff; }
    .btn.night:hover { background: #ff6b6b !important; color: #fff; }
    .summary-box.night { background: #232a36 !important; color: #e0e0e0; box-shadow: 0 2px 8px #0006; }
    /* Night/Light mode switcher - canto inferior esquerdo (igual dashboard) */
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
    }
    .mode-switch.light {
        background: #e3f2fd;
        color: #1976d2;
        border: 1px solid #b3c6e0;
    }
    .mode-switch input[type="checkbox"] {
        width: 36px;
        height: 20px;
        appearance: none;
        background: #bdbdbd;
        outline: none;
        border-radius: 12px;
        position: relative;
        transition: background 0.3s;
        cursor: pointer;
    }
    .mode-switch input[type="checkbox"]:checked {
        background: #1976d2;
    }
    .mode-switch input[type="checkbox"]::before {
        content: '';
        position: absolute;
        left: 3px;
        top: 3px;
        width: 14px;
        height: 14px;
        background: #fff;
        border-radius: 50%;
        transition: left 0.3s;
    }
    .mode-switch input[type="checkbox"]:checked::before {
        left: 19px;
    }
    .mode-switch .icon {
        font-size: 1.1em;
    }
    /* Responsivo */
    @media (max-width: 1100px) {
        .container { padding: 18px 4vw 18px 4vw; }
        .summary-box { flex-direction: column; gap: 10px; }
    }
    @media (max-width: 700px) {
        .container { padding: 10px 2vw 10px 2vw; }
        .ticket-table th, .ticket-table td { font-size: 12px; padding: 7px 4px; }
        h2 { font-size: 1.2rem; }
    }
    </style>
</head>
<body style="margin:0;padding:0;background:#f4f6fa;">
    <!-- Switch de modo claro/noturno -->
    <div class="mode-switch light" id="modeSwitch">
        <span class="icon" id="modeIcon">🌞</span>
        <input type="checkbox" id="modeToggle" aria-label="Alternar modo claro/noturno">
        <span id="modeLabel">Claro</span>
    </div>
    <div class="container" id="container">
        <h2>Lista de Tickets</h2>
        <?php if ($auth): ?>
            <div class="summary-box" id="summaryBox">
                <div><strong>Chamados em aberto:</strong> <span style="color:#d70022; font-weight:bold;" id="span-aberto"><?= $em_aberto ?></span></div>
                <div><strong>Em andamento:</strong> <span style="color:#ff9800; font-weight:bold;" id="span-andamento"><?= $em_andamento ?></span></div>
                <div><strong>Encerrados:</strong> <span style="color:#388e3c; font-weight:bold;" id="span-encerrados"><?= $encerrados ?></span></div>
                <a href="dashboard.php" class="btn" id="dashboardBtn">Dashboard</a>
            </div>
        <?php endif; ?>
        <div style="overflow-x:auto; margin: 0 30px;">
        <table class="ticket-table" id="ticketTable">
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
    // Novo switch de modo
    const modeSwitch = document.getElementById('modeSwitch');
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    const modeLabel = document.getElementById('modeLabel');
    function setMode(night) {
        document.body.classList.toggle('night', night);
        document.getElementById('container').classList.toggle('night', night);
        document.getElementById('ticketTable').classList.toggle('night', night);
        document.querySelectorAll('th').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('tr').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('.btn').forEach(e=>e.classList.toggle('night', night));
        let summary = document.getElementById('summaryBox');
        if(summary) summary.classList.toggle('night', night);
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
    modeToggle.addEventListener('change', function() {
        setMode(this.checked);
    });
    // Inicialização
    if(localStorage.getItem('nightMode')) setMode(true);
    else setMode(false);
    // Remove botão antigo
    var oldBtn = document.getElementById('nightToggle');
    if(oldBtn) oldBtn.remove();
    </script>
</body>
</html>
