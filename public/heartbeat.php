<?php
// MANTENHA O CAMINHO CORRETO PARA SEU ARQUIVO db.php
require __DIR__ . '/../includes/db.php'; 

// Verifica se o identificador do cookie foi enviado via POST
if (isset($_POST['identificador_cookie'])) {
    
    $cookie = $_POST['identificador_cookie'];
    
    // CORREÇÃO: Usamos gmdate() para obter a hora UTC, sem definir um fuso horário local.
    $now = gmdate("Y-m-d H:i:s");

    // Prepara a query para atualizar o timestamp de atividade
    // A query continua a mesma e o nome da tabela "clients" está correto.
    $stmt = $pdo->prepare(
        "UPDATE clients SET last_active_at = ? WHERE identificador_cookie = ?"
    );
    
    // Executa a query
    if ($stmt->execute([$now, $cookie])) {
        // Responde com sucesso
        header('Content-Type: application/json');
        echo json_encode(['status' => 'success']);
    } else {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status.']);
    }
    
} else {
    // Se nenhum cookie foi enviado
    header('Content-Type: application/json');
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Cookie identifier not provided.']);
}