<!DOCTYPE html>
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script src="js/select2/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> 
	<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
	<script src="js/bootstrap/bootstrap.js"></script>
	<script src="js/bootstrap/bootstrap.min.js"></script>
</head>
<body>
	<div id="conciliacion">
		<div class="panel panel-default">
			<div class="panel-heading">
				<h3 class="panel-title">Polizas</h3>
			</div>
			<div class="panel-body">
				<table width="100%" border="0" class="table table-striped table-bordered table-condensed" cellspacing="0">
					<tr>
						<td>Fecha</td>
						<td>Periodo</td>
						<!-- <td>Tipo Poliza</td> -->
						<td>Referencia</td>
						<td>Concepto</td>
						<td>Cargo</td>
						<td>Abono</td>
						<td>Cuenta Banco</td>
						<td>Cuenta</td>
						<td>Saldo Inicial</td>
						<td>Saldo Final</td>
					</tr>
					<tr><?php
				//$Polizas es una varia ble que viene desde el controlador
				//con todos los registros de las polizas
					foreach ($polizas as $k => $v) { ?><?php
						
						$saldo_final='';
						
						while($rr=$v->fetch_array()){
							if ($rr['account_id']!=$cuenta) { ?>
								<tr>
									<td colspan="10" class="success"><strong>Cuenta: <?php echo $rr['account_id'] ?></strong></td>
								</tr><?php
							} 
						
							$cuenta=$rr['account_id'];
							
							if ($saldo_final=='') {
								$saldo_final=$rr['saldo_inicial'];
							}
							
							$saldo_inicial=$saldo_final;
							
							if ($rr['TipoMovto']=='Cargo') {
								$saldo_final-=$rr['importe'];
							}
							
							if ($rr['TipoMovto']=='Abono') {
								$saldo_final+=$rr['importe'];
							} ?>
							
							<tr>
								<td><?php echo $rr['fecha'] ?></td>
								<td><?php echo $rr['idperiodo'] ?></td>
								<!-- <td><?php echo $rr['idtipopoliza'] ?></td> -->
								<td><?php echo $rr['Referencia'] ?></td>
								<td><?php echo $rr['Concepto'] ?></td><?php
								
								if ($rr['TipoMovto']=='Cargo') { ?>
									<td><?php echo $rr['importe'] ?></td>
									<td></td><?php
								}
								
								if ($rr['TipoMovto']=='Abono') { ?>
									<td></td>
									<td><?php echo $rr['importe'] ?></td><?php
								} ?>
								
								<td><?php echo $rr['cuenta'] ?></td>
								<td><?php echo $rr['account_id'] ?></td> <!-- Numero de la cuenta contable -->
								<td><?php echo $saldo_inicial ?></td>
								<td><?php echo $saldo_final ?></td>
							</tr><?php
						}
					} ?>
				</table>
			</div>
		</div>
	</div>
</body>