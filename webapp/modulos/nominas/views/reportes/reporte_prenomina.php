<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<link   rel="stylesheet" type="text/css" href="css/reporteprenomina.css"> 
	<link   rel="stylesheet" type="text/css" href="css/registroentradas.css"> 
	<script type="text/javascript" src="js/reporteprenomina.js"></script>
    <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
	<link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
	<link   rel="stylesheet" href="css/stylesheet-pure-css.css">
	<title>Horarios</title>
</head>
	<script type="text/javascript">
		$(document).ready(function(){
			if ($("#mostrarvisual").prop("checked")){
				$('select[name*="idtipop"] option[period="11"]').hide();
			}else{
				$('select[name*="idtipop"] option[period="11"]').show();
			}
		});
	</script>
	<body>
		<div class="container-fluid encabezado ocultarfiltros" style="text-align:center;font-family: Courier;background-color:#F5F5F5;font-size: 25px;">
			<b>Reporte de Prenomina</b>
		</div>
		<br>
		<div class="col-md-12" align="center">
			<fieldset class="ocultarfiltros scheduler-border" style="font-size: 13px;">
				<legend class="scheduler-border" align="center">Búsqueda</legend>
				<div class="form-inline">
					<input type="checkbox" name="checkboxG4" id="mostrarvisual" class="css-checkbox"  checked/>
					<label for="mostrarvisual" class="css-label" onclick="activarChecked()" style="font-size: 13px;">VISUAL</label>
					<input type="checkbox" name="mostrarimprimir" id="mostrarimprimir" class="css-checkbox"/>
					<label for="mostrarimprimir" onclick="activarCheckeddos()" class="mostrarimprimir css-label" style="margin-left: 20px;margin-right: 20px;font-size: 13px;">IMPRIMIR RECIBOS</label>
					<input type="checkbox" value="1" name="mostrarrangos" id="mostrarrangos" class="css-checkbox"/>
					<label for="mostrarrangos" onclick="activarCheckedtres()" class="mostrarrangos css-label" style="margin-left: 20px;margin-right: 20px;font-size: 13px;">RANGO DE CODIGO</label>
				</div>
				<br>
				<br>
				<br>
				<div class="container-fluid" align="center">
					<form method="post" action="index.php?c=Reportes&f=reporteSobrerecibo" id="formSobrerecibo">
						<div class="row">
							<div class="form-inline"> 
								
								<div class="col-md-3">
									<label>Periodo</label>	
									<select id="idtipop" class="form-control selectpicker btn-xs" data-live-search="true" name="idtipop" data-width="70%" title="Seleccione">
										<option value="*">Todos</option>
										<?php 
										while ($e = $tipoperiodo->fetch_object()){
											$b = "";
											if(isset($datos)){ if($e->idtipop == $datos->idtipop){ $b="selected"; } }
											   echo '<option nombre="'.$e->nombre.'" period="'.$e->idperiodicidad.'" value="'. $e->idtipop .'" '. $b .'>'. $e->nombre .'  </option>';
										}
										?>
									</select>
								</div>
								
								<div class="col-md-3">
									<label>Nomina</label>
									<select id="idnomp" class="idnomp form-control selectpicker btn-xs" data-live-search="true" 
									name="idnomp" data-width="70%" title="Seleccione">
									</select>
										<input type="hidden" id="nomi" name="nomi"/>
										<input type="hidden" id="nomidos" name="nomidos"/>
								</div>
								<div class="col-md-2 extracheck" hidden>
									<label class="extracheck" hidden>Origen</label>
									
									<select id="origen" class="extracheck form-control selectpicker btn-xs" data-live-search="true" name="origen" data-width="70%" title="Seleccione">
											<option  value="*">Todos</option>
											<option  value="1">Aguinaldo</option>
											<option  value="2">Finiquito</option>
											<option  value="3">Ptu</option>
										</select> 
								</div>
								<div class="col-md-3 empleadocheck">
									<label class="empleadocheck">Empleado</label>
									<select id="empleado" class="empleado form-control selectpicker btn-xs" data-live-search="true" name="empleado" onchange="datosEmp()" data-width="70%" title="Seleccione">
											<option value="*">Todos</option>
											<?php 
											while ($e = $empleadosdos->fetch_object()){
												$b = "";
												if(isset($datos)){ if($e->idEmpleado == $datos->idEmpleado){ $b="selected"; } }
												echo '<option value="'. $e->idEmpleado .'" '. $b .'>'.$e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.' </option>';;
											}
											?>
										</select>	
								</div>

								<div class="col-md-2 rangoempleado">
									 <label  hidden class="rangoempleado">De</label>
										<select id="codigouno" class="form-control selectpicker btn-xs" data-live-search="true" name="codigouno" data-width="70%" title="Seleccione">
											<?php 
											while ($e = $codigo->fetch_object()){
												$b = "";
												if(isset($datos)){ if($e->idEmpleado == $datos->idEmpleado){ $b="selected"; } }
												echo '<option value="'. $e->idEmpleado .'" '. $b .'>'. $e->codigo .'  </option>';
											}
											?>
										</select> 
								</div>
								<div class="col-md-2 rangoempleado">
									<label class="rangoempleado" hidden="">Al</label>
									<select id="codigodos" class="form-control selectpicker btn-xs rangoempleado " data-live-search="true" name="codigodos"  hidden="true" data-width="70%" title="Seleccione">
										</select> 
								</div>
							        <br> 
									<br>
									<br>
									<br>
									<div class="col-md-12" style="text-align: center;">
									<button type="button" class="btn btn-primary btn-sm" id="load" style="text-align:center" data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>">Generar Reporte</button>
									<a type="button" id="impresion" class="btn btn-info btn btn-sm" 
									href="javascript:window.print();" hidden="true" onclick="printl()"  title="Generar recibo con firma">
									<img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0" ></a> 
									&ensp;
									<a type="button" id="listaRaya" class="btn btn-sm" 
									href="javascript:window.print();" hidden="true" onclick="listaRaya()" style="background-color: #6bb36b;color: black" title="Generar recibo sin firma">
									<img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0"> Lista de Raya</a> 
									&ensp;
									<a type="button" class="btn btn-sm" style="background-color:#d67166"  href="javascript:pdf();" name="pdf" id="pdf"> 
									<img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  title ="Generar reporte en PDF" border="0" > 
									</a>
									</div>	
								</div>  
							</div>
						</div>
					</fieldset>
					<div id="imprimible">
						<div id="divVisual">
						</div>
					</div>
				</div>
			</div>
		</form>
		<div id="contPerce" style="text-align: center">
			<div id="imprimir" class="imprimir" style="text-align: center">
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
	<script>
		function cerrarloading(){
			$("#loading").fadeOut(0);
			var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
			$("#divmsg").html(divloading);
		}
	</script>
</body>
</html>