<?php
// client_auth_check.php - Use this file to protect client-only pages
session_start();

// Check if this is an AJAX request expecting JSON
if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
    header('Content-Type: application/json');
    $response = [
        'authenticated' => false,
        'user' => null,
        'role' => null
    ];

    if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {
        $response['authenticated'] = true;
        $response['user'] = $_SESSION['user'] ?? null;
        $response['role'] = $_SESSION['role'] ?? null;
    }

    echo json_encode($response);
    exit;
} 
// Regular page navigation check
else if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true || $_SESSION['role'] !== 'cliente') {
    // Redirect to login page with a message
    header('Location: login.php?access=denied&redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
