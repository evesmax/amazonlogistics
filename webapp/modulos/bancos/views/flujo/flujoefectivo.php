<!DOCTYPE html>
<html>
	<head>
		<meta charset="UTF-8" />
		<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> 
		<script type="text/javascript" src='js/flujo.js'></script>
	</head>
	<body>
		
		<div class="nmwatitles">Flujo de Efectivo.</div><br>
		<table>
			<tr>
				<td><a href="javascript:window.print();"><img  border="0" src="../../netwarelog/design/default/impresora.png" width="20px"></a>
				</td>
				<td width="16" align="right">
				 <a href="javascript:pdf();"><img src="../../../webapp/netwarelog/repolog/img/pdf.gif" title ="Generar reporte en PDF" border="0"></a> 
				</td>
				<td width="16" align="right">
				<a href="javascript:mail();">
				<img border="0" title="Enviar reporte por correo electrónico" src="../../../webapp/netwarelog/repolog/img/email.png">
				</a>
				</td>
				<td>
				<img src="images/images.jpg" title="Exportar a Excel" onclick="window.open('<?php echo $url; ?>')" width="25px" height="25px"> 
				</td>
			</tr>
		</table><br><br>
		<table cellspacing="2" cellpadding="2">
			<tr>
				<!-- <td>Moneda:</td>
				<td></td>
				<td>Fecha Actual:</td>
				<td><?php echo date('d/m/Y'); ?></td> -->
			</tr>
			<tr>
				<td>Fecha Inicial:</td>
				<td><input id="inicial" type="date" class="nminputtext"/></td>
				<td>Fecha Final:</td>
				<td><input id="final" type="date"  class="nminputtext"/></td>
				<td><input type="button" value="Consultar" class="btn-primary" /></td>
			</tr>
		</table>
		
		<br><br>
		<table class="table_anex busqueda" width="60%" cellspacing="0" cellpadding="3" border="0">
			<tr class="" style="width:120px !important;">
			<td >SALDO INICAL</td>
			<td align="right">0.00</td>
			</tr>
			<tr style="background-color:#98ac31;">
			<td colspan="2"><b>Ingreso</b></td>
			</tr>
			<tr>
			<td>Ingresos por Predial</td>
			<td align="right">0.00</td>
			</tr>
			<tr>
			<td>Ingresos por Refrendo</td>
			<td align="right">0.00</td>
			</tr>
			<tr>
			<td>Ingresos por Multas</td>
			<td align="right">0.00</td>
			</tr>
			<tr style="background-color:#98ac31;">
			<td ><b>Disponible</b></td>
			<td align="right">0.00</td>
			</tr>
			<tr style="background-color:#98ac31;">
			<td colspan="2"><b>Egresos</b></td>
			</tr>
			<tr>
			<td>Egresos Pago a Proveedores</td>
			<td align="right">0.00</td>
			</tr>
			<tr>
			<td>Egresoso Gastos Generales</td>
			<td align="right">0.00</td>
			</tr>
			<tr>
			<td>Egresos Varios</td>
			<td align="right">0.00</td>
			</tr>
			<tr style="background-color:#98ac31;">
			<td ><b>Total de Egresos</b></td>
			<td align="right">0.00</td>
			</tr>
			<tr class="" style="width:120px !important;">
			<td >SALDO FINAL</td>
			<td align="right">0.00</td>
			</tr>
			
		</table>
	</body>
</html>