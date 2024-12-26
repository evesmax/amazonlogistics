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
		#mov{display: none;}

	</style>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
	<script type="text/javascript">
	$(document).ready(function()
	{
		$('#nmloader_div',window.parent.document).hide();
		desbloqueaProv(0);
		desbloqueaFlujo()
		activaOtraTasa()
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

		function desbloqueaFlujo()
		{
			if($('#filtraflujo:checked').val())
			{
				$('#flujo').removeAttr('disabled')
				$('#cuenta').show('slow')
			}
			else
			{
				$('#flujo').attr('disabled','disabled')
				$('#cuenta').hide('slow')
			}
		}

		function activaOtraTasa()
		{
			if($('#tasas-0:checked').val())
			{
				$('#otraTasaNum').removeAttr('disabled')
				$('#otraTasaNum').css('background-color','white')
				$('#tasa').show('slow')
			}
			else
			{
				$('#otraTasaNum').attr('disabled','disabled')
				$('#otraTasaNum').val('0.00')
				$('#otraTasaNum').css('background-color','#CCC')
				$('#tasa').hide('slow')
			}
		}
	</script>
</head>
<body>
	<div class='repTitulo' >Auxiliar de movimientos de control de IVA.</div>
	<div class="per">
		<form name='reporte' method='post' id='info' action='index.php?c=auxiliar_controlIva&f=VerReporte' onsubmit='return valida(this)'>	
		<ul>
			<li><label>Considerar periodo de acreditamiento</label><input type='checkbox' value='1' id='periodoAcreditamiento' name='periodoAcreditamiento' onchange='cambiaRango()' checked></li>
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
			<li><fieldset style='width:180px;margin-left:40%;'>
						<legend>Proveedores a imprimir</legend>
						<input type='radio' class="nminputradio" name='prov' id='provtodos' onclick="desbloqueaProv(0)" value='todos' checked> Todos<br />
						<input type='radio' class="nminputradio" name='prov' id='provalgunos' onclick="desbloqueaProv(1)" value='algunos'> Algunos
					</fieldset></li>
			<div id="verPro">
				<li><label>Proveedor Inicial:</label><select name='pinicial' id='pinicial' class='nminputselect'>
							<?php 
								while($p = $proveedores1->fetch_object())
								{
									echo "<option value='".$p->idPrv."'>".$p->idPrv."/".$p->razon_social."</option>";
								}
								?>
							
						</select></li>
				<li><label>Proveedor Final:</label><select name='pfinal' id='pfinal' class='nminputselect'>
							<?php 
								while($p = $proveedores2->fetch_object())
								{
									echo "<option value='".$p->idPrv."'>".$p->idPrv."/".$p->razon_social."</option>";
								}
								?>
						</select></li>
			</div>
			<li><label >Filtrar por cuenta de flujo de efectivo</label><input type='checkbox' class="nminputcheck" name='filtraflujo' id='filtraflujo' value='1' onclick='desbloqueaFlujo()'> </li>
			<div id="cuenta">
			<li><label >Cuenta:</label><select name='flujo' id='flujo' class='nminputselect'>
						<?php
							while($f = $flujo->fetch_object())
							{
								echo "<option value='$f->account_id'>$f->manual_code / $f->description</option>";
							}
						?>
						</select></li>
			</div>
			<li><div style="margin-left:42%;"><?php
						$contador=1;
						while($t = $tasas->fetch_object())
						{
							echo "<input type='checkbox' value='$t->valor' id='tasas-$contador' checked name='tasas-$contador'> Tasa $t->tasa<br />";
							$contador++;
						}
						?>
						<input type='checkbox' class="nminputcheck" value='otraTasa' id='tasas-0' onchange='activaOtraTasa()'> Otra tasa<br />
						</div> </li>
			<div id="tasa">
			<li><label>Tasa:</label><input type='text' class="nminputtext" name='otraTasaNum' id='otraTasaNum' value='0.00'></li>
			</div>
			<li><label>No aplica para control de IVA</label><input type='checkbox' class="nminputcheck" class="nminputtext" id='noAplica' name='noAplica' value='1'> </li>
			<li><label>Listar por proveedor</label><input type='checkbox' class="nminputcheck" name='porProv' id='porProv' value='1'> </li>
			<li><label></label><input type="submit" onclick="$('#nmloader_div',window.parent.document).show();" class="nminputbutton" value="Ejecutar Reporte"></li>
		</ul>
	</form>
	</div>

	
</body>
</html>