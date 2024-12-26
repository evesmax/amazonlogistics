<script src="js/acciones/accion14.js" type="text/javascript"></script>

<div id="block_paso14" class="col-sm-8">
	<div class="panel panel-default">
		<div id="ciclo_paso_ph" atr="th"  class="panel-heading">
			Registro de merma. 
		</div>
		<div class="panel-body"  style="font-size:12px;">
			<div class="col-sm-12">
				<div class="form-group">
					<input type="checkbox" id="totalprdtotal" align="right" onchange="cambiareg(1)" /><b style="color:#6291BB">Registro de producto fabricado Final</b><br>
						<input type="checkbox" id="totalmermatotal" align="right" onchange="cambiareg(2)" checked=""/><b style="color:#6291BB">Registro de merma del producto</b><br>
						<input type="hidden" id="tiporegmerma"  value="2"/>
						
					<div id="insumos_block14" class="col-sm-12 p0" style="margin-top: 10px;">
						
						<div class="col-sm-4 " style="margin-top: 10px;">
							<label id="labelprd" class="tmerma" >Total de merma</label>
							<label id="labelprd" class="tprd" style="display: none">Total de producto fabricado</label>
						</div>
						<div class="col-sm-8" style="margin-top: 10px;">
							<input id="b14_ <?php echo $p -> id_orden_produccion . "_" . $p -> id_producto; ?>" type="text" onkeyup=cantidadmerma(<?php echo $p -> cantidad; ?>,this.value,'b14_<?php echo $p -> id_orden_produccion . '_' . $p -> id_producto; ?>') name="" value="<?php echo $p -> cantidad; ?>" class="form-control mer">
						</div>
					
					
						<div class="col-sm-4" style="margin-top: 10px;">
							Tipo de merma
						</div>
						<div class="col-sm-8" style="margin-top: 10px;">
							<select id="tipomerma"  style="width:100%;" class="form-control" >
								<?php if ($tipoMerma) {
								while ($t = $tipoMerma -> fetch_object()) {
								?>			
								<option value="<?php echo $t -> id; ?>">
									<?php echo $t -> tipo_merma; ?>
								</option>
								<?php	}
									}
								?>
							</select>
						</div>
						<br>
						<div class="col-sm-12" style="margin-top: 10px;">
							<div class="form-group shadow-textarea">
								<label for="">Observaciones</label>
								<textarea class="form-control z-depth-1" id="observacion14" rows="3" placeholder="Describa..."></textarea>
							</div>
						</div>
					</div>
			
					<div id="guardar_block14" class="col-sm-12 p0" style="margin-top: 10px;">
						<div class="col-sm-3">
							<button id="save_block14"  class="btn btn-primary btn-sm btn-block" onclick="savePasoAccion14(<?php echo $_REQUEST['accion']; ?>,<?php echo $_REQUEST['idop']; ?>,<?php echo $_REQUEST['paso']; ?>,<?php echo $_REQUEST['idap']; ?>,<?php echo $_REQUEST['idp']; ?>,<?php echo $p -> cantidad; ?>)">
								Guardar
							</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

</div>
