<?php
// Defina $menuAtivo antes de incluir este arquivo.
  function ativo(string $slug): string {
      $current = basename($_SERVER['PHP_SELF'], '.php');
      return $current === $slug ? 'active' : '';
  }
?>
<style>
    /* ... (Estilos gerais do menu, sem alterações) ... */
    .top-menu-container { background-color: #fff; width: 100%; display: flex; justify-content: center; border-bottom: 1px solid #e0e0e0; box-sizing: border-box; }
    .top-menu-nav { width: 100%; max-width: 1600px; display: flex; justify-content: center; align-items: center; height: 60px; padding: 0 24px; position: relative; }
    .top-menu-nav ul { list-style: none; padding: 0; margin: 0; display: flex; align-items: center; gap: 24px; }
    .top-menu-nav .menu-item { font-family: 'Montserrat', sans-serif; font-size: 14px; font-weight: 500; color: #555; text-decoration: none; background-color: transparent; border: none; cursor: pointer; transition: color 0.2s ease; white-space: nowrap; display: flex; align-items: center; }
    .top-menu-nav .menu-item:hover { color: #000; }
    .top-menu-nav .menu-item.active { color: #000; font-weight: 700; }
    .top-menu-nav .btn-ir { background: linear-gradient(70deg, #142463, #034694 100%); color: #fff; font-weight: 700; border-radius: 20px; padding: 10px 20px; margin: 0 12px; }
    .top-menu-nav .btn-ir:hover { background-color: #b21921; color: #fff; }
    .dropdown-container { position: relative; }
    .dropdown-container .menu-item svg { margin-left: 8px; fill: #555; width: 12px; height: 12px; transition: transform 0.3s ease, fill 0.2s ease; }
    .dropdown-container .menu-item:hover svg { fill: #000; }
    .dropdown-container.active .menu-item svg { transform: rotate(180deg); }
    #dropdown-menu { display: block; visibility: hidden; opacity: 0; transition: opacity 0.2s ease, transform 0.2s ease, visibility 0.2s ease; position: absolute; top: 50px; right: 0; background-color: #fff; border-radius: 8px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); padding: 8px 0; min-width: 280px; list-style: none; border: 1px solid #f0f0f0; z-index: 1000; transform: translateY(10px); }
    #dropdown-menu.show { visibility: visible; opacity: 1; transform: translateY(0); }
    #dropdown-menu li a { display: block; padding: 10px 25px; font-size: 14px; color: #666; text-decoration: none; font-weight: 500; }
    #dropdown-menu li a:hover { background-color: #f5f5f5; color: #000; }
    #dropdown-menu .separator { display: block; height: 1px; background-color: #eeeeee; margin: 8px 25px; }
    
    body.menu-is-open { overflow: hidden; }

    .hamburger-btn { display: none; flex-direction: column; justify-content: space-around; width: 30px; height: 24px; background: transparent; border: none; cursor: pointer; padding: 0; z-index: 1001; }
    .hamburger-btn span { width: 100%; height: 3px; background-color: #333; border-radius: 3px; transition: all 0.3s ease-in-out; }
    .hamburger-btn.open span:nth-child(1) { transform: rotate(45deg) translate(5px, 5px); }
    .hamburger-btn.open span:nth-child(2) { opacity: 0; }
    .hamburger-btn.open span:nth-child(3) { transform: rotate(-45deg) translate(7px, -7px); }

    @media (max-width: 992px) {
        /* <<< A CORREÇÃO ESTÁ AQUI >>> */
        .top-menu-nav {
            justify-content: space-between;
            flex-wrap: wrap;
            /* Remove a altura fixa e permite que o container cresça */
            height: auto;
            min-height: 60px; /* Garante a altura mínima da barra fechada */
        }
        
        .hamburger-btn { display: flex; }

        .top-menu-nav ul#main-menu-list {
            flex-basis: 100%;
            order: 3;
            background-color: #fff;
            max-height: 0;
            overflow-y: auto;
            transition: max-height 0.4s ease-in-out, padding 0.4s ease-in-out, border 0.4s ease-in-out;
            flex-direction: column;
            align-items: flex-start;
            gap: 0;
            padding: 0;
            border-top: 1px solid transparent; /* Borda para transição suave */
        }
        .top-menu-nav.menu-open ul#main-menu-list {
            max-height: calc(100vh - 60px); 
            padding: 10px 0;
            border-top-color: #e0e0e0; /* Mostra a borda quando aberto */
        }
        .top-menu-nav ul li { width: 100%; }
        .top-menu-nav .menu-item { padding: 15px 24px; width: 100%; justify-content: flex-start; box-sizing: border-box; }
        .top-menu-nav .btn-ir { margin: 10px 24px; width: calc(100% - 48px); justify-content: center; }
        .dropdown-container { width: 100%; }
        .dropdown-container.active #dropdown-menu { visibility: visible; opacity: 1; transform: translateY(0); max-height: 400px; }
        #dropdown-menu { position: static; box-shadow: none; border: none; border-top: 1px solid #f0f0f0; width: 100%; min-width: 0; padding: 0; max-height: 0; overflow: hidden; transition: max-height 0.3s ease; transform: none; }
        #dropdown-menu li a { padding-left: 40px; }
    }
</style>

<div class="top-menu-container">
    <nav class="top-menu-nav">
        <button id="hamburger-btn" class="hamburger-btn" aria-label="Abrir menu" aria-expanded="false">
            <span></span><span></span><span></span>
        </button>

        <ul id="main-menu-list">    
            <li><a href="/primelogado.php"class="menu-item <?= ativo('primelogado')?>">Início</a></li>
            <li><a href="/saldosprime.php"class="menu-item <?= ativo('saldosprime')?>">Saldos e Extratos</a></li>
            <li><a href="#"class="menu-item <?= ativo('pagamentos')?>">Pagamentos</a></li>
            <li><a href="/pixprime.php"          class="menu-item <?= ativo('pixprime')          ?>">PIX</a></li>
            <li><a href="/transferenciasprime.php" class="menu-item <?= ativo('transferenciasprime') ?>">Transferências</a></li>
            <li><a href="/cartoesprime.php"      class="menu-item <?= ativo('cartoesprime')      ?>">Cartões</a></li>
            <li><a href="#" class="menu-item <?=ativo('emprestimos',$menuAtivo)?>">Empréstimos</a></li>
            <li><a href="#" class="menu-item <?=ativo('agora',$menuAtivo)?>">Ágora/Home Broker</a></li>
            <li><a href="#" class="menu-item <?=ativo('investimentos',$menuAtivo)?>">Investimentos</a></li>
            <li><a href="#" class="menu-item <?=ativo('openfinance',$menuAtivo)?>">Open Finance</a></li>
            <li><button type="button" class="menu-item btn-ir">Imposto de Renda</button></li>
            
            <li class="dropdown-container" id="dropdown-container">
                <button type="button" class="menu-item" id="dropdown-toggle" aria-haspopup="true" aria-expanded="false">
                    Mais opções
                    <svg viewBox="0 0 10 6" width="12" height="12"><path d="M5 6L0 1L1 0L5 4L9 0L10 1L5 6Z" fill="currentColor"></path></svg>
                </button>
                
                <ul id="dropdown-menu" aria-labelledby="dropdown-toggle">
                    <li><a href="#">Imposto de Renda</a></li>
                    <li><a href="#">Câmbio</a></li>
                    <li><a href="#">Capitalização</a></li>
                    <li><a href="#">Celulares</a></li>
                    <li><a href="#">Consórcios</a></li>
                    <li><a href="#">Outros Serviços</a></li>
                    <li><a href="#">Personalização e Segurança</a></li>
                    <li><a href="#">Previdência</a></li>
                    <li><a href="#">Seguros</a></li>
                    <li class="separator"></li> 
                    <li><a href="#">2ª Via comprovantes</a></li>
                    <li><a href="#">Agendamentos</a></li>
                </ul>
            </li>
        </ul>
    </nav>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const hamburgerBtn = document.getElementById('hamburger-btn');
    const mainMenuNav = document.querySelector('.top-menu-nav');
    const dropdownToggle = document.getElementById('dropdown-toggle');
    const dropdownMenu = document.getElementById('dropdown-menu');
    const dropdownContainer = document.getElementById('dropdown-container');

    if (hamburgerBtn && mainMenuNav) {
        hamburgerBtn.addEventListener('click', () => {
            const isMenuOpen = mainMenuNav.classList.toggle('menu-open');
            hamburgerBtn.classList.toggle('open');
            hamburgerBtn.setAttribute('aria-expanded', isMenuOpen);
            document.body.classList.toggle('menu-is-open', isMenuOpen);
        });
    }

    if (dropdownToggle && dropdownMenu && dropdownContainer) {
        dropdownToggle.addEventListener('click', function(event) {
            event.preventDefault();
            const isExpanded = dropdownContainer.classList.toggle('active');
            dropdownMenu.classList.toggle('show');
            dropdownToggle.setAttribute('aria-expanded', isExpanded);
        });
        document.addEventListener('click', function(event) {
            if (!dropdownContainer.contains(event.target)) {
                dropdownMenu.classList.remove('show');
                dropdownContainer.classList.remove('active');
                dropdownToggle.setAttribute('aria-expanded', 'false');
            }
        });
    }
});
</script>