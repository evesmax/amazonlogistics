<?php
	//include("../../../netwarelog/catalog/conexionbd.php");
?>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style type="text/css">
		.cuerpo{width: 520px; height: auto  padding: 7px; font-family: arial;}
		.tamanoSel{width: 200px;	text-overflow: ellipsis;}
		#conv{display: none;}
	</style>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
	<script src="../cont/js/select2/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../cont/js/select2/select2.css" />
	<script type="text/javascript">
	$(function()
		 {
		 	 $("#moneda").select2({
						 width : "250px",
						 placeholder:"Selecciona una moneda"
						});
		 });
	
	$(document).ready(function()
	{
		$('#nmloader_div',window.parent.document).hide();
	});
		
		// Formulario Tipo de Cambio Moneda
		function valida(f)
		{
			if($('#tipoC:checked').val() &&  f.valMon.value =='')
			{
				alert('Debe colocar una cantidad en el Tipo de Cambio');
				return false;
			}
			else 
			{
			if(!$('#tipoC:checked').val() &&  f.valMon.value >0)
			{
				alert('Elimine la cantidad en Tipo de Cambio o Active Convertir Moneda');
				return false;
			}
			}
		}
	
	function conMon()
		{
			if($('#tipoC:checked').val())
			{
				$('#conv').show('slow')
			}
			else
			{
				$('#conv').hide('slow')
			}
		}
	
	</script>
</head>
<body>

	<div class="repTitulo">Balanza de Comprobación</div>
	<div class="per">
		<form name='reporte' id='info' method='post' action='index.php?c=reports&f=balanzaComprobacionReporte' onsubmit='return valida(this)'>
			<ul><li><label>Ejercicio:</label><select name='ejercicio' id='ejercicio' class="nminputselect">
								<?php 
								while($p = $ejercicios->fetch_object())
								{
									echo "<option value='".$p->Id."'>".$p->NombreEjercicio."</option>";
								}
								?>
							</select></li>
				<li><label>Movimientos De:</label><select name='periodo_inicio' id='periodo_inicio' class="nminputselect">
								<option value='1'>Enero</option>
								<option value='2'>Febrero</option>
								<option value='3'>Marzo</option>
								<option value='4'>Abril</option>
								<option value='5'>Mayo</option>
								<option value='6'>Junio</option>
								<option value='7'>Julio</option>
								<option value='8'>Agosto</option>
								<option value='9'>Septiembre</option>
								<option value='10'>Octubre</option>
								<option value='11'>Noviembre</option>
								<option value='12'>Diciembre</option>
							</select></li>
				<li><label>A:</label><select name='periodo_fin' id='periodo_fin' class="nminputselect">
								<option value='1'>Enero</option>
								<option value='2'>Febrero</option>
								<option value='3'>Marzo</option>
								<option value='4'>Abril</option>
								<option value='5'>Mayo</option>
								<option value='6'>Junio</option>
								<option value='7'>Julio</option>
								<option value='8'>Agosto</option>
								<option value='9'>Septiembre</option>
								<option value='10'>Octubre</option>
								<option value='11'>Noviembre</option>
								<option value='12'>Diciembre</option>
								<option value='13'>Cierre del ejercicio</option>
							</select></li>
				<li><label></label>
						<fieldset align='center' style='width: 60%;
  margin-left: 20%;'><legend>Ver por:</legend>
						<input type='radio' name='tipo' value='1' checked> Nivel Afectables<br />
						<input type='radio' name='tipo' value='2'> Nivel de Mayor<br />
						<input type='radio' name='tipo' value='3'> Todos<br />
						</fieldset>
					</td></li>
			<li><label>Convertir Moneda</label><input type='checkbox' value='1' id='tipoC' name='tipoC' onclick='conMon();'></li>
			<div id="conv">
			<li><label>Moneda</label>
									<select name="moneda" id='moneda'>
										<?php
										while($coin = $monedas->fetch_object())
										{
											if($coin->coin_id>1){
											echo "<option value='".$coin->description."'>".$coin->description."(".$coin->codigo.")</option>";
												}
										}
										?>
									</select></li>
			<li><label>Tipo de Cambio</label><input type="text" name="valMon" placeholder="00.00" /></li>
			</div>	
			<li><label></label><input type="submit" class="nminputbutton" value="Ejecutar Reporte"></li>
			</ul>
		</form>
	</div>
	
</body>
</html>