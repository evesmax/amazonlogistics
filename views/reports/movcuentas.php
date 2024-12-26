<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="../cont/js/select2/select2.min.js"></script>
<script type="text/javascript" src="../../../webapp/netwarelog/repolog/js/calendar.js"></script>
<link rel="stylesheet" type="text/css" href="../../../webapp/netwarelog/catalog//css/view.css" />
<link rel="stylesheet" type="text/css" href="../../../webapp/netwarelog/utilerias/css_repolog/estilo-1.css" />
<link rel="stylesheet" type="text/css" href="../cont/js/select2/select2.css" />
<?php include('../../netwarelog/design/css.php');?>
<LINK href="../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->
<link rel="stylesheet" type="text/css" href="css/style.css" />
<script languahe='javascript'>
$(function()
 {
 	 $("#cuentas").select2({
				 width : "250px",
				 placeholder:"Selecciona una o varias cuentas"
				});
 });

function valida(f)
		{
			
			if(!$('#cuentas').val())
			{
				alert("Elija al menos una cuenta");
				f.cuentas.focus();
				return false;
			}
			
			if($('#cuentas').val().length > 2 && f.rango.checked)
			{
				alert("El Rango requiere solo dos cuentas");
				f.cuentas.focus();
				return false;
			}
			
			if($('#cuentas').val().length < 2 && f.rango.checked)
			{
				alert("El Rango requiere de dos cuentas ");
				f.cuentas.focus();
				return false;
			}
			
		}
</script>
<?php
if(intval(isset($_GET['t'])))
{
	$titulo = "De Mayor";
	$otro = "Afectables";
	$liga = "index.php?c=Reports&f=movcuentas";
}
else
{
	$titulo = "Afectables";
	$otro = "De Mayor";
	$liga = "index.php?c=Reports&f=movcuentas&t=1";
}

//Nuevo commit
?>
<div class="repTitulo">Reporte de Movimientos por Cuentas (<?php echo $titulo; ?>)</div>
<div class="per">
	<form name='mov' method='post' id='info' action='index.php?c=Reports&f=movcuentas_despues' onsubmit='return valida(this)'>
	<ul>
		<li><strong><a href='<?php echo $liga; ?>' >Buscar por Cuentas <?php echo $otro; ?></a></strong></li>
		<li><label>Numero de Cuenta</label><select name='cuentas[]' multiple id='cuentas' class="nminputselect" style='width:250px !important;'>
					<option value='todos'>Todos</option>
					<?php
					while($cuentas = $listaCuentas->fetch_object())
					{
						echo "<option value='".$cuentas->account_id."'>".$cuentas->description."(".$cuentas->manual_code.")</option>";
					}
					?>
				</select></li>
		<li><label>Rango de Cuentas</label><input type='checkbox' value='1' name='rango' id='rango' class="nminputcheck"></li>	
		<li><label>Del:</label><div style="margin-left:42%"><div style="float:left;"><input id="f3_2" name="f3_2" title="Día" size="2" maxlength="2" value="<?php echo date("d"); ?>" type="text" class="nminputtext"> / <input id="f3_1" name="f3_1" title="Mes" size="2" maxlength="2" value="<?php echo date("m"); ?>" type="text" class="nminputtext"> / <input id="f3_3" name="f3_3" title="Año" size="4" maxlength="4" value="<?php echo date("Y"); ?>" type="text" class="nminputtext"> </div> <img id="f3_img" style="float:left;" class="datepicker" src="../../../webapp/netwarelog/repolog/img/calendar.gif" alt="Seleccione una fecha." title="Haga clic para seleccionar una fecha."></div></li>
		<li><label>Al:</label><div style="margin-left:42%"><div style="float:left"><input id="f4_2" name="f4_2" title="Día" size="2" maxlength="2" value="<?php echo date("d"); ?>" type="text" class="nminputtext"> / <input id="f4_1" name="f4_1" title="Mes" size="2" maxlength="2" value="<?php echo date("m"); ?>" type="text" class="nminputtext"> / <input id="f4_3" name="f4_3" title="Año" size="4" maxlength="4" value="<?php echo date("Y"); ?>" type="text" class="nminputtext"></div> <img id="f4_img" style="float:left;" class="datepicker" src="../../../webapp/netwarelog/repolog/img/calendar.gif" alt="Seleccione una fecha." title="Haga clic para seleccionar una fecha."></div></li>
		<li><label>Saldos</label><input type='checkbox' id="saldos" name="saldos" title="Saldos" value='1'></li>
		<li><label></label><input type='hidden' name='tipo' value='<?php echo $_GET['t']; ?>'><input type='submit' class="nminputbutton" name='envia'></li>
	</ul>
	</form>
</div>


<!--form name='mov' method='post' action='index.php?c=Reports&f=movcuentas_despues'>
	<table class='reporte'>
		<tbody>
		<tr class='trcontenido'><td class='tdcontenido'>Numero_Cuenta</td>
			<td class='tdcontenido'>
				
			</td></tr>
		<tr class='trcontenido'><td class='tdcontenido'>Del</td><td class='tdcontenido'></td></tr>
		<tr class='trcontenido'><td class='tdcontenido'>Al</td><td class='tdcontenido'></td></tr>
		<tr><th colspan='2' align='right'></th></tr>
	</tbody>
	</table>
</form-->
<script type="text/javascript">
															Calendar.setup({
																inputField	 : 'f3_3',
																baseField    : 'f3',
																displayArea  : 'f3_area',
																button		 : 'f3_img',
																ifFormat	 : '%B %e, %Y',
																onSelect	 : selectDate
															});

															Calendar.setup({
																inputField	 : 'f4_3',
																baseField    : 'f4',
																displayArea  : 'f4_area',
																button		 : 'f4_img',
																ifFormat	 : '%B %e, %Y',
																onSelect	 : selectDate
															});
														</script>
