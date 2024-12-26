<?php
// Valida que existan reservaciones
if (empty($_SESSION['produccion_acciones'])) { ?>
<br /><br />
<blockquote style="font-size: 16px">
	<p>
		Seleccione un <strong>"Ciclo"</strong>
		y	asígnele <strong>"procesos de producción"</strong> para agregarlos.
	</p>
	</blockquote><?php

	return 0;
} ?>


<br /><?php


// Insumos normales
if (!empty($_SESSION['produccion_acciones'])) { ?>
<table id="tabla_insumos_agregados" class="table table-striped table-bordered" cellspacing="0" width="100%" style="font-size: 12px;">
	<thead>
		<tr style="background-color: #DFF0D8;">


			<th align="center"></th>

			<th align="center"><strong>Acción</strong></th>

			<th align="center"><strong>Alias</strong></th>

		</tr>
	</thead>
	<tbody><?php

	$p=0;
	$rdata=explode('#.#.#', $alias);


	foreach ($_SESSION['produccion_acciones'] as $k => $v) {
		if($alias!=''){
			if($rdata[$p]!=''){
				$rexp=explode('#.,#', $rdata[$p]);
				$rid=$v['id'];
				$rstatus=$rexp[1];
				$rtipo=$rexp[2];
				$ralias=$rexp[3];
				$ract=$rexp[4];
// $rhr=$rexp[5];
// $eti=$rexp[6];
// $agru=$rexp[7];
			}else{
				$rid=$v['id'];;
				$rstatus=1;
				$rtipo='';
				$ralias=$v['nombre'];
				$ract=1;
// $rhr='';
// $eti='';
// $agruxx='';
			}
		}else{
			$rid=$v['id'];
			$rstatus=1;
			$rtipo='';
			$ralias=$v['nombre'];
// $ract=1;
// $rhr='';
// $eti='';
// $agru='';
		}



		$exp=explode('_', $rid);
		$texp=$exp[0];

		$p++;

		?>
		<tr id="<?php echo $rid; ?>"  style="cursor: pointer;">
			<!-- Guarda los opcionales al cargar -->
			<td align="center" class="leerdato"><?php echo $texp ?></td>
			<td ><?php echo $v['nombre'] ?></td>
			<td><input class="form-control" id="alias" style="width: 100%;" type="text" value="<?php echo $ralias ?>" /></td>
		</tr>
		<?php 
	} ?>

</tbody>
</table>
<?php
}

?>
<script>

</script>