<!DOCTYPE html>
	<head>
				        <meta charset="UTF-8" />
		<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="css/clipolizas.css"/>
		<script type="text/javascript" src="js/pagoprovee.js"></script>
		<script src="js/select2/select2.min.js"></script>
		<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
		<script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
		<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> 
		<link rel="stylesheet" href="css/style.css" type="text/css">
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
	function pagoss(che,MontoParcial){
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
		if(MontoParcial){ 
		 		$("#ipendiente"+che+",#icobrado"+che).show();
				$("#ipendienteinput"+che+",#icobradoinput"+che).val("0.00");
				$("#ieps"+che+",#ieps2"+che).hide();
				$("#ivacobrado"+che+",#ivapendiente"+che).show();
				$("#ivacobradoinput"+che+",#ivapendienteinput"+che).val("0.00");
				$("#iva2"+che+",#iva"+che).hide();
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
  $(document).ready(function(){
	 $('#periodomanual').val($('#Periodo').val());
});
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
	.btnMenu{
		border-radius: 0; 
		width: 100%;
		margin-bottom: 0.3em;
		margin-top: 0.3em;
	}
	.row
	{
		margin-top: 0.5em !important;
	}
	h4, h3{
		background-color: #eee;
		padding: 0.4em;
	}
	.nmwatitles, [id="title"] {
		padding: 8px 0 3px !important;
		background-color: unset !important;
	}
	.select2-container{
		width: 100% !important;
	}
	.select2-container .select2-choice{
		background-image: unset !important;
		height: 31px !important;
	}
	.busqueda{
		background-image: url("search.png");
    	background-position: right center;
    	background-repeat: no-repeat;
    	background-size: 20px 20px;
	}
	.modal-title{
		background-color: unset !important;
		padding: unset !important;
	}
	td{
		border: medium none !important;
	}
	#s2id_xml{
		width: 98.2% !important;
	}
	input[type="checkbox"]{
		margin-right: 1em !important;
	}
	.tdt{
		background-color: #eee !important;
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
				$provebancoid = explode('/', $prove['listabanco']);
				 $idbanco = $provebancoid[0];
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


	<div class="container">
		<div class="row">
			<div class="col-md-1">
			</div>
			<div class="col-md-10">
				<div class="row">
					<div class="col-md-12">
						<h3 class="nmwatitles text-center">
							Pago a proveedor
							<a  href='index.php?c=CaptPolizas&f=filtroAutomaticas&t=pago' onclick="" id='filtros'>
								<img src="../../netwarelog/repolog/img/filtros.png"  border="0" title='Haga click aqu&iacute; para cambiar los filtros...'>
							</a>
						</h3>
					</div>
				</div>
				<form method="post" ENCTYPE="multipart/form-data" action="index.php?c=CaptPolizas&f=tablaprov" onsubmit="return validacampos(this)">
					<section>
						<h4>Seleccionar xml</h4>
						<div class="row">
							<div class="col-md-12">
								<input type="radio" class="nminputradio" name="radio" id="radio" value="1" onclick="checa()" checked=""/>
								<select id="xml" name="xml[]" style=""  class="" multiple="">
									<?php
										while($facs = $facturasAuto->fetch_object()){
											if(strpos($facs->xml, 'Pago')!==false)
													$cobro = "Pago";
												if(strpos($facs->xml, 'Parcial')!==false)
													$cobro = "Parcial";
												if($facs->folio == '')
													$facs->folio = 'S/F';

												echo  "<option value='".htmlentities($facs->xml)."'>$cobro $facs->folio $facs->razon_social $facs->uuid</option>";
										}
									?>
								</select>
							</div>
						</div>
					</section>
					<h4>Datos del ejercicio</h4>
					<section>
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
						<div class="row">
							<div class="col-md-6">
								<label>Ejercicio Vigente:</label>
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
									} 
								?> 
							</div>
							<div class="col-md-6">
								<label>Periodo actual:</label>
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
									} 
								
							if($Ex['PeriodosAbiertos'])
							{?>
							<select id="periodomanual" title="Seleccione un periodo" onchange="cambioPeriodo(this.value,<?php echo $ejercicioactual; ?>)">
						        <option value="1">1</option>
						        <option value="2">2</option>
						        <option value="3">3</option>
						        <option value="4">4</option>
						        <option value="5">5</option>
						        <option value="6">6</option>
						        <option value="7">7</option>
						        <option value="8">8</option>
						        <option value="9">9</option>
						        <option value="10">10</option>
						        <option value="11">11</option>
						        <option value="12">12</option>
						        <option value="13">13</option>
						      </select>
						<?php }	?>
							</div>
						</div>
						<div class="row">
							<div class="col-md-6">
								<label>Acorde a configuracion:</label>
								<img src="images/reload.png" onclick="periodoactual()" title="Ejercicio y periodo de configuracion por defecto" style="vertical-align:middle;">
							</div>
							<div class="col-md-6">
								<input type="hidden" id="diferencia" value="<?php echo number_format($Cargos['Cantidad']-$Abonos['Cantidad'], 2); ?>" />
							</div>
						</div>
					</section>
					<section>
						<div class="row">
							<div class="col-md-6">
								<h4>Cuentas</h4>
								<div class="row">
									<div class="col-md-6">
										<label>Banco:<label id="bancosno"><?php echo @$bancosno; ?></label></label>
										<img style="vertical-align:middle;width: 15px;height: 15px" title="Agregar Cuenta Bancaria" id="mandacuentabanca" onclick="mandacuentabancaria()" src="images/mas.png">
										<img style="vertical-align:middle;width: 15px;height: 15px" title="Actualizar Listado" onclick="actualizaCatalogos(1)" src="images/reload.png">
										<select  id="banco" name="banco" class="nminputselect" style="width: 150px; margin-left: 2%; margin-bottom: 8px;" onchange="cuentabancarias();">
											<option value="0">Seleccione un Banco</option>
											<?php 
										  	if(isset($bancos)){
											while($b=$bancos->fetch_array()){ 
											 	if($bancocuenta == ($b["account_id"].'/'. $b['description']) ){ ?>
													<option value='<?php echo $b["account_id"].'/'. $b['description']; ?>' selected><?php echo $b['description']."(".$b["manual_code"].")"; ?> </option>
												<?php }else{ ?>
													<option value='<?php echo $b["account_id"].'/'. $b['description']; ?>'><?php echo $b['description']."(".$b["manual_code"].")"; ?> </option>
												<?php } 
											 }
										  } ?>
										</select>
									</div>
									<div class="col-md-6">
										<label>Proveedor:<?php echo @$proveedoresno; ?></label>
										<img style="vertical-align:middle;width: 15px;height: 15px" title="Agregar Bancos al Prv" onclick='mandabancos()' src="images/mas.png">
										<img style="vertical-align:middle;width: 15px;height: 15px" title="Actualizar Listado" onclick="actualizaCatalogos(2)" src="images/reload.png">
										<select id="proveedor" name="proveedor" class="nminputselect" style="width: 150px; margin-bottom: 8px;margin-left: 2%;" onchange="beneficiari();" <?php echo $disable; ?>>
											<?php 
												if(isset($proveedores)){ ?>
													<option value="0" >Seleccione un proveedor</option>
													
											<?php while($b=$proveedores->fetch_array()){  $razon_social=  str_replace('/', ' ', $b['razon_social']);$razon_social = str_replace('-', ' ', $razon_social); 
														if(($b['cuenta'].'/'. $b['idPrv'].'/'. $razon_social)==$provee){?>
															<option value="<?php echo $b['cuenta'].'/'. $b['idPrv'].'/'.$razon_social; ?>" selected><?php echo ($b['razon_social']); ?> </option>
											<?php		}else{?>
															<option value="<?php echo $b['cuenta'].'/'. $b['idPrv'].'/'.$razon_social; ?>"><?php echo ($b['razon_social']); ?> </option>
											<?php 		}
												  }while($b=$proveedores2->fetch_array()){  $description = str_replace('-', ' ', $b['description']);  $description = str_replace('/', ' ', $description); 
												  		if(($b['account_id'].'-'. $description)==$provee){?>
															<option value="<?php echo $b['account_id'].'-'.$description; ?>" selected><?php echo ($b['description']."(".$b['manual_code'].")"); ?> </option>
											<?php 		}else{ ?>
															<option value="<?php echo $b['account_id'].'-'.$description; ?>" ><?php echo ($b['description']."(".$b['manual_code'].")"); ?> </option>
											<?php	    }
												  } 
												}?>
										</select>
									</div>
								</div>
							</div>
							<div class="col-md-6">
								<h4>Datos de registro</h4>
								<div class="row">
									<div class="col-md-6">
										<label>Concepto:</label>
										<input type="text"  class="form-control" placeholder="Concepto..." id="concepto" name="concepto" />
									</div>
									<div class="col-md-6">
										<label>Segmento de negocio:</label>
										<select name='segmento' id='segmento' style='text-overflow: ellipsis;'  class="form-control">
											<?php
											while($LS = $ListaSegmentos->fetch_assoc())
											{
												echo "<option value='".$LS['idSuc']."//".$LS['nombre']."'>".$LS['nombre']."</option>";
											}
											?>
										</select>
									</div>
								</div>
								<div class="row">
									<div class="col-md-6">
										<label>Sucursal:</label></br>
										<select name='sucursal' id='sucursal' style='text-overflow: ellipsis;'  class="form-control">
											<?php
											while($LS = $ListaSucursales->fetch_assoc())
											{
												echo "<option value='".$LS['idSuc']."//".$LS['nombre']."'>".$LS['nombre']."</option>";
											}
											?>
										</select>
									</div>
									<div class="col-md-6">
										<label>Fecha de poliza:</label>
										<?php if(isset($_SESSION['fechaprove'])){ ?>
											<input  type="date" class="form-control" id="fecha" name="fecha" value="<?php echo $_SESSION['fechaprove']; ?>" onmousemove="javascript:fechadefault()"/>
										<?php }else{ ?>
											<input  type="date" class="form-control" id="fecha" name="fecha" onmousemove="javascript:fechadefault()"/>
										<?php } ?>
									</div>
								</div>
							</div>
						</div>
					</section>
					<h4>Datos del pago</h4>
					<section>
						<div class="row">
							<div class="col-md-3">
								<label>Forma de pago:</label>
								<select id="formapago" name="formapago" class="form-control">
								 	
					<?php mb_internal_encoding("UTF-8");
					while($f = $forma_pago->fetch_array()){$sd = "";
						if(($f['idFormapago'].'/'.$f['nombre'])==$formap){  $sd = "selected";} 
						//Si es la claveSat 98 se cambiara por NA
				 		if($f['claveSat'] == '98') { ?>
							<option value="<?php echo $f['idFormapago']."/".$f['nombre'];?>" <?php echo $sd;?>>
								<?php echo"(".$f['claveSat'].") NA";?>
							</option>
						<!-- Si cumple con las siguientes caracteristicas no se va a mostrar -->
						<?php } else if(
							($f['nombre'] == 'Cortesia') || 
							($f['nombre'] == 'Credito') || 
							($f['nombre'] == 'Crédito') || 
							($f['claveSat'] == '28') || 
							($f['claveSat'] == '29') || 
							(($f['claveSat'] == '99') && ($f['nombre'] !== 'Otros')) || 
							($f['claveSat'] == 'NA')
						){ ?>
						<!-- Si es la claveSat 07 se cambiara a tarjeta digital -->
						<?php } else if($f['claveSat'] == '07') { ?>
							<option value="<?php echo $f['idFormapago']."/".$f['nombre'];?>" <?php echo $sd;?>>
								<?php echo"(".$f['claveSat'].") TARJETAS DIGITALES";?>
							</option>
						<!-- si no, imprimir de forma normal -->
				 		<?php } else { ?>
					 		<option value="<?php echo $f['idFormapago']."/".$f['nombre'];?>" <?php echo $sd;?>>
								<?php echo "(".$f['claveSat'].") ".mb_strtoupper($f['nombre']); ?>
							</option>
				 		<?php	}
							
						} ?>
						
								</select>
							</div>
							<div class="col-md-3">
								<label>Numero:</label>
								<input type="text"  class="form-control" size="20" id="numero"  name="numero" value="<?php echo $numero;?>"/>
							</div>
							<div class="col-md-3">
								<label>Banco Origen:</label>
								<select id="listabancoorigen" name="listabancoorigen" class="form-control">
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
							</div>
							<div class="col-md-3">
								<label>No. Cuenta Bancaria Origen/tarjeta</label>
								<input type="text" id="numorigen" name="numorigen" class="form-control" value="<?php echo $numeroorigen; ?>" readonly/>
							</div>
						</div>
						<div class="row">
							<div class="col-md-3">
								<label>Beneficiario:</label>
								<select id="beneficiario" name="beneficiario"  class="form-control"  onchange="cuentarbolbenefi();" >
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
							</div>
							<div class="col-md-3">
								<label>RFC:</label>
								<input type="text" id="rfc" name="rfc" class="form-control" value="<?php echo $rfc; ?>" readonly/>
							</div>
							<div class="col-md-3">
								<label>Banco Destino:</label>
								<select id="listabanco" name="listabanco" onchange="numerocuent()" class="form-control">
									<option value="0">Elija Banco</option>
									<?php 
										while($b=$listabancos->fetch_array()){
											if($b['idbanco']==$idbanco){ ?> 
											<option value="<?php echo  $b['idbanco']."/0"; ?>" selected><?php echo $b['nombre']."(".$b['Clave'].")"; ?> </option>
									<?php  		} else{ ?>
												<option value="<?php echo  $b['idbanco']."/0"; ?>"><?php echo $b['nombre']."(".$b['Clave'].")"; ?> </option>
									<?php		}
										}
									?>
								</select>
							</div>
							<div class="col-md-3">
								<label>No. Cuenta Bancaria Dest./tarjeta</label>
								<img style="vertical-align:middle;width: 15px;height: 15px" title="Cargar numero" onclick='numerocuent()' src="images/reload.png">
								<input type="text" id="numtarje" name="numtarje" class="form-control" value="<?php echo $numtarje; ?>" readonly/>
							</div>
						</div>
					</section>
					<section>
						<div class="row">
							<div class="col-md-2">
								<button type="submit" class="btn btn-primary btnMenu" id="agregar">Leer XML's</button>
							</div>
							<div class="col-md-7">
								<input type="checkbox" value="0" id="unsolobanco" onclick="unSoloBanco()"/><b style="color:red;font-size: 17px">Un solo Abono a Bancos.</b>
							</div>
						</div>
					</section>
				</form>
				<section id="movimientos">
					<div class="row">
						<div class="col-md-12">
							<div class="table-responsive">
								<table id="datos" align="center" cellpadding="2" border="0" style="border: white 1px solid; " class="table">
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
										$cont = $totalbancos = 0;
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
											$totalbancos+=$prove['importe'];
											$segment = explode('//',$prove['segmento']);
												$sucu = explode('//',$prove['sucursal']);
												  ?>
								
								 <tr class="trpagototal"><td colspan="7"><hr></hr>
								 <input type="checkbox"  checked="" id="<?php echo $cont; ?>" onclick="pagoss(<?php echo $cont; ?>,<?php echo $prove['MontoParcial']; ?>)"/>Pago Total</td>
								 </tr>

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
										 <td align="center"><?php echo $prove['xml']; ?></td>
										 <td><img src="images/eliminado.png" title="Eliminar Movimiento" onclick="borra(<?php echo $cont; ?>);"/></td>

									 </tr>
									 <tr class="trbancos">
										 <td  class="nmcatalogbusquedatit" align="center">Bancos</td>
										 <td align="center">0.00</td>
										 <td align="center" id="impor2<?php echo $cont; ?>"><?php echo number_format($prove['importe'],2,'.',','); ?></td>
										 <td align="center" style="display: none" id="imporintro2<?php echo $cont; ?>"><input  type="text" placeholder="0.00" value="0.00" id="imporinput2<?php echo $cont; ?>" onkeyup="calculaIVAIEPS('imporinput',<?php echo $cont; ?>)" /></td>
										 <!-- <td align="center"><?php $f=explode("/",$prove['formapago']); echo $f[1];?></td>	-->				
										 	 <td colspan="4"></td> 					
									 </tr>
									 <?php 
									 if($statusIVA==1){//si calcula
									 	
									 if($prove['IVA']>0){ //pato?>
									 	<script>
									 	$(document).ready(function(){
									 		$("#ivapendientepago<?php echo $cont ?>,#ivapago<?php echo $cont ?>").select2({
						    					 width : "150px"
						   					 });
						   				<?php	
						   				if($prove['MontoParcial']){?>
									 		$("#ivacobrado<?php echo $cont; ?>,#ivapendiente<?php echo $cont; ?>").show();
											$("#ivacobradoinput<?php echo $cont; ?>,#ivapendienteinput<?php echo $cont; ?>").val(<?php echo $prove['IVA']; ?>);
											$("#iva2<?php echo $cont; ?>,#iva<?php echo $cont; ?>").hide();
									 	<?php } ?> 
										});
									 	</script>
									 	<tr>
									 		<td colspan="" class="classiva"></td>
									 		<?php if($statusIVAIEPS==1){?>
									 		<td  class="nmcatalogbusquedatit" align="center"><!-- IVA pendiente de Pago -->
									 			<input type="button" id="ivapendientepago" name="ivapendientepago" onclick="mandaasignarcuenta();" title="Ir a asignacion de cuentas" class="nmcatalogbusquedatit" value="<?php  echo $ivapendientepago[1];?>">
											</td>
											<?php }else{ ?>
											<td  class="" align="center">IVA pendiente de Pago
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
								 			<td  class="" align="center">IVA Pagado
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
									 }
									 if($statusIEPS==1){ 
									   if($prove['IEPS']>0){ ?>
									  	<script>
									  	$(document).ready(function(){
									 		$("#iepspendiente<?php echo $cont ?>,#iepspago<?php echo $cont ?>").select2({ width : "150px" });
						  					<?php 
									 	if($prove['MontoParcial']){?> 
									 		$("#ipendiente<?php echo $cont; ?>,#icobrado<?php echo $cont; ?>").show();
											$("#ipendienteinput<?php echo $cont; ?>,#icobradoinput<?php echo $cont; ?>").val(<?php echo $prove['IEPS']; ?>);
											$("#ieps<?php echo $cont; ?>,#ieps2<?php echo $cont; ?>").hide();

									 	<?php } ?>
						  				});
									 	</script>
									 	<tr>
									 		<td colspan="" class="classieps"></td>
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
								 	} ?>
								 	<tr class="trUnsoloBanco" style="display: none">
								 		<td></td>
								 		<td align="center" ><b style="font-size: 17px;">Bancos</b></td>
								 		<td align="center" ><b style="font-size: 17px;">0.00</b></td>
								 		<td align="center" ><b style="font-size: 17px;"><?php echo number_format($totalbancos,2,'.',',');?></b></td>
								 	</tr>
								 	</tbody>
								</table>
							</div>
						</div>
					</div>
				</section>
				<section>
					<div class="row">
						<div class="col-md-4">
						</div>
						<div class="col-md-5">
							<img src="images/loading.gif" style="display: none" id="load">
						</div>
						<div class="col-md-3">
							<button class="btn btn-primary btnMenu" id="agregaprevio" onclick="antesdeguardar(<?php echo $cont; ?>);">Agregar poliza</button>
							<button class="btn btn-primary btnMenu" id="agrega" onclick="guarda();" style="display: none">Agregar poliza</button>
						</div>
					</div>
				</section>
			</div>
			<div class="col-md-1">
			</div>
		</div>
	</div>

	<?php if(isset( $_COOKIE['ejercicio'])){ ?>
		<input type="hidden" value="<?php echo $_COOKIE['ejercicio']; ?>" id="ejercicio" name="ejercicio">
		<input type="hidden" value="<?php echo $_COOKIE['periodo']; ?>" id="idperiodo" name="idperiodo">	
	<?php }else{ ?>
		<input type="hidden" value="<?php echo $ejercicio; ?>" id="ejercicio" name="ejercicio">
		<input type="hidden" value="<?php echo $idperiodo; ?>" id="idperiodo" name="idperiodo">	
	<?php } ?>

	</body>
</html>
<script>
	//beneficiari();
</script>
