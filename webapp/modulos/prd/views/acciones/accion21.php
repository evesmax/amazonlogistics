<script src="js/acciones/accion21.js" type="text/javascript"></script>

<div id="block_paso20" class="col-sm-8" >
	<div class="panel panel-default">
		<div id="ciclo_paso_ph" atr="th"  class="panel-heading">
			Generacion  de etiqueta caja master
		</div>
		<div class="panel-body"  style="font-size:12px;">
			<div class="col-sm-12 alert alert-warning" style="margin-top: 10px;">
				Usted tiene un total de <?php echo $numcajas;?> cajas, se generaran ese numero de etiquetas.
			</div>
			<div class="col-sm-3" style="margin-top: 10px;">
				<button id="generaEtiqueta"  class="btn btn-success btn-sm btn-block" onclick="generarEtiqueta(<?php echo $_REQUEST['idop']; ?>)">
					Imprimir Etiqueta
				</button>
			</div>
			<div class="col-sm-3" style="margin-top: 10px;">
				<button id="finalizar"  class="btn btn-primary btn-sm btn-block" onclick="savePasoAccion21(<?php echo $_REQUEST['accion']; ?>,<?php echo $_REQUEST['idop']; ?>,<?php echo $_REQUEST['paso']; ?>,<?php echo $_REQUEST['idap']; ?>,<?php echo $_REQUEST['idp']; ?>)">
					Finalizar
				</button>
			</div>
			 
		</div>
	</div>
</div>