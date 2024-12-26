<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<?php include('../../netwarelog/design/css.php');?>
<LINK href="../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->

	<style type="text/css">
		#mov{display: none;}
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
		<div id="info">
		<div class="repTitulo">Anexos IVA causado y acreditable</div>
		<div class="per">
			<ul>
				<li><label>Considerar periodo de causacion</label><input type="checkbox" value="1" id="considera_per" checked></li>
				<div id="eje">
					<li><label>Ejercicio:</label><select id="sel_ejercicio" class="nminputtext">
									<?php
									$res=$ejercicio->fetch_object();
									echo "<option id='ej_".$res->id."' value='".$res->id."' selected>".$res->NombreEjercicio."</option>";
									while($res=$ejercicio->fetch_object()){
										echo "<option id='ej_".$res->id."' value='".$res->id."'>".$res->NombreEjercicio."</option>";
									}
									?>
								</select></li>
					<li><label>Periodo Inicial:</label><select id="per_ini" class="nminputtext">
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
					<li><label>Periodo Final:</label><select id="per_fin" class="nminputtext">
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
				</div>
				<div id="mov">
				<li><label>Movimientos del:</label><input type="date" class="nminputtext" id="fecha_ini" placeholder="aaaa-mm-dd" disabled></li>
				<li><label>Al:</label><input type="date" class="nminputtext" id="fecha_fin" placeholder="aaaa-mm-dd" disabled></li>
				</div>
				<li><label>Acreditar 100% del IVA retenido</label><input type="checkbox" class="nminputcheck" id="acr_iva_ret" value=1 ></li>
				<li><label>Exportar a excel</label><input type="checkbox" class="nminputcheck" id="toexcel" value="1"></li>
				<li><label></label><input type="button" class="nminputbutton" value="Ejecutar Reporte" onclick="reporte_post(); $('#nmloader_div',window.parent.document).show();"></li>
			</ul>
			
		</div>

	</div>
	</div>
	<img id='img' src="images/loading.gif" style="display: none; width:50px;height:50px;">

<div id="div_reporte"></div>
</body>
</html>