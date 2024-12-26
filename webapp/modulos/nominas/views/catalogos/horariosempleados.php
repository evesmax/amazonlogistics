<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="../../libraries/jquery.min2.js"></script>
	<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script> -->
	<script type="text/javascript" src="js/horariosempleados.js"></script>
	<script type="text/javascript" src="../../libraries/bootstrap.min.js"></script>
	<link href="../../libraries/bootstrap-multiselect.css" rel="stylesheet" type="text/css"/>
	<script src="../../libraries/bootstrap-multiselect.js" type="text/javascript"></script>
	<!-- <link rel="stylesheet" href="css/stylesheet-pure-css.css"> -->
</head>
<body>
	<div class="container-fluid" style="text-align:center;font-family:Courier;background-color:#F5F5F5;font-size:25px;">
		<b>Horarios de Empleados</b>
	</div>
	<br>
	<div class="container well" style="width: 96%;">
		<div class="panel panel-default">
			<div class="panel-heading">Seleccione los empleados que asignará el horario.</div>
			<div class="panel-body"><section class="col-md-4">
				<label style="width:80px;">Empleado:</label>
				<div class="form-inline" style="text-align: right;">
					<select id="empleados" class="form-control btn-sm"  multiple="multiple" data-live-search="true" name="empleados" data-width="100%">
						<?php 
						while ($e = $empleados->fetch_object()){
							$b = "";
							if(isset($datos)){ if($e->idEmpleado == $datos->idEmpleado){ $b="selected"; } }
							echo '<option value="'. $e->idEmpleado .'" '. $b .'>'. $e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.' </option>';}
							?>
						</select>
					</div>
				</section>
				<section class="col-md-8">
					<div class="table-responsive alert alert-info"><br>
						<table class="table table-sm table table-fixed table-hover table-bordered">
							<thead>
								<tr style="background-color: rgb(110,110,110);color: #FFFFFF;">
									<th>Hora entrada</th>
									<th>Horas de comida</th>
									<th>Hora salida</th>
									<th>Tolerancia</th>
								</tr>
							</thead>
							<tbody>
								<tr> 
									<td><input type="time" name="horaentra"    id="horaentra"   max="23:30:00"  step="1" class="input form-control" /></td>
									<td><input type="number"  name="horacomida" id="horacomida"  class="input form-control"/></td>
									<td><input type="time"   name="horasalida" id="horasalida"  max="23:30:00"  step="1" class="input form-control"/></td>
									<td><input type="number"  name="tolerancia" id="tolerancia"  class="input form-control"/></td>
								</tr>
							</tbody>
						</table><br>
					</div>
					<div style="text-align: right;">
						<button type="button" class="btn btn-primary btn-sm" id="almacenhrs" style="text-align:center" data-loading-text="<i class='fa fa-refresh fa-spin'</i>">Asignar</button>
						</div>
					</section>
				</div>
			</div>
		</div>
	</body>
	</html>