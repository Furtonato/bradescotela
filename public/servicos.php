<style>
    /* Container principal para esta seção */
    .servicos-content {
        width: 100%;
        max-width: 1200px; 
        margin: 60px auto 40px auto; 
        padding: 0 24px;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
    }

    /* Grid para as duas colunas: Serviços e Atalhos */
    .servicos-grid {
        display: grid;
        grid-template-columns: 2fr 1fr; 
        gap: 24px;
        align-items: start;
    }

    /* Títulos das colunas (Serviços e Atalhos) */
    .column-title {
        margin: 0 0 12px 0;
        padding: 0 5px; 
        font-size: 16px;
        font-weight: 600;
        color: #333;
    }
    
    /* --- SEÇÃO DE SERVIÇOS (ESQUERDA) --- */
    .servicos-column {
        display: flex;
        flex-direction: column;
        gap: 16px; 
    }
    .expansivel-item {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 10px rgba(0,0,0,0.05);
        border: 1px solid #f0f0f0;
        transition: box-shadow 0.2s ease;
    }
    .expansivel-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 15px 25px;
        cursor: pointer;
    }
    .expansivel-cabecalho {
        display: flex;
        align-items: center;
        gap: 15px;
    }
    .icone-expansivel {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background-color: #eef3ff;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .servico-icon {
        width: 40px;
        height: 40px;
    }
    .expansivel-header h3 {
        margin: 0;
        font-size: 15px;
        font-weight: 600;
        color: #333;
    }
    .expansivel-header .arrow-down {
        width: 12px;
        height: 12px;
        fill: #0073e6;
        transition: transform 0.3s ease;
    }
    .expansivel-item.open .arrow-down {
        transform: rotate(180deg);
    }
    .expansivel-conteudo {
        max-height: 0; 
        overflow: hidden;
        transition: max-height 0.4s ease-out;
    }
    .expansivel-item.open .expansivel-conteudo {
        max-height: 500px; /* Aumentado para comportar conteúdo empilhado */
    }
    .expansivel-conteudo-interno {
        padding: 24px 30px;
        border-top: 1px solid #f0f0f0;
    }
    .grid-interno {
        display: grid;
        grid-template-columns: 1.2fr 1fr;
        align-items: center;
        gap: 20px;
    }
    .info-panel { text-align: left; display: flex; flex-direction: column; height: 100%; }
    .info-panel p { color: #555; line-height: 1.6; }
    .info-panel p strong { font-weight: 700; color: #333; }

    .emprestimo-info .icone-valor { width: 48px; height: 48px; margin-bottom: 20px; }
    .emprestimo-info p { font-size: 16px; margin: 0 0 25px 0; }
    .emprestimo-info .btn-visualizar { background-color: #fff; border: 1px solid #d71e28; color: #d71e28; font-size: 14px; font-weight: 600; padding: 10px 30px; border-radius: 20px; cursor: pointer; margin-top: auto; align-self: flex-start; }
    .emprestimo-banner { height: 225px; width: 213px; background-size: cover; background-position: center; border-radius: 8px; }
    
    .cartoes-info h4 { font-size: 16px; font-weight: 600; color: #333; margin: 0 0 8px 0; }
    .cartoes-info p { font-size: 13px; margin: 0 0 20px 0; }
    .cartoes-info .btn-pedir-cartao { background-color: #d71e28; color: #fff; border: none; font-size: 13px; font-weight: 600; padding: 10px 24px; border-radius: 25px; cursor: pointer; margin-top: auto; align-self: flex-start; }
    .cartoes-banner img { flex: 0 0 209px; height: 221px; border-radius: 8px; filter: grayscale(100%); }

    .investimentos-content { display: flex; flex-direction: column; align-items: center; text-align: center; }
    .investimentos-logos { display: flex; align-items: center; justify-content: center; gap: 20px; margin-bottom: 25px; }
    .investimentos-logos .grafico-icon { width: 64px; height: 64px; }
    .investimentos-logos .plus-icon { font-size: 20px; color: #ccc; }
    .investimentos-logos .agora-logo { width: 169px; height: 69px; }
    .investimentos-content p { font-size: 14px; color: #666; line-height: 1.6; margin: 0 0 10px 0; }
    .investimentos-content p strong { color: #333; }
    .investimentos-content .btn-visualizar { background-color: #fff; border: 1px solid #d71e28; color: #d71e28; font-size: 14px; font-weight: 600; padding: 10px 30px; border-radius: 20px; cursor: pointer; margin-top: 25px; }

    /* --- SEÇÃO DE ATALHOS (DIREITA) --- */
    .atalhos-card { background-color: #fff; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); border: 1px solid #f0f0f0; overflow: hidden; height: 100%; box-sizing: border-box; }
    .atalhos-list { list-style: none; padding: 0; margin: 0; }
    .atalhos-list li { border-top: 1px solid #f0f0f0; }
    .atalhos-list li:first-child { border-top: none; }
    .atalhos-list li a { display: flex; justify-content: space-between; align-items: center; padding: 18px 25px; text-decoration: none; font-size: 13px; font-weight: 500; color: #666; transition: background-color 0.2s ease; }
    .atalhos-list li a:hover { background-color: #f8f9fa; }
    .atalhos-list .arrow-right { width: 10px; height: 10px; fill: #0073e6; }

    /* =========================================================
       RESPONSIVIDADE (MEDIA QUERIES)
       ========================================================= */

    /* Para tablets e telas menores (abaixo de 992px) */
    @media (max-width: 992px) {
        .servicos-content {
            margin: 40px auto; /* Reduz margem vertical */
            padding: 0 20px;   /* Reduz espaçamento lateral */
        }

        /* Principal: Transforma o layout de 2 colunas em 1 */
        .servicos-grid {
            grid-template-columns: 1fr;
            gap: 40px; /* Aumenta o espaçamento vertical entre as seções */
        }
    }

    /* Para celulares (abaixo de 768px) */
    @media (max-width: 768px) {
        .servicos-content {
            margin: 20px auto;
            padding: 0 15px;
        }

        /* Ajusta o espaçamento dos itens expansíveis */
        .expansivel-header {
            padding: 15px 20px;
        }
        .expansivel-header h3 {
            font-size: 14px;
        }
        .expansivel-conteudo-interno {
            padding: 20px;
        }

        /* Transforma os grids internos (dentro dos itens) em 1 coluna */
        .grid-interno {
            grid-template-columns: 1fr;
            gap: 25px;
        }

        /* Centraliza os banners e ajusta botões */
        .emprestimo-banner, .cartoes-banner {
            order: -1; /* Move o banner para cima do texto */
            margin: 0 auto;
            display: flex;
            justify-content: center;
        }
        .emprestimo-banner {
             height: 180px;
        }

        .info-panel {
            text-align: center; /* Centraliza o texto */
        }
        .info-panel .btn-visualizar,
        .info-panel .btn-pedir-cartao {
            align-self: center; /* Centraliza os botões */
        }
        
        .cartoes-banner img {
            width: 150px;
            height: auto;
        }

        /* Ajustes finos nos atalhos */
        .atalhos-list li a {
            padding: 15px 20px;
        }
    }
</style>

<div class="servicos-content">
    <div class="servicos-grid">

        <div>
            <h2 class="column-title">Serviços</h2>
            <div class="servicos-column">
                <div class="expansivel-item">
                    <div class="expansivel-header">
                        <div class="expansivel-cabecalho">
                            <div class="icone-expansivel"><img src="https://www.ib12.bradesco.com.br/ibpf/apps/home/icone-emprestimo.svg" alt="Ícone Empréstimos" class="servico-icon"></div>
                            <h3>Empréstimos</h3>
                        </div>
                        <svg class="arrow-down" viewBox="0 0 10 6"><path d="M5 6L0 1L1 0L5 4L9 0L10 1L5 6Z"></path></svg>
                    </div>
                    <div class="expansivel-conteudo">
                        <div class="expansivel-conteudo-interno grid-interno">
                            <div class="info-panel emprestimo-info">
                                <img src="https://www.ib12.bradesco.com.br/ibpf/apps/home/emprestimo-bloqueio.svg" alt="Ícone Valor" class="icone-valor">
                                <p>Confira suas soluções em crédito por aqui.<br><strong>Temos a opção ideal.</strong></p>
                                <button class="btn-visualizar">Visualizar</button>
                            </div>
                            <div class="emprestimo-banner" style="background-image: url('https://www.ib12.bradesco.com.br/ibpf/apps/home/emprestimo-bloqueio.png');"></div>
                        </div>
                    </div>
                </div>

                <div class="expansivel-item">
                    <div class="expansivel-header">
                        <div class="expansivel-cabecalho">
                            <div class="icone-expansivel"><img src="https://www.ib12.bradesco.com.br/ibpf/apps/home/icone-cartoes.svg" alt="Ícone Cartões" class="servico-icon"></div>
                            <h3>Cartões de crédito</h3>
                        </div>
                        <svg class="arrow-down" viewBox="0 0 10 6"><path d="M5 6L0 1L1 0L5 4L9 0L10 1L5 6Z"></path></svg>
                    </div>
                    <div class="expansivel-conteudo">
                        <div class="expansivel-conteudo-interno grid-interno">
                            <div class="info-panel cartoes-info">
                                <h4>Com o cartão de crédito Bradesco, você pode mais</h4>
                                <p>Tenha acesso a benefícios, programa de recompensas, segurança pra fazer compras com o cartão virtual e outras facilidades!</p>
                                <button class="btn-pedir-cartao">Pedir cartão</button>
                            </div>
                            <div class="cartoes-banner"><img src="https://www.ib12.bradesco.com.br/ibpf/apps/home/cartoes-cliente-sem-cartao.png" alt="Cartões Bradesco"></div>
                        </div>
                    </div>
                </div>

                <div class="expansivel-item">
                    <div class="expansivel-header">
                        <div class="expansivel-cabecalho">
                            <div class="icone-expansivel"><img src="https://www.ib12.bradesco.com.br/ibpf/apps/home/icone-investimentos.svg" alt="Ícone Investimentos" class="servico-icon"></div>
                            <h3>Meus investimentos</h3>
                        </div>
                        <svg class="arrow-down" viewBox="0 0 10 6"><path d="M5 6L0 1L1 0L5 4L9 0L10 1L5 6Z"></path></svg>
                    </div>
                    <div class="expansivel-conteudo">
                        <div class="expansivel-conteudo-interno">
                            <div class="investimentos-content">
                                <div class="investimentos-logos">
                                    <img src="https://www.ib12.bradesco.com.br/ibpf/apps/home/investimentos-bloqueio.svg" class="grafico-icon" alt="Ícone Gráfico">
                                    <span class="plus-icon">+</span>
                                    <img src="https://www.ib12.bradesco.com.br/ibpf/apps/home/investimentos-agora.png" class="agora-logo" alt="Logo Ágora">
                                </div>
                                <p>Veja aqui seus investimentos.</p>
                                <p>Quer investir? <strong>Temos a carteira ideal para você.</strong></p>
                                <button class="btn-visualizar">Visualizar</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div>
            <h2 class="column-title">Atalhos</h2>
            <div class="atalhos-card">
                <ul class="atalhos-list">
                    <li><a href="#"><span>Extrato (Últimos Lançamentos)</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                    <li><a href="#"><span>Lançamentos Futuros</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                    <li><a href="#"><span>Boleto de Cobrança</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                    <li><a href="#"><span>Água, Luz, Telefone e Gás</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                    <li><a href="#"><span>Gerenciar limites de transações</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                    <li><a href="#"><span>Para Contas Bradesco</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                    <li><a href="#"><span>Para Contas de Outros Bancos - TED</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                    <li><a href="#"><span>Recarregar Celular Pré-Pago</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                    <li><a href="#"><span>Comprovantes (2ª Via)</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                    <li><a href="#"><span>Agendamentos</span> <svg class="arrow-right" viewBox="0 0 6 10"><path d="M0 9L4 5L0 1L1 0L6 5L1 10L0 9Z"></path></svg></a></li>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const expansivelHeaders = document.querySelectorAll('.expansivel-header');

    expansivelHeaders.forEach(header => {
        header.addEventListener('click', function () {
            const item = this.closest('.expansivel-item');
            const content = item.querySelector('.expansivel-conteudo');

            if (content) {
                const isOpen = item.classList.contains('open');

                document.querySelectorAll('.expansivel-item.open').forEach(openItem => {
                    if (openItem !== item) {
                        openItem.classList.remove('open');
                    }
                });
                
                item.classList.toggle('open');
            }
        });
    });
});
</script>