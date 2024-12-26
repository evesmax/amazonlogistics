<script src="js/acciones/accion19.js" type="text/javascript"></script>

<div id="block_paso19" class="col-sm-8">
	<div class="panel panel-default">
		<div id="ciclo_paso"   class="panel-heading">
			<?php echo $_REQUEST['nac']; ?>
		</div>
		<div class="panel-body"  style="font-size:12px;">
			<div class="col-sm-12" id="iniciar" style="margin-top: 10px; font-size:12px;"><b>Operador</b><br>

		<?php		if($personal->num_rows>0){?>
          				<select id="operador" class="form-control"  >
		  					<option value="0">-Seleccione-</option>
         			<?php	while ($rowSqlpaso4 = $personal->fetch_assoc()) {?>
           					<option  value="<?php echo $rowSqlpaso4['idEmpleado']; ?>"><?php echo $rowSqlpaso4['nombre']; ?></option>
          			<?php	} ?>
          				</select>
       <?php 		} ?>
       				<br>
					<button id="ini_block19"  class="btn btn-success btn-sm " onclick="iniciar(<?php echo $_REQUEST['accion']; ?>, <?php echo $_REQUEST['idop']; ?>,<?php echo $_REQUEST['paso']; ?>,<?php echo $_REQUEST['idap']; ?>,<?php echo $_REQUEST['idp']; ?>)">
						<b>Iniciar Actividad</b>
					</button>
			</div>
			
			<div class="col-sm-12 alert alert-warning" id="terminar" style="display: none;" role="alert" align="center">
					<b style="font-size: 14px">Actividad iniciada</b>
					<br>Tiempo transcurrido
					   <div id="tiempo" style="font-size: 30px;font-weight: bold;"/>          
					<button id="fin_block19"   class="btn btn-danger btn-sm" onclick="finalizar(<?php echo $_REQUEST['accion']; ?>, <?php echo $_REQUEST['idop']; ?>,<?php echo $_REQUEST['paso']; ?>,<?php echo $_REQUEST['idap']; ?>,<?php echo $_REQUEST['idp']; ?>)">
						<b>Terminar Actividad</b>
					</button>
			</div>
		</div>
	</div>
</div>
<script>
$( document ).ready(function() {
<?php
if($actividad!=0){?>
	$("#iniciar").hide();
	$("#terminar").show();
	tiempo('<?php echo $actividad['f_ini'];?>');
<?php }
?>
});		






</script>