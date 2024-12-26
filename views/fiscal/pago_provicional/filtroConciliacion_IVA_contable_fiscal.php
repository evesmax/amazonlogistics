<?php
	//include("../../../netwarelog/catalog/conexionbd.php");
?>

	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<?php include('../../netwarelog/design/css.php');?>
<LINK href="../../netwarelog/design/<?php echo $strGNetwarlogCSS;?>/netwarlog.css" title="estilo" rel="stylesheet" type="text/css" /> <!--NETWARLOG CSS-->
	<link rel="stylesheet" href="css/style.css" type="text/css">

	<style type="text/css">
		.cuerpo_r{width: 420px; height: 320px;  padding: 7px; border: 0px solid; font-family: arial;}
		#mov{display:none;}
	</style>
	<link rel="stylesheet" type="text/css" href="js/select2/select2.css" />
	<script type="text/javascript" src="js/jquery.js"></script>
	<script type="text/javascript" src="js/conciliacion_IVA_contable_fiscal.js"></script>
	<script src="js/select2/select2.min.js"></script>
	<script>
	$(document).ready(function() {
		$('#nmloader_div',window.parent.document).hide();
	});
	function valida(f)
	{  
 
 		 if(f.cuenta_Trans.value == '0' && f.cuenta_Acred.value == '0')
 		 { 
    			alert('Falta seleccionar la cuenta de IVA'); 
    			$('#nmloader_div',window.parent.document).hide();
    			return false
		}  
	}
	</script>
	
	<div class="repTitulo">Conciliacion de IVA contable y Fiscal</div>
	<div class="per">
		<form name='reporte' id='info' method='post' action='index.php?c=conciliacion_IVA_contable_fiscal&f=reporte' onsubmit='return valida(this)'>
		<ul>
			<li><label>Considerar periodo de causacion</label><input type="checkbox" class="nminputtext" value="1" id="considera_per" checked name='considera_per'></li>
			<div id='eje'>
			<li><label>Ejercicio:</label><select id="sel_ejercicio" name="sel_ejercicio" class="nminputtext">
									<?php
									$res=$ejercicio->fetch_object();
									echo "<option id='ej_".$res->id."' value='".$res->id."' selected>".$res->NombreEjercicio."</option>";
									while($res=$ejercicio->fetch_object()){
										echo "<option id='ej_".$res->id."' value='".$res->id."'>".$res->NombreEjercicio."</option>";
									}
									?>
								</select></li>
			<li><label>Periodo Final:</label><select id="per_ini" name="per_ini" class="nminputtext">
								<option id="per_ini_1" value="1" selected>Enero</option>
								<option id="per_ini_2" value="2">Febrero</option>
								<option id="per_ini_3" value="3">Marzo</option>
								<option id="per_ini_4" value="4">Abril</option>
								<option id="per_ini_5" value="5">Mayo</option>
								<option id="per_ini_6" value="6">Junio</option>
								<option id="per_ini_7" value="7">Julio</option>
								<option id="per_ini_8" value="8">Agosto</option>
								<option id="per_ini_9" value="9">Septiembre</option>
								<option id="per_ini_10" value="10">Octubre</option>
								<option id="per_ini_11" value="11">Noviembre</option>
								<option id="per_ini_12" value="12">Diciembre</option>
							</select></li>
			<li><label>Periodo Inicial:</label><select id="per_fin" name="per_fin" class="nminputtext">
								<option id="per_fin_1" value="1" selected>Enero</option>
								<option id="per_fin_2" value="2">Febrero</option>
								<option id="per_fin_3" value="3">Marzo</option>
								<option id="per_fin_4" value="4">Abril</option>
								<option id="per_fin_5" value="5">Mayo</option>
								<option id="per_fin_6" value="6">Junio</option>
								<option id="per_fin_7" value="7">Julio</option>
								<option id="per_fin_8" value="8">Agosto</option>
								<option id="per_fin_9" value="9">Septiembre</option>
								<option id="per_fin_10" value="10">Octubre</option>
								<option id="per_fin_11" value="11">Noviembre</option>
								<option id="per_fin_12" value="12">Diciembre</option>
							</select></li>
			</div>
			<div id="mov">
			<li><label>Movimientos Del:</label><input type="date" class="nminputtext" id="fecha_ini" name="fecha_ini" placeholder="aaaa-mm-dd" disabled></li>
			<li><label>Al:</label><input type="date" class="nminputtext" id="fecha_fin" name="fecha_fin" placeholder="aaaa-mm-dd" disabled></li>
			</div>
			<li><fieldset style="width:160px;margin-left:42%;"><legend><strong>Tipo de Operación</strong></legend>
							<input type="radio" class="nminputradio" id="rad_cau" value="0" name="radio_tipo"> IVA Trasladado <br>
							<input type="radio" class="nminputradio" id="rad_acr" value="1" name="radio_tipo" checked> IVA Acreditable <br>
							<input type="radio" class="nminputradio" id="rad_amb" value="2" name="radio_tipo"> Ambos
						</fieldset></li>
			<li><label>Cuenta de IVA Trasladado:</label><select id="cuenta_Trans" class="nminputselect" name='cuenta_Trans'>
								<option value="0">---</option>
							<?php
								
								while($c = $cuentas1->fetch_object())
								{
									echo '<option value="'.$c->account_id.'">'.$c->manual_code.'_'.$c->description.'</option>';
								}
							?>
							</select></li>
			<li><label>Cuenta de IVA Acreditable:</label><select id="cuenta_Acred" class="nminputselect" name='cuenta_Acred'>
								<option value="0">---</option>
							<?php
								while($c = $cuentas2->fetch_object())
								{
										echo '<option value="'.$c->account_id.'">'.$c->manual_code.'_'.$c->description.'</option>';
								}
							?>
							</select></li>
			<li><label></label><input type="submit" class="nminputbutton" value="Ejecutar Reporte" onclick="$('#nmloader_div',window.parent.document).show();"></li>
		</ul>
	</form>
	
	</div>

