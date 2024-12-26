<script src="js/acciones/accion9.js" type="text/javascript"></script>

<div id="block_paso9" class="col-sm-8" >
	<div class="panel panel-default">
		<div id="ciclo_paso_ph" atr="th"  class="panel-heading">
			Fin orden de produccion
		</div>
		<div class="panel-body"  style="font-size:12px;">
			<div class="col-sm-12">
				<div class="form-group">
					<div id="guardar_block9" class="col-sm-12 p0" style="margin-top: 10px;">
						<div class="col-sm-3">
							<button id="save_block9"  class="btn btn-primary btn-sm btn-block" onclick="savePasoAccion9(<?php echo $_REQUEST['accion']; ?>,<?php echo $_REQUEST['idop']; ?>,<?php echo $_REQUEST['paso']; ?>,<?php echo $_REQUEST['idap']; ?>,<?php echo $_REQUEST['idp']; ?>)">
								Finalizar produccion
							</button>
						</div>
					</div>
				</div>
			</div>

		</div>
	</div>

</div>