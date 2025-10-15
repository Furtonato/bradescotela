<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script src="js/modal_update_handler.js"></script>
<script src="js/modal_qrcode_handler.js"></script>
<script src="js/dashboard_core.js"></script>

<script>
/* =========================================================
   INICIALIZAÇÃO
   ========================================================= */
document.addEventListener('DOMContentLoaded',()=>{
  // A função carregarNovosDados() está em dashboard_core.js
  carregarNovosDados(); 
  setInterval(carregarNovosDados, 1000);

  // As funções de sincronização e tema estão em dashboard_core.js
  syncAndFilter('searchInput','searchInputMobile','input');
  syncAndFilter('searchInputMobile','searchInput','input');
  syncAndFilter('filterStatus','filterStatusMobile','change');
  syncAndFilter('filterStatusMobile','filterStatus','change');
  initializeThemeToggle();
});
</script>