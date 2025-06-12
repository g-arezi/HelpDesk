<?php
// Remove session_start() duplicado, pois dashboard.php já inicia a sessão
// if (!isset($_SESSION)) session_start();
if (!isset($_SESSION['auth']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'tecnico')) {
    header('Location: login.php');
    exit;
}

$usersFile = __DIR__ . '/../logs/quick_users.txt';
$users = file_exists($usersFile) ? json_decode(file_get_contents($usersFile), true) : [];
if (!is_array($users)) $users = [];

// Exibe lista para inclusão via include em dashboard.php
?><div class="section" id="quick-users-section" style="margin-bottom:24px;">
    <h3>Usuários Rápidos para Chat</h3>
    <form method="post" style="display:flex;gap:8px;flex-wrap:wrap;align-items:center;margin-bottom:10px;">
        <input type="text" name="nickname" placeholder="Apelido/Nome" required style="min-width:120px;">
        <input type="email" name="email" placeholder="E-mail">
        <input type="text" name="telefone" placeholder="Telefone">
        <button type="submit" class="btn" style="background:#1976d2;color:#fff;">Adicionar</button>
    </form>
    <div style="overflow-x:auto;">
    <table style="width:100%;background:#fff;border-radius:8px;box-shadow:0 1px 6px #e0e0e0;">
        <thead><tr><th>Apelido</th><th>E-mail</th><th>Telefone</th><th>Ação</th><th>Chat</th></tr></thead>
        <tbody>
        <?php foreach($users as $i=>$u): ?>
            <tr>
                <td><?= htmlspecialchars($u['nickname']) ?></td>
                <td><?= htmlspecialchars($u['email']) ?></td>
                <td><?= htmlspecialchars($u['telefone']) ?></td>
                <td><a href="dashboard.php?quickusers=1&del=<?= $i ?>" onclick="return confirm('Remover este usuário rápido?')" style="color:#d32f2f;font-weight:bold;">Remover</a></td>
                <td>
                    <a href="chat_frontend.html?email=<?= urlencode($u['email']) ?>&telefone=<?= urlencode($u['telefone']) ?>&author=<?= urlencode($u['nickname']) ?>" target="_blank" class="btn" style="background:#43a047;color:#fff;">Acessar Chat</a>
                    <button type="button" class="btn chat-popup-btn" style="background:#1976d2;color:#fff;margin-left:4px;" data-email="<?= htmlspecialchars($u['email']) ?>" data-telefone="<?= htmlspecialchars($u['telefone']) ?>" data-author="<?= htmlspecialchars($u['nickname']) ?>">Pop-up</button>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    </div>
</div>
