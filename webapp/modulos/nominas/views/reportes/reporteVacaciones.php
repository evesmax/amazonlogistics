<!DOCTYPE html>
<html>
<head>
	<title>R E P O R T E     D E    V A C A C I O N E S</title>
	<meta charset="UTF-8" />
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/reporteVacaciones.js"></script>
	<script type="text/javascript" src="js/moment.min.js"></script>
	<link href="../../libraries/bootstrap-multiselect.css" rel="stylesheet" type="text/css"/>
	<script src="../../libraries/bootstrap-multiselect.js" type="text/javascript"></script>

	<link rel="stylesheet" type="text/css"  href="css/reportevacaciones.css">
	<link rel="stylesheet" type="text/css"  href="css/registroentradas.css">    
	<link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script> 
	<link rel="stylesheet" href="css/stylesheet-pure-css.css">
</head>
<body>
	<div class="container-fluid ocultos" style="text-align:center;font-family: Courier;background-color:#F5F5F5;font-size: 25px;">
		<b>Reporte de Vacaciones</b>
	</div>
	<br>
	<div class="container well table-responsive" style="width: 96%;border: 0px">
		<fieldset class="scheduler-border ocultos">
			<legend class="scheduler-border" align="center">Búsqueda</legend>
			<div class="form-inline prueba">
				<div class="col-md-4">
					<label>Tipo de periodo:</label>
					<select id="idtipop" class="form-control selectpicker btn btn-sm" data-live-search="true" name="idtipop" data-width="60%" title="Seleccione" data-size="5">
						<option value="*">Todos</option>
						<?php 
						while ($e = $tipoperiodo->fetch_object()){
							$b = "";
							if($e->idtipop == $idtipop){ $b='selected="selected"'; } 
							echo '<option nombre="'.$e->nombre.'" perio="'.$e->idperiodicidad.'" idtipop="'.$idtipop .'" value="'. $e->idtipop .'" '. $b .'>'. $e->nombre .'  </option>';
						}?>
					</select> 
				</div>
				<div class="col-md-4">
					<label>Empleado:</label>
					<select id="idEmpleado" class="form-control btn btn-sm" data-live-search="true" name="idEmpleado" data-width="70%" title="Seleccione" multiple data-size="5" disabled> 
					</select> 
					<input type="hidden" id="emple" name="emple" />
				</div>
				<div class="col-md-4">
					<label>Ejercicio:</label>
					<select id="anioselec" class="form-control btn btn-sm" data-live-search="true" data-width="70%" title="Seleccione" name="anioselec" multiple data-size="5">

						<?php 
						$year = date("Y");
						for ($i=2000; $i<=$year; $i++){
							echo '<option value="'.$i.'">'.$i.'</option>';
						}?>
					</select>
					<input type="hidden" id="anios" name="anios"/>
				</div>
				<br>
				<br>
				<br>
				<div class="col-md-12" style="text-align: center;">
					<button type="button" class="btn btn-primary btn-sm" id="load" 
					data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>">Generar Reporte
				</button>
				<a type="button" class="btn btn-danger btn-sm" href="javascript:pdf();"> 
					<img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  
					title ="Generar reporte en PDF" border="0"> 
				</a>  
				<a type="button" id="impresion" class="btn btn-info btn btn-sm" href="javascript:window.print();">
					<img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0" title ="Generar reporte en imprimir.">
				</a> 
				<a type="button" class="btn btn-success btn-sm" data-toggle="modal" data-target="#myModal"><img src='images/excel.png' height="20px" width="20px" title ="Importar vacaciones en excel.">Importar</a>
			</div>
		</fieldset>  
		<div class="table-responsive">
			<table class="tablavacaciones table table-striped table-bordered table-responsive table-hover" style="width:100%;font-size: 12px;background-color: white;" border='.1px' bordercolor="#0000FF" 
			cellpadding="2" id="tablavacaciones">
			<thead> 
				<tr style="background-color:#B4BFC1;color:#000000;">
					<td>Empleado</td>
                  	<td>Fecha Inicial</td>
                  	<td>Fecha Final</td>
                  	<td>Días Tomados</td>
                  	<td>Antigüedad Años</td>
                  	<td>Dias</td>
                  	<td>Días que corresponden</td>
                  	<td>Días pendientes</td>
                  	<td>Días pendientes próximo periodo</td>
                  	<td>Año</td>
                  	<td>Fecha Alta</td>
				</tr>
			</thead>
			<tbody>
				<tr><td colspan="10" style="text-align: center;">Ningún dato disponible en esta tabla</td></tr>
			</tbody>
		</table>
	</div>
</div>


<div class="modal fade" id="myModal" role="dialog">
	<div class="modal-dialog modal-md">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal">&times;</button>
				<h4 class="modal-title" style="text-align:center;"><b>IMPORTAR VACACIONES</b></h4>
			</div>
			<div class="modal-body table-responsive">
				<form action="index.php?c=Reportes&f=vacacionexport" method="post" enctype="multipart/form-data">
					<div class="row">
						<div class="col-md-12" style="margin-bottom: 30px; ">
							<img src='images/xls_icon.gif'> 
							<a href='vacaciones.xls'>Descargar plantilla</a>
						</div>
					</div>

					<div class="row" style="margin-bottom: 30px;">
						<div class="col-md-9">
							<input type="file"  name="archivo"  id="archivo" class="btn btn-default btn-sm" required>
						</div>
						<div class="col-md-3">
							<input type="submit" value="Importar" class="btn btn-primary btn-sm" id="submit" style="width: 100%;">
						</div>
					</div>
					<div class='alert alert-danger'>
						Leer Instrucciones de la plantilla.
					</div>

				</form>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-danger btn-sm" data-dismiss="modal">Cerrar</button>
			</div>
		</div>
	</div>
</div>



<!--GENERA PDF*************************************************-->
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
<script>
	function cerrarloading(){
		$("#loading").fadeOut(0);
		var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
		$("#divmsg").html(divloading);
	}
</script> 
</body>
</html>
