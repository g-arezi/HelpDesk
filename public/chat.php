<?php
require_once __DIR__ . '/api_cors.php';
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

session_start();
$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$chatFile = __DIR__ . "/../logs/chat_$id.txt";
$ticketsFile = __DIR__ . '/../logs/tickets.txt';

// Função para validar se o usuário pode acessar o chat do ticket
function isTicketOwner($id, $email, $telefone) {
    global $ticketsFile;
    if (!file_exists($ticketsFile)) return false;
    $tickets = json_decode(file_get_contents($ticketsFile), true);
    if (!is_array($tickets)) return false;
    $idx = $id - 1; // IDs são 1-based na interface
    if (!isset($tickets[$idx])) return false;
    $ticket = $tickets[$idx];
    $ticketEmail = strtolower(trim($ticket['email'] ?? ''));
    $ticketTelefone = trim($ticket['telefone'] ?? '');
    if ($email && strtolower($email) === $ticketEmail) return true;
    if ($telefone && $telefone === $ticketTelefone) return true;
    return false;
}

// Permitir admin/tecnico autenticado acessar qualquer chat
function isAdminOrTecnico() {
    return isset($_SESSION['auth'], $_SESSION['role']) && $_SESSION['auth'] === true && in_array($_SESSION['role'], ['admin', 'tecnico']);
}

if ($id <= 0) {
    echo json_encode(['error' => 'ID do chamado inválido']);
    exit;
}

// Obter email/telefone do request (GET ou POST)
$email = '';
$telefone = '';
if ($method === 'GET') {
    $email = isset($_GET['email']) ? trim($_GET['email']) : '';
    $telefone = isset($_GET['telefone']) ? trim($_GET['telefone']) : '';
} elseif ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $email = trim($input['email'] ?? '');
    $telefone = trim($input['telefone'] ?? '');
}

if (!isAdminOrTecnico() && !isTicketOwner($id, $email, $telefone)) {
    echo json_encode(['error' => 'Acesso negado ao chat deste chamado.']);
    exit;
}

if ($method === 'GET') {
    // Buscar mensagens
    if (file_exists($chatFile)) {
        $messages = json_decode(file_get_contents($chatFile), true) ?: [];
    } else {
        $messages = [];
    }
    echo json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    exit;
}

if ($method === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $author = trim($input['author'] ?? '');
    $message = trim($input['message'] ?? '');
    if (!$author || !$message) {
        echo json_encode(['error' => 'Autor e mensagem são obrigatórios']);
        exit;
    }
    $msg = [
        'author' => $author,
        'message' => $message,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    $messages = file_exists($chatFile) ? json_decode(file_get_contents($chatFile), true) : [];
    $messages[] = $msg;
    file_put_contents($chatFile, json_encode($messages, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
    echo json_encode(['success' => true]);
    exit;
}

echo json_encode(['error' => 'Método não suportado']);
exit;
