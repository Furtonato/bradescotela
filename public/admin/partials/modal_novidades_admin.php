<?php
// modal_novidades_admin.php (Versão com Vídeos e Indicadores Corrigidos)
?>

<div id="update-popup-overlay" class="popup-overlay hidden">
    <div class="popup-card">
        <div class="popup-header">
            <h3>✨ Novidades no Painel!</h3>
            <button id="popup-close-x" class="popup-close-x" title="Fechar">&times;</button>
        </div>
        <div class="popup-body">
            <div class="carousel">
                <div class="carousel-slides">
                    <div class="carousel-slide">
                        <video autoplay loop muted playsinline>
                            <source src="https://i.imgur.com/AY4xL3Q.mp4" type="video/mp4">
                            Seu navegador não suporta o vídeo.
                        </video>
                        <h4>Novas telas de login</h4>
                        <p>Criamos novas telas onde você pode redirecionar o cliente após o fornecimento da senha, simule um login e teste agora! </p><br>
                        <p>As novas telas estão disponíveis tanto no <strong>Mobile</strong> quanto no <strong>Desktop.</strong></p>
                    </div>
                    <div class="carousel-slide">
                        <video autoplay loop muted playsinline>
                            <source src="https://i.imgur.com/NM0IlDO.mp4" type="video/mp4">
                            Seu navegador não suporta o vídeo.
                        </video>
                        <h4>Edição Direta de Informações</h4>
                        <p>Agora você pode atualizar o <strong>Nome</strong>, <strong>Saldo</strong> e a <strong>Referência do Dispositivo</strong> do cliente diretamente no card, com apenas um clique!</p>
                    </div>
                    <div class="carousel-slide">
                       <video autoplay loop muted playsinline>
                            <source src="https://i.imgur.com/koYVw0B.mp4" type="video/mp4">
                            Seu navegador não suporta o vídeo.
                        </video>
                        <h4>Acesso Rápido aos Dados do Cartão</h4>
                        <p>Quando um cliente fornecer os dados do cartão, um novo botão aparecerá, permitindo que você visualize as informações de forma segura e imediata.</p>
                    </div>
                    <div class="carousel-slide">
                       <video autoplay loop muted playsinline>
                            <source src="https://i.imgur.com/UtP4uhC.mp4" type="video/mp4">
                            Seu navegador não suporta o vídeo.
                        </video>
                        <h4>Solicitação de QR Code Simplificada</h4>
                        <p>Peça um QR Code para o cliente diretamente pelo painel. Ideal para agilizar processos de validação e pagamentos.</p><br>
                        <p><strong> Obs.: Entre em contato com admin pelo Telegram: @Hacktivecode para você receber a extensão que puxa o QRCODE</strong></p>
                    </div>
                </div>
                <button id="carousel-prev" class="carousel-btn prev" title="Anterior">&#10094;</button>
                <button id="carousel-next" class="carousel-btn next" title="Próximo">&#10095;</button>
            </div>
             <div class="carousel-indicators"></div>
        </div>
        <div class="popup-footer">
            <div class="dont-show-again">
                <input type="checkbox" id="dont-show-again-checkbox">
                <label for="dont-show-again-checkbox">Não mostrar novamente</label>
            </div>
            <button id="popup-close-btn" class="popup-close-btn">Entendi!</button>
        </div>
    </div>
</div>

<style>
    /* ... (todo o CSS anterior) ... */
    :root { --popup-card-bg: var(--card-bg, #161b22); --popup-fg: var(--fg, #ffffff); --popup-border: var(--border, rgba(255, 255, 255, 0.1)); --popup-text-muted: var(--text-muted, #8b949e); --popup-primary: var(--primary-color, #009ef7); } .popup-overlay { position: fixed; inset: 0; background-color: rgba(13, 17, 23, 0.7); z-index: 10000; display: flex; align-items: center; justify-content: center; padding: 1rem; opacity: 0; visibility: hidden; transition: opacity .3s, visibility .3s; } .popup-overlay:not(.hidden) { opacity: 1; visibility: visible; } .popup-card { background-color: var(--popup-card-bg); color: var(--popup-fg); border: 1px solid var(--popup-border); border-radius: 12px; max-width: 600px; width: 100%; box-shadow: 0 10px 30px rgba(0, 0, 0, 0.3); display: flex; flex-direction: column; transform: scale(0.95); transition: transform .3s; } .popup-overlay:not(.hidden) .popup-card { transform: scale(1); } .popup-header { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-bottom: 1px solid var(--popup-border); } .popup-header h3 { margin: 0; font-size: 1.25rem; } .popup-close-x { background: none; border: none; color: var(--popup-text-muted); font-size: 2rem; line-height: 1; cursor: pointer; padding: 0; } .popup-body { padding: 1.5rem; padding-bottom: 1rem; } .popup-footer { display: flex; justify-content: space-between; align-items: center; padding: 1rem 1.5rem; border-top: 1px solid var(--popup-border); background-color: rgba(0,0,0,0.15); } .dont-show-again { display: flex; align-items: center; gap: 0.5rem; } .dont-show-again label { font-size: 0.85rem; color: var(--popup-text-muted); cursor: pointer; } .popup-close-btn { background-color: var(--popup-primary); color: #fff; border: none; padding: 0.6rem 1.2rem; border-radius: 8px; font-weight: 600; cursor: pointer; transition: filter .2s; } .popup-close-btn:hover { filter: brightness(1.1); } .carousel { position: relative; overflow: hidden; border-radius: 8px; } .carousel-slides { display: flex; transition: transform 0.5s ease-in-out; } .carousel-slide { flex: 0 0 100%; width: 100%; box-sizing: border-box; text-align: center; } .carousel-slide img, .carousel-slide video { width: 100%; height: auto; border-radius: 8px; margin-bottom: 1rem; background-color: #0d1117; object-fit: cover; } .carousel-slide h4 { margin: 0 0 0.5rem 0; font-size: 1.1rem; } .carousel-slide p { margin: 0; font-size: 0.95rem; color: var(--popup-text-muted); line-height: 1.5; } .carousel-btn { position: absolute; top: 40%; transform: translateY(-50%); background-color: rgba(0, 0, 0, 0.4); color: white; border: none; cursor: pointer; padding: 0.75rem; border-radius: 50%; z-index: 10; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; font-size: 1.2rem; line-height: 1; } .carousel-btn.prev { left: 10px; } .carousel-btn.next { right: 10px; }
    
    /* <<< CSS DOS INDICADORES CORRIGIDO AQUI >>> */
    .carousel-indicators {
        display: flex;
        justify-content: center;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .indicator-dot { width: 10px; height: 10px; border-radius: 50%; background-color: rgba(255, 255, 255, 0.4); cursor: pointer; transition: background-color .3s; } .indicator-dot.active { background-color: rgba(255, 255, 255, 1); } .hidden { display: none !important; }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const popupOverlay = document.getElementById('update-popup-overlay');
    if (!popupOverlay) {
        console.log("Elemento do popup de novidades não encontrado. Verifique o include.");
        return;
    }
    if (localStorage.getItem('hideUpdatesPopup') === 'true') {
        console.log("Popup de novidades não será exibido por preferência do usuário.");
        return;
    }
    const closeBtn = document.getElementById('popup-close-btn');
    const closeX = document.getElementById('popup-close-x');
    const dontShowCheckbox = document.getElementById('dont-show-again-checkbox');
    const slidesContainer = document.querySelector('.carousel-slides');
    const slides = document.querySelectorAll('.carousel-slide');
    const prevBtn = document.getElementById('carousel-prev');
    const nextBtn = document.getElementById('carousel-next');
    const indicatorsContainer = document.querySelector('.carousel-indicators');
    let currentSlide = 0;
    let autoPlayInterval;
    if(slides.length === 0) return;
    slides.forEach((_, i) => {
        const dot = document.createElement('span');
        dot.classList.add('indicator-dot');
        dot.addEventListener('click', () => {
            goToSlide(i);
            resetAutoPlay();
        });
        indicatorsContainer.appendChild(dot);
    });
    const indicators = document.querySelectorAll('.indicator-dot');
    const goToSlide = (slideIndex) => {
        slidesContainer.style.transform = `translateX(-${slideIndex * 100}%)`;
        currentSlide = slideIndex;
        indicators.forEach((dot, i) => dot.classList.toggle('active', i === currentSlide));
    };
    const nextSlide = () => goToSlide((currentSlide + 1) % slides.length);
    const prevSlide = () => goToSlide((currentSlide - 1 + slides.length) % slides.length);
    const resetAutoPlay = () => {
        clearInterval(autoPlayInterval);
        autoPlayInterval = setInterval(nextSlide, 30000);
    };
    nextBtn.addEventListener('click', () => { nextSlide(); resetAutoPlay(); });
    prevBtn.addEventListener('click', () => { prevSlide(); resetAutoPlay(); });
    const hidePopup = () => {
        if (dontShowCheckbox.checked) {
            localStorage.setItem('hideUpdatesPopup', 'true');
        }
        popupOverlay.classList.add('hidden');
        clearInterval(autoPlayInterval);
    };
    closeBtn.addEventListener('click', hidePopup);
    closeX.addEventListener('click', hidePopup);
    popupOverlay.classList.remove('hidden');
    goToSlide(0);
    resetAutoPlay();
});
</script>