<?php
// Arquivo de entrada principal para o site HelpDesk
require_once __DIR__ . '/../vendor/autoload.php';

// Iniciar a sessão para verificar autenticação
session_start();

// Verificar se o usuário está autenticado
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    // Redirecionar para a página de login
    header('Location: login.php?access=denied&redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}

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
