<?php
header('Content-Type: application/json; charset=utf-8');
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$telefone = isset($_GET['telefone']) ? trim($_GET['telefone']) : '';
$file = __DIR__ . '/../logs/tickets.txt';
$result = [];
if ((($email && filter_var($email, FILTER_VALIDATE_EMAIL)) || $telefone) && file_exists($file)) {
    $content = file_get_contents($file);
    $tickets = [];
    $tryArray = json_decode($content, true);
    if (is_array($tryArray)) {
        $tickets = $tryArray;
    } else {
        $lines = preg_split('/\r?\n/', $content);
        foreach ($lines as $line) {
            $line = trim($line);
            if ($line) {
                $ticket = json_decode($line, true);
                if (is_array($ticket)) {
                    $tickets[] = $ticket;
                }
            }
        }
    }
    foreach ($tickets as $idx => $ticket) {
        $ticketEmail = isset($ticket['email']) ? strtolower(trim($ticket['email'])) : '';
        $ticketTelefone = isset($ticket['telefone']) ? trim($ticket['telefone']) : '';
        $match = false;
        if ($email && $telefone) {
            // Ambos preenchidos: busca por ambos EXATOS
            $match = ($ticketEmail === strtolower($email)) && ($ticketTelefone === $telefone);
        } elseif ($email) {
            $match = ($ticketEmail === strtolower($email));
        } elseif ($telefone) {
            $match = ($ticketTelefone === $telefone);
        }
        if ($match) {
            $statusMap = [
                'nao_aberto' => 'Não aberto',
                'em_analise' => 'Em análise',
                'resolvido' => 'Resolvido'
            ];
            $result[] = [
                'id' => $idx + 1,
                'status' => $statusMap[$ticket['status'] ?? 'nao_aberto'] ?? 'Não aberto',
                'subject' => $ticket['subject'] ?? '',
                'message' => $ticket['message'] ?? '',
                'telefone' => $ticket['telefone'] ?? ''
            ];
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Buscar Chamados</title>
    <style>
        body { font-family: Arial, sans-serif; background: #f4f8fb; margin:0; transition: background 0.3s, color 0.3s; }
        .container { max-width: 700px; margin: 60px auto 30px auto; background: #fff; border-radius: 14px; box-shadow: 0 4px 24px #0002; padding: 36px 40px 30px 40px; }
        h2 { color: #1976d2; text-align: center; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; background: #fff; border-radius: 8px; overflow: hidden; box-shadow:0 1px 8px #e0e0e0; }
        th, td { padding: 12px; text-align: left; border-bottom: 1px solid #e0e0e0; }
        th { background-color: #f2f2f2; color: #1976d2; font-weight: bold; }
        tr:hover { background-color: #f5f5f5; }
        .btn { padding: 8px 16px; background-color: #0078d7; color: white; border: none; border-radius: 6px; cursor: pointer; font-size: 14px; box-shadow:0 1px 4px #0001; transition: background 0.2s; }
        .btn:hover { background-color: #0056a3; }
        .night-toggle { position:fixed; bottom:24px; left:24px; right:auto; top:auto; z-index:1000; background:linear-gradient(90deg,#ff6b6b,#b71c1c); color:#fff; border:1px solid #b71c1c; border-radius:20px; padding:8px 18px; cursor:pointer; font-weight:bold; box-shadow:0 2px 8px #0002; transition: background 0.3s, color 0.3s; }
        .night-toggle.night { background:linear-gradient(90deg,#b71c1c,#ff6b6b); color:#fff; border-color:#fff; }
        body.night { background: #181c24; color: #e0e0e0; }
        .container.night { background: #232a36; color: #e0e0e0; }
        table.night { background: #232a36; color: #e0e0e0; }
        th.night { background: #263238; color: #90caf9; }
        tr.night:hover { background-color: #222b38; }
        .btn.night { background: #1976d2; color: #fff; }
        .btn.night:hover { background: #1565c0; }
    </style>
</head>
<body>
    <button class="night-toggle" id="nightToggle" onclick="toggleNightMode()">🌙 Modo Noturno</button>
    <div class="container" id="container">
        <h2>Buscar Chamados</h2>
        <form method="get" style="margin-bottom:20px;display:flex;gap:10px;flex-wrap:wrap;align-items:center;">
            <input type="email" name="email" placeholder="E-mail" value="<?=htmlspecialchars($email)?>" style="flex:1;min-width:180px;" class="form-field">
            <input type="text" name="telefone" placeholder="Telefone" value="<?=htmlspecialchars($telefone)?>" style="flex:1;min-width:180px;" class="form-field">
            <button type="submit" class="btn">Buscar</button>
        </form>
        <?php if (!empty($result)): ?>
        <table id="resultTable">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Status</th>
                    <th>Assunto</th>
                    <th>Mensagem</th>
                    <th>Telefone</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($result as $row): ?>
                <tr>
                    <td><?= htmlspecialchars($row['id']) ?></td>
                    <td><?= htmlspecialchars($row['status']) ?></td>
                    <td><?= htmlspecialchars($row['subject']) ?></td>
                    <td><?= htmlspecialchars($row['message']) ?></td>
                    <td><?= htmlspecialchars($row['telefone']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php elseif ($_GET): ?>
            <p style="color:#d32f2f;">Nenhum chamado encontrado para os dados informados.</p>
        <?php endif; ?>
    </div>
    <script>
    function toggleNightMode(force) {
        let night;
        if (typeof force === 'boolean') {
            night = force;
            document.body.classList.toggle('night', night);
        } else {
            night = document.body.classList.toggle('night');
        }
        document.getElementById('container').classList.toggle('night', night);
        document.querySelectorAll('table').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('th').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('tr').forEach(e=>e.classList.toggle('night', night));
        document.querySelectorAll('.btn').forEach(e=>e.classList.toggle('night', night));
        document.getElementById('nightToggle').classList.toggle('night', night);
        if(night) localStorage.setItem('nightMode','1');
        else localStorage.removeItem('nightMode');
    }
    // Aplica modo noturno se já estava ativado
    if(localStorage.getItem('nightMode')) toggleNightMode(true);
    </script>
</body>
</html>
