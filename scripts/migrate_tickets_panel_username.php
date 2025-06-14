<?php
// Migration script to add panel_username to existing tickets
// Run this once to update all tickets

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

// Keep track of updates
$updatedCount = 0;

// Update each ticket
foreach ($tickets as &$ticket) {
    // Check if created_by exists
    if (!isset($ticket['created_by'])) {
        // Create created_by object with available data
        $ticket['created_by'] = [
            'username' => $ticket['user'] ?? 'Usuário desconhecido',
            'email' => $ticket['email'] ?? '',
            'telefone' => $ticket['telefone'] ?? '',
            'role' => 'cliente',
            'panel_username' => ''  // Empty by default
        ];
        $updatedCount++;
    } else if (!isset($ticket['created_by']['panel_username'])) {
        // Add panel_username field if created_by exists but panel_username doesn't
        $ticket['created_by']['panel_username'] = $ticket['username_painel'] ?? '';
        $updatedCount++;
    }

    // Also update username_painel field for backward compatibility
    if (!isset($ticket['username_painel']) && isset($ticket['created_by']['panel_username'])) {
        $ticket['username_painel'] = $ticket['created_by']['panel_username'];
    }
}

// Save the updated tickets
file_put_contents($ticketsFile, json_encode($tickets, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

echo "Migration completed. Updated $updatedCount tickets.\n";
echo "All tickets now have created_by structure with panel_username field.\n";
