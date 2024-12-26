<?php 
	session_start();
	error_reporting(0);
	$reportado=$_REQUEST["reportado"];
	$cortefinal=$_REQUEST["cortefinal"];
	
	//echo json_encode($_SESSION);

	include("controllers/caja.php");
	$cajaController = new Caja;

	date_default_timezone_set('America/Mexico_City');
	$fechaAl = date('Y-m-d H:i:s');  

	$fechaAlH = substr($fechaAl, -8);
	if($fechaAlH > '12:01:00'){
		$turno = 2;
		$turnoR = 'Vespertino';
	}else{
		$turno = 1;
		$turnoR = 'Matutino';
	}

	/// detecta si es el primer corte parcial
	$primerCP = $cajaController->primerCP($_SESSION['accelog_idempleado']);

	/// detecta configuracion de foodware
	$hideprod = $cajaController->hideprod($_SESSION['accelog_idempleado']);

	/// consulta datos del usuario
	$empleado = $cajaController->datosEmpleado($_SESSION['accelog_idempleado']);	
	
	if($primerCP['total'] > 0){ // ya tiene almenos un corte parcial

		if($cortefinal==1){

			$info = $cajaController->infoCorteParcial2($_SESSION['accelog_idempleado'],$primerCP['rows'][0]['fecha'],$fechaAl,$primerCP['rows'][0]['new_inicio']);			
			$saldo = ($info[0]['monto']*1 + $info[0]['inicio']*1);

			//para cerrar en 0
			$reportado = $saldo*1;
			$dif = $saldo*1 - $reportado*1;

			// Guardar Corte Parcial
				
				$idCorteP = $cajaController->guardarCorteP($turno,$fechaAl,$_SESSION['accelog_idempleado'],$info[0]['totalVentas'],$saldo,$saldo,$_SESSION['sucursal'],$info[0]['idinicio']);					 
				//// ULTIMO CORTE PARCIAL DISPARADO POR EL CORTE NORMAL
				//echo 3;
			// Guardar Corte Parcial fin


		}else{

			$info = $cajaController->infoCorteParcial2($_SESSION['accelog_idempleado'],$primerCP['rows'][0]['fecha'],$fechaAl,$primerCP['rows'][0]['new_inicio']);
			$saldo = ($info[0]['monto']*1 + $info[0]['inicio']*1);
			$dif = $saldo*1 - $reportado*1;

			// Guardar Corte Parcial
				
				$idCorteP = $cajaController->guardarCorteP($turno,$fechaAl,$_SESSION['accelog_idempleado'],$info[0]['totalVentas'],$saldo,$reportado,$_SESSION['sucursal'],$info[0]['idinicio']);					 
				//// CORTES PARCIALES DESPUES DE EL PRIMERO
				//echo 2;
			// Guardar Corte Parcial fin

		}

	}else{

			$info = $cajaController->infoCorteParcial($_SESSION['accelog_idempleado'],$fechaAl);
	
			$saldo = ($info[0]['monto']*1 + $info[0]['inicio']*1);
			$dif = $saldo*1 - $reportado*1;

			// Guardar Corte Parcial
				
				$idCorteP = $cajaController->guardarCorteP($turno,$fechaAl,$_SESSION['accelog_idempleado'],$info[0]['totalVentas'],$saldo,$reportado,$_SESSION['sucursal'],$info[0]['idinicio']);					 
				/// PRIMER CORTE PARCIAL
				//echo 1;
			// Guardar Corte Parcial fin			
	}
			
 ?>
 <!-- EN CASO DE VENIR DEL CORTE FINAL PADRE SOLO HARA LAS CONSULTAS PERO NO IMPRIME TICKET DE CORTE PARCIAL-->
	<meta charset="UTF-8">
	<link rel="stylesheet" rev="stylesheet" href="css/netpos.css" />
	<link rel="stylesheet" rev="stylesheet" href="css/netpos_print.css"  media="print"/>
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.0.3/jquery.min.js"></script>
	<style>
		#letraschicas{
		    font-size: 10px;

		}
		.small_button a{
		    color:white;
		    text-decoration:none;
		    font-family:Arial, Helvetica, sans-serif;
		}

		@media print
		{
		    .item_number{display:none;}
		}
		div.centerTable{
		        text-align: center;
		}

		div.centerTable table {
		       margin: 0 auto;
		       text-align: left;
		}
	</style>

	<div id="receipt_wrapper">

	    <div id="receipt_header" style="text-align:center;">
				<h1>FOODWARE</h1>
				<h3>Corte Parcial</h3>
				
				<div  class="centerTable">
					<table>
						<tr>
							<td align="right">Sucursal:</td>
							<td align="left"><label><strong><?php echo $_SESSION['sucursalNombre']; ?></strong></label></td>
						</tr>
						<tr>
							<td align="right">Del:</td>
							<td align="left"><label><strong><?php echo $info[0]['fechaDel']; ?></strong></label></td>
						</tr>
						<tr>
							<td align="right">Al:</td>
							<td align="left"><label><strong><?php echo $fechaAl; ?></strong></label></td>
						</tr>
						<tr>
							<td align="right">ID Corte Parcial:</td>
							<td align="left"><label><strong><?php echo $idCorteP; ?></strong></label></td>
						</tr>
						<tr>
							<td align="right">Turno:</td>
							<td align="left"><label><strong><?php echo $turnoR; ?></strong></label></td>
						</tr>
						<tr>
							<td align="right">Usuario:</td>
							<td align="left"><label><strong><?php echo $_SESSION['nombreEmpleado']; ?></strong></label></td>
						</tr>
						<tr>
							<td align="right">Reportado:</td>
							<td align="left"><label><strong><?php echo '$'.number_format($reportado,2); ?></strong></label></td>
						</tr>
					<?php if($hideprod != 1) {
					?>
					
						<tr>
							<td align="right"><strong>Saldo al corte:</strong></td>
							<td align="left"><label><strong><?php echo '$'.number_format($saldo,2); ?></strong></label></td>
						</tr>
						<tr>
							<td align="right"><strong>Diferencia:</strong></td>
							<td align="left"><label><strong><?php echo '$'.number_format($dif,2); ?></strong></label></td>
						</tr>	

					<?php
					}?>
						
					</table>
				</div>
					<div style="padding-top: 35px;">_________________________________________</div>
					<h2><?php echo $empleado ?></h2>

				
	    </div>

	  
	</div>  

	<script id="scriptAccion" type="text/javascript"> 
	$(document).ready(function() {

	    window.print();
	    window.close();
	    
	});
	</script>  
