<?php
// File: admin/chat_fetch.php

ini_set('display_errors', 1);
error_reporting(E_ALL);

header('Content-Type: application/json');
session_name('ADMINSESS');
session_set_cookie_params([
  'lifetime' => 604800,
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();


require __DIR__ . '/../../includes/db.php';

// 1) Verifica se admin está logado
if (empty($_SESSION['admin'])) {
  http_response_code(401);
  echo json_encode(['error' => 'Não autorizado']);
  exit;
}

// 2) Verifica se o cookie do cliente foi informado
$cookie = trim($_GET['cookie'] ?? '');
if ($cookie === '') {
  echo json_encode([]);
  exit;
}

// 3) Busca o client_id correspondente ao cookie
$stmt = $pdo->prepare("SELECT id FROM clients WHERE identificador_cookie = ?");
$stmt->execute([$cookie]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$row) {
  echo json_encode([]);
  exit;
}

$client_id = intval($row['id']);
$since_id = intval($_GET['since_id'] ?? 0);

// ✅ Garante que exista o chat_status
$pdo->prepare("INSERT INTO chat_status (client_id) VALUES (?) ON CONFLICT DO NOTHING")
    ->execute([$client_id]);

// 4) Verifica se o admin já entrou neste chat
$check = $pdo->prepare("SELECT entrou_admin FROM chat_status WHERE client_id = ?");
$check->execute([$client_id]);
$status = $check->fetch(PDO::FETCH_ASSOC);

// Marca que o admin entrou, se ainda não estava marcado
if (!$status || (int)$status['entrou_admin'] === 0) {
    $pdo->prepare("INSERT INTO chat_status (client_id, entrou_admin) VALUES (?, TRUE)
                   ON CONFLICT (client_id) DO UPDATE SET entrou_admin = TRUE")
        ->execute([$client_id]);
}

// Verifica se já foi enviada a mensagem "Atendente entrou na conversa"
$msgCheck = $pdo->prepare("SELECT COUNT(*) FROM messages 
                           WHERE sender_id = 0 AND receiver_id = ? 
                           AND message = 'Atendente entrou na conversa.'");
$msgCheck->execute([$client_id]);
$jaEnviou = $msgCheck->fetchColumn();

if ($jaEnviou == 0) {
    $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) 
                   VALUES (0, ?, 'Atendente entrou na conversa.')")
        ->execute([$client_id]);
}


// 5) Busca mensagens trocadas entre admin (id=0) e cliente
$stmt = $pdo->prepare("
  SELECT id, sender_id, receiver_id, message, created_at
  FROM messages
  WHERE (
    (sender_id = 0 AND receiver_id = :cid)
    OR
    (sender_id = :cid AND receiver_id = 0)
  ) AND id > :since_id
  ORDER BY created_at ASC
");
$stmt->execute([
  'cid' => $client_id,
  'since_id' => $since_id
]);

$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode($messages);
