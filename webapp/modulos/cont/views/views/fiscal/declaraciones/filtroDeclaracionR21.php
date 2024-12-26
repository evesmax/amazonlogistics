<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style type="text/css">
		.cuerpo{width: 430px; height: 400px;  padding: 7px; border: 0px solid; font-family: arial;}
		
	</style>
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/declaracionR21.js"></script>
</head>
<body>
	<div class="repTitulo">Declaracion R21Impuesto al Valor Agregado </div>
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
			<li><label>Periodo:</label><select id="per_ini" class="nminputselect">
								<option id="per_ini_1" value="1" selected>Enero</option>
								<option id="per_ini_2" value="2" >Febrero</option>
								<option id="per_ini_3" value="3" >Marzo</option>
								<option id="per_ini_4" value="4" >Abril</option>
								<option id="per_ini_5" value="5" >Mayo</option>
								<option id="per_ini_6" value="6" >Junio</option>
								<option id="per_ini_7" value="7" >Julio</option>
								<option id="per_ini_8" value="8" >Agosto</option>
								<option id="per_ini_9" value="9" >Septiembre</option>
								<option id="per_ini_10" value="10" >Octubre</option>
								<option id="per_ini_11" value="11" >Noviembre</option>
								<option id="per_ini_12" value="12" >Diciembre</option>
							</select></li>
			<li><label>Acreditar 100% del IVA retenido</label><input type="checkbox" class="nminputcheckbox" id="acr_iva" onclick="check();" ></li>
			<li><label>Incluir el IVA pagado no acreditable</label><input type="checkbox" class="nminputcheckbox" id="inc_iva" ></li>
			<li><label>Usar Proporcion:</label>	<select id="prop_select" class="nminputselect">
												<option value="1">Conforme articulo 5 LIVA</option>
												<option value="2">Conforme articulo 5-B LIVA</option>
												</select></li>
			<li><label>Proporción</label><input  type="text" class="nminputtext" id="prop" value="0.0000"></li>
			<li><label>Monto acreditable derivado del ajuste</label><input  type="text" class="nminputtext" id="cant1" value="0"></li>
			<li><label>Cantidad actualizada a reintegrarse</label><input  type="text" class="nminputtext" id="cant2" value="0"></li>
			<div id='ai'>
			<li><label>Otras Cantidades a cargo</label><input type="text" class="nminputtext" id="cant3" value="0" disabled=""></li>
			<li><label>Otras cantidades a favor</label><input  type="text" class="nminputtext" id="cant4" value="0" disabled=""></li>
			</div>
			<li><label>Devolucion inmediata obtenida</label><input  type="text" class="nminputtext" id="cant5" value="0"></li>
			<li><label>Acreditamientos de periodos anteriores</label><input  type="text" class="nminputtext" id="cant6" value="0"></li>
			<li><label>IEPS Acreditable</label><input  type="text" class="nminputtext" id="cant7" value="0"></li>
			<div id='ai1'>
			<li><label>IVA retenido al contribuyente</label><input type="text"  class="nminputtext" value="0" id="retenidocontri" disabled="" ></li>
			</div>
			<li><label>Exportar a excel</label><input type="checkbox" class="nminputcheckbox" id="toexcel" value="1"></li>
			<li><label></label><input type="button" class="nminputbutton" value="Ejecutar Reporte" onclick="reporte_post()"></li>
		</ul>
	</div>
 
<div id="div_reporte"></div>
</body>
</html>