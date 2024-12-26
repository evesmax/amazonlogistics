<style>
#lista td
{
	width:146px;
	text-align: center;
	border:1px solid #BDBDBD;
}

#buscar
{
-webkit-border-radius: 4px;
-moz-border-radius: 4px;
-o-border-radius: 4px;
border-radius: 4px;
}

#cargando
{
	display:none;
	position:absolute;
	z-index:1;
}

</style>
	<script src="js/select2/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
<script>
function abrirCalc()
{
	window.open("views/captpolizas/calculadora.php", "_blank", "location=no,menubar=no,titlebar=no,resizable=no,toolbar=no, menubar=no,width=500,height=500");
}
</script>
<div id='capturaMovimiento' title='Agregar Movimientos'>
	<!--<div id='calculadora' style='border:1px solid red;width:300px;height:300px;position:absolute;right:0px;'>
		aca todo
	</div>-->
	<input type='hidden' value='<?php echo $numPoliza['id']; ?>' name='idpoliza' id='idpoliza'>
	<table>
		<tr><td width=150><b>Movimiento #<input type='text' class="nminputtext" name='movto' size='2' id='movto'></b></td><td></td><td></td></tr>
		<tr><td>Asignar XML</td>
			<td><!-- <label id="formap" style="">Forma de Pago</label> --><td></td><td></td></tr>
			<tr><td><select class="nminputselect" name='facturaSelect' id='facturaSelect' style='width: 150px;text-overflow: ellipsis;' placeholder='Facturas'>
			</select>
		</td><td>
			<!-- <select name='pago' id='pago' class="nminputselect" style='width:150px !important;' >
				<option value="0" selected="">--Elija forma--</option>
				<?php
				while($pagos = $formapago->fetch_assoc())
				{
					echo "<option value='".$pagos['idFormapago']."'>".($pagos['nombre'])."</option>";
				}
				?>			
				</select> -->
		</td><td style='text-align:center;'><!--<img style='cursor:pointer;' src='images/calculator.png' width='30px' title='Calculadora' onclick='abrirCalc()'>--></td></tr>
		<tr>
			<td>Cuenta: <img src='images/cuentas.png' onclick='iracuenta()' title='Abrir Ventana de Cuentas' style='vertical-align:middle;'><img src='images/reload.png' onclick='actualizaCuentas()' title='Actualizar Cuentas' style='vertical-align:middle;'><div id='cargando-mensaje' style='font-size:8px;color:red;width:20px;'> Cargando...</div>

				<select name='cuenta' id='cuenta' class="nminputselect" style='width:150px !important;' onclick="buscacuentaext(this.value)">
				<?php
				while($Cuentas = $Accounts->fetch_assoc())
				{
					echo "<option value='".$Cuentas['account_id']."'>".$Cuentas['description']."(".$Cuentas[$type_id_account].")</option>";
				}
				?>			
				</select>
			</td>
			<td>Referencia: <input type='text' class="nminputtext" name='referencia_mov' id='referencia_mov' maxlength='40' ></td>
			<td>Concepto: <input type='text' class="nminputtext" name='concepto_mov' id='concepto_mov' maxlength='50' ></td>
		</tr>
		<tr>
			<td>Segmento de Negocio:
			<select name='segmento' id='segmento' style='width: 150px;text-overflow: ellipsis;'  class="nminputselect">
			
			<?php
				while($LS = $ListaSegmentos->fetch_assoc())
				{
					echo "<option value='".$LS['idSuc']."'>".$LS['clave']." / ".$LS['nombre']."</option>";
				}
				?>
			</select></td>
			<td id="muestraextca" style="display: none">Cargo: <br><input type='text' class="nminputtext" name='cargo' id='cargoext' value='0.00' onChange='abonoscargosext()'><br><label id="carext"></label></td>  
			<td><label id="c">Cargo: $</label><input type='text' class="nminputtext" name='cargo' id='cargo' value='0.00' onChange='abonoscargos()'></td>
		</tr>
		<tr>
			<td>Sucursal:<br>
				<select name='sucursal' id='sucursal' style='width: 150px;text-overflow: ellipsis;'  class="nminputselect">
			
			<?php
				while($LS = $ListaSucursales->fetch_assoc())
				{
					echo "<option value='".$LS['idSuc']."'>".$LS['nombre']."</option>";
				}
				?>
			</select></td>
			</td><td id="muestraextab" style="display: none">Abono: <br><input type='text' class="nminputtext" name='abono' id='abonoext' value='0.00' onChange='abonoscargosext()'><br><label id="abext"></label></td>
			<td><label id="abext"></label><br><label id="a">Abono: $</label><input type='text' class="nminputtext" name='abono' id='abono' value='0.00' onChange='abonoscargos()'><br></td>
			</td>		
		</tr>
		
	
		
	</tr>
	</table>
<input type='hidden' id='nuevo'>
<input type='hidden' id='cambio'>
<img src="images/loading.gif" style="display: none" id="load" title="Cargando tipo de cambio">
<table class='TablaAgregar'>
	<tr>
		<td id='CargosAgregar'></td><td id='AbonosAgregar'></td><td style='cursor:pointer;' title='(alt + arriba) para subir el cuadre de la poliza' id='CuadreAgregar' onclick="cuadraPoliza()"></td>
	</tr>
</table>
<div id='cargando'>
	<b style='color:#91C313;'>Guardando Datos...</b>

</div>

</div>