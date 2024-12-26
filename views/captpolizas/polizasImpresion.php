<?php
	//include("../../../netwarelog/catalog/conexionbd.php");
?>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<!--LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" /-->
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
		/*	if(f.xperiodo.checked)
			{
				if(f.periodoIni.value == '')
				{
					$('#nmloader_div',window.parent.document).hide();
					alert("Elije Periodo inicial.");
					f.fecha_impresion.focus();
					return false;
				}
				if(f.periodoFin.value == '')
				{
					$('#nmloader_div',window.parent.document).hide();
					alert("Elije Periodo final.");
					f.fecha_impresion.focus();
					return false;
				}
			}
			else
			{ */
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
		/*	} */
		}


		function fecImp()
		{
			if($('#xperiodo:checked').val())
			{
				$('#fimpr').show('slow')
				$('#pol').hide('hide')
			}
			else
			{
				$('#fimpr').hide('slow')
				$('#pol').show('hide')
			}
		}
	</script>
</head>
<?php 
if($_GET['tipo']==1){ 
					$tit="Impresión de Polizas"; 
				}else{
					$tit="Libro de Diario";
					}
?>
<body>
	<div class='repTitulo'><?php echo "$tit"; ?></div>
	<div class="per">
		<form name='reporte' id='info' method='post' action='index.php?c=polizasImpresion&f=VerReporte&tipo=<?php echo $_GET['tipo']; ?>' onsubmit='return valida(this)'>
			<ul>
				<!--li>
					<label>Por Periodos</label>
					<input class="nminputcheck" type='checkbox' id='xperiodo' name='xperiodo' value='1' onclick='fecImp();'>
				</li-->
				<div id="pol">
				<li>
					<label>Polizas Del:</label>
					<input class="nminputtext" type="date" id="fecha_ini" name='fecha_ini' placeholder="aaaa-mm-dd">
				</li>
				<li>
					<label>Al:</label>
					<input class="nminputtext" type="date" id="fecha_fin" name='fecha_fin' placeholder="aaaa-mm-dd">
				</li>
				</div>

				<div id="fimpr">
				<li>
					<label>Periodo Inicial:</label>
					<select name='periodoIni' id='periodoIni' class="nminputselect">
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
					</select>
				</li>
				<li>
					<label>Periodo Final:</label>
					<select name='periodoFin' id='periodoFin' class="nminputselect">
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
					</select>
				</li>
				</div>
				<?php if($_GET['tipo']==1){ ?>
				<li>
					<label>Tipo de Poliza:</label>
					<select name="tipo" id="tipo">
						<option value="0">Todos</option>
						<?php 
							while($tipoP=$tipo->fetch_object())
							{ 
							echo "<option value=$tipoP->id >$tipoP->titulo</option>"; 
							}
						?>
					</select>
				</li>
				<?php } ?>
				<li>
					<label>Saldos:</label>
					<input type='checkbox' name='saldos' id='saldos' value='1'>
				</li>
				
				<li>
					<label></label>
					<input class="nminputbutton" type="submit" onclick="$('#nmloader_div',window.parent.document).show();" value="Ejecutar Reporte">
				</li>
			</ul>
		</form>
	</div>
<?php //Nuevo Commit ?>
	
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