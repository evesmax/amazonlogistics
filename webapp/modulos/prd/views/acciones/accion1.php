<script src="js/acciones/accion1.js" type="text/javascript"></script>

<div id="block_paso1" class="col-sm-8" >
	<div class="panel panel-default">
		<div id="ciclo_paso_ph" atr="th"  class="panel-heading">
			Formulacion de insumos
		</div>
		<div class="panel-body"  style="font-size:12px;">
			<div class="col-sm-12">
				<div class="form-group">
					<div id="insumos_block1" class="col-sm-12 p0" style="margin-top: 10px;">

					</div>

					<div id="guardar_block1" class="col-sm-12 p0" style="margin-top: 10px;">

					</div>
				</div>
			</div>

		</div>
	</div>

</div>
<script>
inicioaccion(<?php echo $_REQUEST['idop']; ?>,<?php echo $_REQUEST['paso']; ?>,<?php echo $_REQUEST['accion']; ?>,<?php echo $_REQUEST['idap']; ?>,<?php echo $_REQUEST['idp']; ?>);
</script>