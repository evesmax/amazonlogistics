<?php
// Valida que existan reservaciones
	if (empty($acciones)) { ?>
		<br /><br />
		<blockquote style="font-size: 16px">
			<p>
				Seleccione un <strong>"producto"</strong>
				y	asígnele <strong>"procesos de producción"</strong> para agregarlos.
			</p>
		</blockquote><?php

		return 0;
	}
	 ?>

<br /><?php
// Insumos normales
if (!empty($acciones )) { ?>
<div class="table-responsive">
	<input type="hidden" name="validarAcomodoGuardar" id="validarAcomodoGuardar">
	<table id="tabla_insumos_agregados2" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 11px;">
		<thead>
			<tr>
				<!--<th align="center"><strong>Cantidad</strong></th>-->
				<th align="center"><strong>ID</strong></th>
				<th align="center"><strong>Estatus</strong></th>
				<th align="center"><strong>Tipo</strong></th>
				<th align="center"><strong>Acción</strong></th>

				<th><strong>Actividad</strong></th>
				<th><strong>Hr / Pza</strong></th>
				<th><strong>Etiqueta</strong></th>
				<th><strong>Agrupacion</strong></th>
				<th><strong>Acciones</strong></th>
				<!--<th align="center"><strong>Costo Proveedor</strong></th>
				<th align="center"><strong>Costo Preparacion</strong></th>-->
			</tr>
		</thead>
		<tbody><?php
			foreach ($acciones  as $elem) {

				$exp=explode('_', $elem->idAccion);
				$texp=$exp[0];
				
				?>
				<tr id="acc_<?php echo $elem->idAccion; ?>" style="cursor: pointer;" >

				<!-- Guarda los opcionales al cargar -->
					<td align="center"><?php echo $texp; ?></td>
					<?php if($v['estatus']==1){ ?>
					<td align="center">Activo</td>
					<?php }else{ ?>
					<td align="center">Inactivo</td>
					<?php } ?>
					<?php if( $elem->tipo==1){ ?>
					<td align="center">Secuencial</td>
					<?php }else{ ?>
					<td align="center">No secuencial</td>
					<?php } ?>
					<td><?php echo $elem->nombreAccion; ?> (<?php echo $elem->alias ?>)</td>
					<?php if($elem->actividad==1){ ?>
					<td align="center">Duracion</td>
					<?php }else{ ?>
					<td align="center">Piezas</td>
					<?php } ?>
					<td align="center"><?php echo $elem->alias_hr; ?></td>
					<?php if($elem->eti !=''){ 
						$td_etiqueta='S/N';
						foreach ($etiquetas as $ke => $ve) {
							if($ve['id']==$elem->eti){
								$td_etiqueta=$ve['nombre_etiqueta'];
							}
						}
					?>
					<td align="center"><?php echo $td_etiqueta; ?></td>
					<?php }else{ ?>
					<td align="center">&nbsp;</td>
					<?php } ?>

					<?php if($elem->agru !=''){ 
						$td_agrupacion='S/N';
						foreach ($agrupados as $ke2 => $ve2) {
							if($ve2['id']==$elem->agru){
								$td_agrupacion=$ve2['nombre_agrupacion'];
							}
						}
					?>
					<td align="center"><?php echo $td_agrupacion; ?></td>
					<?php }else{ ?>
					<td align="center">&nbsp;</td>
					<?php } ?>
					<td><button onclick="recetas.removerAccion('<?php echo $paso ?>','<?php echo $elem->idAccion; ?>');" class="btn btn-danger btn-sm"><span class="glyphicon glyphicon-remove"></span> Remover</button></td>
				</tr><?php
			} ?>
		</tbody>
	</table>
</div>
	
	<?php
}

// Insumos preparados
?>