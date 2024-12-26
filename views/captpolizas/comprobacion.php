<!DOCTYPE html>
<head>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.10.3/jquery-ui.min.js"></script>

	<script src="js/moment.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<script type="text/javascript" src="js/sessionejer.js"></script>
	<link rel="stylesheet" type="text/css" href="css/clipolizas.css"/>
	<script type="text/javascript" src="js/poliprovisional.js" ></script>
	<script type="text/javascript" src="js/anticipogastos.js" ></script>
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
}		$oculto = "display:none;";
		if(isset($_SESSION['deudornombre'])){
			$nombredeudor = explode('//', $_SESSION['deudornombre']);
			$oculto = "";
		}
		$cuentaegre="";
		while($ingre=$cuentaegresos->fetch_array()){
			$cuentaegre .= "<option value=".$ingre['account_id'].">".$ingre['description']."(".$ingre['manual_code'].")</option>";
		} 
		$segmento="";
		while($LS = $ListaSegmentos->fetch_assoc()){$select="";
			if($nombredeudor[3]==$LS['idSuc']){ $select="selected";}
			$segmento .= "<option value=".$LS['idSuc']." $select>".$LS['nombre']."</option>";
		} 
		$sucursal="";
		while($LS = $ListaSucursales->fetch_assoc()){$selec = "";
			if($nombredeudor[4]==$LS['idSuc']){ $selec="selected";}
			$sucursal .= "<option value=".$LS['idSuc']." $selec>".$LS['nombre']."</option>";
		} 
		$cuentaparaimpuest = "";
		while($ingre = $cuentaivas->fetch_array()){
			$cuentaparaimpuest .= "<option value=".$ingre['account_id'].">".$ingre['description']."(".$ingre['manual_code'].")</option>";
		} 
		
		$cuentaprove="";
		while ($pr=$cuentaprov->fetch_array()){
			$cuentaprove .= "<option value=".$pr['account_id'].">".$pr['description']."(".$pr['manual_code'].")</option>";
		}
		$cuentadiferen="";
		while ($pr=$Accounts->fetch_array()){
			$cuentadiferen .= "<option value=".$pr['account_id'].">".$pr['description']."(".$pr[$type_id_account].")</option>";
		}
		
?>

<script>
function antesdeguardar(cont){
		var i=0; var status=0; var tipo="";var arra= "";
		$("#load2").show();$("#agregaprevio").hide();$("#cancela").hide();
	  	for(i;i<cont;i++){
	  <?php if($statusIVAIEPS==1){ ?>
		  		if($("#ivapagado").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IVA "); $("#load2").hide(); $("#agregaprevio").show();$("#cancela").show(); return false;}
		<?php	if($statusIEPS==1){?>	
					if($("#ieps").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IEPS o"); $("#load2").hide();$("#agregaprevio").show();$("#cancela").show(); return false;}
		<?php 	}
	  		}
	  if($statusIEPS==0){?>
			if($("#ieps").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion Cuenta de Gasto ");  $("#load2").hide();$("#agregaprevio").show();$("#cancela").show(); return false;}
	<?php }
	  		if($statusRetencionISH==1){?>
	  			if($("#IVA").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas IVA retenido"); $("#load2").hide(); $("#agregaprevio").show();$("#cancela").show(); return false;}
				if($("#ISR").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas ISR retenido"); $("#load2").hide(); $("#agregaprevio").show();$("#cancela").show(); return false;}
				if($("#ish").val()=='ASIGNE CUENTA'){ alert("Elija una cuenta en Asignacion de Cuentas ISH"); $("#load2").hide(); $("#agregaprevio").show();$("#cancela").show(); return false;}
	<?php   } ?>
	  
		
		  	 $.post('index.php?c=CaptPolizas&f=guardanewvaloresprovision',{
	  			cont : i,
		  		ivapendiente : $("#ivapagado"+i).val(),//cuenta
				iepspendiente : $("#ieps"+i).val(),
				iva : $("#IVA"+i).val(),
				isr : $("#ISR"+i).val(),
				ish : $("#ish"+i).val(),
				segmento : $("#segmento"+i).val(),
				sucursal : $("#sucursal"+i).val(),
				cuentacompraventa:$("#cuentaingre"+i).val(),
				concepto:$("#concepto"+i).val(),
				tipo : "proveedor",
				array:"compruebagasto"
				
			 },function(resp){
	  			status+=1;
	  			
	  			if(status==cont ){ $("#load2").hide();$("#agregaprevio").show();$("#cancela").show();
		 			$("#agrega").click();
				}
	  		 });
  		
	 	}
	 	
	 if($("#cerraranticipo").is(":checked")) {
	 	$("#load2").hide();
	 	$("#agrega").click();
	 }	
  }		 
dias_periodo(<?php echo $NombreEjercicio; ?>,<?php echo $v; ?>)
</script>
</head>
<body>
<div class=" nmwatitles ">Comprobacion de Gastos</div><br>
<div id="contenedor" class="div dat" align="right">
	<fieldset style="width: 85%">
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
	<input type="hidden" id="diferencia" value="<?php echo number_format($Cargos['Cantidad']-$Abonos['Cantidad'], 2,'.',''); ?>" />
	
</fieldset><br>

<form method="post" ENCTYPE="multipart/form-data" action="index.php?c=CaptPolizas&f=comprobacionGastos" id="formulario" >

<fieldset style="width: 150%;">	
	<legend>I N F O R M A C I O N</legend>
<!-- <div style="width: 200px">	 -->
	<table>
	<tr>
		<td id="us" style="">Usuario
				<div style="width: 250px">
					<select id="usuarios" name="usuarios" class="nminputselect" onchange="buscaAnticipo()">
					<option value=0>Elija un usuario</option>
					<?php
						while($user = $usuarios->fetch_assoc()){ 
							if($user['idempleado'] == $_SESSION['usercompro']){ $selec = "selected"; }else{ $selec = ""; }?>
							
							<option value="<?php  echo $user['idempleado']?>"  <?php echo $selec; ?>><?php echo $user['usuario']?></option>
					<?php } ?>
					</select>
				</div>
			</td>
		<td><b>Anticipo</b>
			
			<select id="anticipolista" name="anticipolista" onchange="deudoranticipo()" class="nminputselect">
				<option value="0"> Elija un anticipo</option>
				
			</select>
		</td>
		
		<td><img src="images/loading.gif" style="display: none" id="load3"></td>
		<td>
		<input type="checkbox" id="cerraranticipo" onclick="cerrarAnticipo(<?php echo $nombredeudor[2];?>)"/><font color="red" face="arial,verdana">CERRAR ANTICIPO</font></td>
		</td>
	</tr><tr>
		<td>
			<input id="factura" type="file" multiple="" name="factura[]" style="<?php echo $oculto; ?>">
		</td>
		<td>
			<input type="submit" value="Cargar XMLs" id="submit" class="nminputtext" style="<?php echo $oculto; ?>">
		</td><td></td>
		<td>
			<b>Fecha</b>
		 
			<?php if(isset($_SESSION['fechaanticipo'])){ ?>
			<input  type="date" class="nminputtext" id="fecha" style="height: 30px" name="fecha" value="<?php echo $_SESSION['fechaanticipo']; ?>" onmousemove="javascript:fechadefault()" />
			<?php }else{ ?>
			<input  type="date" class="nminputtext" id="fecha" style="height: 30px" name="fecha" onmousemove="javascript:fechadefault()" />
			<?php } ?>
		</td>
	</tr><tr>
				<td><input type="button" value="Facturas no Asignadas" onclick="abrefacturascomprobacion()" id="facturasalmacen" style="<?php echo $oculto; ?>"/></td>

		
	</tr>
</table>

<!-- </div> -->



</fieldset>
<br>

</form>
<br>
<table id="datos">
	<thead>
		<th class="nmcatalogbusquedatit" align="center">Cuenta</th>
		<th class="nmcatalogbusquedatit" align="center">Cargo</th>
		<th class="nmcatalogbusquedatit" align="center">Abono</th>
		<th class="nmcatalogbusquedatit" align="center">Concepto</th>
		<th class="nmcatalogbusquedatit" align="center">Segmento</th>
		<th class="nmcatalogbusquedatit" align="center">Sucursal</th>
		<th class="nmcatalogbusquedatit" align="center">XML</th>
	</thead>
	<tbody>
		
		<?php  $cont=0;
	foreach($_SESSION['compruebagasto'] as $pro){
		 	foreach($pro as $prove){ 
		 if(!isset($prove['cuentanodeducible'])){?>
		 		<script>
					$(document).ready(function(){
						$("#cuentaingre<?php echo $cont; ?>,#sucursal<?php echo $cont; ?>,#segmento<?php echo $cont; ?>").select2({width : "130px"});
					});
				</script>
		<tr>
			<td  class="nmcatalogbusquedatit" align="center">
			<img style="vertical-align:middle;" title="Abrir Ventana de Cuentas" onclick="iracuenta()" src="images/cuentas.png">
			<img style="vertical-align:middle;" title="Actualizar Cuentas" onclick="cuentaegresosact(<?php echo $cont; ?>)" src="images/reload.png">
			<br>		
			<select id="cuentaingre<?php echo $cont; ?>" name="cuentaingre<?php echo $cont; ?>" class="nminputselect" >
				<?php echo $cuentaegre; ?>
			</select>
			</td>
			<td align="center" id="subtotalegre"><?php echo number_format(floatval($prove['cargo']),2,'.',','); ?></td>
			<td align="center">0.00</td>
			<td rowspan="1" align="center"><textarea id="concepto<?php echo $cont; ?>"><?php echo $prove['concepto'];?></textarea></td>
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
	    		<td align="" name="xml" id="xml" style="size: 10px"><?php echo utf8_encode( $prove['xml']); ?></td> 
			<td>
			   	<img src="images/eliminado.png" title="Eliminar Movimiento"  onclick="borra(<?php echo $cont; ?>,'compruebagasto');"/>
			</td>
		</tr>
	
	<?php }
		 if($prove['cargo2']>0){ ?>
		<tr>
		<?php if($statusIVAIEPS==1){ ?>
			<td   align="center">
			<input type="button" id="ivapagado" name="ivaingre<?php echo $cont; ?>" title="Ir a asignacion de cuentas" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%;white-space: normal;" value="<?php   echo $ivapagado[1]; ?>"/>
			</td>
		<?php }else{?>
			<script>
				$(document).ready(function(){
					$("#ivapagado<?php echo $cont; ?>").select2({width : "130px"});
				});
			</script>
			<td  class="nmcatalogbusquedatit" align="center">
				<font color="red" face="Comic Sans MS,arial,verdana">IVA Acreditable Pagado</font>
				<select id="ivapagado<?php echo $cont; ?>" name="ivapagado<?php echo $cont; ?>" class="nminputselect">
					<?php echo $cuentaparaimpuest; ?>
				</select>
			</td> 
		<?php } ?>
			<td align="center" id="importeegre"><?php if($prove['cargo2']){echo number_format($prove['cargo2'],2,'.',',');}else{ echo 0;} ?></td>
			<td align="center">0.00</td>
		</tr>
	<?php } ?>
	<?php if($prove['ieps']>0){ ?>
		<tr>
	<?php if($statusIEPS==1){
			 if($statusIVAIEPS==1){ ?>
				<td align="center">
				<input type="button" id="ieps" name="ieps" title="Ir a asignacion de cuentas" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%" value="<?php   echo $iepspago[1]; ?>"/>
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
	

			<td align="center" id="importeiepsegre"><?php echo number_format($prove['ieps'],2,'.',',');?></td>
			<td align="center">0.00</td>
		</tr>
<?php }	 ?>
<?php if($prove['ish']>0){ ?>
	<tr>
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
			<td align="center" id="importeishegre"><?php echo number_format(floatval($prove['ish']),2,'.',',');?></td>
			<td align="center">0.00</td>
		</tr>
<?php } ?>
<?php foreach ( $prove['retenidos'] as $key => $value){ ?>
		<tr>
			
<?php if($statusRetencionISH==1){
 		if($key=="IVA"){ ?>
	 		<td class="nmcatalogbusquedatit" align="center">
				<input type="button" id="IVA" name="<?php echo $key.$cont;?>" onclick="mandaasignarcuenta();" class="nmcatalogbusquedatit" style="width: 100%" value="<?php  echo $ivaretenido[1]; ?>"/>
			</td>	
 <?php	} if($key=="ISR"){?>
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
		<td class="nmcatalogbusquedatit" align="center"><font color="red" face="Comic Sans MS,arial,verdana">Cuenta para <?php echo $key; ?></font>
	 		<select id="<?php echo $key.$cont;?>" class="nminputselect" style='width: 100px;text-overflow: ellipsis;'> 
				<?php echo $cuentaparaimpuest; ?>
			</select>
		</td>	
<?php 	} ?>
		<td align="center">0.00</td>
		<td align="center" id="total<?php echo $key; ?>" name="total<?php echo $key; ?>"><?php echo number_format(floatval($value),2,'.',','); ?></td>
	</tr>
<?php } 
if(isset($prove['cuentanodeducible'])){ ?>
	
	<!-- <tr>
		<td  class="nmcatalogbusquedatit" align="center"><?php echo $nombredeudor[0]; ?></td>
		<td align="center">0.00</td>
		<td align="center" id="totalegre<?php echo $cont; ?>"><?php echo number_format(floatval($prove['abono']),2,'.',','); ?></td>
	</tr> -->
	<script>
	$(document).ready(function(){
		$("#cuentaingre<?php echo $cont; ?>,#segmento<?php echo $cont; ?>,#sucursal<?php echo $cont; ?>").select2({
		width : "150px"
			});
		});
	</script>
		<tr id="">
		<td class="nmcatalogbusquedatit" align="center">
			<img style="vertical-align:middle;" title="Abrir Ventana de Cuentas" onclick="iracuenta()" src="images/cuentas.png">
			<img style="vertical-align:middle;" title="Actualizar Cuentas" onclick="cuentaegresosact(<?php echo $cont; ?>)" src="images/reload.png">
			<br>
 			<select id="cuentaingre<?php echo $cont; ?>" class="nminputselect">
 				<?php echo  $prove['listacuenta'];?>
 			</select>
 		</td>
 		<td  align="center" class="" ><?php echo number_format($prove['cargo'],2,'.',',');?></td>
 		<td  align="center" class="">0.00</td>
		<td rowspan="1" align="center"><textarea id="concepto<?php echo $cont; ?>"><?php echo $prove['concepto'];?></textarea></td>
 		<td align="center" id="">
     		<select name='segmento<?php echo $cont; ?>' id='segmento<?php echo $cont; ?>' style='width: 100px;text-overflow: ellipsis;'  class="nminputselect">
				<?php echo  $prove['listasegmento']; ?>
			</select>
    		</td>
    		<td align="center" id="">
     		<select name='sucursal<?php echo $cont; ?>' id='sucursal<?php echo $cont; ?>' style='width: 100px;text-overflow: ellipsis;'  class="nminputselect">
			<?php echo $prove['listasucursal'];  ?>
			</select>
    		</td><td></td>
 		<td class="eliminar">
			 <img src="images/eliminado.png" title="Eliminar Movimiento"  onclick="borra(<?php echo $cont; ?>,'compruebagasto');"/>
		</td>
 		</tr>
 	<?php  } ?>
	<tr><td colspan="7"><hr></td></tr>
<?php  
$cont++;
	}//foreach interno
	
}//foreach principal
 if(isset($_SESSION['deudornombre'])){
 
 ?><tr>
 		<td  class="nmcatalogbusquedatit" align="center"><?php echo $nombredeudor[0]; ?></td>
 		<td align="center" >0.00</td>
 		<td align="center"><?php echo number_format($nombredeudor[2],2,'.',','); ?></td>
 	</tr><tr><td colspan="7"><hr></td></tr>
 <?php }?>	
 </tbody>
	<tfoot>

</tfoot>
 	
 	<tr id="ajuste" style="display: none">
 		<td class="nmcatalogbusquedatit" >
 			<select id="cuentadiferencia" class="nminputselect">
 				<?php echo $cuentadiferen; ?>
 			</select>
 		</td>
 		<td  align="center" class="nmcatalogbusquedatit" ><label id="cargo">0.00</label></td>
 		<td  align="center" class="nmcatalogbusquedatit" ><label id="abono">0.00</label></td>
 		<td colspan="4" class="nmcatalogbusquedatit" ><font color="red" face="Comic Sans MS,arial,verdana" id="txt2" style="display: none">El Deudor no comprobo todos los Gastos</font><font color="red" face="Comic Sans MS,arial,verdana" id="txt1" style="display: none">El Deudor comprobo mas gastos de lo que se le dio como Anticipo</font></td>
 	</tr>
 	
 	<tr class="nmcatalogbusquedatit" id="ajustetotal" style="display: none">
 		<td align="center" class="nmcatalogbusquedatit" >TOTAL</td>
 		<td  align="center" class="nmcatalogbusquedatit" ><label id="cargo2">0.00</label></td>
 		<td  align="center" class="nmcatalogbusquedatit" ><label id="abono2">0.00</label></td>
 		<td colspan="4" class="nmcatalogbusquedatit" ><font color="red" face="Comic Sans MS,arial,verdana">Anticipo cubierto al 100%</font></td>
 	
 	</tr>


</table>

<button type="button" class="btn btn-danger"  onclick="agregadeducible()" id="nodedu">
    				<span class="glyphicon glyphicon-plus"></span> Agregar no deducible
  			</button>
<div class=" nmwatitles ">TOTAL</div><br>
	<table class="captura" style="width: 150%;">
		<tr>
			<td>Cargos: <b>$<label id="cargototal"></label> </b></td><td>Abono total comprobado: <b> $<label id="abonocomprobado"></label></b></td><td>Abono Deudor: <b style="color:red;">$<label id="abonodeudor"></label></b></td>
		</tr>
	</table>	
<img src="images/loading.gif" style="display: none;" id="load2">
	<input type="button" class="nminputbutton" value="Agregar Poliza"  id="agregaprevio" onclick="antesdeguardar(<?php echo $cont; ?>);"/>	
	<input type="button" class="nminputbutton" value="Agregar Poliza"  id="agrega" onclick="guardacomprobacion();" style="display: none;"/>
	<input type="button" class="nminputbutton" value="Cancelar Poliza"  id="cancela" onclick="cancelaComprobacion();" />


</body>
</div>
<?php if(isset( $_COOKIE['ejercicio'])){ ?>
		<input type="hidden" id="idperio" value="<?php echo $_COOKIE['periodo']; ?>">
		<input type="hidden" id="ejercicio" value="<?php echo $_COOKIE['ejercicio'];?>" />
	<?php }else{ ?>
		<input type="hidden" id="idperio" value="<?php echo $Ex['PeriodoActual']; ?>">
		<input type="hidden" id="ejercicio" value="<?php echo $Ex['NombreEjercicio'];?>" />
	<?php } ?>
<script>

<?php 
if(isset($_SESSION['usercompro'])){?>
	$('#usuarios').val(<?php echo $_SESSION['usercompro'];?>);
		
		
	<?php 
	if(isset($_SESSION["lista"])){ ?>
		$("#anticipolista").html("<?php echo $_SESSION["lista"]; ?>");
<?php }
	if(isset($nombredeudor[5])){ ?>
		$("#anticipolista").val(<?php echo $nombredeudor[5]; ?>);
		$("#anticipolista").select2({width : "150px" });
		
	<?php } ?>	
		
<?php 
	}else{?>
	$('#usuarios').val(0);
<?php }	 

if(isset($_SESSION['compruebagasto'])){?>
$(document).ready(function(){

	$(function () {
         var cargo=0;var abono=0;
        $("#datos tbody tr").each(function (index) //recorre todos los tr
        {
            $(this).children("td").each(function (index2) //en la fila actual recorremos los td
            {
                switch (index2) //indice
                {
                    case 1: cargo += parseFloat($(this).text().replace(',',''));
                            break;
                    case 2: abono += parseFloat($(this).text().replace(',',''));
                            break;
                }
            })
        })
         $("#cargototal").html(cargo.toFixed(2));
         $("#abonodeudor").html(<?php echo $nombredeudor[2];?>);
         $("#abonocomprobado").html(abono.toFixed(2))
          var diferen = Math.abs(abono.toFixed(2) - cargo.toFixed(2)); 
          
          if( parseFloat(cargo.toFixed(2)) > parseFloat(abono.toFixed(2)) ){ //alert(parseFloat(cargo.toFixed(2))+" >"+ parseFloat(abono.toFixed(2)))
          	$("#ajuste").show();$("#txt1").show();
          	$("#abono").html(diferen.toFixed(2));
          	$("#cargo").html('0.00');
          	$("#cuentadiferencia").select2({
        			 width : "150px"
    			});
          }else if( parseFloat(cargo.toFixed(2)) < parseFloat(abono.toFixed(2)) ){//alert(parseFloat(cargo.toFixed(2))+" <"+ parseFloat(abono.toFixed(2)))
          	$("#ajuste").show();$("#txt2").show();
          	$("#cargo").html(diferen.toFixed(2));
          	$("#cuentadiferencia").val(<?php echo $nombredeudor[1];?>);
          	$("#abono").html('0.00');
          	$("#cuentadiferencia").select2({
        			 width : "150px"
    			});
          }else if ( parseFloat(cargo.toFixed(2)) == parseFloat(abono.toFixed(2)) ){
          	$("#ajuste").hide();
          	$("#ajustetotal").show();
          	
          	$("#abono2").html(abono.toFixed(2));
         	$("#cargo2").html(cargo.toFixed(2));
          }
})
})
<?php } ?>
function cerrarAnticipo(monto){
	if($("#cerraranticipo").is(":checked")) {
		<?php if(isset($_SESSION['compruebagasto'])){?>
    			alert("Elimine los movimientos agregados para cerrar el Anticipo");
    			$("#cerraranticipo").prop("checked",false);
    			return false;
    		<?php } ?>
		$("#ajuste").show();
		$("#cargo").html(monto);
		$("#nodedu").hide();
		$("#cuentadiferencia").select2({
        			 width : "150px"
    			});
    		$("#factura,#submit,#facturasalmacen").hide();
    		
	}else{
		$("#ajuste").hide();
		$("#nodedu").show();
		$("#factura,#submit,#facturasalmacen").show();
	}
}
</script>
<div id="capturaNoDeducible" title="Gasto No Deducible" >
	<table class="">
		<tbody><tr>
      <td class=""><b>Cuenta</b></td>
      <td >
      	<img style="vertical-align:middle;" align="center" title="Abrir Ventana de Cuentas" onclick="iracuenta()" src="images/cuentas.png">
			<img style="vertical-align:middle;" align="center" title="Actualizar Cuentas" onclick="cuentaegresosdeducible()" src="images/reload.png">
			<br>
			<div id='cargando-mensaje' style='font-size:12px;color:blue;width:20px;display: none;'> Cargando...</div>

	 	<select class="nminputselect" id="deducible">
	 	<?php echo $cuentaegre; ?>	
		</select>
	 </td>
      <td align="center" rowspan="1"><b>Concepto</b></td>
      <td><input type="text" maxlength="50" id="conceptode" name="conceptode" class="nminputtext"></td>
		</tr>
      
      </tr>
	<tr>
      <td  id=""><b>Segmento</b></td>
      <td>
 		<select class="nminputselect" style="width: 150px;text-overflow: ellipsis;" id="segmentode" name="segmentode">
		<?php echo $segmento; ?>
		</select>
    	</td>
    	<td class=""><b>Cargo</b></td>
      <td><input type="text" style="width:159px" class="nminputtext" value="0.00" placeholder="0.00" id="cargodeducible"></td>
	</tr><tr>
      <td  id=""><b>Sucursal</b></td>
      <td>
     		<select class="nminputselect" style="width: 150px;text-overflow: ellipsis;" id="sucursalde" name="sucursalde">
				<?php echo $sucursal; ?>
			</select>
    		</td>
    		</tr>
	</tbody>
</table>

</div>
<div id="almacen" style="display: none">
<input type='text' class="nmcatalogbusquedainputtext" id='busqueda' name='busqueda' placeholder='Buscar'>
		<table class='listado'>
		
		</table>
</div>
</html>