<!DOCTYPE html>
<html>
<head>
	<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>   
	<link   rel="stylesheet" type="text/css" href="css/registroentradas.css"> 
	<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
	<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
	<script type="text/javascript" src="../../libraries/numeral.min.js"></script>
	<script type="text/javascript" src='js/resumenAnaliticoDep.js'></script>
	<link   rel="stylesheet" type="text/css" href="css/calculo_ptu.css">
	<link href="../../libraries/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
	<script src="../../libraries/bootstrap-multiselect.js" type="text/javascript"></script>

	<link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 
	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<link   rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
	<script src="../cont/js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
	<title></title>
</head>
<body>

	<div style="text-align:center;font-family: Courier;background-color:#F5F5F5;font-size: 25px;">
		<b>Resumen Analitico por Departamento</b>
	</div>
	<br>

	<div class="container well" style="width: 98%;">
		<form class="ocultos" method="post" action="index.php?c=Reportes&f=resumenAnaliticoDepa" id="formdep">	
			<div class="form-inline">
			<div class="col-md-12" style="text-align: center;">
				<input type="checkbox" name="checkboxG4" id="mostrarnomiDep" class="css-checkbox" checked/>
				<label for="mostrarnomiDep" class="css-label" onclick="activarChecked()">POR NÓMINA</label>
				&nbsp;
				&nbsp;
				<input type="checkbox" value="1" name="mostrarrangonomi" id="mostrarrangonomi" class="css-checkbox"/>
				<label for="mostrarrangonomi" onclick="activarCheckedrango()" class="mostrarrangonomi css-label">RANGO DE NÓMINA</label>
			</div>
			<br><br><br>
			</div>
			<div id="mostrarprimerfiltro">
			<div class="col-md-3">
				<label>Periodo</label>
				<select id="idtipop" class="selectpicker" data-live-search="true" name="idtipop">
					<option value="" selected="selected">Seleccione</option>
					<?php 
					while ($e = $tipoperiodo->fetch_object()){
						$b = "";
						if(isset($datos)){ if($e->idtipop == $datos->idtipop){ $b="selected"; } }
						echo '<option value="'. $e->idtipop .'" '. $b .'>'. $e->nombre .'  </option>';
					}
					?>
				</select>
			</div>
			<div class="col-md-3">
				<label for="idnomp">Nomina:</label>
				<select id="idnomp" class="btn btn-sm " data-live-search="true" name="idnomp" multiple="multiple">
					<?php 
					while ($e = $nominas->fetch_object()){
						$b = ""; 
						if(isset($datos)){ if($e->idtipop == $datos->idtipop){ $b="selected"; } }
						echo '<option value="'. $e->idtipop .'" '.$b .'>'.'('.$e->numnomina.')'.'  '.$e->fechainicio.' '.$e->fechafin.'</option>';
					}
					?> </select>
					<input type="text" id="idnomi" class="form-control" name="idnomi" value ="<?php echo @$_REQUEST['idnomp'] ?>" align="left"  style="display: none;"/>
				</div>
					<div class="col-md-3">
					<label for="dep" style="text-align: center;font-size: 15px;">Departamento:</label>
					<select id="dep" class="btn btn-sm" data-live-search="true" name="dep" multiple="multiple">
						<?php 
						while ($e = $departamentos->fetch_object()){
							$b = "";
							if(isset($datos)){ if($e->idDep == $datos->idDep){ $b="selected"; } }
							echo '<option value="'. $e->idDep .'" '. $b .'>'. $e->nombre .'  </option>';
						}
						?>
					</select>
					<input type="text" id="depa" class="form-control" name="depa" value ="<?php echo @$_REQUEST['dep'] ?>" align="left"  style="display: none;"/>
				</div>
			</div>
			<div id="mostrarfiltrodos" hidden>
				<div class="col-md-3">
				<label>Periodo</label>
				<select id="idtipop" class="selectpicker" data-live-search="true" name="idtipop">
					<option value="" selected="selected">Seleccione</option>
					<?php 
					while ($e = $tipoperiodo2->fetch_object()){
						$b = "";
						if(isset($datos)){ if($e->idtipop == $datos->idtipop){ $b="selected"; } }
						echo '<option value="'. $e->idtipop .'" '. $b .'>'. $e->nombre .'  </option>';
					}
					?>
				</select>
			</div>
			<div class="col-md-3">
			<label>Nomina de</label>
			<select id="nominas" class="selectpicker" data-live-search="true" name="nominas">
				<option value="*">Todos</option>
				<?php 
				while ($e = $nominas->fetch_object()){
					$b = ""; 
					if(isset($datos)){ if($e->idtipop == $datos->idtipop){ $b="selected"; } }
					echo '<option value="'. $e->idtipop .'" '.$b .'>'.'('.$e->numnomina.')'.'  '.$e->fechainicio.' '.$e->fechafin.'</option>';
				}
				?> </select>
				<input type="text" id="nomi" name="nomi" class="selectpicker btn-sm form-control" value="<?php if (isset($_POST['nominas'])) echo $_POST['nominas']; ?>"  style="display:none"/>
				<input type="text" id="extraord" name="extraord" class="selectpicker btn-sm form-control" value="<?php if (isset($_POST['nominas'])) echo $_POST['nominas']; ?>"  style="display:none"/>
			</div>
			<div class="col-md-3">
				<label>Nomina al</label>
				<select id="nominasdos" class="sel selectpicker" data-live-search="true" name="nominasdos">
					<option value="*">Todos</option>
					<?php 
					while ($e = $nominas->fetch_object()){
						$b = ""; 
						if(isset($datos)){ if($e->fechainicio == $datos->idtipop){ $b="selected"; } }
						echo '<option value="'. $e->idtipop .'" '.$b .'>'.'('.$e->numnomina.')'.'  '.$e->fechainicio.' '.$e->fechafin.'</option>';
					}
					?> </select>
				
				</div>

			</div>
			 
			
				
				<div style="text-align: center;">
					<button type="button" class="btn btn-primary btn-sm" id="load" style="text-align: center;" data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>">Generar Reporte</button>
			</div>
			</form>
		</div>



		<div id="imprimible"> 
			<?php
			$deparmento =0;
			if($resumenAnaliticoDep){
				if($resumenAnaliticoDep->num_rows>0) {
					while($in = $resumenAnaliticoDep->fetch_assoc()){
						if ($deparmento != $in['idDep']){
							if ($deparmento != 0 ){?>
						</tbody>
					</table>
					<br>
					<br>
				</div>

				<?php } ?> 
				<div class='alert alert-info' cellspacing='0' width='100%' style='overflow: auto;'>
					<?php  echo"<table id=\"resumenAnalDep".$in['idDep']."\" cellpadding='0' class='resumenAnalDep  table-striped table-bordered dt-responsive nowrap' width='100%'; style='border:solid .3px;font-size:12.5px;' border='1' bordercolor='#0000FF'>";?>
						<thead> 
							<tr style='background-color:#B4BFC1;color:#000000;height: 35px;' class="ancho">
								<th>Clave</th>
								<th>Concepto</th>
								<th>Percepción</th>
								<th>Deducción</th>
							</tr>
						</thead>
						<tbody>
						</div> 
						<?php }?>
						<tr class="clase<?php echo $in['idregistro'];?>">
							<td><?php echo $in['concepto'];?></td>
							<td><?php echo $in['descripcion'];?></td>
							<td style="text-align: right;"><?php echo (number_format($in['perce'],2,'.',','));?></td>
							<td style="text-align: right;"><?php echo (number_format($in['deduc'],2,'.',','));?></td>
						</tr>                  
						<?php  
						$deparmento = $in['idDep'];
					}?>
				</tbody>
			</table>
			<?php   }
		}?>
	</div> 
</div>

</body>
</html>