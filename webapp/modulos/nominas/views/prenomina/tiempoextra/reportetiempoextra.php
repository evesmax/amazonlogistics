<!DOCTYPE html>
<html>
<head>
  <meta charset="UTF-8"/>
  <script type="text/javascript" src="js/jquery-1.10.2.min.js"></script>   
    <link   rel="stylesheet" type="text/css" href="css/reporteincidencias.css"> 

  <link   rel="stylesheet" href="css/reportenomina.css" type="text/css">
  <script src="//ajax.googleapis.com/ajax/libs/jqueryui/1.9.2/jquery-ui.min.js"></script>
  <script type="text/javascript" src="js/ui.datepicker-es-MX.js"></script>
  <script type="text/javascript" src='js/reportetiempoextra.js'></script>
  
  <link   rel="stylesheet" href="../../libraries/font-awesome/css/font-awesome.min.css"> 
  <script src="../../libraries/dataTable/js/datatables.min.js"></script>
  <link   rel="stylesheet" href="../../libraries/bootstrap/dist/css/bootstrap.min.css">
  <link   rel="stylesheet" href="../../libraries/dataTable/css/datatablesboot.min.css"> 
  <script src="../../libraries/dataTable/js/dataTables.bootstrap.min.js"></script>
  <script src="../cont/js/redirect.js" type="text/javascript" language="javascript" charset="UTF-8"></script>
  <script language='javascript' src='../cont/js/pdfmail.js'></script>
</head>
<body>
  <div class="container-fluid" class='encabezado' 
  style="text-align:center;font-family: Courier;font-size: 25px;">
    <b>Reporte de tiempo extra</b>
  </div>
  <br><br>
  <div class="container well" style="width: 96%;">
  	  <fieldset class="scheduler-border">
        <legend align="center" class="scheduler-border">Búsqueda</legend>
  	 		<div class="col-md-4 ">
		          <label >Tipo de periodo:</label>
		          <select id="tipoperiodo" name="tipoperiodo" class="form-control selectpicker btn-sm" data-live-search="true" data-width="55%">
		            <option value="0" >Ninguno</option>
		            <?php 
		            while ($e = $tipoperiodo->fetch_object()){
		           
		              echo '<option value="'. $e->idtipop .'" >'. $e->nombre .'  </option>';
		            }
		            ?>
		          </select> 
		     </div>
	          <div class="col-md-4">
	              <label class="" for="nominas">Nomina:</label>
	              <select id="nominas" class="nominas form-control selectpicker btn-sm" data-live-search="true" name="nominas" data-width="70%">
	              </select> 
	         </div>
            
	        <div class="col-md-4">
	          <label>Empleado:</label>
	          	<select class="selectpicker" id="nombreEmpleado" data-live-search="true" name="empleados">
		            <option value="0">Todos</option>
		            <?php 
		            while ($e = $empleados->fetch_object()){
		              $b = "";
		              if(isset($datos)){ if($e->idEmpleado == $datos->idEmpleado){ $b="selected"; } }
		              echo '<option value="'. $e->idEmpleado .'" '. $b .'>'.$e->apellidoPaterno .' '.$e->apellidoMaterno .' '.$e->nombreEmpleado.' </option>';
		            }
		            ?>
	          	</select>  
	        </div>
	        <div class="col-md-12" style="text-align: center;padding-top: 15px;"> 
              <button type="button" class="btn btn-primary btn-sm" id="load" data-loading-text="Consultando<i class='fa fa-refresh fa-spin'></i>">Generar Reporte</button>
            </div>
   		</fieldset>
  	<br>
  	 <div class="alert alert-info table-responsive">
      <table id="tablate" cellpadding="0" class="table table-striped nowrap table-bordered"  width='100%';>
        <thead>
          <tr style="background-color:#B4BFC1;color:#000000">
            <th>Num. de dias</th>
            <th>Tipo Hora</th>
            <th>Num. de hora</th>
            <th>Importe</th>
            <th>Recibo</th>
            <th>Empleado</th>
            <th>Minutos</th>
            <th>Automatico</th>
            <th>Usuario creacion</th>
          </tr>
        </thead>
        <tbody id="contenidote">
        </tbody>
      </table>
    </div>
  	
  	
  	
  	
  	
  	
  </div>