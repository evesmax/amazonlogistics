<div id="imprimible" class="table-responsive" style="color: black;"> 
<?php 
if($_REQUEST['opc']==1){
	$url = explode('/modulos',$_SERVER['REQUEST_URI']);
  	if($logo == 'logo.png') $logo = 'x.png';
  	$logo = str_replace(' ', '%20', $logo); 
	echo "<img src=http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo style='width: 200px;height: 45px;'>"; 
?>
	
	<br>
	<table  cellspacing="2" cellpadding="2" width="100%" class="table table-striped table-bordered" id="table">
		<thead >
			<th colspan="3" style="background-color: #808080"><b>Emisor</b></th>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">
					Razon Social: 
					<?php  echo $org['nombreorganizacion'];?>
				</td>
				<td>RFC:<?php echo $org['RFC'] ?> </td>
			</tr>
			<tr>
				<td>Calle y Numero: <?php echo $org['domicilio'];?></td>
				<td>Municipio: <?php  echo $org['municipio'];?></td>
				<td>Colonia: <?php  echo $org['colonia'];?></td>
			</tr>
			<tr>
				<td>Estado: <?php  echo $org['estado'];?></td>
				<td>Codigo Postal: <?php  echo $org['cp'];?></td>
			</tr>
		</tbody>
	</table>
	
	<table  cellspacing="2" cellpadding="2" width="100%" class="table table-striped table-bordered" id="table">
		<thead >
			<th colspan="3" style="background-color: #808080"><b>Orden de produccion</b></th>
		</thead>
		<tbody>
			<tr>
				<td colspan="2">
					Numero de Orden: 
					<?php  echo $_REQUEST['idop'];?>
				</td>
			</tr>
			<tr>
				<td colspan="2">Producto: <?php echo strtoupper($_REQUEST['prod']);?></td>
				<td>Cantidad a producir: <?php  echo $_REQUEST['cant'];?></td>
			</tr>
		</tbody>
	</table>
	
	
<?php  } ?>
<table  cellspacing="2" cellpadding="2" width="100%" class="table table-striped table-bordered" id="table">
	<thead >
			<tr style="background-color: #808080">
			<th><b>Insumo</b></th>
			<th><b>Cantidad</b></th>
			<th><b>Unidad</b></th>
			</tr>
		
	</thead>
	<tbody>
	<?php if($insumos){ $total=0; $arrayinsu = array();
		while($in = $insumos->fetch_assoc()){ 
			$arrayinsu[ $in['clave'] ] +=$in['cant_insumo'];
	?>
			
		<tr>
			<td align="left"><?php echo $in['nombre'];?></td>
			<td align="right"><?php echo  $in['cant_insumo']; $total+=$in['cant_insumo'];?></td>
			<td align="center"><?php echo $in['clave'];?></td>
			<?php
				if($in['tipo_producto']==8){ 
					//le resto la cantidad porq si es tranformado debo ver cuanto ocupo para crear a este material
					$total-=$in['cant_insumo']; $arrayinsu[ $in['clave'] ] -=$in['cant_insumo'];
					$insu = $this->Rep_ProduccionModel->insumoMaterial($in['id_material']);
					if($insu){
						while($i = $insu->fetch_assoc()){
							//multimplico la cantidad de insumo que se ocupa para la orden, por la cantidad de su material para saber cuanto es el total de meterial para crear un insumo transformado
							 $toinsumo = $i['cantidad']*$in['cant_insumo']; 
							 $arrayinsu[ $i['clave'] ]+=$i['cantidad']; ?>
						<tr style="color:red">
							<td align="right"><?php echo $i['nombre'];?></td>
							<td align="right"><?php echo  $toinsumo; $total+=$toinsumo;?></td>
							<td align="center"><?php echo $i['clave'];?></td>
						</tr>
						<?php
						} 
					}
				}	
			?>
		</tr>
	<?php 	}
		echo "<tr style='background-color: #808080'><td colspan='2' align='right' style='font-weight: bold;'>Total de unidades</td><td></tr>";
		foreach ($arrayinsu as $key=>$val){
			echo "<tr><td colspan='2' align='right'>".$key."</td>
					<td  align='right'>".number_format($val,2,'.',',') ."</td></tr>";
		}
		echo "<tr style='font-weight: bold;background-color: #808080'><td colspan='2' align='right' >Total general de insumos:</td><td  align='right'>".number_format($total,2,'.',',') ."</td></tr>";
		
		
	}else{ ?> 
		<tr>
			<td colspan="4" align="center">No tiene insumos</td>
		</tr>
	<?php } ?>
</tbody>
</table>
</div>
