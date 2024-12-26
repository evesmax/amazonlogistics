<?php
// Valida que existan reservaciones
	if (empty($_SESSION['insumos_producto'])) { ?>
		<br /><br />
		<blockquote style="font-size: 16px">
			<p>
					Seleccione un <strong>insumo</strong>
			y establezca sus respectivos <strong>parámetros</strong>.
			</p>
		</blockquote><?php

		return 0;
	} ?>

<br /><?php
// Insumos normales
if (!empty($_SESSION['insumos_producto'])) { ?>
	<table id="tabla_insumos_agregados" class="table table-striped table-bordered" cellspacing="0" width="100%">
		<thead>
			<tr>
				<!--<th align="center"><strong>Cantidad</strong></th>-->
				<!--<th><strong>Código</strong></th>-->
				<th><strong>Insumo</strong></th>
				<th><strong>Unidad</strong></th>
				<th><strong>Cant Maxima</strong></th>
				<th><strong>Cant Usada</strong></th>
				<th><strong>Cant a usar</strong></th>
				

			</tr>
		</thead>
		<tbody><?php
			foreach ($_SESSION['insumos_producto'] as $k => $v) {
			// Opciones del select
				?>
				<tr>

				<!-- Guarda los opcionales al cargar -->
					<!--<td><?php echo $v['codigo']?></td>-->
					<td><?php echo $v['nombre'] ?></td>
					<td><?php echo $v['unidad_clave']?></td>
					<td><?php echo $v['cantidadmax']?></td>
					<td><?php echo $v['usada']?></td>

				
					<td>
						<div class="input-group">
							<!--<span class="input-group-addon"  id="loader_inf_<?php echo $v['id'] ?>"><i class="fa fa-slack"></i></span>-->
							<input type="text" id="cant_req_<?php echo $v['id']?>" value="<?php echo $v['cantidad']?>" class="form-control" onkeyup="asignar_cant_req({id:<?php echo $v['id'] ?>, cantidad:$(this).val(),disp:<?php echo $v['disponible'] ?>})"/>
						</div>
					</td>
					
				</tr><?php
			} ?>

		</tbody>
	</table><?php
}
?>
