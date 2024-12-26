<!DOCTYPE html>
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script src="js/moment.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<script type="text/javascript" src="js/sessionejer.js"></script>
	<link rel="stylesheet" type="text/css" href="css/clipolizas.css"/>
	<script type="text/javascript" src="js/poliprovisional.js" ></script>
	<script src="js/select2/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> 
<?php 
if(isset($_COOKIE['ejercicio'])){
	$NombreEjercicio = $_COOKIE['ejercicio'];
	$v=1;
}else{
	$NombreEjercicio = $Ex['NombreEjercicio'];
	$v=0;
}
$cuenta="";
		while($ingre=$cuentaingresos->fetch_array()){
			$cuenta .= "<option value=".$ingre['account_id'].">".$ingre['description']."(".$ingre['manual_code'].")</option>";
		}
		$cuentaegre="";
		while($ingre=$cuentaegresos->fetch_array()){
			$cuentaegre .= "<option value=".$ingre['account_id'].">".$ingre['description']."(".$ingre['manual_code'].")</option>";
		} 
		$segmento="";
		while($LS = $ListaSegmentos->fetch_assoc()){
			$segmento .= "<option value=".$LS['idSuc'].">".$LS['nombre']."</option>";
		} 
		$sucursal="";
		while($LS = $ListaSucursales->fetch_assoc()){
			$sucursal .= "<option value='".$LS['idSuc']."'>".$LS['nombre']."</option>";
		} 
		$cuentaparaimpuest = "";
		while($ingre = $cuentaivas->fetch_array()){
			$cuentaparaimpuest .= "<option value=".$ingre['account_id'].">".$ingre['description']."(".$ingre['manual_code'].")</option>";
		} 
		$cuentacliente="";
		while ($li=$cuentalista->fetch_array()){
			$cuentacliente .= "<option value=".$li['account_id'].">".$li['description']."(".$li['manual_code'].")</option>";
		} 
		$cuentaprove="";
		while ($pr=$cuentaprov->fetch_array()){
			$cuentaprove .= "<option value=".$pr['account_id'].">".$pr['description']."(".$pr['manual_code'].")</option>";
		}
?>

<script>
function antesdeguardar(cont){
		var i=0; var status=0; var tipo="";var arra= "";
	  	for(i;i<cont;i++){
	  <?php if($statusIVAIEPS==1){ ?>
		  		if($("#ivaingre").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IVA ");  return false;}
			<?php	if($statusIEPS==1){?>
						if($("#ieps").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IEPS ");  return false;}
	<?php			}
		 }
		if($statusIEPS==0){?>
			if($("#ieps").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion Cuenta de Gasto ");  return false;}
	<?php }
	  		if($statusRetencionISH==1){?>
	  			if($("#IVA").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IVA retenido");  return false;}
				if($("#ISR").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas ISR retenido");  return false;}
				if($("#ish").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas ISH");  return false;}
	<?php   } 
	  if(isset($_SESSION['comprobante'])){
	  	if($_SESSION['comprobante']==1){?>
	  		tipo="cliente";arra="provisioncliente";
	  <?php	}else{?>
	  		tipo = "proveedor"; arra = "poliprove";
	 <?php  }
	  }
	  	?>
		
		  	 $.post('index.php?c=CaptPolizas&f=guardanewvaloresprovision',{
	  			cont : i,
		  		ivapendiente : $("#ivaingre"+i).val(),//cuenta
				iepspendiente : $("#ieps"+i).val(),
				iva : $("#IVA"+i).val(),
				isr : $("#ISR"+i).val(),
				ish : $("#ish"+i).val(),
				CuentaClientes : $("#CuentaClientes"+i).val(),
				CuentaProveedores : $("#CuentaProveedores"+i).val(),
				segmento : $("#segmento"+i).val(),
				sucursal : $("#sucursal"+i).val(),
				cuentacompraventa:$("#cuentaingre"+i).val(),
				concepto:$("#concepto"+i).val(),
				tipo : tipo,
				array:arra
			 },function(resp){
	  			status+=1;
	  			
	  			if(status==cont ){
		 			$("#agrega").click();
				}
	  		 });
  		
	 	}
	 	
	 	
  }		 
dias_periodo(<?php echo $NombreEjercicio; ?>,<?php echo $v; ?>)
</script>
</head>
<body>
<div class=" nmwatitles ">Polizas de provision</div><br>
<fieldset style="width: 58.5%">
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
<form method="post" ENCTYPE="multipart/form-data" action="index.php?c=CaptPolizas&f=guardaProvisionMultiple" id="formulario" onsubmit="sele()">
<b>Fecha de poliza:</b>
<?php if(isset($_SESSION['fechaprovi'])){ ?>
					<td><input  type="date" class="nminputtext" id="fecha" name="fecha" value="<?php echo $_SESSION['fechaprovi']; ?>" onmousemove="javascript:fechadefault()" /></td>
					<?php }else{ ?>
					<td><input  type="date" class="nminputtext" id="fecha" name="fecha" onmousemove="javascript:fechadefault()" /></td>
						<?php } ?>

<br>
<table>
	<tr>
		<td></td>
		<td><input type="button" value="Facturas no Asignadas" onclick="abrefacturas()"/></td>
	</tr>
	<tr>
		<td>Selecione un comprobante: 
			<select id="comprobante" name="comprobante" onchange="" class="nminputselect" >
				<option value="0" selected="">Elija una opcion.</option>
				<option value="1">Ingresos</option>
				<option value="2">Egresos</option>
			</select>
		</td>
		<td>
			<input id="xml" type="file" multiple="" name="xml[]" >
		</td>
		<td>
			<input type="submit" name="Submit" class="nminputbutton" value="Previsualizar" > 	
		</td>
	</tr>
</table>
<!-- <input type="submit"  id="envia" style="display: none" onclick=""> --><img src="images/loading.gif" style="display: none" id="load">
<div id='cargando-mensaje' style='font-size:12px;color:blue;width:20px;display: none;'> Cargando...</div>
</form>
<table id="datos" align="" cellpadding="2" border="0" style="border: white 1px solid; " width="60%">
				<thead>
					<tr>
						
						<td class="nmcatalogbusquedatit" align="center">Concepto/Referencia</td>
						<td class="nmcatalogbusquedatit" align="center"></td>
						<td class="nmcatalogbusquedatit" align="center">Cargo</td>
						<td class="nmcatalogbusquedatit" align="center">Abono</td>
						<td class="nmcatalogbusquedatit" align="center">XML</td>
						<td class="nmcatalogbusquedatit" align="center">Segmento</td>
						<td class="nmcatalogbusquedatit" align="center">Sucursal</td>
						
					</tr>
					<tr><td colspan="7"><hr></hr></td></tr>
				</thead>
				<?php $cont=0;// print_r($_SESSION['provisioncliente']);
				if($_SESSION['comprobante']==1){ ?>
					<tbody>
		<?php	 foreach($_SESSION['provisioncliente'] as $cliente){
					 	
						foreach($cliente as $cli){ 
					?>	
							
					<script>
					$(document).ready(function(){
						$("#cuentaingre<?php echo $cont; ?>,#sucursal<?php echo $cont; ?>,#segmento<?php echo $cont; ?>,#CuentaClientes<?php echo $cont; ?>").select2({width : "130px"});
					});
					</script>
			 <tr><td colspan="7"><hr id></hr></td></tr>
				 <tr>
					 <td align="center" width="1%"><b>Ref:</b> <?php echo $cli['referencia']; ?></td>
					 <td  class="nmcatalogbusquedatit" align="center">
					 	<img style="vertical-align:middle;" title="Abrir Ventana de Cuentas" onclick="iracuenta()" src="images/cuentas.png">
						<img style="vertical-align:middle;" title="Actualizar Cuentas" onclick="cuentaingresosact(<?php echo $cont; ?>)" src="images/reload.png">
						<br></br>		
						<select id="cuentaingre<?php echo $cont; ?>" name="cuentaingre<?php echo $cont; ?>" class="nminputselect" >
							<?php echo $cuenta; ?>
						</select>
					 </td>
					 <td align="center">0.00</td>
				     <td align="center" ><?php echo number_format($cli['abono'],2,'.',','); ?></td>
				    <td align="center" width="3px;"><?php echo utf8_encode($cli['xml']); ?></td> 
				     <td align="center" id="">
				     	 <select name='segmento<?php echo $cont; ?>' id='segmento<?php echo $cont; ?>' style='width: 100px;text-overflow: ellipsis;'  class="nminputselect">
							<?php echo $segmento; ?>
						</select>
				     </td>
				     <td align="center" id="">
				     	<select name='sucursal<?php echo $cont; ?>' id='sucursal<?php echo $cont; ?>' style='width: 100px;text-overflow: ellipsis;'  class="nminputselect">
						<?php echo $sucursal; ?>
						</select>
				     </td>
				     <td>
				      </td>
				     <td><img src="images/eliminado.png" title="Eliminar Movimiento"  onclick="borra(<?php echo $cont; ?>,'provisioncliente');"/></td>

				  </tr>
				
			<?php if($cli['abono2']>0){ ?>
				 <tr><td></td>
			<?php if($statusIVAIEPS==1){ ?>
							<td  class="nmcatalogbusquedatit" align="center">
							<input type="button" id="ivaingre" name="ivaingre<?php echo $cont; ?>" title="Ir a asignacion de cuentas" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" value="<?php  echo $ivapendientecobro[1];?>">
							</td>
				<?php }else{?>
						<script>
							$(document).ready(function(){
								$("#ivaingre<?php echo $cont; ?>").select2({width : "130px"});
							});
						</script>
						<td  class="nmcatalogbusquedatit" align="center">
							<font color="red" face="Comic Sans MS,arial,verdana">IVA Pendiente de cobro</font>
							<select id="ivaingre<?php echo $cont; ?>" name="ivaingre<?php echo $cont; ?>" class="nminputselect">
								<?php echo $cuentaparaimpuest; ?>
							</select>
						</td> 
				<?php } ?> 		
					<td align="center">0.00</td>
					<td align="center" id="importe"><?php echo number_format($cli['abono2'],2,'.',',');?></td>
				 </tr>
				<?php } ?> 
				
				<tr>
					
					<?php if($cli['ieps']>0){ ?>
							<td></td>
					<?php if($statusIEPS==1){
							 if($statusIVAIEPS==1){ ?>
							<td  class="nmcatalogbusquedatit" align="center">
								<input type="button" id="ieps" name="ieps" title="Ir a asignacion de cuentas"  onclick="mandaasignarcuenta();"  class="nmcatalogbusquedatit" style="width: 100%" value="<?php  echo $iepspendientecobro[1]; ?>"/>
							</td>
					<?php }else{?>
							<script>
								$(document).ready(function(){
									$("#ieps<?php echo $cont; ?>").select2({width : "130px"});
								});
							</script>
							<td class="nmcatalogbusquedatit" align="center">
							<font color="red" face="Comic Sans MS,arial,verdana">Cuenta para IEPS</font>
							<select id="ieps<?php echo $cont; ?>" name="ieps<?php echo $cont; ?>" class="nminputselect">
								<?php echo $cuentaparaimpuest; ?>
							</select>
							</td>
					<?php }
						}else{ ?>
							<td>
							<input type="button" id="ieps" name="ieps" title="Ir a asignacion de cuentas"  onclick="mandaasignarcuenta();"  class="nmcatalogbusquedatit" style="width: 100%" value="<?php  echo $CuentaIEPSgasto[1]; ?>"/>
							</td>
					<?php } ?>
						<td align="center">0.00</td>
						<td align="center" id="importeieps"><?php echo number_format($cli['ieps'],2,'.',',');?></td>
					<?php } ?>
					</tr>
					<!-- ISH -->
					<tr>
					
					<?php if($cli['ish']>0 ){ ?>
					
					<td></td>
						<td class="nmcatalogbusquedatit" align="center"><!-- Cuenta para ISH -->
							<?php if($statusRetencionISH==1){ ?>
									<input type="button" id="ish" name="ish" title="Ir a asignacion de cuentas"  onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%" value="<?php   echo $ishh[1]; ?>"/> 					
							<?php }else{?>
									<script>
										$(document).ready(function(){
											$("#ish<?php echo $cont;?>").select2({width : "130px"});
										});
									</script>
									<font color="red" face="Comic Sans MS,arial,verdana">Cuenta para ISH </font>
									<select id="ish<?php echo $cont;?>" name="ish<?php echo $cont;?>" class="nminputselect" style='width: 100px;text-overflow: ellipsis;' >
										<?php echo $cuentaparaimpuest; ?>
									</select>
							<?php } ?>
						</td>
					
						<td align="center">0.00</td>
						<td align="center" id="importeish<?php echo $cont; ?>"><?php echo number_format($cli['ish'],2,'.',','); ?></td>
					<?php } ?>
					</tr>
					<!--FIN  ISH -->
				 <tr>
				 	<td rowspan="1" align="center"><b>Concepto</b><textarea id="concepto<?php echo $cont; ?>"><?php echo $cli['concepto'];?></textarea></td>
					 <td  class="nmcatalogbusquedatit" align="center" ><?php echo $cli['nombre']; ?></td>
				     <td align="center" id="total<?php echo $cont; ?>"><?php echo number_format($cli['cargo'],2,'.',','); ?></td>
				     <td align="center">0.00</td>
				     <td><?php 
							if(isset($cli['listacliente'])){?>
								<font color="red" face="Comic Sans MS,arial,verdana">Por favor elija una cuenta para el movimiento del cliente.</font>
								<img style="vertical-align:middle;" title="Abrir Ventana de Cuentas" onclick="iracuenta()" src="images/cuentas.png">
								<img style="vertical-align:middle;" title="Actualizar Cuentas" onclick="actualizaCuentascli(<?php echo $cont ;?>)" src="images/reload.png">
								
								<select id="CuentaClientes<?php echo $cont; ?>">
									<!-- <option selected="" value="-1"> Elija una cuenta</option> -->
									<?php echo $cuentacliente; ?>
								</select>
						<?php	} ?></td>
				 </tr>
				  <!-- retencion -->
				  <tr><td colspan="4" class="nmwatitles">Retencion</td></tr>
				 <?php foreach ( $cli['retenidos'] as $key => $value){ ?>
				 	
				<tr>
				 	<td rowspan="1" align="center">Cuenta para <?php echo $key; ?> </td>
				 	
				 	<?php 
				 	if($statusRetencionISH==1){
				 		
				 		if($key=="IVA"){ ?>
					 		<td class="nmcatalogbusquedatit" align="center">
								<input type="button" id="IVA" name="<?php echo $key.$cont;?>" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%" value="<?php  echo $ivaretenido[1]; ?>"/>
							</td>	
				 <?php	} 
				 		if($key=="ISR"){?>
					 		<td class="nmcatalogbusquedatit" align="center">
								<input type="button" id="ISR" name="<?php echo $key.$cont;?>" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%" value="<?php   echo $isrretenido[1]; ?>"/>
							</td>	
				 <?php	}
					}else{?>
						<script>
				 		$(document).ready(function(){
							$("#<?php echo $key.$cont;?>").select2({width : "100px"});
						});
				 	</script>
						<td class="nmcatalogbusquedatit" align="center">
					 		<select id="<?php echo $key.$cont;?>" class="nminputselect" style='width: 100px;text-overflow: ellipsis;'> 
								<?php echo $cuentaparaimpuest; ?>
							</select>
						</td>	
						
				<?php } ?>
				    <td align="center" id="total<?php echo $key.$cont; ?>" name="total<?php echo $key.$cont; ?>"><?php echo number_format($value,2,'.',','); ?></td>
				    <td align="center">0.00</td>
				 </tr>
				 <?php } ?>
				<!-- fin retencion -->
				 <tr><td colspan="7"><hr></hr></td></tr>
				</tbody>
			<?php  $cont++; } }
			}
			if($_SESSION['comprobante']==2){ ?>
				
			<tbody>

<?php  foreach($_SESSION['poliprove'] as $pro){
		 	foreach($pro as $prove){ ?>
		 		<script>
					$(document).ready(function(){
						$("#cuentaingre<?php echo $cont; ?>,#sucursal<?php echo $cont; ?>,#segmento<?php echo $cont; ?>,#CuentaClientes<?php echo $cont; ?>").select2({width : "130px"});
					});
					</script>
				<tr><td colspan="7"><hr id></hr></td></tr>
	 			<tr>
		 			<td align="center" width="1%"><b>Ref:</b> <?php echo $prove['referencia']; ?></td>
		 			<td  class="nmcatalogbusquedatit" align="center">
						<img style="vertical-align:middle;" title="Abrir Ventana de Cuentas" onclick="iracuenta()" src="images/cuentas.png">
						<img style="vertical-align:middle;" title="Actualizar Cuentas" onclick="cuentaegresosact(<?php echo $cont; ?>)" src="images/reload.png">
						<br></br>		
						<select id="cuentaingre<?php echo $cont; ?>" name="cuentaingre<?php echo $cont; ?>" class="nminputselect" >
							<?php echo $cuentaegre; ?>
						</select>
					</td>
					<td align="center" id="subtotalegre"><?php echo number_format(floatval($prove['cargo']),2,'.',''); ?></td>
			 		<td align="center">0.00</td>
					<td align="" name="xml" id="xml" style="size: 10px"><?php echo utf8_encode( $prove['xml']); ?></td> 
					<td align="center" id="">
			     		<select name='segmento<?php echo $cont; ?>' id='segmento<?php echo $cont; ?>' style='width: 100px;text-overflow: ellipsis;'  class="nminputselect">
							<?php echo $segmento; ?>
						</select>
			    		</td>
			    		<td align="center" id="">
			     		<select name='sucursal<?php echo $cont; ?>' id='sucursal<?php echo $cont; ?>' style='width: 100px;text-overflow: ellipsis;'  class="nminputselect">
						<?php echo $sucursal; ?>
						</select>
			    		</td>
			   		<td>
			   			<img src="images/eliminado.png" title="Eliminar Movimiento"  onclick="borra(<?php echo $cont; ?>,'poliprove');"/>
			   		</td>
				</tr>
				

		<?php if($prove['cargo2']>0){ ?>
				<tr><td></td>
			<?php if($statusIVAIEPS==1){ ?>
				<td   align="center">
				<input type="button" id="ivaingre" name="ivaingre<?php echo $cont; ?>" title="Ir a asignacion de cuentas" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%;white-space: normal;" value="<?php   echo $ivapendientepago[1]; ?>"/>
				</td>
			<?php }else{?>
					<script>
						$(document).ready(function(){
							$("#ivaingre<?php echo $cont; ?>").select2({width : "130px"});
						});
					</script>
					<td  class="nmcatalogbusquedatit" align="center">
						<font color="red" face="Comic Sans MS,arial,verdana">IVA Pendiente de Pago</font>
						<select id="ivaingre<?php echo $cont; ?>" name="ivaingre<?php echo $cont; ?>" class="nminputselect">
							<?php echo $cuentaparaimpuest; ?>
						</select>
					</td> 
			<?php } ?>
			<td align="center" id="importeegre"><?php if($prove['cargo2']){echo number_format($prove['cargo2'],2,'.','');}else{ echo 0;} ?></td>
			<td align="center">0.00</td>
		<?php } ?> 
			</tr>
			
	<?php if($prove['ieps']>0){ ?>
			<tr><td></td>
		<?php if($statusIEPS==1){
		 	if($statusIVAIEPS==1){ ?>
				<td align="center">
				<input type="button" id="ieps" name="ieps<?php $cont;?>" title="Ir a asignacion de cuentas" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%" value="<?php   echo $iepspendientepago[1]; ?>"/>
				</td>
		<?php }else{ ?>
				<script>
				$(document).ready(function(){
					$("#ieps<?php echo $cont; ?>").select2({width : "130px"});
				});
				</script>
				<td class="nmcatalogbusquedatit" align="center">
				<font color="red" face="Comic Sans MS,arial,verdana">Cuenta para IEPS</font>
				<select id="ieps<?php echo $cont; ?>" name="ieps<?php echo $cont; ?>" class="nminputselect">
					<?php echo $cuentaparaimpuest; ?>
				</select>
				</td>
			<?php }	
			}else{ ?>
				<td>
				<input type="button" id="ieps" name="ieps" title="Ir a asignacion de cuentas"  onclick="mandaasignarcuenta();"  class="nmcatalogbusquedatit" style="width: 100%" value="<?php  echo $CuentaIEPSgasto[1]; ?>"/>
				</td>
		<?php } ?>

			<td align="center" id="importeiepsegre"><?php echo number_format($prove['ieps'],2,'.','');?></td>
			<td align="center">0.00</td>

	<?php } ?>

		</tr>
		<!-- ISH -->
		<tr>
	<?php if($prove['ish']>0){ ?>
		<td></td>
		<td class="nmcatalogbusquedatit" align="center"><!-- Cuenta para ISH -->
		<?php if($statusRetencionISH==1){ ?>
				<input type="button" id="ish" name="ish" title="Ir a asignacion de cuentas"  onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%" value="<?php   echo $ishh[1]; ?>"/> 					
		<?php }else{?>
				<script>
					$(document).ready(function(){
						$("#ish<?php echo $cont;?>").select2({width : "130px"});
					});
				</script>
				<font color="red" face="Comic Sans MS,arial,verdana">Cuenta para ISH </font>
				<select id="ish<?php echo $cont;?>" name="ish<?php echo $cont;?>" class="nminputselect" style='width: 100px;text-overflow: ellipsis;' >
					<?php echo $cuentaparaimpuest; ?>
				</select>
		<?php } ?>
				</td>
	
				<td align="center" id="importeishegre"><?php echo number_format(floatval($prove['ish']),2,'.','');?></td>
				<td align="center">0.00</td>

	<?php } ?>
		</tr>
		<!--FIN  ISH -->

		<tr>
			<td rowspan="1" align="center"><b>Concepto</b><textarea id="concepto<?php echo $cont; ?>"><?php echo $prove['concepto'];?></textarea></td>
			<td  class="nmcatalogbusquedatit" align="center"><?php echo ($prove['nombre']); ?></td>
			<td align="center">0.00</td>
			<td align="center" id="totalegre<?php echo $cont; ?>"><?php echo number_format(floatval($prove['abono']),2,'.',''); ?></td>
			<?php 
			if(isset($prove['listaprove'])){?>
				<script>
				$(document).ready(function(){
					$("#CuentaProveedores<?php echo $cont; ?>").select2({width : "130px"});
				});
				</script>
				<td>
				<font color="red" face="Comic Sans MS,arial,verdana">Por favor elija una cuenta para el movimiento del Proveedor.</font>
				<img style="vertical-align:middle;" title="Abrir Ventana de Cuentas" onclick="iracuenta()" src="images/cuentas.png">
				<img style="vertical-align:middle;" title="Actualizar Cuentas" onclick="actualizaCuentas(<?php echo $cont; ?>)" src="images/reload.png">
				
				<select id="CuentaProveedores<?php echo $cont; ?>">
					<?php echo $cuentaprove; ?>
				</select>
				</td>
	<?php	} ?> 
		</tr>
<!-- retencion -->
  		<tr><td colspan="4" class="nmwatitles">Retencion</td></tr>
		<?php foreach ( $prove['retenidos'] as $key => $value){ ?>
		<tr>
				 	<td rowspan="1" align="center">Cuenta para <?php echo $key; ?> </td>
				 	
				 	<?php 
				 	if($statusRetencionISH==1){
				 		
				 		if($key=="IVA"){ ?>
					 		<td class="nmcatalogbusquedatit" align="center">
								<input type="button" id="IVA" name="<?php echo $key.$cont;?>" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%" value="<?php  echo $ivaretenido[1]; ?>"/>
							</td>	
				 <?php	} 
				 		if($key=="ISR"){?>
					 		<td class="nmcatalogbusquedatit" align="center">
								<input type="button" id="ISR" name="<?php echo $key.$cont;?>" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%" value="<?php   echo $isrretenido[1]; ?>"/>
							</td>	
				 <?php	}
					}else{?>
						<script>
				 		$(document).ready(function(){
							$("#<?php echo $key.$cont;?>").select2({width : "100px"});
						});
				 	</script>
						<td class="nmcatalogbusquedatit" align="center">
					 		<select id="<?php echo $key.$cont;?>" class="nminputselect" style='width: 100px;text-overflow: ellipsis;'> 
								<?php echo $cuentaparaimpuest; ?>
							</select>
						</td>	
						
				<?php } ?>
				<td align="center">0.00</td>
			    <td align="center" id="total<?php echo $key; ?>" name="total<?php echo $key; ?>"><?php echo number_format(floatval($value),2,'.',''); ?></td>
				</tr>

			<?php }//foreach retencion ?>
					<!-- fin retencion -->

				<tr><td colspan="6"><hr></hr></td></tr>
	<?php  $cont++; }//foreach interno
     	} ?>
		</tbody>
<?php } ?>
	</table>
<div class=" nmwatitles ">TOTAL</div><br>
	<table class="captura">
		<tr>
			<td>Cargos: <b>$<label id="cargo"></label> </b></td><td>Abonos: <b>$<label id="abono"></label></b></td><td>Diferencia: <b  style="color:red;"> $<label id="dife"></label></b></td>
		</tr>
	</table>	
<div class=" nmwatitles "></div><br>		
<img src="images/loading.gif" style="display: none;" id="load2">
	<input type="button" class="nminputbutton" value="Agregar Poliza"  id="agregaprevio" onclick="antesdeguardar(<?php echo $cont; ?>);"/>	
	<input type="button" class="nminputbutton" value="Agregar Poliza"  id="agrega" onclick="guardaprovimultiple();" style="display: none;"/>
	<input type="button" class="nminputbutton" value="Cancelar Poliza"  id="cancela" onclick="cancela();" />
<script>
$(function () {
         var cargo=0;var abono=0;
        $("#datos tbody tr").each(function (index) //recorre todos los tr
        {
            $(this).children("td").each(function (index2) //en la fila actual recorremos los td
            {
                switch (index2) //indice
                {
                    case 2: cargo += parseFloat($(this).text().replace(',',''));
                            break;
                    case 3: abono += parseFloat($(this).text().replace(',',''));
                            break;
                }
            })
        })
          $("#abono").html(abono.toFixed(2));
          $("#cargo").html(cargo.toFixed(2));
          $("#dife").html( Math.abs((cargo.toFixed(2)-abono.toFixed(2)).toFixed(2)));
})
<?php if(isset($_SESSION['comprobante'])){?>
		$("#comprobante").val(<?php echo $_SESSION['comprobante']; ?>);
	<?php } 
	if(isset($_SESSION['provisioncliente']) || isset($_SESSION['poliprove'])){?>
		$("#comprobante").attr("disabled",true);
	<?php } ?>
	function sele(){
		$("#comprobante").attr("disabled",false);
	}
</script>
<?php if(isset( $_COOKIE['ejercicio'])){ ?>
		<input type="hidden" id="idperio" value="<?php echo $_COOKIE['periodo']; ?>">
		<input type="hidden" id="ejercicio" value="<?php echo $_COOKIE['ejercicio'];?>" />
	<?php }else{ ?>
		<input type="hidden" id="idperio" value="<?php echo $Ex['PeriodoActual']; ?>">
		<input type="hidden" id="ejercicio" value="<?php echo $Ex['NombreEjercicio'];?>" />
	<?php } ?>
</body>
<div id="almacen" style="display: none">
<input type='text' class="nmcatalogbusquedainputtext" id='busqueda' name='busqueda' placeholder='Buscar'>
		<table class='listado'>
		
		</table>
</div>
</html>