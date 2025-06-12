<?php
session_start();

// Simulação de autenticação (substitua pelo seu sistema real)
// $_SESSION['perfil'] = 'admin'; // ou 'tecnico'
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'tecnico'])) {
    header('Location: login.php');
    exit;
}

// --- QUICK USERS: processamento de POST/GET antes do HTML ---
$usersFile = __DIR__ . '/../logs/quick_users.txt';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
if (!is_array($users)) $users = [];
// Adiciona novo usuário rápido
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nickname'], $_POST['email'], $_POST['telefone'])) {
    $nickname = trim($_POST['nickname']);
    $email = trim($_POST['email']);
    $telefone = trim($_POST['telefone']);
    if ($nickname && ($email || $telefone)) {
        $users[] = [
            'nickname' => $nickname,
            'email' => $email,
            'telefone' => $telefone
        ];
        file_put_contents($usersFile, json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        header('Location: dashboard.php?quickusers=1');
        exit;
    }
}
// Remove usuário rápido
if (isset($_GET['del']) && is_numeric($_GET['del'])) {
    $idx = (int)$_GET['del'];
    if (isset($users[$idx])) {
        array_splice($users, $idx, 1);
        file_put_contents($usersFile, json_encode($users, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        header('Location: dashboard.php?quickusers=1');
        exit;
    }
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
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f8fb; margin:0; transition: background 0.3s, color 0.3s; }
        .sidebar { width:220px; background: #232a36; height:100vh; position:fixed; left:0; top:0; padding:30px 0; transition: background 0.3s, color 0.3s; box-shadow: 2px 0 16px #0001; border-radius: 0 18px 18px 0; }
        .sidebar h2 { color: #1976d2; text-align:center; margin-bottom:30px; font-size:1.6rem; letter-spacing:1px; transition: color 0.3s; }
        .sidebar a { display:block; color:#1976d2; text-decoration:none; padding:12px 30px; margin:8px 0; border-radius:8px; font-weight:500; transition: background 0.2s, color 0.2s; }
        .sidebar a:hover { background:#bbdefb; }
        /* Night mode styles */
        .sidebar.night h2 { color: #fff !important; text-shadow: 0 1px 2px #0008; }
        .sidebar.night a { color: #fff !important; text-shadow: 0 1px 2px #0008; }
        .sidebar.night a:hover { background: #263238; }
        .main { margin-left:240px; padding:40px 40px 30px 40px; min-height:100vh; transition: background 0.3s, color 0.3s; background: #f4f8fb; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 18px; }
        .header h1 { color: #232a36; font-size:2.2rem; letter-spacing:1px; }
        .logout { background:#d32f2f; color:#fff; border:none; padding:10px 22px; border-radius:8px; cursor:pointer; box-shadow:0 2px 8px #0002; font-size:1.1rem; font-weight:bold; transition: background 0.2s; }
        .logout:hover { background:#b71c1c; }
        .cards { display:flex; gap:24px; margin:30px 0 24px 0; flex-wrap:wrap; }
        .card { flex:1; min-width:200px; margin:0; padding:28px 0 22px 0; border-radius:16px; box-shadow:0 2px 12px #0002; text-align:center; background:linear-gradient(120deg,#fff,#f4f8fb 80%); transition: background 0.3s, color 0.3s; }
        .card.aberto { background:linear-gradient(120deg,#ffdde1,#d32f2f 90%); color:#fff; }
        .card.analise { background:linear-gradient(120deg,#fffbe7,#fbc02d 90%); color:#fff; }
        .card.resolvido { background:linear-gradient(120deg,#e0ffe7,#388e3c 90%); color:#fff; }
        .card .icon { font-size:2.7em; margin-bottom:10px; }
        .card .label { font-size:1.2em; font-weight:bold; letter-spacing:0.5px; }
        .card .value { font-size:2.1em; margin-top:5px; font-weight:600; }
        .section { background:#fff; border-radius:16px; box-shadow:0 2px 16px #0002; padding:28px 32px; margin-bottom:28px; transition: background 0.3s, color 0.3s; }
        .section h3 { color:#1976d2; margin-top:0; font-size:1.3rem; transition: color 0.3s; }
        .section.night h3, .main.night h3, h3.night { color: #fff !important; text-shadow: 0 1px 2px #0008; }
        .chat-link { display:inline-block; margin:10px 0 0 0; background:#1976d2; color:#fff; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:bold; box-shadow:0 2px 8px #0002; transition: background 0.2s; }
        .chat-link:hover { background:#1565c0; }
        table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px; background: #fff; border-radius: 10px; overflow: hidden; box-shadow:0 1px 8px #e0e0e0; font-size:15px; }
        th, td { padding: 13px 10px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background-color: #f2f6fc; color: #1976d2; font-weight: 600; font-size: 1.05rem; letter-spacing: 0.5px; }
        tr:last-child td { border-bottom: none; }
        tr { transition: background 0.2s; }
        tr:hover { background:rgb(201, 201, 201); }
        .btn { padding: 7px 16px; background: #0078d7; color: #fff; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; box-shadow: 0 1px 4px #0001; transition: background 0.2s, color 0.2s; margin: 2px 0; }
        .btn:hover { background: #0056a3; }
        .btn[style*='background:#d70022'] { background: #d70022 !important; }
        .btn[style*='background:#d70022']:hover { background: #b71c1c !important; }
        .btn[style*='background:#0078d7'] { background: #0078d7 !important; }
        .btn[style*='background:#0078d7']:hover { background: #0056a3 !important; }
        .night-toggle { position:fixed; bottom:24px; left:24px; top:auto; right:auto; z-index:1000; background:linear-gradient(90deg,#ff6b6b,#b71c1c); color:#fff; border:1px solid #b71c1c; border-radius:20px; padding:10px 22px; cursor:pointer; font-weight:bold; box-shadow:0 2px 12px #0003; font-size: 1.1rem; transition: background 0.3s, color 0.3s; }
        .night-toggle.night { background:linear-gradient(90deg,#b71c1c,#ff6b6b); color:#fff; border-color:#fff; }
        /* Títulos: preto no light, branco no night, com transição */
        h2, h3 { color: #f4f8fb !important; transition: color 0.3s; text-shadow: none; }
        body.night h1, body.night h2, body.night h3 { color: #fff !important; text-shadow: 0 1px 2px #0008; }
        /* Cabeçalhos de tabela: preto no light, branco no night */
        th { color: #111 !important; }
        th.night, body.night th { color: #fff !important; }
        /* Labels e títulos de seções abaixo de Usuários Rápidos: preto no light, branco no night */
        .section h3, .section label { color: #111 !important; }
        .section.night h3, .section.night label, body.night .section h3, body.night .section label { color: #fff !important; }
        /* Links do menu lateral: preto no light, branco no night, com transição */
        .sidebar a { color: #f4f8fb; transition: color 0.3s; }
        .sidebar.night a { color: #fff !important; text-shadow: 0 1px 2px #0008; }
        .sidebar a:hover { background:#bbdefb; }
        .sidebar.night a:hover { background: #263238; color: #fff !important; }
        /* Night mode styles */
        body.night { background: #181c24 !important; color: #e0e0e0; }
        .sidebar.night { background: #232a36 !important; color: #fff; box-shadow: 2px 0 16px #0006; }
        .sidebar.night a { color: #fff !important; text-shadow: 0 1px 2px #0008; }
        .sidebar.night a:hover { background: #263238; }
        .main.night { background: #181c24 !important; color: #e0e0e0; }
        .section.night { background: #232a36 !important; color: #e0e0e0; box-shadow: 0 2px 16px #0006; }
        .card.night { box-shadow:0 2px 12px #0006; }
        .card.aberto.night { background:linear-gradient(120deg,#3a2323,#b71c1c 90%); color:#fff; }
        .card.analise.night { background:linear-gradient(120deg,#3a2e1a,#fbc02d 90%); color:#fff; }
        .card.resolvido.night { background:linear-gradient(120deg,#1a3a23,#388e3c 90%); color:#fff; }
        table.night { background: #232a36 !important; color: #e0e0e0; box-shadow:0 1px 8px #0006; }
        th.night { background: #263238 !important; color:rgb(255, 255, 255); }
        tr.night:hover { background-color: #222b38 !important; }
        /* .btn.night { background: #b71c1c !important; color: #fff; } */
        /* .btn.night:hover { background: #ff6b6b !important; color: #fff; } */
        /* .chat-link.night { background: #b71c1c !important; color: #fff; } */
        /* .chat-link.night:hover { background: #ff6b6b !important; color: #fff; } */
        /* Night/Light mode switcher - canto inferior esquerdo */
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
        @media (max-width:900px) { .main { margin-left:0; padding:20px 2vw; } .sidebar { position:static; width:100%; height:auto; border-radius:0; } .cards { flex-direction:column; gap:18px; } }
        @media (max-width:700px) { .main { padding:10px 1vw; } .section { padding:12px 6px; } .card { padding:16px 0 12px 0; } th, td { font-size:12px; padding:7px 4px; } .sidebar h2 { font-size:1.1rem; } .header h1 { font-size:1.1rem; } }
    </style>
</head>
<body>
    <!-- Switch de modo claro/noturno -->
    <div class="mode-switch light" id="modeSwitch">
        <span class="icon" id="modeIcon">🌞</span>
        <input type="checkbox" id="modeToggle" aria-label="Alternar modo claro/noturno">
        <span id="modeLabel">Claro</span>
    </div>
    <div class="sidebar" id="sidebar">
        <h2>Helpdesk System</h2>
        <a href="tickets.php">🎟️ Tickets</a>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="open.php">📂 Novo Chamado</a>
        <a href="buscarchamados.html">🔍 Buscar Chamados</a>
    </div>
    <div class="main" id="main">
        <div class="header">
            <h1>Dashboard</h1>
            <form method="post" action="logout.php" style="margin:0;">
                <button class="logout">Logout</button>
            </form>
        </div>
        <div class="cards">
            <div class="card aberto" id="card-aberto">
                <div class="icon">🔒</div>
                <div class="label">NÃO ABERTO</div>
                <div class="value" id="card-aberto-value"><?=$chamados['aberto']?></div>
            </div>
            <div class="card analise" id="card-analise">
                <div class="icon">⏳</div>
                <div class="label">EM ANÁLISE</div>
                <div class="value" id="card-analise-value"><?=$chamados['analise']?></div>
            </div>
            <div class="card resolvido" id="card-resolvido">
                <div class="icon">✅</div>
                <div class="label">RESOLVIDO</div>
                <div class="value" id="card-resolvido-value"><?=$chamados['resolvido']?></div>
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
        <?php if (isset($_SESSION['role']) && in_array($_SESSION['role'], ['admin','tecnico'])): ?>
            <?php include 'quick_users.php'; ?>
        <?php endif; ?>
        <div class="section">
            <h3>Lista de Tickets</h3>
            <div style="overflow-x:auto; margin: 0 0 20px 0;">
            <table class="ticket-table" style="width:100%;background:#fff;border-radius:8px;box-shadow:0 1px 6px #e0e0e0;">
                <thead>
                    <tr>
                        <th>🆔 ID</th>
                        <th>👤 Nome</th>
                        <th>📧 E-mail</th>
                        <th>📦 Produto</th>
                        <th>📝 Assunto</th>
                        <th>💬 Mensagem</th>
                        <th>🖼️ Imagem</th>
                        <th>📱 Telefone</th>
                        <th>📊 Status</th>
                        <th>⚙️ Ações</th>
                    </tr>
                </thead>
                <tbody id="tickets-tbody">
                    <?php foreach ($tickets as $i => $ticket): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= htmlspecialchars($ticket['name'] ?? '') ?></td>
                        <td><?= htmlspecialchars($ticket['email'] ?? '') ?></td>
                        <td><?= htmlspecialchars($ticket['produto'] ?? '') ?></td>
                        <td><?= htmlspecialchars($ticket['subject'] ?? '') ?></td>
                        <td style="max-width:250px;word-break:break-word;">
                            <?php
                            // Exibe campos extras para filmes/séries
                            if (($ticket['produto'] ?? '') === 'filmes') {
                                echo '<b>🍿 Filme:</b> ' . htmlspecialchars($ticket['filme_nome'] ?? '-') . '<br>';
                                echo '<b>🌟 TMDB:</b> ' . htmlspecialchars($ticket['filme_tmdb'] ?? '-') . '<br>';
                                if (!empty($ticket['filme_obs'])) echo '<b>⚠️OBS:</b> ' . htmlspecialchars($ticket['filme_obs']) . '<br>';
                                echo '<hr style="margin:4px 0;">';
                            } elseif (($ticket['produto'] ?? '') === 'series') {
                                echo '<b>📽 Série:</b> ' . htmlspecialchars($ticket['serie_nome'] ?? '-') . '<br>';
                                echo '<b>🌟 TMDB:</b> ' . htmlspecialchars($ticket['serie_tmdb'] ?? '-') . '<br>';
                                if (!empty($ticket['serie_obs'])) echo '<b>⚠️OBS:</b> ' . htmlspecialchars($ticket['serie_obs']) . '<br>';
                                echo '<hr style="margin:4px 0;">';
                            }
                            ?>
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
                                'resolvido' => '<span style="color:#388e3c;font-weight:bold;">✅ Resolvido</span>',
                                'em_analise' => '<span style="color:#fbc02d;font-weight:bold;">⏳ Em análise</span>',
                                'nao_aberto' => '<span style="color:#d32f2f;font-weight:bold;">🔒 Não aberto</span>'
                            ];
                            echo $statusLabel[$status] ?? $statusLabel['nao_aberto'];
                            ?>
                            <form method="post" action="dashboard.php" style="margin-top:5px;display:inline-block;" onsubmit="return alterarStatusDashboard(this, event)">
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
                            <button type="button" class="btn chat-popup-btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#43a047;color:#fff;" data-ticket-id="<?= $i+1 ?>" data-email="<?= htmlspecialchars($ticket['email'] ?? '') ?>" data-telefone="<?= htmlspecialchars($ticket['telefone'] ?? '') ?>">Chat Pop-up</button>
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
    <!-- Modal de Chat Pop-up -->
    <div id="chatModal" style="display:none;position:fixed;z-index:9999;left:0;top:0;width:100vw;height:100vh;background:rgba(0,0,0,0.45);align-items:center;justify-content:center;">
        <div id="chatModalContent" style="background:#fff;max-width:520px;width:95vw;max-height:90vh;border-radius:12px;box-shadow:0 8px 32px #0008;padding:0;position:relative;display:flex;flex-direction:column;">
            <button id="closeChatModal" style="position:absolute;top:10px;right:16px;background:#d32f2f;color:#fff;border:none;border-radius:50%;width:32px;height:32px;font-size:1.3em;cursor:pointer;z-index:2;">&times;</button>
            <iframe id="chatIframe" src="" style="border:none;width:100%;height:70vh;border-radius:12px;"></iframe>
        </div>
    </div>
    <script>
    // Novo switch de modo
    const modeSwitch = document.getElementById('modeSwitch');
    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    const modeLabel = document.getElementById('modeLabel');
    function setMode(night) {
        document.body.classList.toggle('night', night);
        document.getElementById('sidebar').classList.toggle('night', night);
        document.getElementById('main').classList.toggle('night', night);
        document.querySelectorAll('.section').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('.card').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('table').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('th').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('tr').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('.btn').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('.chat-link').forEach(e=>e.classList.toggle('night', night));
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
        if(typeof updateChatModalNightMode === 'function') updateChatModalNightMode();
    }
    modeToggle.addEventListener('change', function() {
        setMode(this.checked);
    });
    // Inicialização
    if(localStorage.getItem('nightMode')) setMode(true);
    else setMode(false);
    // Remove botão antigo se existir
    var oldBtn = document.getElementById('nightToggle');
    if(oldBtn) oldBtn.remove();
    // Chat Pop-up para todos os chamados
    function openChatPopup(ticketId, email, telefone) {
        const chatModal = document.getElementById('chatModal');
        const chatIframe = document.getElementById('chatIframe');
        // Monta a URL do chat com os dados do chamado
        let url = `chat_frontend.html?id=${encodeURIComponent(ticketId)}`;
        if(email) url += `&email=${encodeURIComponent(email)}`;
        if(telefone) url += `&telefone=${encodeURIComponent(telefone)}`;
        chatIframe.src = url;
        chatModal.style.display = 'flex';
        // Night mode no modal
        if(document.body.classList.contains('night')) {
            chatModal.style.background = 'rgba(24,28,36,0.85)';
            chatModalContent.classList.add('night');
        } else {
            chatModal.style.background = 'rgba(0,0,0,0.45)';
            chatModalContent.classList.remove('night');
        }
    }
    // Chat Pop-up para usuários rápidos
    function openQuickUserChatPopup(email, telefone, author) {
        const chatModal = document.getElementById('chatModal');
        const chatIframe = document.getElementById('chatIframe');
        let url = `chat_frontend.html?email=${encodeURIComponent(email)}&telefone=${encodeURIComponent(telefone)}&author=${encodeURIComponent(author)}`;
        chatIframe.src = url;
        chatModal.style.display = 'flex';
        // Night mode no modal
        if(document.body.classList.contains('night')) {
            chatModal.style.background = 'rgba(24,28,36,0.85)';
            chatModalContent.classList.add('night');
        } else {
            chatModal.style.background = 'rgba(0,0,0,0.45)';
            chatModalContent.classList.remove('night');
        }
    }
    document.querySelectorAll('.chat-popup-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            openQuickUserChatPopup(this.dataset.email, this.dataset.telefone, this.dataset.author);
        });
    });
    document.getElementById('closeChatModal').onclick = function() {
        document.getElementById('chatModal').style.display = 'none';
        document.getElementById('chatIframe').src = '';
    };
    // Fecha modal ao clicar fora do conteúdo
    window.addEventListener('click', function(e) {
        const modal = document.getElementById('chatModal');
        const content = document.getElementById('chatModalContent');
        if(e.target === modal) {
            modal.style.display = 'none';
            document.getElementById('chatIframe').src = '';
        }
    });
    // Night mode dinâmico no modal
    function updateChatModalNightMode() {
        const chatModal = document.getElementById('chatModal');
        const chatModalContent = document.getElementById('chatModalContent');
        if(document.body.classList.contains('night')) {
            chatModal.style.background = 'rgba(24,28,36,0.85)';
            chatModalContent.style.background = '#232a36';
        } else {
            chatModal.style.background = 'rgba(0,0,0,0.45)';
            chatModalContent.style.background = '#fff';
        }
    }
    document.getElementById('nightToggle').addEventListener('click', updateChatModalNightMode);
    </script>
</body>
</html>
