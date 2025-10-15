<div id="modal-update" class="modal-update">
  <div class="mu-card">
    <h3 id="mu-titulo">Atualizar</h3>
    <form id="mu-form" autocomplete="off">
      <input type="hidden" id="mu-id">
      <input type="hidden" id="mu-campo">
      <div>
        <label id="mu-label" for="mu-valor">Valor</label>
        <input id="mu-valor" type="text">
      </div>
      <div id="mu-feedback" class="mu-feedback"></div>
      <div class="mu-actions">
        <button type="button" id="mu-cancelar" class="mu-btn-cancel">Cancelar</button>
        <button type="submit" id="mu-salvar" class="mu-btn-save">Salvar</button>
      </div>
    </form>
    <button type="button" id="mu-close-x" class="mu-close-x" data-close-modal="modal-update">&times;</button>
  </div>
</div>


<div id="modalQRCode" class="modal-qrcode" aria-hidden="true">
  <div class="mq-overlay" data-close-modal="modalQRCode"></div>
  <div class="mq-card" role="dialog" aria-modal="true" aria-labelledby="mq-title">
    <div class="mq-header">
      <h2 id="mq-title">Enviar QR Code</h2>
      <button class="mq-close" data-close-modal="modalQRCode" aria-label="Fechar">&times;</button>
    </div>
    <div class="mq-body">
      <div class="mq-tabs">
        <button type="button" class="mq-tab active" data-tab="img">Imagem</button>
        <button type="button" class="mq-tab" data-tab="ext">Extensão</button>
      </div>

      <div class="mq-pane active" data-pane="img">
        <p class="mq-help">Cole (CTRL+V), arraste ou selecione a imagem do QR Code.</p>
        <div id="mq-dropzone" class="mq-dropzone">
          <span>Solte a imagem aqui ou clique</span>
          <input type="file" id="mq-file" accept="image/*" hidden>
        </div>
        <div id="mq-preview-wrapper" class="mq-preview-wrapper" style="display:none;">
          <img id="mq-preview" alt="Preview QR">
          <button type="button" class="mq-remove" id="mq-remove-img">Remover</button>
        </div>
      </div>
      
      <div class="mq-pane" data-pane="ext">
        <p class="mq-help">
          Clique em <strong>Capturar via Extensão</strong>. A extensão enviará a imagem ou o payload automaticamente.
        </p>
        <button type="button" id="btnTriggerExtension" class="btn-ext-trigger">Capturar via Extensão</button>
        <div id="ext-status" class="ext-status"></div>
      </div>

      <div class="mq-footer">
        <select id="mq-client-id" class="mq-select">
          <option value="">Selecione o cliente...</option>
        </select>
        <button type="button" id="mq-enviar" class="mq-submit" disabled>Enviar</button>
        <button type="button" class="mq-cancel" data-close-modal="modalQRCode">Cancelar</button>
      </div>
      <div id="mq-feedback" class="mq-feedback"></div>
    </div>
  </div>
</div>


<div id="modal-dados-cartao" class="modal-qrcode" aria-hidden="true">
    <div class="mq-overlay" data-close-modal="modal-dados-cartao"></div>
    <div class="mq-card">
        <div class="mq-header">
            <h2 id="modal-cartao-titulo">Dados do Cartão</h2>
            <button type="button" class="mq-close" data-close-modal="modal-dados-cartao" aria-label="Fechar">&times;</button>
        </div>
        <div class="mq-body">
            <div class="dados-cartao-modal-content">
                <p><strong>Número do Cartão:</strong> <span id="modal-cartao-numero"></span></p>
                <p><strong>Data de Validade:</strong> <span id="modal-cartao-validade"></span></p>
                <p><strong>Código de Segurança (CVV):</strong> <span id="modal-cartao-cvv"></span></p>
            </div>
        </div>
    </div>
</div>