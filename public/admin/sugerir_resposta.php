<?php
header('Content-Type: application/json');

$apiKey = 'AIzaSyAN1CDVYnDtZdZ7LJZkL9SuMOPSbKrtUQ4';

$requestBody = file_get_contents('php://input');
$data = json_decode($requestBody, true);

if (!isset($data['history']) || empty($data['history'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Histórico da conversa não fornecido.']);
    exit;
}

$chatHistory = "";
foreach ($data['history'] as $message) {
    $sender = $message['sender'] ?? 'Desconhecido';
    $text = $message['text'] ?? '';
    $chatHistory .= $sender . ": " . $text . "\n";
}

// Em sugerir_resposta.php

// ... (início do seu código)

// Cria o prompt que será enviado para a IA
$prompt = "Você é um assistente de atendimento bancário especialista em segurança e autoatendimento.
Sua principal diretriz é: **sempre que possível, induza o cliente a realizar ações e consultas dentro do ambiente seguro da sua conta (app ou site).**

Analise a conversa a seguir entre um cliente e um atendente e sugira a próxima resposta ideal para o ATENDENTE, seguindo estas regras:

1.  **Se a pergunta do cliente for sobre dados pessoais ou operações na conta** (saldo, extrato, faturas, resgate de pontos Livelo, acesso não autorizado, pagamentos, etc.), sua sugestão DEVE:
    a. Reconhecer o pedido do cliente.
    b. Explicar cordialmente que, para a segurança dele, essa informação/operação só pode ser realizada na área logada.
    c. Orientar o cliente a usar o aplicativo do banco ou o site.

2.  **Se a pergunta do cliente for sobre informações gerais** que não expõem dados pessoais (dúvidas sobre produtos, taxas de juros genéricas, horários de agências, etc.), responda diretamente à pergunta de forma prestativa, sem a necessidade de pedir o login.

3.  **Mantenha sempre um tom profissional, cordial e focado na solução.** Não inclua saudações se a conversa já começou.

### Histórico da Conversa:
" . $chatHistory . "";

// ... (resto do seu código, que faz a chamada cURL)
$apiData = [
    'contents' => [
        [
            'parts' => [
                ['text' => $prompt]
            ]
        ]
    ]
];

$url = 'https://generativelanguage.googleapis.com/v1beta/models/gemini-1.5-flash-latest:generateContent?key=' . $apiKey;

// --- INÍCIO DA LÓGICA DE RETRY ---
$maxRetries = 3; // Tentar até 3 vezes
$retryDelay = 1; // Esperar 1 segundo entre as tentativas
$response = null;
$httpcode = 0;

for ($attempt = 1; $attempt <= $maxRetries; $attempt++) {
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($apiData));
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    $response = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // Se a requisição for bem-sucedida (código 200), sai do loop
    if ($httpcode == 200) {
        break;
    }
    
    // Se for um erro 503 e ainda houver tentativas, espera e tenta de novo
    if ($httpcode == 503 && $attempt < $maxRetries) {
        sleep($retryDelay);
    }
}
// --- FIM DA LÓGICA DE RETRY ---


// Trata a resposta final da API
if ($httpcode != 200) {
    http_response_code(500);
    echo json_encode(['error' => 'Falha ao comunicar com a API de IA após várias tentativas.', 'details' => json_decode($response)]);
    exit;
}

$responseData = json_decode($response, true);

$suggestion = $responseData['candidates'][0]['content']['parts'][0]['text'] ?? null;

if ($suggestion) {
    echo json_encode(['suggestion' => trim($suggestion)]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Não foi possível extrair a sugestão da resposta da IA.']);
}