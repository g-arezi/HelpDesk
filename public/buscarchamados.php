<?php
require_once __DIR__ . '/api_cors.php';
header('Content-Type: application/json; charset=utf-8');

// API: retorna apenas JSON, sem HTML
// Uso: GET /buscarchamados.php?email=...&telefone=...

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$telefone = isset($_GET['telefone']) ? trim($_GET['telefone']) : '';
$file = __DIR__ . '/../logs/tickets.txt'; 
$result = [];
$error = '';
if ((($email && filter_var($email, FILTER_VALIDATE_EMAIL)) || $telefone) && file_exists($file)) {
    $content = file_get_contents($file);
    $tickets = json_decode($content, true) ?: [];
    foreach ($tickets as $idx => $ticket) {
        $matchEmail = $email && isset($ticket['email']) && strtolower($ticket['email']) === strtolower($email);
        $matchTelefone = $telefone && isset($ticket['telefone']) && $ticket['telefone'] === $telefone;
        if ($matchEmail || $matchTelefone) {
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
} elseif ($_GET) {
    $error = 'Nenhum chamado encontrado para os dados informados.';
}

http_response_code(200);
echo json_encode([
    'success' => empty($error),
    'error' => $error,
    'tickets' => $result
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
