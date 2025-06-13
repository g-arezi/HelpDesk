<?php
session_start();
header('Content-Type: application/json');
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    echo json_encode(['success' => false, 'error' => 'not_authenticated']);
    exit;
}

$file = __DIR__ . '/../logs/tickets.txt';
// Suporta POST (JSON) e GET (fallback)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $id = isset($data['id']) && is_numeric($data['id']) ? (int)$data['id'] : null;
} else {
    $id = isset($_GET['id']) && is_numeric($_GET['id']) ? (int)$_GET['id'] : null;
}
if ($id === null) {
    echo json_encode(['success' => false, 'error' => 'invalid_id']);
    exit;
}

// Carrega tickets (JSON)
$tickets = [];
if (file_exists($file)) {
    $json = file_get_contents($file);
    $tickets = json_decode($json, true);
    if (!is_array($tickets)) $tickets = [];
}
if (!isset($tickets[$id])) {
    echo json_encode(['success' => false, 'error' => 'not_found', 'id' => $id, 'ticket_count' => count($tickets)]);
    exit;
}

// Remove o ticket pelo índice
array_splice($tickets, $id, 1);
// Reindexar array após remover o item
$tickets = array_values($tickets);
// Salva o array atualizado
file_put_contents($file, json_encode($tickets, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo json_encode(['success' => true, 'message' => 'Ticket deleted successfully']);
exit;
