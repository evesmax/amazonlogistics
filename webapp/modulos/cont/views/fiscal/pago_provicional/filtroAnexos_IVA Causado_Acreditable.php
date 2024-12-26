<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<link rel="stylesheet" type="text/css" href="../../netwarelog/design/default/netwarlog.css" >
	<link rel="stylesheet" type="text/css" href="../netwarelog/design/default/netwarlog.css" >

	<style type="text/css">
		
	</style>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/anexosIVACausadoAcreditable.js"></script>
	
</head>
<script>
	$(document).ready(function() {
		$('#nmloader_div',window.parent.document).hide();
	});
</script>
<body>
	
	<div class="" id="cuerpo">
		<div class="nmwatitles">Anexos IVA causado y acreditable</div>
		<div class="fondo">
			<table width="100%" height="99%" class="tabla_filtro">
				<tr>
					<td width="50%"> 
						<div ><input type="checkbox" value="1" id="considera_per" checked>Considerar periodo de causacion</div>
					</td>
					<td width="50%">
					</td>
				</tr>
				<tr>
					<td>
						<div>Ejercicio</div>
						<div class="filtro_select">
							<select id="sel_ejercicio" class="nminputtext">
									<?php
									$res=$ejercicio->fetch_object();
									echo "<option id='ej_".$res->id."' value='".$res->id."' selected>".$res->NombreEjercicio."</option>";
									while($res=$ejercicio->fetch_object()){
										echo "<option id='ej_".$res->id."' value='".$res->id."'>".$res->NombreEjercicio."</option>";
									}
									?>
								</select>
						</div>
					</td>
					<td>

					</td>
				</tr>
				<tr>
					<td>
						<div>Periodo inicial</div>
						<div class="filtro_select">
							<select id="per_ini" class="nminputtext">
								<option id="per_ini_1" value="1"selected>Enero</option>
								<option id="per_ini_2" value="2">Febrero</option>
								<option id="per_ini_3" value="3">Marzo</option>
								<option id="per_ini_4" value="4">Abril</option>
								<option id="per_ini_5" value="5">Mayo</option>
								<option id="per_ini_6" value="6">Junio</option>
								<option id="per_ini_7" value="7">Julio</option>
								<option id="per_ini_8" value="8">Agosto</option>
								<option id="per_ini_9" value="9">Septiembre</option>
								<option id="per_ini_10" value="10">Octubre</option>
								<option id="per_ini_11" value="11">Noviembre</option>
								<option id="per_ini_12" value="12">Diciembre</option>
							</select>
						</div>
					</td>
					<td>
						<div>Periodo final</div>
						<div class="filtro_select">
							<select id="per_fin" class="nminputtext">
								<option id="per_fin_1" value="1"selected>Enero</option>
								<option id="per_fin_2" value="2">Febrero</option>
								<option id="per_fin_3" value="3">Marzo</option>
								<option id="per_fin_4" value="4">Abril</option>
								<option id="per_fin_5" value="5">Mayo</option>
								<option id="per_fin_6" value="6">Junio</option>
								<option id="per_fin_7" value="7">Julio</option>
								<option id="per_fin_8" value="8">Agosto</option>
								<option id="per_fin_9" value="9">Septiembre</option>
								<option id="per_fin_10" value="10">Octubre</option>
								<option id="per_fin_11" value="11">Noviembre</option>
								<option id="per_fin_12" value="12">Diciembre</option>
							</select>
						</div>
					</td>
				</tr>
				<tr>
					<td class="filtro_text">
						<div>Movimientos del</div>
						<div >
							<input type="date" class="nminputtext" id="fecha_ini" placeholder="aaaa-mm-dd" disabled>
						</div>
					</td>
					<td class="filtro_text">
						<div>Al</div>
						<div >
							<input type="date" class="nminputtext" id="fecha_fin" placeholder="aaaa-mm-dd" disabled>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2"> 
						<div ><input type="checkbox" class="nminputcheck" id="acr_iva_ret" value=1 checked>Acreditar 100% del IVA retenido</div>
					</td>
				</tr>
				<tr>
					<td>
						<div ><input type="checkbox" class="nminputcheck" id="toexcel" value="1">Exportar a excel</div>
					</td>
					<td >
						<div style="float:right; margin-right:20px;"><input type="button" class="nminputbutton" value="Ejecutar Reporte" onclick="reporte_post(); $('#nmloader_div',window.parent.document).show();"></div>
					</td>
				</tr>
			</table>
			<br>
		</div>

	</div>
	<img id='img' src="images/loading.gif" style="display: none; width:50px;height:50px;">

<div id="div_reporte"></div>
</body>
</html>