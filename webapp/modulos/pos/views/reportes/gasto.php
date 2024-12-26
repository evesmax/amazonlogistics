<!-- jQuery -->
<script src="../../libraries/jquery.min.js"></script>
<!-- Bootstrap -->
<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
<!--Select 2 -->
<script src="../../libraries/select2/dist/js/select2.min.js"></script>
<link rel="stylesheet" type="text/css" href="../../libraries/select2/dist/css/select2.min.css" />
<style>
.repo{
	background-color: #FAFAFA;
	padding:15px;
	border:2px solid #F1F1F1;
}
</style>
<div class="container well">
	<div class="row">
		<div class="col-sm-12">
			<div class="col-sm-12">
			<h3>Resumen de Vuelos</h3>
			</div>
		</div>
	</div>
	<div class="row repo">
		<div class="col-sm-12 col-md-3">
			<div class="col-sm-12"><b>Clientes</b></div>
			<div class="col-sm-12">
				<select id='clientes' class='form-control'>
					<?php
						while($c = $clientes->fetch_object())
							echo "<option value='$c->id'>($c->codigo) $c->nombre</option>";
					?>
				</select>
			</div>
		</div>
		<div class="col-sm-12 col-md-3">
			<div class="col-sm-12"><b>Rango</b></div>
			<div class="col-sm-12">
				<input type='text' id='fechas' class='form-control fechas_izq' value=''>
			</div>
		</div>
		<div class="col-sm-12 col-md-2">
			<div class="col-sm-12"><b>Numero de Vuelo</b></div>
			<div class="col-sm-12">
				<input type='text' id='no_vuelo' class='form-control' value=''>
			</div>
		</div>
		<div class="col-sm-12 col-md-2">
			<div class="col-sm-12"><b>Matricula</b></div>
			<div class="col-sm-12">
				<select id='matricula' class='form-control'>
					<option value='0'>Todas</option>
					<?php
						while($m = $matriculas->fetch_object())
							echo "<option value='$m->id'>($m->aeronave) $m->tipo</option>";
					?>
				</select>
			</div>
			<!--<div class="col-sm-12"><b>Forma de pago</b></div>
			<div class="col-sm-12">
				<select id='formasPago' class='form-control'>
					<?php
						//while($f = $formasPago->fetch_object())
						//	echo "<option value='$f->id'>($f->clave) $f->nombre</option>";
					?>
				</select>
			</div>-->
		</div>
		<div class="col-sm-12 col-md-1">
			<div class="col-sm-12">&nbsp;</div>
			<div class="col-sm-12">
				<button class='btn btn-default' onclick='generar()'>Buscar</button>
			</div>
		</div>
	</div>
</div>
<div class="container well" id='resultados'>
	<div class='row repo' id='datos'>
	</div>
</div>


<script type="text/javascript" src="//cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script type="text/javascript" src="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="//cdn.jsdelivr.net/bootstrap.daterangepicker/2/daterangepicker.css" />
<script src="js/gasto.js"></script>
