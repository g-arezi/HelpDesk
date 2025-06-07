<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
$tecnico = isset($_SESSION['role']) && $_SESSION['role'] === 'tecnico';
echo json_encode(['tecnico' => $tecnico]);
