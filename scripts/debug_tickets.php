<?php
// Script para visualizar a estrutura de dados dos tickets
// Útil para diagnosticar problemas e verificar se os campos necessários existem

// Path to the tickets file
$ticketsFile = __DIR__ . '/../logs/tickets.txt';

// Check if file exists
if (!file_exists($ticketsFile)) {
    echo "Error: Tickets file not found at $ticketsFile\n";
    exit(1);
}

// Read the tickets
$content = file_get_contents($ticketsFile);
$tickets = json_decode($content, true);

if (!is_array($tickets)) {
    echo "Error: Could not parse tickets file as JSON array\n";
    exit(1);
}

echo "Total tickets: " . count($tickets) . "\n\n";

// Display a summary of each ticket
foreach ($tickets as $index => $ticket) {
    echo "====== Ticket #" . ($index + 1) . " ======\n";
    
    // Basic info
    echo "Status: " . ($ticket['status'] ?? 'not set') . "\n";
    echo "Produto: " . ($ticket['produto'] ?? 'not set') . "\n";
    echo "Subject: " . ($ticket['subject'] ?? 'not set') . "\n";
    
    // User info
    echo "User field: " . ($ticket['user'] ?? 'not set') . "\n";
    
    // Created by structure
    if (isset($ticket['created_by'])) {
        echo "Created by:\n";
        echo "  - username: " . ($ticket['created_by']['username'] ?? 'not set') . "\n";
        echo "  - email: " . ($ticket['created_by']['email'] ?? 'not set') . "\n";
        echo "  - telefone: " . ($ticket['created_by']['telefone'] ?? 'not set') . "\n";
        echo "  - role: " . ($ticket['created_by']['role'] ?? 'not set') . "\n";
        echo "  - panel_username: " . ($ticket['created_by']['panel_username'] ?? 'not set') . "\n";
    } else {
        echo "Created by: not set\n";
    }
    
    // Compatibility fields
    echo "Email (legacy): " . ($ticket['email'] ?? 'not set') . "\n";
    echo "Telefone (legacy): " . ($ticket['telefone'] ?? 'not set') . "\n";
    echo "Username Painel (legacy): " . ($ticket['username_painel'] ?? 'not set') . "\n";
    
    echo "\n";
}

echo "Dump completed.\n";
