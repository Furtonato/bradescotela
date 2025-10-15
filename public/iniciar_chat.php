<?php
require __DIR__ . '/../includes/db.php';
session_name('CLIENTSESS');
session_start();

$cookie = $_COOKIE['identificador_cliente'] ?? null;

if (!$cookie) {
    http_response_code(400);
    echo 'Cookie não encontrado.';
    exit;
}

// Busca o cliente pelo cookie
$stmt = $pdo->prepare("SELECT id FROM clients WHERE identificador_cookie = ? ORDER BY id DESC LIMIT 1");
$stmt->execute([$cookie]);
$client = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$client) {
    // Se não existir, cria com status 'guest'
    $insert = $pdo->prepare("INSERT INTO clients (identificador_cookie, status, created_at) VALUES (?, 'guest', NOW())");
    $insert->execute([$cookie]);

    // Busca o ID recém criado
    $client_id = $pdo->lastInsertId();
} else {
    $client_id = $client['id'];
}

// Agora garante que existe em chat_status
$stmt = $pdo->prepare("SELECT * FROM chat_status WHERE client_id = ?");
$stmt->execute([$client_id]);

if ($stmt->fetch()) {
    $update = $pdo->prepare("UPDATE chat_status SET entrou_admin = true WHERE client_id = ?");
    $update->execute([$client_id]);
} else {
    $insert = $pdo->prepare("INSERT INTO chat_status (client_id, entrou_admin) VALUES (?, true)");
    $insert->execute([$client_id]);
}

echo 'OK';
