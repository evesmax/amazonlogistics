<?php
	//include("../../../netwarelog/catalog/conexionbd.php");
?>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
	<?php include('../../netwarelog/design/css.php');?>
<LINK href="../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style type="text/css">
		.cuerpo{width: 520px; height: auto;  padding: 7px; font-family: arial;}
		.tamanoSel{width: 200px;	text-overflow: ellipsis;}
		#mov{display:none;}		

	</style>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function()
	{
		$('#nmloader_div',window.parent.document).hide();
		desbloqueaProv(0);
	});
		function valida(f)
		{
			if(!$('#periodoAcreditamiento:checked').val() &&  f.fecha_ini.value == '')
			{
				alert("Falta la fecha de inicio.");
				f.fecha_ini.focus();
				return false;
			}

			if(!$('#periodoAcreditamiento:checked').val() && f.fecha_fin.value == '')
			{
				alert("Falta la fecha fin.");
				f.fecha_fin.focus();
				return false;
			}

			if($('#provalgunos:checked').val() && $('#pinicial').val() > $('#pfinal').val())
			{
				alert("El proveedor inicial no puede ser menor al proveedor final");
				f.pinicial.focus();
				return false;
			}

		}

		function cambiaRango()
		{
			
			if($('#periodoAcreditamiento:checked').val())
			{
				$('#ejercicio').removeAttr('disabled')
				$('#periodo_inicio').removeAttr('disabled')
				$('#periodo_fin').removeAttr('disabled')
				$('#fecha_ini').attr('disabled','disabled')
				$('#fecha_fin').attr('disabled','disabled')
				$('#mov').hide('slow')
				$('#eje').show('slow')	
			}
			else
			{
				$('#ejercicio').attr('disabled','disabled')
				$('#periodo_inicio').attr('disabled','disabled')
				$('#periodo_fin').attr('disabled','disabled')
				$('#fecha_ini').removeAttr('disabled')
				$('#fecha_fin').removeAttr('disabled')
				$('#eje').hide('slow')
				$('#mov').show('slow')
			}
		}

		function desbloqueaProv(valor)
		{
			if(valor)
			{
				$('#pinicial').removeAttr('disabled')
				$('#pfinal').removeAttr('disabled')
				$('#verPro').show('slow')
			}
			else
			{
				$('#pinicial').attr('disabled','disabled')
				$('#pfinal').attr('disabled','disabled')
				$('#verPro').hide('slow')
			}
		}
	</script>
</head>
<body>
	<div class="repTitulo">Auxiliar del Formato A29</div>
	
	<div class="per">
		<form name='reporte' method='post' id='info' action='index.php?c=auxiliar_a29&f=VerReporte' onsubmit='return valida(this)'>
		
			<ul>
			<li><label> Considerar periodo de acreditamiento</label><input type='checkbox' value='1' id='periodoAcreditamiento' name='periodoAcreditamiento' onchange='cambiaRango()' checked></li>
			<div id='eje'>
				<li><label>Ejercicio:</label><select name='ejercicio' id='ejercicio' class="nminputselect">
								<?php 
								while($p = $periodos->fetch_object())
								{
									echo "<option value='".$p->Id."'>".$p->NombreEjercicio."</option>";
								}
								?>
							</select></li>
				<li><label>Movimientos Del:</label><select name='periodo_inicio' id='periodo_inicio' class="nminputselect">
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
				<li><label>Al:</label><select name='periodo_fin' id='periodo_fin' class="nminputselect">
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
			</div>
			
			<div id='mov'>
				<li><label>Movimientos Del:</label><input type="date" class="nminputtext" id="fecha_ini" name='fecha_ini' placeholder="aaaa-mm-dd" disabled></li>
				<li><label>Al:</label><input type="date" class="nminputtext" id="fecha_fin" name='fecha_fin' placeholder="aaaa-mm-dd" disabled></li>
			</div>

			<li><fieldset style='width:180px;margin-left:40%;' >
						<legend>Proveedores a imprimir</legend>
						<input type='radio' name='prov' id='provtodos' onclick="desbloqueaProv(0)" value='todos' checked> Todos<br />
						<input type='radio' name='prov' id='provalgunos' onclick="desbloqueaProv(1)" value='algunos'> Algunos
					</fieldset></li>
			<div id='verPro'>
				<li><label>Proveedor Inicial:</label><select name='pinicial' id='pinicial' class='tamanoSel nminputselect'>
							<?php 
								while($p = $proveedores1->fetch_object())
								{
									echo "<option value='".$p->idPrv."'>".$p->idPrv."/".$p->razon_social."</option>";
								}
								?>
								</select></li>
				<li><label>Proveedor Final:</label><select name='pfinal' id='pfinal' class='tamanoSel nminputselect'>
							<?php 
								while($p = $proveedores2->fetch_object())
								{
									echo "<option value='".$p->idPrv."'>".$p->idPrv."/".$p->razon_social."</option>";
								}
								?>
						</select></li>
			</div>
			<li><label></label><input type="submit" onclick="$('#nmloader_div',window.parent.document).show();" class="nminputbutton" value="Ejecutar Reporte"></li>
			</ul>
		</div>
		</form>
	</div>	


	
</body>
</html>