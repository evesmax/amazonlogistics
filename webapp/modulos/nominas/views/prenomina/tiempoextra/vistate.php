<table  cellspacing="2" cellpadding="2" width="100%" class="table table-striped table-bordered" id="table">
	<thead >
		<th>Num. Dias</th>
		<th>Tipo Hora</th>
		<th>Num. Horas</th>
		<th>Importe</th>
	</thead>
	<tbody>
	<?php if($te){ $min = 0;
		while($in = $te->fetch_assoc()){ 
	$tipohora = array('1' => 'Doble','2' => 'Triples','3' => 'Simples');
		$min += ($in['numhrs']*60);	 
	?>
			
		<tr class="out" onmouseout="this.className='out'" onmouseover="this.className='over'" >
			<td align="right"><?php echo $in['numdia'];?></td>
			<td align="center"><?php echo $tipohora[ $in['tipohora'] ];?></td>
			<td align="center"><?php echo $in['numhrs'];?></td>
			<td align="right"><?php echo $in['importepagado'];?></td>
		</tr>
	<?php 	}
		echo "<tr><td colspan='4' align='center'>Minutos ".$min ."</td></tr>";
	}else{ ?> 
		<tr>
			<td colspan="4" align="center">No tiene Tiempo extra</td>
		</tr>
	<?php } ?>
</tbody>
</table>