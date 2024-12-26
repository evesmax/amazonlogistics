<!DOCTYPE html>
<head>
	<meta charset="utf-8">
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<link   rel="stylesheet" href="css/bootstrap-datetimepicker.css">
	<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
	<script type="text/javascript" src="js/moment.min.js"></script>
	<script type="text/javascript" src="js/registroincidencias.js"></script>
	<script type="text/javascript" src="js/bootstrap-datetimepicker.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.10.6/locale/es.js"></script>
	<link   rel="stylesheet" type="text/css" href="css/prenomina.css" /> 
	<link   rel="stylesheet" type="text/css" href="css/estilomodal.css" />
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css"> 
	<link   rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script> 
</head>  
</head> 
<body> 
	<div>
		<form class="form-inline" id="forminci" action="ajax.php?c=registroincidencias&f=almacenaincidencia" method="post" >
			<!-- Modal -->
			<div class="modal fade" id="myModal" role="dialog" data-backdrop="static">
				<div class="modal-dialog">
					<!-- Contenido del modal-->
					<div class="modal-content" style="padding:20px;">
						<!-- <div class="panel-body modal-header"> -->
							<div class="modal-header-success alert-info encabemodal" >
								<h4 class="modal-title" style="text-align: left;height:30px;margin-left:5px;margin-top: 3px;font-size: 16px;">
									Selección de días y horas
									<button type="button" class="close panelTitleTxt glyphicon glyphicon-remove" data-dismiss="modal" id="btnCerrar" aria-hidden="true" style="margin-top: 2.5px;margin-right:5px;";></button>
								</h4>
								<!-- </div> -->
							</div> 
							<br>
							<br>
							<!-- Calendario icono -->
							<div class="row">
								<div class="col-md-2">
									<img src="images/calendario.png" style="width:75px;height: 75px">
								</div>
								<div class="col-md-10">
									<div class="row">
										<div class="col-md-12">
											<h>Seleccione la(s) incidencia(s) y capture su valor respectivo.</h6>
											</div>
										</div>
										<br>
										<div class="row">
											<div class="col-md-12">
												<label>Selección</label>
												<input type="text" style="height: 25px" class="form-control" name="seleccion" 
												id="seleccion" disabled value="">
											</div> 
										</div>
									</div>
								</div>
								<br>  
								<!--</div>-->
								<input type="hidden" name="clave" id="clave">
								<input type="hidden" name="idIncidencia" id="idtipoincidencia">
								<input type="hidden" name="fecha" id="fecha">
								<input type="hidden" name="idnomp" id="idnomp">
								<input type="hidden" name="idempleado" id="idempleado">
								<input type="hidden" name="hdnfechainicio" id="hdnfechainicio">
								<input type="hidden" name="hdneditable" id="hdneditable">
								<input type="hidden" name="hdnfechafin" id="hdnfechafin">

								<!-- <div class="row alert-info" style="padding:15px;background-color:#EEE;margin-top:1.3em;">  -->
									<div class="" width="100%"><FONT SIZE=3 COLOR="black"><b>Tipos de incidencia</b></font></div> 
										<div class="table-responsive alert alert-info">
											<table class="table table-sm table table-fixed table-hover table-bordered" width="100%">
												<thead style="background-color: rgb(110,110,110);color: #FFFFFF;" title="Deslizar para ver mas...">
													<tr>
														<th align="center">CLAVE</th>
														<th align="left" width="200px">DESCRIPCIÓN</th>
														<th align="left">TIPO</th>
														<th align="left">VALOR</th>
													</tr>
												</thead>
												<tbody id="tablainci">
												</tbody>
											</table>   
										</div>
										<br>
										<div class="row">
											<div class="col-md-5 col-md-offset-7" style="margin-right: 3em">               
												<button type="button" class="btn btn-danger" data-dismiss="modal"><span class="fa fa-times"></span> Cancelar</button>
												<button type="button" class="btn btn-primary" id="load" data-loading-text="<i class='fa fa-refresh fa-spin'></i>"><span class="glyphicon glyphicon-floppy-disk" style="align-content: right" disabled="false"></span> Aceptar</button>
											</div>
										</div>
										<br>
									</div>
								</div>    
							</div>
						</div>
						<!--TERMINA MODAL-->


						<div class="container well table-responsive" style="width: 95%;">
							<h3 align="center" class="alert">Registro de Incidencias</h3> 
							<div class="panel-group">
								<div class="panel panel-default">
									<!-- <div class="panel-heading">Selecciona una nomina</div> -->
									<div class="panel-body">
										<div class="alert alert-warning" style="height: auto;">
											<div class="row">



												<div class="col-md-5">
													<b>PERIODO</b> 			
													<select id="periodonom" class="selectpicker form-control" data-width="70%" data-live-search="true" onchange="cambiaperiodo(this.value)">
														<?php
														while($p = $periodos->fetch_object()){
															if($idtipop->idtipop == $p->idtipop){ $se = "selected";}else{ $se="";}?>
															<option value="<?php echo $p->idtipop;?>" <?php echo $se;?>><?php echo $p->nombre; ?></option>
															<?php } ?> 
														</select>
													</div>
													<div class="col-md-5">
														<select id ="selectPeriodo" class="form-control selectpicker" data-width="70%" data-live-search="true" onchange="traerFechas()" title='Seleccione...'>
															<?php while ($e = $nominasPeriodo->fetch_object()){ 
																echo"
																<option idnomp='$e->idnomp' fechainicio='$e->fechainicio' fechafin='$e->fechafin' autorizado='$e->autorizado' editable='$e->editable'  class='$e->clasedeperiodo'>
																$e->numnomina".".-"."($e->fechainicio - $e->fechafin)
																</option>";
															} ?>
														</select>
													</div>
													<div class="col-md-2">
														<button type="button" class="btn btn-primary form-control" data-toggle='modal' data-target='#ModalAgregarInci'>
															<span class="fa fa-pencil-square-o fa-5x"></span> Agregar día festivo
														</button>
													</div>
												</div>
											</div>
											<div class="col-md-12 alert alert-info table-responsive">
												<table id="tablaincidencias" cellpadding="0" class="table table-striped table-over table-bordered"  width="100%">
													<thead class="" title="Deslizar para ver mas..." >
														<tr style="background: #6E6E6E; color: #F5F7F0" align="center">
															<td  align="center" id="tdp" colspan="16" style="background:#6E6E6E; color: #F5F7F0;border:solid 0px;";>
																<b id="periodo" style="font-size:14px;width:100%";>PERIODO</b>
															</td>
														</tr>
														<tr style="border:solid 0px;background:#6E6E6E; color:#F5F7F0;font-size: 12px;" id="trHeader">
															<th><b>CÓDIGO EMPLEADO</b></th>
															<th><b>NOMBRE EMPLEADO</b></th>
														</tr>
													</thead>
													<tbody id="contenidop" style="font-size: 12px;">
														<!--'contenidop' trae la informacion de los empleados-->
													</tbody> 
												</table>
											</div>
										</div> <!--div de panel-body-->
									</div>
								</div>
							</div>

							<!--Pantalla principal-->
							<input type="hidden" value="<?php echo $nominaActual["rows"][0]["idnomp"];?>" id="idnominaact">
							<input type="hidden" id="fini" value="<?php echo $fi;?>">
							<input type="hidden" id="ffinal" value="<?php echo $ff;?>">
							<input type="hidden" id="nominaseleccionada">
						</div>
					</form>
				</div>


				<div id="ModalAgregarInci" class="modal fade" role="dialog">
					<div class="modal-dialog">
						<!-- Modal content-->
						<div class="modal-content">
							<div class="modal-header">
								<h4 class="modal-title" style="text-align: center;height:30px;margin-left:5px;margin-top: 3px;font-size: 16px;">
									Registro de día festivo.
									<button type="button" class="close panelTitleTxt glyphicon glyphicon-remove" data-dismiss="modal" id="btnCerrar" aria-hidden="true" style="margin-top: 2.5px;margin-right:5px;";></button>
								</h4>
							</div>
							<div class="modal-body">
								<div class="alert alert-danger">
									<i class="fa fa-info-circle fa-lg"></i> 
									INFORMACIÓN.<br>
									Día festivo será aplicado a los empleados del periodo activo.
									<br>
									Si desea agregar día festivo a un periodo diferente al actual debe cambiarlo en 
									<a href="" title="Ir a Configuracion" onclick="irConfiguracion()"><b>Configuracion</b></a>   
								</div>

								<div class="alert alert-info" style="height: 200px;"><br>
									<h4 style="text-align:center">Periodo de <?php echo $fi; ?> al <?php echo $ff; ?></h4>
									<br>
									<div class="row">
										<div class="col-md-3">
											<h4>Día Festivo:</h4>
										</div>
										<div class="col-md-9">
											<div class='input-group date' id='fechadescanso'>
												<input type='text' name="txtfecha" id="txtfecha" class="form-control"  value=<?php echo $fi;?>>
												<span class="input-group-addon">
													<span class="glyphicon glyphicon-calendar"></span>
												</span>
											</div> 
										</div>
									</div>
								</div>
							</div>
							<div class="modal-footer">
								<button type="button" class="btn btn-default btn-sm" data-dismiss="modal">Cerrar</button>
								<button type="button" class="btn btn-primary btn-sm" id="almacdiafest" style="text-align:center" data-loading-text="<i class='fa fa-refresh fa-spin'</i>">Guardar día</button>
								</div>
							</div>
						</div>
					</div><!--MODAL DOS -->
					<div id="menu" style="display: none">
						<ul>
							<li id="sobre" >Abrir Sobre-Recibo</li>
							<li id="emple" >Ver empleado</li>
						</ul>
					</div>
				</body>
				<script type="text/javascript">
					var table = $('#tablaincidencias').DataTable();
					table.destroy();
					setTimeout(function(){
						$('#tablaincidencias').DataTable( {
							"language": {
								"url": "js/Spanish.json"
							}
						})
					}, 1000);

				</script>
				</html>
