<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="js/moment.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="js/select2/select2.min.js"></script>
<script src="js/agregar.js"></script>
<script src="js/copiar.js"></script>
<script src="js/funcionesPolizas.js"></script>
<script src="js/funcionesProveedores.js"></script>
<script src="js/funcionesCausacion.js"></script>
<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<style>
.TablaAgregar
{
	background-color:#98ac31;
	color:white;
	font-size:8px;
}
.TablaAgregar b
{
	font-size:10px;
}
.TablaAgregar td
{
	width:250px;
}
@media print
{
	#imprimir,#buscar,#FacturasButton,img
	{
		display:none;
	}

	input[type='button']
	{
		display:none;
	}

	#botonClientes,#botonProveedores
	{
		color:#ffffff;
		background-color:white
		text-shadow:0px; 
		border:0px;
		width:1px;
	}


}

</style>
<!--Inicio de Captura Movimientos-->
<?php
require("views/captpolizas/agregar.php");
require("views/captpolizas/proveedores.php");
require("views/captpolizas/causacion.php");
require("views/captpolizas/facturas.php");
require("views/captpolizas/copiarpoliza.php");
?>
<script>
 
if($('#tipoPoliza').val()!=2){
		//$('#pago').hide();
		$('#formap').hide();
		//$('#pago').val(0);
		$("#datospago").hide();
		
	}else{
		$("#datospago").show();
		//$('#pago').show();
		$('#formap').show();
	}
</script>
<!--Fin de Captura Movimientos-->
<a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a>
<!-- <img class="nmwaicons" src="images/copiar.png" border="0" title='Copiar Poliza' onclick="copiarPoliza();"> -->
<form name='newCompany' id='forma' method='post' action='index.php?c=CaptPolizas&f=ActualizarPoliza&p=<?php echo $numPoliza['id']; ?>' onsubmit='return validaciones(this)'>
 <?php if(isset($_SESSION['anticipo'])){?>
		<div id='title'>Anticipo Gastos</div>
<?php }else{ ?>
		<div id='title'>Capturar polizas</div>
<?php } ?>
<table><input type="hidden" id="idpoli" value="<?php echo $numPoliza['id']; ?>" />
	<tr><td>Nombre de la organizaci&oacute;n: </td><td><input type='text' class="nminputtext" name='NameCompany' size='50' readonly value='<?php echo $Ex['nombreorganizacion']; ?>'></td></tr>
	<tr><td>Titulo del Ejercicio: </td><td><input type='hidden' name='IdExercise' id='IdExercise' size='50' value='<?php echo $Ex['IdEx']; ?>'><input type='text' name='NameExercise' class="nminputtext" id='NameExercise' size='50' readonly value='<?php echo $Ex['EjercicioActual']; ?>'></td></td>
</table>
<div class='lateral'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Datos del Ejercicio</div>
	<table>
		
		<tr>
			<?php 
		
			$InicioEjercicio = explode("-",$Ex['InicioEjercicio']); 
			$FinEjercicio = explode("-",$Ex['FinEjercicio']); 
			?>
		<td style='width:300px;height:30px;'><b>Ejercicio Vigente:</b> del (<b><?php echo $InicioEjercicio['2']."-".$InicioEjercicio['1']."-".$Ex['EjercicioActual']; ?></b>) al (<b><?php echo $FinEjercicio['2']."-".$FinEjercicio['1']."-".$Ex['EjercicioActual']; ?></b>)</td><td><b>Periodo actual:</b> 
		<label id='PerAct'><?php echo $Ex['PeriodoActual']; ?></label><input type='hidden' id='periodos' name='periodos' value='<?php echo $Ex['PeriodoActual']; ?>'> del (<label id='inicio_mes'></label>) al (<label id='fin_mes'></label>)</td>

		</tr>
		
	</table>
</div>
<div class='lateral' style='margin-top:10px;'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Datos de la Poliza</div>
	<table class='captura'>
		
		<tr>
			<td>Tipo de Poliza: <select name='tipoPoliza' class="nminputselect" id='tipoPoliza' onchange="tipopoliza()">
				<?php
				while($LTP = $ListaTiposPolizas->fetch_assoc())
				{
					echo "<option value='".$LTP['id']."'>".$LTP['titulo']."</option>";
				}
				?>
			</select></td><td>Referencia: <input type='text' class="nminputtext" name='referencia' id="referencia" maxlength='40'></td><td>Concepto: <input type='text' class="nminputtext" name='concepto' id='concepto' maxlength='50'></td>
		</tr>
		<tr>
			<td>Fecha: <input type='text' class="nminputtext" name='fecha' id='datepicker' onMouseOver='javascript:cal()' onFocus='javascript:cal()' onchange="actuali()"></td><td><center><a href='javascript:facturas()' style='font-weight:bold;color:black;' title='Ver Facturas' id='FacturasButton'><img src='images/clip.png' style='vertical-align: middle;' width='40px' title="Asociar Facturas">Anexar Factura</a></center></td><td>
			<?php
			$readonly='';
			 	if(!$manualnumpol) 
					{
						$readonly = 'readonly';
					}
			?>
			# Poliza: <input type='text' name='numpol' id='numpol' class="nminputtext" value='<?php echo $numpol; ?>' maxlength='6' <?php echo $readonly; ?>><span id='numpolload' style='color:red;font-style:italic;font-size:10px;'>Cargando numero...</span><input type='hidden' name='numtipo' id='numtipo' value='<?php echo "1-".$numpol;?>' maxlength='6' <?php echo $readonly; ?>>
		</td>
		</tr>
		<tr id="us" style="display: none">
			<td >Usuario
				<select id="usuarios" name="usuarios" class="nminputselect" style="">
					<?php
						while($user = $usuarios->fetch_assoc()){ ?>
							<option value="<?php  echo $user['idempleado']?>"><?php echo $user['usuario']?></option>
					<?php } ?>
					</select>
			</td>
			<td>
				Importe: <input type="text" id="importeprevio" name="importeprevio" class="nminputtext" />
			</td>
		</tr>
		
		
	</table>
</div>
<div   class='lateral' style='margin-top:10px;'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Cuadre de los Movimientos</div>
	<table class='captura'>
		<td id='Cargos'></td><td id='Abonos'></td><td id='Cuadre' onchange="javascript:alert('Hola mundo')"></td>
	</table>
	
</div>

<div id="datospago" class="lateral" style="margin-top:10px;display: none">
<div class='nmcatalogbusquedatit' style='width:800px;'>Datos del Pago</div>
<table style="width:800px;">
		<tr>
			<td>Forma de pago<select id="formapago" name="formapago" class="form-control" style="width:160px" >
			 	<option value="0">Elija una FormaPago</option>
			 	<?php while($f=$forma_pago->fetch_array()){ ?>
			 				<option value="<?php echo $f['idFormapago']; ?>"><?php echo ($f['nombre']);?></option>
			 	<?php	 } ?>
			 	</select>
			</td>
			<td>Numero
				<input type="text" size="20" id="numero"  name="numero" value="" class="form-control"/>
			</td>
			<td><b style="font-size: 14px">Banco Origen</b>
					<img style="vertical-align:middle;width: 15px;height: 15px" title="Agregar Cuenta Bancaria" onclick="mandacuentabancaria()" src="images/mas.png">
					<img style="vertical-align:middle;" title="Actualizar Listado" onclick="actcuentasbancarias()" src="images/reload.png">

					<select id="listabancoorigen" name="listabancoorigen" onchange="numerocuentorigen()"  style="width:100px;">
					<option value="0">Elija Banco</option>
					<?php 
						while($b=$listacuentasbancarias->fetch_array()){ ?>
							<option value="<?php echo  $b['idbancaria']; ?>"><?php echo $b['nombre']." (".$b['description']."[".$b['manual_code']."])"; ?> </option>
				<?php   } ?>
					
				</select>
			</td>
			<td>No. Cuenta Bancaria Origen/tarjeta
				<input type="text" id="numorigen" class="form-control" name="numorigen" value="" readonly/>
			</td>
			
			</tr>
			<tr><td colspan="4"></td></tr>
			<tr>
				<td>
			Beneficiario
			<img style="vertical-align:middle;" title="Agregar Beneficiario" onclick="irapadron()" src="images/cuentas.png">
			<img style="vertical-align:middle;" title="Actualizar Listado" onclick="actualizaprove()" src="images/reload.png">
			<select id="beneficiario" name="beneficiario"   onchange="cuentarbolbenefi();" style="width:160px">
					<option value="0">Elija un Beneficiario</option>
					
				<?php 
						while($b=$beneficiario->fetch_array()){ ?>
							<option value="<?php echo  $b['idPrv']; ?>"><?php echo ($b['razon_social']); ?> </option>
				<?php  		} ?>
				</select>
			</td>
			<td>RFC:
				<input type="text" id="rfc" name="rfc" value="" class="form-control" readonly/>
			</td>
				<td><b style="font-size: 14px">Banco Destino</b>					
				<img style="vertical-align:middle;width: 15px;height: 15px"  id="mandabanco" title="Agregar Bancos al Prv" onclick='mandabancos()'  src="images/mas.png">
				<select id="listabanco" name="listabanco" onchange="numerocuent()" style="width: 160px; margin-bottom: 8px;margin-left: 2%;" >
					<option value="0">Elija Banco</option>
					<?php 
						while($b=$listabancos->fetch_array()){ ?>
							<option value="<?php echo  $b['idbanco']; ?>"><?php echo $b['nombre']."(".$b['Clave'].")"; ?> </option>
				<?php	} ?>
					
				</select>
			</td>
				<td>No. Cuenta Bancaria Destino/tarjeta
				<img style="vertical-align:middle;width: 15px;height: 15px" title="Cargar numero" onclick='numerocuent()' src="images/reload.png">
				<input type="text" id="numtarje" name="numtarje" class="form-control" value="" readonly/>
			</td>
		</tr>
		
	 
	</table>	
</div>
</div>
<div class='lateral' style='margin-top:10px;'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Lista de Movimientos&nbsp;&nbsp;<input type='button' class="nminputbutton_color2" value='Agregar Movimientos' id='agregar' title='ctrl + m'>
		<input type='text' class="nmcatalogbusquedainputtext" id='buscar'  name='buscar' placeholder='Buscar'><input type='button' class="nminputbutton_color2" value='Relacionar Proveedores' style='float:right;' id='botonProveedores' onClick='abreProveedoresLista(<?php echo $numPoliza['id']; ?>)' title='ctrl + i'><input type='button' class="nminputbutton_color2" value='Desglose de IVA Causado' style='float:right;' id='botonClientes' onClick='abreCausacion(<?php echo $numPoliza['id']; ?>)' title='ctrl + i'></div>
	<table id='lista'>
	</table>
	<div id="relacionn" style="display: none"><font color="red" face="Comic Sans MS,arial,verdana">Agregar relacion con provision</font>
		<select id="relacionextra" name="relacionextra">
			
		</select>
		Saldado<input type="checkbox" id="saldado" name="saldado" value="0" onclick="polizadiario()"/>

	</div>
</div>

<div class='lateral' style='margin-top:10px;width:807px;text-align:right;'>
	<input type='button' value='Guardar Poliza' title='ctrl+enter' id='guardarpolizaboton2' class="nminputbutton" onclick="antesdemandar()">
<input type='submit' value='Guardar Poliza' title='ctrl+enter' id='guardarpolizaboton' style="display:none">&nbsp;&nbsp;<input type='button' value='Cancelar Poliza' class="nminputbutton" title='ctrl + x' onClick='CancelPoliza()' id='cancelarpolizaboton'>
<input type='button' value='Cancelar Poliza' class="nminputbutton" title='ctrl + x' onClick='CancelPolizaAnticipo()' id='cancelarpolizabotonanticipo' style="display: none">
</div>
</form>
<?php if(isset($_SESSION['anticipo'])){?>
 		<script>
 		$("#cancelarpolizabotonanticipo,#us").show();
 		$("#cancelarpolizaboton").hide();
 		$("#tipoPoliza").html("<option value='2' selected>Egresos</option>");
 		$("#datospago").show();
 		$("#botonProveedores").hide();
 		$("#listabancoorigen").attr("onchange",'numerocuentorigenanticipo()');
 		$("#beneficiario").attr("onchange",'cuentArbolBenefiAnticipo()');
 		</script>
<?php } ?>

