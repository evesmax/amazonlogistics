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

	</style>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function()
	{
		$('#nmloader_div',window.parent.document).hide();
		desbloqueaFlux(0);
	});
		function valida(f)
		{
			if(f.fecha_ini.value == '')
			{
				$('#nmloader_div',window.parent.document).hide();
				alert("Falta la fecha de inicio.");
				f.fecha_ini.focus();
				return false;
			}

			if(f.fecha_fin.value == '')
			{
				$('#nmloader_div',window.parent.document).hide();
				alert("Falta la fecha fin.");
				f.fecha_fin.focus();
				return false;
			}

			if($('#provalgunos:checked').val() && $('#finicial').val() > $('#ffinal').val())
			{
				$('#nmloader_div',window.parent.document).hide();
				alert("La cuenta inicial no puede ser menor a la cuenta final");
				f.finicial.focus();
				return false;
			}

		}


		function desbloqueaFlux(valor)
		{
			if(valor)
			{
				$('#finicial').removeAttr('disabled')
				$('#ffinal').removeAttr('disabled')
				$('#ver').show('slow')

			}
			else
			{
				$('#finicial').attr('disabled','disabled')
				$('#ffinal').attr('disabled','disabled')
				$('#ver').hide('slow')
			}
		}

	</script>
</head>
<body>
	<div class='repTitulo'>Conciliaci&oacute;n de Flujo de Efectivo e IVA para DIOT.</div>
	<div class="per">
		<form name='reporte' method='post' id='info' action='index.php?c=flujoEfectivoIva&f=VerReporte' onsubmit='return valida(this)'>
		<ul>
			<li><label>Polizas Del:</label><input type="date" class="nminputtext" id="fecha_ini" name='fecha_ini' placeholder="aaaa-mm-dd"></li>
			<li><label>Al:</label><input type="date" class="nminputtext" id="fecha_fin" name='fecha_fin' placeholder="aaaa-mm-dd"></li>
			<li><fieldset style='width: 180px; margin-left: 40%;'>
						<legend>Ctas de Flujo de Efectivo a imprimir</legend>
						<input type='radio' class="nminputradio" name='prov' id='provtodos' onclick="desbloqueaFlux(0)" value='todos' checked> Todos<br />
						<input type='radio' class="nminputradio" name='prov' id='provalgunos' onclick="desbloqueaFlux(1)" value='algunos'> Algunos
					</fieldset></li>
			<div id="ver">
			<li><label>Cuenta Inicial:</label><select name='finicial' id='finicial' class='tamanoSel nminputselect'>
							<?php 
								while($f = $flujo1->fetch_object())
							{
								echo "<option value='$f->account_id'>$f->manual_code / $f->description</option>";
							}
								?>
							
						</select></li>
			<li><label>Cuenta Final:</label><select name='ffinal' id='ffinal' class='tamanoSel nminputselect'>
							<?php 
								while($f = $flujo2->fetch_object())
							{
								echo "<option value='$f->account_id'>$f->manual_code / $f->description</option>";
							}
								?>
						</select></li>
			<li><label>Imprimir detalle por proveedor</label><input type='checkbox' class="nminputcheck" name='impDetalleProv' id='impDetalleProv' value='1'></li>
			</div>
			<li><label>Mostrar s&oacute;lo las que aplican.</label><input type='checkbox' class="nminputcheck" name='soloAplican' id='soloAplican' value='1'> </li>
			<li><label></label><input class="nminputbutton" onclick="$('#nmloader_div',window.parent.document).show();" type="submit" value="Ejecutar Reporte"></li>
		</ul>
	</form>
	</div>

	
</body>
</html>