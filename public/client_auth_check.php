<?php
// client_auth_check.php - Use this file to protect client-only pages
session_start();

// Check if user is authenticated and is a client
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true || $_SESSION['role'] !== 'cliente') {
    // Redirect to login page with a message
    header('Location: login.php?access=denied&redirect=' . urlencode($_SERVER['REQUEST_URI']));
    exit;
}
