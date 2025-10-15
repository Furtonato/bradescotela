<?php
// admin/chat_send.php
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
header('Content-Type: application/json');

if (empty($_SESSION['admin'])) {
    http_response_code(403);
    echo json_encode(['error'=>'admin não logado']);
    exit;
}

$cookie = $_POST['cookie'] ?? '';
$text   = trim($_POST['message'] ?? '');

if ($cookie === '' || $text === '') {
    echo json_encode(['error'=>'dados ausentes']);
    exit;
}

$stmt = $pdo->prepare("SELECT id FROM clients WHERE identificador_cookie = ?");
$stmt->execute([$cookie]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(['error'=>'cliente não encontrado']);
    exit;
}

// ✅ CERTO
$stmt = $pdo->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
$stmt->execute([0, $row['id'], $text]);
echo json_encode(['success'=>true]);
