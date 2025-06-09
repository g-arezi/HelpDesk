<?php
header('Content-Type: application/json; charset=utf-8');
$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$telefone = isset($_GET['telefone']) ? trim($_GET['telefone']) : '';
$file = __DIR__ . '/../logs/tickets.txt';
$result = [];
if ((($email && filter_var($email, FILTER_VALIDATE_EMAIL)) || $telefone) && file_exists($file)) {
    $content = file_get_contents($file);
    $tickets = json_decode($content, true) ?: [];
    foreach ($tickets as $idx => $ticket) {
        if (
            ($email && isset($ticket['email']) && strtolower($ticket['email']) === strtolower($email)) ||
            ($telefone && isset($ticket['telefone']) && $ticket['telefone'] === $telefone)
        ) {
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
echo json_encode($result, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
