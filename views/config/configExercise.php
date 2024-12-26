<script src="js/jquery-1.10.2.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="../posclasico/js/date.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="../posclasico/js/datepicker_cash.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="js/moment.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src='js/select2/select2.min.js'></script>
<script language='javascript' src='js/config.js'></script>

<link rel="stylesheet" href="js/select2/select2.css">

<style>
.dp-choose-date{
	display:none;
}
</style>
<?php
//Validacion si es configuracion de empresa o de gubernamental
if($tipoConfiguracion == 1)
{
	$priv = "style='display:block;'";
	$gob = "style='display:none;'";
}
else
{
	$priv = "style='display:none;'";
	$gob = "style='display:block;'";
}
//TERMINA Validacion
			
if(!isset($data['id']))
{
	$act=0;
	$titulo='Crear Organizaci&oacute;n';
}
else
{
	$act=1;	
	$titulo='Modificar Organizaci&oacute;n';
}


if(!intval($data['PrimeraVez']))
{
	$readonly = 'readonly';
	$disabled = 'disabled';
	$display = 'style="display:none"';
	$segundodisplay = 'style="display:block"';
	$cl_lab = "<label>".$data['ClaveOrg']."</label>";
	$verSel = "style='display:none;";
	$activo = 'si';

}else
{
	$display = 'style="display:block"';
	$segundodisplay = 'style="display:none"';
	$readonly = '';
	$disabled = '';
	$cl_lab = "";
	$verSel = "style='display:block;";
	$activo = 'no';
}

if(intval($data['TipoCatalogo']) == 3)
{
	$chkd1 = '';
	$chkd2 = 'checked';
}
else
{
	$chkd1 = 'checked';
	$chkd2 = '';	
}

$numPoliza['id'] = "temporales";

require("views/captpolizas/facturas.php");
?>
<input type='hidden' id='idpoliza' value='temporales'>
<form name='newCompany' method='post' action='index.php?c=Config&f=saveConfig&act=<?php echo $act; ?>' onsubmit='return validaciones(this)' enctype="multipart/form-data" >
	<div class="nmwatitles"><?php echo $titulo; ?></div>
	<table>
		<tr>
			<td>Nombre de la organizaci&oacute;n: </td>
			<td><input type='text' class="nminputtext" name='NameCompany' size='50' readonly value='<?php echo $name['nombreorganizacion']; ?>'></td>
		</tr>
		<tr>
			<td>Ejercicio Vigente: </td>
			<td><input type='text' class="nminputtext" name='NameExercise' size='50' readonly value='<?php echo $data['EjercicioActual']; ?>'></td>
		</tr>
	</table>
	<div class='lateral' <?php echo @$segundodisplay ?>>
		<div class='nmcatalogbusquedatit'>Cat&aacute;logo de Cuentas</div>
		<center><input type='button' value='Reiniciar Contabilidad' onclick='reiniciar()' activo='<?php echo $activo; ?>' style='margin:10px 0px;' id='reinicia'></center>
	</div>
	<div class='lateral' <?php echo @$display ?>>
		<div class='nmcatalogbusquedatit'>Cat&aacute;logo de Cuentas</div>

		<div style="float: left; width: 100%; margin-top: 13px; margin-bottom: 10px;">
			<label for="carga2"  style="float: left;width:100%;"><input type="radio" value="2" name="tipoCarga" id="carga2"  <?php echo @$disabled; ?> <?php echo @$chkd1; ?>>Carga catalogo predefinido</label>
			<div style="width: 800px;float:left;margin-left: 10%;margin-left: 10%;">
				<?php if(intval($data['TipoCatalogo']) == 0)
				{
					?>
					<input type='radio' class="nminputradio" name='default_catalog' value='1' <?php echo @$disabled; ?> >Si&nbsp;&nbsp;&nbsp;<input type='radio' class="nminputradio" name='default_catalog' value='0' checked <?php echo @$disabled; ?> >No
					&nbsp;&nbsp;
					<input type='radio' class="nminputradio" name='default_catalog' value='2'<?php echo @$disabled; ?> >Importar Cuentas de Otra Instancia
					<input type="file" name="archivo" id="archivo"  value="Importar Cuentas" <?php echo @$disabled; ?>/>
					<?php	
				}
				if(intval($data['TipoCatalogo']) == 1)
				{
					?>
					<input type='radio' class="nminputradio" name='default_catalog' value='1' checked <?php echo @$disabled; ?> >Si&nbsp;&nbsp;&nbsp;<input type='radio' class="nminputradio" name='default_catalog' value='0' <?php echo @$disabled; ?> >No
					&nbsp;&nbsp;
					<input type='radio' class="nminputradio" name='default_catalog' value='2'<?php echo @$disabled; ?> >Importar Cuentas de Otra Instancia
					<input type="file" name="archivo" id="archivo"  value="Importar Cuentas" <?php echo @$disabled; ?>/>
					<?php	
				}
				if(intval($data['TipoCatalogo']) == 2)
				{
					?>
					<input type='radio' class="nminputradio" name='default_catalog' value='1' <?php echo @$disabled; ?> >Si&nbsp;&nbsp;&nbsp;<input type='radio' class="nminputradio" name='default_catalog' value='0' <?php echo @$disabled; ?> >No
					&nbsp;&nbsp;
					<input type='radio' class="nminputradio" name='default_catalog' value='2' checked <?php echo @$disabled; ?> >Importar Cuentas de Otra Instancia
					<input type="file" name="archivo" id="archivo"  value="Importar Cuentas" <?php echo @$disabled; ?>/>
					<?php	
				}
				?>

			</div>
		</div>

		<div style="float: left; width: 100%; margin-top: 13px; margin-bottom: 10px;border-top:2px solid;">
			<label for="carga3" style="float: left;width:100%;"><input type="radio" value="3" name="tipoCarga" id="carga3" <?php echo @$disabled; ?> <?php echo @$chkd2; ?>>Carga otros Sistemas</label>
			<div style="width: 800px;float:left;margin-left: 10%;">
				<div style="width: 800px;float:left;margin-bottom:20px;">
					<label >Descargar plantillas de Datos</label>
					<a href="Formato_cuentas.xls" style="display:block;text-align:left;width: 300px;"><img src="images/xls_icon.gif" alt="">Cuentas<div style='color: #FF0000;float:right;'> (No elimine ninguna columna del formato.)</div></a>
					<a href="datos.txt" style="display:block;text-align:left;width: 300px;"><img src="images/txt_icon.gif" alt="">Definicion de datos</a>
					<a href="Formato_polizas.xls" style="display:block;text-align:left;width: 300px;"><img src="images/xls_icon.gif" alt="">Polizas<div style='color: #FF0000;float:right;'> (No elimine ninguna columna del formato.)</div></a>
				</div>
				<div style="width: 800px;float:left;margin-bottom:20px;">
					<input style='float:left;display:inline' type='button' name='xmls' value='Importar XML´s' onclick="facturasConf()">
				</div>
				<label for="archivo2" style="float: left; width: 20%;">Importar Cuentas </label>
				<input type="file" name="CONTPAC[]" id="archivo1"  value="Importar Cuentas CONTPAC" <?php echo @$disabled; ?>/><br>
				<label for="archivo2" style="float: left; width: 20%;">Importar Polizas </label>
				<input type="file" name="CONTPAC[]" id="archivo2"  value="Importar Polizas CONTPAC" <?php echo @$disabled; ?>/>
				
				<div style="float:left;width:100%;">
					<label for="txtMascara" style="float: left;">Mascara</label>
					<input class="nminputtext" type="text" name="txtMascara" value="" id="txtMascara" style="float: left; width: 40%; margin-left: 4%;" <?php echo @$disabled; ?>> 
				</div>
				<div style="float:left;width:100%;margin-top: 3px;">
					<label for="txtSeparador" style="float: left;">Separador</label>
					<input class="nminputtext" type="text" name="txtSeparador" onblur="validaSeparador()" value="" id="txtSeparador" style="float: left; width: 40%; margin-left: 2.7%;" <?php echo @$disabled; ?>>
				</div>
			</div>
		</div>
	</div>
	<div class='lateral'>
		<div class='nmcatalogbusquedatit'>Niveles</div>
		<div style="width: 800px;float:left;margin-left: 10%;">
			<input type='hidden' name='values' value='n'>
			<br />
			<fieldset style='width:400px;'>
				<legend>Cuentas</legend>
			<?php if(!isset($data['TipoNiveles']) OR $data['TipoNiveles'] == 'a')
			{
				?>
				<input type='radio' name='level' value='a' checked <?php echo @$disabled; ?> >Numeros Automaticos<br /><input type='radio' name='level' id='lev_manual' value='m' <?php echo @$disabled; ?> >Numeros Manuales <input type='text' class="nminputtext" name='structure' id='structure' value='<?php echo @$data['Estructura'];?>' onchange='estructura_vacia()' <?php echo @$readonly; ?>>
				<?php 
			}
			else
			{
				?>
				<input type='radio' name='level' value='a' <?php echo @$disabled; ?> >Numeros Automaticos<br /><input type='radio' name='level' value='m' checked <?php echo @$disabled; ?> >Numeros Manuales <input type='text' class="nminputtext" name='structure' id='structure' value='<?php echo @$data['Estructura'];?>' onchange='estructura_vacia()' <?php echo @$readonly; ?>>
				<?php	
			}
			?>
			</fieldset>
			<br />
			<fieldset style='width:400px;'>
				<legend>Polizas</legend>
			<?php if(!isset($data['NumPol']) OR !intval($data['NumPol']))
			{
				?>
				<input type='radio' name='numpol' value='0' checked <?php echo @$disabled; ?> >Numeros Automaticos<br /><input type='radio' name='numpol' value='1' <?php echo @$disabled; ?> >Numeros Manuales
				<?php 
			}
			else
			{
				?>
				<input type='radio' name='numpol' value='0' <?php echo @$disabled; ?> >Numeros Automaticos<br /><input type='radio' name='numpol' value='1' checked <?php echo @$disabled; ?> >Numeros Manuales
				<?php	
			}
			?>
			</fieldset>
			<br />

			
			<div <?php echo $priv;?>>
				<fieldset style='width:400px;'>
					<legend>RFC</legend>
					<input type='text' class="nminputtext" name='rfc' size='50' value='<?php echo @$rfc;?>'>
				</fieldset>
			</div>
			
			<div <?php echo $gob;?>>
				<fieldset style='width:400px;'>
					<legend>Clave Administrativa</legend>
					<?php echo $cl_lab; ?>
					<select name='cl_num' id='cl_num' <?php echo $verSel?>>
						<?php 
						echo $sel;
							?>
					</select>
				</fieldset>
			</div>
			<input type='hidden' value='-' name='cl_num'>
			<br /><br />
		</div>
	</div>
</div>
<div class='lateral'>
	<div class='nmcatalogbusquedatit'>Ejercicio Financiero</div>
	<table>
		<tr>
			<?php
			if(!isset($data['InicioEjercicio']))
			{
				$ejercicio = '01-01-'.$data['EjercicioActual'];
			}
			else
			{
				$ej = explode('-',$data['InicioEjercicio']);
				$ejercicio = $ej[2].'-'.$ej[1].'-'.$ej[0];
			}
			?>
			<td>
				Inicio del ejercicio
			</td>
			<td>
				<input type='text' class="nminputtext date-pick" name='begin'  value='<?php echo $ejercicio; ?>' onchange='cambia_fecha()' readonly >
			</td>
		</tr>
		<tr>
			<td>
				Fin del ejercicio
			</td>
			<td>
				<input type='text' class="nminputtext" readonly id='fecha_fin'>
			</td>
		</tr>
	</table>
	<div class='nmcatalogbusquedatit' style='margin-top:30px;'>Periodos contables</div>
	<table>
		<tr>
			<td>Tipo de periodo</td>
			<td>
				Mensual<input type='hidden' name='period' id='period' value='m'>
			</td>
		</tr>
		<tr>
			<td>
				Periodos del ejercicio
			</td>
			<td>
				<label>12</label>
				<input type='hidden' name='periods' value='12'>
			</td>
		</tr>
		<tr>
			<?php
			if(!isset($data['PeriodoActual']))
			{
				$data['PeriodoActual'] = '1';
			}
			?>
			<td>
				Periodo vigente
			</td>
			<td>
				<input type='text' class="nminputtext" value='<?php echo $data['PeriodoActual'];?>' name='current_period' id='current_period' size='2'  onchange='cambia_periodo()' onkeypress="return validar_let(event)" maxlength='2' >
			</td>
		</tr>
		<tr>
			<td>
				Del <label id='inicio_mes'></label>
			</td>
			<td>
				Al <label id='fin_mes'></label>
				<input type='hidden' name='primera_vez' value='<?php echo $data['PrimeraVez']; ?>'>
			</td>
		</tr>
		<tr>
			<?php
			$checked;
			if(@$data['PeriodosAbiertos'])
			{
				$checked='checked';
			}
			?>
			<td>
				<input type='checkbox' value='1' name='open_periods' <?php echo @$checked; ?> >
				<b>Manejar periodos abiertos</b>
			</td>
			<td></td>
		</tr>
	</table>
</div>
<div id='buttons'>
	<input type='submit' class="nminputbutton" name='save' onclick="$('#nmloader_div',window.parent.document).show();" value='Guardar'>
	&nbsp;
	<input type='button' class="nminputbutton" name='cancel' value='Cancelar' onclick='regresar()'>
</div>
</form>