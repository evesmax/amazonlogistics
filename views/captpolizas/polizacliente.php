<!DOCTYPE html>
	<head>
		<meta charset="UTF-8" />
		<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/clipolizas.css"/>
		<?php include('../../netwarelog/design/css.php');?>
		<LINK href="../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->
		<script src="js/select2/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
		<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> 
		<script type="text/javascript" src="js/cobroclientes.js"></script>
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
			//$("#ivapendientecobro"+che).val(0);
			//$("#iepspendiente"+che).val(0);
			//$("#ivacobro"+che).val(0);
			//$("#iepscobro"+che).val(0);

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
	  	for(i;i<cont;i++){
	
	   <?php	 if($statusIVAIEPS==0){ ?>
		  		if( ($("#ivapendientecobro"+i).val()==0 || $("#ivacobro"+i).val()==0 )){
		  			alert("Elija una cuenta de IVA!!"); return false;
		  		}
		  <?php if($statusIEPS==1){ ?>
			  		if( ($("#iepspendiente"+i).val()==0 || $("#iepscobro"+i).val()==0 )){
			  			alert("Elija una cuenta de IEPS!!"); return false;
			  		}
			<?php } 
	  	 }else { ?>
			  	if($("#ivapendientecobro").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IVA Trasladado Pendiente de cobro");  return false;}
				if($("#ivacobro").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IVA Trasladado Cobrado");  return false;}
		<?php if($statusIEPS==1){ ?>	
				if($("#iepscobro").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IEPS Trasladado Cobrado");  return false;}
				if($("#iepspendiente").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IEPS Trasladado Pendiente de cobro");  return false;}
		<?php  }
		} 
		?>
		
		  	 $.post('index.php?c=CaptPolizas&f=guardanewvalores',{
	  			cont : i,
		  		imporinput : $("#imporinput2"+i).val(),//import
				ivacobradoinput : $("#ivacobradoinput"+i).val(),//iva 
				ipendienteinput : $("#ipendienteinput"+i).val(),//ieps
				idclien : $("#idcli"+i).val(),//valor para almacenar en array
				
				ivapendiente : $("#ivapendientecobro"+i).val(),//cuenta
				ivacobrado : $("#ivacobro"+i).val(),
				iepspendiente : $("#iepspendiente"+i).val(),
				iepscobro : $("#iepscobro"+i).val(),
				
				array:"tabla"
			 },function(resp){
	  			status+=1;
	  			
	  			if(status==cont ){
		 			$("#agrega").click();
				}
	  		 });
  		
	 	}
	 	
	 	
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
		height:190px;
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
		<div class="nmwatitles">&nbsp;Cobros a Clientes.</b></div>
		<br></br>
<div id="contenedor" class="div dat" align="right">
	<form method="post" ENCTYPE="multipart/form-data" action="index.php?c=CaptPolizas&f=tabla" onsubmit='return validacli(this)'>
		<table>
			<tr>
				<td>Seleccionar xml.</td>
				<td>
			<input type="radio" class="nminputradio" name="radio" id="radio" value="1" onclick="checa()"/>
				</td>
				<td>
					<select id="xml" name="xml[]" style="display: none;" class="nminputselect" multiple="">
						<option value="0" selected="">Seleccione sus xmls</option>
						<?php
						$directorio=opendir('xmls/facturas/temporales'); 
						while ($archivo = readdir($directorio)){
							$solocobros = strpos($archivo, "Cobro");
							if($archivo != '.' && $archivo != '..' && $archivo != '.file' && $archivo !='.DS_Store'){
								if($solocobros==true){
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
					<input type="radio" class="nminputradio" name="radio" id="radio" value="2" onclick="checa()"/>
				</td>
				<td>
					<input type="file" id="xmlsube" name="xmlsube" style="display: none"/>
				</td>
			</tr> -->
			</table>
		<br></br>		 
<div style="position: relative">
	<fieldset style="width: 80.3%">
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
	<fieldset class="datos">
		<legend>C U E N T A S</legend>	
			<table>
				<tr>
					<td>Banco:
						<select id="banco" name="banco">
				<?php 
					  if(isset($bancos)){
						 while($b=$bancos->fetch_array()){ ?>
							<option value='<?php echo $b["account_id"].'/'. $b['description']; ?>'><?php echo $b['description']."(".$b["manual_code"].")"; ?> </option>
				   <?php } 
					} ?>
						</select>
					</td>
				</tr>
				<tr><td></td></tr>
				<tr>
					<td>Cliente:<select id="cliente" name="cliente" onclick="validacuenta()">
							<option value="0">--Elija un cliente--</option>
						<?php 
							if(isset($clientes)){
								while($b=$clientes->fetch_array()){ ?>
									<option value='<?php echo $b["id"].'/'. $b['nombre']; ?>'><?php echo ($b['nombre']); ?> </option>					
						<?php  }
								while($cli=$sqlcli2->fetch_array()){ ?>
									<option value='<?php echo $cli["account_id"].'-'. $cli['description']; ?>'><?php echo utf8_decode($cli['description']."(".$cli['manual_code'].")"); ?> </option>
		
						<?php	}
							 } ?>
						</select>
					</td>
				</tr>
			</table>
			<div id="muestra" style="display: none;">
				<table>
					<tr>
						<td>Cuenta: </td>
						<td>
						<select id="cuentacliente" name="cuentacliente" onclick="agregacuenta()">
							<option value="0">Elija una cuenta</option>
							<?php while($cli2=$cuentasinarbol->fetch_array()){ ?>
									<option value='<?php echo $cli2["account_id"]; ?>'><?php echo utf8_decode($cli2['description']."(".$cli2['manual_code'].")"); ?> </option>
		
							<?php	}?>
						</select>
						</td>
						</tr>
						<input type="hidden"  value="0" id="clientesincuenta" name="clientesincuenta"/>
						
					</tr>
				</table>
			</div>
			<br></br>
			<td><?php echo @$bancosno; ?></td><br>
			<td><?php echo @$clientesno; ?></td>
	</fieldset>
	<fieldset class="datos">
			<legend>D A T O S  &nbsp;  D E   &nbsp;  R E G I S T R O</legend>	
			
			<table>
				<tr>
					<td>Segmento de Negocio:</td>
					<td><select name='segmento' id='segmento' style='width: 165px;height:30px;text-overflow: ellipsis;'  class="nminputselect">
					
					<?php
						while($LS = $ListaSegmentos->fetch_assoc())
						{
							echo "<option value='".$LS['idSuc']."//".$LS['nombre']."'>".$LS['nombre']."</option>";
						}
						?>
					</select></td>
				<tr>
					<td>Sucursal:</td>
					<td><select name='sucursal' id='sucursal' style='width: 165px;height:30px;text-overflow: ellipsis;'  class="nminputselect">
					
					<?php
						while($LS = $ListaSucursales->fetch_assoc())
						{
							echo "<option value='".$LS['idSuc']."//".$LS['nombre']."'>".$LS['nombre']."</option>";
						}
						?>
						</select>
					</td>
				</tr>
			<tr>
				<td>Concepto: </td>&nbsp;&nbsp;
				<td><input type="text" class="nminputtext" placeholder="Concepto..." id="concepto" name="concepto"/>&nbsp;&nbsp;&nbsp;&nbsp;</td>
			</tr>
			<tr>	
				<td>Fecha Poliza:</td>
				<td>
					<?php if(isset($_SESSION['fechacli'])){ ?>
					<input  type="date" class="nminputtext" id="fecha" onmousemove="javascript:fechadefault()" name="fecha" value="<?php echo $_SESSION['fechacli']; ?>" style='width: 165px;height:30px '/>
					<?php }else{ ?>
						<input  type="date" class="nminputtext" id="fecha" onmousemove="javascript:fechadefault()" name="fecha" style='width: 165px;height:30px '/>
						<?php } ?>
				</td>
			</tr>
		</table>
</fieldset>
<br>


</div><br>	
<input type="submit" class="nminputbutton_color2" value="Leer XMLs" id="agregar" >
</form>	
			
		<div id="movimientos" align="center" style="width: 70%">
			<table id="datos" align="center" cellpadding="2" border="0" style="border: white 1px solid;background: #F2F2F2"  width="100%">
				<thead>
					<tr>
						<td></td>
						<td></td>
						<td class="nmcatalogbusquedatit" align="center">Cargo</td>
						<td  class="nmcatalogbusquedatit" align="center">Abono</td>
						<td class="nmcatalogbusquedatit" align="center">XML</td>
						<td class="nmcatalogbusquedatit" align="center">Segmento</td>
						<td class="nmcatalogbusquedatit" align="center">Sucursal</td>
					</tr>
					<tr><td colspan="7"><hr></hr></td></tr>
				</thead>
					<tbody><?php 
						 //echo count($_SESSION['tabla']);
						
					$cont=0;
					//echo $_SESSION['tabla'][2]['101-CLIENTETEST']['cliente'];
						 foreach($_SESSION['tabla'] as $cli){
						 	//echo count($cli);
							foreach($cli as $cliente){
							if(strrpos($cliente['cliente'],'/')){
								 $clie=explode('/',$cliente['cliente']); 
							}else{
							 $clie=explode('-',$cliente['cliente']); 
							} 
							$segment = explode('//',$cliente['segmento']);
							$sucu = explode('//',$cliente['sucursal']);
						
							?>
			
			 <tr><td colspan="7"><hr></hr>
			 	<input type="checkbox"  checked="" id="<?php echo $cont; ?>" onclick="pagoss(<?php echo $cont; ?>)"/>Pago Total</td></tr>
				 <tr>
				 	<input type="hidden" value="<?php echo $cliente['cliente']; ?>" id="idcli<?php echo $cont; ?>"/>
					 <td rowspan="2" align="center"><b><?php echo utf8_decode($clie[1]); ?></b><br><?php echo $cliente['concepto']; ?></td>
					 <td  class="nmcatalogbusquedatit" align="center">Clientes</td>
					 <td align="center">0.00</td>
					 <td align="center" id="impor<?php echo $cont; ?>"><?php echo number_format($cliente['importe'],2,'.',','); ?> </td>
					  <td align="center" style="display: none" id="imporintro<?php echo $cont; ?>" ><input type="text" placeholder="0.00" value="0.00" id="imporinput<?php echo $cont; ?>" disabled/></td>
					 <td></td>
					 <td align="center"><?php echo $segment[1]; ?></td>
					 <td align="center"><?php echo $sucu[1]; ?></td>
				 </tr>
				 <tr>
					 <td  class="nmcatalogbusquedatit" align="center">Bancos</td>
					 <td align="center" id="impor2<?php echo $cont; ?>"><?php echo number_format($cliente['importe'],2,'.',','); ?></td>
					 <td align="center" style="display: none" id="imporintro2<?php echo $cont; ?>"><input  type="text" placeholder="0.00" value="0.00" id="imporinput2<?php echo $cont; ?>" onkeyup="rellena('imporinput<?php echo $cont; ?>','imporinput2<?php echo $cont; ?>')" /></td>
					 <td align="center">0.00</td>
					 <td align="center"><?php echo ($cliente['xml']); ?></td>
					 <td colspan="4"></td>
					 <td><img src="images/eliminado.png" title="Eliminar Movimiento"  onclick="borra(<?php echo $cont; ?>);"/></td>
				 </tr>
				 <?php if($cliente['IVA']>0){ //pato?>
				 	<script>
				 	$(document).ready(function(){
				 		$("#ivapendientecobro<?php echo $cont ?>,#ivacobro<?php echo $cont ?>").select2({
        					 width : "150px"
       					 });
					});
      
				 	</script>

				 	<tr>
				 		<td colspan=""></td>
				 			<?php if($statusIVAIEPS==1){?>
				 				<td  class="nmcatalogbusquedatit" align="center"><!-- IVA pendiente de cobro -->
									<input type="button" id="ivapendientecobro" title="Ir a asignacion de cuentas" name="ivapendientecobro" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" value="<?php  echo $ivapendientecobro[1];?>">
								</td>
							<?php }else{?>
								<td  class="nmcatalogbusquedatit" align="center">IVA pendiente de cobro
				 					<select id="ivapendientecobro<?php echo $cont; ?>" class="nmcatalogbusquedatit" >
				 						<option value="0">--Elija una cuenta--</option>
							 			<?php echo $listadoivaieps; ?>
				 					</select>
				 				</td>
							<?php } ?>
						
				 		<td align="center" id="iva<?php echo $cont; ?>" ><?php echo number_format($cliente['IVA'],2,'.',','); ?></td>
				 		<td align="center" id="ivapendiente<?php echo $cont; ?>" style="display: none"><input type="text" value="0.00" placeholder="0.00" id="ivapendienteinput<?php echo $cont; ?>" disabled/></td>
				 		<td align="center">0.00</td>
				 	</tr>
				 	<tr>
				 		<td colspan=""></td>
				 		<?php if($statusIVAIEPS==1){?>
				 		<td  class="nmcatalogbusquedatit" align="center"><!-- IVA Cobrado -->
				 			<input type="button" id="ivacobro" title="Ir a asignacion de cuentas"  name="ivacobro" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" value="<?php  echo $ivacobrado[1];?>">
						</td>
						<?php }else{?>
								<td  class="nmcatalogbusquedatit" align="center">IVA Cobrado
				 					<select style="width : 170px" id="ivacobro<?php echo $cont; ?>" class="nmcatalogbusquedatit" >
				 						<option value="0">--Elija una cuenta--</option>
							 			<?php echo $listadoivaieps; ?>
				 					</select>
				 				</td>
							<?php } ?>
				 		<td align="center">0.00</td>
				 		<td align="center" id="iva2<?php echo $cont; ?>"><?php echo number_format($cliente['IVA'],2,'.',','); ?> </td>
				 		<td align="center" id="ivacobrado<?php echo $cont; ?>" style="display: none"><input type="text" value="0.00" placeholder="0.00" id="ivacobradoinput<?php echo $cont; ?>" onkeyup="rellena('ivapendienteinput<?php echo $cont; ?>','ivacobradoinput<?php echo $cont; ?>')"/></td>

				 	</tr>
				 <?php }
				if($statusIEPS==1){ 
				 	 if($cliente['IEPS']>0){ ?>
				  	<script>
				  	$(document).ready(function(){
				 		$("#iepspendiente<?php echo $cont ?>,#iepscobro<?php echo $cont ?>").select2({ width : "150px" });
      				});
				 	</script>
				 	<tr>
				 		<td colspan=""></td>
				 		<?php if($statusIVAIEPS==1){?>
				 		<td  class="nmcatalogbusquedatit" align="center"><!-- IEPS pendiente de cobro  -->
				 			<input type="button" id="iepspendiente" title="Ir a asignacion de cuentas"  name="iepspendiente" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" value="<?php  echo $iepspendientecobro[1];?>">
				 		</td>
				 		<?php }else{?>
				 			<td  class="nmcatalogbusquedatit" align="center">IEPS pendiente de cobro
				 			<select id="iepspendiente<?php echo $cont; ?>">
				 				<option value="0">--Elija una cuenta--</option>
				 				<?php echo $listadoivaieps; ?>
				 			</select>
				 			</td>
				 		<?php } ?>
				 		<td align="center" id="ieps<?php echo $cont; ?>"><?php echo number_format($cliente['IEPS'],2,'.',','); ?></td>
				 		<td align="center" id="ipendiente<?php echo $cont; ?>" style="display: none"><input type="text" value="0.00" placeholder="0.00" id="ipendienteinput<?php echo $cont; ?>" disabled/></td>
				 		
				 		<td align="center">0.00</td>
				 	</tr>
				 	<tr>
				 		<td colspan=""></td>
				 		<?php if($statusIVAIEPS==1){?>
				 		<td  class="nmcatalogbusquedatit" align="center"><!-- IEPS Cobrado -->
				 			<input type="button" id="iepscobro" title="Ir a asignacion de cuentas"  name="iepscobro" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" value="<?php  echo $iepscobrado[1];?>">
				 		</td>
				 		<?php }else{?>
				 			<td  class="nmcatalogbusquedatit" align="center">IEPS Cobrado
				 			<select id="iepscobro<?php echo $cont; ?>">
				 				<option value="0">--Elija una cuenta--</option>
				 				<?php echo $listadoivaieps; ?>
				 			</select>
				 			</td>
				 		<?php } ?>
				 		<td align="center">0.00</td>
				 		<td align="center" id="ieps2<?php echo $cont; ?>"><?php echo number_format($cliente['IEPS'],2,'.',','); ?></td>
				 		<td align="center" id="icobrado<?php echo $cont; ?>" style="display: none"><input type="text" value="0.00" placeholder="0.00" id="icobradoinput<?php echo $cont; ?>" onkeyup="rellena('ipendienteinput<?php echo $cont; ?>','icobradoinput<?php echo $cont; ?>')"/></td>

				 	</tr>
				 <?php } 
				 }?>
				 <tr><td colspan="7"><hr></hr></td></tr>
			
			<?php	$cont++; }
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