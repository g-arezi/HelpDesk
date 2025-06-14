<?php
require_once __DIR__ . '/api_cors.php';
session_start();
// Processa alteração de status e deleção ANTES de qualquer saída
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
    $file = __DIR__ . '/../logs/tickets.txt';
    $tickets = [];
    if (file_exists($file)) {
        $content = file_get_contents($file);
        $tickets = json_decode($content, true) ?: [];
    }
    
    // Deletar ticket (apenas se não for tecnico)
    if (isset($_POST['delete_id']) && (!isset($_SESSION['role']) || $_SESSION['role'] !== 'tecnico')) {
        $deleteId = (int)$_POST['delete_id'];
        if (isset($tickets[$deleteId])) {
            // Remove o ticket pelo índice
            array_splice($tickets, $deleteId, 1);
            // Reindexar array após remover o item
            $tickets = array_values($tickets);
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
            // Nova label
            if (isset($_POST['label_extra'])) {
                $tickets[$id]['label_extra'] = $_POST['label_extra'];
            }
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
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Lista de Tickets - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/mobile.css">
    <style>    body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f6fa; margin:0; padding:0; transition: background 0.3s, color 0.3s; }
    .container { max-width: 1200px; margin: 20px auto; background: #fff; border-radius: 18px; box-shadow: 0 6px 32px #0002; padding: 20px; min-height: 80vh; transition: background 0.3s, color 0.3s; }
    h2 { color:rgb(2, 2, 2); text-align: left; font-size: 2.1rem; margin-bottom: 24px; letter-spacing: 1px; }
    body.night h2 { color: #fff; }
    .summary-box { margin: 0 0 24px 0; padding: 18px 24px; background: #f8fafd; border-radius: 10px; box-shadow: 0 2px 8px #0001; display: flex; gap: 36px; max-width: 700px; }
    .summary-box div { font-size: 1.1rem; }
    .summary-box strong { font-weight: 600; }
    .summary-box span { font-size: 1.2rem; }
    .ticket-table { width: 100%; border-collapse: separate; border-spacing: 0; background: #fff; border-radius: 10px; overflow: hidden; box-shadow: 0 2px 12px #0001; margin-top: 24px; font-size: 15px; }
    .ticket-table th, .ticket-table td { padding: 13px 10px; text-align: left; border-bottom: 1px solid #e0e0e0; }
    .ticket-table th { 
        background: #f2f6fc; 
        color: #111; /* Light mode: preto */
        font-weight: 600; 
        font-size: 1.05rem; 
        letter-spacing: 0.5px; 
    }
    .ticket-table.night th {
        background: #263238 !important;
        color: #fff !important; /* Night mode: branco */
    }
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
    .night-toggle.night { background:linear-gradient(90deg,#b71c1c,#ff6b6b); color:#fff; border-color:#fff; }    /* Modo noturno */
    body.night { background: #181c24 !important; color: #e0e0e0; }
    .container.night { background: #232a36 !important; color: #e0e0e0; box-shadow: 0 6px 32px #0006; }
    .ticket-table.night { background: #232a36 !important; color: #e0e0e0; box-shadow: 0 2px 12px #0006; }
    .ticket-table.night th { background: #263238 !important; color: #90caf9; }
    .ticket-table.night tr:hover { background-color: #222b38 !important; }
    .btn.night { background: #b71c1c !important; color: #fff; }
    .btn.night:hover { background: #ff6b6b !important; color: #fff; }
    .summary-box.night { background: #232a36 !important; color: #e0e0e0; box-shadow: 0 2px 8px #0006; }
    
    /* Aprimoramentos do modo noturno */
    .ticket-table.night td { border-bottom: 1px solid #2c3e50 !important; }
    .ticket-table.night select, 
    .ticket-table.night input { background: #2a2a3d !important; color: #e0e0e0 !important; border: 1px solid #3e3e5c !important; }
    
    /* Mobile específico - modo noturno */
    .mobile-view-toggle.night { background: rgba(35, 42, 54, 0.95) !important; }
    #toggleView.night { background: #0d47a1 !important; color: #fff !important; box-shadow: 0 2px 10px rgba(0, 0, 0, 0.4) !important; }
    .table-scroll-hint.night { color: #90caf9 !important; }
    .ticket-table-container.night { background: #232a36 !important; }
    
    /* Melhorias para o modo card no noturno */
    .ticket-table-mobile-view.night tr { background: #232a36 !important; box-shadow: 0 2px 12px rgba(0, 0, 0, 0.5) !important; border: 1px solid #2c3e50 !important; }
    .ticket-table-mobile-view.night td { border-bottom: 1px dashed #36404c !important; }
    .ticket-table-mobile-view.night td:before { color: #90caf9 !important; }
    .ticket-table-mobile-view.night td[data-label="💬 Mensagem"] { background: #1e2530 !important; }
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
    }    /* Force visibility of night mode switch */
    .mode-switch {
        position: fixed !important;
        left: 18px !important;
        bottom: 18px !important;
        z-index: 9999 !important; 
        display: flex !important;
        opacity: 1 !important;
        visibility: visible !important;
        border: 2px solid #1976d2 !important;
        box-shadow: 0 2px 20px rgba(0, 0, 0, 0.3) !important;
        pointer-events: auto !important;
        transform: scale(1.05);
    }    
    /* Preload night mode to prevent flashing */
    .night-mode-preload {
        background-color: #181c24 !important;
        color: #e0e0e0 !important;
    }
    </style>
    <script>
        // Force night mode if stored in localStorage
        if (localStorage.getItem('nightMode') === '1') {
            document.documentElement.classList.add('night-mode-preload');
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    document.documentElement.classList.remove('night-mode-preload');
                }, 100);
            });
        }
    </script>
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
                <div><strong>⚠️ Em aberto:</strong> <span style="color:#d70022; font-weight:bold;" id="span-aberto"><?= $em_aberto ?></span></div>
                <div><strong>⏳ Em andamento:</strong> <span style="color:#ff9800; font-weight:bold;" id="span-andamento"><?= $em_andamento ?></span></div>
                <div><strong>✅ Encerrados:</strong> <span style="color:#388e3c; font-weight:bold;" id="span-encerrados"><?= $encerrados ?></span></div>
                <a href="dashboard.php" class="btn" id="dashboardBtn">Dashboard</a>
            </div>
        <?php endif; ?>        <!-- Indicador de rolagem para mobile -->
        <div class="table-scroll-hint" id="scrollHint" style="display: none;">
            ← Deslize para ver todos os dados →
        </div>
        
        <div style="overflow-x: auto; margin: 0; -webkit-overflow-scrolling: touch;" class="ticket-table-container">        <!-- Botão para alternar entre visualização de tabela normal e mobile -->
        <div class="mobile-view-toggle" style="margin-bottom: 15px; text-align: center; display: none;">
            <button id="toggleView" class="btn" style="width: auto !important; padding: 10px 20px !important; font-size: 15px !important; display: inline-block !important; background: #1976D2; color: white; border-radius: 20px; box-shadow: 0 2px 5px rgba(0,0,0,0.2);">
                Alternar Visualização
            </button>
        </div>
        
        <table class="ticket-table" id="ticketTable">            <thead>
                <tr>
                    <th>#️⃣ ID</th>
                    <th>👤 Usuário</th>
                    <th>👑 Usuário do Painel</th>
                    <th>📦 Produto</th>
                    <th>📝 Assunto</th>
                    <th>💬 Mensagem</th>
                    <th>🖼️ Imagem</th>
                    <th>🔖 Status</th>
                    <th>⚙️ Ações</th>
                </tr>
            </thead>
            <tbody id="tickets-tbody">
                <?php foreach ($tickets as $i => $ticket): ?>                    <tr>
                        <td data-label="#️⃣ ID"><?= $i + 1 ?></td>
                        <td data-label="👤 Usuário">
                        <?php 
                            if (isset($ticket['user'])) {
                                echo htmlspecialchars($ticket['user']);
                            } elseif (isset($ticket['created_by']) && isset($ticket['created_by']['username'])) {
                                echo htmlspecialchars($ticket['created_by']['username']);
                            } elseif (isset($ticket['name'])) {
                                // Compatibilidade com tickets antigos
                                echo htmlspecialchars($ticket['name']);
                            } else {
                                echo 'Usuário desconhecido';
                            }
                        ?>                        </td>                        <td data-label="👑 Usuário do Painel">
                        <?php 
                            // Obtém o username do painel com ordem de prioridade
                            if (isset($ticket['created_by']) && isset($ticket['created_by']['panel_username']) && !empty($ticket['created_by']['panel_username'])) {
                                echo htmlspecialchars($ticket['created_by']['panel_username']);
                            } elseif (isset($ticket['username_painel']) && !empty($ticket['username_painel'])) {
                                echo htmlspecialchars($ticket['username_painel']);
                            } elseif (isset($ticket['created_by']) && isset($ticket['created_by']['username']) && !empty($ticket['created_by']['username'])) {
                                // Fallback para username do created_by
                                echo htmlspecialchars($ticket['created_by']['username']);
                            } elseif (isset($ticket['user']) && !empty($ticket['user'])) {
                                // Fallback para o campo user
                                echo htmlspecialchars($ticket['user']);
                            } else {
                                echo '-';
                            }
                        ?>
                        </td>
                        <td data-label="📦 Produto"><?= htmlspecialchars($ticket['produto'] ?? '') ?></td>
                        <td data-label="📝 Assunto"><?= htmlspecialchars($ticket['subject'] ?? '') ?></td>
                        <td data-label="💬 Mensagem" style="max-width:250px;word-break:break-word;">
                            <?php
                            // Exibe campos extras para filmes/séries
                            if (($ticket['produto'] ?? '') === 'filmes') {
                                if (!empty($ticket['filmes_obse_label'])) echo '<b>Opção:</b> ' . htmlspecialchars($ticket['filmes_obse_label']) . '<br>';
                                elseif (!empty($ticket['filmes_obse'])) echo '<b>Opção:</b> ' . htmlspecialchars($ticket['filmes_obse']) . '<br>';
                                echo '<b>🍿 Filme:</b> ' . htmlspecialchars($ticket['filme_nome'] ?? '-') . '<br>';
                                echo '<b>🌟 TMDB:</b> ' . htmlspecialchars($ticket['filme_tmdb'] ?? '-') . '<br>';
                                if (!empty($ticket['filme_obs'])) echo '<b>⚠ OBS:</b> ' . htmlspecialchars($ticket['filme_obs']) . '<br>';
                                echo '<hr style="margin:4px 0;">';
                            } elseif (($ticket['produto'] ?? '') === 'series') {
                                if (!empty($ticket['series_obse_label'])) echo '<b>Opção:</b> ' . htmlspecialchars($ticket['series_obse_label']) . '<br>';
                                elseif (!empty($ticket['series_obse'])) echo '<b>Opção:</b> ' . htmlspecialchars($ticket['series_obse']) . '<br>';
                                echo '<b>📽 Série:</b> ' . htmlspecialchars($ticket['serie_nome'] ?? '-') . '<br>';
                                echo '<b>🌟 TMDB:</b> ' . htmlspecialchars($ticket['serie_tmdb'] ?? '-') . '<br>';
                                if (!empty($ticket['serie_obs'])) echo '<b>⚠ OBS:</b> ' . htmlspecialchars($ticket['serie_obs']) . '<br>';
                                echo '<hr style="margin:4px 0;">';
                            }
                            ?>
                            <?= nl2br(htmlspecialchars($ticket['message'] ?? '')) ?>
                        </td>                        <td data-label="🖼️ Imagem">
                            <?php if (!empty($ticket['imagePath'])): ?>
                                <a href="<?= htmlspecialchars($ticket['imagePath']) ?>" target="_blank">
                                    <img src="<?= htmlspecialchars($ticket['imagePath']) ?>" alt="Imagem" style="max-width:80px;max-height:80px;border-radius:6px;box-shadow:0 1px 4px #ccc;">
                                </a>
                            <?php else: ?>
                                <span style="color:#aaa;">-</span>
                            <?php endif; ?>
                        </td>
                        <td data-label="🔖 Status">
                            <?php 
                            $status = isset($ticket['status']) ? $ticket['status'] : 'nao_aberto';
                            $statusLabel = [
                                'resolvido' => '<span style="color:green;font-weight:bold;">✅ Resolvido</span>',
                                'em_analise' => '<span style="color:orange;font-weight:bold;">⏳ Em análise</span>',
                                'nao_aberto' => '<span style="color:red;font-weight:bold;">🔒 Não aberto</span>'
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
                        <td data-label="⚙️ Ações">
                            <?php if ($auth && ($role === 'tecnico' || $role === 'admin')): ?>
                                <a href="buscarchamados.html?email=<?= urlencode(isset($ticket['created_by']['email']) ? $ticket['created_by']['email'] : ($ticket['email'] ?? '')) ?>" class="btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#43a047;color:#fff;" target="_blank">Chat</a>
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
            </tbody>        </table>
        </div>
    </div>
    <script>function renderTickets(data) {
        // Atualiza contadores
        document.getElementById('span-aberto').innerText = data.chamados.aberto;
        document.getElementById('span-andamento').innerText = data.chamados.analise;
        document.getElementById('span-encerrados').innerText = data.chamados.resolvido;
        
        // Verifica se o modo noturno está ativo antes de atualizar a tabela
        const isNightMode = document.body.classList.contains('night');
        
        // Atualiza tabela
        let tbody = document.getElementById('tickets-tbody');
        tbody.innerHTML = '';        data.tickets.forEach(function(ticket, i) {
            let row = document.createElement('tr');
            
            // Adiciona classe night se o modo noturno estiver ativo
            if (isNightMode) {
                row.classList.add('night');
            }
            
            // Adiciona atributos data-label para visualização mobile
            row.innerHTML = `
                <td data-label="#️⃣ ID">${i+1}</td>
                <td data-label="👤 Usuário">${ticket.created_by && ticket.created_by.username ? escapeHtml(ticket.created_by.username) : (ticket.user ? escapeHtml(ticket.user) : (ticket.name ? escapeHtml(ticket.name) : 'Usuário desconhecido'))}</td>
                <td data-label="👑 Usuário do Painel">${getPanelUsername(ticket)}</td>
                <td data-label="📦 Produto">${ticket.produto ? escapeHtml(ticket.produto) : ''}</td>
                <td data-label="📝 Assunto">${ticket.subject ? escapeHtml(ticket.subject) : ''}</td>
                <td data-label="💬 Mensagem" style='max-width:250px;word-break:break-word;'>${ticket.message ? escapeHtml(ticket.message).replace(/\n/g,'<br>') : ''}</td>
                <td data-label="🖼️ Imagem">${ticket.imagePath ? `<a href='${escapeHtml(ticket.imagePath)}' target='_blank'><img src='${escapeHtml(ticket.imagePath)}' alt='Imagem' style='max-width:80px;max-height:80px;border-radius:6px;box-shadow:0 1px 4px #ccc;'></a>` : '<span style=\"color:#aaa;\">-</span>'}</td>
                <td data-label="🔖 Status">${renderStatus(ticket.status,i)}</td>
                <td data-label="⚙️ Ações">
                    <a href='buscarchamados.html?email=${encodeURIComponent(ticket.created_by && ticket.created_by.email ? ticket.created_by.email : (ticket.email ? ticket.email : ''))}' class='btn' style='padding:2px 10px;font-size:13px;margin-left:4px;background:#43a047;color:#fff;' target='_blank'>Chat</a>
                    <button class='btn' style='padding:2px 10px;font-size:13px;margin-left:4px;background:#d70022;color:#fff;' onclick='deleteTicket(${i})'>Deletar</button>
                </td>
            `;
            tbody.appendChild(row);
        });
          // Aplica o modo de visualização atual (mobile ou normal)
        applyCurrentViewMode();
        
        // Reaplica estilos de night mode após atualizar a tabela
        if (isNightMode) {
            const ticketTable = document.getElementById('ticketTable');
            ticketTable.classList.add('night');
            ticketTable.querySelectorAll('tr').forEach(tr => tr.classList.add('night'));
            ticketTable.querySelectorAll('th').forEach(th => th.classList.add('night'));
            ticketTable.querySelectorAll('td').forEach(td => td.classList.add('night'));
            
            // Atualiza também os botões dentro da tabela
            ticketTable.querySelectorAll('.btn').forEach(btn => btn.classList.add('night'));
        }
    }
    function escapeHtml(text) {
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.replace(/[&<>"']/g, function(m) { return map[m]; });
    }    function getPanelUsername(ticket) {
        // Try to get panel username from various possible locations
        if (ticket.created_by && ticket.created_by.panel_username) {
            return escapeHtml(ticket.created_by.panel_username);
        } else if (ticket.username_painel) {
            return escapeHtml(ticket.username_painel);
        } else if (ticket.created_by && ticket.created_by.username) {
            // Fallback to username if no panel username exists
            return escapeHtml(ticket.created_by.username);
        } else if (ticket.user) {
            // Further fallback to user if no created_by exists
            return escapeHtml(ticket.user);
        }
        return '-';
    }
    
    function renderStatus(status, i) {
        let label = '';
        if(status==='resolvido') label = '<span style="color:green;font-weight:bold;">✅ Resolvido</span>';
        else if(status==='em_analise') label = '<span style="color:orange;font-weight:bold;">⏳ Em análise</span>';
        else label = '<span style="color:red;font-weight:bold;">🔒 Não aberto</span>';
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
    }    function updateTickets() {
        fetch('dashboard_data.php')
            .then(r => r.json())
            .then(renderTickets);
    }    setInterval(updateTickets, 3000);    // Aplicar imediatamente para evitar flash
    (function() {
        if (localStorage.getItem('nightMode') === '1') {
            setMode(true);
        }
    })();
    
    // E também quando carregar completamente a página
    window.onload = function() {
        updateTickets();
        initializeNightMode();
    }
      // Função global para inicializar o modo noturno
    function initializeNightMode() {
        console.log('[TICKETS.PHP] Initializing night mode globally');
        const modeSwitch = document.getElementById('modeSwitch');
        const modeToggle = document.getElementById('modeToggle');
        
        console.log('[TICKETS.PHP] modeSwitch element:', modeSwitch);
        console.log('[TICKETS.PHP] modeToggle element:', modeToggle);
        
        if (modeSwitch) {
            modeSwitch.style.display = 'flex';
            modeSwitch.style.opacity = '1';
            modeSwitch.style.visibility = 'visible';
            modeSwitch.style.zIndex = '9999';
            console.log('[TICKETS.PHP] Mode switch made visible');
        } else {
            console.error('[TICKETS.PHP] Mode switch element not found!');
        }
        
        if (modeToggle) {
            console.log('[TICKETS.PHP] Night mode in localStorage:', localStorage.getItem('nightMode'));
            if (localStorage.getItem('nightMode') === '1') {
                console.log('[TICKETS.PHP] Setting night mode from localStorage');
                setMode(true);
                modeToggle.checked = true;
            }
            
            modeToggle.addEventListener('change', function() {
                console.log('[TICKETS.PHP] Toggle changed to:', this.checked);
                setMode(this.checked);
            });
        } else {
            console.error('[TICKETS.PHP] Mode toggle element not found!');
        }
    }
    
    // Novo switch de modo
    const modeSwitch = document.getElementById('modeSwitch');
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    const modeLabel = document.getElementById('modeLabel');    function setMode(night) {
        console.log('Setting night mode:', night);
        
        // Diretamente aplicar ou remover classes para garantir funcionamento
        if (night) {
            // Elementos principais
            document.body.classList.add('night');
            document.getElementById('container').classList.add('night');
            
            // Grupos de elementos
            document.querySelectorAll('.btn').forEach(e => e.classList.add('night'));
            document.querySelectorAll('.action-btn').forEach(e => e.classList.add('night'));
            document.querySelectorAll('a').forEach(e => e.classList.add('night'));
        } else {
            // Elementos principais
            document.body.classList.remove('night');
            document.getElementById('container').classList.remove('night');
            
            // Grupos de elementos
            document.querySelectorAll('.btn').forEach(e => e.classList.remove('night'));
            document.querySelectorAll('.action-btn').forEach(e => e.classList.remove('night'));
            document.querySelectorAll('a').forEach(e => e.classList.remove('night'));
        }
        
        // Elementos específicos
        let summary = document.getElementById('summaryBox');
        if(summary) summary.classList.toggle('night', night);
        
        // Toggle mode switch state
        if (modeSwitch) {
            modeSwitch.classList.toggle('light', !night);
            modeSwitch.classList.toggle('night', night);
            modeSwitch.style.display = 'flex';
            modeSwitch.style.opacity = '1';
        }
        
        if (modeToggle) {
            modeToggle.checked = night;
        }
        
        // Adiciona classe night à tabela inteira para afetar visualização em cards
        const ticketTable = document.getElementById('ticketTable');
        if (ticketTable) {
            ticketTable.classList.toggle('night', night);
            
            // Adicionar classe night em todos os elementos da tabela
            ticketTable.querySelectorAll('tr').forEach(tr => {
                tr.classList.toggle('night', night);
            });
            
            ticketTable.querySelectorAll('th').forEach(th => {
                th.classList.toggle('night', night);
            });
            
            ticketTable.querySelectorAll('td').forEach(td => {
                td.classList.toggle('night', night);
            });
            
            // Tratamento específico para visualização mobile (cards)
            if (ticketTable.classList.contains('ticket-table-mobile-view')) {
                ticketTable.querySelectorAll('tr').forEach(tr => {
                    tr.querySelectorAll('td').forEach(td => td.classList.toggle('night', night));
                });
            }
        }
        
        // Mobile específico
        document.querySelectorAll('.mobile-view-toggle').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('.table-scroll-hint').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('.ticket-table-container').forEach(e=>e.classList.toggle('night', night));
        
        // Elementos de formulário
        document.querySelectorAll('input, select, textarea').forEach(e=>e.classList.toggle('night', night));
        
        // Texto e ícones
        if(night) {
            modeIcon.textContent = '🌙';
            modeLabel.textContent = 'Noturno';
            localStorage.setItem('nightMode','1');
            
            // Atualizar botão de toggle de visualização no modo noturno
            let toggleBtn = document.getElementById('toggleView');
            if(toggleBtn) toggleBtn.classList.add('night');
            
            // Atualizar hint de rolagem
            let scrollHint = document.getElementById('scrollHint');
            if(scrollHint) scrollHint.classList.add('night');
        } else {
            modeIcon.textContent = '🌞';
            modeLabel.textContent = 'Claro';
            localStorage.removeItem('nightMode');
            
            // Atualizar botão de toggle de visualização no modo claro
            let toggleBtn = document.getElementById('toggleView');
            if(toggleBtn) toggleBtn.classList.remove('night');
            
            // Atualizar hint de rolagem
            let scrollHint = document.getElementById('scrollHint');
            if(scrollHint) scrollHint.classList.remove('night');
        }
    }
    
    // Remove botão antigo
    var oldBtn = document.getElementById('nightToggle');
    if(oldBtn) oldBtn.remove();
        // Garante que o night mode funcione em todos os elementos
    document.addEventListener('DOMContentLoaded', function() {
        // Certifica-se de que temos acesso aos elementos do DOM
        const modeSwitch = document.getElementById('modeSwitch');
        const modeToggle = document.getElementById('modeToggle');
        
        if (modeToggle) {
            // Inicializa o modo correto
            if(localStorage.getItem('nightMode')) {
                setMode(true);
                modeToggle.checked = true;
            } else {
                setMode(false);
                modeToggle.checked = false;
            }
            
            // Adiciona listener para o toggle de modo
            modeToggle.addEventListener('change', function() {
                setMode(this.checked);
            });
            
            // Garante que o modo switch seja visível
            if (modeSwitch) {
                modeSwitch.style.display = 'flex';
                modeSwitch.style.opacity = '1';
                modeSwitch.style.zIndex = '1000';
            }
        } else {
            console.error('Elemento de toggle de modo noturno não encontrado!');
        }
    });
    
    // Funções para melhorar responsividade e visibilidade mobile
      // Verificar se está em um dispositivo móvel
    function isMobile() {
        return window.innerWidth <= 768;
    }
    
    // Verificar se está em um dispositivo muito pequeno (iPhone, etc)
    function isSmallDevice() {
        return window.innerWidth <= 390;
    }
    
    // Alternar entre visualização normal e mobile para a tabela
    let mobileViewActive = false;
    const toggleViewBtn = document.getElementById('toggleView');
    const ticketTable = document.getElementById('ticketTable');
    const scrollHint = document.getElementById('scrollHint');
    
    // Mostrar o botão de alternar visualização e o indicador de rolagem em dispositivos móveis
    if (isMobile()) {
        document.querySelector('.mobile-view-toggle').style.display = 'block';
        scrollHint.style.display = 'block';
    }    // Função para aplicar o modo de visualização atual
    function applyCurrentViewMode() {
        if (mobileViewActive) {
            ticketTable.classList.add('ticket-table-mobile-view');
            if (toggleViewBtn) toggleViewBtn.textContent = 'Visualização Normal';
            // Esconder hint de rolagem quando em modo card
            if (scrollHint) scrollHint.style.display = 'none';
            
            // Garantir que o modo noturno seja aplicado corretamente nos cards
            if (document.body.classList.contains('night')) {
                ticketTable.classList.add('night');
                ticketTable.querySelectorAll('tr').forEach(tr => {
                    tr.classList.add('night');
                    tr.querySelectorAll('td').forEach(td => td.classList.add('night'));
                });
                if (toggleViewBtn) toggleViewBtn.classList.add('night');
            }
        } else {
            ticketTable.classList.remove('ticket-table-mobile-view');
            if (toggleViewBtn) toggleViewBtn.textContent = 'Visualização Card';
            // Mostrar hint de rolagem quando em modo tabela normal no mobile
            if (scrollHint && isMobile()) scrollHint.style.display = 'block';
            
            // Manter o modo noturno na tabela normal se estiver ativo
            if (document.body.classList.contains('night')) {
                ticketTable.classList.add('night');
                ticketTable.querySelectorAll('tr').forEach(tr => tr.classList.add('night'));
                ticketTable.querySelectorAll('th').forEach(th => th.classList.add('night'));
                ticketTable.querySelectorAll('td').forEach(td => td.classList.add('night'));
                if (toggleViewBtn) toggleViewBtn.classList.add('night');
            }
        }
    }
    
    // Detectar se a tabela precisa de rolagem horizontal
    const tableContainer = document.querySelector('.ticket-table-container');
    function checkTableOverflow() {
        if (tableContainer && !mobileViewActive) {
            if (tableContainer.scrollWidth > tableContainer.clientWidth) {
                scrollHint.style.display = isMobile() ? 'block' : 'none';
            } else {
                scrollHint.style.display = 'none';
            }
        }
    }
    
    // Verificar overflow na carga inicial e quando a janela for redimensionada
    window.addEventListener('load', checkTableOverflow);
    window.addEventListener('resize', checkTableOverflow);    // Função para alternar modo de visualização quando o botão é clicado
    if (toggleViewBtn) {
        toggleViewBtn.addEventListener('click', function() {
            mobileViewActive = !mobileViewActive;
            localStorage.setItem('mobileViewActive', mobileViewActive ? '1' : '0');
            applyCurrentViewMode();
          // Garantir que o modo noturno seja aplicado após a mudança de visualização
            const isNightMode = document.body.classList.contains('night');            setTimeout(() => {
                // Reaplica as classes night de acordo com o modo atual
                // Isso garante consistência ao alternar visualizações
                const isNightMode = document.body.classList.contains('night');
                
                // Aplica classe night ao botão de toggle
                this.classList.toggle('night', isNightMode);
                
                // Aplica classe night à tabela
                ticketTable.classList.toggle('night', isNightMode);
                
                // Aplica classe night a todos os elementos da tabela
                ticketTable.querySelectorAll('tr').forEach(tr => {
                    tr.classList.toggle('night', isNightMode);
                    tr.querySelectorAll('td').forEach(td => td.classList.toggle('night', isNightMode));
                });
                
                // Aplica classe night aos cabeçalhos também
                ticketTable.querySelectorAll('th').forEach(th => th.classList.toggle('night', isNightMode));
                }
            }, 50); // Pequeno timeout para garantir que as classes sejam aplicadas após a mudança de visualização
        });
    }
    
    // Verificar a preferência de visualização salva
    if (localStorage.getItem('mobileViewActive') === '1' || (isMobile() && localStorage.getItem('mobileViewActive') === null)) {
        mobileViewActive = true;
    }
    
    // Aplicar modo de visualização inicial
    applyCurrentViewMode();
      // Ajustes automáticos ao redimensionar a janela
    window.addEventListener('resize', function() {
        if (isMobile()) {
            document.querySelector('.mobile-view-toggle').style.display = 'block';
            if (!mobileViewActive) {
                scrollHint.style.display = 'block';
            }
        } else {
            document.querySelector('.mobile-view-toggle').style.display = 'none';
            scrollHint.style.display = 'none';
            mobileViewActive = false;
            ticketTable.classList.remove('ticket-table-mobile-view');
        }
        checkTableOverflow();
    });
    </script>
</body>
</html>
