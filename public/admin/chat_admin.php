<style>
    #admin-chat-window{background-color:var(--bg);padding:1rem;display:flex;flex-direction:column;gap:1rem}
    #admin-chat-form{gap:.5rem; align-items: flex-end;}

    #admin-chat-input {
        background-color: var(--card-bg);
        color: var(--fg);
        border: 1px solid var(--border);
        border-radius: 20px;
        padding: .6rem 1rem;
        resize: none;
        /* A MUDANÇA ESTÁ AQUI: */
        overflow-y: auto; /* Alterado de 'hidden' para 'auto' */
        min-height: 40px;
        max-height: 120px;
        line-height: 1.4;
    }
    #admin-chat-input::placeholder{color:var(--fg);opacity:.6}
    #admin-chat-input:focus{background-color:var(--card-bg);color:var(--fg);border-color:var(--primary-color);box-shadow:0 0 0 .25rem rgba(0,158,247,.25)}
    
    .btn-chat-action{border:none;border-radius:50%;width:40px;height:40px;flex-shrink:0;display:flex;align-items:center;justify-content:center;color:#fff}.btn-chat-action:hover{filter:brightness(1.2)}.btn-chat-action.btn-primary{background-color:var(--primary-color)}.btn-chat-action.btn-secondary{background-color:#565e64}
    .message-row{display:flex;align-items:flex-start;gap:10px;max-width:85%}.message-content{display:flex;flex-direction:column}.avatar-chat{width:36px;height:36px;border-radius:50%;background-color:var(--card-bg);border:1px solid var(--border);display:flex;align-items:center;justify-content:center;color:var(--text-muted);flex-shrink:0}.avatar-chat i{font-size:1.2rem}.message-bubble{padding:10px 15px;border-radius:18px;color:var(--fg);font-size:.9rem;word-wrap:break-word}.message-timestamp{font-size:.7rem;color:var(--text-muted);margin-top:4px;padding:0 5px}.message-row.client{align-self:flex-start}.message-row.client .message-bubble{background-color:var(--card-bg);border:1px solid var(--border);border-top-left-radius:5px}.message-row.admin{align-self:flex-end;flex-direction:row-reverse}.message-row.admin .message-bubble{background-color:var(--primary-color);color:#fff;border-top-right-radius:5px}.message-row.admin .message-content{align-items:flex-end}#suggestions-container{background-color:var(--card-bg);border:1px solid var(--border);border-radius:12px;padding:1rem;flex-shrink:0}.chat-suggestions{display:flex;flex-wrap:wrap;gap:8px}.suggestion-pill{padding:4px 12px;border-radius:999px;background-color:var(--bg);border:1px solid var(--border);font-size:.8rem;cursor:pointer;transition:all .2s ease;color:var(--fg);opacity:.8}.suggestion-pill:hover{border-color:var(--primary-color);color:var(--primary-color);opacity:1}.offcanvas-header{background-color:var(--primary-color);color:#fff;border-radius:0 0 1.25rem 1.25rem;margin:0;padding:.75rem 1.25rem;border-bottom:none}.offcanvas-title{flex-grow:1;text-align:center;font-weight:600;font-size:1rem;padding-left:24px}.offcanvas-header .btn-close{--bs-btn-close-color:white;--bs-btn-close-white-filter:none;margin:-.5rem -.75rem -.5rem auto}
</style>

<div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasChat" style="--bs-offcanvas-bg: var(--bg); --bs-offcanvas-border-color: var(--border);">
   <div class="offcanvas-header">
        <h5 class="offcanvas-title">Chat com Cliente</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body d-flex flex-column">
        <div id="suggestions-container" class="mb-3">
            <div id="admin-chat-suggestions" class="chat-suggestions"></div>
        </div>
        <div id="admin-chat-window" class="flex-grow-1 mb-2"></div>
        <form id="admin-chat-form" class="d-flex">
            <textarea id="admin-chat-input" class="form-control" placeholder="Digite sua mensagem…" rows="1"></textarea>
            
            <button type="button" id="btn-suggest-ai" class="btn btn-secondary btn-chat-action ms-2" title="Sugerir resposta com IA">
                <i class="bi bi-robot"></i>
            </button>
            
            <button type="submit" class="btn btn-primary btn-chat-action ms-2" title="Enviar">
                <i class="bi bi-send-fill"></i>
            </button>
        </form>
    </div>
</div>

<script>
    const win = document.getElementById('admin-chat-window');
    const form = document.getElementById('admin-chat-form');
    const input = document.getElementById('admin-chat-input');
    const suggestionsContainer = document.getElementById('admin-chat-suggestions');
    const offcanvasChat = document.getElementById('offcanvasChat');
    const suggestButton = document.getElementById('btn-suggest-ai');

    function appendAdminMessage(msg) {
        if (document.querySelector(`.message-row[data-id='${msg.id}']`)) return;
        const isAdmin = msg.sender_id == 0;
        const messageRow = document.createElement('div');
        messageRow.className = `message-row ${isAdmin ? 'admin' : 'client'}`;
        messageRow.dataset.id = msg.id;
        const avatar = document.createElement('div');
        avatar.className = 'avatar-chat';
        avatar.innerHTML = isAdmin ? '<i class="bi bi-shield-check"></i>' : '<i class="bi bi-person-fill"></i>';
        const messageContent = document.createElement('div');
        messageContent.className = 'message-content';
        const bubble = document.createElement('div');
        bubble.className = 'message-bubble';
        bubble.textContent = msg.message;
        const timestamp = document.createElement('div');
        timestamp.className = 'message-timestamp';
        timestamp.textContent = new Date(msg.created_at.replace(' ', 'T') + 'Z').toLocaleTimeString('pt-BR', { hour: '2-digit', minute: '2-digit' });
        messageContent.appendChild(bubble);
        messageContent.appendChild(timestamp);
        messageRow.appendChild(avatar);
        messageRow.appendChild(messageContent);
        win.appendChild(messageRow);
        window.chatLastId = Math.max(window.chatLastId || 0, msg.id);
        win.scrollTop = win.scrollHeight;
    }

    function fetchAdminMessages() {
        if (!window.chatCookie) return;
        fetch(`/admin/chat_fetch.php?cookie=${window.chatCookie}&since_id=${window.chatLastId||0}`).then(r => r.ok ? r.json() : []).then(list => list.forEach(appendAdminMessage)).catch(console.error);
    }

    function sendChatMessage(text) {
        if (!text || !window.chatCookie) return;
        fetch('/admin/chat_send.php', { method: 'POST', headers: { 'Content-Type': 'application/x-www-form-urlencoded' }, body: `cookie=${encodeURIComponent(window.chatCookie)}&message=${encodeURIComponent(text)}` }).then(r => r.json()).then(res => { if (res.success) { input.value = ''; input.style.height = 'auto'; fetchAdminMessages(); } }).catch(console.error);
    }

    function generateSuggestions() {
        suggestionsContainer.innerHTML = ''; 
        const currentHour = new Date().getHours();
        let greetings = [];
        if (currentHour >= 5 && currentHour < 12) { greetings = ["Bom dia! Em que posso ajudar?"]; } else if (currentHour >= 12 && currentHour < 18) { greetings = ["Boa tarde! Como posso te ajudar hoje?"]; } else { greetings = ["Boa noite! Em que posso ajudar?"]; }
        const serviceMessages = [ "Um momento, por favor, enquanto verifico sua solicitação.", "Para resgatar seus pontos Livelo, acesse o site ou app da Livelo.", "Sobre o acesso não autorizado, para sua segurança, recomendo alterar sua senha imediatamente.", "Posso te ajudar com mais alguma informação?", "Sua solicitação foi registrada com sucesso.", "Só um instante, estou consultando as informações." ];
        const allSuggestions = greetings.concat(serviceMessages);
        allSuggestions.forEach(text => { const pill = document.createElement('div'); pill.className = 'suggestion-pill'; pill.textContent = text; pill.onclick = () => sendChatMessage(text); suggestionsContainer.appendChild(pill); });
    }

    async function getAiSuggestion() {
        const originalIcon = suggestButton.innerHTML;
        suggestButton.innerHTML = '<i class="bi bi-arrow-clockwise spin"></i>';
        suggestButton.disabled = true;
        const history = Array.from(document.querySelectorAll('#admin-chat-window .message-row')).map(row => ({
            sender: row.classList.contains('admin') ? 'Atendente' : 'Cliente',
            text: row.querySelector('.message-bubble').textContent
        }));
        if (history.length === 0) {
             alert('O histórico de chat está vazio. A IA precisa de contexto.');
             suggestButton.innerHTML = originalIcon;
             suggestButton.disabled = false;
             return;
        }
        try {
            const response = await fetch('/admin/sugerir_resposta.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ history: history })
            });
            if (!response.ok) throw new Error('Falha na resposta do servidor.');
            const data = await response.json();
            if (data.suggestion) {
                input.value = data.suggestion;
                input.dispatchEvent(new Event('input')); 
                input.focus();
            } else if (data.error) {
                alert('Erro da IA: ' + data.error);
            }
        } catch (error) {
            console.error('Erro ao buscar sugestão:', error);
            alert('Não foi possível obter uma sugestão no momento.');
        } finally {
            suggestButton.innerHTML = originalIcon;
            suggestButton.disabled = false;
        }
    }
    
    if (input) {
        input.addEventListener('input', () => {
            input.style.height = 'auto';
            input.style.height = (input.scrollHeight) + 'px';
        });
        input.addEventListener('keydown', (e) => {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                form.requestSubmit();
            }
        });
    }

    if (form) form.addEventListener('submit', e => { e.preventDefault(); sendChatMessage(input.value.trim()); });
    if (suggestButton) suggestButton.addEventListener('click', getAiSuggestion);
    if(offcanvasChat) offcanvasChat.addEventListener('show.bs.offcanvas', generateSuggestions);
    setInterval(fetchAdminMessages, 3000);

    const style = document.createElement('style');
    style.innerHTML = `@keyframes spin { from { transform: rotate(0deg); } to { transform: rotate(360deg); } } .spin { animation: spin 1s linear infinite; }`;
    document.head.appendChild(style);
</script>