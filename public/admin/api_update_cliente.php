<?php
session_name('ADMINSESS');
session_start();

header('Content-Type: application/json');

try {
    // ---- Autorização ----
    if (
        !(
            ($_SESSION['admin_logged_in'] ?? false) ||
            ($_SESSION['admin'] ?? false)
        )
    ) {
        echo json_encode(['success'=>false,'message'=>'Acesso não autorizado.']);
        exit;
    }

    require __DIR__ . '/../../includes/db.php';

    $id    = $_POST['id']    ?? '';
    $nome  = isset($_POST['nome'])  ? trim($_POST['nome'])  : null;
    $saldo = isset($_POST['saldo']) ? trim($_POST['saldo']) : null;
    $refDispositivo = isset($_POST['referencia_dispositivo'])
        ? trim($_POST['referencia_dispositivo'])
        : null;

    if ($id === '' || !ctype_digit($id)) {
        echo json_encode(['success' => false, 'message' => 'ID inválido.']);
        exit;
    }

    if ($nome === null && $saldo === null && $refDispositivo === null) {
        echo json_encode(['success' => false, 'message' => 'Nenhum campo para atualizar.']);
        exit;
    }

    // -------- Normalização de saldo --------
    /**
     * Regras:
     *  - "1.115,75" -> 1115.75
     *  - "1115.75"  -> 1115.75
     *  - "111575"   -> 1115.75 (últimos 2 dígitos = centavos)
     */
    function normalizarSaldo($entrada) {
        if ($entrada === null) return null;
        $entrada = trim($entrada);
        if ($entrada === '') return null;

        if (strpos($entrada, ',') !== false) { // formato BR
            $semMilhar = str_replace('.', '', $entrada);
            $entrada = str_replace(',', '.', $semMilhar);
        } elseif (strpos($entrada, '.') === false) {
            if (ctype_digit($entrada)) {
                if (strlen($entrada) === 1)       $entrada = '00'.$entrada;
                elseif (strlen($entrada) === 2)   $entrada = '0'.$entrada;
                $int = substr($entrada, 0, -2);
                $dec = substr($entrada, -2);
                $int = ltrim($int, '0');
                if ($int === '') $int = '0';
                $entrada = $int . '.' . $dec;
            } else {
                return null;
            }
        }

        if (!preg_match('/^\d+(\.\d+)?$/', $entrada)) return null;

        $parts   = explode('.', $entrada, 2);
        $intPart = ltrim($parts[0], '0');
        if ($intPart === '') $intPart = '0';
        $decPart = $parts[1] ?? '00';
        $decPart = str_pad(substr($decPart, 0, 2), 2, '0', STR_PAD_RIGHT);

        return $intPart . '.' . $decPart;
    }

    function excedeLimiteSaldo(string $valor): bool {
        if (!preg_match('/^\d{1,}\.\d{2}$/', $valor)) return true;
        [$int] = explode('.', $valor, 2);
        if (strlen($int) > 9) return true;
        if (strlen($int) < 9) return false;
        return strcmp($int, '999999999') > 0;
    }

    // ----- Busca atual -----
    $stmtChk = $pdo->prepare("SELECT nome, saldo, referencia_dispositivo FROM clients WHERE id = ?");
    $stmtChk->execute([$id]);
    $orig = $stmtChk->fetch(PDO::FETCH_ASSOC);
    if (!$orig) {
        echo json_encode(['success' => false, 'message' => 'Cliente não encontrado.']);
        exit;
    }

    $novoNome  = $orig['nome'];
    $novoSaldo = $orig['saldo'];
    $novaRef   = $orig['referencia_dispositivo'];

    $camposSQL = [];
    $params    = [':id' => $id];

    // --- Nome ---
    if ($nome !== null) {
        if ($nome === '') {
            echo json_encode(['success' => false, 'message' => 'Nome não pode ser vazio.']);
            exit;
        }
        $camposSQL[]     = "nome = :nome";
        $params[':nome'] = $nome;
        $novoNome        = $nome;
    }

    // --- Saldo ---
    if ($saldo !== null) {
        $saldoNorm = normalizarSaldo($saldo);
        if ($saldoNorm === null) {
            echo json_encode(['success' => false, 'message' => 'Saldo inválido.']);
            exit;
        }
        if (excedeLimiteSaldo($saldoNorm)) {
            echo json_encode(['success' => false, 'message' => 'Saldo excede 999.999.999,99.']);
            exit;
        }
        $camposSQL[]      = "saldo = :saldo";
        $params[':saldo'] = $saldoNorm;
        $novoSaldo        = $saldoNorm;
    }

    // --- Referência de dispositivo ---
    if ($refDispositivo !== null) {
        // Sanitização básica:
        // Permitir letras, dígitos, hífen e underline. (Ajuste conforme necessidade)
        $refSan = preg_replace('/[^A-Za-z0-9\-_]/', '', $refDispositivo);
        // Limite de tamanho (ex: 40)
        $refSan = substr($refSan, 0, 40);

        if ($refSan === '') {
            echo json_encode(['success' => false, 'message' => 'Referência inválida.']);
            exit;
        }

        $camposSQL[]                    = "referencia_dispositivo = :ref";
        $params[':ref']                 = $refSan;
        $novaRef                        = $refSan;
    }

    if ($camposSQL) {
        $sql = "UPDATE clients SET ".implode(', ', $camposSQL).", updated_at = NOW() WHERE id = :id";
        $stmtUpd = $pdo->prepare($sql);
        $stmtUpd->execute($params);
    }

    echo json_encode([
        'success'                      => true,
        'message'                      => 'Atualização realizada com sucesso.',
        'id'                           => $id,
        'novo_nome'                    => $novoNome,
        'novo_saldo'                   => $novoSaldo,
        'nova_referencia_dispositivo'  => $novaRef
    ]);
    exit;

} catch (Throwable $e) {
    file_put_contents(__DIR__.'/error_api_update.log',
        date('Y-m-d H:i:s').' '.$e->getMessage()."\n".$e->getTraceAsString()."\n",
        FILE_APPEND
    );
    http_response_code(500);
    echo json_encode(['success'=>false,'message'=>'Erro interno.']);
    exit;
}
