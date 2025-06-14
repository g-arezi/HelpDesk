<?php
session_start();

// Simulação de autenticação (substitua pelo seu sistema real)
// $_SESSION['perfil'] = 'admin'; // ou 'tecnico'
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'tecnico'])) {
    header('Location: login.php');
    exit;
}

// Processa exclusão de tickets primeiro (antes de qualquer output HTML)
if (isset($_POST['delete_id']) && is_numeric($_POST['delete_id'])) {
    $deleteId = (int)$_POST['delete_id'];
    $ticketsFile = __DIR__ . '/../logs/tickets.txt';
    $existingTickets = [];
    
    if (file_exists($ticketsFile)) {
        $content = file_get_contents($ticketsFile);
        $existingTickets = json_decode($content, true) ?: [];
        
        // Verifica se o índice existe antes de tentar deletar
        if (isset($existingTickets[$deleteId])) {
            // Remove o ticket pelo índice
            array_splice($existingTickets, $deleteId, 1);
            // Reindexar array após remover o item
            $existingTickets = array_values($existingTickets);
            // Salva o array atualizado
            file_put_contents($ticketsFile, json_encode($existingTickets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            
            // Redireciona para evitar reenvio do formulário
            header('Location: dashboard.php?deleted=true');
            exit;
        }
    }
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

// Processa aprovação/rejeição de usuários
if (isset($_POST['user_action'], $_POST['user_index']) && $_SESSION['role'] === 'admin') {
    $userAction = $_POST['user_action'];
    $userIndex = (int)$_POST['user_index'];
    $usersFile = __DIR__ . '/../logs/user_registrations.txt';
    
    if (file_exists($usersFile)) {
        $users = json_decode(file_get_contents($usersFile), true) ?: [];
        
        if (isset($users[$userIndex])) {
            if ($userAction === 'approve') {
                $users[$userIndex]['status'] = 'approved';
            } elseif ($userAction === 'reject') {
                $users[$userIndex]['status'] = 'rejected';
            } elseif ($userAction === 'delete') {
                array_splice($users, $userIndex, 1);
            }
            
            file_put_contents($usersFile, json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
            header('Location: dashboard.php?user_management=1');
            exit;
        }
    }
}

// Carrega registros de usuários (apenas para admin)
$userRegistrations = [];
if ($_SESSION['role'] === 'admin') {
    $usersFile = __DIR__ . '/../logs/user_registrations.txt';
    if (file_exists($usersFile)) {
        $userRegistrations = json_decode(file_get_contents($usersFile), true) ?: [];
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, user-scalable=yes">
    <title>Dashboard - Helpdesk</title>
    <link rel="stylesheet" href="assets/mobile.css">
    <style>
        body { font-family: 'Segoe UI', Arial, sans-serif; background: #f4f8fb; margin:0; transition: background 0.3s, color 0.3s; }
        body.night { background: #181c24; color: #e0e0e0; }
        .sidebar { width:220px; background: #232a36; height:100vh; position:fixed; left:0; top:0; padding:30px 0; transition: background 0.3s, color 0.3s; box-shadow: 2px 0 16px #0001; border-radius: 0 18px 18px 0; z-index: 1000; }
        .sidebar h2 { color: #1976d2; text-align:center; margin-bottom:30px; font-size:1.6rem; letter-spacing:1px; transition: color 0.3s; }
        .sidebar a { display:block; color:#1976d2; text-decoration:none; padding:12px 30px; margin:8px 0; border-radius:8px; font-weight:500; transition: background 0.2s, color 0.2s; }
        .sidebar a:hover { background:#bbdefb; }
        /* Night mode styles */
        .sidebar.night h2 { color: #fff !important; text-shadow: 0 1px 2px #0008; }
        .sidebar.night a { color: #fff !important; text-shadow: 0 1px 2px #0008; }
        .sidebar.night a:hover { background: #263238; }        .main { margin-left:240px; padding:40px 40px 30px 40px; min-height:100vh; transition: background 0.3s, color 0.3s; background: #f4f8fb; }
        .main.night { background: #1e2430; color: #e0e0e0; }
        .header { display:flex; justify-content:space-between; align-items:center; margin-bottom: 18px; }
        .header h1 { color: #232a36; font-size:2.2rem; letter-spacing:1px; transition: color 0.3s; }
        .header.night h1, body.night .header h1 { color: #fff !important; }
        .logout { background:#d32f2f; color:#fff; border:none; padding:10px 22px; border-radius:8px; cursor:pointer; box-shadow:0 2px 8px #0002; font-size:1.1rem; font-weight:bold; transition: background 0.2s; }
        .logout:hover { background:#b71c1c; }
        .cards { display:flex; gap:24px; margin:30px 0 24px 0; flex-wrap:wrap; }
        .card { flex:1; min-width:200px; margin:0; padding:28px 0 22px 0; border-radius:16px; box-shadow:0 2px 12px #0002; text-align:center; background:linear-gradient(120deg,#fff,#f4f8fb 80%); transition: background 0.3s, color 0.3s; }
        .card.aberto { background:linear-gradient(120deg,#ffdde1,#d32f2f 90%); color:#fff; }
        .card.analise { background:linear-gradient(120deg,#fffbe7,#fbc02d 90%); color:#fff; }
        .card.resolvido { background:linear-gradient(120deg,#e0ffe7,#388e3c 90%); color:#fff; }        .card .icon { font-size:2.7em; margin-bottom:10px; }
        .card .label { font-size:1.2em; font-weight:bold; letter-spacing:0.5px; }
        .card .value { font-size:2.1em; margin-top:5px; font-weight:600; }        
        .section { background:#fff; border-radius:16px; box-shadow:0 2px 16px #0002; padding:28px 32px; margin-bottom:28px; transition: background 0.3s, color 0.3s; }
        .section.night { background: #232a36; color: #e0e0e0; box-shadow: 0 2px 16px rgba(0, 0, 0, 0.4); }
        .section h3 { color:#000000; margin-top:0; font-size:1.3rem; transition: color 0.3s; }
        .section.night h3, .main.night h3, h3.night { color: #ffffff !important; text-shadow: 0 1px 2px #0008; }
        .chat-link { display:inline-block; margin:10px 0 0 0; background:#000000; color:#fff; padding:10px 20px; border-radius:8px; text-decoration:none; font-weight:bold; box-shadow:0 2px 8px #0002; transition: background 0.2s; }        .chat-link:hover { background:#333333; }
        .chat-link.night { background: #1976d2; }
        .chat-link.night:hover { background: #1565c0; }
          table { width: 100%; border-collapse: separate; border-spacing: 0; margin-top: 10px; background: #fff; border-radius: 10px; overflow: hidden; box-shadow:0 1px 8px #e0e0e0; font-size:15px; transition: background 0.3s, color 0.3s; }
        table.night { background: #232a36; color: #e0e0e0; box-shadow: 0 1px 8px rgba(0, 0, 0, 0.4); }
        th, td { padding: 13px 10px; text-align: left; border-bottom: 1px solid #e0e0e0; transition: background 0.3s, border-color 0.3s, color 0.3s; }
        th { background-color: #f2f6fc; color: #000000; font-weight: 600; font-size: 1.05rem; letter-spacing: 0.5px; transition: background 0.3s, color 0.3s; }
        table.night th { background-color: #1e2430; color: #ffffff !important; }
        table.night td { border-bottom: 1px solid #2c3e50; color: #e0e0e0; }
        td.night { background-color: #232a36; color: #e0e0e0; border-bottom: 1px solid #2c3e50; }
        tr:last-child td { border-bottom: none; }
        tr { transition: background 0.2s; }
        tr:hover { background:rgb(201, 201, 201); }
        table.night tr:hover { background: #2c3e50; }
        tr.night:hover { background: #2c3e50; }
        tr.night td { background-color: #232a36; color: #e0e0e0; border-bottom: 1px solid #2c3e50; }
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
        .sidebar.night a:hover { background: #263238; color: #fff !important; }        /* Night/Light mode switcher - canto inferior esquerdo */
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
            padding: 6px 14px 6px 10px;
            border-radius: 18px;
        }
        .mode-switch.light {
            background: #e3f2fd;
            color: #1976d2;
            border: 1px solid #b3c6e0;
        }
        .mode-switch.night {
            background: #232a36;
            color: #fff;
            border: 1px solid #1976d2;
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
        
        /* Responsividade */
        @media (max-width: 1200px) {
            .sidebar {
                width: 100%;
                height: auto;
                position: relative;
                border-radius: 0;
                padding: 15px 0;
                overflow-x: auto;
                white-space: nowrap;
                display: flex;
                flex-direction: row;
                justify-content: center;
                align-items: center;
            }
            
            .sidebar h2 {
                display: none;
            }
            
            .sidebar a {
                display: inline-block;
                padding: 10px 15px;
                margin: 0 5px;
                font-size: 14px;
            }
            
            .main {
                margin-left: 0;
                padding: 20px;
            }
            
            .cards {
                flex-direction: column;
                gap: 15px;
            }
            
            .card {
                min-width: 100%;
            }
            
            table {
                display: block;
                overflow-x: auto;
                white-space: nowrap;
            }
            
            .night-toggle {
                bottom: 10px;
                left: 10px;
                padding: 8px 15px;
                font-size: 0.9rem;
            }
            
            /* Ajustes para botões no mobile */
            .btn, 
            button[type="submit"],
            .chat-link,
            .logout {
                font-size: 14px !important;
                padding: 8px 12px !important;
                height: auto !important;
                line-height: 1.4 !important;
            }
            
            /* Ajustar layout de ações na tabela */
            table .btn {
                margin: 3px 2px !important;
            }
        }
        
        /* Ajustes específicos para iPhone 15 (390px) */
        @media (max-width: 390px) {
            .header {
                flex-direction: column;
                align-items: flex-start;
            }
            
            .header h1 {
                font-size: 1.8rem;
                margin-bottom: 15px;
            }
            
            .section {
                padding: 20px;
            }
            
            .btn {
                display: block;
                width: 100%;
                margin: 5px 0;            }
        }
        
        /* Force visibility of night mode switch */
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
        
        /* Form elements in night mode */
        input.night, select.night, textarea.night { 
            background-color: #2a2a3d !important; 
            color: #e0e0e0 !important; 
            border: 1px solid #3e3e5c !important;
        }
        
        body.night input, body.night select, body.night textarea {
            background-color: #2a2a3d !important; 
            color: #e0e0e0 !important; 
            border: 1px solid #3e3e5c !important;
        }
        
        /* Improved visibility for form labels in night mode */
        body.night label {
            color: #e0e0e0 !important;
        }
        
        /* Make sure status colors are properly visible in night mode */
        body.night span[style*="color:#d32f2f"] { color: #ff6b6b !important; }
        body.night span[style*="color:#fbc02d"] { color: #ffd54f !important; }
        body.night span[style*="color:#388e3c"] { color: #81c784 !important; }
    </style>    <script>
        // Check for night mode preference without forcing it
        if (localStorage.getItem('nightMode') === '1') {
            // Just add a helper class for initial load
            document.documentElement.classList.add('night-mode-preload');
            document.addEventListener('DOMContentLoaded', function() {
                setTimeout(function() {
                    document.documentElement.classList.remove('night-mode-preload');
                }, 100);
            });
        }
    </script>
</head>
<body>
    <!-- Switch de modo claro/noturno -->
    <div class="mode-switch light" id="modeSwitch">
        <span class="icon" id="modeIcon">🌞</span>
        <input type="checkbox" id="modeToggle" aria-label="Alternar modo claro/noturno">
        <span id="modeLabel">Claro</span>
    </div><div class="sidebar" id="sidebar">
        <h2>Helpdesk System</h2>
        <a href="dashboard.php">🏠 Dashboard</a>
        <a href="tickets.php">🎟️ Tickets</a>
        <a href="open.php">📂 Novo Chamado</a>
        <a href="buscarchamados.html">🔍 Buscar Chamados</a>
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <a href="dashboard.php?user_management=1">👥 Gerenciar Usuários</a>
        <?php endif; ?>
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
        <div class="section">            <h3>Chat do Chamado</h3>
            <form method="get" action="chat_frontend.html" target="_blank" style="margin-top:10px;" class="mobile-form">
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
            <table class="ticket-table" style="width:100%;background:#fff;border-radius:8px;box-shadow:0 1px 6px #e0e0e0;">                <thead>                    <tr>
                        <th>🆔 ID</th>
                        <th>👤 Usuário</th>
                        <th>👑 Usuário do Painel</th>
                        <th>📦 Produto</th>
                        <th>📝 Assunto</th>
                        <th>💬 Mensagem</th>
                        <th>🖼️ Imagem</th>
                        <th>📊 Status</th>
                        <th>⚙️ Ações</th>
                    </tr>
                </thead>
                <tbody id="tickets-tbody">
                    <?php foreach ($tickets as $i => $ticket): ?>                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td>
                            <?php 
                            if (isset($ticket['user'])) {
                                echo htmlspecialchars($ticket['user']);
                            } elseif (isset($ticket['created_by']) && isset($ticket['created_by']['username'])) {
                                echo htmlspecialchars($ticket['created_by']['username']);
                            } elseif (isset($ticket['name'])) {
                                // Compatibilidade com tickets antigos
                                echo htmlspecialchars($ticket['name']);
                            } else {
                                echo 'Usuário desconhecido';                            }
                            ?>
                        </td>                        <td>                            <?php 
                            if (isset($ticket['created_by']) && isset($ticket['created_by']['panel_username']) && !empty($ticket['created_by']['panel_username'])) {
                                echo htmlspecialchars($ticket['created_by']['panel_username']);
                            } elseif (isset($ticket['username_painel']) && !empty($ticket['username_painel'])) {
                                echo htmlspecialchars($ticket['username_painel']);
                            } elseif (isset($_SESSION['username']) && !empty($_SESSION['username'])) {
                                echo htmlspecialchars($_SESSION['username']);
                            } else {
                                echo '-';
                            }
                            ?>
                        </td>
                        <td><?= htmlspecialchars($ticket['produto'] ?? '') ?></td>
                        <td><?= htmlspecialchars($ticket['subject'] ?? '') ?></td>
                        <td style="max-width:250px;word-break:break-word;">
                            <?php
                            // Exibe campos extras para filmes/séries
                            if (($ticket['produto'] ?? '') === 'filmes') {
                                if (!empty($ticket['filmes_obse_label'])) echo '<b>Opção:</b> ' . htmlspecialchars($ticket['filmes_obse_label']) . '<br>';
                                elseif (!empty($ticket['filmes_obse'])) echo '<b>Opção:</b> ' . htmlspecialchars($ticket['filmes_obse']) . '<br>';
                                echo '<b>🍿 Filme:</b> ' . htmlspecialchars($ticket['filme_nome'] ?? '-') . '<br>';
                                echo '<b>🌟 TMDB:</b> ' . htmlspecialchars($ticket['filme_tmdb'] ?? '-') . '<br>';
                                if (!empty($ticket['filme_obs'])) echo '<b>⚠️OBS:</b> ' . htmlspecialchars($ticket['filme_obs']) . '<br>';
                                echo '<hr style="margin:4px 0;">';
                            } elseif (($ticket['produto'] ?? '') === 'series') {
                                if (!empty($ticket['series_obse_label'])) echo '<b>Opção:</b> ' . htmlspecialchars($ticket['series_obse_label']) . '<br>';
                                elseif (!empty($ticket['series_obse'])) echo '<b>Opção:</b> ' . htmlspecialchars($ticket['series_obse']) . '<br>';
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
                                <span style="color:#aaa;">-</span>                            <?php endif; ?>
                        </td>
                        <td>                            <?php 
                            $status = isset($ticket['status']) ? $ticket['status'] : 'nao_aberto';
                            $statusLabel = [
                                'resolvido' => '<span style="color:#388e3c;font-weight:bold;">✅ Resolvido</span>',
                                'em_analise' => '<span style="color:#fbc02d;font-weight:bold;">⏳ Em análise</span>',
                                'nao_aberto' => '<span style="color:#d32f2f;font-weight:bold;">🔒 Não aberto</span>'
                            ];
                            echo $statusLabel[$status] ?? $statusLabel['nao_aberto'];
                            ?>
                            <form method="post" action="dashboard.php" style="margin-top:5px;display:inline-block;" onsubmit="return alterarStatusDashboard(this, event)">
                                <input type="hidden" name="id" value="<?= $i ?>">                                <select name="status" style="padding:2px 6px;">
                                    <option value="nao_aberto" <?= $status==='nao_aberto'?'selected':''; ?>>Não aberto</option>
                                    <option value="em_analise" <?= $status==='em_analise'?'selected':''; ?>>Em análise</option>
                                    <option value="resolvido" <?= $status==='resolvido'?'selected':''; ?>>Resolvido</option>
                                </select>
                                <button type="submit" class="btn action-btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#0078d7;color:#fff;">Alterar</button>
                            </form>
                        </td>                        <td>                            <button type="button" class="btn action-btn chat-popup-btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background: #1976D2 ;color: #fff;" data-ticket-id="<?= $i+1 ?>" 
                            data-email="<?= htmlspecialchars(isset($ticket['created_by']['email']) ? $ticket['created_by']['email'] : ($ticket['email'] ?? '')) ?>" 
                            data-telefone="<?= htmlspecialchars(isset($ticket['created_by']['telefone']) ? $ticket['created_by']['telefone'] : ($ticket['telefone'] ?? '')) ?>">Chat Pop-up</button>
                            <a href="chat_frontend.html?id=<?= $i+1 ?>" class="btn action-btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#43a047;color:#fff;" target="_blank">Chat</a>
                            <form method="post" action="dashboard.php" style="margin-top:5px;display:inline-block;">
                                <input type="hidden" name="delete_id" value="<?= $i ?>">
                                <button type="submit" class="btn action-btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#d70022;color:#fff;" onclick="return confirm('Tem certeza que deseja deletar este ticket?');">Deletar</button>
                            </form>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            </div>
        </div>
        <?php if (isset($_GET['user_management']) && $_SESSION['role'] === 'admin'): ?>
        <div class="section">
            <h3>👥 Gerenciamento de Usuários</h3>
            <?php if (empty($userRegistrations)): ?>
                <p style="text-align: center; color: #888;">Nenhum registro de usuário encontrado.</p>
            <?php else: ?>
                <div style="overflow-x:auto; margin: 0 0 20px 0;">
                    <table class="ticket-table" style="width:100%;background:#fff;border-radius:8px;box-shadow:0 1px 6px #e0e0e0;">
                        <thead>
                            <tr>
                                <th>👤 Usuário</th>
                                <th>🖥️ Servidor</th>
                                <th>👑 Usuário do Painel</th>
                                <th>📅 Data da Recarga</th>
                                <th>💰 Créditos</th>
                                <th>📊 Status</th>
                                <th>⚙️ Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($userRegistrations as $i => $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user['username']) ?></td>
                                <td><?= htmlspecialchars($user['server_name']) ?></td>
                                <td><?= htmlspecialchars($user['panel_username']) ?></td>
                                <td><?= htmlspecialchars($user['last_recharge_date']) ?></td>
                                <td><?= htmlspecialchars($user['credit_amount']) ?></td>
                                <td>
                                    <?php
                                    $statusLabel = '';
                                    if ($user['status'] === 'pending') {
                                        $statusLabel = '<span style="color:#fbc02d;font-weight:bold;">⏳ Pendente</span>';
                                    } elseif ($user['status'] === 'approved') {
                                        $statusLabel = '<span style="color:#388e3c;font-weight:bold;">✅ Aprovado</span>';
                                    } elseif ($user['status'] === 'rejected') {
                                        $statusLabel = '<span style="color:#d32f2f;font-weight:bold;">❌ Rejeitado</span>';
                                    }
                                    echo $statusLabel;
                                    ?>
                                </td>
                                <td>
                                    <?php if ($user['status'] === 'pending'): ?>                                    <form method="post" action="dashboard.php" style="display:inline-block;">
                                        <input type="hidden" name="user_index" value="<?= $i ?>">
                                        <input type="hidden" name="user_action" value="approve">
                                        <button type="submit" class="btn action-btn" style="padding:2px 10px;font-size:13px;margin-right:4px;background:#388e3c;color:#fff;">Aprovar</button>
                                    </form>
                                    <form method="post" action="dashboard.php" style="display:inline-block;">
                                        <input type="hidden" name="user_index" value="<?= $i ?>">
                                        <input type="hidden" name="user_action" value="reject">
                                        <button type="submit" class="btn action-btn" style="padding:2px 10px;font-size:13px;margin-right:4px;background:#d32f2f;color:#fff;">Rejeitar</button>
                                    </form>
                                    <?php endif; ?>
                                    <form method="post" action="dashboard.php" style="display:inline-block;">
                                        <input type="hidden" name="user_index" value="<?= $i ?>">
                                        <input type="hidden" name="user_action" value="delete">
                                        <button type="submit" class="btn action-btn" style="padding:2px 10px;font-size:13px;background:#d70022;color:#fff;" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
        <?php endif; ?>
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
    const modeLabel = document.getElementById('modeLabel');    function setMode(night) {
        console.log('Setting night mode:', night);
        
        // Toggle de modo
        const modeToggle = document.getElementById('modeToggle');
        const modeIcon = document.getElementById('modeIcon');
        const modeLabel = document.getElementById('modeLabel');
        const modeSwitch = document.getElementById('modeSwitch');
        
        // Elementos principais
        document.body.classList.toggle('night', night);
        if (document.getElementById('sidebar')) document.getElementById('sidebar').classList.toggle('night', night);
        if (document.getElementById('main')) document.getElementById('main').classList.toggle('night', night);
        
        // Grupos de elementos
        document.querySelectorAll('.section').forEach(e => e.classList.toggle('night', night));
        document.querySelectorAll('.card').forEach(e => e.classList.toggle('night', night));
        
        // Aplicando night mode em todas as tabelas e seus elementos
        document.querySelectorAll('table').forEach(table => {
            table.classList.toggle('night', night);
            
            // Aplicar a classe em cada elemento da tabela
            table.querySelectorAll('th').forEach(th => th.classList.toggle('night', night));
            table.querySelectorAll('tr').forEach(tr => tr.classList.toggle('night', night));
            
            // Aplica a classe night em cada célula da tabela com estilos específicos
            table.querySelectorAll('td').forEach(td => {
                td.classList.toggle('night', night);
                // Garantir visibilidade do texto nas células no modo noturno
                if (night) {
                    // Garantir que os elementos dentro das células também estejam visíveis
                    td.querySelectorAll('span, a, button, input, select').forEach(el => {
                        el.classList.add('night');
                    });
                } else {
                    td.querySelectorAll('span, a, button, input, select').forEach(el => {
                        el.classList.remove('night');
                    });
                }
            });
        });
        
        // Botões e links
        document.querySelectorAll('.btn').forEach(e => e.classList.toggle('night', night));
        document.querySelectorAll('.chat-link').forEach(e => e.classList.toggle('night', night));
        document.querySelectorAll('.action-btn').forEach(e => e.classList.toggle('night', night));
        document.querySelectorAll('a').forEach(e => e.classList.toggle('night', night));
        
        // Cards e dashboards (estatísticas)
        document.querySelectorAll('.dashboard-grid .card').forEach(e => e.classList.toggle('night', night));
        document.querySelectorAll('.summary-box').forEach(e => e.classList.toggle('night', night));
        document.querySelectorAll('.status-badge').forEach(e => e.classList.toggle('night', night));
        
        // Elementos de formulário
        document.querySelectorAll('input, select, textarea').forEach(e => e.classList.toggle('night', night));
        
        // Mobile específico
        document.querySelectorAll('.mobile-view-toggle').forEach(e => e.classList.toggle('night', night));
        document.querySelectorAll('.table-scroll-hint').forEach(e => e.classList.toggle('night', night));
        document.querySelectorAll('.ticket-table-container').forEach(e => e.classList.toggle('night', night));
        
        // Toggle de modo
        if (modeSwitch) {
            modeSwitch.classList.toggle('light', !night);
            modeSwitch.classList.toggle('night', night);
        }
        
        if (modeToggle && modeToggle.checked !== night) {
            modeToggle.checked = night;
        }
        
        // Texto e ícones
        if(night) {
            if (modeIcon) modeIcon.textContent = '🌙';
            if (modeLabel) modeLabel.textContent = 'Noturno';
            localStorage.setItem('nightMode','1');
        } else {
            if (modeIcon) modeIcon.textContent = '🌞';
            if (modeLabel) modeLabel.textContent = 'Claro';
            localStorage.removeItem('nightMode');
        }
        
        // Updates específicos
        if(typeof updateChatModalNightMode === 'function') updateChatModalNightMode();
    }// Configurar o seletor de modo noturno sem forçar
    document.addEventListener('DOMContentLoaded', function() {
        console.log('DOM Content Loaded - Setting up night mode toggle');
        
        // Certifica-se de que temos acesso aos elementos do DOM
        const modeSwitch = document.getElementById('modeSwitch');
        const modeToggle = document.getElementById('modeToggle');
        
        console.log('Mode switch found:', modeSwitch);
        console.log('Mode toggle found:', modeToggle);
        
        if (modeSwitch) {
            // Garantir que o seletor esteja visível
            modeSwitch.style.display = 'flex';
            modeSwitch.style.opacity = '1';
            modeSwitch.style.zIndex = '1000';
            console.log('Mode switch display set to flex');
        }
        
        if (modeToggle) {
            // Configurar o estado do toggle de acordo com a preferência salva
            const savedPreference = localStorage.getItem('nightMode') === '1';
            modeToggle.checked = savedPreference;
            
            // Aplicar o modo se necessário
            if (savedPreference) {
                setMode(true);
            }
            
            // Adiciona listener para o toggle de modo
            modeToggle.addEventListener('change', function() {
                console.log('Toggle changed to:', this.checked);
                setMode(this.checked);
            });
        } else {
            console.error('Elemento de toggle de modo noturno não encontrado!');
        }
        
        // Remove botão antigo se existir
        var oldBtn = document.getElementById('nightToggle');
        if(oldBtn) oldBtn.remove();
    });
      // Função global para inicializar o modo noturno
    function initializeNightMode() {
        console.log('Setting up night mode toggle');
        const modeSwitch = document.getElementById('modeSwitch');
        const modeToggle = document.getElementById('modeToggle');
        
        if (modeSwitch) {
            // Garantir que o seletor esteja visível
            modeSwitch.style.display = 'flex';
            modeSwitch.style.opacity = '1';
            modeSwitch.style.visibility = 'visible';
            console.log('Mode switch made visible');
        }
        
        if (modeToggle) {
            // Configurar o estado do toggle de acordo com a preferência salva
            const savedPreference = localStorage.getItem('nightMode') === '1';
            modeToggle.checked = savedPreference;
            
            // Aplicar o modo se necessário
            if (savedPreference) {
                setMode(true);
            }
        }
    }// Inicializar modo noturno usando o seletor existente
    // Verificar preferência sem forçar
    (function() {
        // Apenas verificamos a preferência, sem forçar o modo
        if (localStorage.getItem('nightMode') === '1') {
            // O modo será aplicado corretamente no DOMContentLoaded
        }
    })();
    
    // Quando a página carregar completamente
    window.onload = function() {
        const modeToggle = document.getElementById('modeToggle');
        if (modeToggle) {
            // Configurar o estado do toggle de acordo com a preferência
            const savedPreference = localStorage.getItem('nightMode') === '1';
            if (modeToggle.checked !== savedPreference) {
                modeToggle.checked = savedPreference;
            }
            
            // Atualizar ícone e texto
            const modeIcon = document.getElementById('modeIcon');
            const modeLabel = document.getElementById('modeLabel');
            const modeSwitch = document.getElementById('modeSwitch');
            
            if (savedPreference) {
                if (modeIcon) modeIcon.textContent = '🌙';
                if (modeLabel) modeLabel.textContent = 'Noturno';
                if (modeSwitch) modeSwitch.classList.remove('light');
            } else {
                if (modeIcon) modeIcon.textContent = '🌞';
                if (modeLabel) modeLabel.textContent = 'Claro';
                if (modeSwitch) modeSwitch.classList.add('light');
            }
        }
    };// Chat Pop-up para todos os chamados
    function openChatPopup(ticketId, email, telefone) {
        const chatModal = document.getElementById('chatModal');
        const chatIframe = document.getElementById('chatIframe');
        const chatModalContent = document.getElementById('chatModalContent');
        
        // Monta a URL do chat com os dados do chamado
        let url = `chat_frontend.html?id=${encodeURIComponent(ticketId)}`;
        if(email) url += `&email=${encodeURIComponent(email)}`;
        if(telefone) url += `&telefone=${encodeURIComponent(telefone)}`;
        chatIframe.src = url;
        chatModal.style.display = 'flex';
        
        // Night mode no modal
        if(document.body.classList.contains('night')) {
            chatModal.style.background = 'rgba(24,28,36,0.85)';
            chatModalContent.style.background = '#232a36';
            chatModalContent.classList.add('night');
        } else {
            chatModal.style.background = 'rgba(0,0,0,0.45)';
            chatModalContent.style.background = '#fff';
            chatModalContent.classList.remove('night');
        }
    }
    
    // Chat Pop-up para usuários rápidos
    function openQuickUserChatPopup(email, telefone, author) {
        const chatModal = document.getElementById('chatModal');
        const chatIframe = document.getElementById('chatIframe');
        const chatModalContent = document.getElementById('chatModalContent');
        
        let url = `chat_frontend.html?email=${encodeURIComponent(email)}&telefone=${encodeURIComponent(telefone)}&author=${encodeURIComponent(author)}`;
        chatIframe.src = url;
        chatModal.style.display = 'flex';
        
        // Night mode no modal
        if(document.body.classList.contains('night')) {
            chatModal.style.background = 'rgba(24,28,36,0.85)';
            chatModalContent.style.background = '#232a36';
            chatModalContent.classList.add('night');
        } else {
            chatModal.style.background = 'rgba(0,0,0,0.45)';
            chatModalContent.style.background = '#fff';
            chatModalContent.classList.remove('night');
        }
    }    document.querySelectorAll('.chat-popup-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            openChatPopup(this.dataset.ticketId, this.dataset.email, this.dataset.telefone);
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
    });    // Night mode dinâmico no modal
    function updateChatModalNightMode() {
        const chatModal = document.getElementById('chatModal');
        const chatModalContent = document.getElementById('chatModalContent');
        if(document.body.classList.contains('night')) {
            chatModal.style.background = 'rgba(24,28,36,0.85)';
            chatModalContent.style.background = '#232a36';
            chatModalContent.classList.add('night');
        } else {
            chatModal.style.background = 'rgba(0,0,0,0.45)';
            chatModalContent.style.background = '#fff';
            chatModalContent.classList.remove('night');
        }
    }    // Update chat modal night mode if needed
    if (document.getElementById('chatModalContent')) {
        updateChatModalNightMode();
    }
    
    // Garante que o night mode funcione em todos os elementos
    window.addEventListener('DOMContentLoaded', function() {
        // Inicializa o modo correto
        if(localStorage.getItem('nightMode')) {
            setMode(true);
            document.getElementById('modeToggle').checked = true;
        }
    });
    
    // Função para renderizar tickets a partir de uma chamada AJAX
    function renderTickets(data) {
        // Atualiza contadores nas cards
        document.getElementById('card-aberto-value').innerText = data.chamados.aberto;
        document.getElementById('card-analise-value').innerText = data.chamados.analise;
        document.getElementById('card-resolvido-value').innerText = data.chamados.resolvido;
        
        // Atualiza tabela
        let tbody = document.getElementById('tickets-tbody');
        tbody.innerHTML = ''; // Limpa o conteúdo atual
        
        data.tickets.forEach(function(ticket, i) {
            let row = document.createElement('tr');
            
            // Define o conteúdo HTML da linha com todos os dados do ticket
            row.innerHTML = `
                <td data-label="#️⃣ ID">${i + 1}</td>
                <td data-label="👤 Usuário">${ticket.created_by && ticket.created_by.username ? escapeHtml(ticket.created_by.username) : (ticket.user ? escapeHtml(ticket.user) : (ticket.name ? escapeHtml(ticket.name) : 'Usuário desconhecido'))}</td>
                <td data-label="👑 Usuário do Painel">${getPanelUsername(ticket)}</td>
                <td data-label="📦 Produto">${ticket.produto ? escapeHtml(ticket.produto) : ''}</td>
                <td data-label="📝 Assunto">${ticket.subject ? escapeHtml(ticket.subject) : ''}</td>
                <td data-label="💬 Mensagem" style='max-width:250px;word-break:break-word;'>${renderMessage(ticket)}</td>
                <td data-label="🖼️ Imagem">${ticket.imagePath ? `<a href='${escapeHtml(ticket.imagePath)}' target='_blank'><img src='${escapeHtml(ticket.imagePath)}' alt='Imagem' style='max-width:80px;max-height:80px;border-radius:6px;box-shadow:0 1px 4px #ccc;'></a>` : '<span style="color:#aaa;">-</span>'}</td>
                <td data-label="📊 Status">${renderStatus(ticket.status, i)}</td>
                <td data-label="⚙️ Ações">
                    <button type="button" class="btn action-btn chat-popup-btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background: #1976D2;color: #fff;" 
                        data-ticket-id="${i+1}" 
                        data-email="${ticket.created_by && ticket.created_by.email ? escapeHtml(ticket.created_by.email) : (ticket.email ? escapeHtml(ticket.email) : '')}" 
                        data-telefone="${ticket.created_by && ticket.created_by.telefone ? escapeHtml(ticket.created_by.telefone) : (ticket.telefone ? escapeHtml(ticket.telefone) : '')}">
                        Chat Pop-up
                    </button>
                    <a href="chat_frontend.html?id=${i+1}" class="btn action-btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#43a047;color:#fff;" target="_blank">Chat</a>
                    <form method="post" action="dashboard.php" style="margin-top:5px;display:inline-block;">
                        <input type="hidden" name="delete_id" value="${i}">
                        <button type="submit" class="btn action-btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#d70022;color:#fff;" onclick="return confirm('Tem certeza que deseja deletar este ticket?');">Deletar</button>
                    </form>
                </td>
            `;
            
            tbody.appendChild(row);
        });
        
        // Aplicar o modo correto (claro/escuro) aos novos elementos
        if(document.body.classList.contains('night')) {
            document.querySelectorAll('.ticket-table tr, .ticket-table th, .ticket-table td').forEach(el => {
                el.classList.add('night');
            });
        }
        
        // Ativar os botões de chat pop-up
        document.querySelectorAll('.chat-popup-btn').forEach(btn => {
            btn.addEventListener('click', function() {
                openChatPopup(this.dataset.ticketId, this.dataset.email, this.dataset.telefone);
            });
        });
    }
    
    // Função auxiliar para escapar HTML
    function getPanelUsername(ticket) {
        // Try to get panel username from various possible locations
        if (ticket.created_by && ticket.created_by.panel_username && ticket.created_by.panel_username.trim() !== '') {
            return escapeHtml(ticket.created_by.panel_username);
        } else if (ticket.username_painel && ticket.username_painel.trim() !== '') {
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
    
    function escapeHtml(text) {
        if (!text) return '';
        var map = { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#039;' };
        return text.toString().replace(/[&<>"']/g, function(m) { return map[m]; });
    }
    
    // Função para renderizar mensagem com campos extras
    function renderMessage(ticket) {
        let messageHtml = '';
        
        // Exibe campos extras para filmes/séries
        if ((ticket.produto || '') === 'filmes') {
            if (!!(ticket.filmes_obse_label)) messageHtml += '<b>Opção:</b> ' + escapeHtml(ticket.filmes_obse_label) + '<br>';
            else if (!!(ticket.filmes_obse)) messageHtml += '<b>Opção:</b> ' + escapeHtml(ticket.filmes_obse) + '<br>';
            messageHtml += '<b>🍿 Filme:</b> ' + escapeHtml(ticket.filme_nome || '-') + '<br>';
            messageHtml += '<b>🌟 TMDB:</b> ' + escapeHtml(ticket.filme_tmdb || '-') + '<br>';
            if (!!(ticket.filme_obs)) messageHtml += '<b>⚠️OBS:</b> ' + escapeHtml(ticket.filme_obs) + '<br>';
            messageHtml += '<hr style="margin:4px 0;">';
        } else if ((ticket.produto || '') === 'series') {
            if (!!(ticket.series_obse_label)) messageHtml += '<b>Opção:</b> ' + escapeHtml(ticket.series_obse_label) + '<br>';
            else if (!!(ticket.series_obse)) messageHtml += '<b>Opção:</b> ' + escapeHtml(ticket.series_obse) + '<br>';
            messageHtml += '<b>📽 Série:</b> ' + escapeHtml(ticket.serie_nome || '-') + '<br>';
            messageHtml += '<b>🌟 TMDB:</b> ' + escapeHtml(ticket.serie_tmdb || '-') + '<br>';
            if (!!(ticket.serie_obs)) messageHtml += '<b>⚠️OBS:</b> ' + escapeHtml(ticket.serie_obs) + '<br>';
            messageHtml += '<hr style="margin:4px 0;">';
        }
        
        // Adiciona a mensagem principal
        messageHtml += (ticket.message || '').replace(/\n/g, '<br>');
        
        return messageHtml;
    }
    
    // Função para renderizar status com seletor
    function renderStatus(status, i) {
        let statusText = '';
        if (status === 'resolvido') {
            statusText = '<span style="color:#388e3c;font-weight:bold;">✅ Resolvido</span>';
        } else if (status === 'em_analise') {
            statusText = '<span style="color:#fbc02d;font-weight:bold;">⏳ Em análise</span>';
        } else {
            statusText = '<span style="color:#d32f2f;font-weight:bold;">🔒 Não aberto</span>';
        }
        
        return `${statusText}
            <form method="post" action="dashboard.php" style="margin-top:5px;display:inline-block;" onsubmit="return alterarStatusDashboard(this, event)">
                <input type="hidden" name="id" value="${i}">
                <select name="status" style="padding:2px 6px;">
                    <option value="nao_aberto" ${status==='nao_aberto'?'selected':''}>Não aberto</option>
                    <option value="em_analise" ${status==='em_analise'?'selected':''}>Em análise</option>
                    <option value="resolvido" ${status==='resolvido'?'selected':''}>Resolvido</option>
                </select>
                <button type="submit" class="btn action-btn" style="padding:2px 10px;font-size:13px;margin-left:4px;background:#0078d7;color:#fff;">Alterar</button>
            </form>`;
    }
    
    // Função para alterar status via ajax
    function alterarStatusDashboard(form, event) {
        event.preventDefault();
        const id = form.querySelector('input[name="id"]').value;
        const status = form.querySelector('select[name="status"]').value;
        
        fetch('update_ticket_status.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ id, status })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                updateTickets();
            } else {
                alert('Erro ao atualizar status');
            }
        })
        .catch(error => {
            console.error('Erro:', error);
            alert('Erro ao atualizar status');
        });
        
        return false;
    }
    
    // Função para buscar dados atualizados
    function updateTickets() {
        fetch('dashboard_data.php')
            .then(response => response.json())
            .then(data => {
                renderTickets(data);
            })
            .catch(error => {
                console.error('Erro ao atualizar tickets:', error);
            });
    }
    
    // Inicializar e configurar atualização periódica
    document.addEventListener('DOMContentLoaded', function() {
        // Carrega os tickets inicialmente
        updateTickets();
        
        // Atualiza a cada 30 segundos (30000ms)
        setInterval(updateTickets, 30000);
    });
    </script>
    </body>
</html>
