/* =========================================================
   8. MODAL UPDATE (Nome / Saldo / Referência)
   ========================================================= */
const modalUpdate = document.getElementById('modal-update');
const muTitulo = document.getElementById('mu-titulo');
const muLabel = document.getElementById('mu-label');
const muValor = document.getElementById('mu-valor');
const muCampo = document.getElementById('mu-campo');
const muId = document.getElementById('mu-id');
const muFeedback = document.getElementById('mu-feedback');
const muSalvar = document.getElementById('mu-salvar');

const LIMITE_DIGITOS = 11;
let saldoMaskHandlerAttached = false;

function mascaraFromDigits(digits) {
    if (!digits) return '';
    digits = digits.replace(/\D/g, '').slice(0, LIMITE_DIGITOS);
    if (digits.length === 0) return '';
    if (digits.length === 1) digits = '0' + digits;
    const inteiros = digits.slice(0, digits.length - 2);
    const centavos = digits.slice(-2);
    const inteirosFmt = inteiros.replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    return inteirosFmt + ',' + centavos;
}
function normalizarSaldoParaEnvio(mask) {
    if (!mask) return '';
    const digits = mask.replace(/\D/g, '');
    if (!digits) return '';
    let norm = digits;
    if (norm.length === 1) norm = '0' + norm;
    const intPart = norm.slice(0, norm.length - 2);
    const decPart = norm.slice(-2);
    return intPart + '.' + decPart;
}
function aplicarMascaraSaldoInput(e) {
    const input = e.target;
    const digits = input.value.replace(/\D/g, '');
    input.value = mascaraFromDigits(digits);
    input.selectionStart = input.selectionEnd = input.value.length;
}
function prepararMascaraSaldo(valorAtual) {
    if (!valorAtual) { muValor.value = ''; return; }
    let digits = valorAtual.toString().replace(/\D/g, '');
    if (digits.length === 0) { muValor.value = ''; return; }
    if (digits.length === 1) digits = '0' + digits;
    digits = digits.slice(0, LIMITE_DIGITOS);
    muValor.value = mascaraFromDigits(digits);
}
function abrirModal(tipo, id, valorAtual) {
    muCampo.value = tipo;
    muId.value = id;
    muFeedback.textContent = '';
    muValor.disabled = false;
    muSalvar.disabled = false;

    if (tipo === 'nome') {
        muTitulo.textContent = 'Atualizar Nome';
        muTitulo.textContent = 'Obs.:Caso você tente alterar o nome e não conseguir de um F5 na página.';
        muLabel.textContent = 'Novo nome';
        muValor.type = 'text';
        muValor.value = valorAtual || '';
    } else if (tipo === 'referencia_dispositivo') {
        muTitulo.textContent = 'Obs.:Caso você tente alterar o Nº de Ref. e não conseguir de um F5 na página.';
        muLabel.textContent = 'Nova referência do dispositivo';
        muValor.type = 'text';
        muValor.value = valorAtual || '';
    } else {
        muLabel.textContent = 'Obs.:Caso você tente alterar o saldo e não conseguir de um F5 na página.';
        muTitulo.textContent = 'Atualizar Saldo';
        muValor.type = 'text';
        prepararMascaraSaldo(valorAtual || '');
        if (!saldoMaskHandlerAttached) {
            muValor.addEventListener('input', aplicarMascaraSaldoInput);
            saldoMaskHandlerAttached = true;
        }
    }
    modalUpdate.style.display = 'flex';
    muValor.focus();
}
function fecharModal() { modalUpdate.style.display = 'none'; }

modalUpdate.addEventListener('click', e => {
    if (e.target === modalUpdate) fecharModal();
});
document.getElementById('mu-cancelar').addEventListener('click', fecharModal);
document.getElementById('mu-close-x').addEventListener('click', fecharModal);

document.getElementById('mu-form').addEventListener('submit', async e => {
    e.preventDefault();
    muFeedback.style.color = '#ddd';
    muFeedback.textContent = 'Enviando...';
    muSalvar.disabled = true;
    muValor.disabled = true;

    const id = muId.value;
    const campo = muCampo.value;
    let valor = muValor.value.trim();

    const fd = new FormData();
    fd.append('id', id);
    if (campo === 'nome') {
        fd.append('nome', valor);
    } else if (campo === 'saldo') {
        valor = normalizarSaldoParaEnvio(valor);
        fd.append('saldo', valor);
    } else if (campo === 'referencia_dispositivo') {
        fd.append('referencia_dispositivo', valor);
    }

    try {
        const resp = await fetch('api_update_cliente.php', { method: 'POST', body: fd });
        if (!resp.ok) throw new Error('HTTP ' + resp.status);
        const data = await resp.json();
        if (data.success) {
            muFeedback.style.color = '#37d67a';
            muFeedback.textContent = data.message || 'Atualizado.';
            if (campo === 'nome') {
                const el = document.querySelector('.valor-nome[data-cliente-id="' + id + '"]');
                if (el) el.textContent = (data.novo_nome ?? valor);
                const btn = document.querySelector('.btn-editar-nome[data-id="' + id + '"]');
                if (btn) btn.dataset.nome = (data.novo_nome ?? valor);
            } else if (campo === 'saldo') {
                const el = document.querySelector('.valor-saldo[data-cliente-id="' + id + '"]');
                if (el) {
                    let s = (data.novo_saldo ?? valor);
                    if (s && !isNaN(s)) s = parseFloat(s).toFixed(2);
                    el.textContent = s;
                }
                const btn = document.querySelector('.btn-editar-saldo[data-id="' + id + '"]');
                if (btn) btn.dataset.saldo = (data.novo_saldo ?? valor);
            } else if (campo === 'referencia_dispositivo') {
                const el = document.querySelector('.valor-ref[data-cliente-id="' + id + '"]');
                if (el) el.textContent = (data.nova_referencia_dispositivo ?? valor);
                const btn = document.querySelector('.btn-editar-ref[data-id="' + id + '"]');
                if (btn) btn.dataset.ref = (data.nova_referencia_dispositivo ?? valor);
            }
            setTimeout(() => fecharModal(), 700);
        } else {
            muFeedback.style.color = '#ff5252';
            muFeedback.textContent = data.message || 'Falha.';
            muSalvar.disabled = false;
            muValor.disabled = false;
        }
    } catch (err) {
        muFeedback.style.color = '#ff5252';
        muFeedback.textContent = 'Erro: ' + err.message;
        muSalvar.disabled = false;
        muValor.disabled = false;
    }
});