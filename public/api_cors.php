<?php
// Arquivo para padronizar CORS e credenciais para API PHP
header('Access-Control-Allow-Origin: *'); // Alterado para permitir qualquer origem na hospedagem
header('Access-Control-Allow-Credentials: true');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    header('Access-Control-Allow-Headers: Content-Type');
    header('Access-Control-Allow-Methods: POST, GET, OPTIONS');
    exit;
}
