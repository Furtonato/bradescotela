<?php
// File: public/atualizar_estagio.php

header('Content-Type: application/json');

// Requer a conexão com o banco
require __DIR__.'/../includes/db.php';

// 1. Garante que o cookie de identificação do cliente existe.
// Esta é a parte que identifica o usuário.
if (!isset($_COOKIE['identificador_cliente']) || empty($_COOKIE['identificador_cliente'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Identificador de cliente ausente ou inválido.']);
    exit;
}
$identificadorCookie = $_COOKIE['identificador_cliente'];

// 2. Lê o estágio enviado pelo JavaScript
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);
$estagio = trim($data['estagio'] ?? '');

if (empty($estagio)) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Nome do estágio não foi informado.']);
    exit;
}

// 3. Atualiza o banco de dados com o novo estágio e o horário atual
try {
    $sql = "UPDATE clients SET status_estagio = :estagio, last_page_at = NOW() WHERE identificador_cookie = :identificador_cookie";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':estagio'              => $estagio,
        ':identificador_cookie' => $identificadorCookie
    ]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(['success' => true]);
    } else {
        http_response_code(404); // Not Found
        echo json_encode(['success' => false, 'message' => 'Cliente não encontrado com o identificador fornecido.']);
    }

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o estágio.']);
    exit;
}
?>