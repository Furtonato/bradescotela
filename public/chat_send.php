<?php
// public/chat_send.php
session_start();
require __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$cookie = $_COOKIE['identificador_cliente'] ?? '';
if ($cookie === '') {
    echo json_encode(['error'=>'cookie ausente']);
    exit;
}

// busca ou cria cliente
$stmt = $pdo->prepare("SELECT id FROM clients WHERE identificador_cookie=?");
$stmt->execute([$cookie]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if ($row) {
    $clientId = $row['id'];
} else {
    $stmt = $pdo->prepare("INSERT INTO clients (identificador_cookie,status,created_at) VALUES (?, 'chat', NOW())");
    $stmt->execute([$cookie]);
    $clientId = $pdo->lastInsertId();
}
$_SESSION['client_id'] = $clientId;

// texto
$text = trim($_POST['message'] ?? '');
if ($text === '') {
    echo json_encode(['success'=>true]);
    exit;
}

$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->execute([$clientId, 0, $text]);
echo json_encode(['success'=>true]);
