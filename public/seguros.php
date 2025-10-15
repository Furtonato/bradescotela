<style>
    /* Container principal para a seção de seguros */
    .seguros-content {
        width: 100%;
        max-width: 1200px; 
        margin: 40px auto; 
        padding: 0 24px;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
    }

    /* Grid para as três colunas */
    .seguros-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 24px;
        align-items: stretch; 
    }

    /* Títulos das colunas (Seguros e Avisos) */
    .column-title {
        margin: 0 0 12px 0;
        padding: 0 5px; 
        font-size: 20px;
        font-weight: 600;
        color: #333;
    }

    /* Estilo genérico para os cards desta seção */
    .seguros-card {
        background-color: #fff;
        border-radius: 8px;
        padding: 30px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.08);
        border: 1px solid #f0f0f0;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
        height: 100%; 
    }

    /* Card da Esquerda (Logo) */
    .seguros-card.logo-card {
        justify-content: center;
        align-items: center;
    }
    .seguros-card.logo-card img {
        width: 300px; /* Ícone maior */
    }

    /* Card do Meio (Conteúdo Seguros) */
    .seguros-card.seguros-info {
        text-align: center;
        align-items: center;
    }
    .seguros-card .seguro-icon {
        width: 70px; /* Ícone maior */
        height: 70px;
        margin-bottom: 20px;
    }
    .seguros-card p {
        font-size: 14px;
        color: #666;
        line-height: 1.6;
        margin: 0 0 15px 0;
    }
    .seguros-card p strong {
        color: #333;
    }
    .seguros-card .btn-visualizar {
        background-color: #fff;
        border: thin solid #ff003e; /* Cores aplicadas */
        color: #ff003e;
        width: 100%; /* Botão maior */
        max-width: 240px; /* Largura máxima */
        font-weight: 700;
        padding: 12px 30px; /* Padding ajustado */
        border-radius: 25px;
        cursor: pointer;
        transition: all 0.2s ease;
        margin-top: auto; 
    }
    .seguros-card .btn-visualizar:hover {
        background-color: #ff003e;
        color: #fff;
    }

    /* Card da Direita (Avisos) */
    .avisos-card h3 {
        margin: 0 0 10px 0;
        font-size: 16px;
        font-weight: 700;
        color: #333;
    }

    /* Media query para responsividade */
    @media (max-width: 992px) {
        .seguros-grid {
            grid-template-columns: 1fr; 
        }
    }

</style>

<div class="seguros-content">
    <div class="seguros-grid">

        <div>
            <h2 class="column-title">&nbsp;</h2>
            <div class="seguros-card logo-card">
                <img src="https://www.ib12.bradesco.com.br/ibpf/apps/home/brad-fundo.svg" alt="Fundo Bradesco">
            </div>
        </div>

        <div>
            <h2 class="column-title">Seguros</h2>
            <div class="seguros-card seguros-info">
                <img class="seguro-icon" src="https://www.ib12.bradesco.com.br/ibpf/apps/home/meus-seguro-bloqueio.svg" alt="Ícone Seguros">
                <p>Acesse todos os seus bens segurados.</p>
                <p><strong>Ainda não tem?</strong><br>Faça um seguro pra proteger você e sua família.</p>
                <button class="btn-visualizar">Visualizar</button>
            </div>
        </div>

        <div>
            <h2 class="column-title">Avisos</h2>
            <div class="seguros-card avisos-card">
                <h3>Fique ligado!</h3>
                <p>No momento você não tem nenhum aviso, mas o Bradesco deixa você sempre atualizado e auxilia a organizar sua conta e seu dia-a-dia.</p>
            </div>
        </div>

    </div>
</div>