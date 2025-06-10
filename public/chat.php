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

$method = $_SERVER['REQUEST_METHOD'];
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$chatFile = __DIR__ . "/../logs/chat_$id.txt";

if ($id <= 0) {
    echo json_encode(['error' => 'ID do chamado inválido']);
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
    session_start();
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
