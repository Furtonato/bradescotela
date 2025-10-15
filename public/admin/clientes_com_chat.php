<?php
require __DIR__ . '/../../includes/db.php';
session_name('ADMINSESS');
session_set_cookie_params([
  'lifetime' => 604800,     // 7 dias
  'path' => '/admin',       // <-- IMPORTANTE: só visível nas rotas /admin
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();
if (!($_SESSION['admin'] ?? false)) {
  echo json_encode([]);
  exit;
}

$stmt = $pdo->query("
  SELECT c.*
  FROM clients c
  INNER JOIN chat_status cs ON c.id = cs.client_id
  WHERE cs.entrou_admin = true
  ORDER BY c.created_at DESC
");

echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
