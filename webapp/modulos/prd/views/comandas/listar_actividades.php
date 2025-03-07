<?php
// Valida que existan actividades
	if (empty($actividades)) {?>
		<div align="center">
			<h3><span class="label label-default">* No se detecto informacion *</span></h3>
		</div><?php
		
		return 0;
	} ?>

<div class="row">
	<div id="grafica_actividades_dona" class="col-xs-4" style="height: 40%">
		<!-- En esta div se carga la grafica de dona -->
	</div>
	<div id="grafica_actividades_barras" class="col-xs-8" style="height: 40%">
		<!-- En esta div se carga la grafica de barras -->
	</div>
</div>
<table id="tabla_actividades" class="table table-striped table-bordered" cellspacing="0" width="100%">
	<thead>
		<tr>
			<th><strong>Empleado</strong></th>
			<th><strong>Sucursal</strong></th>
			<th><strong>Accion</strong></th>
			<th><strong>Descripcion</strong></th>
			<th align="center"><strong>Fecha / Hora</strong></th>
		</tr>
	</thead>
	<tbody><?php
	// $actividades es un array con las comandas viene desde el controlador
		foreach ($actividades as $key => $value) { ?>
			<tr>
				<td><?php echo $value['empleado'] ?></td>
				<td><?php echo $value['sucursal'] ?></td>
				<td><?php echo $value['accion'] ?></td>
				<td><?php echo $value['descripcion'] ?></td>
				<td align="center"><?php echo $value['fecha'] ?></td>
			</tr> <?php
		} ?>
	</tbody>
</table>
<script>
	comandas.graficar({div:'grafica_actividades',x:'empleado',y:'actividades',label:'Actividades',dona:<?php echo json_encode($dona) ?>,barras:<?php echo json_encode($barras) ?>});
</script>