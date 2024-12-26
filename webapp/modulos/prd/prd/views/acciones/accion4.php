<script src="js/acciones/accion4.js" type="text/javascript"></script>

<div id="block_paso4" class="col-sm-8" style="display: none;">
	<div class="panel panel-default">
		<div id="ciclo_paso_ph" atr="th"  class="panel-heading">
			Registro de personal.
		</div>
		<div class="panel-body"  style="font-size:12px;">
			<div class="col-sm-12">
				<div class="form-group">
					<div id="empleados_block4" class="col-sm-7 p0" style="margin-top: 10px; margin-right: 20px;">
						<select id="select_empleados4" style="width:100%;">
							<option value="0">Seleccione</option>
							<?php foreach ($empleados as $k => $v) {
							?>
							<option area="<?php echo $v['nomarea']; ?>" value="<?php echo $v['idempleado']; ?>"><?php echo $v['nombre']; ?>
								(<?php echo $v['nomarea']; ?>)
							</option>
							<?php } ?>
						</select>

					</div>
					<div id="empleados_block4" class="col-sm-4 p0" style="margin-top: 10px;">
						<button id="addPersonal4" style=" padding: 0px;" onclick="emp4();" class="btn btn-default btn-sm btn-block">
							Agregar
						</button>
					</div>

					<div id="personal_block4" class="col-sm-12 p0" style="margin-top: 10px;">
						<table id="tt4" class="table">
							<thead>
								<tr>
									<th width="80%">Nombre</th>
									<th width="20%">Accion</th>
								</tr>
							</thead>
							<tbody id="bodyempleado4">

							</tbody>
						</table>
					</div>
					<div id="guardar_block4" class="col-sm-12 p0" style="margin-top: 10px;">

					</div>
				</div>
			</div>
		</div>
	</div>

</div>
<script>
	inicioaccion4(<?php echo $_REQUEST['idop']; ?>,<?php echo $_REQUEST['paso']; ?>,<?php echo $_REQUEST['accion']; ?>,<?php echo $_REQUEST['idap']; ?>,<?php echo $_REQUEST['idp']; ?>);
</script>