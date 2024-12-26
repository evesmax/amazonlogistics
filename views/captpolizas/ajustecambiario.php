<!DOCTYPE html>
<head>
	<LINK href="../../netwarelog/catalog/css/view.css" title="estilo" rel="stylesheet" type="text/css" />
	<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
	<script src="js/jquery-1.10.2.min.js"></script>
	<script src="js/ajustecambiario.js"></script>
</head>
<body>
	<div class="nmwatitles">Poliza de Ajuste por diferencia cambiaria.</div><br></br>
	
				Que desea que el proceso realize?<br></br>
		<form action="" method="post">
				<select id="proceso" name="proceso">
					<option value="1">Generar poliza por diferencia cambiaria.</option>
					<option value="2">Mostrar detalle del calculo.</option>
				</select>
				<br></br>
			<div style="background:#424242;width: 50%; text-align: center;color: #FBFBEF">Periodo de Calculo</div>
						<br></br>
			  Ejercicio:<select id='ejercicio' name="ejercicio" class="nminputselect">
                	<?php  while($d = $datos->fetch_array()){ ?>
	 					<option value="<?php echo $d['Id']."/".$d['NombreEjercicio']; ?>" selected><?php echo $d['NombreEjercicio']; ?></option>
		 				<?php } ?>
                </select>
			<br></br>
			 Periodo: <select style="margin-right: 12%;" class="nminputselect" id="periodo" name="periodo">
                	<option selected value='1'>Enero</option>
                	<option value="2">Febrero</option>
                	<option value="3">Marzo</option>
                	<option value="4">Abril</option>
                	<option value="5">Mayo</option>
                	<option value="6">Junio</option>
                	<option value="7">Julio</option>
                	<option value="8">Agosto</option>
                	<option value="9">Septiembre</option>
                	<option value="10">Octubre</option>
                	<option value="11">Noviembre</option>
                	<option value="12">Diciembre</option>
                	
                </select>
		
		
		<br></br>
		
	<div style="background:#424242;width: 50%; text-align: center;color: #FBFBEF">Definir cuentas para</div>
	<br>
	<table>
		<tr>
			<td>UTILIDAD: </td>
			<td>
				<select id="utilidad" name="utilidad">
					<?php  while($d = $utilidad->fetch_array()){ ?>
	 					<option value="<?php echo $d['account_id']."//".$d['description']; ?>" ><?php echo $d['description']."(".$d['manual_code'].")"; ?></option>
		 				<?php } ?>
				</select>
			</td>
		</tr>
		<tr>
			<td>PERDIDA: </td>
			<td><select id="perdida" name="perdida">
						<?php  while($d = $perdida->fetch_array()){ ?>
	 					<option value="<?php echo $d['account_id']."//".$d['description']; ?>" ><?php echo $d['description']."(".$d['manual_code'].")"; ?></option>
		 				<?php } ?>
			</select></td>
			
			</tr>
		<tr>
			<td>Cuentas en: </td>
			<td>
			<select id="moneda" name="moneda">
				<?php  while($m = $tipomoneda->fetch_array()){ 
							if($m['coin_id']!=1){
					?>
						<option value="<?php echo $m['coin_id']; ?>" ><?php echo $m['description']; ?></option>
		 		<?php 		}
					  } ?>
			</select>
			</td>
		</tr>
		<tr>
			<td>Tipo de cambio:</td>
			<td><input type="text" size="10" id="tc" name="tc" /></td>
		</tr>
		<tr>
			<td>Segmento de Negocio:</td>
			<td><select name='segmento' id='segmento' style='width: 150px;text-overflow: ellipsis;'  class="nminputselect">
			
			<?php
				while($LS = $ListaSegmentos->fetch_assoc())
				{
					echo "<option value='".$LS['idSuc']."'>".$LS['nombre']."</option>";
				}
				?>
			</select></td>
			<td>Sucursal:</td>
				<td><select name='sucursal' id='sucursal' style='width: 150px;text-overflow: ellipsis;'  class="nminputselect">
			
			<?php
				while($LS = $ListaSucursales->fetch_assoc())
				{
					echo "<option value='".$LS['idSuc']."'>".$LS['nombre']."</option>";
				}
				?>
			</select></td>
		</tr>
		
		<tr>
			<td colspan="3">
			
			</td>
		</tr>
	</table>
	<fieldset style=" font-size:12px;font-weight:bold; color:#6E6E6E;width: 40%;vertical-align:top; display:inline-block;">
				<legend>En cuentas de Utilidad/Perdida hacer:</legend>
				
				<input type="radio" name="radio" value="2" checked=""/>Ajuste por cadena cuenta
	</fieldset><br /><br>
	
		<input type="button" value="Procesar" id="proceso" onclick="procesa();"/>

</form>	<img src="images/loading.gif" style="display: none" id="load" />
	<div id="info" style="text-align: justify"><br>
		<b>----Informacion----</b><br>
		<b>Definicion:</b><br>
		Procesa las cuentas con saldos en el periodo en moneda<br>
		diferente a pesos, para calcular la diferencia cambiaria y <br>
		crear los movimientos de ajuste. Crea ademas movimientos<br>
		a las cuentas de Utilidad o Perdida, segun corresponda.
		<br><br>
		
		<b>Resultado:</b><br>
		La poliza con los movimientos de ajuste <br>
		y la bitacora del proceso.
		
		
		
	</div>
</body>
</html>