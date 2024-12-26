<script src="js/jquery-1.10.2.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="../posclasico/js/date.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="../posclasico/js/datepicker_cash.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="js/moment.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src='js/select2/select2.min.js'></script>
<script language='javascript'>
function valida(cont)
{
	//alert($("#proveedores").val());
	if($("#clientes").val() == '' || !$("#clientes").val())
	{
		alert('Agregue una cuenta a clientes o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#clientes').focus();
    	return false;
	}

	if($("#ventas").val() == '' || !$("#ventas").val())
	{
		alert('Agregue una cuenta a ventas o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#ventas').focus();
    	return false;
	}

	if($("#IVA").val() == '' || !$("#IVA").val())
	{
		alert('Agregue una cuenta a IVA o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#IVA').focus();
    	return false;
	}

	if($("#caja").val() == '' || !$("#caja").val())
	{
		alert('Agregue una cuenta a caja o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#caja').focus();
    	return false;
	}

	/*if($("#TR").val() == '' || !$("#TR").val())
	{
		alert('Agregue una cuenta a tarjeta de regalo o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#TR').focus();
    	return false;
	}*/

	if($("#bancos").val() == '' || !$("#bancos").val())
	{
		alert('Agregue una cuenta a bancos o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#bancos').focus();
    	return false;
	}

	/*if($("#compras").val() == '' || !$("#compras").val())
	{
		alert('Agregue una cuenta a compras o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#compras').focus();
    	return false;
	}

	if($("#devoluciones").val() == '' || !$("#devoluciones").val())
	{
		alert('Agregue una cuenta a devoluciones o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#devoluciones').focus();
    	return false;
	}*/

	if($("#capital").val() == '' || !$("#capital").val())
	{
		alert('Agregue una cuenta a capital o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#capital').focus();
    	return false;
	}

	/*if($("#flujo").val() == '' || !$("#flujo").val())
	{
		alert('Agregue una cuenta a flujo o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#flujo').focus();
    	return false;
	}*/
	
	if($("#proveedores").val() == '' || !$("#proveedores").val())
	{
		alert('Agregue una cuenta a proveedores o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#proveedores').focus();
    	return false;
	}
	if($("#utilidad").val() == '' || !$("#utilidad").val())
	{
		alert('Agregue una cuenta a Utilidad o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#utilidad').focus();
    	return false;
	}
	if($("#perdida").val() == '' || !$("#perdida").val())
	{
		alert('Agregue una cuenta para Perdida o cancele la operacion')
    	// seleccionamos el campo incorrecto
    	$('#perdida').focus();
			return false;
	}
	if ($("#ivapendientepago").val() == '' || !$("#ivapendientepago").val()) {

		alert('Agregue una cuenta para IVA Acreditable Pendiente de pago')

		// seleccionamos el campo incorrecto

		$('#ivapendientepago').focus();

		return false;

	}

	if ($("#ivapagado").val() == '' || !$("#ivapagado").val()) {

		alert('Agregue una cuenta para IVA Acreditable Pagado')

		// seleccionamos el campo incorrecto

		$('#ivapagado').focus();

		return false;

	}

	if ($("#ivapendientecobro").val() == '' || !$("#ivapendientecobro").val()) {

		alert('Agregue una cuenta para IVA Trasladado Pendiente de cobro')

		// seleccionamos el campo incorrecto

		$('#ivapendientecobro').focus();

		return false;

	}

	if ($("#ivacobrado").val() == '' || !$("#ivacobrado").val()) {

		alert('Agregue una cuenta para IVA Trasladado Cobrado')

		// seleccionamos el campo incorrecto

		$('#ivacobrado').focus();

		return false;

	}

	if ($("#iepspendientepago").val() == '' || !$("#iepspendientepago").val()) {

		alert('Agregue una cuenta para IEPS Acreditable Pendiente de pago')

		// seleccionamos el campo incorrecto

		$('#iepspendientepago').focus();

		return false;

	}

	if ($("#iepspagado").val() == '' || !$("#iepspagado").val()) {

		alert('Agregue una cuenta para IEPS Acreditable Pagado')
		$('#iepspagado').focus();

		return false;

	}

	if ($("#iepspendientecobro").val() == '' || !$("#iepspendientecobro").val()) {

		alert('Agregue una cuenta para IEPS Trasladado Pendiente de cobro')
		$('#iepspendientecobro').focus();

		return false;

	}

	if ($("#iepscobrado").val() == '' || !$("#iepscobrado").val()) {
		alert('Agregue una cuenta para IEPS Trasladado Cobrado')
		$('#iepscobrado').focus();
			return false;

	}
	if ($("#deudores").val() == '' || !$("#deudores").val()) {
		alert('Agregue una cuenta para Deudores')
		$('#deudores').focus();
		return false;

	}
// 
	if ($("#ish").val() == '' || !$("#ish").val()) {
		alert('Agregue una cuenta para ISH')
		$('#ish').focus();

		return false;

	}
	//retenciones
	if ($("#ivaretenido").val() == '' || !$("#ivaretenido").val()) {

		alert('Agregue una cuenta para IVA retenido')
		$('#ivaretenido').focus();

		return false;

	}
	if ($("#isrretenido").val() == '' || !$("#isrretenido").val()) {

		alert('Agregue una cuenta para ISR ')
		$('#isrretenido').focus();

		return false;

	}
	if ($("#iepsgasto").val() == '' || !$("#iepsgasto").val()) {

		alert('Agregue una cuenta para el Gasto  ')
		$('#iepsgasto').focus();

		return false;

	}

}
</script>
<link rel="stylesheet" href="js/select2/select2.css">
<style>
a{
	display:none;
}
.nminputbutton_color2
{
	width:250px;
}
</style>
<?php



	$optionsList = "<option value='-1'>NINGUNO</option>";
	while($Cuentas = $Accounts->fetch_array())
	{
		$optionsList .= "<option value='".$Cuentas['account_id']."'>".$Cuentas['description']."(".$Cuentas['manual_code'].")</option>";
	}
	$circulantelist = "<option value='-1'>NINGUNO</option>";
	while($row = $circulante->fetch_array())
	{
		$circulantelist .= "<option value='".$row['account_id']."'>".$row['description']."(".$row['manual_code'].")</option>";
	}
	$gastoList = "<option value='-1'>NINGUNO</option>";
	while($row = $ishgasto->fetch_array())
	{
		$gastoList .= "<option value='".$row['account_id']."'>".$row['description']."(".$row['manual_code'].")</option>";
	}
	

	

?>
<form name='newCompany' method='post' action='index.php?c=Config&f=saveConfigAccounts' onsubmit='return valida(this)'>
	<div id='title'>Asignacion de Cuentas.</div>
	<table>
		<tr>
			<td>Nombre de la organizaci&oacute;n: </td>
			<td><input type='text' class="nminputtext" name='NameCompany' size='50' readonly value='<?php echo $name['nombreorganizacion']; ?>'></td>
		</tr>
		<tr>
			<td>Ejercicio Vigente: </td>
			<td><input type='text'  class="nminputtext" name='NameExercise' size='50' readonly value='<?php echo $data['EjercicioActual']; ?>'></td>
		</tr>
	</table>
	<div class="lateral">
		<div class='nmcatalogbusquedatit'>Selecciona la cuenta de mayor</div>
		<table width='100%'>
			<tr>
				<td>
					<label for="clientes">Cuenta de Clientes:</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showClientes" data-id='clientes' value='Asignar cuenta de clientes'>
					<div id="hideclientes">
						<select name='clientes' id='clientes' class='s2'>
							<?php
								echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelclientes" data-id="clientes" value='Cancelar'>
					</div>
				</td>
				<td><label title='nada' style='font-wenght:bold;'>?</label></td>
			</tr>
			<tr>
				<td>
					<label for="ventas">Cuenta de Ventas:</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showVentas" data-id='ventas' value='Asignar cuenta de ventas'>
					<div id="hideventas">
						<select name='ventas' id='ventas' class='s2'>
							<?php
								echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelventas" data-id="ventas" value='Cancelar'>
					</div>
				</td>
				<td><label title='nada' style='font-wenght:bold;'>?</label></td>
			</tr>
			<tr>
				<td>
					<label for="IVA">Cuenta de IVA:</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showIva" data-id='IVA' value='Asignar cuenta de IVA'>
					<div id="hideIVA">
						<select name='IVA' id='IVA' class='s2'>
							<?php
								echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelIVA" data-id="IVA" value='Cancelar'>
					</div>
				</td>
				<td><label title='nada' style='font-wenght:bold;'>?</label></td>
			</tr>
			<tr>
				<td>
					<label for="caja">Cuenta de Caja:</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showCaja" data-id='caja' value='Asignar cuenta de Caja'>
					<div id="hidecaja">
						<select name='caja' id='caja' class='s2'>
							<?php
								echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelcaja" data-id="caja" value='Cancelar'>
					</div>
				</td>
				<td><label title='nada' style='font-wenght:bold;'>?</label></td>
			</tr>
			<!--<tr>
				<td>
					<label for="TR">Cuenta de Tarjetas de regalo:</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showTr" data-id='TR' value='Asignar cuenta de tarjeta de regalo'>
					<div id="hideTR">
						<select name='TR' id='TR' class='s2'>
							<?php
								//echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelTR" data-id="TR" value='Cancelar'>
					</div>
				</td>
			</tr>-->
			<tr>
				<td>
					<label for="bancos">Cuenta de Bancos:</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showBancos" data-id='bancos' value='Asignar cuenta de Bancos'>
					<div id="hidebancos">
						<select name='bancos' id='bancos' class='s2'>
							<?php
								echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelbancos" data-id="bancos" value='Cancelar'>
					</div>
				</td>
				<td><label title='nada' style='font-wenght:bold;'>?</label></td>
			</tr>
			<!--<tr>
				<td>
					<label for="compras">Cuenta de Compras:</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showCompras" data-id='compras' value='Asignar cuenta de Compras'>
					<div id="hidecompras">
						<select name='compras' id='compras' class='s2'>
							<?php
							//	echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelcompras" data-id="compras" value='Cancelar'>
					</div>
				</td>
			</tr>
			
			<tr>
				<td>
					<label for="devoluciones">Cuenta de Devoluciones</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showDevoluciones" data-id="devoluciones" value='Asignar Cuenta de Devoluciones'>
					<div id="hidedevoluciones">
						<select name='devoluciones' id='devoluciones' class='s2'>
							<?php
								//echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="canceldev" data-id="devoluciones" value='Cancelar'>
					</div>
				</td>
			</tr>-->
			<tr>
				<td>
					<label for="capital">Cuenta de Capital (Saldos ejercicios)</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showCapital" data-id="capital" value='Asignar Cuenta de Saldo ejercicios'>
					<div id="hidecapital">
						<select name='capital' id='capital' class='s2'>
							<?php
								echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelcap" data-id="capital" value='Cancelar'>
					</div>
				</td>
				<td><label title='nada' style='font-wenght:bold;'>?</label></td>
			</tr>
			<!--<tr>
				<td>
					<label for="flujo">Cuenta de flujo de efectivo</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showFlujo" data-id="flujo" value='Asignar Cuenta de Flujo de efectivo'>
					<div id="hideflujo">
						<select name='flujo' id='flujo' class='s2'>
							<?php
								//echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelflu" data-id="flujo" value='Cancelar'>
					</div>
				</td>
			</tr>-->
			
			<tr>
				<td>
					<label for="proveedores">Cuenta de Proveedores</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showProveedores" data-id="proveedores" value='Asignar Cuenta de Proveedores'>
					<div id="hideproveedores">
						<select name='proveedores' id='proveedores' class='s2'>
							<?php
								echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelpro" data-id="proveedores" value='Cancelar'>
					</div>
				</td>
				<td><a href="javascript:alert('nada de nada')">?</a></td>
			</tr>
			<!-- nuevo para utilidad y perdida -->
			<tr>
				<td>
					<label for="utilidad">Utilidad en cambios</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showUtilidad" data-id="utilidad" value='Asignar Cuenta de Utilidad'>
					<div id="hideutilidad">
						<select name='utilidad' id='utilidad' class='s2'>
							<?php
								echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelutilidad" data-id="utilidad" value='Cancelar'>
					</div>
				</td>
				<td></td>
			</tr>	
			
			<tr>
				<td>
					<label for="perdida">Perdida en cambios</label>
				</td>
				<td>
					<input type='button' class="nminputbutton_color2" id="showPerdida" data-id="perdida" value='Asignar Cuenta de Perdida'>
					<div id="hideperdida">
						<select name='perdida' id='perdida' class='s2'>
							<?php
								echo $optionsList;
							?>
						</select>
						<input type="button" class="nminputbutton_color2" id="cancelperdida" data-id="perdida" value='Cancelar'>
					</div>
				</td>
				<td></td>
			</tr>
			<tr>

				<td><label for="deudores">Deudores</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showDeudores"  data-id="deudores" value='Asignar Cuenta Deudores'>
				<div id="hidedeudores">

					<select name='deudores' id='deudores' class='s2'>

						<?php
						echo $optionsList;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="canceldeudores" data-id="deudores" value='Cancelar'>

				</div></td>

				<td></td>

			</tr>
			<tr>
				<td colspan="2">
				</td>
			</tr>
		</table>
		<tr>
				<td colspan="3"><div class="nmcatalogbusquedatit">Selecciona la cuenta Afectable</div></td>
			</tr>
		<input type="checkbox" id="defaultimp" name="defaultimp" value="1" onclick="defaultimpuesto();" /><label class="nmwatitles">Asignar cuenta IVA,IEPS por default</label>
		<div id="cuentaivaieps" style="display: none;">
			<table width='100%'>
			<!-- <tr>
				<td colspan="3"><div class="nmcatalogbusquedatit">Selecciona la cuenta Afectable</div></td>
			</tr> -->
			<tr>

				<td><label for="ivapendientepago">IVA Acreditable Pendiente de pago</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIvapendientepago"  data-id="ivapendientepago" value='Asignar IVA A.P.P'>
				<div id="hideivapendientepago">

					<select name='ivapendientepago' id='ivapendientepago' class='s2'>

						<?php
							echo $circulantelist;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="cancelivapendientepago" data-id="ivapendientepago" value='Cancelar'>

				</div></td>

				<td></td>

			</tr>

			<tr>

				<td><label for="ivapagado">IVA Acreditable Pagado</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIvapagado"  data-id="ivapagado" value='Asignar IVA A.P.'>
				<div id="hideivapagado">

					<select name='ivapagado' id='ivapagado' class='s2'>

						<?php
							echo $circulantelist;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="cancelivapagado" data-id="ivapagado" value='Cancelar' >

				</div></td>

				<td></td>

			</tr>

			<tr>
				<td>
					<label for="ivapendientecobro">IVA Trasladado Pendiente de cobro</label>
				</td>
				<td>
				<input type='button' class="nminputbutton_color2" id="showIvapendientecobro"  data-id="ivapendientecobro" value='Asignar IVA T.P.C'>
				<div id="hideivapendientecobro">
					<select name='ivapendientecobro' id='ivapendientecobro' class='s2'>
						<?php
							echo $circulantelist;
						?>
					</select>
					<input type="button" class="nminputbutton_color2" id="cancelivapendientecobro" data-id="ivapendientecobro" value='Cancelar'>
				</div>
				</td>
				
			</tr>
			<tr>

				<td><label for="ivacobrado">IVA Trasladado Cobrado</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIvacobrado"  data-id="ivacobrado" value='Asignar IVA T.C.'>
				<div id="hideivacobrado">

					<select name='ivacobrado' id='ivacobrado' class='s2'>

						<?php
							echo $circulantelist;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="cancelivacobrado" data-id="ivacobrado" value='Cancelar'>

				</div></td>

				<td></td>

			</tr>

		</table>
		</div>
		
			<div id="calculaieps" style="display:none" >
					<table width='100%'>
				<tr>

				<td><label for="iepspendientepago">IEPS Acreditable Pendiente de pago</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIepspendientepago"  data-id="iepspendientepago" value='Asignar IEPS A.P.P'>
				<div id="hideiepspendientepago">

					<select name='iepspendientepago' id='iepspendientepago' class='s2'>

						<?php
							echo $circulantelist;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="canceliepspendientepago" data-id="iepspendientepago" value='Cancelar'>

				</div></td>

				<td></td>

			</tr>

			<tr>

				<td><label for="iepspagado">IEPS Acreditable Pagado</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIepspagado"  data-id="iepspagado" value='Asignar IEPS A.P.'>
				<div id="hideiepspagado">

					<select name='iepspagado' id='iepspagado' class='s2'>

						<?php
							echo $circulantelist;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="canceliepspagado" data-id="iepspagado" value='Cancelar'>

				</div></td>

				<td></td>

			</tr>

			<tr>

				<td><label for="iepspendientecobro">IEPS Trasladado Pendiente de cobro</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIepspendientecobro"  data-id="iepspendientecobro" value='Asignar IEPS T.P.C'>
				<div id="hideiepspendientecobro">

					<select name='iepspendientecobro' id='iepspendientecobro' class='s2'>

						<?php
							echo $circulantelist;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="canceliepspendientecobro" data-id="iepspendientecobro" value='Cancelar'>

				</div></td>

				<td></td>

			</tr>

			<tr>

				<td><label for="iepscobrado">IEPS Trasladado Cobrado</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIepscobrado"  data-id="iepscobrado" value='Asignar IEPS T.C'>
				<div id="hideiepscobrado">

					<select name='iepscobrado' id='iepscobrado' class='s2'>

						<?php
						echo $circulantelist;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="canceliepscobrado" data-id="iepscobrado" value='Cancelar'>

				</div></td>

				<td></td>

			</tr>
			</table>
		</div>
		<br>
		<input type="checkbox" id="defaultieps" name="defaultieps" value="1" onclick="defaultiepscuenta();" /><label class="nmwatitles">Calcula IEPS</label>

		<div id="nocalculaieps" style="display: none">
			<table width='100%'>
			<tr>

				<td><label for="iepsgasto">Cuenta de Gasto</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIepsgasto"  data-id="iepsgasto" value='Asignar Cuenta de Gasto'>
				<div id="hideiepsgasto">

					<select name='iepsgasto' id='iepsgasto' class='s2'>

						<?php
						echo $gastoList;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="canceliepsgasto" data-id="iepsgasto" value='Cancelar'>

				</div></td>

				<td></td>

			</tr>
			</table>
		</div>
		
		<br>
		<input type="checkbox" id="retencion" name="retencion" value="1" onclick="retenshowhide();"/><label class="nmwatitles">Asignar cuenta ISH y Retenciones por default</label>
		<div id="retencionhideshow" style="display: none;">
		<table width='100%'>
			<tr>

				<td width="45%"><label for="ish">ISH</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIsh"  data-id="ish" value='Asignar ISH'>
				<div id="hideish">

					<select name='ish' id='ish' class='s2'>

						<?php
							echo $gastoList;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="cancelish" data-id="ish" value='Cancelar'>

				</div></td>

				<td></td>

			</tr>
			<tr><td colspan="3"><div class="nmcatalogbusquedatit">Selecciona cuenta para Retenciones</div></td></tr>
			<tr>
				<td width="45%"><label for="ivaretenido">IVA Retenido</label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIvaretenido"  data-id="ivaretenido" value='Asignar Retencion'>
				<div id="hideivaretenido">

					<select name='ivaretenido' id='ivaretenido' class='s2'>

						<?php
							echo $circulantelist;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="cancelivaretenido" data-id="ivaretenido" value='Cancelar'>

				</div></td>

				<td></td>
			</tr>
			<tr>
				<td width="45%"><label for="ivaretenido">ISR </label></td>

				<td>
				<input type='button' class="nminputbutton_color2" id="showIsrretenido"  data-id="isrretenido" value='Asignar Retencion'>
				<div id="hideisrretenido">

					<select name='isrretenido' id='isrretenido' class='s2'>

						<?php
							echo $circulantelist;
						?>
					</select>

					<input type="button" class="nminputbutton_color2" id="cancelisrretenido" data-id="isrretenido" value='Cancelar'>

				</div></td>

				<td></td>
			</tr>
				
		</table>
	</div>
</div>
	<div id='buttons'>
		<input type='submit' name='save'  class="nminputbutton" value='Guardar'>
	</div>
</form>
<script>
	$(document).ready(function(){
		// Inicia seccion de asignacion de valores iniciales
			/*jshint ignore:start*/
				<?php
					$datos  = ( $data['CuentaClientes'] 	 != -1) ? "$( '#clientes'     ).val( " . $data['CuentaClientes'] . ");" : "";
					$datos .= ( $data['CuentaVentas']  		 != -1) ? "$( '#ventas'       ).val( " . $data['CuentaVentas']   . ");" : "";
					$datos .= ( $data['CuentaIVA']  		 != -1) ? "$( '#IVA'          ).val( " . $data['CuentaIVA']      . ");" : "";
					$datos .= ( $data['CuentaCaja']  		 != -1) ? "$( '#caja'         ).val( " . $data['CuentaCaja']     . ");" : "";
					$datos .= ( $data['CuentaTR']  			 != -1) ? "$( '#TR'           ).val( " . $data['CuentaTR']     	 . ");" : "";
					$datos .= ( $data['CuentaBancos']  		 != -1) ? "$( '#bancos'       ).val( " . $data['CuentaBancos']   . ");" : "";
					$datos .= ( $data['CuentaCompras']  	 != -1) ? "$( '#compras'      ).val( " . $data['CuentaCompras']  . ");" : "";
					$datos .= ( $data['CuentaDev']  		 != -1) ? "$( '#devoluciones' ).val( " . $data['CuentaDev']      . ");" : "";
					$datos .= ( $data['CuentaSaldos']  		 != -1) ? "$( '#capital' 	  ).val( " . $data['CuentaSaldos']   . ");" : "";
					$datos .= ( $data['CuentaFlujoEfectivo'] != -1) ? "$( '#flujo'		  ).val( " . $data['CuentaFlujoEfectivo'] . ");" : "";
					$datos .= ( $data['CuentaProveedores']   != -1) ? "$( '#proveedores'  ).val( " . $data['CuentaProveedores']   . ");" : "";
					$datos .= ( $data['CuentaUtilidad']   != -1) ? "$( '#utilidad'  ).val( " . $data['CuentaUtilidad']   . ");" : "";
					$datos .= ( $data['CuentaPerdida']   != -1) ? "$( '#perdida'  ).val( " . $data['CuentaPerdida']   . ");" : "";
					$datos .= ( $data['CuentaIVAPendientePago']   != -1) ? "$( '#ivapendientepago'  ).val( " . $data['CuentaIVAPendientePago']   . ");" : "";
					$datos .= ( $data['CuentaIVApagado']   != -1) ? "$( '#ivapagado'  ).val( " . $data['CuentaIVApagado']   . ");" : "";
					$datos .= ( $data['CuentaIVAPendienteCobro']   != -1) ? "$( '#ivapendientecobro'  ).val( " . $data['CuentaIVAPendienteCobro']   . ");" : "";
					$datos .= ( $data['CuentaIVAcobrado']   != -1) ? "$( '#ivacobrado'  ).val( " . $data['CuentaIVAcobrado']   . ");" : "";
					$datos .= ( $data['CuentaIEPSPendientePago']   != -1) ? "$( '#iepspendientepago'  ).val( " . $data['CuentaIEPSPendientePago']   . ");" : "";
					$datos .= ( $data['CuentaIEPSpagado']   != -1) ? "$( '#iepspagado'  ).val( " . $data['CuentaIEPSpagado']   . ");" : "";
					$datos .= ( $data['CuentaIEPSPendienteCobro']   != -1) ? "$( '#iepspendientecobro'  ).val( " . $data['CuentaIEPSPendienteCobro']   . ");" : "";
					$datos .= ( $data['CuentaIEPScobrado']   != -1) ? "$( '#iepscobrado'  ).val( " . $data['CuentaIEPScobrado']   . ");" : "";
					$datos .= ( $data['CuentaDeudores']  != -1) ? "$( '#deudores'  ).val( " . $data['CuentaDeudores'] . ");" : "";
					$datos .= ( $data['ISH']   != -1) ? "$( '#ish'  ).val( " . $data['ISH']   . ");" : "";
					$datos .= ( $data['ISRretenido']   != -1) ? "$( '#isrretenido'  ).val( " . $data['ISRretenido']   . ");" : "";
					$datos .= ( $data['IVAretenido']   != -1) ? "$( '#ivaretenido'  ).val( " . $data['IVAretenido']   . ");" : "";
					$datos .= ( $data['CuentaIEPSgasto']   != -1) ? "$( '#iepsgasto'  ).val( " . $data['CuentaIEPSgasto']   . ");" : "";
					$datos .= (	$data['statusIVAIEPS']   != 0) ? "$( '#defaultimp'  ).prop('checked',true);$('#cuentaivaieps').show(); " : "";
					$datos .= (	$data['statusRetencionISH']   != 0) ? " $( '#retencion'  ).prop('checked',true);$('#retencionhideshow').show(); " : "";
					$datos .= ( $data['statusIEPS'] !=0 ) ? " $( '#defaultieps ').prop('checked',true); $('#calculaieps').show();$('#nocalculaieps').hide(); " : "";
					$datos .= ( $data['statusIEPS'] ==0 ) ? "  $( '#defaultieps ').prop('checked',false); $('#nocalculaieps').show(); $('#calculaieps').hide();" : "";
			echo $datos;
				?>
			/* jshint ignore:end*/
		// Termina seccion de asignacion de valores iniciales

		// Inicia seccion de control de botones de modificacion
			$("select.s2").each(function(){
				if( parseInt( $(this).val(),10 ) !== -1 )
				{
					$(this).attr("disabled",'disabled').after("<input type='hidden' name='" + $(this).attr("name") + "' value='" + $(this).val() + "'>");
					//$(":button[data-id=" + $(this).attr("id") + "]").remove();
					$(":button[data-id=" + $(this).attr("id") + "]").hide();
				}
				else
				{
					$("#compras,#ventas,#devoluciones,#clientes,#IVA,#caja,#TR,#bancos,#capital,#flujo,#proveedores,#utilidad,#perdida,#ivapendientepago,#ivapagado,#ivapendientecobro,#ivacobrado,#iepspendientepago,#iepspagado,#iepspendientecobro,#iepscobrado,#deudores,#ish,#isrretenido,#ivaretenido,#iepsgasto").select2({"width":"300px"});
					$("#hide"+$(this).attr("id")).hide();
				}
			});
		// Termina seccion de control de botones de modificacion

		// Inicia seccion de boton de modificacion de cuentas
			$("#showCompras,#showVentas,#showDevoluciones,#showClientes,#showIva,#showCaja,#showTr,#showBancos,#showCapital,#showFlujo,#showProveedores,#showPerdida,#showUtilidad,#showUtilidad,#showIvapendientepago,#showIvapagado,#showIvapendientecobro,#showIvacobrado,#showIepspendientepago,#showIepspagado,#showIepspendientecobro,#showIepscobrado,#showDeudores,#showIsh,#showIvaretenido,#showIsrretenido,#showIepsgasto").click(function(){
				if(confirm("Esta seguro de asignar la cuenta de " + $(this).data("id") + "?.\nEsta accion no podra ser modificada en ningun momento."))
				{
					$("#hide" + $(this).data("id") ).show();
					$( "#" + $(this).data('id') + " option[value=-1]" ).prop("disabled",true);
					$( "#" + $(this).data('id') ).select2({"width": "300px"});
					$(this).hide();
				}
			});
		// Termina seccion de boton de modificacion de cuentas

		// Inicia seccion de cancelacion de modificacion de cuentas
			$("#cancelcompras,#cancelventas,#canceldev,#cancelclientes,#cancelIVA,#cancelcaja,#cancelTR,#cancelbancos,#cancelcap,#cancelflu,#cancelpro,#cancelutilidad,#cancelperdida,#cancelivapendientepago,#cancelivapagado,#cancelivapendientecobro,#cancelivacobrado,#canceliepspendientepago,#canceliepspagado,#canceliepspendientecobro,#canceliepscobrado,#canceldeudores,#cancelish,#cancelisrretenido,#cancelivaretenido,#canceliepsgasto").click(function(){
				var type = ucFirst($(this).data("id"));
				$("#show" + type).show();
				$("#hide" + $(this).data("id") ).hide();
				$("#" + $(this).data('id') + " option[value=-1]").prop("disabled",false);
				$("#" + $(this).data('id') ).select2('destroy').select2({'width':'300px'});
				$('#' + $(this).data('id') ).val('-1');
			});
		// Termina seccion de cancelacion de modificacion de cuentas

		// Inicia Validacion de desigualdad
			$("#compras,#ventas,#devoluciones,#clientes,#IVA,#caja,#TR,#bancos,#capital,#flujo,#proveedores,#perdida,#utilidad,#ivapendientepago,#ivapagado,#ivapendientecobro,#ivacobrado,#iepspendientepago,#iepspagado,#iepspendientecobro,#iepscobrado,#deudores,#ish,#isrretenido,#ivaretenido,#iepsgasto").on('change',function(){
				//var compras = $("#compras").val();
				var compras = 0;
				var ventas  = $("#ventas").val();
				var IVA  = $("#IVA").val();
				var caja  = $("#caja").val();
//				var TR  = $("#TR").val();
				var TR  = 0;
				var bancos = $("#bancos").val();
				//var dev = $("#devoluciones").val();
				var dev = 0;
				var clientes = $("#clientes").val();
				var capital = $("#capital").val();
				//var flujo = $("#flujo").val();
				var flujo = 0;
				var proveedores = $('#proveedores').val();
				var utilidad = $('#utilidad').val();
				var perdida = $('#perdida').val();
				var ivapendientepago = $('#ivapendientepago').val();
				var ivapagado = $('#ivapagado').val();
				var ivapendientecobro = $('#ivapendientecobro').val();
				var ivacobrado = $('#ivacobrado').val();
				var iepspendientepago = $('#iepspendientepago').val();
				var iepspagado = $('#iepspagado').val();
				var iepspendientecobro = $('#iepspendientecobro').val();
				var iepscobrado = $('#iepscobrado').val();
				var deudores = $("#deudores").val();
				var ish = $('#ish').val();
				var isrretenido = $('#isrretenido').val();
				var ivaretenido = $('#ivaretenido').val();
				var iepsgasto = $('#iepsgasto').val();
// 				
				var id = $(this).attr('id');
				
				switch(id)
				{
					// en todas se quito la compracion con devoluciones || compras == devoluciones 
					case "compras":
						if(compras == ventas || compras == clientes || compras == IVA || compras == caja || compras == TR || compras == bancos || compras==proveedores || compras==utilidad || compras==perdida || compras==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						} 
						break;
					case "ventas":
						if(ventas == compras  || ventas == clientes || ventas == IVA || ventas == caja || ventas == TR || ventas == bancos || ventas==proveedores || ventas==utilidad || ventas==perdida || ventas==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;
					case "IVA":
						if(IVA == compras || IVA == clientes  || IVA == ventas || IVA == caja || IVA == TR || IVA == bancos || IVA==proveedores || IVA==utilidad || IVA==perdida || IVA==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;
					case "devoluciones":
						if(devoluciones == ventas || devoluciones == compras || devoluciones == clientes || devoluciones == IVA || devoluciones == caja || devoluciones == TR || devoluciones == bancos || devoluciones==proveedores || devoluciones==utilidad || devoluciones==perdida || devoluciones==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;
					case "clientes":
						if(clientes == ventas || clientes == compras  || clientes == IVA || clientes == caja || clientes == TR || clientes == bancos || clientes==proveedores || clientes==utilidad || clientes==perdida || clientes==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;	

					case "caja":
						
						if(caja == ventas || caja == compras || caja == IVA || caja == TR || caja == bancos || caja == clientes || caja==proveedores || caja==utilidad || caja==perdida || caja==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;	
						
					case "TR":
						if(TR == ventas || TR == compras || TR == IVA || TR == caja || TR == bancos || TR == clientes || TR==proveedores || TR==utilidad || TR==perdida || TR==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;	
						
					case "bancos":
						if(bancos == ventas || bancos == compras || bancos == IVA || bancos == TR || bancos == caja || bancos == clientes || bancos==proveedores || bancos==utilidad || bancos==perdida || bancos==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;	
					case "proveedores":
						if(proveedores == ventas || proveedores == compras  || proveedores == IVA || proveedores == TR || proveedores == caja || proveedores == clientes || proveedores == bancos || proveedores==utilidad || proveedores==perdida || proveedores==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;
					case "utilidad":
						if(utilidad == ventas || utilidad == compras  || utilidad == IVA || utilidad == TR || utilidad == caja || utilidad == clientes || utilidad == bancos || utilidad==proveedores || utilidad==perdida || utilidad==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;
					case "perdida":
				
						if(perdida == ventas || perdida == compras  || perdida == IVA || perdida == TR || perdida == caja || perdida == clientes || perdida == bancos || perdida==proveedores || perdida==utilidad || perdida==deudores)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;
						//AFECTABLES

					case "ivapendientepago":
				
					if (ivapendientepago == ivapagado || ivapendientepago == ivapendientecobro || ivapendientepago == ivacobrado || ivapendientepago == iepspendientepago || ivapendientepago == iepspagado || ivapendientepago == iepspendientecobro || ivapendientepago == iepscobrado || ivapendientepago==iepsgasto) {
				
						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();
				
					}
				
					break;
				
					case "ivapagado":
				
					if (ivapagado == ivapendientepago || ivapagado == ivapendientecobro || ivapagado == ivacobrado || ivapagado == iepspendientepago || ivapagado == iepspagado || ivapagado == iepspendientecobro || ivapagado == iepscobrado || ivapagado==iepsgasto) {
				
						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();
				
					}
				
					break;
				
					case "ivapendientecobro":
				
					if (ivapendientecobro == ivapendientepago || ivapendientecobro == ivapagado || ivapendientecobro == ivacobrado || ivapendientecobro == iepspendientepago || ivapendientecobro == iepspagado || ivapendientecobro == iepspendientecobro || ivapendientecobro == iepscobrado || ivapendientecobro == iepsgasto) {
				
						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();
				
					}
				
					break;
				
					case "ivacobrado":
				
					if (ivacobrado == ivapendientepago || ivacobrado == ivapagado || ivacobrado == ivapendientecobro || ivacobrado == iepspendientepago || ivacobrado == iepspagado || ivacobrado == iepspendientecobro || ivacobrado == iepscobrado || ivacobrado == iepsgasto) {
				
						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();
				
					}
				
					break;
				
					case "iepspendientepago":
				
					if (iepspendientepago == ivapendientepago || iepspendientepago == ivapagado || iepspendientepago == ivapendientecobro || iepspendientepago == ivacobrado || iepspendientepago == iepspagado || iepspendientepago == iepspendientecobro || iepspendientepago == iepscobrado || iepspendientepago == iepsgasto) {
				
						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();
				
					}
				
					break;
				
					case "iepspagado":
				
					if (iepspagado == ivapendientepago || iepspagado == ivapagado || iepspagado == ivapendientecobro || iepspagado == ivacobrado || iepspagado == iepspendientepago || iepspagado == iepspendientecobro || iepspagado == iepscobrado || iepspagado == iepsgasto) {
				
						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();
				
					}
				
					break;
				
					case "iepspendientecobro":
				
					if (iepspendientecobro == ivapendientepago || iepspendientecobro == ivapagado || iepspendientecobro == ivapendientecobro || iepspendientecobro == ivacobrado || iepspendientecobro == iepspendientepago || iepspendientecobro == iepspagado || iepspendientecobro == iepscobrado || iepspendientecobro == iepsgasto) {
				
						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();
				
					}
				
					break;
				
					case "iepscobrado":
				
					if (iepscobrado == ivapendientepago || iepscobrado == ivapagado || iepscobrado == ivapendientecobro || iepscobrado == ivacobrado || iepscobrado == iepspendientepago || iepscobrado == iepspagado || iepscobrado == iepspendientecobro || iepscobrado==deudores || iepscobrado == iepsgasto) {
				
						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();
				
					}
				
					break;
					case "deudores":
				
					if(deudores == ventas || deudores == compras  || deudores == IVA || deudores == TR || deudores == caja || deudores == clientes || deudores == bancos || deudores==proveedores || deudores==utilidad || deudores==perdida)
					{
						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
						$("#cancel" + id).click();
					}
					break;
					case "ish":
// 				
					if (ish == ivapendientepago || ish == ivapagado || ish == ivapendientecobro || ish == ivacobrado || ish == iepspendientepago || ish == iepspagado || ish == iepspendientecobro || ish == iepscobrado || ish==isrretenido || ish==ivaretenido || ish == iepsgasto) {

						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();

					}
// 
					break;
					//RETENCION
					case "isrretenido":
				
					if (isrretenido == ivapendientepago || isrretenido == ivapagado || isrretenido == ivapendientecobro || isrretenido == ivacobrado || isrretenido == iepspendientepago || isrretenido == iepspagado || isrretenido == iepspendientecobro || isrretenido == iepscobrado || isrretenido==ivaretenido || isrretenido==ish || isrretenido == iepsgasto) {

						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();

					}
// 
					break;
					case "ivaretenido":
				
					if (ivaretenido == ivapendientepago || ivaretenido == ivapagado || ivaretenido == ivapendientecobro || ivaretenido == ivacobrado || ivaretenido == iepspendientepago || ivaretenido == iepspagado || ivaretenido == iepspendientecobro || ivaretenido == iepscobrado || ivaretenido==isrretenido || ivaretenido==ish || ivaretenido == iepsgasto) {

						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();

					}

					break;
					case "iepsgasto":
				
					if (iepsgasto == ivapendientepago || iepsgasto == ivapagado || iepsgasto == ivapendientecobro || iepsgasto == ivacobrado || iepsgasto == iepspendientepago || iepsgasto == iepspagado || iepsgasto == iepspendientecobro || iepsgasto == iepscobrado || iepsgasto==isrretenido || iepsgasto==ish || iepsgasto == ivaretenido) {

						alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
				
						$("#cancel" + id).click();

					}

					break;
					
					
			}
	});
		// Termina Validacion de desigualdad		
	});
	function ucFirst(string) {
		return string.substring(0, 1).toUpperCase() + string.substring(1).toLowerCase();
    }
    function defaultimpuesto(){ 
	    	if($('#defaultimp').is(':checked')){
			$("#cuentaivaieps").show();
		}else{
			if(confirm("Se perderán las cuentas asignadas desea continuar?")){
				$("#cuentaivaieps").hide();
				$(":hidden[name='ivapendientepago'],:hidden[name='ivapagado'],:hidden[name='ivapendientecobro'],:hidden[name='ivacobrado']").val("-1");
				$("#cancelivapendientepago,#cancelivapagado,#cancelivapendientecobro,#cancelivacobrado").click();
				$("#cancelivapendientepago,#cancelivapagado,#cancelivapendientecobro,#cancelivacobrado").show();
				$("#ivapendientepago,#ivapagado,#ivapendientecobro,#ivacobrado").prop("disabled",false);
			}
		}
    }
    function retenshowhide(){//pp
    		if($('#retencion').is(':checked')){
			$("#retencionhideshow").show();
		}else{
			if(confirm("Se perderán las cuentas asignadas desea continuar?")){
				$("#retencionhideshow").hide();
				$(":hidden[name='ish'],:hidden[name='ivaretenido'],:hidden[name='isrretenido']").val("-1");
				$("#cancelish,#cancelisrretenido,#cancelivaretenido").click();
				$("#cancelish,#cancelisrretenido,#cancelivaretenido").show();
				$("#ish,#isrretenido,#ivaretenido").prop("disabled",false);
			}
			
		}
    }
    function defaultiepscuenta(){ 
	    	if($('#defaultieps').is(':checked')){
	    		if(confirm("Se perderán la cuenta de gasto asignada desea continuar?")){
	    			$("#calculaieps").show();
	    			$("#nocalculaieps").hide();
	    			$(":hidden[name='iepsgasto']").val("-1");
	    			$("#canceliepsgasto").click();
				//$("#canceliepsgasto").show();
				$("#iepsgasto").prop("disabled",false);
	    		}
			
		}else{
			if(confirm("Se perderán las cuentas asignadas desea continuar?")){
				$("#calculaieps").hide();
				$(":hidden[name='iepspendientepago'],:hidden[name='iepspagado'],:hidden[name='iepspendientecobro'],:hidden[name='iepscobrado']").val("-1");
				$("#canceliepspendientepago,#canceliepspagado,#canceliepspendientecobro,#canceliepscobrado").click();
				$("#canceliepspendientepago,#canceliepspagado,#canceliepspendientecobro,#canceliepscobrado").show();
				$("#iepspendientepago,#iepspagado,#iepspendientecobro,#iepscobrado").prop("disabled",false);
				$("#nocalculaieps").show();
			}
		}
				
    }
    
				
    
   
</script>