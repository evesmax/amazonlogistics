<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8"/>
<script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>   
<link rel="stylesheet" type="text/css" href="css/registroentradas.css"> 
<script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
<script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
<script type="text/javascript" src='js/reportesobrerecibo.js'></script>
<link rel="stylesheet" type="text/css" href="css/reportesobrerecibo.css">
<link rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
<link rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"><link rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css">
<link rel="stylesheet" href="../../libraries/datepicker/css/bootstrap-datepicker.min.css">
<script src="../../libraries/dataTable/js/datatables.min.js"></script>
<script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
<script language='javascript' src='js/pdfmail.js'></script>
<body>
<div style="height: 46px;width:100%;font-family: Courier;background-color:#F5F5F5;" align="center" class="ocultarfiltros"><b style="font-size:25px;">Reporte de Prenomina</b></div> 
<div class="panel-body">
<div class="row">
<div class="col-md-12" align="center">
	<form method="post" action="index.php?c=Reportes&f=reporteSobrerecibo" id="formfecha">
		<fieldset class="ocultarfiltros">
			<legend align="center">Búsqueda</legend>
			<div class="form-inline">
				<input type="checkbox" name="checkboxG4" id="mostrarvisual" class="css-checkbox"  checked />
				<label for="mostrarvisual" class="css-label" onclick="activarChecked()">VISUAL</label>
				<input type="checkbox" name="mostrarimprimir" id="mostrarimprimir" class="css-checkbox"  />
				<label for="mostrarimprimir" onclick="activarCheckeddos()" class="mostrarimprimir css-label" style="margin-left: 20px;margin-right: 20px;">IMPRIMIR RECIBOS</label>	
				<input type="checkbox" value="1" name="mostrarrangos" id="mostrarrangos" class="css-checkbox"/>
				<label for="mostrarrangos" onclick="activarCheckedtres()" class="mostrarrangos css-label" style="margin-left: 20px;margin-right: 20px;">RANGO DE CODIGO</label>
			</div>
			<br>
			<br>
			<br>						
			<div class="container-fluid" align="center">
				<div class="row">
					<div class="form-inline"> 
						<label>Tipo de periodo:</label>
						<div class="input-group mb-2 mr-sm-2 mb-sm-0">
							<select       id="idtipop" class="form-control selectpicker btn-sm" data-live-search="true" name="idtipop">
								<option value="*">Todos</option>
								<?php 
								while ($e = $tipoperiodo->fetch_object()){
									$b = "";
									if(isset($datos)){ if($e->idtipop == $datos->idtipop){ $b="selected"; } }
									echo '<option value="'. $e->idtipop .'" '. $b .'>'. $e->nombre .'  </option>';
								}
								?>
							</select>
						</div>
						&ensp;
						&ensp;
						&ensp;
						<label>Nomina:</label>
						<div class="input-group mb-2 mr-sm-2 mb-sm-0">
							<select id="idnomp" class="form-control selectpicker btn-sm" data-live-search="true" name="idnomp">
								<option value="*" >Todos</option>
								<?php 
								while ($e = $nominas->fetch_object()){
									$b = ""; 
									if(isset($datos)){ if($e->idtipop == $datos->idtipop){ $b="selected"; } }
									echo '<option value="'. $e->idtipop .'" '.$b .'>'.'('.$e->numnomina.')'.'  '.$e->fechainicio.' '.$e->fechafin.'</option>';
								}?> 
							</select>
						</div>
						&ensp;
						&ensp;
						&ensp;
						<label class="empleadocheck">Empleado:</label>
						<div class="input-group mb-2 mr-sm-2 mb-sm-0 empleadocheck">
							<select id="empleado" class="empleado form-control selectpicker btn-sm" data-live-search="true" name="empleado" onchange="datosEmp()">
								<option value="*">Todos</option>
								<?php 
								while ($e = $empleadosdos->fetch_object()){
									$b = "";
									if(isset($datos)){ if($e->idEmpleado == $datos->idEmpleado){ $b="selected"; } }
									echo '<option value="'. $e->idEmpleado .'" '. $b .'>'.$e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.' </option>';;
								}
								?>
							</select> 
						</div>
						<label  hidden class="rangoempleado">De:</label>
						<div  class="input-group mb-2 mr-sm-2 mb-sm-0 rangoempleado" hidden>
							<select id="codigouno" class="form-control selectpicker btn-sm" data-live-search="true" name="codigouno">
								<option value="">selecciona</option>
								<?php 
								while ($e = $codigo->fetch_object()){
									$b = "";
									if(isset($datos)){ if($e->idEmpleado == $datos->idEmpleado){ $b="selected"; } }
									echo '<option value="'. $e->idEmpleado .'" '. $b .'>'. $e->codigo .'  </option>';
								}
								?>
							</select> 
						</div>
						<label class="rangoempleado" hidden="">al:</label>
						<div class="input-group mb-2 mr-sm-2 mb-sm-0 rangoempleado" hidden>
							<select id="codigodos" class="form-control selectpicker btn-sm rangoempleado " data-live-search="true" name="codigodos"  hidden="true">
								<option value="">selecciona</option>
								<?php 
								while ($e = $codigodos->fetch_object()){
									$b = "";
									if(isset($datos)){ if($e->idEmpleado == $datos->idEmpleado){ $b="selected"; } }
									echo '<option value="'. $e->idEmpleado .'" '. $b .'>'. $e->codigo .'  </option hidden>';
								}
								?>
							</select> 
						</div>
						&ensp;
						&ensp;
						&ensp;
						<button type="button" class="btn btn-primary btn-sm" id="load" style="center" data-loading-text="Consultando<i class='fa fa-refresh fa-spin '></i>">Generar Reporte</button>
						&ensp;
						<a type="button" id="impresion" class="btn btn-info btn btn-sm" 
						href="javascript:window.print();" hidden="true" onclick="printl()">
							<img src="../../../webapp/netwarelog/repolog/img/impresora.png" border="0" ></a> 
						<!-- &ensp;
						<a type="button" class="btn btn-sm" style="background-color:#d67166"  href="javascript:pdf();"> <img src="../../../webapp/netwarelog/repolog/img/pdf.gif"  
						title ="Generar reporte en PDF" border="0"> 
						</a> -->
					</div>  
				</div>
			</div>
		</fieldset>
		<br>
		<br>			
		<div id="imprimir" class="imprimir" hidden>
		</div>
		<div id="contPerce"></div>
		<br>
		<br> <div id="imprimible">
		<div id="divVisualv" style="width: 100%" class="col-md-12">
			<div id="divVisual"> 
			</div>
			</div>
		</div>
	</div>
	<br/>
</form>
</div>
</div>
<br>
<br>
</div>
<!--GENERA PDF*************************************************-->
<div id="divpanelpdf" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-sm">
   <div class="modal-content">
    <div class="modal-header">
     <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
     <h4 class="modal-title">Generar PDF</h4>
   </div>
   <form id="formpdf" action="../cont/libraries/pdf/examples/generaPDF.php" method="post" target="_blank" onsubmit="generar_pdf()">
     <div class="modal-body">
      <div class="row">
       <div class="col-md-6">
        <label>Escala (%):</label>
        <select id="cmbescala" name="cmbescala" class="form-control">
         <?php
         for($i=100; $i > 0; $i--){
          echo '<option value='. $i .'>' . $i . '</option>';
        }
        ?>
      </select>
    </div>
    <div class="col-md-6">
      <label>Orientación:</label>
      <select id="cmborientacion" name="cmborientacion" class="form-control">
       <option value='P'>Vertical</option>
       <option value='L'>Horizontal</option>
     </select>
   </div>
 </div>
 <textarea id="contenido" name="contenido" style="display:none"></textarea>
 <input type='hidden' name='tipoDocu' value='hg'>
 <input type='hidden' value='<?php echo "http://".$_SERVER['SERVER_NAME'].$url[0]."/netwarelog/archivos/1/organizaciones/$logo"; ?>' name='logo' />
 <input type='hidden' name='nombreDocu' value='Detalle Nomina'>
</div>
<div class="modal-footer">
  <div class="row">
   <div class="col-md-6">
    <input type="submit" value="Crear PDF" autofocus class="btn btn-primary btnMenu">
  </div>
  <div class="col-md-6">
    <input type="button" value="Cancelar" onclick="cancelar_pdf()" class="btn btn-danger btnMenu">
  </div>
</div>
</div>
</form>
</div>
</div>
</div>
<div id="loading" style="position: absolute; top:30%; left: 50%;display:none;z-index:2;">
  <div id="divmsg" style="
  opacity:0.8;
  position:relative;
  background-color:#000;
  color:white;
  padding: 20px;
  -webkit-border-radius: 20px;
  border-radius: 10px;
  left:-50%;
  top:-200px
  ">
  <center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...
  </center>
</div>
</div>
<script>
  function cerrarloading(){
    $("#loading").fadeOut(0);
    var divloading="<center><img src='../../../webapp/netwarelog/repolog/img/loading-black.gif' width='50'><br>Cargando...</center>";
    $("#divmsg").html(divloading);
  }
</script>
	
	</body>
	</html>