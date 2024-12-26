<!-- Modal -->
<div class="modal fade" id="exampleModal"style="z-index:3000 !important;" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <div class="form-group">
          <label>Buscar <label id="claveTituloModal"></label> <div id="esperaBuscarOrigenes"></div></label>
          <input type="search" class="form-control" id="inputBuscarModal" onkeyup="buscarOrigDes(this);"></input>
          <input type="hidden" id="posicionSeleccionada"></input>
          <input type="hidden" id="isOrigen"></input>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>
