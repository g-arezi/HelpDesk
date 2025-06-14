<?php
session_start();
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: login.php');
    exit;
}

$file = __DIR__ . '/../logs/tickets.txt';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tickets.php');
    exit;
}
$id = (int)$_GET['id'];

$tickets = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
if (!isset($tickets[$id])) {
    header('Location: tickets.php');
    exit;
}
$ticket = json_decode($tickets[$id], true);

// Atualizar ticket
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $ticket['name'] = $_POST['name'] ?? $ticket['name'];
    $ticket['email'] = $_POST['email'] ?? $ticket['email'];
    $ticket['subject'] = $_POST['subject'] ?? $ticket['subject'];
    $ticket['message'] = $_POST['message'] ?? $ticket['message'];
    // Não permite alterar imagem por simplicidade
    $tickets[$id] = json_encode($ticket);
    file_put_contents($file, implode("\n", $tickets));
    header('Location: tickets.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>Editar Ticket</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/mobile.css">
</head>
<body>
    <div class="container">
        <h2>Editar Ticket</h2>
        <form method="post">
            <label>Nome:<br><input type="text" name="name" value="<?= htmlspecialchars($ticket['name']) ?>" required></label><br><br>
            <label>E-mail:<br><input type="email" name="email" value="<?= htmlspecialchars($ticket['email']) ?>" required></label><br><br>
            <label>Assunto:<br><input type="text" name="subject" value="<?= htmlspecialchars($ticket['subject']) ?>" required></label><br><br>
            <label>Mensagem:<br><textarea name="message" required><?= htmlspecialchars($ticket['message']) ?></textarea></label><br><br>
            <button type="submit" class="btn">Salvar Alterações</button>
            <a href="tickets.php" class="btn">Cancelar</a>
        </form>
    </div>
</body>
</html>
