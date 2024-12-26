<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script type="text/javascript" src="js/horarios.js"></script>
	<script type="text/javascript" src="js/moment.min.js"></script>
	<link href="../../libraries/bootstrap-multiselect.css" rel="stylesheet" type="text/css"/>
    <script src="../../libraries/bootstrap-multiselect.js" type="text/javascript"></script>
	<link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
	<link rel="stylesheet" href="css/stylesheet-pure-css.css">
	<title>Horarios</title>
</head>
<body>
	<input type="hidden" name="" id="emplesele">
	<div id="exTab2" class="container well" style="width: 98%;" >
		<ul class="nav nav-tabs">
			<li class="active">
				<a href="#1" data-toggle="tab">1.Horarios</a>
			</li>
			<li><a href="#2" data-toggle="tab" onclick="">2.Asignar Horarios</a>
			</li>
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="1">
				<br>
				<div class="panel panel-default">
					<div class="panel-heading">
						<button class="btn btn-primary" onclick="newhorario()">
							<i class="fa fa-plus" aria-hidden="true"></i> Nuevo Horario
						</button>
					</div>
					<div class="panel-body">
						<div class="alert alert-info table-responsive" style="overflow-x: scroll;"> 
							<br>
							<table id="horariosalta" cellpadding="0" class="horariosalta table table-striped" style="width: 100%">
								<thead> 
									<tr style="background-color:#B4BFC1;color:#000000;">
										<td>Nombre</td>
										<td></td>	
									</tr>
								</thead>
								<tbody>
									<?php
									while($in = $horariosalta->fetch_assoc()){?>
									<tr>
										<td><?php echo $in['nombrehorario'];?></td>
										<td>
											<a href="#" class="btn btn-danger btn-sm active" onclick="accionEliminarHorario('<?php echo $in['idhorario'] ?>')"><span class="glyphicon glyphicon-remove" id="<?php echo $in['idhorario'] ?>'"></span>Eliminar</a>
											&nbsp;&nbsp;
											<a href="index.php?c=Catalogos&f=nuevohorario&editar=<?php echo $in['idhorario'] ?>" class="btn btn-primary btn-sm active"><span class="glyphicon glyphicon-edit"></span> Editar</a>
										</td>
									</tr>
									<?php } ?>	
								</tbody> 
							</table>
						</div>
					</div>
				</div>
			</div>
			<div class="tab-pane" id="2">
				<br>
				<div class="panel panel-default">
					<div class="panel-heading">
						<button type="button" class="btn btn-primary" id="asignarhrs" style="text-align:center" data-loading-text="<i class='fa fa-refresh fa-spin'</i>"><span class="glyphicon glyphicon-floppy-disk"></span> Asignar Horario</button></div>
							<div class="panel-body">
								<div class="alert alert-warning table-responsive">Asignar horario a empleado.
								</div>	
								<div class="col-md-3 form-inline">
									<label style="width:80px;">Horarios:</label>
									<select id="horario" class="btn-sm form-control selectpicker" data-live-search="true"  
									name="horario" data-width="100%">
									<option value="">Seleccione</option>
									<?php 
									while ($e = $horario->fetch_object()){
										$b = "";
										if(isset($datos)){ if($e->idhorario == $datos->idhorario){ $b="selected"; } }
										echo '<option value="'. $e->idhorario .'" '. $b .'>'. $e->nombrehorario.' </option>';}
										?>
									</select>
								</div>
								<div class="col-md-3 form-inline">
									<label for="departamento" style="text-align: center;font-size: 15px;">Departamento:</label>
									<select id="departamento" class="btn-sm form-control selectpicker" data-live-search="true" name="departamento" data-width="100%">
										<option value="no" selected>Seleccione</option>
										<option value="*">Todos</option>
										<?php 
										while ($e = $departamentos->fetch_object()){
											$b = "";
											if(isset($datos)){ if($e->idDep == $datos->idDep){ $b="selected"; } }
											echo '<option value="'. $e->idDep .'" '. $b .'>'. $e->nombre .'  </option>';
										}
										?>
									</select>
								</div>
								<div class="col-md-3 form-inline">
									<label style="width:80px;">Empleado:</label>
									<select  disabled id="empleados" class="form-control btn-sm"  multiple data-live-search="true" name="empleados" data-width="100%">
									</select>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</body>
		</html>
