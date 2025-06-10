<?php
header('Content-Type: application/json; charset=utf-8');
$input = json_decode(file_get_contents('php://input'), true);
$id = isset($input['id']) ? intval($input['id']) : -1;
$status = isset($input['status']) ? $input['status'] : '';
$file = __DIR__ . '/../logs/tickets.txt';
if ($id < 0 || !$status) {
    echo json_encode(['success' => false, 'error' => 'Dados inválidos']);
    exit;
}
if (!file_exists($file)) {
    echo json_encode(['success' => false, 'error' => 'Arquivo não encontrado']);
    exit;
}
$tickets = json_decode(file_get_contents($file), true);
if (!is_array($tickets) || !isset($tickets[$id])) {
    echo json_encode(['success' => false, 'error' => 'Ticket não encontrado']);
    exit;
}
$tickets[$id]['status'] = $status;
file_put_contents($file, json_encode($tickets, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
echo json_encode(['success' => true]);
