<!DOCTYPE html>
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>
	<script src="js/select2/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
	<script src="js/cheque.js"></script>
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> 
</head>
<body>
	
	<?php $cuentaarbol="";
		while($b=$sqlcuentasnoenprv->fetch_array()){ 
			$cuentaarbol .= "<option value='".$b['account_id']."'>".$b['description']."(".$b['manual_code'].")</option>";
		} ?>

	<div class="nmwatitles">&nbsp;Cheques</b></div><br></br>
	
	<div class="container">
			<fieldset><br>
				<table align="center" width="70%">
					<th><img src="images/guardar.png" style="width: 25px;height: 25px" title="Guardar" onclick="guardar()"></th>
					<th><img src="images/cancelar.png" style="width: 25px;height: 25px" title="Borrar"></th>
					<th><img src="images/cancelar2.png" style="width: 30px;height: 30px" title="Cancelar" onclick="cancelar()"></th>
					<th><img src="images/copiar.png" style="width: 30px;height: 30px" title="Copiar"></th>
					<th><img src="images/tras.png" style="width: 30px;height: 30px" title="Traspaso"></th>
					<th><img src="images/devolu.png" style="width: 30px;height: 30px" title="Devolocion" onclick="devolver()"></th>
					<th><img class="nmwaicons" border="0" title="Imprimir" src="../../netwarelog/design/default/impresora.png" onclick="updateimprime()"></th>
					<th><img src="images/poli2.png" style="width: 25px;height: 30px" title="Ver poliza" onclick="verpoliza()">
					<img src="images/loading.gif" id="img" style="display: none" />
					</th>

				</table>
				<hr>
			<br><br>
				
				<form>
					<?php  if(!@$id){ $id=0; }?>
					<input type="hidden" id="idDocumento" value="<?php echo $id; ?>"> 
				<table >
					<tr>
						<td>Cuenta:</td>
						<td><select id="cuenta" onclick="buscanumerocheque();" >
							<option value="0">--Seleccione--</option>
							<?php
							while($b=$cuentasbancarias->fetch_array()){?>
								<option value="<?php echo  $b['idbancaria']."//".$b['account_id']; ?>"><?php echo $b['nombre']." (".$b['description']."[".$b['manual_code']."])"; ?> </option>
							<?php } ?>
							</select>
						</td>
						
					</tr>
					<tr>
						
						
					</tr>
			</table>
		<br>
		<div style="position: absolute;left: 250px; top: 250px; z-index: 3;display: none" id="cancelado">
			<img src="images/cancelado.png" width="600px" height="150px"/>

		</div>
		<div style="position: absolute;left: 250px; top: 250px; z-index: 3;display: none" id="devuelto">
			<img src="images/devuelto.png" width="600px" height="150px"/>

		</div>
		<div style="width:60%; display:inline-block;">

				<table width="60%">
					<tr>
						<td>Numero:</td>
						<td>
							<input id="numero" style="width: 50px" type="text">
						</td>
						
					</tr>
					<tr>
						<td>Paguese a:</td>
						<td>
							<select id="paguese" style="width: 30px" onchange="verificacuenta();">
								<option value="0">--Seleccione--</option>
								<?php while($b=$sqlprov->fetch_array()){ ?>
										<option value="<?php echo $b['cuenta'].'/'. $b['idPrv']; ?>" ><?php echo ($b['razon_social']); ?> </option>
								<?php } ?>
							</select>
						</td>
						
					</tr>
					<tr>
						<td colspan="2">
							<div id="vercuentasprv" style="display: none;">
								<label style="color: red">Seleccione una cuenta para el beneficiario</label><br>
								<select id="paguese2">
								<?php echo $cuentaarbol; ?>
								</select>
							</div>
							
						</td>
						<td colspan="3" ><span class="label label-success" id="letra" style="align:center;"></span></td>
					</tr>
				</table>
				</div>
	<div style="width:35%;display:inline-block;">

			<table style="">
					<tr><td colspan="" width="20%"><b>Saldo contable al:</b> &nbsp;</td>
						<td width="20%" ><input type="date" id="fechasaldo" onchange="saldofecha()" value="<?php echo date('Y-m-d');?>" /></td>
						<td  width="20%" id="saldo" style="font: center;color: #FF0000"></td>
					</tr>
					<tr>
						<td colspan=""></td>
						<td align="right">Fecha:</td>
						<td><input type="date" id="fecha"/></td>
						
					</tr>
					<tr>
						<td colspan=""></td>
						<td align="right">Importe: </td>
						<td>
							<div class="input-group">
					         <span class="input-group-addon">$</span>
					         <input type="text" class="form-control" id="importe" onkeyup="letra()" style="width: 123px">
					         </div>
						</td>
					</tr>
				</table>
			</div>
			  
				<hr><span class="label label-success" id="letra" style="align:center;"></span></hr>
				<table>
					<tr>
						<td>Referencia:</td>
						<td><input type="text" id="referencia" /></td>
						<td>Concepto General:</td>
						<td id="area" ><textarea id="textarea"></textarea></td>
						<td><img onclick="conceptos();" id="buscarconcepto" title="Buscar Concepto" src="images/busca3.png" style="width: 30px;height: 30px"></img></td>
						<td><img  onclick="ocultalista();" id="ocultaconcepto" title="Introducir Concepto" src="images/edita.png" style="width: 30px;height: 30px;display: none"></img></td>
						<td id="selectconceptos" style="display: none">
							<select id="listaconcepto">
								<option value="0"></option>
						<?php  while( $row = $listaconceptos->fetch_array()) {?>
								<option value="<?php echo $row['descripcion']; ?>"><?php echo $row['descripcion']."(".$row['codigo'].")"; ?></option>
						<?php } ?>
							</select>
						</td>
					</tr>
					<tr>
						<td>Clasificador:</td>
						<td>
							<select id="clasificador">
								<option value="0"></option>
						<?php while($c = $clasificador->fetch_array()){ ?>
								<option value="<?php echo $c['id']; ?>" > <?php echo $c['nombreclasificador']."(".$c['codigo'].")"; ?></option>
						<?php } ?>
							</select>
						</td>
					</tr>
				</table>
	<hr></hr>
	<div id="tabs" style="width: 95%">
  <ul>
    <li><a href="#tabs-1">Documentos</a></li>
    <li><a href="#tabs-2">Otros Datos</a></li>
  </ul>
  <div id="tabs-1">
  	orden de compra
  </div>
  <div id="tabs-2">
  	<div style="border-right: 2px solid #98ac31; display:inline-block">
  	<table height="160px" >
            	
            	<tr>
            		<td>Posibilidad de pago</td>
            		<td>
            			<select>
            				<option value="1">Alta</option>
            				<option value="2">Media</option>
            				<option value="0">Baja</option>
            			</select>
            		</td>
            	</tr>
            	<tr>
            		<td><input type="radio" name="tipo" value="0"/><b>Proyectado</b></td>
            	</tr>
            	<tr>
            		<td><input type="radio" value="1" name="tipo" checked=""/><b>Autorizado</b></td>
            	</tr>
            	<tr>
            		<td><input type="checkbox" value="1" name="forma" id="impreso"/>Impreso</td>
            	</tr>
            	<tr>
            		<td><input type="checkbox" value="2" name="forma" id="conciliado"/>Conciliado</td>
            	</tr>
            	<tr>
            		<td><input type="checkbox" value="3" name="forma" id="asociado"/>Asociado</td>
				</tr>
            </table>
	
  </div>
  <div style=" border-right: 2px solid #98ac31;display:inline-block">
  	<table height="160px" width="100px">
  		<tr>
  			<td>Moneda:</td>
  			<td><select id="moneda">
  					<?php while($moni = $moneda->fetch_array()){?>
  							<option value="<?php echo $moni['coin_id'] ?>"><?php echo $moni['description']?></option>
  					<?php } ?>
  				</select>
  			</td>
  		</tr>
  		<tr>
  			<td>Tipo de Cambio</td>
  		</tr>
  		<tr>
  			<td>Importe</td>
  			<td><input type="text" /></td>
  		</tr>
  	</table>
  </div>
  <div style=" border-right: 2px solid #98ac31;display:inline-block">
  	<table style="height: 160px;">
  		<tr>
  			<td><input type="checkbox" value="1"  />Incluir Leyenda para abono en cuenta</td>
  		</tr>
  		<tr>
  			<td>Prioridad: <input type="text" id="prioridad" /></td>
  		</tr>
  		
  		<tr>
  			<td>Estado del Cheque: Activo</td>
  		</tr>
  	</table
  </div>
  
  
  
  </div>
 
</div>
</form>
	 </fieldset>
			
		
	</div>
</body>
</html>