<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>
<script src="js/mask.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="js/moment.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
<script src="js/select2/select2.min.js"></script>
<script src="js/agregar.js"></script>
<script src="js/copiar.js"></script>
<script src="js/funcionesPolizas.js"></script>
<script src="js/funcionesProveedores.js"></script>
<script src="js/funcionesCausacion.js"></script>
<script src="js/BigEval.js"></script>
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
	#imprimir,#buscar,#FacturasButton,#copiapoli,img
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

$numPoliza['id'] = $PolizaInfo['id'];
require("views/captpolizas/agregar.php");
require("views/captpolizas/proveedores.php");
require("views/captpolizas/causacion.php");
require("views/captpolizas/facturas.php");
require("views/captpolizas/copiarpoliza.php");

?>
<!--Fin de Captura Movimientos-->
<a href="javascript:window.print();" id='imprimir'><img class="nmwaicons" src="../../netwarelog/design/default/impresora.png" border="0" title='Imprimir'></a>
<img class="nmwaicons" src="images/copiar.png" border="0" title='Copiar Poliza' id="copiapoli" onclick="copiarPoliza();">
	<form name='newCompany' method='post' action='index.php?c=CaptPolizas&f=ActualizarPoliza&p=<?php echo $numPoliza['id']; ?>' onsubmit='return validaciones(this)' enctype='multipart/form-data'>
<div id='title'>Modificar polizas</div><input type="hidden" id="idpoli" value="<?php echo $numPoliza['id']; ?>" />
<table>
	<?php 
			$InicioEjercicio = explode("-",$Ex['InicioEjercicio']); 
			$FinEjercicio = explode("-",$Ex['FinEjercicio']); 
			?>
	<tr><td>Nombre de la organizaci&oacute;n: </td><td><input type='text' class="nminputtext" name='NameCompany' size='50' readonly value='<?php echo $Ex['nombreorganizacion']; ?>'></td></tr>
	<tr><td>Titulo del Ejercicio: </td><td><input type='hidden' name='IdExercise' id='IdExercise' size='50' value='<?php echo $Ex['IdEx']; ?>'><input type='text' class="nminputtext" name='NameExercise' id='NameExercise' size='50' readonly value='<?php echo $Ex['EjercicioActual']; ?>'></td></td>
</table>
<div class='lateral'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Datos del Ejercicio</div>
	<table>
		
		<tr>
		<td style='width:300px;height:30px;'><b>Ejercicio poliza:</b> del (<b><?php echo $InicioEjercicio['2']."-".$InicioEjercicio['1']."-".$Ex['EjercicioActual']; ?></b>) al (<b><?php echo $FinEjercicio['2']."-".$FinEjercicio['1']."-".$Ex['EjercicioActual']; ?></b>)</td><td><b>Periodo de la poliza:</b> 
		<label id='PerAct'><?php echo $PolizaInfo['idperiodo']; ?></label><input type='hidden' id='periodos' name='periodos' value='<?php echo $PolizaInfo['idperiodo'] ?>'> del (<label id='inicio_mes'></label>) al (<label id='fin_mes'></label>)</td>

		</tr>
		
	</table>
</div>
<div class='lateral' style='margin-top:10px;'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Datos de la Poliza</div>
	<table class='captura'>
		
		<tr>
			<?php
			$fechaPoliza = explode("-",$PolizaInfo['fecha']);
			?>
			<td>Tipo de Poliza: <select name='tipoPoliza' id='tipoPoliza' class="nminputselect" onchange="tipopoliza()">
			<?php
				while($LTP = $ListaTiposPolizas->fetch_assoc())
				{
					if($LTP['id'] === $PolizaInfo['idtipopoliza'])
					{
						$selected = "selected";
					}else
					{
						$selected ='';
					}
					echo "<option value='".$LTP['id']."' ".$selected." >".$LTP['titulo']."</option>";
				}
				?>
			</select></td><td>Referencia: <input type='hidden' id='estruc' value='<?php echo $estructura; ?>'><input type='hidden' id='tipo_cuentas' value='<?php echo $type_id_account; ?>'><input type='text' class="nminputtext" name='referencia' value='<?php echo $PolizaInfo['referencia']; ?>' maxlength='40' ></td><td>Concepto: <input type='text' class="nminputtext" name='concepto' id='concepto' value='<?php echo $PolizaInfo['concepto']; ?>' maxlength='50' ></td>
		</tr>
		<tr>
			<td>Fecha: <input type='text' name='fecha'  class="nminputtext" id='datepicker' onMouseOver='javascript:cal()' value='<?php echo $fechaPoliza[2]."-".$fechaPoliza[1]."-".$fechaPoliza[0]; ?>'></td><td><center><a href='javascript:facturas()' style='font-weight:bold;color:black;' title='Ver Facturas' id='FacturasButton'><img src='images/clip.png' style='vertical-align: middle;' width='40px' title="Asociar Facturas">Anexar Factura</a></center></td>
			<?php
			$readonly='';
			 	if(!$manualnumpol) 
					{
						$readonly = 'readonly';
					}
			?>
			<td>
			# Poliza: <input type='text' name='numpol' id='numpol' class="nminputtext" value='<?php echo $PolizaInfo['numpol'];?>' maxlength='6' <?php echo $readonly; ?>><span id='numpolload' style='color:red;font-style:italic;font-size:10px;'>Cargando numero...</span><input type='hidden' name='numtipo' id='numtipo' value='<?php echo $PolizaInfo['idtipopoliza']."-".$PolizaInfo['numpol'];?>' maxlength='6' <?php echo $readonly; ?>>
		</td>
		</tr>
		<tr>
			<td id="us" style="display: none">Usuario
				<div style="width: 250px">
					<select id="usuarios" name="usuarios" class="nminputselect" style="">
					<?php
						while($user = $usuarios->fetch_assoc()){ 
							if($user['idempleado'] == $PolizaInfo['idUser']){ $selec = "selected"; }else{ $selec = ""; }?>
							
							<option value="<?php  echo $user['idempleado']?>"  <?php echo $selec; ?>><?php echo $user['usuario']?></option>
					<?php } ?>
					</select>
				</div>
			</td>
		</tr>
		
	</table>
</div>
<div class='lateral' style='margin-top:10px;'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Cuadre de los Movimientos</div>
	<table class='captura'>
		<td id='Cargos'></td><td id='Abonos'></td><td id='Cuadre'></td>
	</table>
</div>
<?php if($PolizaInfo['idtipopoliza']!=2){ $display="display:none";}?>
<div id="datospago" class="lateral" style="margin-top:10px;<?php echo $display; ?>">
<div class='nmcatalogbusquedatit' style='width:800px;'>Datos del Pago</div>
<table  style="width: 100%;table-layout: fixed">
		<tr>
		
			<td>Forma de pago<select id="formapago" name="formapago" class="form-control" style="width:160px" >
				<option value="0">Elija una FormaPago</option>
			 	<?php while($f=$forma_pago->fetch_array()){ 
			 				if($f['idFormapago'] == $idformapago){?>
			 					<option value="<?php echo $f['idFormapago']; ?>" selected><?php echo ($f['nombre']);?></option>
			 	<?php		}else{ ?>
			 					<option value="<?php echo $f['idFormapago']; ?>"><?php echo ($f['nombre']);?></option>
			 	<?php }
							} ?>
			 	</select>
			</td>
		
			<td>Numero
				<input type="text" size="20" class="form-control"  id="numero"  name="numero" value="<?php echo $PolizaInfo['numero'];?>"/>
			</td>
			<td><b style="font-size: 14px">Banco Origen</b>
					<img style="vertical-align:middle;width: 15px;height: 15px" title="Agregar Cuenta Bancaria" onclick="mandacuentabancaria()" src="images/mas.png">
					<img style="vertical-align:middle;" title="Actualizar Listado" onclick="actcuentasbancarias()" src="images/reload.png">
					<select id="listabancoorigen" name="listabancoorigen" onchange="numerocuentorigen()"  >
					<option value="0">Elija Banco</option>
					<?php 
						while($b=$listacuentasbancarias->fetch_array()){ 
							if($b['idbancaria'] == $PolizaInfo['idCuentaBancariaOrigen']){?>
							<option value="<?php echo  $b['idbancaria']; ?>" selected><?php echo $b['nombre']." (".$b['description']."[".$b['manual_code']."])"; ?> </option>
				<?php		}else{ ?>
								<option value="<?php echo  $b['idbancaria']; ?>"><?php echo $b['nombre']." (".$b['description']."[".$b['manual_code']."])"; ?> </option>

				<?php       }
						} ?>
					
				</select>
			</td>
			<td>No. Cuenta Bancaria Origen/tarjeta
				<input type="text" id="numorigen" class="form-control" name="numorigen" value="<?php echo @$numeroorigen['cuenta'];?>" readonly/>
			</td>
			
			</tr>
			<tr><td colspan="4"></td></tr>
			<tr>
			<td>Beneficiario
	<?php if($PolizaInfo['tipoBeneficiario']!=2){ // el 2 esde empleado?>

			<img style="vertical-align:middle;" title="Agregar Beneficiario" onclick="irapadron()" src="images/cuentas.png">
			<img style="vertical-align:middle;" title="Actualizar Listado" onclick="actualizaprove()" src="images/reload.png">
			<select id="beneficiario" name="beneficiario"  onchange="cuentarbolbenefi();">
					<option value="0">Elija un Beneficiario</option>
					
				<?php 
						while($b=$beneficiario->fetch_array()){
							if($b['idPrv'] == $PolizaInfo['beneficiario']){ ?>
								<option value="<?php echo  $b['idPrv']; ?>" selected><?php echo ($b['razon_social']); ?> </option>
				<?php  		}else{ ?>
								<option value="<?php echo  $b['idPrv']; ?>" ><?php echo ($b['razon_social']); ?> </option>

				<?php  	}
							}	 ?>
				</select>
		<?php }else{ ?>
			<select id="beneficiario" name="beneficiario"  onchange="cuentarbolbenefi();">
					<option value="0">Elija un Beneficiario</option>
					
				<?php 
						while($b=$empleados->fetch_array()){
							if($b['idEmpleado'] == $PolizaInfo['beneficiario']){ ?>
								<option value="<?php echo  $b['idEmpleado']; ?>" selected><?php echo $b['nombreEmpleado']." ".$b['apellidoPaterno']." ".$b['apellidoMaterno']; ?> </option>
				<?php  		}else{ ?>
								<option value="<?php echo  $b['idEmpleado']; ?>" ><?php echo $b['nombreEmpleado']." ".$b['apellidoPaterno']." ".$b['apellidoMaterno']; ?> </option>

				<?php  	}
							}	 ?>
				</select>
		<?php	} ?>
			</td>
			<td>RFC:
				<input type="text" id="rfc" name="rfc" class="form-control" value="<?php echo $PolizaInfo['rfc'];?>" readonly/>
			</td>
				<td><b style="font-size: 14px">Banco Destino</b>
					<img style="vertical-align:middle;width: 15px;height: 15px"  id="mandabanco" title="Agregar Bancos al Proveedor" onclick='mandabancos()'  src="images/mas.png">
					<br>
					<select id="listabanco" name="listabanco" onchange="numerocuent()" style="width: 160px;" >
					<?php 
						while($b=$listabancos->fetch_array()){ 
							if($b['idbanco'] == $PolizaInfo['idbanco']){?>
							<option value="<?php echo  $b['idbanco']; ?>" selected><?php echo $b['nombre']."(".$b['Clave'].")"; ?> </option>
				<?php		}else{ ?>
								<option value="<?php echo  $b['idbanco']; ?>"><?php echo $b['nombre']."(".$b['Clave'].")"; ?> </option>

				<?php       }
						} ?>
					
				 </select>
			</td>
			<td>No. Cuenta Bancaria Destino/tarjeta
				<img style="vertical-align:middle;width: 15px;height: 15px" title="Cargar numero" onclick='numerocuent()' src="images/reload.png">
				<input type="text" id="numtarje" class="form-control"  name="numtarje" value="<?php echo $PolizaInfo['numtarjcuent'];?>" readonly/>
			</td>
		</tr>
		
	 
	</table>	
</div>
</div>

<div class='lateral' style='margin-top:10px;'>
	<div class='nmcatalogbusquedatit' style='width:800px;'>Lista de Movimientos&nbsp;&nbsp;<input type='button' class="nminputbutton_color2" value='Agregar Movimientos' id='agregar' title='ctrl + m'><input type='text' class="nmcatalogbusquedainputtext" id='buscar'  name='buscar' placeholder='Buscar'><input type='button' class="nminputbutton_color2" value='Relacionar Proveedores' style='float:right;' id='botonProveedores' onClick='abreProveedoresLista(<?php echo $numPoliza['id']; ?>)' title='ctrl + i'><input type='button' class="nminputbutton_color2" value='Desglose de IVA Causado' style='float:right;' id='botonClientes' onClick='abreCausacion(<?php echo $numPoliza['id']; ?>)' title='ctrl + i'></div>
	<table id='lista'>
	</table>
	
	<?php  
	if($relacion2!="no"){  
		if(!$relacion['id']){
			$id=0;
			$op="<option value=".$id." selected>--Ninguno--</option>";
			
		}else{
			$id=$relacion['id'];
			$op="";
			
		}
		
	}?>
	<div id="relacionn" style="display: none"><font color="red" face="Comic Sans MS,arial,verdana">Agregar relacion con provision</font>
		<select id="relacionextra" name="relacionextra">
			<option value=0>Ninguna</option>
			<?php 
			 if($relacion2!=0 && $relacion2!="no"){
			 	echo $relacion2;
				// while($row=$relacion2->fetch_array()){
					// if($id==$row['id']){
						// echo "<option value=".$row['id']." selected>Poliza:".$row['numpol'].", Concepto:".$row['concepto']."</option>";
					// }else{
						// echo "<option value=".$row['id'].">Poliza:".$row['numpol'].", Concepto:".$row['concepto']."</option>";
					// }
				// }
			}?>
		</select>
		Saldado<input type="checkbox" id="saldado" name="saldado" value="0" onclick="polizadiario()"/>
	</div>
	
</div>
<div class='lateral' style='margin-top:10px;width:807px;text-align:right;text-align:left;'>
	<label for='facsCheck' id='titleFacs'>Desplegar Lista de Grupo de Facturas</label> <input type='checkbox' id='facsCheck' onclick='listafaccheck(this)'>
	<table id='listaMovsFacturas' width='100%' style='display:none;'>
	</table>
</div>
<div class='lateral' style='margin-top:10px;width:807px;text-align:right;text-align:left;'>
	<table>
		<tr><td>Creación:</td><td><div id='c_div'><?php echo $PolizaInfo['fecha_creacion']." / ".$usuario_creacion; ?></div></td></tr>
		<tr><td>Última Modificación:&nbsp;</td><td><div id='m_div'><?php echo $PolizaInfo['fecha_modificacion']." / ".$usuario_modificacion; ?></div></td></tr>
	</table>
</div>
<div class='lateral' style='margin-top:10px;width:807px;text-align:right;'>
<input type='button' value='Actualizar' title='ctrl+enter' id='actualizarboton2' class="nminputbutton" onclick="antesdemandar()">
<input type='submit' class="nminputbutton" value='Actualizar' id='actualizarboton' title='ctrl+enter' style="display: none">&nbsp;&nbsp;

</div>


</form>
<?php
	if(isset($_REQUEST['im'])){ //1- ingresos, 2- egresos
		if($_REQUEST['im']==1){
?> 
			<script>
				if(confirm("Desea realizar el Desglose de IVA?")){
					$("#botonClientes").click();
				}else{
					setTimeout(function(){ window.print(); },5000);
				}
			</script>
<?php	}if($_REQUEST['im']==2){ ?>
			<script>
			var prv = <?php echo $_REQUEST['prv']; ?>;
			setTimeout(function(){ 
				if(confirm("Desea realizar la Relacion con Proveedores?")){
					$("#botonProveedores").click();
					$('#ProveedoresSelect option[value='+prv+']').attr("selected","");
					abreProveedores(prv,0);
					setTimeout(function(){ modificaImpuestos(); },5000);
				}else{
					window.print(); 
				}
			},5000);
			</script>
<?php	}
		if($_REQUEST['im']==3){ ?>
			<script>
			setTimeout(function(){ window.print(); },5000);
			</script>
<?php	}
	}
?>
<script>
	if($('#tipoPoliza').val()!=2){
		$('#pago').hide();
		$('#formap').hide();
		$('#pago').val(0);
		
	}else{
		$('#pago').show();
		$('#formap').show();
		//cuentarbolbenefi();

	}
	

if($('#tipoPoliza').val()!=3){
	$.post('ajax.php?c=Ajustecambiario&f=moviextranjeros2',{
		idejer:$("#IdExercise").val(),
		idpoliza:$('#idpoli').val(),
		idperido:$('#periodos').val()
		},function (resp){
			if(resp!="no"){
				
				$('#relacionextra').html(resp);
				$("#relacionn").show();
			}
		});
 	$.post('ajax.php?c=CaptPolizas&f=verificasaldado',
 		{idpoliza:$('#idpoli').val()}
 		,function(resp2){
 		  if(resp2==1){
 		  	$('#saldado').prop("checked", true);
 		  }
 		});
 }else{
 	//$("#relacionn").val(0);
 }
 
</script>
<?php if(isset($_SESSION['anticipo'])){?>
 		<script>
 		$("#us").show();
 		$("#botonProveedores").hide();
 		</script>
<?php } ?>
