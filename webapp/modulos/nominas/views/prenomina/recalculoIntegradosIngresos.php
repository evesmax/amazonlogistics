<!DOCTYPE html>

<html>
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/recalculosdi.js"></script>
	<script type="text/javascript" src="js/moment.min.js"></script>
	<link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
	<title>RECALCULO DE INTEGRADOS POR INGRESOS</title>
</head>
<body>
	<div style="text-align:center;font-family: Courier;background-color:#F5F5F5;font-size: 25px;">
		<b>Recalculo de integrados por ingresos</b>
	</div>
	<br>
	<div class="container well" style="width: 98%;">
		<div class="alert alert-info table-responsive">
			<div class="col-md-12" style="text-align: center;font-size: 15px;">
				<?php echo $periodoconfi['nombre']; ?>
			</div>
			<br>
			<br>

			<div class="col-md-12" style="text-align: center;">
				<?php echo  "<b>Fecha Inicio</b>"." ".$periodoconfi['fi']." "."<b>Fecha Final</b>"." ".$periodoconfi['ff']; ?>
			</div>
		</div>
		
		<?php if ($periodoconfi['bimestre']=='') {?>
		<div class="alert alert-danger" style="text-align: center;">
			El periodo no pertenece a bimestral.
		</div>
	</div>

<!-- MOVERLO EL SI NO TIENE -->
		<?php } else{?> 

		<div class="panel panel-default table-responsive">
			<div class="panel-heading" style="text-align: right;">
			<button type="button" class="btn btn-primary btn-sm" id="cargarSDI" style="text-align: center;" data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>">Recalcular integrados</button>
			</div>
				<div class="panel-body">
					<div class="alert alert-info table-responsive" cellspacing="0" width="100%">
						<table id="tablasdi" class="table table-striped table-bordered table-hover" cellspacing="0" width="100%" 
						style="font-size:13px";>
							<thead> 
								<tr style='background-color:#B4BFC1;color:#000000;height: 35px;'>
									<td>Nombre empleado</td>
									<td>SDI activo</td>
									<td>Dias Bimestre</td>
									<td>Incapacidades</td>
									<td>Faltas,permisos sin goce,dias castigo</td>
									<td>Total de dias recalculo</td>
									<td>Total de percepciones variables</td>
									<td>Total de integrar</td>
									<td>SDI recalculado</td>
									<td>Inicio vigencia</td>
									<td>Fin vigencia</td>
									<td>Ver</td>
								</tr>
							</thead>
							<tbody>
							</tbody>
						</table>
						<br>
						<div style="text-align:right">
							<button type="button" class="btn btn-primary btn-sm" id="existeSdiBimestral" name="existeSdiBimestral" style="text-align:center;width: 120px;" data-loading-text="Guardando<i class='fa fa-refresh fa-spin'></i>"><span class="glyphicon glyphicon-floppy-disk"></span> Aplicar cambio</button> 
							
							<!-- // if($calculoptuview){ 
							// 	echo "<script type='text/javascript'>
							// 	datosCalculoPTU = '".json_encode($encode)."';
							// 	</script>
							// 	";
							// } -->
							
						</div>
					</div>		
				</div>
			</div>		
		</div>
<?php } ?>
		<div class="modal fade" id="myModal" role="dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title" style="text-align: center;height:30px;margin-left:5px;margin-top: 3px;font-size: 16px;">
							Conceptos por empleado
						</h4>
					</div>
					<div class="modal-body">
						<div class="alert alert-info table-responsive">
							<table class="table table-bordered table-hover" id="tableconceptos" style="width: 100%;">
								<thead>
									<tr style='background-color:#B4BFC1;color:#000000;height: 35px;'>
										<td>Concepto</td>
										<td>Importe gravado</td>
									</tr>
								</thead>
								<tbody>
							</tbody>
							</table>	
						</div>		
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-danger" data-dismiss="modal">Cerrar</button>
					</div>
				</div>
			</div>
		</div>
	</body>
	</html>