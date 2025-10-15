<?php
// File: public/verificar_status.php

require __DIR__ . '/../includes/db.php';

// Retornar JSON
header('Content-Type: application/json; charset=utf-8');

$id = $_GET['id'] ?? null;
if (!$id) {
    http_response_code(400);
    echo json_encode(['error' => 'invalid_id']);
    exit;
}

// Busca status e tipo
$stmt = $pdo->prepare("SELECT status, tipo FROM clients WHERE id = ?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $status = $row['status'] ?? 'pending';
    // se não houver tipo no banco, assume 'classic' como padrão
    $tipo   = $row['tipo']   ?? 'classic';
} else {
    // cliente não encontrado: status pendente e tipo classic
    $status = 'pending';
    $tipo   = 'classic';
}

// Retorna o objeto JSON com status e tipo
echo json_encode([
    'status' => $status,
    'tipo'   => $tipo
]);
