<?php
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['client_id'])) {
    echo json_encode(['success'=>false,'message'=>'Não autenticado.']); exit;
}

require __DIR__ . '/../includes/db.php';

$stmt = $pdo->prepare("SELECT qrcode_img_base64, qrcode_updated_at FROM clients WHERE id = ? LIMIT 1");
$stmt->execute([$_SESSION['client_id']]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$row) {
    echo json_encode(['success'=>false,'message'=>'Cliente não encontrado.']); exit;
}

$img = $row['qrcode_img_base64'] ?? '';
if ($img && strpos($img,'data:image') !== 0) {
    $img = 'data:image/png;base64,' . $img;
}

echo json_encode([
    'success' => true,
    'qrcode_img' => $img,            // string ou vazio
    'updated_at' => $row['qrcode_updated_at']
]);
