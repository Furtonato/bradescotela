<?php
session_name('ADMINSESS');
session_set_cookie_params([
    'lifetime' => 604800,
    'secure' => true,
    'httponly' => true,
    'samesite' => 'Lax'
]);
session_start();
require __DIR__ . '/../../includes/db.php';

// Valida se a requisição é do tipo POST e se os dados mínimos foram enviados
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($_POST['id']) ||
    empty($_POST['status'])
) {
    http_response_code(400); // Bad Request
    echo "Erro: Parâmetros inválidos.";
    exit;
}

// --- LÓGICA PRINCIPAL ---

$id     = (int) $_POST['id'];
$status = trim($_POST['status']);

// ✅ NOVO: Captura o parâmetro 'tipo', se ele tiver sido enviado pelo front-end
$tipo = isset($_POST['tipo']) && !empty($_POST['tipo']) ? trim($_POST['tipo']) : null;

try {
    // ✅ NOVO: A query agora é dinâmica

    // Se o 'tipo' foi informado (na primeira liberação), atualiza status e tipo
    if ($tipo) {
        $sql = "UPDATE clients SET status = ?, tipo = ? WHERE id = ?";
        $params = [$status, $tipo, $id];
    } else {
        // Para as outras ações, atualiza apenas o status, como antes
        $sql = "UPDATE clients SET status = ? WHERE id = ?";
        $params = [$status, $id];
    }

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);

    // ✅ MELHORIA: Responde com sucesso para o JavaScript saber que deu certo
    http_response_code(200);
    exit;

} catch (PDOException $e) {
    // Em caso de erro no banco, responde com um erro de servidor
    http_response_code(500);
    // Para depuração, você pode ecoar o erro. Em produção, seria melhor registrar em um log.
    // echo "Erro de banco de dados: " . $e->getMessage();
    exit;
}