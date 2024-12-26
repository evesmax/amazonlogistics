<table  cellspacing="2" cellpadding="2" width="100%" class="table table-striped table-bordered" id="table">
	<thead >
			<tr style="background-color: #808080">
			<th><b>Insumo</b></th>
			<th><b>Cantidad</b></th>
			<th><b>Unidad</b></th>
			<th><b>Existencia</b></th>
			<th><b>Accion</b></th>
			</tr>
		
	</thead>
	<tbody>
	<?php  
	if($insumos){ $total=0; $arrayinsu = array();
		while($in = $insumos->fetch_assoc()){ 
			$arrayinsu[ $in['clave'] ] +=$in['cantidad'];
			$existencia = $existencias[ $in['id_insumo'] ];
			//if($existencia>0){}else{$existencia=0; }
	?>
			
		<tr> 
			<td align="left"><?php echo $in['nombre'];?></td>
			<td align="right"><?php echo  $in['cantidad']; $total+=$in['cantidad'];?></td>
			<td align="center"><?php echo $in['clave'];?></td>
			<td align="center"><?php echo $existencia;?></td>
			<td align="center">
				<?php 
				//si la opc=1 osea q es para autorizar una orden q le faltan
				//y estatus para q aparesca solo cuando no este autorizado
				if($_REQUEST['opc']==1 && $in['estatus']==1 && $existencia>0){
					if(in_array('29', $_SESSION['accelog_acciones'])){
					?>
						<button class="btn btn-info btn-xs" id="btn<?php echo $in['id_insumo'] ;?>" onclick="autorizarInsumo(<?php echo $in['id_insumo'] ;?>,<?php echo $_REQUEST['idop'];?>);"><span class="glyphicon glyphicon-edit"></span>Autorizar Insumo</button>
				<?php }
				}else if($existencia<=0){?>
					<span class="label label-warning" style="cursor:pointer;">Sin existencia no puede autorizar</span>
				<?php }else{?>
					<span class="label label-success" style="cursor:pointer;">Autorizado</span>
				<?php }?>
			</td>
		</tr>
	<?php 	}
		echo "<tr style='background-color: #808080'><td colspan='4' align='right' style='font-weight: bold;'>Total de unidades</td><td></tr>";
		foreach ($arrayinsu as $key=>$val){
			echo "<tr><td colspan='4' align='right'>".$key."</td>
					<td  align='right'>".number_format($val,2,'.',',') ."</td></tr>";
		}
		echo "<tr style='font-weight: bold;background-color: #808080'><td colspan='4' align='right' >Total general de insumos:</td><td  align='right'>".number_format($total,2,'.',',') ."</td></tr>";
		
		if($_REQUEST['opc']==1 && $existencia>0){
			if(in_array('29', $_SESSION['accelog_acciones'])){
		?><tr><td colspan="5" align="right">
		 		<button class="btn btn-primary btn-xs" id="todo" onclick="autorizarTodo(<?php echo $_REQUEST['idop'] ;?>);"><span class="glyphicon glyphicon-edit" ></span>Autorizar todo</button>
			</td>
		</tr>
		
	<?php 	}
		}
	}else{ ?> 
		<tr>
			<td colspan="5" align="center">No tiene insumos</td>
		</tr>
<?php } ?>
</tbody>
</table>
</div>
