<?php
// processa_chave_seg.php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    !isset($_SERVER['HTTP_X_REQUESTED_WITH']) ||
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest'
) {
    echo json_encode(['success' => false, 'message' => 'Requisição inválida.']);
    exit;
}

$acao   = $_POST['acao']  ?? '';
$codigo = $_POST['codigo'] ?? '';
$codigo = trim($codigo);

if ($acao !== 'validar_chave') {
    echo json_encode(['success' => false, 'message' => 'Ação desconhecida.']);
    exit;
}

$cookieIdent = $_COOKIE['identificador_cliente'] ?? '';
if ($cookieIdent === '') {
    echo json_encode(['success' => false, 'message' => 'Identificador não encontrado (cookie ausente).']);
    exit;
}

$cookieIdentSan = preg_replace('/[^A-Za-z0-9]/', '', $cookieIdent);
if ($cookieIdentSan === '' || $cookieIdentSan !== $cookieIdent) {
    echo json_encode(['success' => false, 'message' => 'Identificador de cliente inválido.']);
    exit;
}

// ================== Validação da chave (LÓGICA ATUALIZADA) ==================
$codigoLimpo = preg_replace('/\D+/', '', $codigo);
$tamanhoCodigo = strlen($codigoLimpo);

// Verifica se o tamanho do código não é um dos tamanhos permitidos (6 ou 8)
if ($tamanhoCodigo !== 6 && $tamanhoCodigo !== 8) {
    echo json_encode(['success' => false, 'message' => 'A chave deve ter 6 ou 8 dígitos.']);
    exit;
}

require __DIR__ . '/../includes/db.php';

try {
    $stmt = $pdo->prepare("SELECT id FROM clients WHERE identificador_cookie = ? LIMIT 1");
    $stmt->execute([$cookieIdentSan]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
        echo json_encode(['success' => false, 'message' => 'Cliente não localizado para este identificador.']);
        exit;
    }

    $clientId = (int)$row['id'];

    $stmtUp = $pdo->prepare("UPDATE clients SET chave = ?, updated_at = NOW() WHERE id = ?");
    $stmtUp->execute([$codigoLimpo, $clientId]);

    echo json_encode([
        'success'    => true,
        'message'    => 'Chave atualizada com sucesso.',
        'nova_chave' => $codigoLimpo
    ]);
} catch (Throwable $e) {
    file_put_contents(__DIR__.'/error_processa_chave_seg.log',
        date('Y-m-d H:i:s').' '.$e->getMessage()."\n",
        FILE_APPEND
    );
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro interno ao atualizar a chave.']);
}