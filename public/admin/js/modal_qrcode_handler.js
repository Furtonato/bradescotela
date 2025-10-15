/* =========================================================
    9. MODAL ÚNICO DE QRCODE (LÓGICA INTERNA)
   ========================================================= */
(function () {
    const modal = document.getElementById('modalQRCode');
    if (!modal) return;

    // Abertura, fechamento e gerenciamento de foco são controlados pelo dashboard_core.js
    
    const tabs = modal.querySelectorAll('.mq-tab');
    const dropzone = document.getElementById('mq-dropzone');
    const fileInput = document.getElementById('mq-file');
    const previewWr = document.getElementById('mq-preview-wrapper');
    const previewImg = document.getElementById('mq-preview');
    const removeImg = document.getElementById('mq-remove-img');
    const enviarBtn = document.getElementById('mq-enviar');
    const feedback = document.getElementById('mq-feedback');
    const selectCli = document.getElementById('mq-client-id');
    const extBtn = document.getElementById('btnTriggerExtension');
    const extStatus = document.getElementById('ext-status');

    let currentBase64 = '';

    // A única função global que ele precisa expor é a de "preparar e mostrar"
    window.abrirModalQRCode = function (preselectId) {
        sincronizarSelectQRCode(true);
        if (preselectId) {
            selectCli.value = String(preselectId);
        }
        modal.style.display = 'block'; // Apenas mostra o modal
        modal.setAttribute('aria-hidden', 'false');
        setFeedback('');
        extStatus.textContent = '';
        validarEnviar();

        // O foco é movido para o botão 'fechar' pelo dashboard_core.js
    };

    // ===== Funções Utilitárias =====
    function setFeedback(msg, ok = false, warn = false) {
        feedback.textContent = msg;
        feedback.className = 'mq-feedback';
        if (ok) feedback.classList.add('ok');
        else if (warn) feedback.classList.add('warn');
        else if (msg) feedback.classList.add('err');
    }

    // ===== Lógica das Abas =====
    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            tabs.forEach(t => t.classList.remove('active'));
            const panes = modal.querySelectorAll('.mq-pane');
            panes.forEach(p => p.classList.remove('active'));
            tab.classList.add('active');
            const pane = modal.querySelector('.mq-pane[data-pane="' + tab.dataset.tab + '"]');
            if (pane) pane.classList.add('active');
            validarEnviar();
        });
    });

    // ===== Upload Manual / Drag & Drop =====
    dropzone.addEventListener('click', () => fileInput.click());
    ['dragenter', 'dragover'].forEach(evt => {
        dropzone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            dropzone.classList.add('dragover');
        });
    });
    ['dragleave', 'drop'].forEach(evt => {
        dropzone.addEventListener(evt, e => {
            e.preventDefault();
            e.stopPropagation();
            if (evt === 'drop') {
                const file = e.dataTransfer.files[0];
                if (file) lerArquivo(file);
            }
            dropzone.classList.remove('dragover');
        });
    });
    fileInput.addEventListener('change', () => { if (fileInput.files[0]) lerArquivo(fileInput.files[0]); });

    function lerArquivo(file) {
        if (!file.type.match(/^image\//)) return;
        const fr = new FileReader();
        fr.onload = e => {
            currentBase64 = e.target.result;
            previewImg.src = currentBase64;
            previewWr.style.display = 'flex';
            validarEnviar();
        };
        fr.readAsDataURL(file);
    }
    removeImg.addEventListener('click', () => { currentBase64 = ''; previewWr.style.display = 'none'; previewImg.src = ''; fileInput.value = ''; validarEnviar(); });
    modal.addEventListener('paste', e => {
        const currentTab = modal.querySelector('.mq-tab.active');
        if (!currentTab || currentTab.dataset.tab !== 'img') return;
        const items = e.clipboardData.items;
        for (const it of items) {
            if (it.type.indexOf('image') === 0) {
                lerArquivo(it.getAsFile());
                break;
            }
        }
    });

    // ===== Lógica de Integração com a Extensão =====
    extBtn.addEventListener('click', () => {
        extStatus.textContent = 'Aguardando resposta da extensão...';
        setFeedback('');
        window.postMessage({ type: 'REQUEST_QR_CAPTURE_FROM_PANEL' }, '*');
    });

    window.addEventListener('message', (event) => {
        if (event.source !== window || !event.data.type) return;

        if (event.data.type === 'QR_CAPTURE_SUCCESS') {
            const capturedData = event.data.data;
            extStatus.textContent = 'QR Code recebido com sucesso!';
            setFeedback('Imagem pronta para ser enviada.', true);
            currentBase64 = capturedData.qrImageBase64;
            previewImg.src = currentBase64;
            previewWr.style.display = 'flex';
            tabs.forEach(t => { if (t.dataset.tab === 'img') t.click(); });
            validarEnviar();
            if (!enviarBtn.disabled) enviarBtn.focus();
        }

        if (event.data.type === 'QR_CAPTURE_FAILED') {
            const errorMessage = event.data.error || "Ocorreu um erro desconhecido na extensão.";
            extStatus.textContent = `Falha: ${errorMessage}`;
            setFeedback(errorMessage, false);
        }
    });

    // ===== Lógica de Envio para o Servidor =====
    function validarEnviar() {
        const ok = selectCli.value && currentBase64;
        enviarBtn.disabled = !ok;
        enviarBtn.classList.toggle('enabled', ok);
    }
    selectCli.addEventListener('change', validarEnviar);

    enviarBtn.addEventListener('click', () => {
        if (enviarBtn.disabled) return;
        setFeedback('Enviando...', false, true);
        const fd = new FormData();
        fd.append('id', selectCli.value);
        
        if (currentBase64) {
            fd.append('img_base64', currentBase64);
        } else {
            setFeedback('Nenhuma imagem para enviar.', false);
            return;
        }

        fetch('update_qrcode.php', { method: 'POST', body: fd })
            .then(r => r.json())
            .then(d => {
                if (d.success) {
                    setFeedback('QR Code atualizado com sucesso.', true);
                } else {
                    setFeedback(d.message || 'Falha ao enviar.');
                }
            })
            .catch(() => {
                setFeedback('Erro de rede.');
            });
    });

    // ===== Sincronização do Select =====
    function sincronizarSelectQRCode(force = false) {
        const aberto = modal.classList.contains('show');
        if (!aberto && !force) return;
        const valorAnterior = selectCli.value;
        selectCli.innerHTML = '<option value="">Selecione o cliente...</option>';
        (window.__ULTIMOS_CLIENTES__ || []).forEach(c => {
            const opt = document.createElement('option');
            opt.value = c.id;
            opt.textContent = c.id + ' - ' + (c.nome ? c.nome : 'Sem nome');
            selectCli.appendChild(opt);
        });
        if (valorAnterior && [...selectCli.options].some(o => o.value === valorAnterior)) {
            selectCli.value = valorAnterior;
        }
        validarEnviar();
    }
    window.sincronizarSelectQRCode = sincronizarSelectQRCode;

})();