<?php
// delete_all_clients.php
session_name('ADMINSESS');
session_start();
require __DIR__ . '/../../includes/db.php';

// Proteção: Apenas administradores podem executar esta ação
if (!isset($_SESSION['admin'])) {
    http_response_code(403); // Forbidden
    echo "Acesso não autorizado.";
    exit;
}

// Ação de exclusão somente via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // TRUNCATE é mais eficiente que DELETE para limpar a tabela inteira.
        // RESTART IDENTITY reinicia a contagem de IDs (o próximo cliente será o #1).
        $pdo->exec("TRUNCATE TABLE clients RESTART IDENTITY");
        
        // Redireciona de volta para o painel após a exclusão bem-sucedida
        header('Location: dashboard.php?status=deleted');
        exit;
    } catch (PDOException $e) {
        // Em caso de erro, exibe uma mensagem
        die("Erro ao excluir os clientes: " . $e->getMessage());
    }
} else {
    // Se alguém tentar acessar este arquivo diretamente (via GET), redireciona
    header('Location: dashboard.php');
    exit;
}
?>