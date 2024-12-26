<div class="modal fade" tabindex="-1" role="dialog" id="modalImpuesto">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"><label id="nombreProducto"></<label></h4>
        <input type="hidden" id="idProducto" />
        <input type="hidden" id="indexRow" />
      </div>
      <div class="modal-body">
        <table class="table table-bordered">
          <tr>
            <th></th>
            <th>ID</th>
            <th>Nombre</th>
            <th>Valor</th>
          </tr>
          <?php while($row = $impuestos->fetch_assoc()){ ?>
            <tr>
              <td>
                  <label class="containerCheckBox">
                    <input class="checkImpuesto" data-nombre="<?php echo $row["nombre"]; ?>" type="checkbox" value="<?php echo $row['idImpuesto']; ?>">
                    <span class="checkmark"></span>
                  </label>
              </td>
              <td><label><?php echo $row["idImpuesto"]; ?></label></td>
              <td><label><?php echo $row["nombre"]; ?></label></td>
              <td><label><?php echo $row["valor"]; ?></label></td>
            </tr>
          <?php }?>
        </table>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" onclick="cancelarImpuestos()">Cancelar</button>
        <button type="button" class="btn btn-primary" onclick="guardarImpuestos()" id="btnGuardarImpuestos">Guardar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
