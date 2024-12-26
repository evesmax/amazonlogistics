<!DOCTYPE html>
<head>
	
</head>
<body>
		<div class="nmwatitles" align="center">Poliza de Ajuste por diferencia cambiaria.<br>
			Ejercicio:<?php echo $ejer[1];?> Periodo:<?php echo $_REQUEST['periodo'];?><br>
			Moneda:<?php echo $moneda; ?> Tipo de cambio:<?php echo $_REQUEST['tc'];?>
		</div><br></br>
	<table cellpadding="2">
		<tr>
			<th>C U E N T A</th>
			<TH></TH>
			<th>N O M B R E</th>
		</tr>
		<tr>
			<th>Fecha</th>
			<th>Tipo</th>
			<th>Numero</th>
			<th>Concepto</th>
			<th>Referencia</th>
			<th>Cargos ME</th>
			<th>Abonos ME</th>
			<th>Saldo ME</th>
			<th>T.C.</th>
			<th>Cargos MN</th>
			<th>Abonos MN</th>
			<th>Saldo MN</th>
		</tr>
	<tr><th colspan="12"><hr><hr></th></tr>
	<?php 
	$utilidadcuenta=$separau[1];
	$perdidacuenta=$separaper[1];
	foreach($total as $value){ 
		if(($value['utilidad']>0) or ($value['perdida']>0)){
		if($value['perdida']){
			$cuenta=$perdidacuenta;
			$cargo=($value['perdida']);
			$abono="";
			if(isset($value['montomn'])){
				$saldo=$value['montomn'] - $value['perdida'];
			}
			if(isset($value['provemontomn'])){
				$saldo=$value['provemontomn']  + $value['perdida'];
			}
			//$value['perdida'];
		}
		//echo$value['descripcion']."->".$value['CME']."<br>";
		if($value['utilidad']){
			$cuenta=$utilidadcuenta;
			$abono=($value['utilidad']);
			$cargo="";
			if(isset($value['montomn'])){
				$saldo=$value['montomn'] + $value['utilidad'];
			}
			if(isset($value['provemontomn'])){
				$saldo=$value['provemontomn'] - $value['utilidad'];
			}
			
			//$value['utilidad'];
		}
		// if(($value['CME']-$value['AME'])!=0){
			// if($value['CME']){
				// $saldo=abs($value['CME']*$tc);
				$cambio=$tc;
			// }
			// if($value['AME']){
				// $saldo=abs($value['AME']*$tc);
				// $cambio=$tc;
			// }
		// }else{
			// $saldo=0;
			// $cambio="";
		// }
		
		 ?>
		<tr style="font-weight: bold;">
			<td ><?php echo $value['manual']?></td>
			<td></td>
			<td><?php echo $value['descripcion'];?></td>
			<td colspan="2"></td>
			<td><?php //echo $value['CME'];?></td>
			<td><?php //echo $value['AME'];?></td>
			<?php if(isset($value['montome'])){?>
			<td><?php echo number_format(@$value['montome'],2,'.','');?> </td>
			<?php } if(isset($value['provemontome'])){?>
			<td><?php echo number_format(@$value['provemontome'],2,'.',''); }?></td>
			<td colspan=""></td>
			<td><?php //echo $value['cargo'];?></td>
			<td><?php //echo $value['abono'];?></td>
			<td></td>

		</tr>
		<tr>
			<td><?php echo $fecha;?></td>
			<td>Diario</td>
			<td><?php echo $cuenta; ?></td>
			<td colspan="4"></td>
			<?php if(isset($value['montome'])){?>
			<td><?php echo number_format(@$value['montome'],2,'.','');?> </td>
			<?php } if(isset($value['provemontome'])){?>
			<td><?php echo number_format(@$value['provemontome'],2,'.',''); }?></td>
			<td><?php echo $cambio;?></td>
			<td><?php echo $cargo; ?></td>
			<td><?php echo $abono; ?></td>
			<td><?php echo number_format($saldo,2,'.',''); ?></td>
			
		</tr>
		<tr><td><hr></hr></td></tr>
		<?php
			}			
	
		}//if del foreach
		// foreach ($mov as $val){//
			// foreach($val as $value){
				// echo $value['saldo'];
			// }
		// }
		foreach ($bancos as $value){
		 if($value['perdida']){
			$cuenta=$perdidacuenta;
			$cargo=abs($value['perdida']);
			$abono="";
			$saldo=$value['saldomn']-$value['perdida'];
			//$value['perdida'];
		}
		//echo$value['descripcion']."->".$value['CME']."<br>";
		if($value['utilidad']){
			$cuenta=$utilidadcuenta;
			$abono=abs($value['utilidad']);
			$cargo="";
			$saldo=$value['saldomn'] + $value['utilidad'];
			
			//$value['utilidad'];
		}
		
		// if($value['saldoext']!=0){
			// $saldo=$value['saldoext']*$tc;
		// }else{
			// $saldo=0;
		// }
				?>
			<tr style="font-weight: bold;">
				<td ><?php echo $value['manual']?></td>
				<td></td>
				<td><?php echo $value['nombre'];?></td>
				<td colspan="2"></td>
				<td></td>
				<td></td>
				<td><?php echo  number_format($value['saldome'],2,'.','');?></td>
				<td colspan=""></td>
				<td><?php //echo $value['cargo'];?></td>
				<td><?php //echo $value['abono'];?></td>
				<td></td>
			</tr>
			<tr>
			<td><?php echo $fecha;?></td>
			<td>Diario</td>
			<td><?php echo $cuenta; ?></td>
			<td colspan="4"></td>
			<td><?php echo number_format($value['saldome'],2,'.',''); ?></td>
			<td><?php echo $cambio;?></td>
			<td><?php echo $cargo; ?></td>
			<td><?php echo $abono; ?></td>
			<td><?php echo number_format($saldo,2,'.',''); ?></td>
			
		</tr>
		<tr><td><hr></hr></td></tr>	
		<?php		//echo $value['saldo']."<br>";
		}
	?>
	</table>
	<input type="button" onclick="window.location='index.php?c=ajustecambiario&f=verfiltro'" value="Regresar"/>
</body>
</html>