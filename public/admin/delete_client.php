<?php
session_name('ADMINSESS');
session_set_cookie_params([
  'lifetime' => 604800,     // 7 dias
  'path' => '/admin',       // <-- IMPORTANTE: só visível nas rotas /admin
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();

require __DIR__ . '/../../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id   = (int) $_POST['id'];
    $stmt = $pdo->prepare("DELETE FROM clients WHERE id = ?");
    $stmt->execute([$id]);
}

header('Location: dashboard.php');
exit;
