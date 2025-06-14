<?php
session_start();

// Check if user is authenticated
if (isset($_SESSION['auth']) && $_SESSION['auth'] === true) {    // Redirect based on role
    if ($_SESSION['role'] === 'cliente') {
        header('Location: buscarchamados_page.php');
    } else {
        header('Location: dashboard.php');
    }
    exit;
}

// Not authenticated, redirect to login
header('Location: login.php');
exit;
