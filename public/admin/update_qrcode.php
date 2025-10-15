<?php
session_name('ADMINSESS');
session_start();
header('Content-Type: application/json; charset=utf-8');

if (!(
    ($_SESSION['admin_logged_in'] ?? false) ||
    ($_SESSION['admin'] ?? false)
)) {
    echo json_encode(['success'=>false,'message'=>'Não autorizado.']); exit;
}

require __DIR__ . '/../../includes/db.php';

$id = $_POST['id'] ?? '';
if ($id === '' || !ctype_digit($id)) {
    echo json_encode(['success'=>false,'message'=>'ID inválido.']); exit;
}

$imgBase64   = isset($_POST['img_base64'])   ? trim($_POST['img_base64'])   : null;
$payloadText = isset($_POST['payload_text']) ? trim($_POST['payload_text']) : null;

$sets = [];
$params = [':id'=>$id];

if ($imgBase64 !== null) {
    if ($imgBase64 === '') {
        $sets[] = "qrcode_img_base64 = NULL";
    } else {
        // Aceita com ou sem prefixo
        if (strpos($imgBase64, 'data:image') !== 0) {
            // se quiser forçar prefixo png:
            $imgBase64 = 'data:image/png;base64,' . $imgBase64;
        }
        // valida base64 “pura” (parte após a vírgula)
        $pure = $imgBase64;
        if (strpos($pure, ',') !== false) {
            $pure = substr($pure, strpos($pure, ',')+1);
        }
        if (!preg_match('/^[A-Za-z0-9+\/=\r\n]+$/', $pure)) {
            echo json_encode(['success'=>false,'message'=>'Imagem base64 inválida.']); exit;
        }
        $sets[] = "qrcode_img_base64 = :img";
        $params[':img'] = $imgBase64;
    }
}

if ($payloadText !== null) {
    $sets[] = "qrcode_payload = :pl";
    $params[':pl'] = ($payloadText === '' ? null : $payloadText);
}

if (!$sets) {
    echo json_encode(['success'=>false,'message'=>'Nada para atualizar.']); exit;
}

$sets[] = "qrcode_updated_at = NOW()";
$sql = "UPDATE clients SET ".implode(', ',$sets)." WHERE id = :id";
$stm = $pdo->prepare($sql);
$stm->execute($params);

echo json_encode(['success'=>true,'message'=>'QR Code atualizado.']);
