<!-- Modal -->
<div class="modal fade" id="modalAgregarPiloto"style="z-index:3000 !important;" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Piloto</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
            <input id="idPiloto" value="0" type="hidden"/>
            <div class="row">
              <div class="col-md-12">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Nombre</label>
                    <input type="text" class="form-control inputPiloto" id="nombrePiloto"/>
                    <span class="label label-warning" id="lblNombrePiloto"></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Apellidos</label>
                    <input type="text" class="form-control inputPiloto" id="apellidosPiloto"/>
                    <span class="label label-warning" id="lblApellidosPiloto"></span>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Fecha de nacimiento</label>
                    <input type="text" class="form-control inputPiloto" id="fechaNacimientoPiloto" readonly="true"/>
                    <span class="label label-warning" id="lblFechaNacimientoPiloto"></span>
                  </div>
                </div>
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Número de certificado</label>
                    <input type="text" class="form-control inputPiloto" id="numeroCertificadoPiloto" />
                    <span class="label label-warning" id="lblNumeroCertificadoPiloto"></span>
                  </div>
                </div>
              </div>
              <div class="col-md-12">
                <div class="col-md-6">
                  <div class="form-group">
                    <label>Estatus</label>
                    <select id="idEstatusPiloto" class="form-control">
                      <option value="1">Activo</option>
                      <option value="0">Desactivado</option>
                    </select>
                  </div>
                </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-success" onclick="guardarPiloto()" id="btnAgregarPiloto"><i class="fa fa-plus" aria-hidden="true"></i>
              Agregar</button>
          <button data-dismiss="modal" aria-label="Close" type="button" class="btn btn-danger"><i class="fa fa-times" aria-hidden="true" onclick="limpiarCamposPiloto()"></i> Cerrar</button>
        </div>
    </div>
  </div>
</div>
