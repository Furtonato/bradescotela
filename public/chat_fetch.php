<?php
session_start();
require __DIR__ . '/../includes/db.php';
header('Content-Type: application/json');

$clientId = $_SESSION['client_id'] ?? null;

if (!$clientId && !empty($_COOKIE['identificador_cliente'])) {
    $stmt = $pdo->prepare("SELECT id FROM clients WHERE identificador_cookie=?");
    $stmt->execute([$_COOKIE['identificador_cliente']]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
        $clientId = $row['id'];
        $_SESSION['client_id'] = $clientId;

        // Insere status inicial
        $pdo->prepare("INSERT INTO chat_status (client_id) VALUES (?) ON CONFLICT DO NOTHING")->execute([$clientId]);
    }
}

if (!$clientId) {
    echo json_encode(['error'=>'cliente não identificado']);
    exit;
}

$since_id = intval($_GET['since_id'] ?? 0);

$stmt = $pdo->prepare("
    SELECT * FROM messages
    WHERE (
      (sender_id = :me AND receiver_id = 0)
      OR
      (sender_id = 0 AND receiver_id = :me)
    ) AND id > :since_id
    ORDER BY created_at ASC
");
$stmt->execute(['me'=>$clientId, 'since_id'=>$since_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
