<table class='table table-striped'>
<tr><th></th><th colspan="4">Pagado</th><th colspan="2">Por Pagar</th></tr>
<tr><th>Concepto</th><th>BBVA MXN</th><th>BBVA USD</th><th>Efectivo MXN</th><th>Efectivo USD</th><th>Por Pagar MXN</th><th>Por Pagar USD</th></tr>
<?php
$idViajeAnt = 0;
$cont = 0;
while($d = $datos->fetch_assoc()){
	if($d['idViaje'] != $idViajeAnt){
		if($cont > 0)
		{
			echo "<tr style='background-color:#F1F1F1;'><td><b style='color:#161C7C;'>Total de gastos variables</b></td><td>$ ".number_format($totalb1mx,2)."</td><td>$ 0.00</td><td>$ ".number_format($totalmx,2)."</td><td>$ 0.00</td><td>$ ".number_format($totalppmx,2)."</td><td>$ 0.00</td></tr>";
			echo "<tr style='background-color:#F1F1F1;'><td><b style='color:#161C7C;'>Dolarizado</b></td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td></tr>";
			echo "<tr style='background-color:#F1F1F1;'><td><b style='color:#161C7C;'>Resultado de operación sin fijos</b></td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td></tr>";
			echo "<tr><td colspan='7'>&nbsp;</td></tr>";
		}
		$ingOp1 = $ingOp2 = $ingOp3 = $ingOp4 = $ingOp5 = $ingOp6 = 0;
		$ingresoOpMTC = explode('/',$d['ingresoOpMTC']);

		if(intval($ingresoOpMTC[0]) == 1){
			if($d['ingresoOpE'])
				$ingOp3 = $d['ingresoOpE'];

			if($d['ingresoOpC'])
				$ingOp5 = $d['ingresoOpC'];
		}

		if(intval($ingresoOpMTC[0]) == 2){
			if($d['ingresoOpE'])
				$ingOp4 = $d['ingresoOpE'];

			if($d['ingresoOpC'])
				$ingOp6 = $d['ingresoOpC'];
		}
		
		echo "<tr style='background-color:#F1F1F1;'><th>Nombre del Cliente:<br /> ".$d['cliente']."</th><th>Fecha de Vuelo: <br />".$d['fechaIda']."</th><th>Ruta: <br />".$d['origen']."-".$d['destino']."-".$d['origen']."</th><th>Tiempo de Vuelo <br />".$d['tiempoTotal']."</th><th colspan='3'></th></tr>";
		echo "<tr><td style='text-align:center;'>INGRESOS</td><td colspan='6'></td></tr>";
		echo "<tr><td>Ingresos por operacion</td><td>$ ".number_format($ingOp1,2)."</td><td>$ ".number_format($ingOp2,2)."</td><td>$ ".number_format($ingOp3,2)."</td><td>$ ".number_format($ingOp4,2)."</td><td>$ ".number_format($ingOp5,2)."</td><td>$ ".number_format($ingOp6,2)."</td></tr>";
		echo "<tr><td>Ingresos por terceros</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td></tr>";
		echo "<tr><td>TOTAL DE INGRESOS</td><td>$ ".number_format($ingOp1,2)."</td><td>$ ".number_format($ingOp2,2)."</td><td>$ ".number_format($ingOp3,2)."</td><td>$ ".number_format($ingOp4,2)."</td><td>$ ".number_format($ingOp5,2)."</td><td>$ ".number_format($ingOp6,2)."</td></tr>";
		echo "<tr><td>Dolarizado</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td></tr>";
		echo "<tr><td style='text-align:center;'>GASTOS VARIABLES</td><td colspan='6'></td></tr>";
		$totalmx = $totalb1mx = $totalppmx = 0;
	}
	$b1mx = $b1us = $mx = $us = $ppmx = $ppus = 0.00;
	if(intval($d['formaPago'] == 1)){
		$mx = $d['importe'];
	}elseif(intval($d['formaPago'] == 6)){
		$ppmx = $d['importe'];
	}else{
		$b1mx = $d['importe'];
	}

	echo "<tr><td>".$d['concepto']."</td><td>$ ".number_format($b1mx,2)."</td><td>$ ".number_format($b1us,2)."</td><td>$ ".number_format($mx,2)."</td><td>$ ".number_format($us,2)."</td><td>$ ".number_format($ppmx,2)."</td><td>$ ".number_format($ppus,2)."</td></tr>";
	$totalmx += $mx;
	$totalppmx += $ppmx;
	$totalb1mx += $b1mx;
	$idViajeAnt = $d['idViaje'];
	$cont++;
}
if($cont){
	echo "<tr style='background-color:#F1F1F1;'><td><b style='color:#161C7C;'>Total de gastos variables</b></td><td>$ ".number_format($totalb1mx,2)."</td><td>$ 0.00</td><td>$ ".number_format($totalmx,2)."</td><td>$ 0.00</td><td>$ ".number_format($totalppmx,2)."</td><td>$ 0.00</td></tr>";
			echo "<tr style='background-color:#F1F1F1;'><td><b style='color:#161C7C;'>Dolarizado</b></td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td></tr>";
			echo "<tr style='background-color:#F1F1F1;'><td><b style='color:#161C7C;'>Resultado de operación sin fijos</b></td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td><td>$ 0.00</td></tr>";
}else{
	echo "<tr><td colspan='7' style='text-align:center;'><i>No hay datos.</i></td></tr>";
}

?>
</table>