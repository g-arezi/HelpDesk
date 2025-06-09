<?php
require_once __DIR__ . '/api_cors.php';
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['auth']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'tecnico')) {
    http_response_code(401);
    echo json_encode(['error' => 'Não autorizado']);
    exit;
}

$file = __DIR__ . '/../logs/tickets.txt';
$tickets = [];
if (file_exists($file)) {
    $content = file_get_contents($file);
    $tickets = json_decode($content, true) ?: [];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = isset($input['id']) ? (int)$input['id'] : null;
    $status = isset($input['status']) ? $input['status'] : null;
    if ($id !== null && $status && isset($tickets[$id])) {
        $tickets[$id]['status'] = $status;
        file_put_contents($file, json_encode($tickets, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        echo json_encode(['success' => true, 'ticket' => $tickets[$id]]);
        exit;
    } else {
        http_response_code(400);
        echo json_encode(['error' => 'Dados inválidos ou ticket não encontrado.']);
        exit;
    }
}

echo json_encode($tickets, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
