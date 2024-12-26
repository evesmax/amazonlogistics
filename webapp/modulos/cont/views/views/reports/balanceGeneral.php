<?php
	//include("../../../netwarelog/catalog/conexionbd.php");
?>
<html>
<head>
	<title></title>
	<link rel="stylesheet" type="text/css" href="css/rep_fiscal.css">
	<LINK href="../../netwarelog/catalog/css/estilo.css" title="estilo" rel="stylesheet" type="text/css" />
	<link rel="stylesheet" type="text/css" href="css/style.css">
	<style type="text/css">
		.cuerpo{width: 520px; height: auto  padding: 7px; font-family: arial;}
		.tamanoSel{width: 200px;	text-overflow: ellipsis;}
		#conv{display: none;}
	</style>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js" type="text/javascript"></script>
	<script src="../cont/js/select2/select2.min.js"></script>
	<link rel="stylesheet" type="text/css" href="../cont/js/select2/select2.css" />
	<script type="text/javascript">
	$(function()
		 {
		 	 $("#moneda").select2({
						 width : "250px",
						 placeholder:"Selecciona una moneda"
						});
		 });

	$(document).ready(function()
	{
		$('#nmloader_div',window.parent.document).hide();
	});

	<?php if($_GET['tipo']<3){
		// Formulario Tipo de Cambio Moneda
		echo 
		"function valida(f)
		{
			if($('#tipoC:checked').val() &&  f.valMon.value =='')
			{
				alert('Debe colocar una cantidad en el Tipo de Cambio');
				return false;
			}
			else 
			{
			if(!$('#tipoC:checked').val() &&  f.valMon.value >0)
			{
				alert('Elimine la cantidad en Tipo de Cambio o Active Convertir Moneda');
				return false;
			}
			}";
			
			if($_GET['tipo']==0){

			echo "
			if($('#comSeg:checked').val() &&  f.periodo.value == 0)
			{
				alert('Para hacer la comparación debe elegir un Periodo');
				return false;
			}
			else 
			{
			if($('#comSeg:checked').val() &&  f.periodo.value != 0 && f.segmento.value != 0)
			{
				alert('Para hacer la comparación debe elegir Todos los segmentos');
				return false;
			}
			}";

			}
echo "}
	

	function conMon()
		{
			if($('#tipoC:checked').val())
			{
				$('#conv').show('slow')
			}
			else
			{
				$('#conv').hide('slow')
			}
		}";

		}
	?>
	</script>
</head>
<body>
	<?php 
	switch($_GET['tipo'])
	{
case 0: $titulo = 'Estado de Resultados.';$detalle="<fieldset style='width: 60%;margin-left: 20%;'><legend>Organizar por</legend><input type='radio' value='0' name='detalle' checked> De Mayor<br /><input type='radio' value='1' name='detalle'> Detalle</fieldset>";break;
case 1: $titulo = 'Balance General.';break;
case 2: $titulo = 'Estado de Origen y Aplicacion de Recursos.';break;
case 3: $titulo = 'Estado de Situacion Financiera.';break;
case 4: $titulo = 'Estado de Resultado Integral.';break;
	}
	?>

	<div class="repTitulo"><?php echo $titulo;?></div>
	<div class="per">
		<form name='reporte' method='post' id='info' action='index.php?c=reports&f=balanceGeneralReporte&tipo=<?php echo $_GET['tipo']; ?>' <?php if($_GET['tipo']<3){ echo "onsubmit='return valida(this)'";}?> >
		<ul><li><label>Ejercicio:</label><select name='ejercicio' id='ejercicio' class="nminputselect">
								<?php 
								while($p = $ejercicios->fetch_object())
								{
									echo "<option value='".$p->NombreEjercicio."'>".$p->NombreEjercicio."</option>";
								}
								?>
							</select></li>
			<li><label>Periodo:</label><select name='periodo' id='periodo' class="nminputselect">
								<?php
								if(intval($_GET['tipo'])<2)
									{
										echo"<option value='0'>Todos</option>";
									}
								?>
								<option value='1'>Enero</option>
								<option value='2'>Febrero</option>
								<option value='3'>Marzo</option>
								<option value='4'>Abril</option>
								<option value='5'>Mayo</option>
								<option value='6'>Junio</option>
								<option value='7'>Julio</option>
								<option value='8'>Agosto</option>
								<option value='9'>Septiembre</option>
								<option value='10'>Octubre</option>
								<option value='11'>Noviembre</option>
								<option value='12'>Diciembre</option>
								<option value='13'>Cierre del ejercicio</option>
							</select></li>
			<li><label>Sucursal:</label><select name='sucursal' id='sucursal' class="nminputselect">
								<option value='0'>Todos</option>
								<?php
									while($suc = $sucursales->fetch_assoc())
									{
										echo "<option value='".$suc['idSuc']."'>".$suc['nombre']."</option>";
									}
								?>
							</select></li>
			<li><label>Segmento:</label><select name='segmento' id='segmento' class="nminputselect">
								<option value='0'>Todos</option>
								<?php
									while($seg = $segmentos->fetch_assoc())
									{
										echo "<option value='".$seg['idSuc']."'>".$seg['nombre']."</option>";
									}
								?>
							</select></li>
							<?php if($_GET['tipo']==0){ 
								$totalSeg=count($seg);
								echo "<li><label>Comparativo por Segmentos</label><input type='checkbox' value='1' id='comSeg' name='comSeg' ></li>
										<input type='hidden' value='$totalSeg' id='totalSeg' name='totalSeg' >";
							 } ?>
						<?php if($detalle!=""){echo "<li><label></label> $detalle </li>";} ?>
						<?php if($_GET['tipo']<3){ ?>
			<li><label>Convertir Moneda</label><input type='checkbox' value='1' id='tipoC' name='tipoC' onclick='conMon();'></li>
			<div id="conv">
			<li><label>Moneda</label><select name="moneda" id="moneda">
										<?php
										while($coin = $monedas->fetch_object())
										{
											if($coin->coin_id>1){
											echo "<option value='".$coin->description."'>".$coin->description."(".$coin->codigo.")</option>";
												}
										}
										?>
									</select></li>
			<li><label>Tipo de Cambio</label><input type="text" name="valMon" placeholder="00.00" /></li>
			</div>	
			<?php } ?>	
							<li><label></label><input type="submit" <?php if($_GET['tipo']>=3){ ?>onclick="$('#nmloader_div',window.parent.document).show();" <?php } ?> class="nminputbutton" value="Ejecutar Reporte"></li>
		</ul>
		</form>
	</div>
</body>
</html>