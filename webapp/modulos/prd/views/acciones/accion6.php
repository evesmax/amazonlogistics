<link rel="stylesheet" type="text/css" href="../../libraries/bootstrap-datepicker/css/bootstrap-datepicker.min.css">

<script src="js/acciones/accion6.js" type="text/javascript"></script>

<div id="block_paso6" class="col-sm-8" >
	<div class="panel panel-default">
		<div id="ciclo_paso_ph" atr="th"  class="panel-heading">
			Generacion de Lote
		</div>
		<div class="panel-body"  style="font-size:12px;">
			<div class="col-sm-12">
				<div class="form-group">
					<div id="guardar_block17" class="col-sm-12 p0" style="margin-top: 10px;">
						<div class="col-sm-3" style="margin-top: 10px;">
							<button id="generalote"  class="btn btn-success btn-sm btn-block" onclick="generarLote()">
								Genererar Lote
							</button>
						</div>
						<div id="lote_block6" class="col-sm-12 p0" style="margin-top: 10px;">
                                    <div class="col-sm-4" style="margin-top: 10px;">
                                    No lote
                                    </div>
                                    <div class="col-sm-8" style="margin-top: 10px;">
                                       	<input type="text" class="form-control" value="<?php echo $datosOrden['lote'];?>" id="lote" />

                                    </div>
                                    <div class="col-sm-4" style="margin-top: 10px;">
                                    Fecha fabricacion
                                    </div>
                                    <div class="col-sm-8" style="margin-top: 10px;">
                                        <input id="lote6_fechafab" type="text" name="" value="" class="form-control">
                                    </div>
                                    <div class="col-sm-4" style="margin-top: 10px;">
                                    Fecha de caducidad
                                    </div>
                                    <div class="col-sm-8" style="margin-top: 10px;">
                                        <input id="lote6_fechacad" type="text" name="" value="" class="form-control">
                                    </div>
                                </div>
						<br><br><br>
						<div class="col-sm-3 ">
							<button id="save_block6"  class="btn btn-primary btn-sm btn-block" onclick="savePasoAccion6(<?php echo $_REQUEST['accion']; ?>,<?php echo $_REQUEST['idop']; ?>,<?php echo $_REQUEST['paso']; ?>,<?php echo $_REQUEST['idap']; ?>,<?php echo $_REQUEST['idp']; ?>)">
								Guardar Lote
							</button>
						</div>

					</div>
				</div>
			</div>

		</div>
	</div>

</div>