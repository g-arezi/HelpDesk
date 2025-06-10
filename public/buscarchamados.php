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
            // Ambos preenchidos: busca por ambos
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
} elseif ($_GET) {
    $error = 'Nenhum chamado encontrado para os dados informados.';
}

http_response_code(200);
echo json_encode([
    'success' => empty($error),
    'error' => $error,
    'tickets' => $result
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
exit;
