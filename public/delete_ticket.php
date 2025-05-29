<?php
session_start();
if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
    header('Location: login.php');
    exit;
}

$file = __DIR__ . '/../tickets.txt';
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: tickets.php');
    exit;
}
$id = (int)$_GET['id'];

// Carrega tickets
$tickets = file_exists($file) ? file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) : [];
if (!isset($tickets[$id])) {
    header('Location: tickets.php');
    exit;
}

unset($tickets[$id]);
file_put_contents($file, implode("\n", $tickets));
header('Location: tickets.php');
exit;
