<style>
    /* Estilos para o container principal do corpo */
    .main-content {
        width: 100%;
        max-width: 1200px; 
        margin: 40px auto; 
        padding: 0 24px;
        box-sizing: border-box;
        font-family: 'Montserrat', sans-serif;
    }

    /* Estilos para a parte de cima (Ocultar valores) */
    .content-header {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        margin-bottom: 20px;
        font-size: 14px;
        font-weight: 500;
        color: #333;
    }

    .toggle-switch {
        position: relative;
        display: inline-block;
        width: 40px;
        height: 22px;
        margin-left: 10px;
    }
    .toggle-switch input { opacity: 0; width: 0; height: 0; }
    .slider {
        position: absolute;
        cursor: pointer;
        top: 0; left: 0; right: 0; bottom: 0;
        background-color: #ccc;
        transition: .4s;
        border-radius: 22px;
    }
    .slider:before {
        position: absolute;
        content: "";
        height: 16px;
        width: 16px;
        left: 3px;
        bottom: 3px;
        background-color: white;
        transition: .4s;
        border-radius: 50%;
    }
    input:checked + .slider { background-color: #0073e6; }
    input:checked + .slider:before { transform: translateX(18px); }
    
    /* Grid principal com as duas colunas */
    .content-grid {
        display: grid;
        grid-template-columns: 320px 1fr; 
        gap: 24px;
        align-items: flex-start; /* Alterado para flex-start para melhor alinhamento vertical */
    }

    /* Títulos que ficam FORA dos cards */
    .column-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 12px;
        padding: 0 5px; 
    }
    .column-header h2 {
        margin: 0;
        font-size: 20px;
        font-weight: 700;
        color: #222;
    }
    .column-header a {
        font-size: 13px;
        color: #0073e6;
        text-decoration: none;
        font-weight: 600;
    }

    /* Estilo genérico para os cards brancos */
    .card {
        background-color: #fff;
        border-radius: 8px;
        padding: 20px 25px;
        box-shadow: 0 4px 6px rgba(0,0,0,0.04), 0 1px 3px rgba(0,0,0,0.08); 
        height: 100%;
        box-sizing: border-box;
        display: flex;
        flex-direction: column;
    }
    .card h3 {
        font-size: 16px;
        font-weight: 700;
        color: #333;
        margin: 0 0 20px 0;
    }
    
    /* Card "Meus Saldos" (Esquerda) */
    .saldos-list {
        list-style: none;
        padding: 0;
        margin: 0;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }
    .saldos-list li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 0;
        font-size: 14px;
        color: #666; 
        border-top: 1px solid #f0f0f0;
    }
    .saldos-list li:first-child {
        padding-top: 0;
        border-top: none;
    }
    .saldos-list li.total {
        margin-top: auto;
        padding-bottom: 0;
    }
    .saldos-list li .value {
        font-weight: 600;
        color: #333;
    }

    /* Card "Resumo" (Direita) */
    .resumo-grid {
        display: grid;
        grid-template-columns: 0.8fr 1.2fr;
        gap: 30px;
        flex-grow: 1;
    }
    .resumo-30dias { display: flex; flex-direction: column; }
    .resumo-30dias ul { list-style: none; padding: 0; margin: 0; font-size: 14px; }
    .resumo-30dias li {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 5px;
        color: #666;
    }
    .summary-label { display: flex; flex-direction: column; align-items: flex-start; }
    .summary-label .indicator { margin-top: 4px; }
    .resumo-30dias .indicator { width: 12px; height: 4px; border-radius: 2px; }
    .resumo-30dias .indicator.green { background-color: #28a745; }
    .resumo-30dias .indicator.red { background-color: #dc3545; }
    .resumo-conta-corrente {
        margin-top: auto;
        padding-top: 10px;
        border-top: 2px solid #f0f0f0;
    }

    /* Timeline de Transações */
    .transactions-timeline { list-style: none; padding: 0; margin: 0; position: relative; }
    .transactions-timeline::before { content: ''; position: absolute; left: 30px; top: 10px; bottom: 10px; width: 1px; background-color: #e0e0e0; }
    .transactions-timeline li { position: relative; padding: 0 0 25px 60px; min-height: 50px; }
    .transactions-timeline li:last-child { padding-bottom: 0; }
    .transaction-date { position: absolute; left: 0; top: -2px; width: 60px; font-size: 11px; color: #888; text-transform: uppercase; text-align: left; }
    .transaction-date strong { display: block; font-size: 15px; font-weight: 700; color: #333; margin-bottom: 2px; }
    .transaction-dot { width: 8px; height: 8px; border-radius: 50%; position: absolute; left: 26.3px; top: 5px; z-index: 1; background-color: #333; }
    .transaction-dot.hollow { background-color: #fff; border: 2px solid #ccc; left: 24.5px; top: 5px; }
    .transaction-details { font-size: 14px; }
    .transaction-details strong { display: block; color: #333; font-weight: 600; margin-bottom: 3px; }
    .transaction-details span { font-size: 12px; color: #888; }
    
    /* =========================================================
       RESPONSIVIDADE (MEDIA QUERIES)
       ========================================================= */

    /* Para tablets e telas menores (abaixo de 992px) */
    @media (max-width: 992px) {
        .main-content {
            padding: 0 20px; /* Reduz o espaçamento lateral */
            margin: 30px auto; /* Reduz o espaçamento superior/inferior */
        }

        /* Principal: Transforma o layout de 2 colunas em 1 coluna */
        .content-grid {
            grid-template-columns: 1fr;
            gap: 30px; /* Aumenta o espaçamento vertical entre os cards */
        }

        /* Card "Resumo": Também transforma o grid interno em 1 coluna */
        .resumo-grid {
            grid-template-columns: 1fr;
            gap: 25px; /* Ajusta o espaçamento */
        }
    }

    /* Para celulares (abaixo de 768px) */
    @media (max-width: 768px) {
        .main-content {
            padding: 0 15px; /* Reduz ainda mais o espaçamento lateral */
            margin: 20px auto;
        }

        /* Ajusta o espaçamento dos cards */
        .card {
            padding: 20px;
        }
        
        /* Ajusta os títulos */
        .column-header h2 {
            font-size: 18px;
        }
        .card h3 {
            font-size: 15px;
            margin-bottom: 15px;
        }

        /* Ajusta o espaçamento da lista de saldos */
        .saldos-list li {
            padding: 14px 0;
        }

        /* Ajusta a timeline de transações para ser mais compacta */
        .transactions-timeline::before {
            left: 22px; /* Move a linha do tempo para mais perto */
        }
        .transactions-timeline li {
            padding-left: 50px; /* Reduz o recuo dos detalhes */
        }
        .transaction-date {
            width: 50px; /* Ajusta a largura da data */
        }
        .transaction-dot {
            left: 18.3px; /* Centraliza o ponto na nova linha */
        }
        .transaction-dot.hollow {
            left: 16.5px;
        }
    }
</style>

<main class="main-content">
    <div class="content-header">
        Ocultar valores
        <label class="toggle-switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>
    </div>

    <div class="content-grid">
        <div class="column-left">
            <div class="column-header">
                <h2>Meus saldos</h2>
                <a href="#">Ver mais</a>
            </div>
            <div class="card saldos-card">
                <ul class="saldos-list">
                    <li><span>Conta-corrente</span><span class="value">-</span></li>
                    <li><span>Bloqueado</span><span class="value">-</span></li>
                    <li><span>Investimentos<br>c/ bx aut.</span><span class="value">-</span></li>
                    <li class="total"><span>Total disponível</span><span class="value">-</span></li>
                </ul>
            </div>
        </div>

        <div class="column-right">
            <div class="column-header">
                <h2>Resumo</h2>
                <a href="#">Ver mais</a>
            </div>
            <div class="card resumo-card">
                <div class="resumo-grid">
                    <div class="resumo-30dias">
                        <h3>Últimos 30 dias</h3>
                        <ul>
                            <li>
                                <div class="summary-label">
                                    <span>Entrada</span>
                                    <div class="indicator green"></div>
                                </div> 
                                <span>-</span>
                            </li>
                            <li>
                                <div class="summary-label">
                                    <span>Saída</span>
                                    <div class="indicator red"></div>
                                </div>
                                <span>-</span>
                            </li>
                        </ul>
                        
                        <div class="resumo-conta-corrente">
                             <ul>
                                <li>
                                    <span>Conta-corrente</span>
                                    <span class="indicator green"></span>
                                </li>
                            </ul>
                        </div>
                    </div>
                    <div class="resumo-transacoes">
                        <h3>Últimas transações</h3>
                        <ul class="transactions-timeline">
                            <li>
                                <div class="transaction-date"><strong>18</strong><span>JUL</span></div>
                                <div class="transaction-dot"></div>
                                <div class="transaction-details">
                                    <strong>Não foi possível obter as informações</strong>
                                    <span>Tente novamente mais tarde.</span>
                                </div>
                            </li>
                            <li>
                                <div class="transaction-date"><strong>17</strong><span>JUL</span></div>
                                <div class="transaction-dot"></div>
                                <div class="transaction-details">
                                    <strong>Não foi possível obter as informações</strong>
                                    <span>Tente novamente mais tarde.</span>
                                </div>
                            </li>
                            <li>
                                 <div class="transaction-dot hollow"></div>
                                 <div class="transaction-details">
                                    <strong>Não foi possível obter as informações</strong>
                                    <span>Tente novamente mais tarde.</span>
                                </div>
                            </li>
                            <li>
                                 <div class="transaction-dot hollow"></div>
                                 <div class="transaction-details">
                                    <strong>Não foi possível obter as informações</strong>
                                    <span>Tente novamente mais tarde.</span>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>