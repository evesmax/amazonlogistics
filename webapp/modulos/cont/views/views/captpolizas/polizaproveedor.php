<!DOCTYPE html>
	<head>
				        <meta charset="UTF-8" />
		<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/clipolizas.css"/>
		<script type="text/javascript" src="js/pagoprovee.js"></script>
		<script src="js/select2/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> 
		<script src="js/moment.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
		<script type="text/javascript" src="js/sessionejer.js"></script>
		
<?php 
if(isset($_COOKIE['ejercicio'])){
	$NombreEjercicio = $_COOKIE['ejercicio'];
	$v=1;
}else{
	$NombreEjercicio = $Ex['NombreEjercicio'];
	$v=0;
}
?>
<script>
	dias_periodo(<?php echo $NombreEjercicio; ?>,<?php echo $v; ?>);
	function pagoss(che){
		if($('#'+che).is(":checked")) {
			$('#impor'+che).show();
			$("#impor2"+che).show();
			$("#iva"+che).show();
			$("#iva2"+che).show();
			$("#ieps"+che).show();
			$("#ieps2"+che).show();
			$("#imporintro"+che).hide();
			$("#imporintro2"+che).hide();
			$("#ivacobrado"+che).hide();
			$("#ivapendiente"+che).hide();
			$("#ipendiente"+che).hide();
			$("#icobrado"+che).hide();
			//input
			$("#imporinput"+che).val("0.00");
			$("#imporinput2"+che).val("0.00");
			$("#ivacobradoinput"+che).val("0.00");
			$("#ivapendienteinput"+che).val("0.00");
			$("#ipendienteinput"+che).val("0.00");
			$("#icobradoinput"+che).val("0.00");
			//select


		}else{
			$("#impor"+che).hide();
			$("#impor2"+che).hide();
			$("#iva"+che).hide();
			$("#iva2"+che).hide();
			$("#ieps"+che).hide();
			$("#ieps2"+che).hide();
			$("#imporintro"+che).show();
			$("#imporintro2"+che).show();
			$("#ivacobrado"+che).show();
			$("#ivapendiente"+che).show();
			$("#ipendiente"+che).show();
			$("#icobrado"+che).show();
			
		}
	}
	function antesdeguardar(cont){
		var i=0; var status=0;
		var idformapago = $('#formapago').val().split('/');
		if(idformapago[0]==2){
	  		if($('#numero').val()==""){
	  			alert("La forma de pago en Cheque requiere que proporcione un numero");
	  			$('#numero').css("border-color","red");
	  			return false;
	  		}
	  	}	
	  	for(i;i<cont;i++){
	  	 <?php if($statusIVAIEPS==0){ ?>
			  	  	if( ($("#ivapendientepago"+i).val()==0 || $("#ivapago"+i).val()==0 )){
				  			alert("Elija una cuenta de IVA!!"); return false;
				  		}
			<?php if($statusIEPS==1){ ?>
				  		if( ($("#iepspendiente"+i).val()==0 || $("#iepspago"+i).val()==0 )){
				  			alert("Elija una cuenta de IEPS!!"); return false;
				  		}
			  <?php 	}
			}else { ?>
			  	if($("#ivapendientepago").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IVA Acreditable Pendiente de pago");  return false;}
				if($("#ivapago").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IVA Acreditable Pagado");  return false;}

		<?php if($statusIEPS==1){ ?>
					if($("#iepspendiente").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IEPS Acreditable Pendiente de pago");  return false;}
					if($("#iepspago").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IEPS Acreditable Pagado");  return false;}
	<?php 		}
		} ?>
	  	 $.post('index.php?c=CaptPolizas&f=guardanewvalores',{
  			cont : i,
	  		imporinput : $("#imporinput2"+i).val(),//import
			ivacobradoinput : $("#ivacobradoinput"+i).val(),//iva 
			ipendienteinput : $("#ipendienteinput"+i).val(),//ieps
			idclien : $("#idcli"+i).val(),//valor para almacenar en array
			
			ivapendiente : $("#ivapendientepago"+i).val(),//cuenta
			ivacobrado : $("#ivapago"+i).val(),
			iepspendiente : $("#iepspendiente"+i).val(),
			iepscobro : $("#iepspago"+i).val(),
			
			array:"proveedor"
		 },function(resp){
  			status+=1;
  			
  			if(status==cont ){
	 			$("#agrega").click();
			}
  		 });
  		 
	 	}
	 	//alert(status);alert(cont);
	 	
  }	
  function rellena(relleno,valor){
		//alert($("#"+valor).val());
		$("#"+relleno).val($("#"+valor).val());
	}

	</script>
	<style>
	.datos{
		font-size:12px;
		font-weight:bold; 
		color:#6E6E6E;
		width: 40%;
		height:200px;
		vertical-align:middle;
		display:inline;
		margin:0;
	}
	.dat{
		width: 100%;
		margin:0;
		border:0;
	}
	</style>
	</head>
	<body>
	<?php
	$disable = "";
	$idbeneficiario=0;
	$numero = "";
	$rfc ="";
	$numtarje = "";
	$idbanco =0;
	$prove=0;
	$formap="";
	$numeroorigen = "";
	$idbancoorigen = 0;
	$bancocuenta=0;
if(isset($_SESSION['proveedor'])){
		//$disable = "disabled=''";
	
	foreach($_SESSION['proveedor'] as $cli){
		foreach($cli as $prove){
			if(isset($prove['formapago'])){
				$formap=$prove['formapago'];
			}
			if(isset($prove['beneficiario'])){
				 $idbeneficiario = $prove['beneficiario'];
			}
			if(isset($prove['numero'])){
				$numero = $prove['numero'];
			}
			if(isset($prove['rfc'])){
				$rfc =$prove['rfc'];
			}
			if(isset($prove['numtarje'])){
				$numtarje = $prove['numtarje'];
			}
			if(isset($prove['listabanco'])){
				 $idbanco = $prove['listabanco'];
			}
			if(isset($prove['proveedor'])){
				$provee=$prove['proveedor'];
				
			}
			if(isset($prove['numorigen'])){
				$numeroorigen=$prove['numorigen'];
				
			}
			if(isset($prove['listabancoorigen'])){
				$banorigen = $prove['listabancoorigen'];
				
			}
			 if(isset($prove['banco'])){
				
				 $bancocuenta=$prove['banco'];
			}
			
			
		}
	}
}
	?>
	<div class="nmwatitles">&nbsp;Pago a proveedor.</b></div>
		<br></br>
		<div id="contenedor" class="div dat" align="right">
			
<form method="post" ENCTYPE="multipart/form-data" action="index.php?c=CaptPolizas&f=tablaprov" onsubmit="return validacampos(this)">

			<table>
			<tr>
			

				<td>Seleccionar xml.</td>
				<td>
			<input type="radio" class="nminputradio" name="radio" id="radio" value="1" onclick="checa()" />
				</td>
				<td>
					<select id="xml" name="xml" style="display: none;width: 50%;" class="nminputselect">
						<option value="0" selected="">Elija un xml</option>
						<?php
						$directorio=opendir('xmls/facturas/temporales'); 
						while ($archivo = readdir($directorio)){
							$solopagos = strpos($archivo, "Pago");
							if($archivo != '.' && $archivo != '..' && $archivo != '.file' && $archivo !='.DS_Store'){
								if($solopagos==true){
								 echo '<option value="'.$archivo.'">'.($archivo).'</option>';
								}
							}
						}
			  
						closedir($directorio); 
						?>
				</select>
				</td>
			</tr>
			<!-- <tr>
				<td>Subir archivo</td>
				<td>
			<input type="radio" class="nminputradio" name="radio" id="radio" value="2"  onclick="checa()"/>
				</td>
				<td>
					<input type="file" id="xmlsube" name="xmlsube" style="display: none"/>
				</td>
		
			</tr> -->
			</table>
			<br></br>
<div style="position: relative">
	<fieldset style="width: 85.8%">
<legend>D A T O S  &nbsp;  D EL   &nbsp;  E J E R C I C I O</legend>
	<table border=0>
		
		<tr>
			<?php 
			
			
			if(isset($_COOKIE['ejercicio'])){ 
				$InicioEjercicio = explode("-","01-0".$_COOKIE['periodo']."-".$_COOKIE['ejercicio']); 
				$FinEjercicio = explode("-","31-0".$_COOKIE['periodo']."-".$_COOKIE['ejercicio']);  
				$peridoactual = $_COOKIE['periodo'];
				$ejercicioactual = $_COOKIE['ejercicio'];
			}else{
				$InicioEjercicio = explode("-",$Ex['InicioEjercicio']); 
				$FinEjercicio = explode("-",$Ex['FinEjercicio']); 
				$peridoactual = $Ex['PeriodoActual'];
				$ejercicioactual = $Ex['EjercicioActual'];
				
			}
			
			?>
		<td style='height:30px;'><b>Ejercicio Vigente:</b> 
			<?php
			if($Ex['PeriodosAbiertos'])
				{
					if($ejercicioactual > $firstExercise)
					{
						?><a href='javascript:cambioEjercicio(<?php echo $peridoactual; ?>,<?php echo $ejercicioactual-1; ?>);' title='Ejercicio Anterior'><img class='flecha' src='images/flecha_izquierda.png'></a>
				<?php }
				} ?>
	
			del (<?php echo $InicioEjercicio['2']."-".$InicioEjercicio['1']."-".$InicioEjercicio['0']; ?>) al (<?php echo $FinEjercicio['2']."-".$FinEjercicio['1']."-".$FinEjercicio['0']; ?>)
			<?php if($Ex['PeriodosAbiertos'])
				{
					if($ejercicioactual < $lastExercise)
					{
						?><a href='javascript:cambioEjercicio(<?php echo $peridoactual; ?>,<?php echo $ejercicioactual+1; ?>)' title='Ejercicio Siguiente'><img class='flecha' src='images/flecha_derecha.png'></a>
				<?php }
				} ?> </td>

			<td>
			
			</tr>
			<tr>
				<td>	<b>Periodo actual:</b> 

		<?php 
				if($Ex['PeriodosAbiertos'])
				{
					if($peridoactual>1)
					{
						?><a href='javascript:cambioPeriodo(<?php echo $peridoactual-1; ?>,<?php echo $ejercicioactual; ?>);' title='Periodo Anterior'><img class='flecha' src='images/flecha_izquierda.png'></a>
				<?php }
				} ?>  
				<label id='PerAct'><?php echo $peridoactual; ?></label><input type='hidden' id='Periodo' value='<?php echo $peridoactual; ?>'> del (<label id='inicio_mes'></label>) al (<label id='fin_mes'></label>)  
			 	<?php if($Ex['PeriodosAbiertos'])
				{
					if($peridoactual<13)
					{
						?><a href='javascript:cambioPeriodo(<?php echo $peridoactual+1; ?>,<?php echo $ejercicioactual; ?>)' title='Periodo Siguiente'><img class='flecha' src='images/flecha_derecha.png'></a>
				<?php }
				} ?> </td>
			 </td>
			
		</tr>
		<td>
			Acorde a configuracion:<img src="images/reload.png" onclick="periodoactual()" title="Ejercicio y periodo de configuracion por defecto" style="vertical-align:middle;">
		</td>
		
	</table>
	<input type="hidden" id="diferencia" value="<?php echo number_format($Cargos['Cantidad']-$Abonos['Cantidad'], 2); ?>" />

</fieldset><br>
<fieldset class="datos" style="width: 45.2%;">
<td><?php echo @$bancosno; ?></td><br>
				<td><?php echo @$proveedoresno; ?></td>
			<legend>
			C U E N T A S 
			</legend>
			<table>
				<tr>
					<td>Banco : 
						<img style="vertical-align:middle;width: 15px;height: 15px" title="Agregar Cuenta Bancaria" id="mandacuentabanca" onclick="mandacuentabancaria()" src="images/mas.png">
						<select  id="banco" name="banco" class="nminputselect" style="width: 150px; margin-left: 2%; margin-bottom: 8px;" onchange="cuentabancarias();">
						<option value="0">Elija un cuenta</option>
				<?php 
					  if(isset($bancos)){
						 while($b=$bancos->fetch_array()){ 
						 	if($bancocuenta == ($b["account_id"].'/'. $b['description']) ){ ?>
								<option value='<?php echo $b["account_id"].'/'. $b['description']; ?>' selected><?php echo $b['description']."(".$b["manual_code"].")"; ?> </option>
				<?php 		}else{?>
								<option value='<?php echo $b["account_id"].'/'. $b['description']; ?>'><?php echo $b['description']."(".$b["manual_code"].")"; ?> </option>
				<?php		} 
						 }
					  } ?>
					</select></td>
					<td>

					</td>
				</tr>	
				<tr>
					<td>Proveedor:						
					<img style="vertical-align:middle;width: 15px;height: 15px" title="Agregar Bancos al Prv" onclick='mandabancos()' src="images/mas.png">

					<select id="proveedor" name="proveedor" class="nminputselect" style="width: 150px; margin-bottom: 8px;margin-left: 2%;" onchange="beneficiari();" <?php echo $disable; ?>>
				<?php 
					if(isset($proveedores)){ ?>
						<option value="0" >Elija un proveedor</option>
						
				<?php while($b=$proveedores->fetch_array()){ 
							if(($b['cuenta'].'/'. $b['idPrv'].'/'. $b['razon_social'])==$provee){ ?>
								<option value="<?php echo $b['cuenta'].'/'. $b['idPrv'].'/'. $b['razon_social']; ?>" selected><?php echo ($b['razon_social']); ?> </option>
				<?php		}else{?>
								<option value="<?php echo $b['cuenta'].'/'. $b['idPrv'].'/'. $b['razon_social']; ?>"><?php echo ($b['razon_social']); ?> </option>
				<?php 		}
					  }while($b=$proveedores2->fetch_array()){ 
					  		if(($b['account_id'].'-'. $b['description'])==$provee){?>
								<option value="<?php echo $b['account_id'].'-'. $b['description']; ?>" selected><?php echo ($b['description']."(".$b['manual_code'].")"); ?> </option>
				<?php 		}else{ ?>
								<option value="<?php echo $b['account_id'].'-'. $b['description']; ?>" ><?php echo ($b['description']."(".$b['manual_code'].")"); ?> </option>
				<?php	    }
					  } 
					}?>
					</select></td>
					
					<td>

					</td>
				</tr>
			</table>
	</fieldset>
		
			<!-- <label>Importe: </label>
					<input type="text" class="nminputtext" placeholder="0.00" size="10" id="importe" style="margin-left: 4%;" name="importe" onkeypress="return valida(event)"/>&nbsp;&nbsp;
					 -->
<fieldset class="datos">
			<legend>D A T O S  &nbsp;  D E   &nbsp;  R E G I S T R O</legend>
			<table >
				<tr>
					<td>Concepto: </td>
					<td><input type="text"  class="nminputtext" placeholder="Concepto..." id="concepto" name="concepto" /></td>
				</tr>
				<tr>
					<td>Segmento de Negocio:</td>
					<td><select name='segmento' id='segmento' style='width: 165px;height:35px;text-overflow: ellipsis;'  class="nminputselect">
					
					<?php
						while($LS = $ListaSegmentos->fetch_assoc())
						{
							echo "<option value='".$LS['idSuc']."//".$LS['nombre']."'>".$LS['nombre']."</option>";
						}
						?>
					</select></td>
				</tr>
					<td>Sucursal:</td>
					<td><select name='sucursal' id='sucursal' style='width: 165px;height:35px; text-overflow: ellipsis;'  class="nminputselect">
					
					<?php
						while($LS = $ListaSucursales->fetch_assoc())
						{
							echo "<option value='".$LS['idSuc']."//".$LS['nombre']."'>".$LS['nombre']."</option>";
						}
						?>
						</select></td>
				</tr>
					<td>Fecha Poliza:</td>
					
					<?php if(isset($_SESSION['fechaprove'])){ ?>
					<td><input  type="date" class="nminputtext" id="fecha" name="fecha" value="<?php echo $_SESSION['fechaprove']; ?>" onmousemove="javascript:fechadefault()" style='width: 165px;height:35px '/></td>
					<?php }else{ ?>
					<td><input  type="date" class="nminputtext" id="fecha" name="fecha" onmousemove="javascript:fechadefault()" style='width: 165px;height:35px '/></td>
						<?php } ?>
				</tr>
					</table>
</fieldset>
</div><br>
	<fieldset style="width: 85.5%;">
	<legend>D A T O S &nbsp;D E L &nbsp;P A G O</legend>
	<table style="text-align:center;">
		<tr>
			<td>Forma de pago<select id="formapago" name="formapago" class="form-control" style="width:160px">
			 	
			 	<?php while($f=$forma_pago->fetch_array()){
			 			if(($f['idFormapago'].'/'.$f['nombre'])==$formap){ ?>
			 				<option value="<?php echo $f['idFormapago']."/".$f['nombre']; ?>" selected><?php echo $f['nombre'];?></option>
			 	<?php 	}else{?>
			 				<option value="<?php echo $f['idFormapago']."/".$f['nombre']; ?>"><?php echo ($f['nombre']);?></option>
			 	<?php	 } 
					}?>
			 	</select>
			</td>
			<td>Numero:<input type="text"  class="form-control" size="20" id="numero"  name="numero" value="<?php echo $numero;?>"/>
			</td>
			<td><b style="font-size: 14px">Banco Origen:</b>
				<select id="listabancoorigen" name="listabancoorigen"  style="width:160px" class="form-control">
					<?php 
					
					while($b=$listacuentasbancarias->fetch_array()){
						if($b['idbancaria']==$banorigen){?>
							<option value="<?php echo $b['idbancaria']; ?>" selected ><?php echo $b['nombre']; ?></option>";
				<?php	}else{ ?>
							<option value="<?php echo $b['idbancaria']; ?>"  ><?php echo $b['nombre']; ?></option>";
				<?php	}
					} 
					
					 ?>
				</select>
			</td>
			<td>No. Cuenta Bancaria Origen/tarjeta
				<input type="text" id="numorigen" name="numorigen" class="form-control" value="<?php echo $numeroorigen; ?>" readonly/>
			</td>
			
			
		</tr>
		<tr><td colspan="4"><hr></td></tr>
		<tr>
			<td >Beneficiario:
				<select id="beneficiario" name="beneficiario"  class="form-control" style="width: 160px; "  onchange="cuentarbolbenefi();" >
					<option value="0">Elija un Beneficiario</option>
					
				<?php 
						while($b=$beneficiario->fetch_array()){ 
							if($b['idPrv']==$idbeneficiario){  ?>
								<option value="<?php echo  $b['idPrv']; ?>" selected><?php echo ($b['razon_social']); ?> </option>
				<?php 		}else{ ?>	
								<option value="<?php echo  $b['idPrv']; ?>"><?php echo ($b['razon_social']); ?> </option>
				<?php  		}
						} 
				?>
				</select>
			</td>
			<td>&nbsp;RFC:<input type="text" id="rfc" name="rfc" class="form-control" value="<?php echo $rfc; ?>" readonly/>
			</td>
			<td ><b style="font-size: 14px">Banco Destino:</b>
				<select id="listabanco" name="listabanco" onchange="numerocuent()" style="width:160px" class="form-control">
					<option value="0">Elija Banco</option>
					<?php 
						while($b=$listabancos->fetch_array()){
							if($b['idbanco']==$idbanco){ ?> 
							<option value="<?php echo  $b['idbanco']; ?>" selected><?php echo $b['nombre']."(".$b['Clave'].")"; ?> </option>
					<?php  		} else{ ?>
								<option value="<?php echo  $b['idbanco']; ?>"><?php echo $b['nombre']."(".$b['Clave'].")"; ?> </option>
					<?php		}
						}
					?>
					
				</select>
			</td>
			<td>No. Cuenta Bancaria Destino/tarjeta
				<img style="vertical-align:middle;width: 15px;height: 15px" title="Cargar numero" onclick='numerocuent()' src="images/reload.png">

				<input type="text" id="numtarje" name="numtarje" class="form-control" value="<?php echo $numtarje; ?>" readonly/>
			</td>
			
		</tr>
		<tr>
			<td colspan="4"></td>
			
			
		</tr>
	 
	</table><br>		
</fieldset>	<br>			
		<input type="submit" value="Leer XML" class="nminputbutton_color2" id="agregar" >
		</form>
		
		
		
			<br></br>
		<div id="movimientos" align="center" style="width: 84%">
			<table id="datos" align="center" cellpadding="2" border="0" style="border: white 1px solid; " width="100%">
				<thead>
					<tr>
						<td></td>
						<td></td>
						<td class="nmcatalogbusquedatit" align="center">Cargo</td>
						<td class="nmcatalogbusquedatit" align="center">Abono</td>
						<td class="nmcatalogbusquedatit" align="center">XML</td>
						<!-- <td class="nmcatalogbusquedatit" align="center">Forma de pago</td> -->						
						<td class="nmcatalogbusquedatit" align="center">Segmento</td>
						<td class="nmcatalogbusquedatit" align="center">Sucursal</td>

					</tr>
					<tr><td colspan="8"><hr></hr></td></tr>
				</thead>
					<tbody><?php 
					$cont=0;
					
						 foreach($_SESSION['proveedor'] as $cli){
						 	//echo count($cli);
							foreach($cli as $prove){
						if(strrpos($prove['proveedor'],"-")){
							 $p=explode('-',$prove['proveedor']);
							 $cli=$p[1]; 
						}else{
							$p=explode('/',$prove['proveedor']);
							$cli=$p[2];
						}
						$segment = explode('//',$prove['segmento']);
							$sucu = explode('//',$prove['sucursal']);
							  ?>
			
			 <tr><td colspan="7"><hr></hr>
			 <input type="checkbox"  checked="" id="<?php echo $cont; ?>" onclick="pagoss(<?php echo $cont; ?>)"/>Pago Total</td></tr>

			 </tr>
			 	
				 <tr>
					 <td rowspan="2" align="center"><b><?php echo ($cli); ?></b><br><?php echo $prove['concepto']; ?></td>
					 <input type="hidden" value="<?php echo $prove['proveedor']; ?>" id="idcli<?php echo $cont; ?>"/>
					 <td  class="nmcatalogbusquedatit" align="center">Proveedores</td>
					 <td align="center" id="impor<?php echo $cont; ?>"><?php echo number_format($prove['importe'],2,'.',','); ?></td>
					 <td align="center" style="display: none" id="imporintro<?php echo $cont; ?>" ><input type="text" placeholder="0.00" value="0.00" id="imporinput<?php echo $cont; ?>" disabled/></td>
					 <td align="center">0.00</td>
					 <td colspan=""></td>
					 <td align="center"><?php echo $segment[1]; ?></td>
					 <td align="center"><?php echo $sucu[1]; ?></td>
				 </tr>
				 <tr>
					 <td  class="nmcatalogbusquedatit" align="center">Bancos</td>
					 <td align="center">0.00</td>
					 <td align="center" id="impor2<?php echo $cont; ?>"><?php echo number_format($prove['importe'],2,'.',','); ?></td>
					 <td align="center" style="display: none" id="imporintro2<?php echo $cont; ?>"><input  type="text" placeholder="0.00" value="0.00" id="imporinput2<?php echo $cont; ?>" onkeyup="rellena('imporinput<?php echo $cont; ?>','imporinput2<?php echo $cont; ?>')" /></td>
					 <td align="center"><?php echo $prove['xml']; ?></td>
					 <!-- <td align="center"><?php $f=explode("/",$prove['formapago']); echo $f[1];?></td>	-->				
					 	 <td colspan="2"></td> 					
					 	  <td><img src="images/eliminado.png" title="Eliminar Movimiento" onclick="borra(<?php echo $cont; ?>);"/></td>
				 </tr>
				 <?php if($prove['IVA']>0){ //pato?>
				 	<script>
				 	$(document).ready(function(){
				 		$("#ivapendientepago<?php echo $cont ?>,#ivapago<?php echo $cont ?>").select2({
        					 width : "150px"
       					 });
       					 
					});
				 	</script>
				 	<tr>
				 		<td colspan=""></td>
				 		<?php if($statusIVAIEPS==1){?>
				 		<td  class="nmcatalogbusquedatit" align="center"><!-- IVA pendiente de Pago -->
				 			<input type="button" id="ivapendientepago" name="ivapendientepago" onclick="mandaasignarcuenta();" title="Ir a asignacion de cuentas" class="nmcatalogbusquedatit" value="<?php  echo $ivapendientepago[1];?>">
						</td>
						<?php }else{ ?>
						<td  class="nmcatalogbusquedatit" align="center">IVA pendiente de Pago
				 					<select id="ivapendientepago<?php echo $cont; ?>" class="nmcatalogbusquedatit" >
				 						<option value="0">--Elija una cuenta--</option>
							 			<?php echo $listadoivaieps; ?>
				 					</select>
				 				</td>
							<?php } ?>
				 		<td align="center">0.00</td>
				 		<td align="center" id="iva<?php echo $cont; ?>" ><?php echo number_format($prove['IVA'],2,'.',','); ?></td>
				 		<td align="center" id="ivapendiente<?php echo $cont; ?>" style="display: none"><input type="text" value="0.00" placeholder="0.00" id="ivapendienteinput<?php echo $cont; ?>" disabled/></td>
				 	</tr>
				 	<tr>
				 		<td colspan=""></td>
				 		<?php if($statusIVAIEPS==1){?>
				 		<td  class="nmcatalogbusquedatit" align="center"><!-- IVA Pagado -->
				 			<input type="button" id="ivapago" name="ivapago" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" title="Ir a asignacion de cuentas"  value="<?php  echo $CuentaIVApagado[1];?>">
				 		</td>
				 		<?php }else{ ?>
			 			<td  class="nmcatalogbusquedatit" align="center">IVA Pagado
		 					<select style="width : 170px" id="ivapago<?php echo $cont; ?>" class="nmcatalogbusquedatit" >
		 						<option value="0">--Elija una cuenta--</option>
					 			<?php echo $listadoivaieps; ?>
		 					</select>
		 				</td>
				 		<?php } ?>
				 		<td align="center" id="iva2<?php echo $cont; ?>"><?php echo number_format($prove['IVA'],2,'.',','); ?> </td>
				 		<td align="center" id="ivacobrado<?php echo $cont; ?>" style="display: none"><input type="text" value="0.00" placeholder="0.00" id="ivacobradoinput<?php echo $cont; ?>" onkeyup="rellena('ivapendienteinput<?php echo $cont; ?>','ivacobradoinput<?php echo $cont; ?>')"/></td>
						<td align="center">0.00</td>
				 	</tr>
				 <?php }
				 if($statusIEPS==1){ 
				   if($prove['IEPS']>0){ ?>
				  	<script>
				  	$(document).ready(function(){
				 		$("#iepspendiente<?php echo $cont ?>,#iepspago<?php echo $cont ?>").select2({ width : "150px" });
      				});
				 	</script>
				 	<tr>
				 		<td colspan=""></td>
				 		<?php if($statusIVAIEPS==1){?>
				 		<td  class="nmcatalogbusquedatit" align="center"><!-- IEPS pendiente de Pago -->
				 			<input type="button" id="iepspendiente" name="iepspendiente" onclick="mandaasignarcuenta();" title="Ir a asignacion de cuentas"  class="nmcatalogbusquedatit" value="<?php  echo $iepspendientepago[1];?>">
				 		</td>
				 		<?php } else{ ?>
			 			<td  class="nmcatalogbusquedatit" align="center">IEPS pendiente de Pago
				 			<select id="iepspendiente<?php echo $cont; ?>">
				 				<option value="0">--Elija una cuenta--</option>
				 				<?php echo $listadoivaieps; ?>
				 			</select>
			 			</td>
				 		<?php }?>
				 		<td align="center">0.00</td>
				 		<td align="center" id="ieps<?php echo $cont; ?>"><?php echo number_format($prove['IEPS'],2,'.',','); ?></td>
				 		<td align="center" id="ipendiente<?php echo $cont; ?>" style="display: none"><input type="text" value="0.00" placeholder="0.00" id="ipendienteinput<?php echo $cont; ?>" disabled/></td>
				 	</tr>
				 	<tr>
				 		<td colspan=""></td>
				 		<?php if($statusIVAIEPS==1){?>
				 		<td  class="nmcatalogbusquedatit" align="center"><!-- IEPS Pagado -->
				 			<input type="button" id="iepspago" name="iepspago" onclick="mandaasignarcuenta();" title="Ir a asignacion de cuentas" class="nmcatalogbusquedatit" value="<?php  echo $CuentaIEPSpagado[1];?>">
				 		</td>
				 		<?php } else{ ?>
				 			<td  class="nmcatalogbusquedatit" align="center">IEPS Pagado
				 			<select id="iepspago<?php echo $cont; ?>">
				 				<option value="0">--Elija una cuenta--</option>
				 				<?php echo $listadoivaieps; ?>
				 			</select>
				 			</td>
				 		<?php } ?>
				 		<td align="center" id="ieps2<?php echo $cont; ?>"><?php echo number_format($prove['IEPS'],2,'.',','); ?></td>
				 		<td align="center" id="icobrado<?php echo $cont; ?>" style="display: none"><input type="text" value="0.00" placeholder="0.00" id="icobradoinput<?php echo $cont; ?>" onkeyup="rellena('ipendienteinput<?php echo $cont; ?>','icobradoinput<?php echo $cont; ?>')"/></td>
					 	<td align="center">0.00</td>
				 	</tr>
				 <?php }
				 } ?>
				 <tr><td colspan="7"><hr></hr></td></tr>
			
			<?php $cont++; }
			 } ?></tbody>
				
	</table><br>
		</div>	
		<?php if(isset( $_COOKIE['ejercicio'])){ ?>
			<input type="hidden" value="<?php echo $_COOKIE['ejercicio']; ?>" id="ejercicio" name="ejercicio">
			<input type="hidden" value="<?php echo $_COOKIE['periodo']; ?>" id="idperiodo" name="idperiodo">	
		<?php }else{ ?>
			<input type="hidden" value="<?php echo $ejercicio; ?>" id="ejercicio" name="ejercicio">
			<input type="hidden" value="<?php echo $idperiodo; ?>" id="idperiodo" name="idperiodo">	
		<?php } ?>
<input type="button" class="nminputbutton" value="Agregar Poliza"  id="agregaprevio" onclick="antesdeguardar(<?php echo $cont; ?>);"/>	
<input type="button" class="nminputbutton" value="Agregar Poliza"  id="agrega" onclick="guarda();" style="display: none"/>
				<img src="images/loading.gif" style="display: none" id="load">

		</div>
	</body>
</html>
<script>
	//beneficiari();
</script>
