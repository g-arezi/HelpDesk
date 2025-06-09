<?php
// Script para adicionar o campo 'status' aos tickets antigos
$file = __DIR__ . '/../logs/tickets.txt';
if (!file_exists($file)) {
    echo "Arquivo tickets.txt não encontrado.\n";
    exit(1);
}
$content = file_get_contents($file);
$tickets = json_decode($content, true);
if (!is_array($tickets)) {
    echo "Formato inválido em tickets.txt\n";
    exit(1);
}
$alterado = false;
foreach ($tickets as &$ticket) {
    if (!isset($ticket['status']) || $ticket['status'] === '' || $ticket['status'] === 'Desconhecido') {
        $ticket['status'] = 'nao_aberto';
        $alterado = true;
    }
}
unset($ticket);
if ($alterado) {
    $json = json_encode($tickets, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    if ($json === false) {
        echo "Erro ao codificar JSON.\n";
        exit(1);
    }
    $ok = file_put_contents($file, $json);
    if ($ok === false) {
        echo "Erro ao salvar tickets.txt. Verifique permissões.\n";
        exit(1);
    }
    echo "Tickets atualizados com sucesso!\n";
    echo $json . "\n";
} else {
    echo "Todos os tickets já possuem status.\n";
}
