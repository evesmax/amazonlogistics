<!DOCTYPE html>
<html>
<head>
	<meta charset="UTF-8" />

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


	<!-- <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>    -->
	<!-- <link   rel="stylesheet" type="text/css" href="css/reporteacumulado.css">  -->
	<!-- <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script> -->
	<!-- <script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script> -->
	<!-- <link rel="stylesheet" type="text/css" href="css/reporteprenomina.css">   -->


	<!-- <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script> -->

<!-- 
	<link rel="stylesheet" type="text/css" href="css/registroentradas.css"> 

	<link rel="stylesheet" type="text/css" href="css/reporteentradas.css"> 
	<script type="text/javascript" src="../../libraries/numeral.min.js"></script>
	<script type="text/javascript" src='js/resumenAnaliticoDep.js'></script>


	<script type="text/javascript" src="../../libraries/bootstrap.min.js"></script>
	<link href="../../libraries/bootstrap-multiselect.css" rel="stylesheet" type="text/css" />
	<script src="../../libraries/bootstrap-multiselect.js" type="text/javascript"></script>

	<link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
	<link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css">
	<link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
	<link   rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
	<script src="../../libraries/dataTable/js/datatables.min.js"></script>
	<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script> -->
	<!-- <script src="../cont/js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script> -->
	<title>Resumen Analitico por Departamento.</title>
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
				<input type="checkbox" name="checkboxG4" id="mostrarvisual" class="css-checkbox"  checked/>
				<label for="mostrarvisual" class="css-label" onclick="activarChecked()">VISUAL</label>
				&nbsp;
<!-- <input type="checkbox" name="mostrarimprimir" id="mostrarimprimir" class="css-checkbox"/>
	<label for="mostrarimprimir" onclick="activarCheckeddos()" class="mostrarimprimir css-label">IMPRIMIR RECIBOS</label> -->
	&nbsp;
	<input type="checkbox" value="1" name="mostrarrangos" id="mostrarrangos" class="css-checkbox"/>
	<label for="mostrarrangos" onclick="activarCheckedtres()" class="mostrarrangos css-label">RANGO DE CODIGO</label>
</div>
</div>
<br>
<br>
<br>


<div class="col-md-12" style="text-align: center;">
	<div class="col-md-3">
		<label>Periodo</label>
		<select id="idtipop" class="selectpicker" data-live-search="true" name="idtipop">
			<option value="*" selected="selected">Todos</option>
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
			<input type="text" id="idnomi" class="form-control" name="idnomi" value ="<?php echo @$_REQUEST['idnomp'] ?>" align="left"  required  />
		</div>

		<div class="col-md-3" hidden="">
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
			<div class="col-md-3" hidden="">
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
					<!-- <input type="text" id="nomidos" name="nomidos" class="selectpicker btn-sm form-control" value="<?php if (isset($_POST['nominasdos'])) echo $_POST['nominasdos']; ?>"  style="display:none"/> -->
				</div>
				<div class="col-md-3">
					<label for="dep" style="text-align: center;font-size: 15px;">Departamento:</label>
					<select id="dep" class="btn btn-sm" data-live-search="true" name="dep" multiple="multiple">

						<!-- <option value="1">Seleccione</option> -->
						<!-- <option value="*">Todos</option> -->
						<?php 
						while ($e = $departamentos->fetch_object()){
							$b = "";
							if(isset($datos)){ if($e->idDep == $datos->idDep){ $b="selected"; } }
							echo '<option value="'. $e->idDep .'" '. $b .'>'. $e->nombre .'  </option>';
						}
						?>
					</select>
					<input type="text" id="depa" class="form-control" name="depa" value ="<?php echo @$_REQUEST['dep'] ?>" align="left"  required/>
				</div>
			</div>
			</div>
		</form>
		 <!--DIV CONTAINER-->
<div class="alert alert-info wrap" cellspacing="0" width="100%">
 <?php  
      if($resumenAnaliticoDep->num_rows>0) {
         while($in = $resumenAnaliticoDep->fetch_assoc()){?>



		
		<table id="resumenAnalDep" class="resumenAnalDep table table-striped table-bordered nowrap" cellspacing="0" width="100%" style="font-size: 19px;">
				<thead> 

					<tr style="background-color:#B4BFC1;color:#000000;">
						<th>Clave</th>
						<th>Concepto</th>
						<th>Percepción</th>
						<th>Deducción</th>	
					</tr>
				</thead> 
		
				<?php 
				echo "<tbody>
					<tr>
 					 <td>".($in->concepto)."</td>
        			 <td>".($in->descripcion)."</td>
                     <td align='right'>".(number_format($in->perce,2,'.',','))."</td>
                     <td align='right'>".(number_format($in->deduc,2,'.',','))."</td>
     				<tr>
     				</tbody>";
					  }
				}
				?>

</table>
</div>
</div>
<button type="button" class="btn btn-primary btn-sm" id="load" style="text-align: center;" data-loading-text="Consultando<i class='fa fa-refresh fa-spin '></i>">Generar</button>
</body>
</html>