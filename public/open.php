<?php
// Arquivo de entrada principal para o site HelpDesk
require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\TicketController;

$controller = new TicketController();
$controller->open();
