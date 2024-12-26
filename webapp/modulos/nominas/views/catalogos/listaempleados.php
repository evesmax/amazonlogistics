<!DOCTYPE>
<html>
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/modalempleados.js"></script>
	<script type="text/javascript" src="js/empleados.js"></script>
	<link   rel="stylesheet" href="css/bootstrap-datetimepicker.css">
	<script type="text/javascript" src="js/moment.min.js"></script>
	<script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>
	<script type="text/javascript" src="../../libraries/numeral.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/locale/es.js"></script>
	<link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 

	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<link   rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script> 

	<?php
	echo "<input id='fechainic'  value='$valiperioact[1]' style='display:none' /> ";
	?> 
</head>

</script>
<body>

	<input type="hidden" value="<?php echo $Nominas; ?>" name="nominas" id="nominas" />
	<!--Modal-->
	<div id="myModal"  class="modal fade" role="dialog" data-backdrop="static">
		<div class="modal-dialog "  id="mdialTamanio" style="width: 28%" > 
			<!--Modal contenido-->
			<div class="modal-content  alert-info">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal">&times;</button>
					<h5 class="modal-title "> 
					</h5>
				</div>
				<div class="modal-body"> 
					<div class="row"> 
						<div class="col-md-2"> 
							<label for="">Fecha:</label>
						</div>
						<div class="col-md-10">
							<div class='input-group date' id='fecha'>
								<input type='text' id="txtfecha" class="form-control" readonly/>
								<span class="input-group-addon">
									<span class="glyphicon glyphicon-calendar"></span>
								</span>
							</div>
						</div>
					</div>	 
					<br>
					<br>
					<div class="row">
						<div class="col-sm-12" style="text-align: right;">       
							<button type="button" class="btn btn-danger btn-sm " data-dismiss="modal"><span class="btn-group btn-group-xs fa fa-times"></span> Cancelar</button>
							<button type="button" class="btn btn-primary btn-sm" id="btnbaja" data-loading-text="<i class='fa fa-refresh fa-spin'></i>"><span class=" btn-group btn-group-xs glyphicon glyphicon-floppy-disk" style="align-content: right" disabled="false"></span> Aceptar</button>
						</div>
					</div>
				</div>
			</div> 
		</div>
	</div>

	<div class="container well table-responsive">
		<h3>Empleados</h3>
		<div class="row">
			<div class="col-md-10">
				<button class="btn btn-primary" onclick="newEmpleado();">
					<i class="fa fa-plus" aria-hidden="true"></i> Nuevo Empleado
				</button>
			</div>
			<div class="col-md-2" style="text-align: right;">
				<a type="button" class="btn btn-sm" style="background-color:#d67166;color: black;"  href="javascript:pdf();" name="pdf" id="pdf"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  
					title ="Generar reporte en PDF" border="0"> Exportar
				</a>
			</div>
		</div>
		<br>
		<div id="imprimible">
			<h2 hidden class="listadoemple" style="text-align: center;background-color:#B4BFC1;color:#000000;" border="0.1">Listado de empleados</h2>
			<table class="table table-hover table-fixed tableGrid table-bordered" style="width: 100%;" id="tableGrid" border="0.1">
				<thead>
					<tr style="background-color:#B4BFC1;color:#000000;font-weight: bold;">
						<th class="codigo">Codigo</th>
						<th class="nombreemple">Nombre</th>
						<th class="nss">N.S.S.</th>
						<th class="rfc">R.F.C.</th>
						<th class='curp'>C.U.R.P.</th>
						<th class="status">Estatus</th>
						<th class="ocultarcoll"></th>
					</tr>
				</thead>
				<body>
					<?php while ($e = $empleados->fetch_object() ){ $ok=1; $activo = "<span class='label label-success'>Activo</span>";
					if( $e->activo == 2 ){
						$ok =2; $activo = "<span class='label label-warning'>Baja</span>"; 
					}
					else if( $e->activo == 3){

						$ok =3; $activo = "<span class='label label-danger'>Reingreso</span>"; 
					}

					?>
					<tr>
						<td class="codigo"><?php echo $e->codigo; ?></td>
						<td class="nombreemple"><?php echo $e->nombreEmpleado. " " .$e->apellidoPaterno. " " .$e->apellidoMaterno ; ?></td>
						<td class="nss"><?php echo $e->nss; ?></td>
						<td class="rfc"><?php echo $e->rfc; ?></td>
						<td class="curp"><?php echo $e->curp; ?></td>
						<td class="status"><?php echo $activo; ?></td>
						<td class="ocultarcoll">
							
							<?php if($ok==1){?>
							<a href="index.php?c=Catalogos&f=empleadoview&editar=<?php echo $e->idEmpleado; ?>" class="btn btn-primary btn-xs active"><span class="glyphicon glyphicon-edit"></span> Editar</a>
							<a href="#" class="btn btn-danger btn-xs active" onclick="accionEmpleado(<?php echo $e->idEmpleado; ?>,2,<?php echo  $e->idtipop; ?>);"><span class="glyphicon glyphicon-remove"></span>Baja</a>
							<?php }else if ($ok == 2 ){?>
							<a href="#" class="btn btn-info btn-xs active" onclick="accionEmpleado(<?php echo $e->idEmpleado; ?>,3,<?php echo  $e->idtipop; ?>);"><span class="glyphicon glyphicon-check"></span> Reingreso</a>
							<!-- agregado para ver informacion de empleado sin editar -->
							<a href="index.php?c=Catalogos&f=empleadoview&editar=<?php echo $e->idEmpleado; ?>&ver=<?php echo 'baja'?>" class="btn btn-primary btn-xs active"><span class="glyphicon glyphicon-eye-open"></span> Ver</a>
							<!-- fin -->
							<?php }else if( $ok == 3){?>
							<a href="index.php?c=Catalogos&f=empleadoview&editar=<?php echo $e->idEmpleado; ?>" class="btn btn-primary btn-xs active"><span class="glyphicon glyphicon-edit"></span> Editar</a>
							<a href="#" class="btn btn-danger btn-xs active" onclick="accionEmpleado(<?php echo $e->idEmpleado; ?>,2,<?php echo  $e->idtipop; ?>);"><span class="glyphicon glyphicon-remove"></span> Baja</a>
							<?php } ?>
							<input type="hidden" value="<?php echo $e->idtipop ?>" id='idtipoperi'>
						</td>
					</tr>
					<?php } ?>
				</body>
			</table>
		</div>
	</div>

	<!-- <!GENERA PDF*************************************************-->
	<div id="divpanelpdf" class="modal fade" tabindex="-1" role="dialog">
		<div class="modal-dialog modal-sm">
			<div class="modal-content">
				<div class="modal-header">
					<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
					<h4 class="modal-title">Generar PDF</h4>
				</div>
				<form id="formpdf" action="../cont/libraries/pdf/examples/generaPDF.php" method="post" target="_blank" onsubmit="generar_pdf()">
					<div class="modal-body">
						<div class="row">
							<div class="col-md-6">
								<label>Escala (%):</label>
								<select id="cmbescala" name="cmbescala" class="form-control">
									<?php
									for($i=100; $i > 0; $i--){
										echo '<option value='. $i .'>' . $i . '</option>';
									}
									?>
								</select>
							</div>
							<div class="col-md-6">
								<label>Orientación:</label>
								<select id="cmborientacion" name="cmborientacion" class="form-control">
									<!-- <option value='P'>Vertical</option> -->
									<option value='L'>Horizontal</option>
								</select>
							</div>
						</div>
						<textarea id="contenido" name="contenido" style="display:none"></textarea>
						<input type='hidden' name='tipoDocu' value='hg'>
						<input type='hidden' value='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' name='logo' />
						<input type='hidden' name='nombreDocu' value='Detalle Nomina'>
					</div>
					<div class="modal-footer">
						<div class="row">
							<div class="col-md-6">
								<input type="submit" value="Crear PDF" autofocus class="btn btn-primary btnMenu">
							</div>
							<div class="col-md-6">
								<input type="button" value="Cancelar" onclick="cancelar_pdf()" class="btn btn-danger btnMenu">
							</div>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
	<div id="loading" style="position: absolute; top:30%; left: 50%;display:none;z-index:2;">
		<div id="divmsg" style="
		opacity:0.8;
		position:relative;
		background-color:#000;
		color:white;
		padding: 20px;
		-webkit-border-radius: 20px;
		border-radius: 10px;
		left:-50%;
		top:-200px
		">
		<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...
		</center>
	</div>
</div>
</body>
</html>