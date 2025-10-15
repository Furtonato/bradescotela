<style>
    .main-footer {
        margin-top: 0px; 
        padding-top: 20px;
        font-family: 'Montserrat', sans-serif;  
        color: #fff;
        background-image: linear-gradient(70deg, #142463, #034694 100%);
    }
    .footer-container {
        width: 100%;
        max-width: 1200px; 
        margin: 0 auto;
        padding: 0 24px;
        box-sizing: border-box;
    }
    .footer-grid {
        display: grid;
        grid-template-columns: 2.5fr 1fr; 
        gap: 40px;
    }
    .footer-logo {
        background: url('https://www.ib13.bradesco.com.br/ibpf/imagens/novologin/header-logo/bradesco-prime.png') no-repeat left center;
        background-size: contain;
        height: 40px;
        margin-bottom: 30px;
    }
    .fone-facil-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0;
    }
    .fone-facil-grid > div:not(:first-child) {
        border-left: 1px solid rgba(255, 255, 255, 0.2);
        padding-left: 30px;
    }
    .fone-facil h2 { grid-column: 1 / -1; margin: 0 0 20px 0; font-size: 16px; font-weight: 700; }
    .fone-facil h3 { font-size: 13px; margin: 0 0 8px 0; font-weight: 500; opacity: 0.9; }
    .fone-facil h4 { font-size: 16px; margin: 0 0 12px 0; font-weight: 700; }
    .fone-facil p { font-size: 12px; line-height: 1.6; opacity: 0.8; margin: 0; }
    .whatsapp-bia { grid-column: 1 / -1; display: flex; align-items: center; margin-top: 30px; font-size: 13px; }
    .whatsapp-bia .qrcode { width: 48px; height: 48px; margin-right: 15px; background-color: #fff; padding: 4px; box-sizing: border-box; border-radius: 4px; }
    .whatsapp-bia a { color: #fff; margin-left: auto; }
    .seguranca-card { background-color: #fff; color: #333; border-radius: 8px; padding: 24px; box-sizing: border-box; max-width: 350px; margin-top: 40px; }
    .seguranca-card h2 { margin: 0 0 10px 0; font-size: 16px; font-weight: 700; }
    .seguranca-card p { font-size: 14px; line-height: 1.6; color: #666; margin-bottom: 20px; }
    .seguranca-card a { color: #0073e6; font-weight: 600; text-decoration: none; font-size: 14px; }
    .footer-bottom {
        margin-top: 40px;
        padding: 18px 0;
        background-image: linear-gradient(90deg, #67686e 5%, #babbc4 99%);
    }
    .footer-bottom a { color: #fff; text-decoration: none; font-weight: 600; font-size: 14px; display: flex; align-items: center; gap: 10px; cursor: pointer; }
    .footer-bottom svg { width: 12px; height: 12px; fill: #fff; transition: transform 0.4s ease; }
    .footer-bottom a.open svg { transform: rotate(90deg); }
    .telefones {
        background-color: #e4e5e9;
        color: #333;
        padding: 0;
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.7s ease-in-out, padding 0.7s ease-in-out;
    }
    .telefones.open {
        padding: 40px 0;
        max-height: 4000px;
    }
    .telefones .grid-telefones {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 40px;
        padding-bottom: 30px;
        margin-bottom: 30px;
        border-bottom: 1px solid #ccc;
    }
    .telefones .grid-telefones:last-child { border-bottom: none; margin-bottom: 0; padding-bottom: 0; }
    .telefones h3 { font-size: 18px; font-weight: 700; margin: 0 0 20px 0; grid-column: 1 / -1; }
    .telefones h4 { font-size: 14px; font-weight: 700; margin: 15px 0 5px 0; }
    .telefones p { font-size: 13px; margin: 0 0 5px 0; line-height: 1.6; color: #555; }
    .telefones .tel { font-size: 16px; font-weight: 700; margin-bottom: 10px; color: #333; }
    .telefones a { color: #0073e6; text-decoration: none; font-weight: 600; }
    .telefones b { font-weight: 700; }

    /* =========================================================
       RESPONSIVIDADE (MEDIA QUERIES)
       ========================================================= */

    /* Para tablets e telas menores (abaixo de 992px) */
    @media (max-width: 992px) {
        .main-footer {
            margin-top: 0px; /* Reduz a margem superior */
            padding-top: 40px;
        }

        /* Principal: Transforma o layout de 2 colunas em 1 */
        .footer-grid {
            grid-template-columns: 1fr;
            gap: 30px;
        }

        /* Ajusta o card de segurança para ocupar a largura total */
        .seguranca-card {
            max-width: none;
            margin-top: 0; /* Remove a margem extra */
        }

        /* Telefones: Transforma o grid de 2 colunas em 1 */
        .telefones .grid-telefones {
            grid-template-columns: 1fr;
            gap: 30px;
        }
    }

    /* Para celulares (abaixo de 768px) */
    @media (max-width: 768px) {
        .footer-container {
            padding: 0 15px; /* Reduz o espaçamento lateral */
        }

        /* Fone Fácil: Transforma o grid de 3 colunas em 1 */
        .fone-facil-grid {
            grid-template-columns: 1fr;
            gap: 25px; /* Adiciona um espaçamento vertical */
        }

        /* Remove as bordas laterais e o padding esquerdo */
        .fone-facil-grid > div:not(:first-child) {
            border-left: none;
            padding-left: 0;
            /* Adiciona uma borda superior para separar os itens */
            border-top: 1px solid rgba(255, 255, 255, 0.2);
            padding-top: 25px;
        }
        
        /* Ajusta o layout do WhatsApp BIA para empilhar */
        .whatsapp-bia {
            flex-direction: column;
            align-items: flex-start;
            gap: 15px;
        }
        .whatsapp-bia a {
            margin-left: 0; /* Remove o alinhamento à direita */
        }

        /* Ajusta o padding do card de segurança */
        .seguranca-card {
            padding: 20px;
        }
    }
</style>

<footer class="main-footer">
    <div class="footer-container">
        <div class="footer-grid">
            <div class="footer-info">
                <div class="footer-logo"></div>
                <div class="fone-facil">
                    <h2>Fone Fácil</h2>
                    <div class="fone-facil-grid">
                        <div>
                            <h3>Capitais e regiões<br>metropolitanas</h3>
                            <h4>4002 0022</h4>
                            <p>Consulta de saldo, extrato,<br>transações financeiras e<br>cartão de crédito.</p>
                        </div>
                        <div>
                            <h3>Demais regiões</h3>
                            <h4>0800 570 0022</h4>
                            <p>SAC - deficiência<br>Auditiva ou de Fala</p>
                            <h4 style="font-size: 16px; margin-top: 10px;">0800 722 0099</h4>
                        </div>
                        <div>
                            <h3>SAC - Alô Bradesco</h3>
                            <h4>0800 704 8383</h4>
                            <p>Cancelamento,<br>reclamação, informação,<br>sugestão e elogio.</p>
                        </div>
                    </div>
                    <div class="whatsapp-bia">
                        <img src="https://www.ib12.bradesco.com.br/ibpf/brad-app-mfe/qr-code-whatsapp-bia.png" alt="QR Code para WhatsApp da BIA" class="qrcode">
                        <p>Se preferir, fale com a BIA pelo WhatsApp: <strong>11 3335 0237</strong></p>
                        <a href="#"><b>Aviso de Privacidade</b></a>
                    </div>
                </div>
            </div>
            <div class="footer-seguranca">
                <div class="seguranca-card">
                    <h2>Segurança</h2>
                    <p>O desenho do cadeado deve<br>aparecer na barra do seu<br>navegador.</p>
                    <a href="#">Ver mais</a>
                </div>
            </div>
        </div>
    </div>
    <div class="footer-bottom">
        <div class="footer-container">
            <a id="toggle-telefones">
                Ver outros telefones
                <svg viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg>
            </a>
        </div>
    </div>
    
    <div class="telefones" id="telefones-content">
        <div class="footer-container">
            <div>
                <h4><b>Fone Fácil Bradesco Private Bank</b></h4>
                <p>Capitais e regiões metropolitanas: <b class="tel">3003 9980</b></p>
                <p>Demais regiões: <b class="tel">0800 718 9980</b></p>
                <p>Atendimento de segunda a sexta-feira, das 7h às 21h e, aos sábados, das 9h às 15h.</p>
                <h4>Ouvidoria</h4>
                <p class="tel">0800 727 9933</p>
                <p>Atendimento de segunda a sexta-feira, das 9h às 18h, exceto feriados.<br> Para consultar mais telefones, acesse <a href="#"> Fale Conosco</a>.</p>
            </div>

            <div class="grid-telefones">
                <div>
                    <h3>Cartões</h3>
                    <p>Cancelamentos, reclamações e informações gerais</p>
                    <h4>SAC – cartões de crédito Elo, Visa e Mastercard</h4>
                    <p class="tel">0800 727 9988</p>
                    <h4>SAC – cartões de crédito e compras American Express</h4>
                    <p class="tel">0800 721 1188</p>
                </div>
                <div>
                    <h3>Previdência</h3>
                     <h4>SAC - Bradesco Vida e Previdência</h4>
                     <p class="tel">0800 721 1144</p>
                     <h4>Deficiência auditiva ou de fala</h4>
                     <p class="tel"><b>0800 701 2778</b></p>
                </div>
            </div>
             <div class="grid-telefones">
                <div>
                    <h3>Seguros</h3>
                    <h4>SAC - Bradesco Seguros</h4>
                    <p class="tel">0800 727 9966</p>
                </div>
                <div>
                    <h3>Capitalização</h3>
                    <h4>SAC – capitalização</h4>
                    <p class="tel">0800 721 1155</p>
                </div>
            </div>
        </div>
    </div>
</footer>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggleButton = document.getElementById('toggle-telefones');
        const contentDiv = document.getElementById('telefones-content');

        if (toggleButton && contentDiv) {
            toggleButton.addEventListener('click', function(event) {
                event.preventDefault();
                contentDiv.classList.toggle('open');
                toggleButton.classList.toggle('open');
            });
        }
    });
</script>

<script>
    // --- SCRIPT GLOBAL DO TIMER DE SESSÃO ---
    document.addEventListener('DOMContentLoaded', () => {
        const minutesElement = document.getElementById('timer-minutes');
        const circleElement = document.getElementById('timer-circle');
        
        if (minutesElement && circleElement) {
            const totalDuration = 20 * 60; // 20 minutos em segundos
            const circumference = 2 * Math.PI * 20;
            circleElement.style.strokeDasharray = circumference;

            const storageKey = 'sessionEndTime';
            let endTime = sessionStorage.getItem(storageKey);

            // Se não houver um tempo final salvo, cria um novo
            if (!endTime) {
                endTime = Date.now() + (totalDuration * 1000);
                sessionStorage.setItem(storageKey, endTime);
            }

            const timerInterval = setInterval(() => {
                const now = Date.now();
                let timeRemaining = Math.round((endTime - now) / 1000);

                if (timeRemaining < 0) {
                    timeRemaining = 0;
                }

                // Atualiza a interface do timer
                const minutes = Math.floor(timeRemaining / 60);
                minutesElement.textContent = String(minutes).padStart(2, '0');
                const progress = timeRemaining / totalDuration;
                const offset = circumference * (1 - progress);
                circleElement.style.strokeDashoffset = offset;

                // Para o timer e redireciona para logout quando o tempo acabar
                if (timeRemaining <= 0) {
                    clearInterval(timerInterval);
                    sessionStorage.removeItem(storageKey); // Limpa o storage
                    window.location.href = '/logout.php'; // Redireciona para logout
                }
            }, 1000);
        }
    });
</script>
