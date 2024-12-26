<?php
	//include("../../../netwarelog/catalog/conexionbd.php");
?>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
	<script src="//ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
	<style type="text/css">
		.cuerpo{width: 420px; height: 200px;  padding: 7px; font-family: arial;}
		#checkboxAjuste{width: 15px; height: 20px; position: relative; top: -16px; left: 130px;}
		#fimpr{display: none;}
	</style>
	<script type="text/javascript">
	$(document).ready(function() {
		$('#nmloader_div',window.parent.document).hide();
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

			if(f.impresion.checked && f.fecha_impresion.value == '')
			{
				$('#nmloader_div',window.parent.document).hide();
				alert("Falta la fecha de impresion.");
				f.fecha_impresion.focus();
				return false;
			}

		}


		function fecImp()
		{
			if($('#impresion:checked').val())
			{
				$('#fimpr').show('slow')
			}
			else
			{
				$('#fimpr').hide('slow')
			}
		}
	</script>
</head>
<body>
	<div class='repTitulo'>Egresos sin control de IVA</div>
	<div class="per">
		<form name='reporte' id='info' method='post' action='index.php?c=EgresosSinIva&f=VerReporte' onsubmit='return valida(this)'>
			<ul>
				<li><label>Movimientos Del:</label><input class="nminputtext" type="date" id="fecha_ini" name='fecha_ini' placeholder="aaaa-mm-dd"></li>
				<li><label>Al:</label><input class="nminputtext" type="date" id="fecha_fin" name='fecha_fin' placeholder="aaaa-mm-dd"></li>
				<li><label>Usar fecha de impresi&oacute;n:</label><input class="nminputcheck" type='checkbox' id='impresion' name='impresion' value='1' onclick='fecImp();'></li>
				<div id="fimpr">
				<li><label>Fecha del Reporte:</label><input type="date" class="nminputtext" id="fecha_impresion" name='fecha_impresion' placeholder="aaaa-mm-dd"></li>
				</div>
				<li><label></label><input class="nminputbutton" type="submit" onclick="$('#nmloader_div',window.parent.document).show();" value="Ejecutar Reporte"></li>
			</ul>
		</form>
	</div>

	
				<!--tr>
					<td class="filtro_text">
						Usar fecha de impresi&oacute;n<br /><div id="checkboxAjuste"></div>
					</td>
					<td class="filtro_text">
						Fecha del reporte<br />
					</td>
				</tr-->	
</body>
</html>