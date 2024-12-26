<?php
$cargarexistentes = $this -> recetasModel -> cargarexistentes();
$cargaresinflujo  = $this -> recetasModel -> cargaresinflujo();
?>

<script src="../../libraries/bootstrap-select-1.9.3/dist/js/bootstrap-select.min.js"></script>
<script src="../../libraries/select2/dist/js/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />

<script type="text/javascript">
	$(document).ready(function() {

		$('#inicial').select2(); 
		$('#final').select2(); 
	});

</script>

<!DOCTYPE html>
<html>
<head>
</head>
<body>
	<div class="container" style="width: 90%;">
		<form class="table-responsive">
			<div class="panel panel-warning table-responsive">
				<div class="panel-heading"><strong>Copiar los pasos existentes de otro producto.</strong></div>
				<div class="panel-body table-responsive">
					<div class="row">
						<div class="col-md-12 col-sm-12">
							<div class="col-md-5 form-inline">
								<label>Copiar A:</label>
								<div class="input-group">
									<span class="input-group-addon">
										<span class="glyphicon glyphicon-saved"></span> 
									</span>
									<select id="inicial" class="btn-sm form-control" data-live-search="true" name="inicial"  data-width="200px">
										<option disabled selected>Seleccione</option>
										<?php 
										while ($e = $cargarexistentes->fetch_object()){
											echo '<option value="'.$e->id_producto.'">'.$e->nombre .' </option>'; }?> 
										</select>
									</div>
								</div>
								<div class="col-md-2">
									<button id="guardarcopia" type="button" class="btn btn-success btn-lg" data-loading-text="<i class='fa fa-refresh fa-spin'></i>">
										<i class="fa fa-check"></i>Copiar
									</button>
								</div>
								<div class="col-md-5 form-inline">
									<label>Copiar A:</label>
									<div class="input-group">
										<span class="input-group-addon">
											<span class="glyphicon glyphicon-saved"></span> 
										</span>
										<select id="final" class="btn-sm form-control" data-live-search="true" name="final"  data-width="200px">
											<option disabled selected>Seleccione</option>
											<?php 
											while ($e = $cargaresinflujo->fetch_object()){
												echo '<option value="'.$e->idProducto.'">'. $e->nombre .' </option>'; }?> 
											</select>
										</div>
									</div>
								</div>
							</div>
						</div>
					</div>
				</div>
			</form>
		</div>
	</body>
	</html>