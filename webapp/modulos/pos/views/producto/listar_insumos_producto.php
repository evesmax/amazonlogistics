<?php
// Valida que existan reservaciones
	if (empty($_SESSION['insumos_producto'])) { ?>
		<br /><br />
		<blockquote style="font-size: 16px">
			<p>
					Seleccione un <strong>producto</strong>
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
				<th><strong>Código</strong></th>
				<th><strong>Insumo</strong></th>
				<th><strong>Cant. Req.</strong></th>
				<th><strong>Agrupador</strong></th>
				<th><strong>Unidad</strong></th>

			</tr>
		</thead>
		<tbody><?php
			foreach ($_SESSION['insumos_producto'] as $k => $v) {
			// Opciones del select
				?>
				<tr>

				<!-- Guarda los opcionales al cargar -->
					<td><?php echo $v['codigo']?></td>
					<td><?php echo $v['nombre'] ?></td>
					<td>
						<div class="input-group">
							<span class="input-group-addon"  id="loader_inf_<?php echo $v['id'] ?>"><i class="fa fa-slack"></i></span>
							<input type="text" id="cant_req_<?php echo $v['id']?>" value="<?php echo $v['cantidad'] ?>" class="form-control" onkeyup="asignar_cant_req({id:<?php echo $v['id'] ?>, cantidad:$(this).val()})"/>
						</div>
					<td>
						<select id="selid_<?php echo $v['id']; ?>" class="form-control" onchange="asignar_agru_req({id:<?php echo $v['id'] ?>});">
							<?php if ($v['agrupador']!=0){ ?>
							<option value="0">No</option>
							<option selected="selected" value="<?php echo $v['agrupador'] ?>"><?php echo $v['agrupadornom'] ?></option>
							<?php }else{ ?> 
							<option value="0" selected="selected">No</option>
							<?php } ?>
							<?php if ($agrupas['total']>0){ 
								foreach ($agrupas['rows'] as $kk => $vv) { ?>
								    <?php if($v['agrupador']==$vv['id']){ continue; } ?>
									<option value="<?php echo $vv['id']; ?>"><?php echo $vv['nombre']; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</td>
					</td>
					<td><?php echo $v['unidad_clave']?></td>
				</tr><?php
			} ?>

		</tbody>
	</table><?php
}
?>
<script>
$(function(){
    $('#tabla_insumos_length select').val('10').trigger('change');
});


</script>
