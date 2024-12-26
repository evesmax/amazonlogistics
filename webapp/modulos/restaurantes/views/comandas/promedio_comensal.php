<div class="panel-default">
	<div class="panel-body"><?php
	
	// Valida que existan promedios en las fechas seleccionadas
		if (empty($promedios)) {?>
			<div align="center">
				<h3><span class="label label-default">* No se detecto informacion *</span></h3>
			</div><?php
			
			return 0;
		} ?>
		
		<table class="table table-bordered table-striped">
			<tr>
				<td><strong>Comanda</strong></td>
				<td><strong>Comensales</strong></td>
				<td><strong>Fecha / Hora</strong></td>
				<td><strong>Empleado</strong></td>
				<td><strong>Promedio Comanda</strong></td>
			</tr><?php
			
		// $promedios es un array con los promedios por comensal que viene desde el controlador
			foreach ($promedios as $key => $value) { 
				$promedio_general+=$value['promedioComensal'];
				$num_comandas++; ?>
				<tr>
					<td><?php echo $value['id'] ?></td>
					<td><?php echo $value['personas'] ?></td>
					<td><?php echo $value['timestamp'] ?></td>
					<td><?php echo $value['idempleado'] ?></td>
					<td>$ <?php echo $value['promedioComensal'] ?></td>
				</tr> <?php
			} 
			
			$promedio_general/=$num_comandas;
			?>
			
			<tr>
				<td colspan="4" align="right">
					<strong>Promedio General:</strong>
				</td>
				<td colspan="4" align="left">
					<strong>$ <?php echo $promedio_general ?></strong>
				</td>
			</tr>
		</table>
	</div>
</div>