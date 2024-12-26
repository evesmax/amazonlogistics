<script src="js/jquery-1.10.2.min.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src='js/select2/select2.min.js'></script>
<style>
.nminputbutton_color2
{
	width:250px;
}
</style>
<script language='javascript'>
$(function()
 {
 guardando(0);
var anterior = $('#anterior_corte').val();
if(parseInt(anterior))
{
	$("#corte option[value='"+anterior+"']").attr("selected","selected");  
}
		// Inicia seccion de asignacion de valores iniciales
			/*jshint ignore:start*/
				<?php
					$datos  = ( $data['clientes'] 	!= -1) ? "$( '#clientes'     ).val( " . $data['clientes'] . ");" : "";
					$datos .= ( $data['ventas']  	!= -1) ? "$( '#ventas'       ).val( " . $data['ventas']   . ");" : "";
					$datos .= ( $data['iva']  		!= -1) ? "$( '#IVA'          ).val( " . $data['iva']      . ");" : "";
					$datos .= ( $data['caja']  		!= -1) ? "$( '#caja'         ).val( " . $data['caja']     . ");" : "";
					$datos .= ( $data['bancos']  	!= -1) ? "$( '#bancos'       ).val( " . $data['bancos']   . ");" : "";

					echo $datos;
				?>
			/* jshint ignore:end*/
		// Termina seccion de asignacion de valores iniciales

		// Inicia seccion de control de botones de modificacion
			$("select.s2").each(function(){
				if( parseInt( $(this).val(),10 ) !== -1 )
				{
					$(this).attr("disabled",'disabled').after("<input type='hidden' name='" + $(this).attr("name") + "' value='" + $(this).val() + "'>");
					$(":button[data-id=" + $(this).attr("id") + "]").remove();
				}
				else
				{
					$("#ventas,#clientes,#IVA,#caja,#bancos").select2({"width":"300px"});
					$("#hide"+$(this).attr("id")).hide();
				}
			});
		// Termina seccion de control de botones de modificacion

		// Inicia seccion de boton de modificacion de cuentas
			$("#showVentas,#showClientes,#showIva,#showCaja,#showBancos").click(function(){
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
			$("#cancelventas,#cancelclientes,#cancelIVA,#cancelcaja,#cancelbancos").click(function(){
				var type = ucFirst($(this).data("id"));
				$("#show" + type).show();
				$("#hide" + $(this).data("id") ).hide();
				$("#" + $(this).data('id') + " option[value=-1]").prop("disabled",false);
				$("#" + $(this).data('id') ).select2('destroy').select2({'width':'300px'});
				$('#' + $(this).data('id') ).val('-1');
			});
		// Termina seccion de cancelacion de modificacion de cuentas

		// Inicia Validacion de desigualdad
			$("#ventas,#clientes,#IVA,#caja,#bancos").on('change',function(){
				var ventas  = $("#ventas").val();
				var IVA  = $("#IVA").val();
				var caja  = $("#caja").val();
				var bancos = $("#bancos").val();
				var clientes = $("#clientes").val();
				var id = $(this).attr('id');
				
				switch(id)
				{
					case "ventas":
						if(ventas == clientes || ventas == IVA || ventas == caja || ventas == bancos)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;
					case "IVA":
						if(IVA == clientes  || IVA == ventas || IVA == caja || IVA == bancos)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;
					case "clientes":
						if(clientes == ventas || clientes == IVA || clientes == caja || clientes == bancos)
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;	

					case "caja":
						if(caja == ventas || caja == IVA || caja == bancos || caja == clientes )
						{
							alert("No debe Elegir cuentas repetidas para esta operacion. Cancelando...");
							$("#cancel" + id).click();
						}
						break;		
						
					case "bancos":
						if(bancos == ventas || bancos == IVA || bancos == caja || bancos == clientes )
						{
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

function valida(cont)
{
	//alert($("#clientes").val());
	if($("#clientes").val() == '' || !$("#clientes").val() || $("#clientes").val() == '-1')
	{
		alert('Agregue una cuenta a de clientes.')
    	// seleccionamos el campo incorrecto
    	$('#clientes').focus();
    	guardando(0);
    	return false;
	}

	if($("#ventas").val() == '' || !$("#ventas").val() || $("#ventas").val() == '-1')
	{
		alert('Agregue una cuenta de ventas.')
    	// seleccionamos el campo incorrecto
    	$('#ventas').focus();
    	guardando(0);
    	return false;
	}

	if($("#IVA").val() == '' || !$("#IVA").val() || $("#IVA").val() == '-1')
	{
		alert('Agregue una cuenta de IVA.')
    	// seleccionamos el campo incorrecto
    	$('#IVA').focus();
    	guardando(0);
    	return false;
	}

	/*if($("#caja").val() == '' || !$("#caja").val() || $("#caja").val() == '-1')
	{
		alert('Agregue una cuenta de caja.')
    	// seleccionamos el campo incorrecto
    	$('#caja').focus();
    	guardando(0);
    	return false;
	}


	if($("#bancos").val() == '' || !$("#bancos").val() || $("#bancos").val() == '-1')
	{
		alert('Agregue una cuenta de bancos.')
    	// seleccionamos el campo incorrecto
    	$('#bancos').focus();
    	guardando(0);
    	return false;
	}*/

	var anterior = $('#anterior_corte').val();
	var p = 0;
	if(!parseInt(anterior))
	{
		p = confirm('Esta seguro de guardar la configuracion?\nLa opcion del historial de ventas y las cuentas no podran ser modificados despues');
		if(!p)
		{
			guardando(0);
			return false
		}
	}

	if(parseInt(anterior))
	{
		p = confirm('Esta seguro de guardar los cambios a la configuracion?');
		if(!p)
		{
			guardando(0);
			return false
		}
	}	
}
function guardando(mostrar)
{
	if(mostrar)
	{
		$('#nmloader_div',window.parent.document).show();
	}
	else
	{
		$('#nmloader_div',window.parent.document).hide();
	}
}
</script>
<?php
if(intval($data['polizas_por']))
{
	$readonly = 'disabled';
}
$chkdHistorial1 = '';
$chkdHistorial2 = '';
$chkdConectar = '';
if(intval($data['historial']))
{
	$chkdHistorial1 = "checked";
}
else
{
	$chkdHistorial2 = "checked";
}
if(intval($data['conectar']))
{
	$chkdConectar = 'checked';
}
?>


<link rel="stylesheet" href="js/select2/select2.css">
<?php



	$optionsList = "<option value='-1'>NINGUNO</option>";
	while($Cuentas = $Accounts->fetch_array())
	{
		$optionsList .= "<option value='".$Cuentas['account_id']."'>".$Cuentas['description']."(".$Cuentas['manual_code'].")</option>";
	}

?>
<form name='newCompany' method='post' action='index.php?c=Config&f=saveConfigPDV' onsubmit='return valida(this)'>
	<div id='title'>Configuracion de Punto de Venta.</div>
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
		<div class='nmcatalogbusquedatit'>Conectar al Punto de Venta</div>
		<table>
			<tr><td>Historial de ventas del periodo actual <input type='radio' name='historial' id='historial' value='1' <?php echo $readonly." ".$chkdHistorial1; ?>>Si <input type='radio' name='historial' id='historial' value='0' <?php echo $readonly." ".$chkdHistorial2; ?>>No</td></tr>
			<tr><td>Conectar al Punto de Venta <input type='checkbox' name='conectar' id='conectar' value='1' <?php echo $chkdConectar; ?>></td></tr>
			<tr><td>Crear Poliza por <select name='corte' id='corte'>
				<option value='1'>Corte de Caja</option>
			</select><input type='hidden' name='ejercicio' id='ejercicio' value='<?php echo $data['EjercicioActual']; ?>'><input type='hidden' name='anterior_corte' id='anterior_corte' value='<?php echo $data['polizas_por']; ?>'></td></tr>
		</table>
		<br /><br />
	</div>
	<div class="lateral">
		<div class='nmcatalogbusquedatit'>Configurar Cuentas Afectables para el Punto de Venta</div>
		<table>
			<tr>
				<td>
					<label for="clientes">Cuenta de Clientes (Default):</label>
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
				<td><label title='Asignar cuenta(s) de clientes' style='font-wenght:bold;'>?</label></td>
			</tr>
			<tr>
				<td>
					<label for="ventas">Cuenta de Ventas (Default):</label>
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
				<td><label title='Asignar cuenta(s) de ventas' style='font-wenght:bold;'>?</label></td>
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
				<td><label title='Asignar cuenta de IVA' style='font-wenght:bold;'>?</label></td>
			</tr>
			<!--<tr>
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
			</tr>-->
		</table>
		<br /><br />
	</div>
	<div id='buttons'>
		<input type='submit' name='save'  class="nminputbutton" value='Guardar' onclick='guardando(1)'>
	</div>
</form>
