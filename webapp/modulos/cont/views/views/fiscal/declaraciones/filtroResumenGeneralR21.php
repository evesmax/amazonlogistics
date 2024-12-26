<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style type="text/css">
		.cuerpo{width: 420px; height: 250px;  padding: 7px; border: 0px solid; font-family: arial;}
		
	</style>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/resumenGeneralR21.js"></script>
</head>
<body>
	<div class="repTitulo">Resumen General R21</div>
	<div class="per" id='info'>
		<ul>
			<li><label>Ejercicio:</label><select id="sel_ejercicio" class="nminputselect">
								<?php
								$res=$ejercicio->fetch_object();
								echo "<option id='ej_".$res->id."' value='".$res->id."' selected>".$res->NombreEjercicio."</option>";
								while($res=$ejercicio->fetch_object()){
									echo "<option id='ej_".$res->id."' value='".$res->id."'>".$res->NombreEjercicio."</option>";
								}
								?>
							</select></li>
			<li><label>Periodo Inicial:</label><select id="per_ini" class="nminputselect">
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
							</select></li>
			<li><label>Periodo Final:</label><select id="per_fin" class="nminputselect">
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
							</select></li>
			<li><label>Acreditar 100% del IVA retenido</label><input type="checkbox" id="considera_per" ></li>
			<li><label>Proporción</label><input type="text" class="nminputtext" value="0.0000" id="prop"></li>
			<li><label>Usar Proporción</label><select id="use_prop" class="nminputselect">
								<option value="1">Conforme articulo 5 LIVA</option>
								<option value="2">Conforme articulo 5-B LIVA</option>
							</select></li>
			<li><label>Tasas de Iva:</label><select  class="nminputselect">
								<option id='0' value="0" selected>Todos</option>
									<?php
										while($rqry_eje=$tasaIVA->fetch_object()){
											echo "<option id='tasa_".$rqry_eje->id."' value='".$rqry_eje->valor."'>".$rqry_eje->tasa."</option>";	
										}
									?>
								<option>Otra tasa</option>
							</select></li>
			<li><label>Resumen R21</label><input type="radio" class="nminputradio" name="sel_rep" id="rep1" value="1" checked></li>
			<li><label>Exportar a excel</label><input type="checkbox" class="nminputcheckbox" id="toexcel" value="1"></li>
			<li><label></label><input type="button" class="nminputbutton" value="Ejecutar Reporte" onclick="reporte_post()"></li>
		</ul>
	</div>

	
<div id="div_reporte"></div>
</body>
</html>