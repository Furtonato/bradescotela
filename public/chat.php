<?php

// Verifica se a sessão JÁ ESTÁ ATIVA
if (session_status() !== PHP_SESSION_ACTIVE) {

    // --- Configuração da Sessão (DEVE vir antes de session_start()) ---
    session_name('CLIENTSESS');
    session_set_cookie_params([
        'lifetime' => 86400 * 30,
        'path'     => '/',
        'secure'   => true, // Use TRUE se o seu site estiver rodando em HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);

    // --- Inicia a sessão ---
    session_start();
}
// Se a sessão JÁ ESTIVER ativa, o bloco acima é ignorado,
// e o Notice/Warnings são suprimidos.


// --- Lógica de Protocolo e Cookie (pode vir DEPOIS) ---

// Gera protocolo de chat se ainda não existir
if (!isset($_SESSION['chat_protocol'])) {
    $_SESSION['chat_protocol'] = strtoupper(bin2hex(random_bytes(4)));
}

// Define cookie identificador_cliente
// NOTA: setcookie deve ser chamado ANTES de qualquer saída (HTML), mas pode vir aqui
// após session_start() se não houver saída anterior.
if (!isset($_COOKIE['identificador_cliente'])) {
    setcookie('identificador_cliente', $_SESSION['chat_protocol'], time() + 86400 * 30, '/');
    $_COOKIE['identificador_cliente'] = $_SESSION['chat_protocol'];
}
?>