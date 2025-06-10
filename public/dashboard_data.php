<?php
header('Content-Type: application/json; charset=utf-8');
$ticketsFile = __DIR__ . '/../logs/tickets.txt';
$chamados = [
    'aberto' => 0, // não aberto
    'analise' => 0, // em analise
    'resolvido' => 0
];
$tickets = [];
if (file_exists($ticketsFile)) {
    $tickets = json_decode(file_get_contents($ticketsFile), true);
    if (!is_array($tickets)) $tickets = [];
    foreach ($tickets as $t) {
        $status = strtolower($t['status'] ?? 'nao_aberto');
        if ($status === 'nao_aberto') $chamados['aberto']++;
        elseif ($status === 'em_analise') $chamados['analise']++;
        elseif ($status === 'resolvido') $chamados['resolvido']++;
        else $chamados['aberto']++; // fallback
    }
}
echo json_encode([
    'chamados' => $chamados,
    'tickets' => $tickets
], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
