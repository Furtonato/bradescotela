<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['client_id'])) {
    echo json_encode(['success' => false, 'message' => 'Não autenticado.']);
    exit;
}

require __DIR__ . '/../includes/db.php';

$clientId = $_SESSION['client_id'];

$stmt = $pdo->prepare("
    SELECT 
        nome,
        agencia,
        conta,
        digito,
        status,
        referencia_dispositivo,
        saldo
    FROM clients
    WHERE id = ?
    LIMIT 1
");
$stmt->execute([$clientId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Cliente não encontrado.']);
    exit;
}
// -------- Função segura para formatar saldo --------
function formatarSaldoBR(string $valor): string {
    $valor = trim($valor);
    if ($valor === '' || !preg_match('/^\d+(\.\d+)?$/', $valor)) {
        return 'R$ 0,00';
    }
    if (strpos($valor, '.') !== false) {
        [$inteira, $decimal] = explode('.', $valor, 2);
    } else {
        $inteira = $valor;
        $decimal = '00';
    }
    $decimal = substr(str_pad($decimal, 2, '0'), 0, 2);
    $inteira = ltrim($inteira, '0');
    if ($inteira === '') $inteira = '0';

    // insere separadores de milhar
    $inteiraFormatada = preg_replace('/\B(?=(\d{3})+(?!\d))/', '.', $inteira);

    return 'R$ ' . $inteiraFormatada . ',' . $decimal;
}


$saldoRaw = isset($row['saldo']) ? (string)$row['saldo'] : '0.00';
// Garante sempre padrão NNN...NN.NN
if (!preg_match('/^\d+\.\d{2}$/', $saldoRaw)) {
    // Normaliza se vier por acaso com 1 casa ou sem decimal
    if (strpos($saldoRaw, '.') === false) {
        $saldoRaw .= '.00';
    } else {
        [$i,$d] = explode('.', $saldoRaw, 2);
        $d = substr(str_pad($d, 2, '0'), 0, 2);
        $saldoRaw = $i . '.' . $d;
    }
}

echo json_encode([
    'success'                => true,
    'nome'                   => $row['nome'],
    'agencia'                => $row['agencia'],
    'conta'                  => $row['conta'],
    'digito'                 => $row['digito'],
    'status'                 => $row['status'],
    'saldo_raw'              => $saldoRaw,                     // Ex: "1234567.80"
    'saldo_formatado'        => formatarSaldoBR($saldoRaw),    // Ex: "R$ 1.234.567,80"
    'referencia_dispositivo' => $row['referencia_dispositivo'] ?? ''
], JSON_UNESCAPED_UNICODE);
