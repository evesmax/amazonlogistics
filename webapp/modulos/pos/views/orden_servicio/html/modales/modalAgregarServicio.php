<!-- Modal -->
<div class="modal fade" id="modalAgregarServicios"style="z-index:3000 !important;" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLabel">Agregar Servicio</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <div class="modal-body">
        <input type="text" class="form-control" placeholder="Buscar..." id="inputBuscarSer" onkeyup="buscarServicios()" />
        <span class="label label-warning" id="labelBuscando"></span>
        <table class="table table-striped" id="tablaMasServicios">
          <thead>
            <tr>
              <td>Seleccionar</td>
              <td>Servicio</td>
            </tr>
          </thead>
          <tbody id="tbodyServicios">

              <?php
                    $contador = 0;
                    foreach ($productos['productos'] as $key => $r) {
              ?>
                <tr>
                  <td>
                    <label class="containerCheckBox">
                      <input id="check<?php echo $contador; ?>" type="checkbox" value="<?php echo $r['id']; ?>">
                      <span class="checkmark"></span>
                    </label>

                  </td>
                  <td><label><?php echo $r["nombre"] ?></label></td>
                </tr>
              <?php
                  $contador++;
                  }
              ?>

          </tbody>
        </table>
        <input id="countServicios" value="<?php echo $contador ?>" type="hidden"></input>
      </div>
      <div class="modal-footer">
        <button class="btn btn-success" id="btnModalAgregarServicios" onclick="agregarServicios()"><i class="fa fa-plus" aria-hidden="true"></i>
 Agregar</button>
        <button type="button" class="btn btn-danger" onclick="cerrarModalAgregarServicios()"><i class="fa fa-times" aria-hidden="true"></i>

 Cerrar</button>
      </div>
    </div>
  </div>
</div>
