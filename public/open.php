<?php
// Arquivo de entrada principal para o site HelpDesk
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\TicketController;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$controller = new TicketController();
$controller->open();
