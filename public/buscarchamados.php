<?php
require_once __DIR__ . '/api_cors.php';
header('Content-Type: application/json; charset=utf-8');
session_start(); // Start session to be able to check for logged-in user

// API: retorna apenas JSON, sem HTML
// Uso: GET /buscarchamados.php?email=...&telefone=...
// Se usuário estiver logado, também procura por username

$email = isset($_GET['email']) ? trim($_GET['email']) : '';
$telefone = isset($_GET['telefone']) ? trim($_GET['telefone']) : '';
$username = isset($_SESSION['user']) ? $_SESSION['user'] : '';
$file = __DIR__ . '/../logs/tickets.txt'; 
$result = [];
$error = '';

if ((($email && filter_var($email, FILTER_VALIDATE_EMAIL)) || $telefone || $username) && file_exists($file)) {
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
    }    foreach ($tickets as $idx => $ticket) {
        $ticketEmail = isset($ticket['email']) ? strtolower(trim($ticket['email'])) : '';
        $ticketTelefone = isset($ticket['telefone']) ? trim($ticket['telefone']) : '';
        $ticketUser = isset($ticket['user']) ? trim($ticket['user']) : '';
        $ticketCreatedBy = isset($ticket['created_by']) && isset($ticket['created_by']['username']) ? 
                          trim($ticket['created_by']['username']) : '';

        $match = false;
        
        // Verificar correspondência de nome de usuário se estiver logado
        if ($username && ($username === $ticketUser || $username === $ticketCreatedBy)) {
            $match = true;
        }
        // Verificar email e telefone conforme o método antigo
        else if ($email && $telefone) {
            // Ambos preenchidos: busca por ambos
            $match = ($ticketEmail === strtolower($email)) && ($ticketTelefone === $telefone);
        } elseif ($email) {
            // Verifica também o email do created_by
            $createdByEmail = isset($ticket['created_by']) && isset($ticket['created_by']['email']) ? 
                             strtolower(trim($ticket['created_by']['email'])) : '';
            $match = ($ticketEmail === strtolower($email)) || ($createdByEmail === strtolower($email));
        } elseif ($telefone) {
            // Verifica também o telefone do created_by
            $createdByTelefone = isset($ticket['created_by']) && isset($ticket['created_by']['telefone']) ? 
                               trim($ticket['created_by']['telefone']) : '';
            $match = ($ticketTelefone === $telefone) || ($createdByTelefone === $telefone);
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
